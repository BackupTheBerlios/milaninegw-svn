<?php
	/**************************************************************************\
	* eGroupWare API - smtp mailer							*
	* This file written by Itzchak Rehberg <izzysoft@qumran.org>			*
	* and Joseph Engo <jengo@phpgroupware.org>					*
	* and Angelo "Angles" Puglisi <angles@aminvestments.com>			*
	* This module should replace php's mail() function. It is fully syntax		*
	* compatible. In addition, when an error occures, a detailed error info		*
	* is stored in the array $send->err (see ../inc/email/global.inc.php for		*
	* details on this variable).								*
	* Copyright (C) 2000, 2001 Itzchak Rehberg, and					*
	* Copyright (C) 2001, 2002 Angelo Puglisi (Angles)					*
	* -------------------------------------------------------------------------			*
	* This library is part of the eGroupWare API					*
	* http://www.egroupware.org/api							* 
	* ------------------------------------------------------------------------ 			*
	* This library is free software; you can redistribute it and/or modify it		*
	* under the terms of the GNU Lesser General Public License as published by 	*
	* the Free Software Foundation; either version 2.1 of the License,			*
	* or any later version.								*
	* This library is distributed in the hope that it will be useful, but			*
	* WITHOUT ANY WARRANTY; without even the implied warranty 		*
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	*
	* See the GNU Lesser General Public License for more details.			*
	* You should have received a copy of the GNU Lesser General Public License	*
	* along with this library; if not, write to the Free Software Foundation,		*
	* Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA			*
	\**************************************************************************/
	
	/* $Id: class.mail_send.inc.php,v 1.15 2004/07/11 08:31:40 ralfbecker Exp $ */
	
	/*!
	@class mail_send
	@abstract sockets based SMTP class, will communicate with an MTA to send mail
	@result returns True on success (mail was sent), returns False on error (no mail sent)
	@discussion class provides for complex SMTP transactions, bypassing need for php's
	builtin mail sending functions. Currently part of the email class group, when mature will
	be moved to standard egroupware api.
	@author (a) Itzchak Rehberg - initial implementation, SMTP communication and control flow, excellent work! 
	and (b) Angelo Puglisi (Angles) - convert to multi-dimentional array driven architecture, expanded debugging,
	RFC2822 and 2821 compliance, retain a copy for archiving option, fake send debug, handshake retention, other stuff...
	*/
	class mail_send
	{
		var $err = array();
		var $to_res = array();
		var $default_smtp_port = 25;
		
		//var $debug_fake_send = True;
		var $debug_fake_send = False;
		
		var $retain_copy = False;
		
		// trace flag 0 = none, 1 = server only, 2 = server and client, 3 = totally extra verbose
		var $trace_flag = 0;
		var $trace_data = array();
		
		// some of the MTA communication should not go into the copy, like ELHO stuff
		var $retain_copy_ignore = True;
		var $assembled_copy = '';

		function mail_send()
		{
			$this->err['code'] = '';
			$this->err['msg']  = '';
			$this->err['desc'] = '';
			$this->err['server_chat'] = "\r\n";
		}
		
		function send_init()
		{
			// depreciated
		}
		
		function log_trace($prefix='', $data)
		{
			$next_idx = count($this->trace_data);
			$this->trace_data[$next_idx] = $prefix.' : '.htmlspecialchars(rtrim($data));
		}
		// ===  some sub-functions  ===

		function socket2msg($socket)
		{
			if ($this->debug_fake_send)
			{
				// we are not really sending mail, pretend the server accepted out data
				return True;
			}
			
			$followme = '-';
			$this->err["msg"] = '';
			do
			{
				//$rmsg = fgets($socket,255);
				$rmsg = fgets($socket,1024);
				$this->err['server_chat'] .= htmlspecialchars('s->c: '.$rmsg);
				if ($this->trace_flag > 0) { $this->log_trace('socket2msg: rmsg', $rmsg); }
				$this->err['code'] = substr($rmsg,0,3);
				if ($this->trace_flag > 2) { $this->log_trace('socket2msg: $this->err[code]', $this->err['code']); }
				$followme = substr($rmsg,3,1);
				if ($this->trace_flag > 2) { $this->log_trace('socket2msg: $followme', $followme); }
				$this->err['msg'] = substr($rmsg,4);
				if ($this->trace_flag > 2) { $this->log_trace('socket2msg: $this->err[msg]', $this->err['msg']); }
				if (substr($this->err['code'],0,1) != 2 && substr($this->err['code'],0,1) != 3)
				{
					$rc  = fclose($socket);
					return false;
				}
				
				if ($followme == ' ')
				{
					break;
				}
			}
			while ($followme == '-');
			
			return true;
		}

		function msg2socket($socket,$message)
		{
			if ($this->debug_fake_send)
			{
				echo $GLOBALS['phpgw']->msg->htmlspecialchars_encode($message);
				return True;
			}
			// if we need a copy of this message for the "sent" folder, assemble it here
			if (($this->retain_copy)
			&& (!$this->retain_copy_ignore))
			{
				$this->assembled_copy .= "$message";
			}
			// on the contrary, server chat ONLY wants the pre- DATA stuff
			if ($this->retain_copy_ignore)
			{
				// "retain_copy_ignore" means we are still in the handshake phase, which is what "server_chat" wants
				$this->err['server_chat'] .= htmlspecialchars('c->s: '.$message);
			}
			
			if ($this->trace_flag > 1) { $this->log_trace('msg2socket: $message', $message); }
			
			$rc = fputs($socket,"$message");
			if (!$rc)
			{
				$this->err['code'] = '420';
				$this->err['msg']  = 'lost connection';
				$this->err['desc'] = 'Lost connection to smtp server.';
				$rc  = fclose($socket);
				return false;
			}
			return true;
		}

		// ===== [ main function: smail_2822() ] =======

		function smail_2822($mail_out)
		{
			// don't start retaining the email copy until after the MTA handshake
			$this->retain_copy_ignore = True;
			
			// error code and message of failed connection
			$errcode = '';
			$errmsg = '';
			// timeout in secs
			$timeout = 5;
			
			if ($this->debug_fake_send)
			{
				// arbitrary number, no significance
				// we do not actually communicate with the SMTP server for a fake send
				$socket = 41;
				// announce the fact this is echo'd debug output, not an actual session
				echo '<html><body><h2>FAKE SEND DEBUG:</h2> <h3>this is what the client *would* send to the SMTP server were this an actual send</h3>';
			}
			else
			{
				$smtp_server = $GLOBALS['phpgw_info']['server']['smtp_server'];
				$smtp_port = $GLOBALS['phpgw_info']['server']['smtp_port'];
				// some people do not set this up correctly in the site-wide admin for email
				if (empty($smtp_port))
				{
					$smtp_port = $this->default_smtp_port;
				}
				
				// OPEN SOCKET - now we try to open the socket and check, if any smtp server responds
				$socket = fsockopen($smtp_server,$smtp_port,$errcode,$errmsg,$timeout);
				$this->err['server_chat'] .= htmlspecialchars('c->s: fsockopen('.$smtp_server.','.$smtp_port.','.$errcode.','.$errmsg.','.$timeout.') ; returned: '.$socket )."\r\n";

			}
			if (!$socket)
			{
				$this->err['code'] = '420';
				$this->err['msg']  = $errcode.':'.$errmsg;
				$this->err['desc'] = 'Connection to '.$GLOBALS['phpgw_info']['server']['smtp_server'].':'.$GLOBALS['phpgw_info']['server']['smtp_port'].' failed - could not open socket.';
				return false;
			}
			else
			{
				$rrc = $this->socket2msg($socket);
			}
			
			$mymachine = $mail_out['mta_elho_mymachine'];
			$fromuser = $mail_out['mta_from'];
			// START SMTP SESSION - now we can send our message. 1st we identify ourselves and the sender

			if ($GLOBALS['phpgw_info']['server']['smtp_auth_user'])
			{
				$mybase64login=base64_encode($GLOBALS['phpgw_info']['server']['smtp_auth_user']);
				$mybase64password=base64_encode($GLOBALS['phpgw_info']['server']['smtp_auth_passwd']);
				$cmds = array (
					"\$src = \$this->msg2socket(\$socket,\"EHLO \$mymachine\r\n\");",
					"\$rrc = \$this->socket2msg(\$socket);",
					"\$src = \$this->msg2socket(\$socket,\"AUTH LOGIN\r\n\");",
	                                "\$rrc = \$this->socket2msg(\$socket);",
					"\$src = \$this->msg2socket(\$socket,\"\$mybase64login\r\n\");",
					"\$rrc = \$this->socket2msg(\$socket);",
					"\$src = \$this->msg2socket(\$socket,\"\$mybase64password\r\n\");",
					"\$rrc = \$this->socket2msg(\$socket);",
					"\$src = \$this->msg2socket(\$socket,\"MAIL FROM:\$fromuser\r\n\");",
					"\$rrc = \$this->socket2msg(\$socket);"
				);
			}
			else
			{
				$cmds = array (
					"\$src = \$this->msg2socket(\$socket,\"EHLO \$mymachine\r\n\");",
					"\$rrc = \$this->socket2msg(\$socket);",
					"\$src = \$this->msg2socket(\$socket,\"MAIL FROM:\$fromuser\r\n\");",
					"\$rrc = \$this->socket2msg(\$socket);"
				);
			}

			if ($this->debug_fake_send)
			{
				echo '<pre>';
			}
			for ($src=true,$rrc=true,$i=0; $i<count($cmds);$i++)
			{
				eval ($cmds[$i]);
				if (!$src || !$rrc)
				{
					return false;
				}
			}
			
			// RCPT TO - now we've got to feed the to's and cc's
			for ($i=0; $i<count($mail_out['mta_to']); $i++)
			{
				$src = $this->msg2socket($socket,'RCPT TO:'.$mail_out['mta_to'][$i]."\r\n");
				$rrc = $this->socket2msg($socket);
				// for lateron validation
				$this->to_res[$i][addr] = $mail_out['mta_to'][$i];
				$this->to_res[$i][code] = $this->err['code'];
				$this->to_res[$i][msg]  = $this->err['msg'];
				$this->to_res[$i][desc] = $this->err['desc'];
			}
			
			if (!$this->debug_fake_send)
			{
				//now we have to make sure that at least one $to-address was accepted
				$stop = 1;
				for ($i=0;$i<count($this->to_res);$i++)
				{
					$rc = substr($this->to_res[$i][code],0,1);
					if ($rc == 2)
					{
						// at least to this address we can deliver
						$stop = 0;
					}
				}
				if ($stop)
				{
					// no address found we can deliver to
					return false;
				}
			}
			
			// HEADERS - now we can go to deliver the headers!
			if (!$this->msg2socket($socket,"DATA\r\n"))
			{
				return false;
			}
			if (!$this->socket2msg($socket))
			{
				return false;
			}
			
			// READY TO SEND MAIL: start retaining the email copy (if necessary)
			$this->retain_copy_ignore = False;
			
			// BEGIN THE DATA SEND
			for ($i=0; $i<count($mail_out['main_headers']); $i++)
			{
				if (!$this->msg2socket($socket,$mail_out['main_headers'][$i]."\r\n"))
				{
					return false;
				}
			}
			// HEADERS TERMINATION - this CRLF terminates the header, signals the body will follow next (ONE CRLF ONLY)
			if (!$this->msg2socket($socket,"\r\n"))
			{
				return false;
			}
			// BODY - now we can go to deliver the body!
			for ($part_num=0; $part_num<count($mail_out['body']); $part_num++)
			{
				// mime headers for this mime part (if any)
				if (($mail_out['is_multipart'] == True)
				|| ($mail_out['is_forward'] == True))
				{
					for ($i=0; $i<count($mail_out['body'][$part_num]['mime_headers']); $i++)
					{
						//$this_line = rtrim($this_line = $mail_out['body'][$part_num]['mime_headers'][$i])."\r\n";
						// note by angles: anglemail forward does not add senders sig, so this may not be necessary here, but definately is below
						// patch egw #821245 thanks Carsten Wolff
						// the signature-delimiter must not be trimmed!
						if ($mail_out['body'][$part_num]['mime_headers'][$i] == '-- ')
						{
							$this_line = $mail_out['body'][$part_num]['mime_headers'][$i]."\r\n";
						}
						else
						{
							$this_line = rtrim($mail_out['body'][$part_num]['mime_headers'][$i])."\r\n";
						}
						if (!$this->msg2socket($socket,$this_line))
						{
							return false;
						}
					}
					// a space needs to seperate the mime part headers from the mime part content
					if (!$this->msg2socket($socket,"\r\n"))
					{
						return false;
					}
				}
				// the part itself
				for ($i=0; $i<count($mail_out['body'][$part_num]['mime_body']); $i++)
				{
					//$this_line = rtrim($mail_out['body'][$part_num]['mime_body'][$i])."\r\n";
					// patch egw #821245 thanks Carsten Wolff
					// the signature-delimiter must not be trimmed!
					if ($mail_out['body'][$part_num]['mime_body'][$i] == '-- ')
					{
						$this_line = $mail_out['body'][$part_num]['mime_body'][$i]."\r\n";
					}
					else
					{
						$this_line = rtrim($mail_out['body'][$part_num]['mime_body'][$i])."\r\n";
					}
					// TRANSPARENCY - rfc2821 sect 4.5.2 - any line beginning with a dot, add another dot
					if ((strlen($this_line) > 0)
					&& ($this_line[0] == '.'))
					{
						// rfc2821 add another dot to the begining of this line
						$this_line = '.' .$this_line;
					}
					if (!$this->msg2socket($socket,$this_line))
					{
						return false;
					}
					// TESTING memory saving feature, clear already sent lines IF saving them in assembled_copy
					if (($this->retain_copy)
					&& (!$this->retain_copy_ignore))
					{
						// we no longer need the array item, clear it from memory
						$mail_out['body'][$part_num]['mime_body'][$i] = '';
					}
				}
				// this space will seperate this part from any following parts that may be coming
				if (!$this->msg2socket($socket,"\r\n"))
				{
					return false;
				}
			}
			// FINAL BOUNDARY - at the end of a multipart email, we need to add the "final" boundary
			if (($mail_out['is_multipart'] == True)
			|| ($mail_out['is_forward'] == True))
			{
				// attachments / parts have their own boundary preceeding them in their mime headers
				// this is: "--"boundary
				// all boundary strings are have 2 dashes "--" added to their begining
				// and the FINAL boundary string (after all other parts) ALSO has 
				// 2 dashes "--" tacked on tho the end of it, very important !! 
				//   the first or last \r\n is *probably* not necessary
				$final_boundary = '--' .$mail_out['boundary'].'--'."\r\n";
				if (!$this->msg2socket($socket,$final_boundary))
				{
					return false;
				}
				// another blank line
				if (!$this->msg2socket($socket,"\r\n"))
				{
					return false;
				}
			}
			
			// stop retaining the email copy, the message is over, only MTA closing handshake remainse
			$this->retain_copy_ignore = True;
			
			// DATA END - special string "DOTCRLF" signals the end of the body
			if (!$this->msg2socket($socket,".\r\n"))
			{
				return false;
			}
			if (!$this->socket2msg($socket))
			{
				return false;
			}
			// QUIT
			if (!$this->msg2socket($socket,"QUIT\r\n"))
			{
				return false;
			}
			
			if ($this->debug_fake_send)
			{
				echo '</pre><h3>end of Fake Send</h3></body></html>';
			}
			
			if (!$this->debug_fake_send)
			{
				do
				{
					$closing = $this->socket2msg($socket);
				}
				while ($closing);
			}
			return true;
		}
		
	// end of class
	}
?>

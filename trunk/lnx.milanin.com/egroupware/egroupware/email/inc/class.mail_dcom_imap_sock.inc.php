<?php
	/**************************************************************************\
	* eGroupWare API - IMAP                                                    *
	* This file written by Angelo "Angles" Puglisi <angles@aminvestments.com>  *
	* Pure php code sunstantial replacement for php-imap functionality	   *
	* Copyright (C) 2002-2004 Anglo "Angles" Puglisi                           *
	* -------------------------------------------------------------------------*
	* This library is part of the eGroupWare API                               *
	* http://www.egroupware.org/                                               * 
	* ------------------------------------------------------------------------ *
	* This library is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU Lesser General Public License as published by *
	* the Free Software Foundation; either version 2.1 of the License,         *
	* or any later version.                                                    *
	* This library is distributed in the hope that it will be useful, but      *
	* WITHOUT ANY WARRANTY; without even the implied warranty of               *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     *
	* See the GNU Lesser General Public License for more details.              *
	* You should have received a copy of the GNU Lesser General Public License *
	* along with this library; if not, write to the Free Software Foundation,  *
	* Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            *
	\**************************************************************************/
	
	/* $Id: class.mail_dcom_imap_sock.inc.php,v 1.25.2.1 2004/08/09 10:22:27 reinerj Exp $ */
	
	/*!
	@class mail_dcom extends mail_dcom_base FOR SOCKETS
	@abstract implements IMAP module FOR SOCKETS, replaces php IMAP extension
	@author Angles
	@discussion In around summer 2001, Mark Peeters "skeeter" emailed me "Angles" some 
	files which were the skeleton of a sockets replacement for the php IMAP extensions. I 
	believe skeeter wrote the base "network" class in the phpgwapi, and these files extended 
	that class, an excellent approach. Skeeter covered the low level network functionality 
	in his "network" class, but the original IMAP file was appeared to be templated from 
	an incompleted NNTP sockets class that was itself perhaps a template. 
	To the extent skeeters code still exitst in this file, skeeter is listed amoung the authors for that function. 
	However, in early 2004 this file was substantially reimplemented from scratch by Angles. 
	For example, the parsing of the IMAP server strings into php structures is original work by 
	Angles and constitutes the majority of the concern of this class. In fact that is the primary goal of this class. 
	Many of the other capabilities are standard data communications of a client server nature, such as 
	a command to move mail as opposed as a request for a bodystructure which is then transformed 
	into a known php structure.
	*/
	class mail_dcom extends mail_dcom_base
	{
		// we put the fetchstructure thing here so all functions can add to it
		// IS THIS USED?
		var $my_fetchstruct = '##NOTHING##';
		// IS THIS USED?
		var $fs_rawstr = '';
		
		// CLASS VARS
		// bodystructure string goes here
		var $bs_rawstr = '##NOTHING##';
		// each imap command should have a different ID
		var $last_cmd_num = 0;
		
		// next 2 are not important
		// what debth level are we working on of bodystructure
		var $bs_cur_debth = 0;
		// what is the maximum debth we have been to in bodystructure
		var $bs_max_debth = 0;
			
		// stub for base structure part
		var $msg_struct_stub = '##NOTHING##';
		
		// envelope string goes here
		var $env_rawstr = '##NOTHING##';
		
		// finished envelope structure goes here
		var $envelope_struct = '##NOTHING##';
		
		// last 5 structure requests are kept here because we process 2 different ones at one time usually
		var $last_five=array();

		/**************************************************************************\
		*	data analysis specific to IMAP data communications
		\**************************************************************************/

		/*!
		@function get_next_cmd_num
		@abstract make imap command id number
		@author Angles
		@result string
		@discussion  each IMAP command needs a different ID associated with it.
		*/
		function get_next_cmd_num()
		{
			// we want 8 digit number starting with 0 padded to always be 8 digits long
			// forst increase the simple integer we use to keep track of this
			$this->last_cmd_num++;
			$cmd_id = (string)$this->last_cmd_num;
			$cmd_id_length = strlen($cmd_id);
			$new_id_final = '';
			if ($cmd_id_length <= 7)
			{
				if (function_exists('str_pad') == False)
				{
					// we need to add some "0" digits preappended to this command id
					$new_id_final = $cmd_id;
					$add_digits = 8 - $cmd_id_length;
					for ($i = 0; $i < $add_digits; $i++)
					{
						$new_id_final = '0'.$new_id_final;
					}
				}
				else
				{
					// same thing using str_pad
					$new_id_final = str_pad($cmd_id, 8, '0', STR_PAD_LEFT);
				}
			}
			else
			{
				// almost impossible we have this many commands in 1 page view, but just in case
				$new_id_final = $cmd_id;
			}
			// return the prepared IMAP command ID
			return $new_id_final;
		}
		
		/*!
		@function str_begins_with
		@abstract determine if string $haystack begins with string $needle
		@param $haystack (string) data to examine to determine if it starts with $needle
		@param $needle (string) $needle should or should not start at position 0 (zero) of $haystack
		@author Angles
		@result (Boolean) True or False
		@discussion this is a NON-REGEX way to to so this, and is NOT case sensitive
		this *should* be faster then Regular expressions and *should* not be confused by
		regex special chars such as the period "." or the slashes "/" and "\" , etc...
		@access public or private
		*/
		function str_begins_with($haystack,$needle='')
		{
			if ((trim($haystack) == '')
			|| (trim($needle) == ''))
			{
				return False;
			}
			/*
			// now do a case insensitive search for needle as the beginning part of haystack
			if (stristr($haystack,$needle) == False)
			{
				// needle is not anywhere in haystack
				return False;
			}
			// so needle IS in haystack
			// now see if needle is the same as the begining of haystack (case insensitive)
			if (strpos(strtolower($haystack),strtolower($needle)) == 0)
			{
				// in this case we know 0 means "at position zero" (i.e. NOT "could not find")
				// because we already checked for the existance of needle above
				return True;
			}
			else
			{
				return False;
			}
			*/
			// now do a case insensitive search for needle as the beginning part of haystack
			// stristr returns everything in haystack from the 1st occurance of needle (including needle itself)
			//   to the end of haystack, OR returns FALSE if needle is not in haystack
			$stristr_found = stristr($haystack,$needle);
			if ($stristr_found == False)
			{
				// needle is not anywhere in haystack
				return False;
			}
			// so needle IS in haystack
			// if needle starts at the beginning of haystack then stristr will return the entire haystack string
			// thus strlen of $stristr_found and $haystack would be the same length
			if (strlen($haystack) == strlen($stristr_found))
			{
				// needle DOES begin at position zero of haystack
				return True;
			}
			else
			{
				// where ever needle is, it is NOT at the beginning of haystack
				return False;
			}
		}
		
		/*!
		@function imap_read_port
		@abstract reads data from an IMAP server until the line that begins with the specified param "cmd_tag"
		@param $cmd_tag (string) the special string that indicates a server is done sending data
		this is generally the same "tag" identifier that the client sent when initiate the command, ex. "A001"
		@author Angles, skeeter
		@result array where each line of the server data exploded at every CRLF pair into an array
		@discussion IMAP servers send out data that is fairly well "typed", meaning RFC2060
		is pretty strict about what the server may send out, allowing the client (us) to more easily
		interpet this data. See syntax for a description.
		@syntax The important indicator is the string at the beginning of each line of data from the server, it can be
		"*" (astrisk) = "untagged" =  means "this line contains server data and more data will follow"
		"+" (plus sign) means "you, the client, must now finish sending your data to the server"
		"tagged" is the command tag that the client used to initiate this command, such as "A001"
		IMAP server's final line of data for that command will contain that command's tag as sent from the client
		The tagged "command completion" signal is followed by either 
		"OK" = successful command completion
		"NO" = failure of some kind
		"BAD" = protocol error such as unrecognized command or syntax error, client should abort this command processing
		@access private
		*/
		function imap_read_port($cmd_tag='')
		{
			// the $cmd_tag OK, BAD, NO line that marks the completion of server data
			// is not actually considered data
			// to put this line in the return data array may confuse the calling function
			// so it will go in $this->server_last_ok_response
			// for inspection by the calling function if so desired
			// so clear it of any left over value from a previous request
			$this->server_last_ok_response = '';
			
			// should we reset this here or leave it filled?
			//$this->server_last_error_str = '';
			
			// we return an array of strings, so initialize an empty array
			$return_me = Array();
			// is we do not know what to look for as an end tag, then abort
			if ($cmd_tag == '')
			{
				return $return_me;
			}
			// read the data until a tagged command completion is encountered
			while ($line = $this->read_port())
			{
				if ($this->str_begins_with($line, $cmd_tag) == False)
				{
					// continue reading from this port
					// each line of data from the server goes into an array
					$next_pos = count($return_me);
					$return_me[$next_pos] = $line;
				}
				// so we have a cmd_tag, is it followed by OK ?
				elseif ($this->str_begins_with($line, $cmd_tag.' OK'))
				{
					// we got a tagged command response OK
					// but if we send an empty array under this test error scheme
					// calling function will think there was an error
					// DECISION: if array is count zero, put this OK line in it
					// otherwise array already had valid server data in it
					// FIXME: and we do not want to add this OK line which is NOT actually data
					// FIXME: OR we ALWAYS add the final OK line and expect calling function
					// to ignore it ????
					if (count($return_me) == 0)
					{
						// add this OK line just to return a NON empty array
						$return_me[0] = $line;
					}
					else
					{
						// valid server data ALREADY exists in the return array
						// to add this final OK line *MAY* confuse the calling function
						// because this final OK line is NOT actually server data
						// THEREFOR: put the OK line in $this->server_last_ok_response for inspection
						// by the calling function if so desired
						$this->server_last_ok_response = $line;
					}
					// END READING THE PORT
					// in any case, we reached the end of server data
					// so we must break out of this loop
					break;
				}
				// not an OK tag, was it an understandable error NO or BAD ?
				elseif (($this->str_begins_with($line, $cmd_tag.' NO'))
				|| ($this->str_begins_with($line, $cmd_tag.' BAD')))
				{
					// error analysis, we have a useful error response from the server
					// put that error string into $this->server_last_error_str
					$this->server_last_error_str = $line;
					// what should we return here IF there was a NO or BAD error ?
					// how about an empty array, how about FALSE ??
						
					// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
					// empty the array
					$return_me = Array();
					// END READING THE PORT
					// in any case (BAD or NO)
					// we reached the end of server data
					// so we must break out of this loop
					break;
				}
				else
				// so not OK and not a known error, log the unknown error
				{
					// error analysis, generic record of unknown error situation
					// put that error string into $this->server_last_error_str
					$this->server_last_error_str = 'imap unknown error in imap_read_port: "'.$line.'"';
					// what should we return here IF there was a NO or BAD error ?
					// how about an empty array, how about FALSE ??
						
					// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
					// empty the array
					$return_me = Array();
					// END READING THE PORT
					// in any case (unknown data after $cmd_tag completion)
					// we reached the end of server data
					// so we must break out of this loop
					break;
				}
			}
			return $return_me;
		}
		
		/*!
		@function report_svr_data
		@abstract reports server data array for debugging purposes
		@param $data_array (array) server response data as returned by function "imap_read_port" as an array
		@param $calling_func_name (string) for debugging info, the name of the calling function
		@param $show_ok_msg (boolean) default TRUE, the last line of server data is not really data, just an 
		indication that the server has finished sending its data. Set this to TRUE to include that line in the output.
		@author Angles
		@result  none, this function DIRECTLY echos multiline data, nothing is returned from this function
		@access private
		*/
		function report_svr_data($data_array, $calling_func_name='', $show_ok_msg=True)
		{
			echo 'imap: '.$calling_func_name.': response_array line by line:<br>';
			for ($i=0; $i<count($data_array); $i++)
			{
				echo ' -- ArrayPos['.$i.'] data: ' .htmlspecialchars($data_array[$i]) .'<br>';
			}
			echo 'imap: '.$calling_func_name.': =ENDS= response_array line by line:<br>';
			if ($show_ok_msg == True)
			{
				echo 'imap: '.$calling_func_name.': last server completion line: "'.htmlspecialchars($this->server_last_ok_response).'"<br>';
			}
		}
		
		/*!
		@function server_last_error
		@abstract implements IMAP_LAST_ERROR
		@result string
		@author Angles
		@discussion UNDER CONSTRUCTION
		@access public
		*/
		function server_last_error()
		{
			if ($this->debug_dcom > 0) { echo 'imap: call to server_last_error<br>'; }
			return $this->server_last_error_str;
		}
		
		
		/**************************************************************************\
		*	Some of the following are Functions NOT YET IMPLEMENTED
		\**************************************************************************/
		/*!
		@function append
		@abstract implements php-imap function IMAP_APPEND
		@param $stream_notused we do not use this in sockets but it is given anyway
		@param $fq_folder (string) Fully Qualified Folder Name 
		@param &message (string) the message to append to a folder
		@param $flags_str (string) OPTIONAL these are message flags, not command options, thus is a string like "\\Seen" 
		@author Angles
		@discussion implements imap_append
		@syntax The param $fq_folder is expected to be like this 
		{ServerName:Port/options}NAMESPACE_DELIMITER_FOLDERNAME
		repeat for inline doc parser
		&#123;ServerName:Port/options&#125;NAMESPACE_DELIMITER_FOLDERNAME
		An example of this is this
		{mail.example.net:143/imap/notls}INBOX.Sent Items
		example again for docs 
		&#123;mail.example.net:143/imap&#125;INBOX.Sent Items
		Where INBOX is the namespace and the dot is the delimiter, which will always preceed any subfolder.
		*/
		function append($stream_notused, $fq_folder, $message, $flags_str='')
		{
			//if ($this->debug_dcom > 0) { echo 'imap: call to unimplemented socket function: append<br>'; }
			//return true;
			if ($this->debug_dcom > 0) { echo 'imap.append('.__LINE__.'): ENTERING append<br>'; }

			// fq_folder is a "fully qualified folder", seperate the parts:
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder);
			$folder = $svr_data['folder'];
			// if folder has a space we need to enclose it in quotes
			if (strpos($folder, ' ') > 1)
			{
				$folder = '"'.$folder.'"';
			}
			if ($this->debug_dcom > 1) { echo 'imap.append('.__LINE__.'): processed $folder: ['.htmlspecialchars($folder).'] <br>'; }
			
			// FLAGS
			// FUTURE: maybe NEED SANITY CHECK ON THE FLAGS STRING
			if (trim($flags_str))
			{
				// put parens around it and surrounding spaces for drop in placement in the full command
				$flags = ' ('.trim($flags_str).')';
			}
			else
			{
				$flags = '';
			}
			
			// SIZE
			$size = strlen($message);
			if ($this->debug_dcom > 1) { echo 'imap.append('.__LINE__.'): using : $flags ['.htmlspecialchars($flags).'], $size ['.$size.'] <br>'; }
			
			// COMMAND SEQUENCE: this command is in 5 parts
			// 1. initial commnand including tells server the size of binary data to follow
			// 2. wait for continuation "+ "
			// 3. feed the $message
			// 4. wait for standard finishing resoponse from server
			
			// 1. initial command: 00000006 APPEND "INBOX.Sent Items" (\Seen) {526}
			// flags already has spaces around it if it exists, else is empty
			//$cmd_tag = 'J001';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' APPEND '.$folder.$flags.' {'.$size.'}';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD

			if ($this->debug_dcom > 1) { echo 'imap.append('.__LINE__.'): write_port: $full_command is ['.htmlspecialchars($full_command).'] <br>'; }
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap.append('.__LINE__.'): LEAVING with error: could not write_port($full_command)<br>'; }
				$this->error();
				// does $this->error() ever continue onto next line?
				return False;
			}
			// 2. wait for the continuation signal from the server
			$line = $this->read_port();
			if ($this->debug_dcom > 1) { echo 'imap.append('.__LINE__.'): this should be continuation signal line starting with "+ " : [' .htmlspecialchars($this->show_crlf($line)) .'] <br>'; }
			if ($this->str_begins_with($line, '+ ') == False)
			{
					// error analysis, we have a useful error response from the server
					// put that error string into $this->server_last_error_str
					$this->server_last_error_str = $line;
					if ($this->debug_dcom > 0) { echo 'imap.append('.__LINE__.'): LEAVING with error, did not get continuation resoponse "+ ", $line is [' .htmlspecialchars($this->show_crlf($line)) .']<br>'; }
					return False;
			}
			// 3. feed the $message
			if(!$this->write_port($message))
			{
				if ($this->debug_dcom > 0) { echo 'imap.append('.__LINE__.'): LEAVING with error: could not write_port($message)<br>'; }
				$this->error();
				// does $this->error() ever continue onto next line?
				return False;
			}
			// 4. standard stuff read the server data, hope for OK
			$response_array = $this->imap_read_port($expecting);
			if ($this->debug_dcom > 1) { echo 'imap.append('.__LINE__.'): here is what the server have us after all that: <br>'; }
			if ($this->debug_dcom > 1) { $this->report_svr_data($response_array, 'reopen', True); }
			// imap_read_port returns empty array is an error occurs, if no error we get an array filled with something
			if ($response_array)
			{
				$return_bool = True;
			}
			else
			{
				$return_bool = False;
			}
			if ($this->debug_dcom > 0) { echo 'imap.append('.__LINE__.'): LEAVING returning ['.serialize($return_bool).'] <br>'; }
			return $return_bool;
		}
		
		// base64  is DEPRECIATED - NOT USED
		// SEE BELOW for:  close *=DONE=*
		
		/*!
		@function createmailbox
		@abstract implements IMAP_CREATEMAILBOX
		@author Angles
		*/
		function createmailbox($stream_notused,$fq_folder) 
		{
			if ($this->debug_dcom > 0) { echo 'imap.createmailbox('.__LINE__.'): ENTERING <br>'; }
			// fq_folder is a "fully qualified folder", seperate the parts:
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder);
			$folder = $svr_data['folder'];
			// if folder has a space we need to enclose it in quotes
			if (strpos($folder, ' ') > 1)
			{
				$folder = '"'.$folder.'"';
			}
			if ($this->debug_dcom > 1) { echo 'imap.createmailbox('.__LINE__.'): processed $folder: ['.htmlspecialchars($folder).'] <br>'; }
			// assemble the server querey, looks like this:  00000004 Create INBOX.New1
			//$cmd_tag = 'm010';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' Create '.$folder;
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.createmailbox('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.createmailbox('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			// issue callback to clean cache
			$this->folder_list_did_change();
			// proceed
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.createmailbox('.__LINE__.'): LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;				
			}
			// read the server data
			$response = $this->imap_read_port($expecting);
			if ($response == False)
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.createmailbox('.__LINE__.'): LEAVING on ERROR: returning False, $this->server_last_error_str ['. htmlspecialchars($this->server_last_error_str) .']<br>'; }
				return False;
			}
			else
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.createmailbox('.__LINE__.'): LEAVING with success: returning True <br>'; }
				return True;
			}
		}
		
		/*!
		@function deletemailbox
		@abstract implements IMAP_DELETEMAILBOX
		@author Angles
		*/
		function deletemailbox($stream_notused,$fq_folder) 
		{
			if ($this->debug_dcom > 0) { echo 'imap.deletemailbox('.__LINE__.'): ENTERING <br>'; }
			// fq_folder is a "fully qualified folder", seperate the parts:
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder);
			$folder = $svr_data['folder'];
			// if folder has a space we need to enclose it in quotes
			if (strpos($folder, ' ') > 1)
			{
				$folder = '"'.$folder.'"';
			}
			if ($this->debug_dcom > 1) { echo 'imap.deletemailbox('.__LINE__.'): processed $folder: ['.htmlspecialchars($folder).'] <br>'; }
			// assemble the server querey, looks like this:  00000004 Delete "INBOX.New Two"
			//$cmd_tag = 'm011';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' Delete '.$folder;
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.deletemailbox('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.deletemailbox('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			// issue callback to clean cache
			$this->folder_list_did_change();
			// proceed
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.deletemailbox('.__LINE__.'): LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;				
			}
			// read the server data
			$response = $this->imap_read_port($expecting);
			if ($response == False)
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.deletemailbox('.__LINE__.'): LEAVING on ERROR: returning False, $this->server_last_error_str ['. htmlspecialchars($this->server_last_error_str) .']<br>'; }
				return False;
			}
			else
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.deletemailbox('.__LINE__.'): LEAVING with success: returning True <br>'; }
				return True;
			}
		}
		
		/*!
		@function renamemailbox
		@abstract implements IMAP_RENAMEMAILBOX
		@author Angles
		*/
		function renamemailbox($stream_notused,$fq_folder_old,$fq_folder_new)
		{
			if ($this->debug_dcom > 0) { echo 'imap.renamemailbox('.__LINE__.'): ENTERING <br>'; }
			// fq_folder is a "fully qualified folder", seperate the parts:
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder_old);
			$folder_old = $svr_data['folder'];
			// if folder has a space we need to enclose it in quotes
			if (strpos($folder_old, ' ') > 1)
			{
				$folder_old = '"'.$folder_old.'"';
			}
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder_new);
			$folder_new = $svr_data['folder'];
			// if folder has a space we need to enclose it in quotes
			if (strpos($folder_new, ' ') > 1)
			{
				$folder_new = '"'.$folder_new.'"';
			}
			if ($this->debug_dcom > 1) { echo 'imap.renamemailbox('.__LINE__.'): processed $folder: ['.htmlspecialchars($folder).'] <br>'; }
			// assemble the server querey, looks like this:  00000004 Rename INBOX.New1 "INBOX.New TWO"
			//$cmd_tag = 'm012';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' Rename '.$folder_old.' '.$folder_new;
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.renamemailbox('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.renamemailbox('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			// issue callback to clean cache
			$this->folder_list_did_change();
			// proceed
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.renamemailbox('.__LINE__.'): LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;				
			}
			// read the server data
			$response = $this->imap_read_port($expecting);
			if ($response == False)
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.renamemailbox('.__LINE__.'): LEAVING on ERROR: returning False, $this->server_last_error_str ['. htmlspecialchars($this->server_last_error_str) .']<br>'; }
				return False;
			}
			else
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.renamemailbox('.__LINE__.'): LEAVING with success: returning True <br>'; }
				return True;
			}
		}
		
		/**************************************************************************\
		*	DELETE a Message From the Server
		\**************************************************************************/
		/*!
		@function setflag_full
		@abstract implements IMAP_SETFLAG_FULL
		@discussion often used by mail move and delete
		@author Angles
		*/
		function setflag_full($stream_notused,$msg_list,$flags_str='',$flags)
		{
			if ($this->debug_dcom > 0) { echo 'imap_sock.setflag_full('.__LINE__.'): ENTERING <br>'; }
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & SE_UID)) )
			{
				$flags |= SE_UID;
			}
			// flags blank or  SE_UID
			// only SE_UID is supported right now, no flag is not supported because we only use the "UID" command right now
			if ($this->debug_dcom > 1) { echo 'imap_sock.setflag_full('.__LINE__.'): param $flags ['.htmlspecialchars(serialize($flags)).'], ($flags & SE_UID) is ['.htmlspecialchars(serialize(($flags & SE_UID))).'] <br>'; }
			if ($flags & SE_UID)
			{
				$using_uid = True;
			}
			else
			{
				echo 'imap_sock.setflag_full('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>';
				if ($this->debug_dcom > 0) { echo 'imap_sock.setflag_full('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>'; }
				return False;
			}
			if ($this->debug_dcom > 1) { echo 'imap_sock.setflag_full('.__LINE__.'): $flags ['.htmlspecialchars(serialize($flags)).'], $using_uid ['.htmlspecialchars(serialize($using_uid)).'] only SE_UID coded for, so continuing...<br>'; }
			// assemble the server querey, looks like this:  00000007 UID STORE 20 +Flags (\Deleted)
			//$cmd_tag = 'n014';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' UID STORE '.$msg_list.' +Flags ('.$flags_str.')';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.setflag_full('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.setflag_full('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			// proceed
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.setflag_full('.__LINE__.'): LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;				
			}
			// read the server data
			$response = $this->imap_read_port($expecting);
			if ($response == False)
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.setflag_full('.__LINE__.'): LEAVING on ERROR: returning False, $this->server_last_error_str ['. htmlspecialchars($this->server_last_error_str) .']<br>'; }
				return False;
			}
			else
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.setflag_full('.__LINE__.'): LEAVING with success: returning True <br>'; }
				return True;
			}
		}
		
		/*!
		@function delete
		@abstract implements IMAP_DELETE
		@discussion really this is a passthru to setflag_full with DELETED as the flags_str param
		@author Angles
		*/
		function delete($stream_notused,$msg_list,$flags=0)
		{
			if ($this->debug_dcom > 0) { echo 'imap_sock.delete('.__LINE__.'): ENTERING <br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.delete('.__LINE__.'): this is primary a passthru command to setflag_full with DELETED flag<br>'; }
			
			// assemble the server querey, looks like this:  00000006 UID STORE 21:25 +Flags (\DELETED)
			$response = $this->setflag_full($stream_notused, $msg_list, "\\DELETED", $flags);
			if ($response == False)
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.delete('.__LINE__.'): LEAVING on ERROR: returning False, $this->server_last_error_str ['. htmlspecialchars($this->server_last_error_str) .']<br>'; }
				return False;
			}
			else
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.delete('.__LINE__.'): LEAVING with success: returning True <br>'; }
				return True;
			}
		}
		
		/*!
		@function expunge
		@abstract implements IMAP_EXPUNGE
		@author Angles
		*/
		function expunge($stream_notused)
		{
			if ($this->debug_dcom > 0) { echo 'imap_sock.expunge('.__LINE__.'): ENTERING <br>'; }
			// assemble the server querey, looks like this:  00000007 EXPUNGE
			//$cmd_tag = 'n015';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' EXPUNGE';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.expunge('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.expunge('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			// proceed
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.expunge('.__LINE__.'): LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;				
			}
			// read the server data
			$response = $this->imap_read_port($expecting);
			if ($response == False)
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.expunge('.__LINE__.'): LEAVING on ERROR: returning False, $this->server_last_error_str ['. htmlspecialchars($this->server_last_error_str) .']<br>'; }
				return False;
			}
			else
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.expunge('.__LINE__.'): LEAVING with success: returning True <br>'; }
				return True;
			}
		}
		
		// SEE BELOW for:  fetchbody
		// SEE BELOW for:  header
		//  headers  is DEPRECIATED - NOT USED
		//  fetch_raw_mail  is DEPRECIATED - NOT USED
		// SEE BELOW for:  fetchheader
		// SEE BELOW for:  fetchstructure
		//  get_header is DEPRECIATED - NOT USED
		// SEE BELOW for:  listmailbox  *=DONE=*
		// SEE BELOW for:  mailboxmsginfo
		/*!
		@function mailcopy
		@abstract not yet implemented in IMAP sockets module
		@discussion implements imap_mail_copy
		*/
		function mailcopy($stream,$msg_list,$mailbox,$flags)
		{
			// not yet implemented
			if ($this->debug_dcom > 0) { echo 'imap: call to unimplemented socket function: mailcopy<br>'; }
			return False;
		}
		
		/*!
		@function mail_move
		@abstract implements IMAP_MAIL_MOVE
		@discussion also uses setflag_full
		@author Angles
		*/
		function mail_move($stream_notused,$msg_list,$fq_folder,$flags=0)
		{
			if ($this->debug_dcom > 0) { echo 'imap_sock.mail_move('.__LINE__.'): ENTERING <br>'; }
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & SE_UID)) )
			{
				$flags |= SE_UID;
			}
			// flags blank or  SE_UID
			// only SE_UID is supported right now, no flag is not supported because we only use the "UID" command right now
			if ($this->debug_dcom > 1) { echo 'imap_sock.mail_move('.__LINE__.'): param $flags ['.htmlspecialchars(serialize($flags)).'], ($flags & SE_UID) is ['.htmlspecialchars(serialize(($flags & SE_UID))).'] <br>'; }
			if ($flags & SE_UID)
			{
				$using_uid = True;
			}
			else
			{
				echo 'imap_sock.mail_move('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>';
				if ($this->debug_dcom > 0) { echo 'imap_sock.mail_move('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>'; }
				return False;
			}
			if ($this->debug_dcom > 1) { echo 'imap_sock.mail_move('.__LINE__.'): $flags ['.htmlspecialchars(serialize($flags)).'], $using_uid ['.htmlspecialchars(serialize($using_uid)).'] only SE_UID coded for, so continuing...<br>'; }
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder);
			$folder = $svr_data['folder'];
			// if folder has a space we need to enclose it in quotes
			if (strpos($folder, ' ') > 1)
			{
				$folder = '"'.$folder.'"';
			}
			if ($this->debug_dcom > 1) { echo 'imap.mail_move('.__LINE__.'): processed $folder: ['.htmlspecialchars($folder).'] <br>'; }
			// assemble the server querey, looks like this:  00000006 UID COPY 12:14 INBOX.Trash
			//$cmd_tag = 'n016';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' UID COPY '.$msg_list.' '.$folder;
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.mail_move('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.mail_move('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			// proceed
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.mail_move('.__LINE__.'): LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;				
			}
			// read the server data
			$success = $this->imap_read_port($expecting);
			if ($success == False)
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.mail_move('.__LINE__.'): LEAVING on ERROR: returning False, $this->server_last_error_str ['. htmlspecialchars($this->server_last_error_str) .']<br>'; }
				return False;
			}
			
			//... CONTINUE ... with FLAGS passthru call
			if ($this->debug_dcom > 0) { echo 'imap_sock.mail_move('.__LINE__.'): $success is ['.serialize($success).'] so CONTINUE with flag set <br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.mail_move('.__LINE__.'): from here on this is primary a passthru command to setflag_full with DELETED flag<br>'; }
			
			// assemble the server querey, looks like this:  00000006 UID STORE 21:25 +Flags (\DELETED)
			$response = $this->setflag_full($stream_notused, $msg_list, "\\DELETED", $flags);
			if ($response == False)
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.mail_move('.__LINE__.'): LEAVING on ERROR: returning False, $this->server_last_error_str ['. htmlspecialchars($this->server_last_error_str) .']<br>'; }
				return False;
			}
			else
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.mail_move('.__LINE__.'): LEAVING with success: returning True <br>'; }
				return True;
			}
		}
		
		//  num_msg  is DEPRECIATED - NOT USED
		/*!
		@function noop_ping_test
		@abstract implements IMAP_PING
		@discussion implements imap_ping
		@author Angles
		*/
		function noop_ping_test($stream_notused)
		{
			if ($this->debug_dcom > 0) { echo 'imap_sock.noop_ping_test('.__LINE__.'): ENTERING <br>'; }
			// assemble the server querey, looks like this:  00000007 NOOP
			//$cmd_tag = 'n016';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' NOOP';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.noop_ping_test('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.noop_ping_test('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			// proceed
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.noop_ping_test('.__LINE__.'): LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;				
			}
			// read the server data
			$response = $this->imap_read_port($expecting);
			if ($response == False)
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.noop_ping_test('.__LINE__.'): LEAVING on ERROR: returning False, $this->server_last_error_str ['. htmlspecialchars($this->server_last_error_str) .']<br>'; }
				return False;
			}
			else
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.noop_ping_test('.__LINE__.'): LEAVING with success: returning True <br>'; }
				return True;
			}
		}
		// SEE BELOW for:  open  *=DONE=*
		// function qprint moved to main msg class
		// SEE BELOW for:  reopen  *=DONE=*
		// SEE *ABOVE* for:  server_last_error
		/*
		@function i_search
		@abstract  implements IMAP_SEARCH, search the mailbox currently opened for param $criteria args
		@param $stream_notused Stream is automatically handled by the underlying code in this socket class. 
		@param $criteria is a string, delimited by spaces, in which the following keywords are allowed. 
		(See syntax for the keywords). Any multi-word arguments (eg. FROM "joey smith") must be quoted.
		@param  flags  Valid values for flags are SE_UID, which causes the returned array to contain UIDs 
		instead of messages sequence numbers.
		@result array
		@discussion: To match all unanswered messages sent by Mom, you'd use: "UNANSWERED FROM mom".
		Searches appear to be case insensitive.
		@syntax Search Keywords can be 
		ALL - return all messages matching the rest of the criteria
		ANSWERED - match messages with the \\ANSWERED flag set
		BCC "string" - match messages with "string" in the Bcc: field
		BEFORE "date" - match messages with Date: before "date"
		BODY "string" - match messages with "string" in the body of the message
		CC "string" - match messages with "string" in the Cc: field
		DELETED - match deleted messages
		FLAGGED - match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
		FROM "string" - match messages with "string" in the From: field
		KEYWORD "string" - match messages with "string" as a keyword
		NEW - match new messages
		OLD - match old messages
		ON "date" - match messages with Date: matching "date"
		RECENT - match messages with the \\RECENT flag set
		SEEN - match messages that have been read (the \\SEEN flag is set)
		SINCE "date" - match messages with Date: after "date"
		SUBJECT "string" - match messages with "string" in the Subject:
		TEXT "string" - match messages with text "string"
		TO "string" - match messages with "string" in the To:
		UNANSWERED - match messages that have not been answered
		UNDELETED - match messages that are not deleted
		UNFLAGGED - match messages that are not flagged
		UNKEYWORD "string" - match messages that do not have the keyword "string"
		UNSEEN - match messages which have not been read yet
		*/
		function i_search($stream_notused,$criteria,$flags=0)
		{
			//$empty_return=array();
			// not yet implemented
			//if ($this->debug_dcom > 0) { echo 'imap: call to unimplemented socket function: i_search<br>'; }
			//return $empty_return;
			if ($this->debug_dcom > 0) { echo 'imap.i_search('.__LINE__.'): ENTERING i_search<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap.i_search('.__LINE__.'): param $criteria is ['.htmlspecialchars($criteria).']<br>'; }
			
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & SE_UID)) )
			{
				$flags |= SE_UID;
			}
			// flags blank or  SE_UID
			// only SE_UID is supported right now, no flag is not supported because we only use the "UID" command right now
			if ($this->debug_dcom > 1) { echo 'imap_sock.i_search('.__LINE__.'): param $flags ['.htmlspecialchars(serialize($flags)).'], ($flags & SE_UID) is ['.htmlspecialchars(serialize(($flags & SE_UID))).'] <br>'; }
			if ($flags & SE_UID)
			{
				$using_uid = True;
			}
			else
			{
				echo 'imap_sock.i_search('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>';
				if ($this->debug_dcom > 0) { echo 'imap_sock.i_search('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>'; }
				return False;
			}
			if ($this->debug_dcom > 1) { echo 'imap_sock.i_search('.__LINE__.'): $flags ['.htmlspecialchars(serialize($flags)).'], $using_uid ['.htmlspecialchars(serialize($using_uid)).'] only SE_UID coded for, so continuing...<br>'; }
			
			// assemble the server querey, looks like this:   
			// 00000004 UID SEARCH ALL SEEN UNSEEN BEFORE 24-Mar-2004 SINCE 20-Nov-2003 FROM "Mailer App" TO mark SUBJECT tommy BODY "really drive"
			
			//$cmd_tag = 'k009';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' UID SEARCH ALL '.$criteria;
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.i_search('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.i_search('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.i_search('.__LINE__.'): LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;				
			}
			
			// read the server data
			$raw_response = array();
			$prepped_response = '';
			$response_array = array();
			// for some reason I get back an array with a single element, item $raw_response[0] which is the string I want to work with
			$raw_response = $this->imap_read_port($expecting);
			if ($this->debug_dcom > 2) { echo 'imap_sock.i_search('.__LINE__.'): $raw_response DUMP: <pre>'; print_r($raw_response); echo '</pre>';  }
			if (!$raw_response)
			{
				//$response_array = array();
				if ($this->debug_dcom > 1) { echo 'imap_sock.i_search('.__LINE__.'): ERROR: returning False, $this->server_last_error_str ['. htmlspecialchars($this->server_last_error_str) .']<br>'; }
				$response_array = False;
			}
			elseif ((count($raw_response) == 1)
			&& (trim($raw_response[0]) == '* SEARCH'))
			{
				if ($this->debug_dcom > 1) { echo 'imap_sock.i_search('.__LINE__.'): no matches, will retuen False<br>'; }
				$response_array = False;
			}
			else
			{
				// it is probably only 1 element, but just to be sure, do this
				$loops = count($raw_response);
				for($i=0;$i<=$loops;$i++)
				{
					// combine and also get rid of any CRLF at the end of the elements
					$prepped_response .= rtrim($raw_response[$i]);
				}
				// get rid or string "* SORT " at beginning of response, then make an array
				//$raw_response[0] = str_replace('* SORT ', '', $raw_response[0]);
				$prepped_response= str_replace('* SEARCH ', '', $prepped_response);
				//$raw_response[0] = rtrim($raw_response[0]);
				// MAKE THE ARRAY
				$response_array = explode(' ', $prepped_response);
			}
			
			if ($this->debug_dcom > 2) { echo 'imap_sock.i_search('.__LINE__.'): about to return $response_array DUMP: <pre>'; print_r($response_array); echo '</pre>';  }
			if ($this->debug_dcom > 0) { echo 'imap_sock.i_search('.__LINE__.'): LEAVING returning $response_array<br>'; }
			return $response_array;
		}
		
		// SEE BELOW for:  sort
		// SEE BELOW for:  status  *=DONE=*
		// construct_folder_str  is DEPRECIATED - NOT USED
		// deconstruct_folder_str  is DEPRECIATED - NOT USED
		// rfc_get_flag  is DEPRECIATED - NOT USED
		/*!
		@function fetch_overview
		@abstract not yet implemented in IMAP sockets module
		*/
		function fetch_overview($stream_notused,$criteria,$flags)
		{
			// not yet implemented
			if ($this->debug_dcom > 0) { echo 'imap: fetch_overview NOT YET IMPLEMENTED imap sockets function<br>'; }
			return False;
		}

		
		/**************************************************************************\
		*	OPEN and CLOSE Server Connection
		\**************************************************************************/
		/*!
		@function open
		@abstract implements php function IMAP_OPEN
		@param $fq_folder (string) 
		@param $user (string) account name to log into on the server
		@param $pass (string) password for this account on the mail server
		@param $flags (defined int) NOT YET IMPLEMENTED
		@author Angles, skeeter
		@result False on error, SocketPtr on success
		@discussion implements the functionality of php function IMAP_OPEN
		note that php IMAP_OPEN applies to IMAP, POP3 and NNTP servers
		@syntax The param $fq_folder is expected to be like this
		{ServerName:Port/options}NAMESPACE_DELIMITER_FOLDERNAME
		repeat for inline doc parser
		&#123;ServerName:Port/options&#125;NAMESPACE_DELIMITER_FOLDERNAME
		An example of this is this
		&#123;mail.example.net:143/imap&#125;INBOX.Sent
		Where INBOX is the namespace and the dot is the delimiter, which will always preceed any subfolder.
		@access public
		*/
		function open ($fq_folder, $user, $pass, $flags='')
		{
			if ($this->debug_dcom > 0) { echo 'imap: ENTERING open<br>'; }
			
			// fq_folder is a "fully qualified folder", seperate the parts:
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder);
			$folder = $svr_data['folder'];
			$server = $svr_data['server'];
			$port = $svr_data['port'];
			if ($this->debug_dcom > 0) { echo 'imap: open: svr_data:<br>'.serialize($svr_data).'<br>'; }
			
			if (!$this->open_port($server,$port,15))
			{
				echo '<p><center><b>' .lang('There was an error trying to connect to your IMAP server.<br>Please contact your admin to check the servername, username or password.') .'</b></center>';
				echo('<CENTER><A HREF="'.$GLOBALS['phpgw']->link('/home.php').'">'.lang('Click here to continue').'...</A></CENTER>'); //cbsman
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			else
			{
				$junk = $this->read_port();
				if ($this->debug_dcom > 1) { echo 'imap: open: open port server hello: "' .htmlspecialchars($this->show_crlf($junk)) .'"<br>'; }
			}


			if ($this->debug_dcom > 1) { echo 'imap: open: user and pass NO quotemeta: user ['. htmlspecialchars($user).'] pass ['.htmlspecialchars($pass).']<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap: open: user and pass WITH quotemeta: user ['. htmlspecialchars(quotemeta($user)).'] pass ['.htmlspecialchars(quotemeta($pass)).']<br>'; }
			
			//$cmd_tag = 'L001';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' LOGIN "'.$user.'" "'.$pass.'"';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap: open: write_port: '. htmlspecialchars($full_command) .'<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap: open: expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap: open: LEAVING with error: could not write_port<br>'; }
				$this->error();
				// does $this->error() ever continue onto next line?
				return False;
			}
			// server can spew some b.s. hello messages before the official response
			// read the server data
			$response_array = $this->imap_read_port($expecting);
			
			// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
			if (count($response_array) == 0)
			{
				if ($this->debug_dcom > 1)
				{
					echo 'imap: open: error in Open<br>';
					echo 'imap: open: last recorded error:<br>';
					echo  $this->server_last_error().'<br>';
				}
				if ($this->debug_dcom > 0) { echo 'imap: LEAVING Open with error<br>'; }
				return False;
			}
			else
			{
				if ($this->debug_dcom > 1) { $this->report_svr_data($response_array, 'open', True); }
				if ($this->debug_dcom > 0) { echo 'imap: open: Successful IMAP Login<br>'; }
			}
			
			// now that we have logged in, php's IMAP_OPEN would now select the desired folder
			if ($this->debug_dcom > 1) { echo 'imap: open: php IMAP_OPEN would now select desired folder: "'. htmlspecialchars($folder) .'"<br>'; }
			// php's IMAP_OPEN also selects the desired folder (mailbox) after the connection is established
			if($folder != '')
			{
				$this->reopen('',$fq_folder);
			}
			if ($this->debug_dcom > 0) { echo 'imap: LEAVING open<br>'; }
			return $this->socket;
		}

		/*!
		@function close
		@abstract implements LOGOUT
		@author Angles, skeeter
		*/
		function close($flags="")
		{
			if ($this->debug_dcom > 0) { echo 'imap: ENTERING Close<br>'; }
			
			//$cmd_tag = 'c001';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' LOGOUT';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap: close: write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap: close: expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap: close: LEAVING with error: could not write_port<br>'; }
				$this->error();
			}
			
			// server can spew some b.s. goodbye message before the official response
			// read the server data
			$response_array = $this->imap_read_port($expecting);
			
			// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
			if (count($response_array) == 0)
			{
				if ($this->debug_dcom > 1)
				{
					echo 'imap: close: error in Close<br>';
					echo 'imap: close: last recorded error:<br>';
					echo  $this->server_last_error().'<br>';
				}
				if ($this->debug_dcom > 0) { echo 'imap: Leaving Close with error<br>'; }
				return False;				
			}
			else
			{
				if ($this->debug_dcom > 1) { $this->report_svr_data($response_array, 'close', True); }
				if ($this->debug_dcom > 0) { echo 'imap: LEAVING Close<br>'; }
				return True;
			}
		}

		/*!
		@function reopen
		@abstract implements last part of IMAP_OPEN and all of IMAP_REOPEN
		@param $stream_notused Socket class handles stream reference internally
		@param $fq_folder (string) "fully qualified folder" {SERVER_NAME:PORT/OPTIONS}FOLDERNAME 
		repeat for inline docs &#123;SERVER_NAME:PORT/OPTIONS&#125;NAMESPACE_DELIMITER_FOLDERNAME
		@param $flags (defined int) Not Yet Implemented
		@result boolean True on success or False on error
		@author Angles
		@access public
		*/
		function reopen($stream_notused, $fq_folder, $flags='')
		{
			if ($this->debug_dcom > 0) { echo 'imap: ENTERING reopen<br>'; }
			
			// fq_folder is a "fully qualified folder", seperate the parts:
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder);
			$folder = $svr_data['folder'];
			if ($this->debug_dcom > 0) { echo 'imap: reopen: folder value is: ['.$folder.']<br>'; }
			
			//$cmd_tag = 'r001';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' SELECT "'.$folder.'"';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap: reopen: write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap: reopen: expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap: reopen: could not write_port<br>'; }
				$this->error();
			}
			
			// read the server data
			$response_array = $this->imap_read_port($expecting);
			
			// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
			if (count($response_array) == 0)
			{
				if ($this->debug_dcom > 1)
				{
					echo 'imap: reopen: error in reopen<br>';
					echo 'imap: reopen: last recorded error:<br>';
					echo  $this->server_last_error().'<br>';
				}
				if ($this->debug_dcom > 0) { echo 'imap: LEAVING reopen with error<br>'; }
				return False;				
			}
			else
			{
				if ($this->debug_dcom > 1) { $this->report_svr_data($response_array, 'reopen', True); }
				if ($this->debug_dcom > 0) { echo 'imap: LEAVING reopen<br>'; }
				return True;
			}
		}

		/*!
		@function listmailbox
		@abstract implements IMAP_LISTMAILBOX
		@param $stream_notused Socket class handles stream reference internally
		@param $server_str (string) {SERVER_NAME:PORT/OPTIONS} 
		repeat for inline docs &#123;SERVER_NAME:PORT/OPTIONS&#125;
		@param $pattern (string) can be a namespace, or a mailbox name, or a namespace_delimiter, 
		or a namespace_delimiter_mailboxname, AND/OR including either "%" or "*" (see discussion below)
		@result an array containing the names of the mailboxes. 
		@discussion: if param $pattern includes some form of mailbox reference, that tells the server where in the
		mailbox hierarchy to start searching. If neither wildcard "%" nor "*" follows said mailbox reference, then the
		server returns the delimiter and the namespace for said mailbox reference. More typically, either one of the
		wildcards "*" or "%" follows said mailbox reference, in which case the server behaves as such:
		_begin_PHP_MANUAL_quote: There are two special characters you can pass as part of the pattern: '*' and '%'.
		'*' means to return all mailboxes. If you pass pattern as '*', you will get a list of the entire mailbox hierarchy. 
		'%' means to return the current level only. '%' as the pattern parameter will return only the top level mailboxes; 
		'~/mail/%' on UW_IMAPD will return every mailbox in the ~/mail directory, but none in subfolders of that directory.
		_end_quote_
		See RFC 2060 Section 6.3.8 (client specific) and Section 7.2.2 (server specific) for more details.
		The imap LIST command takes 2 params , the first is either blank or a mailbox reference, the second is either blank
		or one of the wildcard tokens "*" or "%". PHP's param $pattern is a combination of the imap LIST command's
		2 params, the difference between the imap and the php param(s) is that the php param $pattern will contain
		both mailbox reference AND/OR one of the wildcaed tokens in the same string, whereas the imap command
		seperates the wildcard token from the mailbox reference. I refer to IMAP_LISTMAILBOX's 2nd param as
		$server_str here while the php manual calls that same param "$ref", which is somewhat misnamed because the php
		manual states "ref should normally be just the server specification as described in imap_open()" which apparently
		means the server string {serverName:port/options} with no namespace, no delimiter, nor any mailbox name.
		@author Angles, skeeter
		@access public
		*/
		function listmailbox($stream_notused,$server_str,$pattern)
		{
			if ($this->debug_dcom > 0) { echo 'imap: ENTERING listmailbox<br>'; }
			$mailboxes_array = Array();
			
			// prepare params, seperate wildcards "*" or "%" from param $pattern
			// LIST param 1 is empty or is a mailbox reference string withOUT any wildcard
			// LIST param 2 is empty or is the wildcard either "%" or "*"
			if ((strstr($pattern, '*'))
			|| (strstr($pattern, '%')))
			{
				if (($pattern == '*')
				|| ($pattern == '%'))
				{
					// no mailbox reference string, so LIST param 1 is empty
					$list_params = '"" "' .$pattern .'"';
				}
				else
				{
					// just assume the * or % is at the end of the string
					// seperate it from the rest of the pattern
					$boxref = substr($pattern, 0, -1);
					$wildcard = substr($pattern, -1);
					$list_params = '"' .$boxref .'" "' .$wildcard .'"';
				}
			}
			elseif (strlen($pattern) == 0)
			{
				// empty $pattern equates to both LIST params being empty, which IS Valid
				$list_params = '"" ""';
			}
			else
			{
				// we have a string with no wildcard, so LIST param 2 is empty
				$list_params = '"' .$pattern .'" ""';
			}

			//$cmd_tag = 'X001';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' LIST '.$list_params;
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap: listmailbox: write_port: ['. htmlspecialchars($full_command) .']<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap: listmailbox: expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap: listmailbox: could not write_port<br>'; }
				$this->error();
			}
			
			// read the server data
			$response_array = $this->imap_read_port($expecting);
			
			// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
			if (count($response_array) == 0)
			{
				if ($this->debug_dcom > 1)
				{
					echo 'imap: listmailbox: error in listmailbox<br>';
					echo 'imap: listmailbox: last recorded error:<br>';
					echo  $this->server_last_error().'<br>';
				}
				if ($this->debug_dcom > 0) { echo 'imap: Leaving listmailbox with error<br>'; }
				return False;				
			}
			else
			{
				if ($this->debug_dcom > 1) { $this->report_svr_data($response_array, 'reopen', True); }
			}
			
			// delete all text except the folder name
			for ($i=0; $i<count($response_array); $i++)
			{
				// don't include "noselect" folders
				if (stristr($response_array[$i], '\NoSelect'))
				{
					// do nothing
				}
				else
				{
					// get everything to the right of the quote_space " , INCLUDES the quote_space itself
					$folder_name = strstr($response_array[$i],'" ');
					// delete that quote_space and trim
					$folder_name = trim(substr($folder_name, 2));
					// if the folder name includes space(s) then it will be enclosed in quotes
					// note: Courier puts all folder names in quotes
					if ((strlen($folder_name) > 0)
					&& ($folder_name[0] == '"') )
					{
						// delete the opening quote
						$folder_name = substr($folder_name, 1);
						// delete the closing quote
						$folder_name = substr($folder_name, 0, -1);
					}
					// it looks like sockets data xfer with Courier adds escape slashes
					// experiment: STRIP these magic slashes
					$folder_name = stripslashes($folder_name);
					// php builtin function returns the server_str before the folder name
					$folder_name = $server_str .$folder_name;
					// add to the result array
					$next_pos = count($mailboxes_array);
					$mailboxes_array[$next_pos] = $folder_name;
				}
			}
			
			if ($this->debug_dcom > 1) { $this->report_svr_data($mailboxes_array, 'listmailbox INTERNAL_mailboxes_array', False); }
			if ($this->debug_dcom > 0) { echo 'imap: LEAVING listmailbox<br>'; }
			//return '';
			return $mailboxes_array;
		}
		
		
		/**************************************************************************\
		*	Mailbox Status and Information
		\**************************************************************************/
		
		/*!
		@function mailboxmsginfo
		@abstract not yet implemented in IMAP sockets module
		@discussion implements imap_mailboxmsginfo
		*/
		function mailboxmsginfo($stream_notused='')
		{
			if ($this->debug_dcom > 0) { echo 'imap: mailboxmsginfo NOT YET IMPLEMENTED imap sockets function<br>'; }
			return False;
		}
		
		/*
		function mailboxmsginfo($folder='')
		{
			$info = new msg_mb_info;
			if($folder=='' || $folder==$this->folder)
			{
				$info->messages = $this->num_msgs;
				if ($info->messages)
				{
					$info->size = $this->total($this->fetch_field(1,$info->messages,'RFC822.SIZE'));
					return $info;
				}
				else
				{
					return False;
				}
			}
			else
			{
				$mailbox = $folder;
			}
			
			$info->messages = $this->num_msgs($mailbox);
			$info->size  = $this->total($this->fetch_field(1,$info->messages,'RFC822.SIZE'));
			
			if ($info->messages)
			{
				return $info;
			}
			else
			{
				return False;
			}
		}
		*/
		
		/*!
		@function status
		@abstract implements php function IMAP_STATUS
		@param $stream_notused  socket class handles stream reference internally
		@param $fq_folder (string) &#123;SERVER_NAME:PORT/OPTIONS&#125;FOLDERNAME
		@param $flags (defined int) see syntax for available flags
		@author Angles, skeeter
		@discussion implements the functionality of php function IMAP_STATUS. Mailserver 
		*should* return a single line of data, (data does not include the tag completion line) 
		But, in certain cases buggy servers in non-error situations may return 2 lines of data, instead on 1 line, 
		see example for an example.
		This quote from the IMAP RFC summarizes the STATUS command  
		" The STATUS command provides an alternative to opening a second IMAP4rev1 connection 
		and doing an EXAMINE command on a mailbox to query that mailbox's status without 
		deselecting the current mailbox in the first IMAP4rev1 connection".
		@syntax The param flags gives instructions on what info to get, default is SA_ALL, flags are
		SA_MESSAGES - set status->messages to the number of messages in the mailbox
		SA_RECENT - set status->recent to the number of recent messages in the mailbox
		SA_UNSEEN - set status->unseen to the number of unseen (new) messages in the mailbox
		SA_UIDNEXT - set status->uidnext to the next uid to be used in the mailbox
		SA_UIDVALIDITY - set status->uidvalidity to a constant that changes when uids for the mailbox may no longer be valid
		SA_ALL - set all of the above
		@example This is an examle of a buggy imap server returning 2 lines instead of 1 line, in a STATUS reply
		-- ArrayPos[0] data: * NO CLIENT BUG DETECTED: STATUS on selected mailbox: INBOX
		-- ArrayPos[1] data: * STATUS INBOX (MESSAGES 724 RECENT 0 UNSEEN 436 UIDNEXT 843 UIDVALIDITY 1005967489)
		Normally only the line with the actual data would be returned by the server
		@access public
		*/
		function status($stream_notused='', $fq_folder='',$options=SA_ALL)
		{
			if ($this->debug_dcom > 0) { echo 'imap: ENTERING status<br>'; }
			
			// fq_folder is a "fully qualified folder", seperate the parts:
			$svr_data = array();
			$svr_data = $this->distill_fq_folder($fq_folder);
			$folder = $svr_data['folder'];
			// build the query string
			$query_str = '';
			$available_options = Array(
				SA_MESSAGES	=> 'MESSAGES',
				SA_RECENT	=> 'RECENT',
				SA_UNSEEN	=> 'UNSEEN',
				SA_UIDNEXT	=> 'UIDNEXT',
				SA_UIDVALIDITY	=> 'UIDVALIDITY'
			);
			@reset($available_options);
			while(list($key,$value) = each($available_options))
			{
				if($options & $key)
				{
					$query_str .= $value.' ';
				}
			}
			$query_str = trim($query_str);
			
			//$cmd_tag = 's001';
			$cmd_tag = $this->get_next_cmd_num();
			//$full_command = $cmd_tag.' STATUS '.$svr_data['folder'].' (MESSAGES RECENT UIDNEXT UIDVALIDITY UNSEEN)';
			$full_command = $cmd_tag.' STATUS "'.$svr_data['folder'].'" ('.$query_str.')';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap: status: write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap: status: expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap: status: LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;				
			}
			
			// read the server data
			$response_array = $this->imap_read_port($expecting);
			
			
			// TEST THIS ERROR DETECTION - empty array = error (BAD or NO)
			if (count($response_array) == 0)
			{
				if ($this->debug_dcom > 1)
				{
					echo 'imap: status: error in status<br>';
					echo 'imap: status: last recorded error:<br>';
					echo  $this->server_last_error().'<br>';
				}
				if ($this->debug_dcom > 0) { echo 'imap: LEAVING status with error<br>'; }
				return False;				
			}
			
			// STATUS should only return 1 line of data
			//if (count($response_array) > 1)
			// BUGGY UWASH IMAP SERVERS RETURN 2 LINES HERE, so increase to > 2
			//if (count($response_array) > 2)
			//{
			//	if ($this->debug_dcom > 1)
			//	{
			//		echo 'imap: status: error in status, more than (one) TWO lines (for buggy uwash servers) line server response, not normal<br>';
			//		echo 'imap: status: last recorded error:<br>';
			//		echo  $this->server_last_error().'<br>';
			//	}
			//	if ($this->debug_dcom > 0) { echo 'imap: Leaving status with error<br>'; }
			//	return False;				
			//}
			
			// if we get here we have valid server data
			if ($this->debug_dcom > 1) { $this->report_svr_data($response_array, 'status', True); }
			
			
			// initialize structure
			$info = new mailbox_status;
			$info->messages = '';
			$info->recent = '';
			$info->unseen = '';
			$info->uidnext = '';
			$info->uidvalidity = '';
			
			// for buggy servers that return 2 lines, the last line has the data
			// for compliant 1 line responses, that single line can also be defined as the "last" line of data
			// so to account for buggy servers, we'll take the LAST array element\
			$last_line = (count($response_array) - 1);
			$response_line_of_data = $response_array[$last_line];
			
			// ERROR CHECK
			if (stristr($response_line_of_data, '* STATUS') == False)
			{
				if ($this->debug_dcom > 1)
				{
					echo 'imap: status: error in status, $response_line_of_data does not have "* STATUS" so it is not valid data<br>';
					echo 'imap: status: last recorded error:<br>';
					echo  $this->server_last_error().'<br>';
				}
				if ($this->debug_dcom > 0) { echo 'imap: LEAVING status with error at '.__LINE__.'<br>'; }
				return False;				
			}
			
			
			// ok... 
			//typical server data:
			// * STATUS INBOX (MESSAGES 15 RECENT 1 UNSEEN 2 UIDNEXT 17 UIDVALIDITY 1005967489)
			// data starts after the mailbox name, which could actually have similar strings as the status querey
			// get data the includes and follows the opening paren
			// $status_data_raw = strstr($response_array[0], '(');
			$status_data_raw = strstr($response_array[$last_line], '(');
			
			
			// snarf any of the 5 possible pieces of data if they are present
			$status_data['messages'] = $this->snarf_status_data($status_data_raw, 'MESSAGES');
			$status_data['recent'] = $this->snarf_status_data($status_data_raw, 'RECENT');
			$status_data['unseen'] = $this->snarf_status_data($status_data_raw, 'UNSEEN');
			$status_data['uidnext'] = $this->snarf_status_data($status_data_raw, 'UIDNEXT');
			$status_data['uidvalidity'] = $this->snarf_status_data($status_data_raw, 'UIDVALIDITY');
			
			// fill structure and unset any unfilled data elements
			if ($status_data['messages'] != '')
			{
				$info->messages = $status_data['messages'];
			}
			else
			{
				unset($info->messages);
			}
			if ($status_data['recent'] != '')
			{
				$info->recent = $status_data['recent'];
			}
			else
			{
				unset($info->recent);
			}
			if ($status_data['unseen'] != '')
			{
				$info->unseen = $status_data['unseen'];
			}
			else
			{
				unset($info->unseen);
			}
			if ($status_data['uidnext'] != '')
			{
				$info->uidnext = $status_data['uidnext'];
			}
			else
			{
				unset($info->uidnext);
			}
			if ($status_data['uidvalidity'] != '')
			{
				$info->uidvalidity = $status_data['uidvalidity'];
			}
			else
			{
				unset($info->uidvalidity);
			}
			// function "sort" needs to know "->messages" the total num msgs in (hopefully) this folder
			// so L1 class var cache it so "sort" does not have to call this function if it has ALREADY been run
			$this->mailbox_status = $info;
			if ($this->debug_dcom > 1) { echo 'imap: status: L1 class var caching: $this->mailbox_status DUMP:<pre>'; print_r($this->mailbox_status); echo '</pre>'; }
			if ($this->debug_dcom > 0) { echo 'imap: LEAVING status<br>'; }
			return $info;
		}
		
		/*!
		@function snarf_status_data
		@abstract Utility function used by STATUS function
		@author Angles
		@access private
		*/
		function snarf_status_data($status_raw_str='',$snarf_this='')
		{
			// bogus data detection
			if (($status_raw_str == '')
			|| ($snarf_this == ''))
			{
				return '';
			}
			// fallback value
			$return_data = '';
			
			//typical server data:
			// * STATUS INBOX (MESSAGES 15 RECENT 1 UNSEEN 2 UIDNEXT 17 UIDVALIDITY 1005967489)
			
			// see if $snarf_this is in the raw data
			$data_mini_str = stristr($status_raw_str, $snarf_this);
			if ($data_mini_str != False)
			{
				// $data_mini_str has everything including and to the right of $snarf_this
				// integer follows $snarf_this+space
				$delete_len = strlen($snarf_this.' ');
				// delete up to integer
				$data_mini_str = substr($data_mini_str, $delete_len);
				// integer will be followed by (A) a space ' ' or (B) a closing paren ')', or (C) any non-integer char
				for ($i=0; $i< strlen($data_mini_str); $i++)
				{
					if ((ord($data_mini_str[$i]) >= chr(0))
					&& (ord($data_mini_str[$i]) <= chr(9)))
					{
						// continue looking, this is integer data
					}
					else
					{
						// we reached a non-integer, so the position just prior to this ends the integer data
						$data_end = $i - 1;
						break;
					}
				}
				// snarf the data
				$data_mini_str = trim(substr($data_mini_str, 0, $data_end));
				$return_data = (int)$data_mini_str;
				if ($this->debug_dcom > 1) { echo 'imap: snarf_status_data: '.$snarf_this.' = '.$return_data.'<br>'; }
			}
			return $return_data;
		}
		
		/*!
		@function num_msg
		@abstract OBSOLETED
		*/
		// OBSOLETED
		function num_msg($folder='')
		{
			if($folder == '' || $folder == $this->folder)
			{
				return $this->num_msgs;
			}
			return $this->status_query($folder,'MESSAGES');
		}
		
		/*!
		@function total
		@abstract OBSOLETED
		*/
		// OBSOLETED
		function total($field)
		{
			$total = 0;
			reset($field);
			while(list($key,$value) = each($field))
			{
				$total += intval($value);
			}
			return $total;
		}
		
		/**************************************************************************\
		*	Message Number Stuff
		\**************************************************************************/
		/*!
		@function msgno_to_uid
		@abstract returns the UID for the given message sequence number **NOT CURRENTLY USED**
		@param $stream_notused Stream is automatically handled by the underlying code in this socket class.
		@param $msg_num (int) is the message sequence number, i.e. not its UID which is what we want to get.
		@author Angles
		@discussion not yet implemented in IMAP sockets module. 
		implements imap_uid
		*/
		function msgno_to_uid($stream_notused,$msg_num)
		{
			// not yet implemented
			if ($this->debug_dcom > 0) { echo 'imap: call to unimplemented socket function: msgno_to_uid<br>'; }
			return False;
		}
		/*!
		@function uid_to_msgno
		@abstract returns the message sequence number for the given UID
		@param $stream_notused Stream is automatically handled by the underlying code in this socket class.
		@param $msg_num_uid (int) is the message UID, i.e. not its sequence number which is what we want to get.
		@author Angles
		@discussion not yet implemented in IMAP sockets module 
		Implements imap_msgno. 
		Change UID number into msg sequence number because functions like "imap_header" aka "imap_headerinfo" do not take UIDs, 
		in this class "imap_header" is wrapped by class function "header"
		RFC2060 6.4.8. UID Command
		Yes do not be confused, we use IMAP "UID Command" in a php command 
		called "imap_msgno" BUT NOT IN php command "imap_uid" HAHA. 
		Often we need the simple sequence number for other IMAP commands 
		so we use this function with a UID as the arg and get back the sequence number. 
		Since we use the FETCH command as the carrier here, we can ask for and get other  
		data at the same time just added to the data we get back, such as flags. But the
		php-imap function only wants the sequence number.
		EXAMPLE: 
			C: 00000007 UID FETCH 131 UID
			S: * 97 FETCH (UID 131)
			S: 00000007 OK Completed
		*/
		function uid_to_msgno($stream_notused,$msg_num_uid)
		{
			// not yet implemented
			if ($this->debug_dcom > 0) { echo 'imap: call to unimplemented socket function: uid_to_msgno<br>'; }
			return False;
		}
		
		/**************************************************************************\
		*	Message Sorting
		\**************************************************************************/
		/*!
		@function sort
		@abstract not yet implemented in IMAP sockets module
		@param $stream_notused 
		@param $criteria (defined int) 0=SORTDATE , 1=SORTARRIVAL, 2=SORTFROM, 3=SORTSUBJECT, 4=SORTTO, 5=SORTCC, 6=SORTSIZE 
		@param $reverse 1=reverse-sorting, 0=regular sort is low to high by default
		@param $flags (bitmask) SE_UID return UIDs instead of sequence numbers, SE_NOPREFETCH dont prefetch searched messages.
		@returns array of message numbers for a folder.
		@author Angles
		@discussion implements imap_sort
		@example
			00000006 UID SORT (REVERSE ARRIVAL)  US-ASCII ALL
		*/
		function sort($stream_notused,$criteria=SORTARRIVAL,$reverse=False,$flags=0)
		{
			if ($this->debug_dcom > 0) { echo 'imap_sock.sort('.__LINE__.'): ENTERING sort <br>'; }
			//if ($this->debug_dcom > 0) { echo 'imap: sort NOT YET IMPLEMENTED imap sockets function<br>'; }
			//return False;
			
			// criteria
			$str_criteria = '';
			if ($criteria == SORTDATE)
			{
				$str_criteria = 'ARRIVAL';
			}
			elseif ($criteria == SORTARRIVAL)
			{
				$str_criteria = 'ARRIVAL';
			}
			elseif ($criteria == SORTFROM)
			{
				$str_criteria = 'FROM';
			}
			elseif ($criteria == SORTSUBJECT)
			{
				$str_criteria = 'SUBJECT';
			}
			elseif ($criteria == SORTTO)
			{
				$str_criteria = 'TO';
			}
			elseif ($criteria == SORTCC)
			{
				$str_criteria = 'CC';
			}
			elseif ($criteria == SORTSIZE)
			{
				$str_criteria = 'SIZE';
			}
			else
			{
				$str_criteria = 'ARRIVAL';
			}
			if ($this->debug_dcom > 1) { echo 'imap_sock.sort('.__LINE__.'): param $criteria ['.htmlspecialchars(serialize($criteria)).'], $str_criteria ['.$str_criteria.'] <br>'; }
			
			// order
			$str_reverse = '';
			if ($reverse)
			{
				$str_reverse = 'REVERSE';
			}
			else
			{
				$str_reverse = '';
			}
			if ($this->debug_dcom > 1) { echo 'imap_sock.sort('.__LINE__.'): param $reverse ['.htmlspecialchars(serialize($reverse)).'], $str_reverse ['.$str_reverse.'] <br>'; }
			
			// format the space for final querey
			$reverse_and_criteria = '';
			if ($str_reverse != '')
			{
				$reverse_and_criteria = $str_reverse.' '.$str_criteria;
			}
			else
			{
				// no space needed, only one param
				$reverse_and_criteria = $str_criteria;
			}
			if ($this->debug_dcom > 1) { echo 'imap_sock.sort('.__LINE__.'): final $reverse_and_criteria ['.$reverse_and_criteria.'] <br>'; }
			
			// CHARSET` (taken from RFC2060 Section  6.4.4.  SEARCH Command)
			// US-ASCII MUST be supported; other [CHARSET]s MAY be supported.
			$str_charset = 'US-ASCII';
			// search key "ALL"` (taken from RFC2060 Section  6.4.4.  SEARCH Command)
			// All messages in the mailbox; the default initial key for ANDing.
			$str_search_key = 'ALL';
			
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & SE_UID)) )
			{
				$flags |= SE_UID;
			}
			// flags blank or  SE_UID andor SE_NOPREFETCH
			// only SE_UID is supported right now, no flag is not supported because we only use the "UID" command right now
			if ($this->debug_dcom > 1) { echo 'imap_sock.sort('.__LINE__.'): param $flags ['.htmlspecialchars(serialize($flags)).'], ($flags & SE_UID) is ['.htmlspecialchars(serialize(($flags & SE_UID))).'] <br>'; }
			if ($flags & SE_UID)
			{
				$using_uid = True;
			}
			else
			{
				echo 'imap_sock.sort('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>';
				if ($this->debug_dcom > 0) { echo 'imap_sock.sort('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>'; }
				return False;
			}
			if ($this->debug_dcom > 1) { echo 'imap_sock.sort('.__LINE__.'): $flags ['.htmlspecialchars(serialize($flags)).'], $using_uid ['.htmlspecialchars(serialize($using_uid)).'] only SE_UID coded for, so continuing...<br>'; }
			
			
			// assemble the server querey, looks like this:  00000006 UID SORT (REVERSE ARRIVAL)  US-ASCII ALL
			//$cmd_tag = 's006';
			$cmd_tag = $this->get_next_cmd_num();
			$full_command = $cmd_tag.' UID SORT ('.$reverse_and_criteria.') '.$str_charset.' '.$str_search_key;
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.sort('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.sort('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.sort('.__LINE__.'): LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;				
			}
			
			// read the server data
			$raw_response = array();
			$prepped_response = '';
			$response_array = array();
			// for some reason I get back an array with a single element, item $raw_response[0] which is the string I want to work with
			$raw_response = $this->imap_read_port($expecting);
			if ($this->debug_dcom > 2) { echo 'imap_sock.sort('.__LINE__.'): $raw_response DUMP: <pre>'; print_r($raw_response); echo '</pre>';  }
			if (!$raw_response)
			{
				$response_array = array();
			}
			else
			{
				// it is probably only 1 element, but just to be sure, do this
				$loops = count($raw_response);
				for($i=0;$i<=$loops;$i++)
				{
					// combine and also get rid of any CRLF at the end of the elements
					$prepped_response .= rtrim($raw_response[$i]);
				}
				// get rid or string "* SORT " at beginning of response, then make an array
				//$raw_response[0] = str_replace('* SORT ', '', $raw_response[0]);
				$prepped_response= str_replace('* SORT ', '', $prepped_response);
				//$raw_response[0] = rtrim($raw_response[0]);
				// MAKE THE ARRAY
				$response_array = explode(' ', $prepped_response);
			}
			
			if ($this->debug_dcom > 2) { echo 'imap_sock.sort('.__LINE__.'): about to return $response_array DUMP: <pre>'; print_r($response_array); echo '</pre>';  }
			if ($this->debug_dcom > 0) { echo 'imap_sock.sort('.__LINE__.'): LEAVING returning $response_array<br>'; }
			return $response_array;
		}
		
		
		/*!
		@function fetch_header
		@abstract Under Construction - Used by sort 
		@description OBSOLETED
		*/
		function fetch_header($start,$stop,$element)
		{
			if ($this->debug_dcom > 0) { echo 'imap: ENTERING fetch_header<br>'; }
			
			if(!$this->write_port('a001 FETCH '.$start.':'.$stop.' RFC822.HEADER'))
			{
				$this->error();
			}
			$field_element = array();
			
			for($i=$start;$i<=$stop;$i++)
			{
				$response = $this->read_port();
				//while(!ereg('FETCH completed',$response))
				while(chop($response)!='')
				{
					if ($this->debug_dcom > 1) { echo 'imap: fetch_header: Response = '.$response."<br>\r\n"; } 
					if(ereg('^\*',$response))
					{
						$field = explode(' ',$response);
						$msg_num = $field[1];
					}
					if(ereg('^'.$element,$response))
					{
						$field_element[$msg_num] = $this->phpGW_quoted_printable_decode2(substr($response,strlen($element)+1));
						if ($this->debug_dcom > 1) { echo 'imap: fetch_header: <b>Field:</b> '.$field_element[$msg_num]."\t = <b>Msg Num</b> ".$msg_num."<br>\r\n"; } 
					}
					elseif(ereg('^'.strtoupper($element),$response))
					{
						$field_element[$msg_num] = $this->phpGW_quoted_printable_decode2(substr($response,strlen(strtoupper($element))+1));
						if ($this->debug_dcom > 1) { echo 'imap: fetch_header: <b>Field:</b> '.$field_element[$msg_num]."\t = <b>Msg Num</b> ".$msg_num."<br>\r\n"; } 
					}
					$response = $this->read_port();
				}
				$response = $this->read_port();
			}
			$response = $this->read_port();
			if ($this->debug_dcom > 1) { echo 'imap: fetch_header: returning $field_element ['.$field_element.'] <br>'; } 
			if ($this->debug_dcom > 0) { echo 'imap: LEAVING fetch_header<br>'; }
			return $field_element;
		}
		
		
		/*!
		@function fetch_field
		@abstract Under Construction
		@discussion OBSOLETED
		*/
		function fetch_field($start,$stop,$element)
		{
			if(!$this->write_port('a001 FETCH '.$start.':'.$stop.' '.$element))
			{
				$this->error();
			}
			$response = $this->read_port();
			while(!ereg('FETCH completed',$response))
			{
				//echo 'Response = '.$response."<br>\n";
				$field = explode(' ',$response);
				$msg_num = intval($field[1]);
				$field_element[$msg_num] = substr($field[4],0,strpos($field[4],')'));
				//echo '<b>Field:</b> '.substr($field[4],0,strpos($field[4],')'))."\t = <b>Msg Num</b> ".$field_element[substr($field[4],0,strpos($field[4],')'))]."<br>\n";
				$response = $this->read_port();
			}
			return $field_element;
		}		
		

		/**************************************************************************\
		*
		*	Message Structural Information - FETCHSTRUCTURE
		*
		\**************************************************************************/

		/*!
		@function make_param
		@abstract 
		@param $parameters_str (string) space seperated attrib value items NO PARENS
		@discussion strip open and close parens before feeding into here. You get back an 
		array of parameter objects as nevessary
		@author Angles
		*/
		function make_param($parameters_str)
		{
			$tmp_data = array();
			$tmp_data['parameters_str'] = $parameters_str;
			if ($this->debug_dcom > 0) { echo '<pre>'.'make_param('.__LINE__.'): ENTERING param $parameters_str BETTER already have open and close parens stripped! '."\n"; }
			if ($this->debug_dcom > 1) { echo 'make_param('.__LINE__.'): $tmp_data[parameters_str] is: ['.$tmp_data['parameters_str'] ."]\n"; }
			// what we have now looks somethign like one of these
			// "CHARSET" "utf-8"
			// "TYPE" "multipart/alternative" "BOUNDARY" "----=_NextPart_000_00B9_01C3CE2B.BE78A8C0"
			// "NAME" "GTMASTERsplash copy.pdf"
			$tmp_data['params_exploded'] = array();
			$tmp_data['parameters'] = array();
			// no this b0rks on items with spaces in them
			//$tmp_data['params_exploded'] = explode(' ', $tmp_data['parameters_str']);
			$tmp_data['params_exploded'] = explode('" "', $tmp_data['parameters_str']);
			// loop to clean of leading and trailing quotes
			$loops = count($tmp_data['params_exploded']);
			for ($i=0; $i < $loops ;$i++)
			{
				$this_str = $tmp_data['params_exploded'][$i];
				if ($this_str{0} == '"')
				{
					$this_str = substr($this_str, 1);
				}
				$last_pos = (strlen($this_str) - 1);
				if ($this_str{$last_pos} == '"')
				{
					$this_str = substr($this_str, 0, $last_pos);
				}
				$tmp_data['params_exploded'][$i] = $this_str;
			}
			//echo 'make_param('.__LINE__.'): post-cleaning $tmp_data[params_exploded] is: ['.serialize($tmp_data['params_exploded'])."]\n";
			if ($this->debug_dcom > 2) { echo 'make_param('.__LINE__.'): post-cleaning $tmp_data[params_exploded] DUMP: '.""; print_r($tmp_data['params_exploded']); echo ""; }
			// loop to make param objects
			$loops = count($tmp_data['params_exploded']);
			for ($i=0; $i < $loops ;$i=($i+2))
			{
				$attribute = 'UNKNOWN_PARAM_ATTRIBUTE';
				if ((isset($tmp_data['params_exploded'][$i]))
				&& (trim($tmp_data['params_exploded'][$i]) != ''))
				{
					$attribute = $tmp_data['params_exploded'][$i];
				}
				$value = 'UNKNOWN_PARAM_VALUE';
				$val_pos = $i+1;
				if ((isset($tmp_data['params_exploded'][$val_pos]))
				&& (trim($tmp_data['params_exploded'][$val_pos]) != ''))
				{
					$value = $tmp_data['params_exploded'][$val_pos];
				}
				// make this param pair object
				$tmp_data['parameters'][] = new msg_params($attribute,$value);
			}
			if ($this->debug_dcom > 2) { echo 'make_param('.__LINE__.'): post-looping $tmp_data[parameters] DUMP: '.""; print_r($tmp_data['parameters']); echo ""; }
			//echo 'make_param('.__LINE__.'): $tmp_data[parameters] is: ['.serialize($tmp_data['parameters'])."]\n";
			if ($this->debug_dcom > 0) { echo 'make_param('.__LINE__.'): LEAVING returning $tmp_data[parameters]'.'</pre>'."\n"; }
			return $tmp_data['parameters'];
		}

		/*!
		@function make_msg_struct
		@abstract build php-imap style fetstructure body object
		@param $called_from (string) used for debugging
		@param $ref_parent REFERENCE of type either "msg_structure" or "parts_parent_stub"
		either object has element VAR->parts[] which is what is important.
		@author Angles
		@discussion under construction. Uses recursive calls. 
		Each call lasts only for one part.
		@example
			// this is the order of the stuff
			// "both" = both simple and multipart contain this data
			// otherwise only the simple struct contains that data
			/*
			1a		type			"TEXT"
			1b		() paren struct implied by multitype
		both	2		subtype		"PLAIN"
		both	3		parameters		("CHARSET" "utf-8")
			4		id				NIL
			5		description	string or NIL
			6		encoding		"QUOTED-PRINTABLE"
			7		bytes			484
			8a		lines
					// THEN IT DEPENDS
					if type TEXT (and some other types) then  "lines" goes here
			8b		or IF MESSAGE RFC822 encap the a paren (bodystruct) structure goes here
			
			// 		THIS BEGINS OPTIONAL "EXTENSION DATA" ALWAYS IN ORDER FROM HERE, MAY END AT ANY TIME THOUGH
			9		MD5
		both	10		disposition	("INLINE" NIL)
		both	11		language		NIL
			// plus some other stuff
		@access private
		*/
		function make_msg_struct($called_from='', &$ref_parent)
		{
	
			if ($this->debug_dcom > 0) { echo '<pre>'.'bs('.__LINE__.'): ENTERING make_msg_struct, called from ['.$called_from."]\n\n"; }
			$this_level_debth = 0;
			if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $this->bs_cur_debth ['.$this->bs_cur_debth.'], $this->bs_max_debth ['.$this->bs_max_debth.'], $this_level_debth ['.$this_level_debth.']'."\n"; }
			if ($this->debug_dcom > 2) { echo 'bs: CURRENT $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
			$tmp_data=array();
			
			if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): ** looking for standard open paren'."\n\n"; }
			
			// LEVEL ANALYSIS
			if ($this->bs_rawstr{0} == '(')
			{
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): found standard open paren, increasing debths'."\n"; }
				// increase debth items
				$this->bs_cur_debth++;
				$this_level_debth++;
				if ($this->bs_cur_debth > $this->bs_max_debth)
				{
					$this->bs_max_debth = $this->bs_cur_debth;
				}
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $this->bs_cur_debth ['.$this->bs_cur_debth.'], $this->bs_max_debth ['.$this->bs_max_debth.'], $this_level_debth ['.$this_level_debth.']'."\n"; }
				// eat this paren and move on 
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): eat this paren and move on'."\n"; }
				// substr(str, 1) will remove only the first char (at pos [0])
				$this->bs_rawstr = substr($this->bs_rawstr, 1);
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
			}
			else
			{
				echo "\n".'bs('.__LINE__.'): *** FREAK OUT we wanted an open paren, did not get it'."\n";
			}
			
			// FILL_ITEM: STRUCTURE
			if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): *** MAKE OBJECT no matter what, we are going to need a new part and fill it'."\n"; }
			if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): create new msg_structure object, attach to $ref_parent->parts[]'."\n"; }
			// parent-?parts is an array zero based, so the next item number is the count of that array
			$my_partnum = count($ref_parent->parts);
			$ref_parent->parts[$my_partnum] = '';
			$ref_parent->parts[$my_partnum] = new msg_structure;
			// we do not use the "custom" item for imap
			unset($ref_parent->parts[$my_partnum]->custom);
			if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $my_partnum is ['.$my_partnum.'], $ref_parent->parts['.$my_partnum.'] is this level part'."\n"; }
			//if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): status: current $ref_parent DUMP'."\n"; print_r($ref_parent); echo "\n"; }
			
			// continue on...
			if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): ... continue, either collect simple type, or find a kick-up open paren'."\n\n"; }
			
			// tells us what data to collect later
			$was_kicked_up = False;
			// the next char better be an open paren (for a kick-up) or type data
			if ($this->bs_rawstr{0} == '(')
			{
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): found kick-up open paren, this implies type MULTIPART'."\n"; }
				$tmp_data['type_str'] = 'MULTIPART';
				$tmp_data['type_int'] = $this->type_str_to_int($tmp_data['type_str']);
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $tmp_data[type_str] is ['.$tmp_data['type_str'].'] $tmp_data[type_int] is ['.$tmp_data['type_int'].']'."\n"; }
				
				// FILL_ITEM
				$ref_parent->parts[$my_partnum]->type = $tmp_data['type_int'];
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->type is ['.serialize($ref_parent->parts[$my_partnum]->type).']'."\n"; }
				
				// NOW RECURSE, when the recurse upper level dies and returns, we continue on
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): pre-recurse status: $this->bs_cur_debth ['.$this->bs_cur_debth.'], $this->bs_max_debth ['.$this->bs_max_debth.'], $this_level_debth ['.$this_level_debth.']'."\n"; }
				
				//echo "\n".'bs('.__LINE__.'): *** FREAK OUT we NEED TO recurse here, not coded yet!!!'."\n";
				if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): *** CALLING FOR KICK-UP RECURSE NOW!!!!!, $ref_parent->parts[$my_partnum] is parent'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum] will be passed as parent, this will deepen the debth level'."\n"; }
				// a. eat no parens, make recurse call
				$this->make_msg_struct('kick-up recurse line('.__LINE__.')', $ref_parent->parts[$my_partnum]);
				
				// b. when we return, collect data typical of multipart data
				if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): *** RETURNING FROM KICK-UP RECURSE CALL!!!!!'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): post-recurse status: $this->bs_cur_debth ['.$this->bs_cur_debth.'], $this->bs_max_debth ['.$this->bs_max_debth.'], $this_level_debth ['.$this_level_debth.']'."\n"; }
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): post-recurse $this->bs_rawstr is: ['.$this->bs_rawstr."]\n"; }
				if ($this->bs_rawstr{0} == ' ')
				{
					if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): now eat the leading space of $this->bs_rawstr'."\n"; }
					$this->bs_rawstr = substr($this->bs_rawstr, 1);
				}
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): post-eat-space $this->bs_rawstr is: ['.$this->bs_rawstr."]\n"; }
				// after recurse upper level is done, we fallback to here and continue
				$was_kicked_up = True;
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): setting $was_kicked_up to ['.serialize($was_kicked_up)."]\n"; }
				// eat this paren and move on ????
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): ...continue on to collect data typical of multipart data'."\n\n"; }
			}
			elseif ($this->bs_rawstr{0} == '"')
			{
				// not multipart - STANDARD, BASIC LEVEL INFO, starts with TYPE
				$was_kicked_up = False;
				// ** TYPE **  main string is now
				// "TEXT" "PLAIN"   .....
				// cause we just ate any open parens above
				//$start = strpos($this->bs_rawstr, '"');
				$start = 1;
				
				// we know " " is next, so ...
				$end = strpos($this->bs_rawstr, '" "' );
				$slen = ($end-0) - $start;
				$tmp_data['type_str'] = substr($this->bs_rawstr, $start, $slen);
				if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[type_str] is: ['.$tmp_data['type_str'] ."]\n"; }
				$tmp_data['type_int'] = $this->type_str_to_int($tmp_data['type_str']);
				if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[type_int] is: ['.$tmp_data['type_int']."]\n"; }
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $tmp_data[type_str] is ['.$tmp_data['type_str'].'] $tmp_data[type_int] is ['.$tmp_data['type_int'].']'."\n"; }
				
				// FILL ITEM
				$ref_parent->parts[$my_partnum]->type = $tmp_data['type_int'];
				if ($this->debug_dcom > 3) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->type is ['.serialize($ref_parent->parts[$my_partnum]->type).']'."\n"; }
				
				//// type2 DELETE MAIN STRING OF DONE DATA
				// str_replace WAS GREEDY, use another way
				$this->bs_rawstr = substr($this->bs_rawstr, ($end+2));
				if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
			
			}
			else
			{
				echo "\n".'bs('.__LINE__.'): *** FREAK OUT unhandled situation, not open paren, not open quote'."\n\n\n";
			}
			
			// CONTINUE COMMECTING DATA
			// some is common to simple and multipart, some is just simple
				
			if (($was_kicked_up == True)
			|| ($was_kicked_up == False))
			{
				// ** SUBTYPE **  main string is now
				// "PLAIN" ("CHARSET" "utf-8") NIL NI
				// (this is common to both)
				//$start = strpos($this->bs_rawstr, '"');
				$start = 0;
				$start = $start + 1;
				$end = strpos($this->bs_rawstr, '" ' );
				$slen = ($end-0) - $start;
				$tmp_data['subtype_str'] = substr($this->bs_rawstr, $start, $slen);
				if (trim($tmp_data['subtype_str']) == '')
				{
					if ($this->debug_dcom > 1) { echo 'bs: no subtype, SOULD WE put a default here?'."\n"; }
					$tmp_data['ifsubtype'] = False;
					$tmp_data['subtype'] = '';
				}
				else
				{
					$tmp_data['ifsubtype'] = True;
					$tmp_data['subtype'] = $tmp_data['subtype_str'];
				}
				if ($this->debug_dcom > 1) { echo 'bs: $tmp_data[subtype_str] is: ['.$tmp_data['subtype_str'].'], $tmp_data[subtype] is: ['.$tmp_data['subtype']."]\n"; }
				if ($this->debug_dcom > 1) { echo 'bs: $tmp_data[ifsubtype] is: ['.serialize($tmp_data['ifsubtype'])."]\n"; }
				// FILL_ITEM
				$ref_parent->parts[$my_partnum]->ifsubtype = $tmp_data['ifsubtype'];
				if ($tmp_data['ifsubtype'] == True)
				{
					$ref_parent->parts[$my_partnum]->subtype = $tmp_data['subtype'];
				}
				else
				{
					// make obvious int 0 instead of bool
					$ref_parent->parts[$my_partnum]->ifsubtype = 0;
					unset($ref_parent->parts[$my_partnum]->subtype);
				}
				//// type2 DELETE MAIN STRING OF DONE DATA
				// str_replace WAS GREEDY, use another way
				$this->bs_rawstr = substr($this->bs_rawstr, ($end+2));
				if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				
				
				// ** PARAMETERS **  main string is now
				// ("CHARSET" "utf-8") NIL NIL "QUOT
				// OR multiple params look like this
				// ("TYPE" "multipart/alternative" "BOUNDARY" "----=_NextPart_000_00B9_01C3CE2B.BE78A8C0") NIL ....
				// OR some params have spaces in them
				// ("NAME" "GTMASTERsplash copy.pdf")
				// OR COULD it BE BLANK  ()  ???
				// OR could be nested params  ???
				// OR could it be NIL ???
				// (this is common to both)
				if (($this->bs_rawstr{0} == '(')
				&& ($this->bs_rawstr{1} == ')'))
				{
					$tmp_data['parameters_whatup'] = 'blank_parens';
					$end = 2;
				}
				elseif (($this->bs_rawstr{0} == 'N')
				&& ($this->bs_rawstr{1} == 'I')
				&& ($this->bs_rawstr{2} == 'L'))
				{
					$tmp_data['parameters_whatup'] = 'nil';
					$end = 3;
				}
				else
				{
					$tmp_data['parameters_whatup'] = 'ok';
				}
				// so what do we do now...
				if ($tmp_data['parameters_whatup'] != 'ok')
				{
					if ($this->debug_dcom > 1) { echo 'bs: no parameters'."\n"; }
					$tmp_data['ifparameters'] = False;
					$tmp_data['parameters'] = '';
					//// type2 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+1));
					if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				}
				else
				{
					$start = 0;
					$start = $start + 1;
					// this is safe in both cases of contimue with simple or skip below to multipart
					$end = strpos($this->bs_rawstr, ') ' );
					$slen = ($end-0) - $start;
					$tmp_data['parameters_str'] = substr($this->bs_rawstr, $start, $slen);
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): $tmp_data[parameters_str] is: ['.$tmp_data['parameters_str'] ."]\n"; }
					// this better be true if we are here!
					$tmp_data['ifparameters'] = True;
					// call sub-function to make the params for us
					if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): calling $this->make_param($tmp_data[parameters_str]) '."\n"; }
					$tmp_data['parameters'] = array();
					$tmp_data['parameters'] = $this->make_param($tmp_data['parameters_str']);
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): post-make_param $tmp_data[parameters] DUMP: '.""; print_r($tmp_data['parameters']); echo ""; }
					
					/*
					// what we have now looks somethign like one of these
					// "CHARSET" "utf-8"
					// "TYPE" "multipart/alternative" "BOUNDARY" "----=_NextPart_000_00B9_01C3CE2B.BE78A8C0"
					$tmp_data['ifparameters'] = True;
					$tmp_data['params_exploded'] = array();
					$tmp_data['parameters'] = array();				
					$tmp_data['params_exploded'] = explode(' ', $tmp_data['parameters_str']);
					
					// loop to clean of leading and trailing quotes
					$loops = count($tmp_data['params_exploded']);
					for ($i=0; $i < $loops ;$i++)
					{
						$this_str = $tmp_data['params_exploded'][$i];
						if ($this_str{0} == '"')
						{
							$this_str = substr($this_str, 1);
						}
						$last_pos = (strlen($this_str) - 1);
						if ($this_str{$last_pos} == '"')
						{
							$this_str = substr($this_str, 0, $last_pos);
						}
						$tmp_data['params_exploded'][$i] = $this_str;
					}
					//echo 'bs('.__LINE__.'): post-cleaning $tmp_data[params_exploded] is: ['.serialize($tmp_data['params_exploded'])."]\n";
					echo 'bs('.__LINE__.'): post-cleaning $tmp_data[params_exploded] DUMP: '.""; print_r($tmp_data['params_exploded']); echo "";
					// loop to make param objects
					$loops = count($tmp_data['params_exploded']);
					for ($i=0; $i < $loops ;$i=($i+2))
					{
						$attribute = 'UNKNOWN_PARAM_ATTRIBUTE';
						if ((isset($tmp_data['params_exploded'][$i]))
						&& (trim($tmp_data['params_exploded'][$i]) != ''))
						{
							$attribute = $tmp_data['params_exploded'][$i];
						}
						$value = 'UNKNOWN_PARAM_VALUE';
						$val_pos = $i+1;
						if ((isset($tmp_data['params_exploded'][$val_pos]))
						&& (trim($tmp_data['params_exploded'][$val_pos]) != ''))
						{
							$value = $tmp_data['params_exploded'][$val_pos];
						}
						// make this param pair object
						$tmp_data['parameters'][] = new msg_params($attribute,$value);
					}
					echo 'bs: post-looping $tmp_data[parameters] DUMP: '.""; print_r($tmp_data['parameters']); echo "";
					//echo 'bs: $tmp_data[parameters] is: ['.serialize($tmp_data['parameters'])."]\n";
					*/
					
					
					//// type2 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+2));
					if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				}
				// ok we have finished handling param data, so...
				// FILL_ITEM
				$ref_parent->parts[$my_partnum]->ifparameters = $tmp_data['ifparameters'];
				if (($tmp_data['ifparameters'])
				&& (count($tmp_data['parameters']) > 0))
				{
					$ref_parent->parts[$my_partnum]->parameters = $tmp_data['parameters'];
				}
				else
				{
					// make obvious int 0 instead of bool
					$ref_parent->parts[$my_partnum]->ifparameters = 0;
					unset($ref_parent->parts[$my_partnum]->parameters);
				}
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->ifparameters is ['.serialize($ref_parent->parts[$my_partnum]->ifparameters).']'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->parameters is ['.serialize($ref_parent->parts[$my_partnum]->parameters).']'."\n"; }
				// done parameters
			}	
			
			// this begins a bloc of data ONLY present for simple, not multipart, data
			if ($was_kicked_up == False)
			{
				// ** ID **  main string is now
				// NIL NIL "QUOTED-PRINTABLE" 48
				//  --OR--
				// "<image003.jpg@01C2454F.51BF0000>" NIL "BASE64" 1092
				$start = 0;
				$end = strpos($this->bs_rawstr, ' ' );
				$slen = ($end-0) - $start;
				$tmp_data['id_str'] = substr($this->bs_rawstr, $start, $slen);
				if (trim($tmp_data['id_str']) == 'NIL')
				{
					$tmp_data['ifid'] = False;
					$tmp_data['id'] = '';
				}
				else
				{
					$tmp_data['ifid'] = True;
					$tmp_data['id'] = $tmp_data['id_str'];
					if ($this->debug_dcom > 1) { echo 'bs: strip open and close quotes from $tmp_data[id_str], ['.$tmp_data['id_str'] ."]\n"; }
					if ($tmp_data['id']{0} == '"')
					{
						$tmp_data['id'] = substr($tmp_data['id'], 1);
					}
					$last_pos = (strlen($tmp_data['id']) - 1);
					if ($tmp_data['id']{$last_pos} == '"')
					{
						$tmp_data['id'] = substr($tmp_data['id'], 0, $last_pos);
					}
					if ($this->debug_dcom > 1) { echo 'bs: prepped quote-stripped $tmp_data[id], ['.$tmp_data['id'] ."]\n"; }
				}
				if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[id_str] is: ['.$tmp_data['id_str'] ."]\n"; }
				if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[ifid] is: ['.serialize($tmp_data['ifid'])."]\n"; }
				if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[id] is: ['.$tmp_data['id'] ."]\n"; }
				// FILL_ITEM
				$ref_parent->parts[$my_partnum]->ifid = $tmp_data['ifid'];
				if (($tmp_data['ifid'])
				&& (trim($tmp_data['id']) != ''))
				{
					$ref_parent->parts[$my_partnum]->id = $tmp_data['id'];
				}
				else
				{
					// make obvious int 0 instead of bool
					$ref_parent->parts[$my_partnum]->ifid = 0;
					unset($ref_parent->parts[$my_partnum]->id);
				}
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->ifid is ['.serialize($ref_parent->parts[$my_partnum]->ifid).']'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->id is ['.serialize($ref_parent->parts[$my_partnum]->id).']'."\n"; }
				//// type2 DELETE MAIN STRING OF DONE DATA
				// str_replace WAS GREEDY, use another way
				$this->bs_rawstr = substr($this->bs_rawstr, ($end+1));
				if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				
				
				// ** DESCRIPTION **  main string is now
				// NIL "QUOTED-PRINTABLE" 48
				// OR an actual description looks like this
				// "This is a digitally signed message part" "7BIT" 196 ...
				if (($this->bs_rawstr{0} == 'N')
				&& ($this->bs_rawstr{1} == 'I')
				&& ($this->bs_rawstr{2} == 'L'))
				{			
					$start = 0;
					$end = strpos($this->bs_rawstr, ' ' );
					$slen = ($end-0) - $start;
					$tmp_data['description_str'] = substr($this->bs_rawstr, $start, $slen);
					$tmp_data['ifdescription'] = False;
					$tmp_data['description'] = '';
					//// type2 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+1));
					// show this later for better clarity of debugging
					//if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				}
				elseif ($this->bs_rawstr{0} == '"')
				{
					// there BETTER be a quote as first char, so start = 1
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): $this->bs_rawstr{0} is ['.htmlspecialchars($this->bs_rawstr{0}).'] so description_str standard quoted data '."\n"; }
					$start = 1;
					$end = strpos($this->bs_rawstr, '" ' );
					$slen = ($end-0) - $start;
					$tmp_data['description_str'] = substr($this->bs_rawstr, $start, $slen);
					$tmp_data['ifdescription'] = True;
					$tmp_data['description'] = $tmp_data['description_str'];
					//// type2 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+2));
					// show this later for better clarity of debugging
					//echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n";
				}
				elseif ($this->bs_rawstr{0} == '{')
				{
					// there BETTER be a { as first char
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): $this->bs_rawstr{0} is ['.htmlspecialchars($this->bs_rawstr{0}).'] so description_str STRING LITERAL data '."\n"; }
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): RFC3501 Sec 4.2 String Literal is rare in BODYSTRUCTURE, only loose typed strings like description or maybe some params'."\n"; }
					// handle literl SEQUENCE
					$start = 1;
					$end = strpos($this->bs_rawstr, '}'."\r\n");
					$slen = $end-$start;
					$literal_len = substr($this->bs_rawstr, 1, $slen);
					$literal_len = (int)trim($literal_len);
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): $literal_len is ['.serialize($literal_len).'] '."\n"; }
					// chop up to start of literal data
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+3));
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): chopped interim NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n"; }
					// grab exact literal data
					// find the first space at or after $literal_len
					// multibyte strings throw the function substr off 
					// we use OFFSET to start searching at $literal_len
					$best_space_pos = strpos($this->bs_rawstr, ' ', $literal_len);
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): $best_space_pos is ['.serialize($best_space_pos).'] '."\n"; }
					if ($best_space_pos > $literal_len)
					{
						$end = $best_space_pos;
					}
					else
					{
						$end = $literal_len;
					}
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): decide to use $end is ['.serialize($end).'] '."\n"; }
					$tmp_data['description_str'] = substr($this->bs_rawstr, 0, $end);
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): $tmp_data[description_str] is ['.$tmp_data['description_str'].'] '."\n"; }
					$tmp_data['ifdescription'] = True;
					$tmp_data['description'] = $tmp_data['description_str'];
					//// type2 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+1));
					// show this later for better clarity of debugging
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				}
				
				if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[description_str] is: ['.$tmp_data['description_str'] ."]\n"; }
				if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[ifdescription] is: ['.serialize($tmp_data['ifdescription'])."]\n"; }
				if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[description] is: ['.$tmp_data['description'] ."]\n"; }
				// FILL_ITEM
				$ref_parent->parts[$my_partnum]->ifdescription = $tmp_data['ifdescription'];
				if (($tmp_data['ifdescription'])
				&& (trim($tmp_data['description']) != ''))
				{
					$ref_parent->parts[$my_partnum]->description = $tmp_data['description'];
				}
				else
				{
					// make obvious int 0 instead of bool
					$ref_parent->parts[$my_partnum]->ifdescription = 0;
					unset($ref_parent->parts[$my_partnum]->description);
				}
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->ifdescription is ['.serialize($ref_parent->parts[$my_partnum]->ifdescription).']'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->description is ['.serialize($ref_parent->parts[$my_partnum]->description).']'."\n"; }
				if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				
				// ** ENCODING **  main string is now
				// "QUOTED-PRINTABLE" 484 20 NIL (
				//$start = strpos($this->bs_rawstr, '"');
				$start = 1;
				$end = strpos($this->bs_rawstr, '" ' );
				$slen = ($end-0) - $start;
				$tmp_data['encoding_str'] = substr($this->bs_rawstr, $start, $slen);
				if ($this->debug_dcom > 1) { echo 'bs: $tmp_data[encoding_str] is: ['.$tmp_data['encoding_str']."]\n"; }
				$tmp_data['encoding_int'] = $this->encoding_str_to_int($tmp_data['encoding_str']);
				if ($this->debug_dcom > 1) { echo 'bs: $tmp_data[encoding_int] is: ['.$tmp_data['encoding_int']."]\n"; }
				// FILL_ITEM
				$ref_parent->parts[$my_partnum]->encoding = $tmp_data['encoding_int'];
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->encoding is ['.serialize($ref_parent->parts[$my_partnum]->encoding).']'."\n"; }
				//// type2 DELETE MAIN STRING OF DONE DATA
				// str_replace WAS GREEDY, use another way
				$this->bs_rawstr = substr($this->bs_rawstr, ($end+2));
				if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				
				// ** BYTES **  main string is now
				// 484 20 NIL (
				$start = 0;
				$end = strpos($this->bs_rawstr, ' ');
				$slen = ($end-0) - $start;
				$tmp_data['bytes'] = substr($this->bs_rawstr, $start, $slen);
				$tmp_data['bytes'] = (int)$tmp_data['bytes'];
				if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[bytes] is: ['.$tmp_data['bytes']."]\n"; }
				// FILL_ITEM
				$ref_parent->parts[$my_partnum]->bytes = $tmp_data['bytes'];
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->bytes is ['.serialize($ref_parent->parts[$my_partnum]->bytes).']'."\n"; }
				//// type2 DELETE MAIN STRING OF DONE DATA
				// str_replace WAS GREEDY, use another way
				$this->bs_rawstr = substr($this->bs_rawstr, ($end+1));
				if ($this->debug_dcom > 3) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }


				// *** END BASIC DATA ***
				// now we might find:
				// a. ENCAPSULATION *** if message/rfc822
				// 			that is 1. (envelope) 2. (bodystructure) 3. THEN (b) LINES
				// b. ** LINES **  if type is text and it is a number
				// c. Extension Data, in this order:
				// 		MD5, disposition, language, location
				
				// so what do we have ...
				// message/rfc822 encap ?
				if ($this->bs_rawstr{0} == '(')
				{
					// ** ENCAPSULATION *** if message/rfc822
					if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): found open paren, so we have *** ENCAPSULATED message/rfc822'."\n"; }
					// 1. (envelope)
					//counting parens can be b0rked by parens in the strings of env data, so
					// try looking for the few known ways this encap env will end
					// either [>") (] OR  [ NIL) (]  where that open paren starts again the bodystruct data
					$end_1 = strpos($this->bs_rawstr, '>") (');
					$end_2 = strpos($this->bs_rawstr, ' NIL) (');
					// pick the lowest above zero
					if (($end_1 > 0)
					|| ($end_2 > 0))
					{
						if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): ENCAP 1. preferred method, is look for ending strings of (envelope) to get its length - eat it, and move on... '."\n"; }
						$tmp_data['encap_env_end'] = 0;
						$num_array = array();
						if ((int)$end_1 > 0)
						{
							$num_array[] = (int)$end_1;
						}
						if ((int)$end_2 > 0)
						{
							$num_array[] = (int)$end_2;
						}
						$tmp_data['encap_env_end'] = min($num_array);
						// find the first space just after the beginning of known end strings we just found
						$tmp_data['encap_env_firstspace'] = strpos($this->bs_rawstr, ' ', $tmp_data['encap_env_end']+1);
						// eat the envelope
						// then encap_env_firstspace+1 eats the space itself I hope
						if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): eat the envelope..., eliminate up to $tmp_data[encap_env_firstspace]+1 is ['.$tmp_data['encap_env_firstspace'].']+1'."\n"; }
						$this->bs_rawstr = substr($this->bs_rawstr, $tmp_data['encap_env_firstspace']+1);
						if ($this->debug_dcom > 2) { echo 'bs: post-envelope-eat NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
					}
					else
					{
						// much less accurate fallback method
						if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): ENCAP 1b. backup method, is count parens in (envelope) to get its length - eat it, and move on... '."\n"; }
						$loopz = strlen($this->bs_rawstr);
						$tmp_data['encap_paren_count'] = 0;
						$tmp_data['encap_env_end'] = 0;
						for ($x=0; $x < $loopz ; $x++)
						{
							// when paren count gets back to 0, we get to the end of enbeded envelope
							// this ends with either [>") (]  OR  [ NIL) (]
							if ($this->bs_rawstr{$x} == '(')
							{
								$tmp_data['encap_paren_count']++;
								if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): ... ENCAP 1. (envelope-eating-loop) found open paren, $tmp_data[encap_paren_count] now ['.$tmp_data['encap_paren_count'].']'."\n"; }
							}
							elseif ($this->bs_rawstr{$x} == ')')
							{
								$tmp_data['encap_paren_count']--;
								if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): ... ENCAP 1. (envelope-eating-loop) found close paren, $tmp_data[encap_paren_count] now ['.$tmp_data['encap_paren_count'].']'."\n"; }
							}
							// are we back to zero?
							
							// do we continue to loop
							if ($tmp_data['encap_paren_count'] == 0)
							{
								// BREAK
								$tmp_data['encap_env_end'] = $x;
								if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): break from this loop - got end of encap envelope at $tmp_data[encap_env_end] is ['.$tmp_data['encap_env_end'].']'."\n"; }
								break;
							}
						}
						// eat the envelope
						if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): eat the envelope..., eliminate up to $tmp_data[encap_env_end]+2 ['.$tmp_data['encap_env_end'].']+2'."\n"; }
						// encap_env_end +2 eats the env close paren and the space that follows it
						$this->bs_rawstr = substr($this->bs_rawstr, $tmp_data['encap_env_end']+2);
						if ($this->debug_dcom > 2) { echo 'bs: post-envelope-eat NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
					}
					
					// 2. (bodystructure)
					if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): ... now should be at ENCAP 2. bodystructure, look for open paren '."\n"; }
					if ($this->bs_rawstr{0} != '(')
					{
						echo "\n".'bs('.__LINE__.'): *** FREAK OUT we wanted open paren of, did not get it'."\n";
					}
					else
					{
						if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): *** CALLING FOR ENCAP KICK-UP RECURSE NOW!!!!!, $ref_parent->parts[$my_partnum] is parent'."\n"; }
						if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum] will be passed as parent, this will deepen the debth level'."\n"; }
						// a. eat NO parens, make recurse call
						$this->make_msg_struct('encap kick-up recurse line('.__LINE__.')', $ref_parent->parts[$my_partnum]);
					}
	
					// 3. return and contuinue on...
					// b. when we return, collect data typical of multipart data
					if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): *** RETURNING FROM ENCAP KICK-UP RECURSE CALL!!!!!'."\n"; }
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): post-recurse status: $this->bs_cur_debth ['.$this->bs_cur_debth.'], $this->bs_max_debth ['.$this->bs_max_debth.'], $this_level_debth ['.$this_level_debth.']'."\n"; }
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): post-recurse $this->bs_rawstr is: ['.$this->bs_rawstr."]\n"; }
					if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): now returned, continue collect data as if nothing happened, i.e. as for single part struct'."\n"; }
					if ($this->bs_rawstr{0} == ' ')
					{
						if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): left-trim $this->bs_rawstr, we do not want space at the first position'."\n"; }
						$this->bs_rawstr = ltrim($this->bs_rawstr);
						if ($this->debug_dcom > 2) { echo 'bs: status: post-ltrim NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
					}
				}
				
				// b. ** LINES **  if type is text and it is a number and on return from encap, or
				// c. ** MD5 if not a number (usually NIL but may be number in pos1 anyway?)
				// main string is now
				// 20 		NIL (
				// OR 		NIL ("INLINE
				// do we have a number? (btw, is 0 a good lines value anyway?)
				$tmp_data['lines_test'] = $this->bs_rawstr{0};
				if ((
					((string)$tmp_data['lines_test'] == '0')
					)
				|| (((int)$tmp_data['lines_test'] >= 1)
					&& ((int)$tmp_data['lines_test'] <= 9)
					)
				)
				{
					if ($this->debug_dcom > 1) { echo 'bs: we DO have LINES value YES'."\n"; }
					$start = 0;
					$end = strpos($this->bs_rawstr, ' ');
					$slen = ($end-0) - $start;
					$tmp_data['lines'] = substr($this->bs_rawstr, $start, $slen);
					$tmp_data['lines'] = (int)$tmp_data['lines'];
					if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[lines] is: ['.$tmp_data['lines']."]\n"; }
					// FILL_ITEM
					$ref_parent->parts[$my_partnum]->lines = $tmp_data['lines'];
					if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->lines is ['.serialize($ref_parent->parts[$my_partnum]->lines).']'."\n"; }
					//// type2 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+1));
					if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				}
				else
				{
					if ($this->debug_dcom > 1) { echo 'bs: we have NO LINES value Skipping LINES'."\n"; }
					$tmp_data['lines'] = '';
					// FILL_ITEM
					unset($ref_parent->parts[$my_partnum]->lines);
					if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $ref_parent->parts[$my_partnum]->lines is ['.serialize($ref_parent->parts[$my_partnum]->lines).']'."\n"; }
				}
				
				if ($this->debug_dcom > 2) { echo "\n".'bs('.__LINE__.'): note this is the end of rfc3501 Basic Data'."\n"; }
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): what follows is rfc3501 Extended Data: any of these in this order: '."\n"; }
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): a. MD5, b. disposition, c. language, d. location ; and stopping at any one of them'."\n\n"; }
				
				// ** MD5 **  main string is now
				// NIL ("INLINE
				// right now php-imap does not handle this item, it is almost always NIL
				$start = 0;
				$end = strpos($this->bs_rawstr, ' ');
				$slen = ($end-0) - $start;
				$tmp_data['md5'] = substr($this->bs_rawstr, $start, $slen);
				if ($this->debug_dcom > 1) { echo 'bs: This is probably MD5 but we do not handle it now, usualy NIL, so eat it and move on'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs: $tmp_data[md5] is: ['.$tmp_data['md5']."]\n"; }
				// FILL_ITEM
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): php doe NOT handle MD5 right now, nothing to set or unset, skipping...'."]\n"; }
				//// type2 DELETE MAIN STRING OF DONE DATA
				// str_replace WAS GREEDY, use another way
				$this->bs_rawstr = substr($this->bs_rawstr, ($end+1));
				if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
			}
			else
			{
				// UNSET AND/OR NORMALIZE items that get skipped for kicked-up parts
				// instead of FALSE, make obvious int 0 instead of bool
				// ID
				$ref_parent->parts[$my_partnum]->ifid = 0;
				unset($ref_parent->parts[$my_partnum]->id);
				// DESCRIPTION
				$ref_parent->parts[$my_partnum]->ifdescription = 0;
				unset($ref_parent->parts[$my_partnum]->description);
				// ENCODING
				// PROBLEM: mutipart does not really provide encoding explicitly
				// it *may* give info about it in dparams or params maybe
				// BUT example multipart parent items seem to always have 0 for encoding
				// encoding 0 = "7bit", perhaps RFC require this outer element to be 7bit???
				$ref_parent->parts[$my_partnum]->encoding = ENC7BIT;
				// BYTES 
				unset($ref_parent->parts[$my_partnum]->bytes);
				// LINES
				unset($ref_parent->parts[$my_partnum]->lines);
				// MD5
				// php doe NOT handle MD5 right now, nothing to set or unset here
			}
			
			// this data exists for both simple and multipart data
			if (($was_kicked_up == True)
			|| ($was_kicked_up == False))
			{
				
				// ** DISPOSITION **  main string is now
				// 			("INLINE" NIL) NIL)
				// or		("ATTACHMENT" ("FILENAME" "hook_sidebox_menu.inc.php")) NIL)
				// or		NIL NIL)("APPLICATION" "OCTE
				if (($this->bs_rawstr{0} == 'N')
				&& ($this->bs_rawstr{1} == 'I')
				&& ($this->bs_rawstr{2} == 'L'))
				{
					if ($this->debug_dcom > 1) { echo 'bs: we have NO disposition, and thus we have no dparameters'."\n"; }
					$tmp_data['ifdisposition'] = 0;
					$tmp_data['ifdparameters'] = False;
					if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[ifdisposition] is: ['.$tmp_data['ifdisposition']."]\n"; }
					if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[ifdparameters] is: ['.$tmp_data['ifdparameters']."]\n"; }
					// FILL_ITEM
					// make obvious int 0 instead of bool
					$ref_parent->parts[$my_partnum]->ifdisposition = 0;
					unset($ref_parent->parts[$my_partnum]->disposition);
					// make obvious int 0 instead of bool
					$ref_parent->parts[$my_partnum]->ifdparameters = 0;
					unset($ref_parent->parts[$my_partnum]->dparameters);
					//// type3 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					$this->bs_rawstr = substr($this->bs_rawstr, 4);
					if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				}
				else
				{
					if ($this->debug_dcom > 1) { echo 'bs: we do have some kind of disposition data'."\n"; }
					$start = 0;
					$end = strpos($this->bs_rawstr, ') ');
					// end+1 so we incluse all parens including the first and last
					$slen = ($end+1) - $start;
					$tmp_data['disposition_total'] = substr($this->bs_rawstr, $start, $slen);
					//echo 'bs: $tmp_data[disposition_total] is: ['.$tmp_data['disposition_total']."]\n";
					
					//// type2 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					// DO THIS NOW - disposition_total has coumpound data, we use it alone for this whole section
					if ($this->debug_dcom > 2) { echo "\n".'bs: before we process disposition, clean $this->bs_rawstr of it '."\n"; }
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+2));
					if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
	
					//echo 'bs: ok ... continue handling disposition and  dparameters ...'."\n";
					
					// We now have a disposition_total that looks like one of these
					// 	("INLINE" NIL)
					//	("ATTACHMENT" ("FILENAME" "hook_sidebox_menu.inc.php"))
					//	("ATTACHMENT" ("FILENAME" "hook_sidebox_menu.inc.php" "another_value" "another_attrib"))
					//	("ATTACHMENT" ("FILENAME" "GTMASTERsplash copy.pdf"))
					// a. the first "string" is the disposition
					// b. then we see NIL or (list) as dparams
					// this is hypothetical, not seen but is this possible (NIL ("value" "attrib"))
					
					// do we have disposition or not
					if (($tmp_data['disposition_total']{0} == '(')
					&& ($tmp_data['disposition_total']{1} == '"'))
					{
						// grab this item
						if ($this->debug_dcom > 1) { echo 'bs: we confirmed to have disposition data'."\n"; }
						$start = 2;
						$end = strpos($tmp_data['disposition_total'], '" ');
						$slen = ($end+0) - $start;
						$tmp_data['ifdisposition'] = True;
						$tmp_data['disposition'] = substr($tmp_data['disposition_total'], $start, $slen);
						//echo 'bs: post-grab $tmp_data[disposition] is: ['.$tmp_data['disposition']."]\n";
						// FILL_ITEM
						$ref_parent->parts[$my_partnum]->ifdisposition = $tmp_data['ifdisposition'];
						$ref_parent->parts[$my_partnum]->disposition = $tmp_data['disposition'];
						
						// remove the  disposition_str from the disposition_total
						$tmp_data['disposition_total'] = substr($tmp_data['disposition_total'], ($end+2));
						if ($this->debug_dcom > 2) { echo 'bs: post-chop $tmp_data[disposition_total] is: ['.$tmp_data['disposition_total']."]\n"; }
						
						// *** disposition params ***
						// this is what $tmp_data[disposition_total] might be right now
						// 	NIL)
						//	("FILENAME" "hook_sidebox_menu.inc.php"))
						//	("FILENAME" "hook_sidebox_menu.inc.php" "another_value" "another_attrib"))
						//	("NAME" "GTMASTERsplash copy.pdf"))
						if (($tmp_data['disposition_total']{0} == 'N')
						&& ($tmp_data['disposition_total']{1} == 'I')
						&& ($tmp_data['disposition_total']{2} == 'L'))
						{
							//we are DONE there are NO dparams
							if ($this->debug_dcom > 1) { echo 'bs: we have NO dparameters'."]\n"; }
							$tmp_data['ifdparameters'] = 0;
							$tmp_data['dparameters'] = '';
							// FILL_ITEM
							$ref_parent->parts[$my_partnum]->ifdparameters = $tmp_data['ifdparameters'];
							unset($ref_parent->parts[$my_partnum]->dparameters);
						}
						else
						{
							// we have dparams
							if ($this->debug_dcom > 1) { echo 'bs: we have dparameters, first strip open and close parens'."]\n"; }
							// strip leading and trailing parans
							if ($tmp_data['disposition_total']{0} == '(')
							{
								$tmp_data['disposition_total'] = substr($tmp_data['disposition_total'], 1);
							}
							// there SHOULD be double closing parens (1 for whole disposition, 1 for just the dparams)
							$end = strpos($tmp_data['disposition_total'], '))');
							if ($end != 0)
							{
								$tmp_data['disposition_total'] = substr($tmp_data['disposition_total'], 0, $end);
							}
							if ($this->debug_dcom > 2) { echo 'bs: post-strip $tmp_data[disposition_total] is: ['.$tmp_data['disposition_total']."]\n"; }
							
							// call sub-function to make the params for us
							if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): calling $this->make_param($tmp_data[disposition_total]) '."\n"; }
							$tmp_data['ifdparameters'] = True;
							$tmp_data['dparameters'] = array();
							$tmp_data['dparameters'] = $this->make_param($tmp_data['disposition_total']);
							if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): post-make_param $tmp_data[dparameters] DUMP: '.""; print_r($tmp_data['dparameters']); echo ""; }
	
							
							// FILL_ITEM (HACK HACK)
							//echo 'bs: now seting ifdparameters and dparameters '."\n";
							$ref_parent->parts[$my_partnum]->ifdparameters = $tmp_data['ifdparameters'] ;
							$ref_parent->parts[$my_partnum]->dparameters = $tmp_data['dparameters'];
						}
						//echo 'bs:final $tmp_data[disposition_total] is: ['.$tmp_data['disposition_total']."]\n";
						if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): note: by this point we should have totally handled disposition and dparams '."\n\n"; }
					}
					else
					{
						echo "\n".'bs('.__LINE__.'): *** FREAK OUT we wanted disposition data, did not get it'."\n";
					}
					
					/*
					//echo 'bs: FIXME we need to code for disposition, right now make False, eat it, and move on'."\n";
					echo 'bs: FIXME we need to code for disposition, right fake it, eat it, and move on'."\n";
					$tmp_data['ifdisposition'] = True;
					echo 'bs: $tmp_data[ifdisposition] is: ['.$tmp_data['ifdisposition']."]\n";
					// FILL_ITEM (HACK HACK)
					$ref_parent->parts[$my_partnum]->ifdisposition = $tmp_data['ifdisposition'];
					$ref_parent->parts[$my_partnum]->disposition = $tmp_data['disposition_str'];
					echo 'bs: FIXME we need to code for dparameters!!!'."\n";
					echo 'bs: ... for now, set ifdparameters to false and unset dparameters '."\n";
					$ref_parent->parts[$my_partnum]->ifdparameters = 0;
					unset($ref_parent->parts[$my_partnum]->dparameters);
					//// type2 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+2));
					echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n";
					*/
				}
				
				// ** LANGUAGE **  main string is now
				// NIL)
				//  -- OR --
				// ("EN")
				//  -- OR --
				// "en") 
				// note: in each case above the last paren is the end of data paren
				// RFC3501 sect 7.4.2 says this is "A string or parenthesized list giving the body language value as defined in [LANGUAGE-TAGS]"
				if ($this->debug_dcom > 1) { echo 'bs: now looking LANGUAGE but we do not handle it now, but we still need to eat it exactly and move on, or maybe at end of str now'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs: it is quite likely the end paren of this part is next... '."\n"; }
				if (($this->bs_rawstr{0} == 'N')
				&& ($this->bs_rawstr{1} == 'I')
				&& ($this->bs_rawstr{2} == 'L'))
				{			
					if ($this->debug_dcom > 1) { echo 'bs: LANGUAGE is NIL, so eat it and move on, it is quite likely the end paren of this part is next'."\n"; }
					$start = 0;
					//$end = strpos($this->bs_rawstr, ')');
					// we know the length of "NIL"
					$end = 3;
					$slen = ($end+0) - $start;
					$tmp_data['language'] = substr($this->bs_rawstr, $start, $slen);
					if ($this->debug_dcom > 2) { echo 'bs: This is probably LANGUAGE but we do not handle it now, if not set it is NIL, so eat it and move on, or maybe at end of str now'."\n"; }
					if ($this->debug_dcom > 1) { echo 'bs: $tmp_data[language] is: ['.$tmp_data['language']."]\n"; }
					//// type2 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					// leave any closing parens
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+0));
					if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				}
				else
				{
					if ($this->debug_dcom > 1) { echo 'bs: we found LANGUAGE element ... grab it for future compat even though php-imap does not handle it'."\n"; }
					if ($this->debug_dcom > 2) { echo 'bs: rfc3501s7.3.2 says it can be a simple string OR it can be a paren list item, if it exitis '."\n"; }
					if ($this->debug_dcom > 2) { echo 'bs: I have seen both, mailer netscape webmail is one of the few that sets this header: Content-language: en'."\n"; }
					if ($this->debug_dcom > 2) { echo 'bs: server Cyrus will represent that as a paren list, server Courier-imap as a simple quoted string'."\n"; }
					if ($this->bs_rawstr{0} == '(')
					{
						if ($this->debug_dcom > 2) { echo 'bs: language exists and is in a paren list of some kind'."\n"; }
						$start = 0;
						$end = strpos($this->bs_rawstr, ')');
						$slen = ($end+1) - $start;
						$tmp_data['language'] = substr($this->bs_rawstr, $start, $slen);
						if ($this->debug_dcom > 2) { echo 'bs: $tmp_data[language] is: ['.$tmp_data['language']."]\n"; }
						if ($this->debug_dcom > 2) { echo 'bs: prep language by removing any open and close paren since it is supposed to be a list'."\n"; }
						if ($tmp_data['language']{0} == '(')
						{
							$tmp_data['language'] = substr($tmp_data['language'], 1);
						}
						$lang_end = strlen($tmp_data['language'])-1;
						if ($tmp_data['language']{$lang_end} == ')')
						{
							$tmp_data['language'] = substr($tmp_data['language'], 0, $lang_end);
						}
					}
					elseif ($this->bs_rawstr{0} == '"')
					{
						if ($this->debug_dcom > 2) { echo 'bs: language exists and is a quoted string'."\n"; }
						$start = 0;
						$end = strpos($this->bs_rawstr, '"', 1);
						$slen = ($end+1) - $start;
						$tmp_data['language'] = substr($this->bs_rawstr, $start, $slen);
					}
					if ($this->debug_dcom > 1) { echo 'bs: final $tmp_data[language] is: ['.$tmp_data['language']."]\n"; }
					//// type2 DELETE MAIN STRING OF DONE DATA
					// str_replace WAS GREEDY, use another way
					// leave any closing parens
					$this->bs_rawstr = substr($this->bs_rawstr, ($end+1));
					if ($this->debug_dcom > 2) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				}
				
			}
			
			// END OF ALL DATA WE HOPE
			if ($this->debug_dcom > 1) { echo "\n".'bs: status: now we expect the close paren of the outer part, that will close this function call, and we found ['.$this->bs_rawstr{0}.']'."\n\n"; }
			if ($this->bs_rawstr{0} != ')')
			{
				if ($this->debug_dcom > 1) { echo 'bs: oops, we have extension data we are not coded to handle, so we should eat it'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs: note that rfc3501s7.3.2 does define for here LOCATION as string list giving the body content URI as defined in LOCATION'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs: next char is NOT  ) so we have more extension data we are not coded to handle'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs: ATTEMPTING to eat this extra unhandlable data up to right before the final ) '."\n"; }
				$end = strpos($this->bs_rawstr, ')');
				$this->bs_rawstr = substr($this->bs_rawstr, $end);
				if ($this->debug_dcom > 1) { echo 'bs: NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
			}
			
			// *** parts need unseting? ***
			// parts only gets filled withTYPEMULTIPART 
			// and *some* type TYPEMESSAGE such as message/rfc822 encapsulation
			// otherwise we can UNSET ->parts[] because no other types can have child parts
			if (($ref_parent->parts[$my_partnum]->type != TYPEMULTIPART)
			&& ($ref_parent->parts[$my_partnum]->type != TYPEMESSAGE))
			{
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): this is NOT type TYPEMULTIPART and NOT type TYPEMESSAGE'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): so we now UNSET the ->parts element it is not used in this case'."\n"; }
				unset($ref_parent->parts[$my_partnum]->parts);
			}
			else
			{
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): leave ->parts element unmolested, this is TYPEMULTIPART or type TYPEMESSAGE'."\n"; }
			}
	
			// END DATA COLLECTION
			if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): DONE collecting data for this level'."\n"; }
			if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): status: $this->bs_cur_debth ['.$this->bs_cur_debth.'], $this->bs_max_debth ['.$this->bs_max_debth.'], $this_level_debth ['.$this_level_debth.']'."\n"; }
			if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): we either a. die and return or b. have a continuation of current level (non kick-up recurse)'."\n"; }
			if ($this->debug_dcom > 2) { echo 'bs: going into these checks, current $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
			
			// freak out check
			if ($this->bs_rawstr{0} != ')')
			{
				echo "\n".'bs('.__LINE__.'): *** FREAK OUT we wanted an close paren, did not get it'."\n";
				echo "\n".'bs('.__LINE__.'): FIXME what would we do if this happens?'."\n";
			}	
			
			
			// LEVEL ANALYSIS
			// do we DIE or...
			// continuation of level recurse occurrs if a consecutive level part is immediately next
			// freak out if we do not see what should be here
		
			// first, see if we anticipate a contiguous level
			//$contiguous_level_recall = False;
			if ((strlen($this->bs_rawstr) > 1)
			&& ($this->bs_rawstr{0} == ')')
			&& ($this->bs_rawstr{1} == '('))
			{
				// anticipating CONTINUATION recurse,
				if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): we anticipate a CONTINUATION recurse, but lets make sure ... '."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): RUN AGAIN )( calls for CONTINUATION recurse'."\n"; }
				
				
				//echo 'bs('.__LINE__.'): FIXME CODE FOR THIS RECUSRION'."\n\n\n";
				// a. decrease level counters
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): a. standard close paren, decrease debths'."\n"; }
				// decrease debth items
				$this->bs_cur_debth--;
				$this_level_debth--;
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): $this->bs_cur_debth ['.$this->bs_cur_debth.'], $this->bs_max_debth ['.$this->bs_max_debth.'], $this_level_debth ['.$this_level_debth.']'."\n"; }
				
				// b. eat this paren 
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): eat this close paren before we call for contiguous run'."\n"; }
				// substr(str, 1) will remove only the first char (at pos [0])
				$this->bs_rawstr = substr($this->bs_rawstr, 1);
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				
				// c. call us again
				if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): *** CALLING CONTINUATION NEXT PART CALL AGAIN RECURSE NOW!!!!!'."\n"; }
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): "$ref_parent" will be passed AGAIN as parent, this will keep us on the SAME debth level'."\n"; }
				// a. eat no parens, make recurse call
				$this->make_msg_struct('continuation nect part call again recurse line('.__LINE__.')', $ref_parent);
				
				// b. when we return, collect data typical of multipart data
				if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): *** RETURNING FROM CONTINUATION RECURSE CALL!!!!!'."\n"; }
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): post-recurse status: $this->bs_cur_debth ['.$this->bs_cur_debth.'], $this->bs_max_debth ['.$this->bs_max_debth.'], $this_level_debth ['.$this_level_debth.']'."\n"; }
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): post-recurse $this->bs_rawstr is: ['.$this->bs_rawstr."]\n"; }
	
				// d. we returned, DIE
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): when we return from this recursion, this call should die because ... '."\n"; }
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): ... a. because next call will continue any contiguous parts if necessary, or'."\n"; }
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): ... b. a previous call will continue to collect multipart data for its level'."\n"; }
				
				// return here?????
				// DIE
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): *** EXITING THIS CALL we processsed 1 part THEN called for next part recurse, now we have nothing left to do in our little universe'."\n"; }
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): alas, we must EXIT, returning $this->bs_cur_debth ['.$this->bs_cur_debth.']'."\n"; }
				if ($this->debug_dcom > 0) { echo 'bs('.__LINE__.'): LEAVING, returning $this->bs_cur_debth ['.$this->bs_cur_debth.']'.'</pre>'."\n\n\n"; }
				return $this->bs_cur_debth;
			}
			else
			{
				// ... more checks...
				if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): we have NO continuation part to worry about, this call should DIE now'."\n"; }
				
				// check for some DIE conditions
				if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): lets check for some DIE conditions DO WE NEED TO????'."\n"; }
				if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): status: currently $this->bs_rawstr is: ['.$this->bs_rawstr."]\n\n"; }
				// I'd say we die under these conditions
				
				// initialize this var
				$die_test_said = False;
				if ((strlen($this->bs_rawstr) < 2)
				&& ($this->bs_rawstr{0} == ')'))
				{
					$die_test_said = True;
					if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): DONE, string is 1 char and it is close paren, set $die_test_said to ['.serialize($die_test_said).']'."\n"; }
				}
				if ((strlen($this->bs_rawstr) > 2)
				&& ($this->bs_rawstr{0} == ')')
				&& ($this->bs_rawstr{1} == ' '))
				{
					$die_test_said = True;
					if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): DONE, 1st char is ) and next is space, just like end of kick-up looks like, DIE back to previous call, set $die_test_said to ['.serialize($die_test_said).']'."\n"; }
				}
				
				if ($die_test_said == True)
				{
					// DONE !!!!!
					if ($this->debug_dcom > 2) { echo "\n".'bs('.__LINE__.'): DONE, string is 1 char and it is close paren'."\n"; }
					if ($this->debug_dcom > 1) { echo "\n".'bs('.__LINE__.'): DONE, $die_test_said set to ['.serialize($die_test_said).']'."\n"; }
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): standard close paren, decrease debths'."\n"; }
					
					// decrease debth items
					$this->bs_cur_debth--;
					$this_level_debth--;
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): $this->bs_cur_debth ['.$this->bs_cur_debth.'], $this->bs_max_debth ['.$this->bs_max_debth.'], $this_level_debth ['.$this_level_debth.']'."\n"; }
					
					// eat this paren and move on 
					if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): eat this close paren before we leave'."\n"; }
					// substr(str, 1) will remove only the first char (at pos [0])
					$this->bs_rawstr = substr($this->bs_rawstr, 1);
					if ($this->debug_dcom > 2) { echo 'bs('.__LINE__.'): NEW $this->bs_rawstr is: ['.$this->bs_rawstr."]\n"; }
					
					// DIE
					if ($this->debug_dcom > 1) { echo 'bs('.__LINE__.'): *** EXITING THIS CALL we processsed ONE part and there is no contiguous part, we must EXIT'."\n"; }
					if ($this->debug_dcom > 0) { echo 'bs('.__LINE__.'): LEAVING, returning $this->bs_cur_debth ['.$this->bs_cur_debth.']'.'</pre>'."\n\n\n"; }
					return $this->bs_cur_debth;
				}
				/*
				elseif ((strlen($this->bs_rawstr) > 3)
				&& ($this->bs_rawstr{0} == ')')
				&& ($this->bs_rawstr{1} == ' '))
				{
					// for function more_data_test expects param to start with possible next items
					$inspect_str = substr($this->bs_rawstr, 2);
					echo 'bs('.__LINE__.'): ... continue analysis, $inspect_str is ['.$inspect_str.']'."\n";
					// circumstances NOT consistent with next data element, i.e. more of this data
					if ($this->more_data_test($inspect_str) == True)
					{
						echo "\n".'bs('.__LINE__.'): *** FREAK OUT I think we have more bodystruct left, UNEXPECTED'."\n";
						echo 'bs('.__LINE__.'): $this->more_data_test($inspect_str) test returned TRUE'."\n";
						echo 'bs('.__LINE__.'): FIXME what would we do if this happens?'."\n\n\n";
					}
					else
					{
						echo "\n".'bs('.__LINE__.'): $this->more_data_test($inspect_str) returns FALSE'."\n";
						echo "\n".'bs('.__LINE__.'): DONE, test says no bodystructure data follows'."\n";
						echo 'bs('.__LINE__.'): FIXME do I return here?'."\n\n\n";
					}
				}
				*/
				else
				{
					echo "\n".'bs('.__LINE__.'): *** FREAK OUT unhandled situation!!!!, $die_test_said is ['.serialize($die_test_said).']'."\n";
				}
				echo 'bs('.__LINE__.'): finished DIE double check block, what now??? DIE now???'."\n";
			}
			
			
			echo "\n".'bs('.__LINE__.'): *** FREAK OUT what are we doing down here, we should return by now!!!!'."\n";
			// ** WE ARE DONE, I HOPE, WITH THIS LEVEL
			echo 'bs('.__LINE__.'): for whatever reason we reach the end of function, LEAVING NOW'.'</pre>'."\n";
			return;
		}



		/*!
		@function fetchstructure
		@abstract not yet implemented in IMAP sockets module
		@param $stream_notused
		@param $msg_num Sockets CAN use UID directly, feed here the UID
		@param $flags FT_UID is the only valid flag
		@author Angles
		@discussion implements imap_fetchstructure
		*/
		function fetchstructure($stream_notused,$msg_num,$flags="")
		{
			
			if ($this->debug_dcom > 0) { echo 'imap_sock.fetchstructure('.__LINE__.'): ENTERING $msg_num ['.$msg_num.'] <br>'; }
			// outer control structure for the multi-pass functions
			//if ($this->debug_dcom > 0) { echo 'imap: fetchstructure NOT YET IMPLEMENTED imap sockets function<br>'; }
			//return False;
			
			// SHELL FUNCTION
			// calls sub function
			$this->fetch_request_common($stream_notused,$msg_num,$flags);

			
			if ($this->debug_dcom > 0) { echo 'imap_sock.fetchstructure('.__LINE__.'): LEAVING returning object<br>'; }
			// FOR DEBUGGING
			//return array();
			return $this->msg_struct_stub->parts[0];
		}

		/*!
		@function fetch_request_common
		@abstract first function called by imap_fetchstructure AND imap_header
		@param $stream_notused
		@param $msg_num Sockets CAN use UID directly, feed here the UID
		@param $flags FT_UID is the only valid flag
		@author Angles
		@discussion implements imap_fetchstructure
		*/
		function fetch_request_common($stream_notused,$msg_num,$flags="")
		{
			
			if ($this->debug_dcom > 0) { echo '<pre>'.'imap_sock.fetch_request_common('.__LINE__.'): ENTERING fetchstructure, $msg_num ['.$msg_num.'] <br>'; }
			// outer control structure for the multi-pass functions
			//if ($this->debug_dcom > 0) { echo 'imap: fetchstructure NOT YET IMPLEMENTED imap sockets function<br>'; }
			//return False;
			
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & SE_UID)) )
			{
				$flags |= SE_UID;
			}
			// only SE_UID is supported right now, no flag is not supported because we only use the "UID" command right now
			if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): param $flags ['.htmlspecialchars(serialize($flags)).'], ($flags & SE_UID) is ['.htmlspecialchars(serialize(($flags & SE_UID))).'] <br>'; }
			if ($flags & SE_UID)
			{
				$using_uid = True;
			}
			else
			{
				echo 'imap_sock.fetch_request_common('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>';
				if ($this->debug_dcom > 0) { echo 'imap_sock.fetch_request_common('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>'.'</pre>'; }
				return False;
			}
			if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): $flags ['.htmlspecialchars(serialize($flags)).'], $using_uid ['.htmlspecialchars(serialize($using_uid)).'] only SE_UID coded for, so continuing...<br>'; }
			
			// assemble the server querey, looks like this:  
			// 00000008 UID FETCH 131 (FLAGS INTERNALDATE RFC822.SIZE ENVELOPE BODY)
			//$cmd_tag = 's007';
			$cmd_tag = $this->get_next_cmd_num();
			//$full_command = $cmd_tag.' UID FETCH '.$msg_num.' (FLAGS INTERNALDATE RFC822.SIZE ENVELOPE BODY)';
			$full_command = $cmd_tag.' UID FETCH '.$msg_num.' (FLAGS INTERNALDATE RFC822.SIZE ENVELOPE BODYSTRUCTURE)';
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.fetch_request_common('.__LINE__.'): LEAVING with error: could not write_port<br>'.'</pre>'; }
				$this->error();
				return False;
			}
			
			// read the server data
			$response_array = array();
			// for some reason I get back an array with a single element, item $raw_response[0] which is the string I want to work with
			$response_array = $this->imap_read_port($expecting);
			//if ($this->debug_dcom > 2) { echo 'imap_sock.fetch_request_common('.__LINE__.'): $response_array[0] DUMP: <pre>'; print_r($response_array[0]); echo '</pre>';  }
			if ($this->debug_dcom > 2) { echo 'imap_sock.fetch_request_common('.__LINE__.'): $response_array DUMP: <pre>'; print_r($response_array); echo '</pre>';  }
			
			$do_impode = False;
			if (count($response_array) > 1)
			{
				$do_impode = True;
			}
			if ($do_impode)
			{
				if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): $response_array has more tham 1 element, this is element [0]: <br>'."\n"; }
				if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): $response_array DUMP: <pre>'; print_r($response_array); echo '</pre>'."\n";  }
				
				//if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): assemble $return_str, checking if it has that _{NUMBER}_ chunk data thing pops up <br>'."\n"; }
				
				/*
				$test_str = rtrim($response_array[0]);
				$last_char = strlen($test_str)-1;
				if ($test_str{$last_char} == '}')
				{
					if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): FOUND THAT  _{NUMBER}_ thing , first rtrim each element<br>'."\n";  }
					$loops = count($response_array);
					for ($i=0; $i < $loops ; $i++)
					{
						$response_array[$i] = rtrim($response_array[$i]);
					}
					if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): FOUND THAT  _{NUMBER}_ thing at the end, elimiate that now <br>';  }
					// now eliminate {NUMBER}
					$loops = strlen($response_array[0])-1;
					// fallback
					$end = $loops;
					// search backwars
					for ($x=$loops; $x > 0; $x--)
					{
						// we know last char is } so find the {
						$this_char = $response_array[0]{$x};
						if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): at poss $x ['.$x.'], $this_char is ['.$this_char.'] <br>';  }
						if ($this_char == '{')
						{
							$end = $x;
							if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): FOUND open brach at $end ['.$end.'] <br>';  }
							break;
						}
					}
					// chop it
					$response_array[0] = substr($response_array[0], 0, $end);
					if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): NEW $response_array DUMP: <pre>'; print_r(implode('', $response_array)); echo '</pre>'."\n";  }
					*/
				
				/*
				$return_str = '';
				$loops = count($response_array);
				$seen_chunky_brace = False;
				for ($i=0; $i < $loops ; $i++)
				{
					//if ($seen_chunky_brace == False)
					//{
						$test_str = rtrim($response_array[$i]);
						$last_char = strlen($test_str)-1;
						if ($test_str{$last_char} == '}')
						{
							if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): FOUND THAT  _{NUMBER}_ thing , here starts chunked data<br>'."\n";  }
							$seen_chunky_brace = True;
							if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): we need to eliminate that  _{NUMBER}_ thing at the end of this element <br>';  }
							// now eliminate {NUMBER}
							$loopz = strlen($response_array[$i])-1;
							// fallback
							$end = $loopz;
							// search backwars
							for ($x=$loopz; $x > 0; $x--)
							{
								// we know last char is } so find the {
								$this_char = $response_array[$i]{$x};
								if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): at poss $x ['.$x.'], $this_char is ['.$this_char.'] <br>';  }
								if ($this_char == '{')
								{
									$end = $x;
									if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): FOUND open brach at $end ['.$end.'] <br>';  }
									break;
								}
							}
							// chop it
							$response_array[$i] = substr($response_array[$i], 0, $end);
							// add back that CRLF because we are going to chop that below now that we found chunky brases
							$response_array[$i] .= "\r\n";
							if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): post-chop NEW $response_array['.$i.'] DUMP: <pre>'; print_r($response_array[$i]); echo '</pre>'."\n";  }
						}
					//}
					
					// assemble return string
					if ($seen_chunky_brace == True)
					{
						// chop crlf, we do not want in FETCHSTRUCTURE data
						$this_end = strlen($response_array[$i])-1;
						if (($response_array[$i]{$this_end} == "\n")
						|| ($response_array[$i]{$this_end} == "\r"))
						{
							//$this_end = strlen($response_array[$i])-2;
							//$return_str .= substr($response_array[$i], 0 , $this_end);
							$response_array[$i] = substr($response_array[$i], 0 , $this_end);
						}
						// try again
						$this_end = strlen($response_array[$i])-1;
						if (($response_array[$i]{$this_end} == "\n")
						|| ($response_array[$i]{$this_end} == "\r"))
						{
							//$this_end = strlen($response_array[$i])-2;
							//$return_str .= substr($response_array[$i], 0 , $this_end);
							$response_array[$i] = substr($response_array[$i], 0 , $this_end);
						}
					}
					$return_str .= $response_array[$i];
				}
				*/
				
				if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): $response_array needs imploding AND rtrim-ing, then call function fetch_head_and_struct <br>'; }
				// CALL PROCESSING FUNCTION
				//$this->fetch_head_and_struct(implode('', $response_array));
				//$this->fetch_head_and_struct(rtrim(implode('', $response_array)));
				$return_str = implode('', $response_array);
				if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): imploded $return_str DUMP: <pre>'; print_r($return_str); echo '</pre>'."\n";  }
				// sometimes we still need to rtrim the final product, because we do not want a CRLF at the end of FETCHSTRUCTURE data
				if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): we also rtrim the assembled $return_str because we NEVER want a CRLF at the end of FETCHSTRUCTURE data<br>';  }
				$return_str = rtrim($return_str);
				if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): now call function fetch_head_and_struct(rtrim($return_str)) <br>'; }
				$this->fetch_head_and_struct($return_str);
			}
			else
			{
				$return_str = $response_array[0];
				if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): call function fetch_head_and_struct with rtrim and stripslashed $response_array[0] as feed param<br>'; }
				if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): we also rtrim the assembled $return_str because we NEVER want a CRLF at the end of FETCHSTRUCTURE data<br>';  }
				$return_str = rtrim($return_str);
				//if ($this->debug_dcom > 1) { echo 'imap_sock.fetch_request_common('.__LINE__.'): STRIPSLASH the assembled $return_str because TESTING indicates Courier slash escapes its data <br>';  }
				//$return_str = stripslashes($return_str);
				// CALL PROCESSING FUNCTION
				$this->fetch_head_and_struct($return_str);
			}
			
			if ($this->debug_dcom > 0) { echo 'imap_sock.fetch_request_common('.__LINE__.'): LEAVING <br>'.'</pre>'; }
			// FOR DEBUGGING
			//return array();
		}




		/*!
		@function fetch_head_and_struct
		@abstract MAIN FUNCTION unsed by both imap_header AND imap_fetchstructure
		@param $this__fs_rawstr (string) the return of the compound FETCH request
		@author Angles
		*/
		function fetch_head_and_struct($this__fs_rawstr='')
		{
			if ($this->debug_dcom > 0) { echo '<pre>'.'imap_sock.fetch_head_and_struct('.__LINE__.'): ENTRING fetch_head_and_struct <br>'; }
			$tmp_data = array();
			//$this__fs_rawstr = $response_array[0];
			
			if ($this->debug_dcom > 3) { echo '$this__fs_rawstr is: '.$this__fs_rawstr ."\n\n\n"; }
			
			// the data always comes in this order
			// except if we do not use UID FETCH then UID will not appear, so we check to be safe	
			// NEW: U0Wash puts UID before FLAGS
			
			// ** MSGNO **
			// if using UID then the msg sequence number will be available here
			$start = 0;
			$end = strpos($this__fs_rawstr, ' FETCH (');
			// +7 will get rid of the open paren too
			$end = $end + 8;
			$slen = ($end+0) - $start;
			$tmp_data['msgno'] = substr($this__fs_rawstr, $start, $slen);
			//echo 'raw $tmp_data[msgno] is: ['.$tmp_data['msgno'] ."]\n";
			// now reduce down to the msgnum itself
			// [anything here] * 10 FETCH (
			// chop off last 8 chars
			$tmp_data['msgno'] = substr($tmp_data['msgno'], 0, -8);
			//echo 'raw2 $tmp_data[msgno] is: ['.$tmp_data['msgno'] ."]\n";
			// * 10
			// msgno is from last space to end
			$tmp_data['msgno'] = strrchr($tmp_data['msgno'], ' ');
			$tmp_data['msgno'] = trim($tmp_data['msgno']);
			if ($this->debug_dcom > 1) { echo '$tmp_data[msgno] is: ['.$tmp_data['msgno'] ."]\n"; }
			//// type2 DELETE MAIN STRING OF DONE DATA
			$this__fs_rawstr = substr($this__fs_rawstr, ($end+0));
			//echo 'NEW $this__fs_rawstr is: ['.$this__fs_rawstr."]\n\n\n";
			
			// ** UID for U-Wash **
			// UWash puts UID first, other servers put it later
			if (($this__fs_rawstr{0} == 'U')
			&& ($this__fs_rawstr{1} == 'I')
			&& ($this__fs_rawstr{2} == 'D'))
			{
				// ** UID **  main string is now
				// UID 1044 FLAGS (\Seen) INTERNALDATE "19-Feb-2001 20:27:00 -0500" ...
				// chop first 4 chars
				$this__fs_rawstr = substr($this__fs_rawstr, 4);
				$start = 0;
				// we know a space is right after the number, so ...
				$end = strpos($this__fs_rawstr, ' ');
				$slen = ($end-0) - $start;
				$tmp_data['uid'] = substr($this__fs_rawstr, $start, $slen);
				if ($this->debug_dcom > 1) { echo '$tmp_data[uid] is: ['.$tmp_data['uid'] ."]\n"; }
				// type2 DELETE MAIN STRING OF DONE DATA
				$this__fs_rawstr = substr($this__fs_rawstr, ($end+1));
				//echo 'NEW $this__fs_rawstr is: ['.$this__fs_rawstr."]\n\n\n";
			}
			
			// ** FLAGS **
			$start = strpos($this__fs_rawstr, 'FLAGS (');
			$start = $start + 6;
			// we know flags are (), so first ") " is end of data
			$end = strpos($this__fs_rawstr, ') ' );
			$slen = ($end+1) - $start;
			$tmp_data['flags'] = substr($this__fs_rawstr, $start, $slen);
			if ($this->debug_dcom > 1) { echo '$tmp_data[flags] is: ['.$tmp_data['flags'] ."]\n"; }
			// type2 DELETE MAIN STRING OF DONE DATA
			$this__fs_rawstr = substr($this__fs_rawstr, ($end+2));
			//echo 'NEW $this__fs_rawstr is: ['.$this__fs_rawstr."]\n\n\n";
			
			// ** UID for Cyrus **
			if (($this__fs_rawstr{0} == 'U')
			&& ($this__fs_rawstr{1} == 'I')
			&& ($this__fs_rawstr{2} == 'D'))
			{
				// ** UID **  main string is now
				// UID 129 INTERNALDATE "12-Feb-2004 ....
				$start = strpos($this__fs_rawstr, 'UID ');
				$start = $start + 4;
				// we know INTERNALDATE is next, so ...
				$end = strpos($this__fs_rawstr, 'INTERNALDATE' );
				$slen = ($end-1) - $start;
				$tmp_data['uid'] = substr($this__fs_rawstr, $start, $slen);
				//echo 'prelim $tmp_data[uid] is: ['.$tmp_data['uid'] ."]\n";
				// type2 DELETE MAIN STRING OF DONE DATA
				$this__fs_rawstr = substr($this__fs_rawstr, ($end+0));
				// move this echo to below just for aestetics
				//echo 'NEW $this__fs_rawstr is: ['.$this__fs_rawstr."]\n\n\n";
				// for robustness, just in case something came between UID and INTERNALDATE
				if (stristr($tmp_data['uid'], ' '))
				{
					// chop anything after the first space
					$start = 0;
					$end = strpos($tmp_data['uid'], ' ');
					$slen = ($end-0) - $start;
					$tmp_data['uid'] = substr($tmp_data['uid'], $start, $slen);
					//echo 'post-prep $tmp_data[uid] is: ['.$tmp_data['uid'] ."]\n";
				}
				if ($this->debug_dcom > 1) { echo '$tmp_data[uid] is: ['.$tmp_data['uid'] ."]\n"; }
				//echo 'NEW $this__fs_rawstr is: ['.$this__fs_rawstr."]\n\n\n";
			}

			// ** INTERNALDATE **  main string is now
			// INTERNALDATE "12-Feb-2004 23:16:40 -0500" RFC822.SIZE 2.....
			$start = strpos($this__fs_rawstr, ' "');
			$start = $start + 2;
			// we know RFC822.SIZE is next, so ...
			$end = strpos($this__fs_rawstr, 'RFC822.SIZE' );
			$slen = ($end-2) - $start;
			$tmp_data['internaldate'] = substr($this__fs_rawstr, $start, $slen);
			if ($this->debug_dcom > 1) { echo '$tmp_data[internaldate] is: ['.$tmp_data['internaldate'] ."]\n"; }
			// type2 DELETE MAIN STRING OF DONE DATA
			$this__fs_rawstr = substr($this__fs_rawstr, ($end+0));
			//echo 'NEW $this__fs_rawstr is: ['.$this__fs_rawstr."]\n\n\n";
			
			// ** RFC822.SIZE **  main string is now
			// RFC822.SIZE 2694 ENVELOPE ("Tue, 10 Feb 2004 06:....
			$start = strpos($this__fs_rawstr, 'FC822.SIZE ');
			// a trick because RFC could be pos 0 that is confusing, so FC will be pos 1
			if ($start > 0)
			{
				// snap off "RFC822.SIZE "
				$this__fs_rawstr = substr($this__fs_rawstr, $start+11);
				//echo 'interim NEW $this__fs_rawstr is: ['.$this__fs_rawstr."]\n\n\n";
				// we know a space immediately follows the number
				$start = 0;
				$end = strpos($this__fs_rawstr, ' ');
				$slen = ($end-0) - $start;
				$tmp_data['rfc822.size'] = substr($this__fs_rawstr, $start, $slen);
				if ($this->debug_dcom > 1) { echo '$tmp_data[rfc822.size] is: ['.$tmp_data['rfc822.size'] ."]\n"; }
				// type2 DELETE MAIN STRING OF DONE DATA
				$this__fs_rawstr = substr($this__fs_rawstr, ($end+1));
				//echo 'NEW $this__fs_rawstr is: ['.$this__fs_rawstr."]\n\n\n";
			}
			
			// ** ENVELOPE **  main string is now
			// ENVELOPE ("Tue, 10 Feb 2004 06:48:53 -0200" "Re: sig -
			if (($this__fs_rawstr{0} != 'E')
			&& ($this__fs_rawstr{1} != 'N')
			&& ($this__fs_rawstr{2} != 'V'))
			{
				// chop up to "ENVELOPE ("
				// this is for robustness, ENVELOPE should be right here anyway
				$start = strpos($this__fs_rawstr, 'ENVELOPE (');
				// type2 DELETE MAIN STRING OF DONE DATA
				$this__fs_rawstr = substr($this__fs_rawstr, $start);
				//echo 'interim NEW $this__fs_rawstr is: ['.$this__fs_rawstr."]\n\n\n";
			}
			$start = strpos($this__fs_rawstr, ' (');
			$start = $start + 2;
			// we know ) BODYSTRUCTURE ( is next, so ...
			$end = strpos($this__fs_rawstr, ') BODYSTRUCTURE (' );
			$slen = ($end-0) - $start;
			$tmp_data['envelope'] = substr($this__fs_rawstr, $start, $slen);
			if ($this->debug_dcom > 1) { echo '$tmp_data[envelope] is: ['.$tmp_data['envelope'] ."]\n"; }
			// type2 DELETE MAIN STRING OF DONE DATA
			$this__fs_rawstr = substr($this__fs_rawstr, ($end+2));
			//echo 'NEW $this__fs_rawstr is: ['.$this__fs_rawstr."]\n\n\n";
			
			// ** BODYSTRUCTURE **  main string is now
			// BODYSTRUCTURE (("TEXT" "PLAIN" ("F...
			// we are going to leave all parens including outer most, for the subfunction needs them
			$start = strpos($this__fs_rawstr, ' (');
			$start = $start + 1;
			// we know last char in string is extra ) paren from the outer fetch response as a whole
			rtrim($this__fs_rawstr);
			$end = strlen($this__fs_rawstr)-1;
			$slen = ($end-0) - $start;
			$tmp_data['bodystructure'] = substr($this__fs_rawstr, $start, $slen);
			if ($this->debug_dcom > 1) { echo '$tmp_data[bodystructure] is: ['.$tmp_data['bodystructure'] ."]\n\n"; }
			// string is DONE, nothing left to parse
			
			// final report
			if ($this->debug_dcom > 3) { echo '$tmp_data DUMP'."\n"; print_r($tmp_data); echo "\nend final report \n\n"; }
			
			
			// HANDLE BODYSTRUCTURE
			if ($this->debug_dcom > 1) { echo "\n".' *** about to handle bodystructure ***'."\n"; }
			if ($this->debug_dcom > 1) { echo ' putting $tmp_data[bodystructure] into class var [$this->bs_rawstr]'."\n"; }
			//$this->make_msg_struct($tmp_data['bodystructure']);
			// put bodystructure in a class var so all recursions can operate on it
			$this->bs_rawstr = $tmp_data['bodystructure'];
			// prepare stuff for the function
			// are we in the bodystruct main parens or not
			// OBSOLETE $this->bs_inside = False;
			// initialize the base stub
			//if ($this->msg_struct_stub == '##NOTHING##')
			//{
				$this->msg_struct_stub = '';
				$this->msg_struct_stub = new parts_parent_stub;
				if ($this->debug_dcom > 1) { echo ' initialized [$this->msg_struct_stub] struct as new parts_parent_stub'."\n"; }
				//echo ' $this->msg_struct_stub DUMP'."\n"; print_r($this->msg_struct_stub); echo "\n";
			//}
			// what debth level are we working on of bodystructure
			$this->bs_cur_debth = 0;
			// what is the maximum debth we have been to in bodystructure
			$this->bs_max_debth = 0;
			// keep data about each level such as how many parts on on level X
			if ($this->debug_dcom > 1) { echo ' *** CALLING $this->make_msg_struct, using $this->msg_struct_stub as param ref_parent *** '."\n\n\n"; }
			$this->make_msg_struct('main loop thing line ('.__LINE__.')', $this->msg_struct_stub);

			if ($this->debug_dcom > 1) { echo ' RETURNING from "make_msg_struct" function'."\n"; }
			if ($this->debug_dcom > 2) { echo 'FINAL REPORT for msg_struct $this->msg_struct_stub DUMP'."\n"; print_r($this->msg_struct_stub); echo "\n\n"; }
			
			// GET ENVELOPE DATA
			//if ($this->debug_dcom > 1) { echo ' prep for envelope: STRIPSLASH the $tmp_data[envelope] because TESTING indicates Courier slash escapes its data <br>';  }
			//$tmp_data['envelope'] = stripslashes($tmp_data['envelope']);
			if ($this->debug_dcom > 1) { echo ' *** ENVELOPE - calling $this->imap_parse_header($tmp_data) '."\n\n\n"; }
			$this->envelope_struct = $this->imap_parse_header($tmp_data);
			
			
			if ($this->debug_dcom > 2) { echo 'FINAL REPORT for envelope_struct $this->envelope_struct DUMP'."\n"; print_r($this->envelope_struct); echo "\n\n"; }
			if ($this->debug_dcom > 0) { echo 'imap_sock.fetch_head_and_struct('.__LINE__.'): LEAVING <br>'.'</pre>'; }
		} 
		// main_loop_thing


		/**************************************************************************\
		*
		*	Message Envelope (Header Info) Data
		*
		\**************************************************************************/
		
		/*!
		@function make_address
		@abstract 
		@param $address_str (string) space seperated address string items NO PARENS
		@discussion strip open and close parens before feeding into here. You get back an 
		array of object of type address, the param is of type from the ENVELOPE data.
		@author Angles
		*/
		function make_address($address_str)
		{
			$tmp_data = array();
			$tmp_data['address_str'] = $address_str;
			if ($this->debug_dcom > 0) { echo '<pre>'.'make_address('.__LINE__.'): ENTERING param $address_str BETTER already have open and close parens stripped! '."\n"; }
			if ($this->debug_dcom > 1) { echo 'make_address('.__LINE__.'): $tmp_data[address_str] is: ['.$tmp_data['address_str'] ."]\n"; }
			// what we have now looks somethign like one of these
			// "CHARSET" "utf-8"
			// "TYPE" "multipart/alternative" "BOUNDARY" "----=_NextPart_000_00B9_01C3CE2B.BE78A8C0"
			$tmp_data['params_exploded'] = array();
			$tmp_data['parameters'] = array();				
			$tmp_data['params_exploded'] = explode(' ', $tmp_data['address_str']);
			// loop to clean of leading and trailing quotes
			$loops = count($tmp_data['params_exploded']);
			for ($i=0; $i < $loops ;$i++)
			{
				$this_str = $tmp_data['params_exploded'][$i];
				if ($this_str{0} == '"')
				{
					$this_str = substr($this_str, 1);
				}
				$last_pos = (strlen($this_str) - 1);
				if ($this_str{$last_pos} == '"')
				{
					$this_str = substr($this_str, 0, $last_pos);
				}
				$tmp_data['params_exploded'][$i] = $this_str;
			}
			//echo 'make_address('.__LINE__.'): post-cleaning $tmp_data[params_exploded] is: ['.serialize($tmp_data['params_exploded'])."]\n";
			if ($this->debug_dcom > 2) { echo 'make_address('.__LINE__.'): post-cleaning $tmp_data[params_exploded] DUMP: '.""; print_r($tmp_data['params_exploded']); echo ""; }
			// loop to make param objects
			$loops = count($tmp_data['params_exploded']);
			for ($i=0; $i < $loops ;$i=($i+2))
			{
				$attribute = 'UNKNOWN_PARAM_ATTRIBUTE';
				if ((isset($tmp_data['params_exploded'][$i]))
				&& (trim($tmp_data['params_exploded'][$i]) != ''))
				{
					$attribute = $tmp_data['params_exploded'][$i];
				}
				$value = 'UNKNOWN_PARAM_VALUE';
				$val_pos = $i+1;
				if ((isset($tmp_data['params_exploded'][$val_pos]))
				&& (trim($tmp_data['params_exploded'][$val_pos]) != ''))
				{
					$value = $tmp_data['params_exploded'][$val_pos];
				}
				// make this param pair object
				$tmp_data['parameters'][] = new msg_params($attribute,$value);
			}
			if ($this->debug_dcom > 2) { echo 'make_address('.__LINE__.'): post-looping $tmp_data[parameters] DUMP: '.""; print_r($tmp_data['parameters']); echo ""; }
			//echo 'make_param('.__LINE__.'): $tmp_data[parameters] is: ['.serialize($tmp_data['parameters'])."]\n";
			if ($this->debug_dcom > 0) { echo 'make_address('.__LINE__.'): LEAVING returning $tmp_data[parameters]'.'</pre>'."\n"; }
			return $tmp_data['parameters'];
		}
		
		
		/*!
		@function extract_header_item
		@abstract 
		@result 
		@discussion 
		@author Angles
		@access private
		*/
		function extract_header_item($data_type,$next_item_type,$if_nothing)
		{
			// $data_type - is either "string" or "paren_list"
			// $next_item_type - "string" or "paren_list" or "eol", used to tell us what to expect as end to this data
			// $if_nothing - tells us what an absence of data looks like, either NIL, (), or ""
			if ($this->debug_dcom > 0) { echo '<pre>'.'extract_header_item('.__LINE__.'): ENTERING'."\n"; }
			// is it empty
			$nothing_test = substr($this->env_rawstr, 0, strlen($if_nothing));
			// backup test for nil
			$backup_nil_test = substr($this->env_rawstr, 0, 3);
			if ($nothing_test == $if_nothing)
			{
				if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): this item is empty, clean $this->env_rawstr, param $if_nothing ['.$if_nothing.']'."\n"; }
				// eat this empty item
				$this->env_rawstr = substr($this->env_rawstr, strlen($if_nothing)+1);
				if ($this->debug_dcom > 2) { echo 'extract_header_item: NEW $this->env_rawstr is: ['.$this->env_rawstr."]\n"; }
				// return empty item
				if ($this->debug_dcom > 0) { echo 'extract_header_item('.__LINE__.'): LEAVING returning empty item'.'</pre>'."\n"; }
				return;
			}
			// BACKUP TEST FOR NIL
			if (($if_nothing != 'NIL')
			&& ($backup_nil_test == 'NIL'))
			{
				if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): this item is NIL, UNEXPECTEDLY we did not expect a NIL here, clean $this->env_rawstr, note param $if_nothing ['.$if_nothing.']'."\n"; }
				// eat this empty item
				$this->env_rawstr = substr($this->env_rawstr, strlen('NIL')+1);
				if ($this->debug_dcom > 2) { echo 'extract_header_item: NEW $this->env_rawstr is: ['.$this->env_rawstr."]\n"; }
				if ($this->debug_dcom > 0) { echo 'extract_header_item('.__LINE__.'): LEAVING returning empty item'.'</pre>'."\n"; }
				return;
			}
			if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): status update: $data_type is ['.$data_type.'], $next_item_type is ['.$next_item_type.'], $if_nothing is ['.$if_nothing.']'."\n"; }
			
			// continue
			if ($data_type == 'string')
			{
				$got_str = '';
				// sanity check
				if ($this->env_rawstr{0} == '"')
				{
					if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): first char is ['.$this->env_rawstr{0}.'] so we have standard quoted string'."\n"; }

					if ($next_item_type == 'string')
					{
						//$end = strpos($this->env_rawstr, '" "');
						$end_1 = strpos($this->env_rawstr, '" "');
						$end_2 = strpos($this->env_rawstr, '" {');
						$end_3 = strpos($this->env_rawstr, '" ');
						if (($end_1 == $end_3)
						|| ($end_2 == $end_3))
						{
							// good, this is what we expect
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): status: $end_1 = pos [" "] = ['.$end_1.']; $end_2 = pos [" {] ['.$end_2.']; $end_3 = pos [" ] ['.$end_3.']'."\n"; }
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): good, either $end_1 == $end_3 or $end_2 == $end_3, so use $end is $end_3'."\n"; }
							$end = $end_3;
						}
						else
						{
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): current $this->env_rawstr DUMP: '.""; print_r($this->env_rawstr); echo "\n"; }
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): STRANGE THINGS: $end_1 = pos [" "] = ['.$end_1.']; $end_2 = pos [" {] ['.$end_2.']; $end_3 = pos [" ] ['.$end_3.']'."\n"; }
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): neither $end_1 == $end_3 nor does $end_2 == $end_3'."\n"; }
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): so something somewhere is is a malfomed '."\n"; }
							$end_4 = strpos($this->env_rawstr, ' "');
							$end_5 = strpos($this->env_rawstr, ' NIL ');
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): $end_3 = pos [ "] = ['.$end_3.']; $end_4 = pos [ NIL ] = ['.$end_4.']'."\n"; }
							if (($end_4 - $end_2) == 1)
							{
								if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): $end_4 - $end_2 == 1 SO this item is OK and next item is NIL, $end_2 as $end'."\n"; }
								$end = $end_2;
							}
							elseif ($end_4 < $end_3)
							{
								if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): $end_4 < $end_3 SO probably this item is missing close quote, $end_4 as $end'."\n"; }
								$end = $end_4;
							}
							//elseif (($end_3 - $end_2) == 1)
							//{
							//	echo 'extract_header_item('.__LINE__.'): $end_3 - $end_2 == 1 SO probably next item is a malfomed string then use $end_2 as $end'."\n";
							//	$end = $end_2;
							//}
							elseif (($end_3 < $end_2)
							&& ($end_3 > 0))
							{
								if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): GUESSING conditions say probably THIS item is a malfomed string then use $end_3 as $end'."\n"; }
								$end = $end_3;
							}
							elseif ($end_2 > 0)
							{
								if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): GUESSING conditions say probably next item is a malfomed string then use $end_2 as $end'."\n"; }
								$end = $end_2;
							}
							elseif ($end_3 > 0)
							{
								if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): conditions say this item is a malfomed string then use $end_3 as $end'."\n"; }
								$end = $end_3;
							}
							else
							{
								echo 'extract_header_item: FREAK OUT line '.__LINE__."\n";
								if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): unhandled condition, fallback to use $end_1 as $end'."\n"; }
								$end = $end_1;
							}
						}
					}
					elseif ($next_item_type == 'paren_list')
					{
						//$end = strpos($this->env_rawstr, '" (');
						// we know in these header items the parens are always enbeded so (( always is next
						$end_1 = strpos($this->env_rawstr, '" ((');
						$end_2 = strpos($this->env_rawstr, ' ((');
						if (($end_2 - $end_1) == 1)
						{
							// good, this is what we expect
							$end = $end_1;
						}
						else
						{
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): STRANGE THINGS: $end_2 - $end_1 != 1; $end_1 = pos [" (] = ['.$end_1.']; $end_2 = pos [ (] ['.$end_2.']'."\n"; }
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): probably this item is a malfomed string missing its ending quote, then use $end_2 as $end'."\n"; }
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): current $this->env_rawstr DUMP: '.""; print_r($this->env_rawstr); echo "\n"; }
							$end = $end_2;
						}	
					}
					elseif ($next_item_type == 'eol')
					{
						$end = strlen($this->env_rawstr)-1;
					}
					else
					{
						echo 'extract_header_item: FREAK OUT line '.__LINE__."\n";
					}
					$start = 1;
					// end
					$slen = ($end-0) - $start;
					$got_str = substr($this->env_rawstr, $start, $slen);
					if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): $got_str is ['.$got_str.']'."\n"; }

					
					//// type2 DELETE MAIN STRING OF DONE DATA
					if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): we can now clean $this->env_rawstr of it '."\n"; }
					// but do NOT chop off a potential open quote of the next string
					if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): first use ($end+1) as start of strip '."\n"; }
					$this->env_rawstr = substr($this->env_rawstr, ($end+1));
					if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): prelim NEW $this->env_rawstr is: ['.$this->env_rawstr."]\n"; }
					if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): secondly we ltrim to finish off the strip '."\n"; }
					$this->env_rawstr = ltrim($this->env_rawstr);
					if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): NEW $this->env_rawstr is: ['.$this->env_rawstr."]\n"; }

					if ($this->debug_dcom > 0) { echo 'extract_header_item: LEAVING returning $got_str ['.$got_str.']'.'</pre>'."\n\n"; }
					return $got_str;
					
				}
				elseif ($this->env_rawstr{0} == '{')
				{
					if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $this->env_rawstr{0} is ['.htmlspecialchars($this->env_rawstr{0}).'] so description_str STRING LITERAL data '."\n"; }
					// handle literl SEQUENCE
					$got_str = '';
					$start = 1;
					$end = strpos($this->env_rawstr, '}'."\r\n");
					$slen = $end-$start;
					$literal_len = substr($this->env_rawstr, 1, $slen);
					$literal_len = (int)trim($literal_len);
					if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $literal_len is ['.$literal_len.'] '."\n"; }
					// chop up to start of literal data
					$this->env_rawstr = substr($this->env_rawstr, ($end+3));
					if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): chopped interim NEW $this->env_rawstr is: ['.$this->env_rawstr."]\n"; }
					// grab exact literal data
					// find the first space at or after $literal_len
					// multibyte strings throw the function substr off 
					// we use OFFSET to start searching at $literal_len
					$best_space_pos = strpos($this->env_rawstr, ' ', $literal_len);
					if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $best_space_pos is ['.serialize($best_space_pos).'] '."\n"; }
					if ($best_space_pos > $literal_len)
					{
						$end = $best_space_pos;
					}
					else
					{
						$end = $literal_len;
					}
					if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): decide to use $end is ['.serialize($end).'] '."\n"; }
					$got_str = substr($this->env_rawstr, 0, $end);
					//// type2 DELETE MAIN STRING OF DONE DATA
					$this->env_rawstr = substr($this->env_rawstr, ($end+1));
					// show this later for better clarity of debugging
					if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): NEW $this->env_rawstr is: ['.$this->env_rawstr."]\n"; }
					if ($this->debug_dcom > 0) { echo 'extract_header_item: LEAVING returning $got_str ['.$got_str.']'.'</pre>'."\n\n"; }
					return $got_str;
				}
				else
				{
					echo 'extract_header_item('.__LINE__.'): FIXME: FREAK OUT first char is supposed to be " but it is not'."\n";
					$got_str = '';
					
					if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): semi-FREAK OUT first char is supposed to be " but it is not'."\n"; }
					if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): current $this->env_rawstr DUMP: '."\n"; print_r($this->env_rawstr); echo "\n"; }
					/*
					echo 'extract_header_item('.__LINE__.'): attempt to recover by finding the next available " and assume it is open quote'."\n";
					echo 'extract_header_item('.__LINE__.'): sometimes malformed subject can cause imap server to screw up the open quote for the subject element'."\n";
					echo 'extract_header_item('.__LINE__.'): in which case anything before the open " is not RFC3501 compiant so dicsard it'."\n";
					$found_at = strpos($this->env_rawstr, '"');
					echo 'extract_header_item('.__LINE__.'): found first open quote at $found_at ['.$found_at.'], eat all before it'."\n";
					if ($found_at > 0)
					{
						echo 'extract_header_item('.__LINE__.'): eat all before that paren as invalid data'."\n";
						$this->env_rawstr = substr($this->env_rawstr, $found_at);
						echo 'extract_header_item('.__LINE__.'): NEW $this->env_rawstr DUMP: '.""; print_r($this->env_rawstr); echo "";
					}
					echo 'extract_header_item('.__LINE__.'): DONE attempt to recover, contuinue on ... '."\n";
					*/
					if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): attempt to recover by simply adding a " to the beginning of the string'."\n"; }
					if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): yea I know it is crazy, but server should not send us unconforming strings anyway, and set var $manually_added_quote'."\n"; }
					$manually_added_quote = True;
					$this->env_rawstr = '"'.$this->env_rawstr;
					if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): NEW $this->env_rawstr DUMP: '."\n"; print_r($this->env_rawstr); echo "\n"; }
				}
			}
			elseif ($data_type == 'paren_list')
			{
				$got_list = array();
				$tmp_data = array();
				// sanity check
				if ($this->env_rawstr{0} != '(')
				{
					echo 'extract_header_item: FREAK OUT line '.__LINE__."\n";
					$got_list = array();
				}
				
				// continue ...
				if ($next_item_type == 'string')
				{
					$end = strpos($this->env_rawstr, ') "');
				}
				elseif ($next_item_type == 'paren_list')
				{
					$end = strpos($this->env_rawstr, ') (');
				}
				elseif ($next_item_type == 'eol')
				{
					$end = strlen($this->env_rawstr)-1;
				}
				else
				{
					echo 'extract_header_item: FREAK OUT line '.__LINE__."\n";
				}
				// sanity check #1
				if ($end < 1)
				{
					// fallback, maybe next item is itself empty
					if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): fallback, assuming next item is NIL '."\n"; }
					$end = strpos($this->env_rawstr, ') NIL');
				}
				if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $end is ['.serialize($end).']'."\n"; }
				$start = 1;
				// end
				$slen = ($end-0) - $start;
				$tmp_data['raw_str'] = substr($this->env_rawstr, $start, $slen);
				if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): $tmp_data[raw_str] is ['.$tmp_data['raw_str'].']'."\n"; }
				
				//// type2 DELETE MAIN STRING OF DONE DATA
				if ($this->debug_dcom > 1) { echo 'extract_header_item: before we process paren_list, clean $this->bs_rawstr of it '."\n"; }
				$this->env_rawstr = substr($this->env_rawstr, ($end+2));
				if ($this->debug_dcom > 2) { echo 'extract_header_item: NEW $this->env_rawstr is: ['.$this->env_rawstr."]\n"; }
				
				//
				$tmp_data['addys_exploded'] = explode(')(', $tmp_data['raw_str']);
				if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $tmp_data[addys_exploded] DUMP: '.""; print_r($tmp_data['addys_exploded']); echo "\n"; }
				//
				
				// loop to clean of leading and trailing quotes
				$loops = count($tmp_data['addys_exploded']);
				for ($i=0; $i < $loops ;$i++)
				{
					$this_str = $tmp_data['addys_exploded'][$i];
					if ($this_str{0} == '(')
					{
						$this_str = substr($this_str, 1);
					}
					$last_pos = (strlen($this_str) - 1);
					if ($this_str{$last_pos} == ')')
					{
						$this_str = substr($this_str, 0, $last_pos);
					}
					$tmp_data['addys_exploded'][$i] = $this_str;
				}
				if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): post-paren-strip $tmp_data[addys_exploded] DUMP: '.""; print_r($tmp_data['addys_exploded']); echo "\n"; }
				
				// loop to make address object(s)
				$loops = count($tmp_data['addys_exploded']);
				$tmp_data['addy_item'] = array();
				$tmp_data['return_array'] = array();
				for ($i=0; $i < $loops ;$i++)
				{
					
					// looking for 4 items, all either string or nil
					// personal adl mailbox host
					// "Lars Kneschke" NIL "lars" "example.de"
					// 1st - personal
					$this_str = $tmp_data['addys_exploded'][$i];
					
					// we know there are 4 items, so do 4 loops
					for ($x=0; $x < 4 ;$x++)
					{
						if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): -begin- inner loop $i ['.$i.'], $x ['.$x.'], $this_str is ['.$this_str.']'."\n"; }
						if (($this_str{0} == 'N')
						&& ($this_str{1} == 'I')
						&& ($this_str{2} == 'L'))
						{
							$tmp_data['addy_item'][$i][$x] = False;
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): empty DATA ITEM: $tmp_data[addy_item]['.$i.']['.$x.'] is ['.$tmp_data['addy_item'][$i][$x].']'."\n"; }
							// chop this item from $this_str
							$this_str = substr($this_str, 4);
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): NEW $this_str is ['.$this_str.']'."\n"; }
						}
						elseif ($this_str{0} == '{')
						{
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $this_str{0} is ['.htmlspecialchars($this_str{0}).'] so description_str STRING LITERAL data '."\n"; }
							// handle literl SEQUENCE
							$my_personal = '';
							$start = 1;
							$end = strpos($this_str, '}'."\r\n");
							$slen = $end-$start;
							$literal_len = substr($this_str, 1, $slen);
							$literal_len = (int)trim($literal_len);
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $literal_len is ['.$literal_len.'] '."\n"; }
							// chop up to start of literal data
							$this_str = substr($this_str, ($end+3));
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): chopped interim NEW $this_str is: ['.$this_str."]\n"; }
							// grab exact literal data
							// find the first space at or after $literal_len
							// multibyte strings throw the function substr off 
							// we use OFFSET to start searching at $literal_len
							$best_space_pos = strpos($this_str, ' ', $literal_len);
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $best_space_pos is ['.serialize($best_space_pos).'] '."\n"; }
							if ($best_space_pos > $literal_len)
							{
								$end = $best_space_pos;
							}
							else
							{
								$end = $literal_len;
							}
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): decide to use $end is ['.serialize($end).'] '."\n"; }
							$my_personal = substr($this_str, 0, $end);
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): initial $my_personal ['.$my_personal.']'."\n"; }
							// put the personal into out item holder
							$tmp_data['addy_item'][$i][$x] = $my_personal;
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): FINAL DATA ITEM: $tmp_data[addy_item]['.$i.']['.$x.'] is ['.$tmp_data['addy_item'][$i][$x].']'."\n"; }
							//// type2 DELETE MAIN STRING OF DONE DATA
							$this_str = substr($this_str, ($end+1));
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): NEW $this_str is ['.$this_str.']'."\n"; }
						}
						elseif ($x == 0)
						{
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): in real life we know item 2 is always NIL'."\n"; }
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): so this item 1 is everything before NIL, if all goes well, that is the personal part'."\n"; }
							$start = 0;
							// in real like we have 1. personal, 2. spaceNILspace then the reat
							$end = strpos($this_str, ' NIL ');
							$slen = ($end-0) - $start;
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $start ['.$start.'], $end ['.$end.'],  $slen ['.$slen.']'."\n"; }
							// grab the data
							$my_personal = substr($this_str, $start, $slen);
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): initial ITEM: $my_personal is ['.$my_personal.']'."\n"; }
							// if this personal has is open AND closing paren, strip them
							$last_pos = strlen($my_personal)-1;
							if ($my_personal{0} == '"')
							if ($my_personal{$last_pos} == '"')
							{
								if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $my_personal glob has both open and close paren, so strip them'."\n"; }
								if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): i.e. this is normal, it seems strange but sometimes this is not the case'."\n"; }
								// strip closing paren
								$my_personal = substr($my_personal, 0, $last_pos);
								// strip open paren
								$my_personal = substr($my_personal, 1);
							}
							else
							{
								if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $my_personal glob missing either open or close paren, so leave as is'."\n"; }
								if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): in these cases server gave is strange data, leave unmolested'."\n"; }
							}
							// put the personal into out item holder
							$tmp_data['addy_item'][$i][$x] = $my_personal;
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): FINAL DATA ITEM: $tmp_data[addy_item]['.$i.']['.$x.'] is ['.$tmp_data['addy_item'][$i][$x].']'."\n"; }
							
							// strip the main string of the done data
							$this_str = substr($this_str, $end+1);
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): NEW $this_str is ['.$this_str.']'."\n"; }
						}
						elseif ($this_str{0} != '"')
						{
							// freak out, we should have an open quote
							echo 'extract_header_item('.__LINE__.'): FREAK OUT, we should have an open quote'."\n";
							$tmp_data['addy_item'][$i][$x] = False;
						}
						else
						{
							// grab this string
							// we know the open quote is first char
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): step 1. we know first char is quote'."\n"; }
							$start = 1;
							// what is the end of this data?
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): step 2. what is the end of this data'."\n"; }
							if ($x == 3)
							{
								// this is the end of the string
								if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): step 2 answer: since $x == 3 (it is ['.$x.']). that means we are at the end of data, last item of 4'."\n"; }
								$end = strlen($this_str)-1;
							}
							else
							{
								// pick the smallest number above zero
								$end_1 = strpos($this_str, '" "');
								$end_2 = strpos($this_str, '" NIL');
								if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): ... $end_1 is ['.$end_1.'] and $end_2 is ['.$end_2.'], pick the smallest number above zero'."\n"; }
								if (($end_1 < 1)
								&& ($end_2 < 1))
								{
									// FREAKED OUT! fallback to the end of the string
									$end = strlen($this_str)-1;
								}
								else
								{
									// start here
									$end = (int)$end_1;
									// imediate check
									if ($end < 1)
									{
										// we have no choice here
										$end = $end_2;
									}
									elseif (($end_2 < $end)
									&& ($end_2 > 0))
									{
										// looks like end_2 is smallest number above zero
										$end = $end_2;
									}
									// freak out fallback
									if ($end < 1)
									{
										// FREAKED OUT! fallback to the end of the string
										$end = strlen($this_str)-1;
									}
								}
							}
							$slen = ($end-0) - $start;
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): $start ['.$start.'], $end ['.$end.'],  $slen ['.$slen.']'."\n"; }
							// grab the data
							$tmp_data['addy_item'][$i][$x] = substr($this_str, $start, $slen);
							if ($this->debug_dcom > 1) { echo 'extract_header_item('.__LINE__.'): DATA ITEM: $tmp_data[addy_item]['.$i.']['.$x.'] is ['.$tmp_data['addy_item'][$i][$x].']'."\n"; }
							// strip the main string of the done data
							$this_str = substr($this_str, $end+2);
							if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): NEW $this_str is ['.$this_str.']'."\n"; }
						}
						if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): loop check $i is ['.$i.'], $x is is ['.$x.']'."\n"; }
					}
					// done with this individual address item
					// prepare new object
					$this_addy = new address;
					// fill object from data we just collected
					// personal
					if ($tmp_data['addy_item'][$i][0])
					{
						$this_addy->personal = $tmp_data['addy_item'][$i][0];
					}
					else
					{
						unset($this_addy->personal);
					}
					// adl - in real life this is not used
					unset($this_addy->adl);
					// mailbox - php says INVALID_ADDRESS on error
					// because we better have a mailbox or we do not have an email addy
					// and with a basic check for the absence if @ in this part
					if (($tmp_data['addy_item'][$i][2])
					&& (!stristr($tmp_data['addy_item'][$i][2], '@')))
					{
						$this_addy->mailbox = $tmp_data['addy_item'][$i][2];
					}
					else
					{
						//unset($this_addy->mailbox);
						// php says INVALID_ADDRESS on error
						// because we better have a mailbox or we do not have an email addy
						$this_addy->mailbox = 'INVALID_ADDRESS';
					}
					// mailbox, with a very basic syntax check for presence a dot
					// we do not check specific for dot at end-3 or end-2 because I hear of a ".mobile" coming
					if (($tmp_data['addy_item'][$i][3])
					&& (stristr($tmp_data['addy_item'][$i][3], '.')))
					{
						$this_addy->host = $tmp_data['addy_item'][$i][3];
					}
					else
					{
						//unset($this_addy->host);
						// php says something like .SYNTAX-ERROR. (with those dots) on error
						$this_addy->host = '.SYNTAX-ERROR.';
					}
					// put into the return structure
					$next_pos = count($tmp_data['return_array']);
					$tmp_data['return_array'][$next_pos] = $this_addy;
				}
				if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): post-process $tmp_data[addy_item] DUMP: '.""; print_r($tmp_data['addy_item']); echo "\n"; }
				if ($this->debug_dcom > 2) { echo 'extract_header_item('.__LINE__.'): final $tmp_data[return_array] DUMP: '.""; print_r($tmp_data['return_array']); echo "\n"; }
				
				if ($this->debug_dcom > 0) { echo 'extract_header_item: LEAVING returning $tmp_data[return_array] ['.serialize($tmp_data['return_array']).']'.'</pre>'."\n\n"; }
				return $tmp_data['return_array'];
			}
			
			echo 'extract_header_item: FREAK OUTwhat are we doing here? line '.__LINE__."\n";
		}
		
		/*!
		@function make_xxaddress_str
		@abstract makes the non-array part of the address struct for envelope
		@param $array_of_address (array) each item is var of type address
		@result string
		@discussion sub-function, returns either personal or mailbox@host
		@author Angles
		@access private
		*/
		function make_xxaddress_str($array_of_address)
		{
			$loops = count($array_of_address);
			$return_str = '';
			for ($i=0; $i < $loops ;$i++)
			{
				$this_name = '';
				$address_obj = $array_of_address[$i];
				if (isset($address_obj->personal))
				{
					$this_name = $address_obj->personal;
					// from what I can tell, this is what php-imap does
					// averything normal, leave unchanged
					// exceptions:
					// a. apply "addslashes" to it, if it changed, put quotes around it
					// b. if there is a comma, put quotes around it
					if (($this_name != addslashes($this_name))
					|| (strpos($this_name, ',') > 0))
					{
						$this_name = '"'.addslashes($this_name).'"';
					}
					// finally, I kid u not, this is how php-imap does it, it adds a space to personal here
					$this_name .= ' ';					
				}
				else
				{
					$this_name = $address_obj->mailbox .'@'. $address_obj->host;
				}
				// assemble this part into the bigger return string
				if ($i == 0)
				{
					$return_str = $this_name;
				}
				else
				{
					$return_str .= ','.$this_name;
				}
			}
			return $return_str;
		}
		
		/*!
		@function imap_parse_header
		@abstract implements IMAP_HEADER (alias to IMAP_HEADERINFO)
		@result returns an instance of Class "hdr_info_envelope", or returns False on error
		@discussion sub-function, some data is passed into here, other data is made here. 
		imap FETCH ENVELOPE response this has 10 peices if information in this order 
		1. date - string
		2. subject - string
		3. from
		4. sender
		5. reply-to
		6. to
		7. cc
		8. bcc
		9. in-reply-to - string
		10. message-id - string
		NOTE items 3 thru 8 are data type paren list, which can contain paren lists in them, 
		the others are type string which are deliniated by quotes
		@author Angles
		@access public
		*/
		function imap_parse_header($tmp_data=array())
		{			
			// make new structure
			if ($this->debug_dcom > 0) { echo '<pre>'.'imap_parse_header('.__LINE__.'): ENTERING <br>'."\n"; }
			$info = new hdr_info_envelope;
			
			// initialize and/or fill with data already gathered by another function
			unset($info->remail);
			// (this chunk moved below)
			// these next two never seen in the wild with imap usage
			unset($info->return_pathaddress);
			unset($info->return_path);
			// --- Message Flags --- 
			$info->Recent = ' ';
			$info->Unseen = ' ';
			$info->Answered = ' ';
			$info->Deleted = ' ';	
			$info->Draft = ' ';
			$info->Flagged = ' ';
			if ((stristr($tmp_data['flags'],'\Recent'))
			&& (stristr($tmp_data['flags'],'\Seen') == False))
			{
				//  'R' if recent and seen, 'N' if recent and not seen, ' ' if not recent
				$info->Recent = 'N';
			}
			if ((stristr($tmp_data['flags'],'\Seen') == False)
			&& (stristr($tmp_data['flags'],'\Recent') == False))
			{
				//  'U' if not seen AND not recent, ' ' if seen OR not seen and recent
				$info->Unseen = 'U';
			}
			if (stristr($tmp_data['flags'],'\Flagged'))
			{
				//  'F' if flagged, ' ' if not flagged
				$info->Flagged = 'F';
			}
			if (stristr($tmp_data['flags'],'\Answered'))
			{
				//  'A' if answered, ' ' if unanswered
				$info->Answered = 'A';
			}
			if (stristr($tmp_data['flags'],'\Deleted'))
			{
				//  'D' if deleted, ' ' if not deleted
				$info->Deleted = 'D';
			}
			if (stristr($tmp_data['flags'],'\Draft'))
			{
				//  'X' if draft, ' ' if not draft
				$info->Draft = 'X';
			}
			// --- Additional Stuff  ---
			// "Msgno" message sequence number, NOT the UID
			// php-imap provides this by forcing you to use msgnum for this function
			// thus you need to do extra query to go from UID to msgnum before calling this function in php-imap
			// HOWEVER in this sockets mode we ONLY use UID and it is wasteful that php-imap does the above simply for this data
			// so, we will put uid here, this MIGHT break php-imap rules but anglemail cares not about this item
			$info->Msgno = $tmp_data['uid'];
			// imap INTERNALDATE it looks like this: 17-Sep-2003 01:41:43 -0400
			$info->MailDate = $tmp_data['internaldate'];
			// IMAP "rfc822.size" data
			$info->Size = $tmp_data['rfc822.size'];
			
			// udate moved below
			
			// ALWAYS present even if not filled, and not filled for imap
			$info->fetchfrom = '';
			// ALWAYS present even if not filled, and not filled for imap
			$info->fetchsubject = '';
			// unset for imap
			unset($info->lines);
		
			// THIS is what we are here to collect if we can...
			// put envelope into class var for manipulation
			$this->env_rawstr = $tmp_data['envelope'];
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): $this->env_rawstr is: ['.$this->env_rawstr ."]\n"; }
			
			// ENVELOPE has 10 peices if information in this order
			// date, subject, from, sender, reply-to, to, cc, bcc, in-reply-to, message-id
			
			// 1. date - string
			// message Date header, should be like this: Tue, 16 Sep 2003 15:28:37 -0400
			// function extract_header_item($data_type,$next_item_type,$if_nothing)
			$info->date = $this->extract_header_item('string', 'string', '""');
			$info->Date = $info->date;
			// UDATE mail message date in unix time, example 1076645800
			$info->udate = $this->make_udate($info->Date);
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): got $info->date is: ['.$info->date."]\n\n"; }
			
			// 2. subject - string
			$info->subject = $this->extract_header_item('string', 'paren_list', '""');
			// if subject line is not in headers we get nil here
			if ($info->subject)
			{
				$info->Subject = $info->subject;
			}
			else
			{
				unset($info->subject);
				unset($info->Subject);
			}
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): got $info->subject is: ['.$info->subject."]\n\n"; }
			
			// 3. from
			// $from, $to, etc. arrays are numbered arrays of object O->personal  O->mailbox  O->host
			$info->from = $this->extract_header_item('paren_list', 'paren_list', 'NIL');
			if ($info->from)
			{
				// fromaddress string is either A. personal if available, or B. mailbox@host
				$info->fromaddress = $this->make_xxaddress_str($info->from);
			}
			else
			{
				unset($info->from);
				unset($info->fromaddress);
			}
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): got $info->from is: ['.serialize($info->from)."]\n\n"; }
			
			// 4. sender
			$info->sender = $this->extract_header_item('paren_list', 'paren_list', 'NIL');
			if ($info->sender)
			{
				$info->senderaddress = $this->make_xxaddress_str($info->sender);
			}
			else
			{
				unset($info->sender);
				unset($info->senderaddress);
			}
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): got $info->sender is: ['.serialize($info->sender)."]\n\n"; }
			
			// 5. reply-to
			$info->reply_to = $this->extract_header_item('paren_list', 'paren_list', 'NIL');
			if ($info->reply_to)
			{
				$info->reply_toaddress = $this->make_xxaddress_str($info->reply_to);
			}
			else
			{
				unset($info->reply_to);
				unset($info->reply_toaddress);
			}
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): got $info->reply_to is: ['.serialize($info->reply_to)."]\n\n"; }

			// 6. to
			$info->to = $this->extract_header_item('paren_list', 'paren_list', 'NIL');
			if ($info->to)
			{
				$info->toaddress = $this->make_xxaddress_str($info->to);
			}
			else
			{
				unset($info->to);
				unset($info->toaddress);
			}
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): got $info->to is: ['.serialize($info->to)."]\n\n"; }
			
			// 7. cc
			$info->cc = $this->extract_header_item('paren_list', 'paren_list', 'NIL');
			if ($info->cc)
			{
				$info->ccaddress = $this->make_xxaddress_str($info->cc);
			}
			else
			{
				unset($info->cc);
				unset($info->ccaddress);
			}
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): got $info->cc is: ['.serialize($info->cc)."]\n\n"; }
			
			// 8. bcc
			$info->bcc = $this->extract_header_item('paren_list', 'string', 'NIL');
			if ($info->bcc)
			{
				$info->bccaddress = $this->make_xxaddress_str($info->bcc);
			}
			else
			{
				unset($info->bcc);
				unset($info->bccaddress);
			}
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): got $info->bcc is: ['.serialize($info->bcc)."]\n\n"; }
			
			// 9. in-reply-to - string or NIL
			// unset if not available in imap ENVELOPE
			$info->in_reply_to = $this->extract_header_item('string', 'string', 'NIL');
			if (!$info->in_reply_to)
			{
				unset($info->in_reply_to);
			}
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): got $info->in_reply_to is: ['.$info->in_reply_to."]\n\n"; }
			
			// 10. message-id - string or ""
			// because in real life it is ALWAYS set even if not filled (aka "")
			$info->message_id = $this->extract_header_item('string', 'eol', '""');
			if ($this->debug_dcom > 1) { echo 'imap_parse_header('.__LINE__.'): got $info->message_id is: ['.$info->message_id."]\n\n"; }

			// *** EXTRA DATA *** php-imap might return
			// unset for imap
			unset($info->newsgroups);
			// unset if not filled - NOT available in imap ENVELOPE data
			unset($info->followup_to);
			// unset if not filled - NOT available in imap ENVELOPE data
			unset($info->references);
			
			if ($this->debug_dcom > 0) { echo 'imap_parse_header('.__LINE__.'): LEAVING returning $info <br>'.'</pre>'."\n"; }
			return $info;

		}


		
		
		/*!
		@function header
		@abstract not yet implemented in IMAP sockets module
		@discussion implements imap_header AND USES imap_msgno
		*/
		function header($stream_notused,$msg_num,$fromlength="",$tolength="",$defaulthost="")
		{
			//if ($this->debug_dcom > 0) { echo 'imap: header NOT YET IMPLEMENTED imap sockets function<br>'; }
			//return False;
			if ($this->debug_dcom > 0) { echo 'imap: header('.__LINE__.'): ENTERING <br>'; }
			
			if ((isset($this->envelope_struct->Msgno))
			&& ((int)$this->envelope_struct->Msgno == (int)$msg_num))
			{
				// return assembled data
				if ($this->debug_dcom > 0) { echo 'imap: header('.__LINE__.'): LEAVING - data ALREADY collected, returning requested data <br>'; }
				return $this->envelope_struct;
			}
			
			// SHELL FUNCTION
			// calls sub function
			$this->fetch_request_common($stream_notused,$msg_num,$flags);
			
			// return assembled data
			if ($this->debug_dcom > 0) { echo 'imap: header('.__LINE__.'): LEAVING returning requested data <br>'; }
			return $this->envelope_struct;
		}
		
		
		/**************************************************************************\
		*	More Data Communications (dcom) With IMAP Server
		\**************************************************************************/
	
		
		/**************************************************************************\
		*	Get Message Headers From Server
		\**************************************************************************/
		/*!
		@function fetchheader
		@abstract implements IMAP_FETCHHEADER
		@discussion gets raw message headers via PEEK so the seen flag is left unchanged.
		*/
		function fetchheader($stream_notused,$msg_num,$flags=0)
		{
			// NEEDED: code for flags: FT_UID; FT_INTERNAL; FT_PREFETCHTEXT
			//if ($this->debug_dcom > 0) { echo 'imap: fetchheader NOT YET IMPLEMENTED imap sockets function<br>'; }
			//return False;
			if ($this->debug_dcom > 0) { echo 'imap: fetchheader('.__LINE__.'): ENTERING+LEAVING by returning $this->fetchbody() just for headers <br>'; }
			return $this->fetchbody($stream_notused,$msg_num,'HEADER',$flags, 'PEEK');
		}
		
		
		/**************************************************************************\
		*	Get Message Body (Parts) From Server
		\**************************************************************************/
		/*!
		@function fetchbody
		@abstract not yet implemented in IMAP sockets module
		@discussion implements imap_fetchbody
		*/
		function fetchbody($stream_notused,$msg_num,$part_num="",$flags=0, $just_peek='')
		{
			//if ($this->debug_dcom > 0) { echo 'imap: fetchbody  NOT YET IMPLEMENTED imap sockets function<br>'; }
			//return False;
			// c->s:	00000006 FETCH 97 BODY[1]
			// s->c:	* 97 FETCH (BODY[1] {1230}..(snip)
			
			if ($this->debug_dcom > 0) { echo 'imap_sock.fetchbody('.__LINE__.'): ENTERING fetchbody, $msg_num ['.$msg_num.'] , $part_num ['.$part_num.']<br>'; }
			
			// do we force use of msg UID's 
			if ( ($this->force_msg_uids == True)
			&& (!($flags & SE_UID)) )
			{
				$flags |= SE_UID;
			}
			// only SE_UID is supported right now, no flag is not supported because we only use the "UID" command right now
			if ($this->debug_dcom > 1) { echo 'imap_sock.fetchbody('.__LINE__.'): param $flags ['.htmlspecialchars(serialize($flags)).'], ($flags & SE_UID) is ['.htmlspecialchars(serialize(($flags & SE_UID))).'] <br>'; }
			if ($flags & SE_UID)
			{
				$using_uid = True;
			}
			else
			{
				echo 'imap_sock.fetchbody('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>';
				if ($this->debug_dcom > 0) { echo 'imap_sock.fetchbody('.__LINE__.'): LEAVING on ERROR, flag SE_UID is not present, nothing else coded for yet <br>'; }
				return False;
			}
			if ($this->debug_dcom > 1) { echo 'imap_sock.fetchbody('.__LINE__.'): $flags ['.htmlspecialchars(serialize($flags)).'], $using_uid ['.htmlspecialchars(serialize($using_uid)).'] only SE_UID coded for, so continuing...<br>'; }
			
			// do some RFC3501 formatting (lame, needs more work)
			if ((string)$part_num == '0')
			{
				$part_num = 'HEADER';
			}
			
			// assemble the server querey, looks like this:  
			// 00000006 UID FETCH 131 BODY[1]
			// OR if only peeking at the header
			// 00000003 UID FETCH 6  BODY.PEEK[HEADER]
			//$cmd_tag = 's008';
			$cmd_tag = $this->get_next_cmd_num();
			if (($part_num == 'HEADER')
			|| ($just_peek))
			{
				$full_command = $cmd_tag.' UID FETCH '.$msg_num.' BODY.PEEK['.$part_num.']';
			}
			else
			{
				$full_command = $cmd_tag.' UID FETCH '.$msg_num.' BODY['.$part_num.']';
			}
			$expecting = $cmd_tag; // may be followed by OK, NO, or BAD
			
			if ($this->debug_dcom > 1) { echo 'imap_sock.fetchbody('.__LINE__.'): write_port: "'. htmlspecialchars($full_command) .'"<br>'; }
			if ($this->debug_dcom > 1) { echo 'imap_sock.fetchbody('.__LINE__.'): expecting: "'. htmlspecialchars($expecting) .'" followed by OK, NO, or BAD<br>'; }
			
			if(!$this->write_port($full_command))
			{
				if ($this->debug_dcom > 0) { echo 'imap_sock.fetchbody('.__LINE__.'): LEAVING with error: could not write_port<br>'; }
				$this->error();
				return False;
			}
			
			// read the server data
			$response_array = array();
			// for some reason I get back an array with a single element, item $raw_response[0] which is the string I want to work with
			$response_array = $this->imap_read_port($expecting);
			//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): $response_array DUMP: <pre>'; print_r($response_array); echo '</pre>';  }
			
			// prepare the return data
			// we know that first line will be like this
			// * 97 FETCH (UID 131 BODY[1] {1230}
			// BUT it is possible last elements are continuation data like this
			//  * 97 FETCH (FLAGS (\Seen))
			// which the server may feed to us before the command completion string
			
			//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): prep-FIRST eliminate trailing continuation data <br>'; }
			// 1. pop off any trailing continuation data
			// grab up to " FETCH (" inclusive
			$end = strpos($response_array[0], ' FETCH (');
			if ($end > 0)
			{
				$end = $end+8;
				$continue_str = substr($response_array[0], 0, $end);
				// it should be like this [* 97 FETCH (]
				//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): $continue_str ['.$continue_str.'] <br>'; }
				// loop 3 times max, just an arbitrary number, server ashould not feed too much of this
				$loops = 3;
				$lines_pos = count($response_array)-1;
				for ($i=0; $i < $loops ;$i++)
				{
					// look backwards 3 times for any continuation data
					$this_pos = $lines_pos - $i;
					//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): $this_pos is ['.$this_pos.'], $response_array[$this_pos] is ['.htmlspecialchars($response_array[$this_pos]).']<br>'; }
					//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): strstr($response_array[$this_pos], $continue_str) which is ['.htmlspecialchars(serialize(strstr($response_array[$this_pos], $continue_str))).']<br>'; }
					if (strstr($response_array[$this_pos], $continue_str))
					{
						// pop off the last element which is continuation data
						//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): pop off continuation data at $response_array['.$this_pos.'] which is ['.$response_array[$this_pos].']<br>'; }
						array_pop($response_array);
					}
					else
					{
						// continuation data must be contiguous,
						// so if none is here, none can be before this
						break;
					}
				}
				//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): after continuation data strip, $response_array DUMP: <pre>'; print_r($response_array); echo '</pre>';  }
			}
			
			//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): prep-SECOND shift off element 0 from the array <br>'; }
			// 2. pop off the element [0] because it looks like this:
			// * 97 FETCH (UID 131 BODY[1] {1230}
			array_shift($response_array);
			
			//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): prep-THIRD pop off the closing paren <br>'; }
			// 3. pop off the last element which is only the close paren to the open paren we just eliminated above
			// usually last element is bare ")"
			$lines_pos = count($response_array)-1;
			if ($response_array[$lines_pos] == ')'."\r\n")
			{
				//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): DONE, simple prep-THIRD pop off the closing paren <br>'; }
				array_pop($response_array);
			}
			else
			{
				// it *may* be possible the server put the close paren at the end of the last line
				$this_line = $response_array[$lines_pos];
				if (strlen($this_line) > 3)
				{
					// flip it backwards for easier comparison
					$this_line = strrev($this_line);
					if (($this_line{0} == "\n")
					&& ($this_line{1} == "\r")
					&& ($this_line{2} == ")"))
					{
						// remove the last 3 chars of this string
						$response_array[$lines_pos] = substr($response_array[$lines_pos], 0, -3);
						// replace the final CRLF
						$response_array[$lines_pos] .= "\r\n";
						//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): DONE, prep-THIRD, used complicated method of removing the paren fron within the last line of data <br>'; }
					}
				}
			}
			//if ($this->debug_dcom > 3) { echo 'imap_sock.fetchbody('.__LINE__.'): FINAL prepped, after array_shift and array_pop, $response_array DUMP: <pre>'; print_r($response_array); echo '</pre>';  }
			
			// 4. each element is one line, implode them, they already have CRLF
			// AND RETURN IT
			if ($this->debug_dcom > 0) { echo 'imap_sock.fetchbody('.__LINE__.'): LEAVING fetchbody, imploding then returning a string<br>'; }
			return implode("", $response_array);
			
			//if ($this->debug_dcom > 0) { echo 'imap_sock.fetchbody('.__LINE__.'): LEAVING fetchbody <br>'; }
			// FOR DEBUGGING
			//return array();
		}
		
		/*!
		@function get_body
		@abstract implements IMAP_BODY
		@discussion mostly used only for raw message viewing. Passes thru to fetchbody with TEXT as part num.
		@author Angles
		*/
		function get_body($stream_notused,$msg_num,$flags=0)
		{
			if ($this->debug_dcom > 0) { echo 'imap: get_body('.__LINE__.'): ENTERING+LEAVING by returning $this->fetchbody() just for headers <br>'; }
			return $this->fetchbody($stream_notused,$msg_num,'TEXT',$flags);
		}
	}

?>

<?php
  /**************************************************************************\
  * eGroupWare API - MAIL										*
  * This file written by Mark Peters <skeeter@phpgroupware.org>			*
  * and Angelo "Angles" Puglisi <angles@aminvestments.com>				*
  * Handles general functionality for mail/mail structures					*
  * Copyright (C) 2001 Mark Peters									*
  * Copyright (C) 2001- 2004 Angelo "Angles" Puglisi						*
  * -------------------------------------------------------------------------			*
  * This library is part of the eGroupWare API                             *
  * http://www.egroupware.org/api                                          * 
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

	
	/*!
	@class mail_dcom_base (sockets)
	@abstract part of mail Data Communications class
	@discussion mail_dcom_base Extends phpgw api class network
	After mail_dcom_base is loaded, a top level class mail_dcom is created 
	specifically for the necessary propocol, either POP3, IMAP, or NNTP
	@syntax CreateObject('email.mail_dcom');
	@author Angles, Skeeter, Itzchak Rehberg, Joseph Engo
	@copyright LGPL
	@package email (to be moved to phpgwapi when mature)
	@access public
	*/
	class mail_dcom_base extends network
	{
		// OBSOLETED "folder_list_changed" by the newer function "folder_list_did_change"
		var $folder_list_changed=False;
		// Cached Data
		// raw message data from the server, some raw data, some exploded into a string list
		var $header_glob = '';
		var $header_glob_msgnum = '';
		var $header_array = array();
		var $header_array_msgnum = '';
		//var $body_glob = '';
		//var $body_glob_msgnum = '';
		var $body_array = array();
		var $body_array_msgnum = '';	
		// structural information from our processing functions
		//var $mailbox_msg_info = '';  // caching this with POP3 is OK but will cause HAVOC with IMAP or NNTP
		
		// USED in IMAP SOCKETS class to cache an internally needed item, the total number messages in a folder NEEDED by sort
		// so sort does not have to call status again for data already recieved.
		var $mailbox_status='';
		
		var $msg_structure = '';
		var $msg_structure_msgnum = '';
		var $hdr_info_envelope;
		// server error strings *should* get stored here
		var $server_last_error_str = '';
		// server data returned to the calling function does NOT include the final OK line
		// because this line is not actually data, so put this line here for inspection if necessary
		var $server_last_ok_response = '';
		
		// future use
		var $refto_msg_parent;
		var $mailbox;
		var $numparts;
		var $sparts;
		var $hsub=array();
		var $bsub=array();
		var $folder = '';
		
		var $imap_builtin=False;
		var $force_msg_uids = False;
		
		// DEBUG FLAG: Debug Levels are 0=none, 1=basic, 2=detailed, 3=more detailed, 4=maybe even more
		var $debug_dcom=0;
		
		function mail_dcom_base()
		{
			$this->errorset = 0;
			$this->network(True);
			if (isset($GLOBALS['phpgw_info']))
			{
				$this->tempfile = $GLOBALS['phpgw_info']['server']['temp_dir'].SEP.$GLOBALS['phpgw_info']['user']['sessionid'].'.mhd';
				$this->att_files_dir = $GLOBALS['phpgw_info']['server']['temp_dir'].SEP.$GLOBALS['phpgw_info']['user']['sessionid'];
			}
			else
			{
				// NEED GENERIC DEFAULT VALUES HERE				
			}
		}
		
		/*!
		@function error
		@abstract none
		*/
		function error()
		{
			echo 'Error: '.$this->error['code'].' : '.$this->error['msg'].' - '.$this->error['desc']."<br>\r\n";
			$this->close();
			echo('<A HREF="'.$GLOBALS['phpgw']->link('/home.php').'">'.lang('Click here to continue').'...</A>'); //cbsman
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		
		/*!
		@function folder_list_did_change
		@abstract if folder is created, deleted, or renamed this this function handles cleanup of stale data in main msg object. 
		@author Angles
		@discussion THIS IS THE EXACT SAME FUNCTION AS IN DCOM_BASE non-sockets class.
		*/
		function folder_list_did_change()
		{
			// NOTE THIS FLAG "folder_list_changed" IS NOW OBSOLETED SINCE THIS 
			// CALLBACK FUNCTION IS PROVEN TO WORK
			$this->folder_list_changed = True;
			if (is_object($GLOBALS['phpgw']->msg))
			{
				// call that classes "callback" function designed to handle cleaning stale folder_list there
				$sucess = $GLOBALS['phpgw']->msg->folder_list_change_callback();
				// if it was handled correctly, then reset the "folder_list_changed" because we did our job
				if ($sucess)
				{
					$this->folder_list_changed = False;
				}
			}
		}
		
		/*!
		@function get_flag
		@abstract REDUNDANT FUNCTION FROM NON-SOCK CLASS
		*/
		function get_flag($stream,$msg_num,$flag)
		{
			$header = $this->fetchheader($stream,$msg_num);
			$flag = strtolower($flag);
			for ($i=0;$i<count($header);$i++)
			{
				$pos = strpos($header[$i],':');
				if (is_int($pos) && $pos)
				{
					$keyword = trim(substr($header[$i],0,$pos));
					$content = trim(substr($header[$i],$pos+1));
					if (strtolower($keyword) == $flag)
					{
						return $content;
					}
				}
			}
			return false;
		}
		
		/*!
		@function distill_fq_folder
		@abstract break down a php fully qualified folder name into its seperate barts
		@param $fq_folder : string : {SERVER_NAME:PORT/OPTIONS}FOLDERNAME
		@result array structure as shown in the syntax
		@syntax this is the array return struture
		result['folder'] : string is the folder (a.k.a. mailbox) name WITHOUT the bracketed {server:port/options}
		result['svr_and_port'] string : for Internal Use
		result['server'] : string is the IP or NAME of the server
		result['port_with_junk'] string : for Internal Use
		result['port'] string is the port number
		@discussion $fq_folder name arrives as:
		{SERVER_NAME:PORT}FOLDERNAME
		OR some variation like this:
		{SERVER_NAME:PORT/pop3}FOLDERNAME
		{SERVER_NAME:PORT/imap/ssl/novalidate-cert}FOLDERNAME
		this is how php passes around this data in its builtin IMAP extensions
		this function breaks down that string into it's parts
		@author Angles
		@access private
		*/
		function distill_fq_folder($fq_folder)
		{
			// initialize return structure array
			$svr_data = Array();
			$svr_data['folder'] = '';
			$svr_data['svr_and_port'] = '';
			$svr_data['server'] = '';
			$svr_data['port_with_junk'] = '';
			$svr_data['port'] = '';
			
			// see if we have any data to work with
			if ((!isset($fq_folder))
			|| ((trim($fq_folder) == '')))
			{
				// no data, return the reliable default of INBOX
				$svr_data['folder'] = 'INBOX';
				return $svr_data;
				// we're out'a here
			}
			
			// see if this is indeed a fully qualified folder name
			if (strstr($fq_folder,'}') == False)
			{
				// all we have is a _simple_ folder name, no server or port info included
				$svr_data['folder'] = $fq_folder;
				return $svr_data;
				// we're out'a here
			}
			
			// -- (1) -- get the folder name stripped of the server string
			// folder name at this stage is  {SERVER_NAME:PORT}FOLDERNAME
			// ORsome variation like this:
			// {SERVER_NAME:PORT/pop3}FOLDERNAME
			// {SERVER_NAME:PORT/imap/ssl/novalidate-cert}FOLDERNAME
			// get everything to the right of the bracket "}", INCLUDES the bracket itself
			$svr_data['folder'] = strstr($fq_folder,'}');
			// get rid of that 'needle' "}"
			$svr_data['folder'] = substr($svr_data['folder'], 1);
			// -- (2) -- get the {SERVER_NAME:PORT} part and strip the brackets
			$svr_callstr_len = strlen($fq_folder) - strlen($svr_data['folder']);
			// start copying at position 1 skipping the opening bracket
			// and stop copying at length of {SERVER_NAME:PORT} - 2 to skip the closing beacket
			$svr_data['svr_and_port'] = substr($fq_folder, 1, $svr_callstr_len - 2);
			// -- (3)-- get the port number INCLUDING any junk that may come after it, like "/pop3/ssl/novalidate-cert"
			// "svr_and_port" at this stage is  SERVER_NAME:PORT , or SERVER_NAME:PORT/pop3  , etc...
			// get everything to the right of the colon ":", INCLUDES the colon itself
			$svr_data['port_with_junk'] = strstr($svr_data['svr_and_port'],':');
			// get rid of that 'needle' ":"
			$svr_data['port_with_junk'] = substr($svr_data['port_with_junk'], 1);
			// -- (4)-- get the server name 
			// port_with_junk + 1 means the port number with the added 1 char length of the colon we got rid of just above
			$svr_only_len = strlen($svr_data['svr_and_port']) - strlen($svr_data['port_with_junk']);
			// $svr_only_len - 1 means leave out the 1 char length of the colon we stripped deom "port_with_junk" above
			$svr_data['server'] = substr($svr_data['svr_and_port'], 0, $svr_only_len - 1);
			// -- (5)-- get the port number , stripping any junk that _may_ be with it
			//  get everything to the right of the forst slash "/", INCLUDES the slash itself, else returns FALSE
			$port_junk = strstr($svr_data['port_with_junk'],'/');
			// test
			//$svr_data['port'] = $port_junk;
			if ($port_junk)
			{
				$port_only_len = strlen($svr_data['port_with_junk']) - strlen($port_junk);
				$svr_data['port'] = substr($svr_data['port_with_junk'], 0, $port_only_len);
			}
			else
			{
				$svr_data['port'] = trim($svr_data['port_with_junk']);
			}
			return $svr_data;
		}
		
		/*!
		@function read_port_glob
		@abstract used with POP3, reads data from port until we encounted param $end
		@param $end (string) is the flag to look for that tells us when to stop reading the port's data
		@result raw string of data (glob) as received from the server
		@discussion POP3 servers data typically ends with special charactor(s),
		usually an empty line, i.e. a lone CRLF pair, or line that is a period "." followed br a CRLF pair
		thus we can direct this function to read the server's data until such special end flag is reached
		@author Angles, skeeter
		@access private
		*/
		function read_port_glob($end='.')
		{
			$glob_response = '';
			while ($line = $this->read_port())
			{
				//echo $line."<br>\r\n";
				if (chop($line) == $end)
				{
					break;
				}
				$glob_response .= $line;
			}
			return $glob_response;
		}
		
		/*!
		@function glob_to_array
		@abstract used with POP3, converts raw string server data into an array
		@param $data string
		@param $keep_blank_lines boolean
		@param $cut_from_here string
		@param $keep_received_lines  boolean
		@param $idx_offset (integer) where to start the returned array at (if not zero)
		@result  array
		@discussion ?
		@author Angles
		@access	private
		*/
		function glob_to_array($data,$keep_blank_lines=True,$cut_from_here='',$keep_received_lines=True,$idx_offset=0)
		{
			$data_array = explode("\r\n",$data);
			$return_array = Array();
			for($i=0;$i < count($data_array);$i++)
			{
				$new_str = $data_array[$i];
				if ($cut_from_here != '')
				{
					$cut_here = strpos($new_str,$cut_from_here);
					if ($cut_here > 0)
					{
						$new_str = substr($new_str,0,$cut_here);
					}
					else
					{
						$new_str = '';
					}
				}
				if (($keep_blank_lines == False)
				&& (trim($new_str) == ''))
				{
					// do noting
				}
				elseif (($keep_received_lines == False)
				&& (stristr($new_str, 'received:'))
				&& (strpos(strtolower($new_str),'received:') == 0))
				{
					// do noting
				}			
				else
				{
					$new_idx = count($return_array) + $idx_offset;
					$return_array[$new_idx] = $new_str;
				}
			}
			return $return_array;
		}
		
		/*!
		@function show_crlf
		@abstract replace actual CRLF sequence with the string "CRLF"
		@param $data  (string)
		@result returns string "\r\n" CRFL pairs replaced with the string "CRLF"
		@discussion useful for debugging, CRLF pairs are CarrageReturn + LineFeed
		which is the standard way email client and servers end any line while communicating
		@author Angles
		@access public or private
		*/
		function show_crlf($data='')
		{
			return str_replace("\r\n", 'CRLF', $data);
		}
		
		/*!
		@function create_header
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function create_header($line,$header,$line2='')
		{
			$thead = explode(':',$line);
			$key = trim($thead[0]);
			switch(count($thead))
			{
				case 1:
					$value = TRUE;
					break;
				case 2:
					$value = trim($thead[1]);
					break;
				default: 
					$thead[0] = '';
					$value = '';
					for($i=1,$j=count($thead);$i<$j;$i++)
					{
						$value .= $thead[$i].':';
					}
					//$value = trim($value.$thead[$j++]);
					//$value = trim($value);
					break;
			}
			$header[$key] = $value;
			if (ereg('^multipart/mixed;',$value))
			{
				if (! ereg('boundary',$header[$key]))
				{
					if ($line2 == 'True')
					{
						$line2 = $this->read_port();
						echo 'Response = '.$line2.'<br>'."\n";
					}
				}
				$header[$key] .= chop($line2);
			}
			//echo 'Header[$key] = '.$header[$key].'<br>'."\n";
		}
	
		/*!
		@function build_address_structure
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function build_address_structure($key)
		{
			$address = array(new address);
			// Build Address to Structure
			$temp_array = explode(';',$this->header[$key]);
			for ($i=0;$i<count($temp_array);$i++)
			{
				$this->decode_author($temp_array[$i],&$email,&$name);
				$temp = explode('@',$email);
				$address[$i]->personal = $this->decode_header($name);
				$address[$i]->mailbox = $temp[0];
				if (count($temp) == 2)
				{
					$address[$i]->host = $temp[1];
					$address[$i]->adl = $email;
				}
				return $address;
			}
		}
		
		/*!
		@function convert_date_array
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function convert_date_array($field_list)
		{
			$new_list = Array();
			while(list($key,$value) = each($field_list))
			{
				//$new_list[$key] = $this->convert_date($value);
				$new_list[$key] = $this->make_udate($value);
				if ($this->debug_dcom >= 2) { echo 'base_sock: convert_date_array: field_list: "'.$new_list[$key].'" was "'.$value.'"<br>'; }
	
			}
			return $new_list;
		}
		
		/*!
		@function convert_date
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function convert_date($msg_date)
		{
			$dta = array();
			$ta = array();
			
			// Convert "Sat, 15 Jul 2000 20:50:22 +0200" to unixtime
			$comma = strpos($msg_date,',');
			if($comma)
			{
				$msg_date = substr($msg_date,$comma + 2);
			}
			//echo 'Msg Date : '.$msg_date."<br>\n";
			$dta = explode(' ',$msg_date);
			$ta = explode(':',$dta[3]);
			
			if(substr($dta[4],0,3) <> 'GMT')
			{
				$tzoffset = substr($dta[4],0,1);
				(int)$tzhours = substr($dta[4],1,2);
				(int)$tzmins = substr($dta[4],3,2);
				switch ($tzoffset)
				{
					case '+': 
						(int)$ta[0] -= (int)$tzhours;
						(int)$ta[1] -= (int)$tzmins;
						break;
					case '-':
						(int)$ta[0] += (int)$tzhours;
						(int)$ta[1] += (int)$tzmins;
						break;
				}
			}
			
			$new_time = mktime($ta[0],$ta[1],$ta[2],$GLOBALS['month_array'][strtolower($dta[1])],$dta[0],$dta[2]) - ((60 * 60) * intval($GLOBALS['phpgw_info']['user']['preferences']['common']['tzoffset']));
			//echo 'New Time : '.$new_time."<br>\n";
			return $new_time;
		}
		
		/*!
		@function make_udate
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function make_udate($msg_date)
		{
			$pos = strpos($msg_date,',');
			if ($pos)
			{
				$msg_date = trim(substr($msg_date,$pos+1));
			}
			$pos = strpos($msg_date,' ');
			$day = substr($msg_date,0,$pos);
			$msg_date = trim(substr($msg_date,$pos));
			$month = $GLOBALS['month_array'][strtolower(substr($msg_date,0,3))];
			$msg_date = trim(substr($msg_date,3));
			$pos  = strpos($msg_date,' ');
			$year = trim(substr($msg_date,0,$pos));
			$msg_date = trim(substr($msg_date,$pos));
			$hour = substr($msg_date,0,2);
			$minute = substr($msg_date,3,2);
			$second = substr($msg_date,6,2);
			$pos = strrpos($msg_date,' ');
			$tzoff = trim(substr($msg_date,$pos));
			if (strlen($tzoff)==5)
			{
				$diffh = substr($tzoff,1,2); $diffm = substr($tzoff,3);
				if ((substr($tzoff,0,1)=='+') && is_int($diffh))
				{
					$hour -= $diffh; $minute -= $diffm;
				}
				else
				{
					$hour += $diffh; $minute += $diffm;
				}
			}
			$utime = mktime($hour,$minute,$second,$month,$day,$year);
			return $utime;
		}
		
		/*!
		@function ssort_prep
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function ssort_prep($a)
		{
			$a = strtoupper($a);
			if(strpos(' '.$a,'FW: ') == 1 || strpos(' '.$a,'RE: ') == 1)
			{
				$a_mod = substr($a,4);
			}
			elseif(strpos(' '.$a,'FWD: ') == 1)
			{
				$a_mod = substr($a,5);
			}
			else
			{
				$a_mod = $a;
			}
			
			while(substr($a_mod,0,1) == ' ')
			{
				$a_mod = substr($a_mod,1);
			}
	
			//if(strpos(' '.$a_mod,'[') == 1)
			//{
			//	$a_mod = substr($a_mod,1);
			//}
			return $a_mod;
		}
		
		/*!
		@function ssort_ascending
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function ssort_ascending($a,$b)
		{
			$a_mod = $this->ssort_prep($a);
			$b_mod = $this->ssort_prep($b);
			if ($a_mod == $b_mod)
			{
				return 0;
			}
			return ($a_mod < $b_mod) ? -1 : 1;
		}
		
		/*!
		@function ssort_decending
		@abstract 
		*/
		function ssort_decending($a,$b)
		{
			$a_mod = $this->ssort_prep($a);
			$b_mod = $this->ssort_prep($b);
			if ($a_mod == $b_mod)
			{
				return 0;
			}
			return ($a_mod > $b_mod) ? -1 : 1;
		}
		
		/*!
		@function mail_header
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function mail_header($msgnum)
		{
			$this->msg = new msg;
			// This needs to be pulled back to the actual read header of the mailer type.
			//$this->mail_fetch_overview($msgnum);
			
			// From:
			$this->msg->from = array(new address);
			$this->msg->from = $this->build_address_structure('From');
			$this->msg->fromaddress = $this->header['From'];
			
			// To:
			$this->msg->to = array(new address);
			if (strtolower($this->type) == 'nntp')
			{
				$temp = explode(',',$this->header['Newsgroups']);
				$to = array(new address);
				for($i=0;$i<count($temp);$i++)
				{
					$to[$i]->mailbox = '';
					$to[$i]->host = '';
					$to[$i]->personal = $temp[$i];
					$to[$i]->adl = $temp[$i];
				}
				$this->msg->to = $to;
			}
			else
			{
				$this->msg->to = $this->build_address_structure('To');
				$this->msg->toaddress = $this->header['To'];
			}
	
			// Cc:
			$this->msg->cc = array(new address);
			if(isset($this->header['Cc']))
			{
				$this->msg->cc[] = $this->build_address_structure('Cc');
				$this->msg->ccaddress = $this->header['Cc'];
			}
	    
			// Bcc:
			$this->msg->bcc = array(new address);
			if(isset($this->header['bcc']))
			{
				$this->msg->bcc = $this->build_address_structure('bcc');
				$this->msg->bccaddress = $this->header['bcc'];
			}
	
			// Reply-To:
			$this->msg->reply_to = array(new address);
			if(isset($this->header['Reply-To']))
			{
				$this->msg->reply_to = $this->build_address_structure('Reply-To');
				$this->msg->reply_toaddress = $this->header['Reply-To'];
			}
	
			// Sender:
			$this->msg->sender = array(new address);
			if(isset($this->header['Sender']))
			{
				$this->msg->sender = $this->build_address_structure('Sender');
				$this->msg->senderaddress = $this->header['Sender'];
			}
	
			// Return-Path:
			$this->msg->return_path = array(new address);
			if(isset($this->header['Return-Path']))
			{
				$this->msg->return_path = $this->build_address_structure('Return-Path');
				$this->msg->return_pathaddress = $this->header['Return-Path'];
			}
	
			// UDate
			$this->msg->udate = $this->convert_date($this->header['Date']);
	
			// Subject
			$this->msg->subject = $this->phpGW_quoted_printable_decode($this->header['Subject']);
	
			// Lines
			// This represents the number of lines contained in the body
			$this->msg->lines = $this->header['Lines'];
		}
	
		/*!
		@function mail_headerinfo
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function mail_headerinfo($msgnum)
		{
			$this->mail_header($msgnum);
		}

		/*!
		@function read_and_load
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function read_and_load($end)
		{
			$this->header = Array();
			while ($line = $this->read_port())
			{
				//echo $line."<br>\n";
				if (chop($line) == $end) break;
				$this->create_header($line,&$this->header,"True");
			}
			return 1;
		}

		/*!
		@function phpGW_quoted_printable_decode
		@abstract NOT USED
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		@param $string string
		@discussion Not currently used. Unknown why this additional processing was required.
		Author noted this - PHP "quoted_printable_decode" function does not work properly, 
		it should convert "_" characters into " ".
		*/
		function phpGW_quoted_printable_decode($string)
		{
			$string = str_replace('_', ' ', $string);
			return quoted_printable_decode($string);
		}
		
		/*!
		@function phpGW_quoted_printable_decode2
		@abstract NOT USED Remove "=" at the end of the lines. "quoted_printable_decode" doesn't do it.
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		@param $string (string)
		@discussion Not currently used. Unknown why this additional processing was required.
		*/
		function phpGW_quoted_printable_decode2($string)
		{
			$string = $this->phpGW_quoted_printable_decode($string);
			return preg_replace("/\=\n/", '', $string);
		}
		
		/*!
		@function decode_base64
		@abstract NOT USED base64 decoding with additional string manipulations.
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		@param $string (string)
		@discussion Not currently used. Unknown why this additional processing was required. 
		Uses ereg_replace and preg_replace in addition to php function base64_decode. 
		It is possible the preg_replace is used to pull only the base64 encoded text from a larger 
		message.
		*/
		function decode_base64($string)
		{
			$string = ereg_replace("'", "\'", $string);
			$string = preg_replace("/\=\?(.*?)\?b\?(.*?)\?\=/ieU",base64_decode("\\2"),$string);
			return $string;
		}
		
		/*!
		@function decode_qp
		@abstract NOT USED
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		@param $string (string)
		@discussion Calls other unused functions in this file. 
		*/
		function decode_qp($string)
		{
			$string = ereg_replace("'", "\'", $string);
			$string = preg_replace("/\=\?(.*?)\?q\?(.*?)\?\=/ieU",$this->phpGW_quoted_printable_decode2("\\2"),$string);
			return $string;
		}
		
		/*!
		@function decode_header
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		@param $string (string)
		@discussion Calls other unused functions in this file. 
		*/
		function decode_header($string)
		{
			/* Decode from qp or base64 form */
			if (preg_match("/\=\?(.*?)\?b\?/i", $string))
			{
				return $this->decode_base64($string);
			}
			if (preg_match("/\=\?(.*?)\?q\?/i", $string))
			{
				return $this->decode_qp($string);
			}
			return $string;
		}

		/*!
		@function decode_author
		@abstract ?
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		@discussion Calls other plossibly unused functions in this file. 
		*/
		function decode_author($author,&$email,&$name)
		{
			/* Decode from qp or base64 form */
			$author = $this->decode_header($author);
			/* Extract real name and e-mail address */
			/* According to RFC1036 the From field can have one of three formats:
				1. Real Name <name@domain.name>
				2. name@domain.name (Real Name)
				3. name@domain.name
			*/
			/* 1st case */
			//if (eregi("(.*) <([-a-z0-9\_\$\+\.]+\@[-a-z0-9\_\.]+[-a-z0-9\_]+)>",
			if (eregi("(.*) <([-a-z0-9_$+.]+@[-a-z0-9_.]+[-a-z0-9_]+)>",$author, $regs))
			{
				$email = $regs[2];
				$name = $regs[1];
			}
			/* 2nd case */
			elseif (eregi("([-a-z0-9_$+.]+@[-a-z0-9_.]+[-a-z0-9_]+) ((.*))",$author, $regs))
			{
			//if (eregi("([-a-z0-9\_\$\+\.]+\@[-a-z0-9\_\.]+[-a-z0-9\_]+) \((.*)\)",$author, $regs))
				$email = $regs[1];
				$name = $regs[2];
			}
			/* 3rd case */
			else
			{
				$email = $author;
			}
			if ($name == '')
			{
				$name = $email;
			}
			$name = eregi_replace("^\"(.*)\"$", "\\1", $name);
			$name = eregi_replace("^\((.*)\)$", "\\1", $name);
		}
		
		// OBSOLETED ?
		/*!
		@function get_mime_type
		@abstract ?
		@author Angles and unknown, maybe Skeeter ? Rehberg ? Engo ?
		@discussion useable but depreciated
		*/
		function get_mime_type($de_part)
		{
			if (!isset($de_part->type))
			{
				return 'unknown';
			}
			else
			{
				return $this->type_int_to_str($de_part->type);
			}
		}

		/*!
		@function type_str_to_int
		@abstract generic conversion function
		@discussion moved here from pop3 class
		@author Angles and others
		*/
		function type_str_to_int($type_str)
		{
			// fallback value
			$type_int = TYPEOTHER;
			// make sure this is lower case
			$type_str = strtolower($type_str);
			switch ($type_str)
			{
				case 'text'		: $type_int = TYPETEXT; break;
				case 'multipart'	: $type_int = TYPEMULTIPART; break;
				case 'message'		: $type_int = TYPEMESSAGE; break;
				case 'application'	: $type_int = TYPEAPPLICATION; break;
				case 'audio'		: $type_int = TYPEAUDIO; break;
				case 'image'		: $type_int = TYPEIMAGE; break;
				case 'video'		: $type_int = TYPEVIDEO; break;
				// this causes errors under php 4.0.6, but used to work before that, I think
				//defaut			: $type_int = (-1); break;
			}
			return $type_int;
		}


		/*!
		@function type_int_to_str
		@abstract 
		@author Angles and unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function type_int_to_str($type_int)
		{
			switch ($type_int)
			{
				case TYPETEXT		: $type_str = 'text'; break;
				case TYPEMULTIPART	: $type_str = 'multipart'; break;
				case TYPEMESSAGE		: $type_str = 'message'; break;
				case TYPEAPPLICATION	: $type_str = 'application'; break;
				case TYPEAUDIO		: $type_str = 'audio'; break;
				case TYPEIMAGE		: $type_str = 'image'; break;
				case TYPEVIDEO		: $type_str = 'video'; break;
				case TYPEOTHER		: $type_str = 'other'; break;
				default			: $type_str = 'unknown';
			}
			return $type_str;
		}
		
		/*!
		@function get_mime_encoding
		@abstract ?
		@author Angles and unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function get_mime_encoding($de_part)
		{
			if (!isset($de_part->encoding))
			{
				return 'other';
			}
			else
			{
				$encoding_str = $this->type_int_to_str($de_part->encoding);
				if ($encoding_str == 'quoted-printable')
				{
					$encoding_str = 'qprint';
				}
				return $encoding_str;
			}
		}
		
		/*!
		@function encoding_str_to_int
		@abstract generic conversion function
		@author angles and others
		*/
		function encoding_str_to_int($encoding_str)
		{
			// make sure this is lower case
			$encoding_str = strtolower($encoding_str);
			switch ($encoding_str)
			{
				case '7bit'		: $encoding_int = ENC7BIT; break;
				case '8bit'		: $encoding_int = ENC8BIT; break;
				case 'binary'		: $encoding_int = ENCBINARY; break;
				case 'base64'		: $encoding_int = ENCBASE64; break;
				case 'quoted-printable' : $encoding_int = ENCQUOTEDPRINTABLE; break;
				case 'other'		: $encoding_int = ENCOTHER; break;
				case 'uu'		: $encoding_int = ENCUU; break;
				default			: $encoding_int = ENCOTHER; break;
			}
			return $encoding_int;
		}

		
		/*!
		@function encoding_int_to_str
		@abstract ?
		@author Angles and unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function encoding_int_to_str($encoding_int)
		{
			switch ($encoding_int)
			{
				case ENC7BIT	: $encoding_str = '7bit'; break;
				case ENC8BIT	: $encoding_str = '8bit'; break;
				case ENCBINARY	: $encoding_str = 'binary';  break;
				case ENCBASE64	: $encoding_str = 'base64'; break;
				case ENCQUOTEDPRINTABLE : $encoding_str = 'quoted-printable'; break;
				case ENCOTHER	: $encoding_str = 'other';  break;
				case ENCUU	: $encoding_str = 'uu';  break;
				default		: $encoding_str = 'other';
			}
			return $encoding_str;
		}
		
		// OBSOLETED
		/*!
		@function get_att_name
		@abstract ?
		@author Angles and unknown, maybe Skeeter ? Rehberg ? Engo ?
		@discussion usable but depreciated
		*/
		function get_att_name($de_part)
		{
			$param = new parameter;
			$att_name = 'Unknown';
			if (!isset($de_part->parameters))
			{
				return $att_name;
			}
			for ($i=0;$i<count($de_part->parameters);$i++)
			{
				$param=(!$de_part->parameters[$i]?$de_part->parameters:$de_part->parameters[$i]);
				if(!$param)
				{
					break;
				}
				$pattribute = $param->attribute;
				if (strtolower($pattribute) == 'name')
				{
					$att_name = $param->value;
				}
			}
			return $att_name;
		}
			
		/*!
		@function uudecode
		@abstract NOT USED appears to implement UUDECODE without any special php functions.
		@author unknown, maybe Skeeter ? Rehberg ? Engo ?
		*/
		function uudecode($str)
		{
			$file='';
			for($i=0;$i<count($str);$i++)
			{
				if ($i==count($str)-1 && $str[$i] == "`")
				{
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
				$pos=1;
				$d=0;
				$len=(int)(((ord(substr($str[$i],0,1)) ^ 0x20) - ' ') & 077);
				while (($d+3<=$len) && ($pos+4<=strlen($str[$i])))
				{
					$c0=(ord(substr($str[$i],$pos  ,1)) ^ 0x20);
					$c1=(ord(substr($str[$i],$pos+1,1)) ^ 0x20);
					$c2=(ord(substr($str[$i],$pos+2,1)) ^ 0x20);
					$c3=(ord(substr($str[$i],$pos+3,1)) ^ 0x20);
					$file .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4));
					$file .= chr(((($c1 - ' ') & 077) << 4) | ((($c2 - ' ') & 077) >> 2));
					$file .= chr(((($c2 - ' ') & 077) << 6) |  (($c3 - ' ') & 077)      );
					$pos+=4;
					$d+=3;
				}
				if (($d+2<=$len) && ($pos+3<=strlen($str[$i])))
				{
					$c0=(ord(substr($str[$i],$pos  ,1)) ^ 0x20);
					$c1=(ord(substr($str[$i],$pos+1,1)) ^ 0x20);
					$c2=(ord(substr($str[$i],$pos+2,1)) ^ 0x20);
					$file .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4));
					$file .= chr(((($c1 - ' ') & 077) << 4) | ((($c2 - ' ') & 077) >> 2));
					$pos+=3;
					$d+=2;
				}
				if (($d+1<=$len) && ($pos+2<=strlen($str[$i])))
				{
					$c0=(ord(substr($str[$i],$pos  ,1)) ^ 0x20);
					$c1=(ord(substr($str[$i],$pos+1,1)) ^ 0x20);
					$file .= chr(((($c0 - ' ') & 077) << 2) | ((($c1 - ' ') & 077) >> 4));
				}
			}
			return $file;
		}
	}
?>

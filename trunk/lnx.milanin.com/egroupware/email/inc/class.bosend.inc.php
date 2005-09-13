<?php
	/**************************************************************************\
	* eGroupWare - email send non-SMTP functions					*
	* http://www.egroupware.org									*
	* Written by Angelo (Angles) Puglisi <angles@aminvestments.com>		*
	* --------------------------------------------							*
	*  This program is free software; you can redistribute it and/or modify it		*
	*  under the terms of the GNU General Public License as published by the	*
	*  Free Software Foundation; either version 2 of the License, or (at your		*
	*  option) any later version.								*
	\**************************************************************************/

	/* $Id: class.bosend.inc.php,v 1.27.2.3 2005/04/15 12:56:46 ralfbecker Exp $ */

	/*!
	@class bosend
	@abstract bo class for assembling messages for sending via class send
	@author Angles, server side attachment storage technique borrowed from Squirrelmail,

	*/
	class bosend
	{
		var $public_functions = array(
			'sendorspell'	=> True,
			'spellcheck'	=> True,
			'send'	=> True
		);
		var $mail_spell;
		var $msg_bootstrap;
		var $nextmatchs;
		var $tz_offset='';
		var $not_set='-1';
		var $mail_out = array();
		var $xi;

		// --- DEBUG FLAGS ---  level between 0 to 3, level 4 *sometimes* will dump info and exit
		// this debugs stuff in the constructor AND passes the debug flag to msg_bootstrap
		var $debug_constructor = 0;
		// this debugs decisions made in the "sendorspell" function
		var $debug_sendorspell = 0;
		// this debugs stuff in the "spellcheck" function
		var $debug_spellcheck = 0;
		// this debugs the general "send" function, if = 4 then dump info and exit send immediately
		var $debug_send = 0;
		// this debugs stuff in the attachment handling section inside the send section
		var $debug_struct = 0;
		var $company_disclaimer = '';

		function bosend()
		{
			if ($this->debug_constructor > 0) { echo 'email.bosend *constructor*: ENTERING<br>'; }

			// May 9, 2003 Ryan Bonham adds company disclaimer code
			// This Disclaimer will be added to any out going mail
			//var $company_disclaimer = "\r\n\r\n-- \r\n This message was sent using Forester GroupWare. Visit the Forest City Regional website at http://www.forestcityschool.org.\r\nThis message does not necessarily reflect the views of the Forest City Regional School District, nor has it been approved or sanctioned by it. \r\n";

			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			$this->msg_bootstrap->ensure_mail_msg_exists('email.bosend.constructor', $this->debug_constructor);

			$this->not_set = $GLOBALS['phpgw']->msg->not_set;
			if ($this->debug_constructor > 0) { echo 'email.bosend *constructor*: LEAVING<br>'; }
		}

		/*!
		@function get_originating_ip
		@abstract the client IP for this phpgw user at the time the send button was clicked
		@discussion Gets the value for the "X-Originating-IP" header. That header  is used
		by hotmail, for example, it looked like a "good thing" and was a feature request, so we
		use it here too. Even if the IP private (such as on a LAN), this can still be useful for the admin.
		*/
		function get_originating_ip()
		{
			$got_ip = '';
			if (is_object($GLOBALS['phpgw']->session))
			{
				$got_ip = $GLOBALS['phpgw']->session->getuser_ip();
			}
			elseif (isset($GLOBALS['HTTP_SERVER_VARS']['REMOTE_ADDR']))
			{
				$got_ip = $GLOBALS['HTTP_SERVER_VARS']['REMOTE_ADDR'];
			}

			// did we get anything useful ?
			if (trim((string)$got_ip) == '')
			{
				$got_ip = 'not available';
			}
			return $got_ip;
		}



		/*!
		@function copy_to_sent_folder
		@abstract Put a message in "Sent" Folder, if Applicable. This MUST be a message that has been sent already!
		@result Boolean
		@author Angles
		@discussion If a message has already been sent, and IF the user has set the pref enabling the use of the sent folder,
		only then should this function be used. If a message has not actually been sent, it should NOT be copied to the "Sent"
		folder because that misrepresents to the user the history of the message. Mostly this is an issue with automated
		messages sent from other apps. My .02 cents is that if a user did not send a message by pressing the "Send" button,
		then the message does not belong in the Sent messages folder. Other people may have a different opinion, so
		this function will not zap your keyboard if you think differently. Nonetheless, if the user has not enabled
		the preference "Sent mail copied to Sent Folder", then noting gets copied there no matter what. Note that we
		obtain these preference settings as shown in the example for this function. If the folder does not already exist,
		class mail_msg has code to make every reasonable attempt to create the folder automatically. Some servers
		just do things differently enough (unusual namespaces, sub folder trees) that the auto create may not work,
		but it is nost likly that it can be created, and even more likely that it already exists. NOTE: this particular class
		should be made availabllle to public use without the brain damage that is the current learning curve for this
		code. BUT for now, this is a private function unless you really know what you are doing. Even then, code
		in this class is subject to change.
		@access private - NEEDS TO BE MADE AVAILABLE FOR PUBLIC USE
		*/
		function copy_to_sent_folder()
		{
			/*!
			@capability (FUTURE CODE) append to sent folder without a pre-existing mailsvr_stream.
			@discussion FUTURE CODE what follows is untested but should work to accomplish that.
			While we do need to login to the mail server, we can just select the INBOX because the IMAP
			APPEND command does not require you have "selected" the folder that is the target of the append.
			We should be able to simply bootstrap the msg objext and call login, because during initialization
			the msg object gathers all the data it can find on what account number we are dealing with here,
			it handles that for us automatically. We do not want to append to the sent folder of the wrong account.
			@example ## this should work if a stream does not already exist (UNTESTED)
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			$this->msg_bootstrap->ensure_mail_msg_exists('email.bosend.copy_to_sent_folder', $this->debug_send);
			## now run the rest of the function as usual.
			*/

			if ($GLOBALS['phpgw']->msg->get_isset_pref('use_sent_folder') == False)
			{
				// ERROR, THIS ACCT DOES NOT WANT SENT FOLDER USED
				return False;
			}


			// note: what format should these folder name options (sent and trash) be held in
			// i.e. long or short name form, in the prefs database
			$sent_folder_name = $GLOBALS['phpgw']->msg->get_pref_value('sent_folder_name');

			// NOTE: append will open the stream automatically IF it is not open
			//if ((($GLOBALS['phpgw']->msg->get_isset_arg('mailsvr_stream')))
			//&& ($GLOBALS['phpgw']->msg->get_arg_value('mailsvr_stream') != ''))
			//{
				// note: "append" will CHECK  to make sure this folder exists, and try to create it if it does not
				// also note, make sure there is a \r\n CRLF empty last line sequence so Cyrus will be happy
				$success = $GLOBALS['phpgw']->msg->phpgw_append($sent_folder_name,
								$GLOBALS['phpgw']->mail_send->assembled_copy."\r\n",
								"\\Seen");
				//if ($success) { echo 'append to sent OK<br>'; } else { echo 'append to sent FAILED<br>'; echo 'imap_last_error: '.imap_last_error().'<br>'; }
			//}
			//else
			//{
				//echo 'NO STREAM available for sent folder append<br>';
			//	return False;
			//}

			return $success;
		}

		//  -------  This will be called just before leaving this page, to clear / unset variables / objects -----------
		function send_message_cleanup()
		{
			//echo 'send_message cleanup';
			$GLOBALS['phpgw']->msg->end_request();
			// note: the next lines can be removed since php takes care of memory management
			$this->mail_out = '';
			unset($this->mail_out);
			$GLOBALS['phpgw']->mail_send = '';
			unset($GLOBALS['phpgw']->mail_send);
		}

		/*!
		@function sendorspell
		@abstract detects whether the compose page was submitted as a send or spellcheck, and acts accordingly
		@params none, uses GET and POST vars
		@author Angles
		@discussion Compose form submit action target is bosend, naturally, however the spell check button submit is identical
		EXCEPT "btn_spellcheck" POST var will be set, which requires we handoff the handling to the spell class.
		*/
		function sendorspell()
		{
			if ($this->debug_sendorspell > 0) { $GLOBALS['phpgw']->msg->dbug->out('ENTERING: email.bosend.sendorspell('.__LINE__.') <br>'); }

			if ($this->debug_sendorspell > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.sendorspell('.__LINE__.'): $GLOBALS[HTTP_POST_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_POST); }
			if ($this->debug_sendorspell > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.sendorspell('.__LINE__.'): $GLOBALS[HTTP_GET_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_GET); }

			if ((isset($GLOBALS['phpgw']->msg->ref_POST['btn_spellcheck']))
			&& ($GLOBALS['phpgw']->msg->ref_POST['btn_spellcheck'] != ''))
			{
				if ($this->debug_sendorspell > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.sendorspell('.__LINE__.'): "btn_spellcheck" is set; calling $this->spellcheck()'.'<br>'); }
				$this->spellcheck();
			}
			elseif ((isset($GLOBALS['phpgw']->msg->ref_POST['btn_send']))
			&& ($GLOBALS['phpgw']->msg->ref_POST['btn_send'] != ''))
			{
				if ($this->debug_sendorspell > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.sendorspell('.__LINE__.'): "btn_send" is set; calling $this->send()'.'<br>'); }
				$this->send();
			}
			else
			{
				if ($this->debug_sendorspell > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.sendorspell('.__LINE__.'): ERROR: neither "btn_spellcheck" not "btn_send" is set; fallback action $this->send()'.'<br>'); }
				$this->send();
			}

			if ($this->debug_sendorspell > 0) { $GLOBALS['phpgw']->msg->dbug->out('LEAVING: email.bosend.sendorspell('.__LINE__.')'.'<br>'); }
		}


		/*!
		@function spellcheck
		@abstract if the compose page was submitted as a pellcheck, this function is called, it then calls the emai.spell class
		@params none, uses GET and POST vars
		@discussion If needed, put the body through stripslashes_gpc() before handing it off to the mail_spell object.
		This function simply gathers the required information and hands it off to the mail_spell class,
		*/
		function spellcheck()
		{
			if ($this->debug_spellcheck > 0) { $GLOBALS['phpgw']->msg->dbug->out('ENTERING: email.bosend.spellcheck('.__LINE__.')'.'<br>'); }

			if ($this->debug_spellcheck > 2) { 	$GLOBALS['phpgw']->msg->dbug->out('email.bosend.spellcheck('.__LINE__.'): $GLOBALS[HTTP_POST_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_POST); }
			if ($this->debug_spellcheck > 2) { 	$GLOBALS['phpgw']->msg->dbug->out('email.bosend.spellcheck: data dump('.__LINE__.'): $GLOBALS[HTTP_GET_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_GET); }

			// we may strip slashes, but that is all we should do before handing the body to the spell class
			//$my_body = $GLOBALS['phpgw']->msg->stripslashes_gpc(trim($GLOBALS['phpgw']->msg->get_arg_value('body')));
			//$this->mail_spell->set_body_orig($my_body);

			$this->mail_spell = CreateObject("email.spell");
			// preserve these vars
			$this->mail_spell->set_preserve_var('action', $GLOBALS['phpgw']->msg->get_arg_value('action'));
			// experimental, should this go here? is not this already in the URI or something?
			//$this->mail_spell->set_preserve_var('orig_action', $GLOBALS['phpgw']->msg->recall_desired_action());
			$this->mail_spell->set_preserve_var('from', $GLOBALS['phpgw']->msg->get_arg_value('from'));
			$this->mail_spell->set_preserve_var('sender', $GLOBALS['phpgw']->msg->get_arg_value('sender'));
			$this->mail_spell->set_preserve_var('to', $GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('to')));
			$this->mail_spell->set_preserve_var('cc', $GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('cc')));
			$this->mail_spell->set_preserve_var('bcc', $GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('bcc')));
			$this->mail_spell->set_preserve_var('msgtype', $GLOBALS['phpgw']->msg->get_arg_value('msgtype'));

			$this->mail_spell->set_subject($GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('subject')));
			$this->mail_spell->set_body_orig($GLOBALS['phpgw']->msg->stripslashes_gpc(trim($GLOBALS['phpgw']->msg->get_arg_value('body'))));

			// oops, do not forget about these, "attach_sig" and "req_notify"
			if (($GLOBALS['phpgw']->msg->get_isset_arg('attach_sig'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('attach_sig') != ''))
			{
				$this->mail_spell->set_preserve_var('attach_sig', $GLOBALS['phpgw']->msg->get_arg_value('attach_sig'));
			}
			if (($GLOBALS['phpgw']->msg->get_isset_arg('req_notify'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('req_notify') != ''))
			{
				$this->mail_spell->set_preserve_var('req_notify', $GLOBALS['phpgw']->msg->get_arg_value('req_notify'));
			}

			//$this->mail_spell->basic_spcheck();
			$this->mail_spell->spell_review();



			if ($this->debug_spellcheck > 0) { echo 'LEAVING: email.bosend.spellcheck('.__LINE__.')'.'<br>'; }
		}

		/*!
		@function send
		@abstract if the compose page was submitted as a pellcheck, this function is called
		@params none, uses GET and POST vars, however this will be OOPd for API use
		@discussion advanced function to send mail with all the complexities of modern MIME usage.
		Currently handles forwarding as an "encapsulated" MIME part, thus prewserving the original
		messages structure, including any attachments the original message had.
		Of course the user can attach files, this includes attaching additional files to a forwarded message which
		itself alsready has attachments.
		*/
		function send()
		{
			if ($this->debug_send> 0) { $GLOBALS['phpgw']->msg->dbug->out('ENTERING: mail.bosend.send('.__LINE__.') <br>'); }
			if ($this->debug_send> 2) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.send('.__LINE__.'): $GLOBALS[HTTP_POST_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_POST); }
			if ($this->debug_send> 2) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.send('.__LINE__.'): $GLOBALS[HTTP_GET_VARS] DUMP:', $GLOBALS['phpgw']->msg->ref_GET); }
			if ($this->debug_send> 3) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.send('.__LINE__.'): $this->debug_send > 3 PREMATURE EXIT, returning...'); return; }

			// ---- BEGIN BO SEND LOGIC

			if (($GLOBALS['phpgw']->msg->get_isset_arg('msgball'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('msgball') != ''))
			{
				$msgball = $GLOBALS['phpgw']->msg->get_arg_value('msgball');
			}
			else
			{
				$msgball = $this->not_set;
			}

			//  -------  Init Array Structure For Outgoing Mail  -----------
			$this->mail_out = Array();
			$this->mail_out['to'] = Array();
			$this->mail_out['cc'] = Array();
			$this->mail_out['bcc'] = Array();
			$this->mail_out['mta_to'] = Array();
			$this->mail_out['mta_from'] = '<'.trim($GLOBALS['phpgw']->msg->get_pref_value('address')).'>';
			$this->mail_out['mta_elho_domain'] = '';
			$this->mail_out['message_id'] = $GLOBALS['phpgw']->msg->make_message_id();
			$this->mail_out['in_reply_to'] = '';
			$this->mail_out['boundary'] = $GLOBALS['phpgw']->msg->make_boundary();
			$this->mail_out['date'] = '';
			$this->mail_out['originating_ip'] = '['.$this->get_originating_ip().']';
			$this->mail_out['main_headers'] = Array();
			$this->mail_out['body'] = Array();
			$this->mail_out['is_multipart'] = False;
			$this->mail_out['num_attachments'] = 0;
			$this->mail_out['whitespace'] = chr(9);
			$this->mail_out['is_forward'] = False;
			$this->mail_out['fwd_proc'] = '';
			$this->mail_out['from'] = array();
			$this->mail_out['sender'] = '';
			$this->mail_out['charset'] = '';
			$this->mail_out['feed_charset'] = '';
			$this->mail_out['msgtype'] = '';

			//  -------  Start Filling Array Structure For Outgoing Mail  -----------

			// -----  X-PHPGW flag (msgtype)  ------
			/*!
			@var msgtype
			@abstract obsoleted way phpgw apps used to inter-operate
			@discussion NOTE  this is a vestigal way for phpgw apps to inter-operate,
			I *think* this is being obsoleted via n-tiering and xml-rpc / soap methods.
			RARELY USED, maybe NEVER used, most email code for this is now commented out
			"back in the day..." the "x-phpgw" header was specified by a phpgw app *other* than the email app
			which was used to include special phpgw related handling instructions in the message which
			to the message intentended to be noticed and processed by the phpgw email app when the
			user open the mail for viewing, at which time the phpgw email app would issue the
			special handling instructions contained in the "x-phpgw" header.
			even before n-tiering of the phpgw apps and api begain, I (angles) considered this a possible
			area of abuse and I commented out the code in the email app that would notice, process and issue
			those instructions.
			*/
			if (($GLOBALS['phpgw']->msg->get_isset_arg('msgtype'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('msgtype') != ''))
			{
				// convert script GPC args into useful mail_out structure information
				$this->mail_out['msgtype'] = $GLOBALS['phpgw']->msg->get_arg_value('msgtype');
				// after this, ONLY USE $this->mail_out structure for this
			}

			// -----  CHARSET  -----
			/*!
			@property charset
			@abstract not user specified, not a user var, not an argument, not a paramater.
			@discussion charset could take up a lot of notes here, suffice to say that email began life as a
			US-ASCII thing and still us-ascii chars are strictly required for some headers, while other headers
			and the body have various alternative ways to deal with other charsets, ways that are well documented
			in email and other RFC's and other literature. In the rare event that the phpgw api is unable
			to provide us with a charset value, we use the RFC specified default value of "US-ASCII"
			*/
			$this->mail_out['charset'] = $GLOBALS['phpgw']->translation->charset();
			// if we sent a param chatset it goes here
			$this->mail_out['feed_charset'] = '';
			if (($GLOBALS['phpgw']->msg->get_isset_arg('charset'))
			&& (trim($GLOBALS['phpgw']->msg->get_arg_value('charset') != '')))
			{
				$this->mail_out['feed_charset'] = $GLOBALS['phpgw']->msg->get_arg_value('charset');
			}

			// -----  FROM  -----
			/*!
			@var from
			@abstract the mail's author, OPTIONAL, usually no need to specify this as an arg passed to the script.
			@discussion Generally this var does not need to be specified. When the mail is being sent from the
			user's default email account (or mail on behalf of the user, like automated email notifications),
			we generate the "from" header for the user, hence no custom "from" arg is necessary.
			This is the most common scenario, in which case we generate the "from" value as follows:
			(1) the user's "fullname" (a.k.a. the "personal" part of the address) is always picked up
			from the phpgw api's value that contains the users name, and
			(2) the user's email address is either (2a) the default value from the phpgw api which was
			passed into the user's preferences because the user specified no custom email address preference, or
			(2b) the user specified a custom email address in the email preferences in which case the aformentioned
			phpgw api default email address is not used in the user's preferences array, this user supplied
			value is used instead.
			Providing a "from" arg is usually for extra email accounts and/or alternative email profiles,
			where the user wants other than the "from" info otherwise defaultly associated with this email acccount.
			NOTE: from != sender
			from is who the mail came from assuming that person is also the mail's author.
			this is by far the most common scenario, "from" and "author" are usually one in the same
			(see below for info on when to *also* use "sender" - VERY rare)
			*/
			if (($GLOBALS['phpgw']->msg->get_isset_arg('from'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('from') != ''))
			{
				$from_assembled = $GLOBALS['phpgw']->msg->get_arg_value('sender');
			}
			else
			{
				$from_name = $GLOBALS['phpgw']->msg->get_pref_value('fullname');
				//$from_name = $GLOBALS['phpgw_info']['user']['fullname'];
				if(isset($GLOBALS['phpgw_info']['user']['email']))
				{
					$from_address = $GLOBALS['phpgw_info']['user']['email'];
				}
				else
				{
					$from_address = $GLOBALS['phpgw']->msg->get_pref_value('address');
				}
				$from_assembled = '"'.$from_name.'" <'.$from_address.'>';
			}
			// this array gets filled with functiuon "make_rfc_addy_array", but it will have only 1 numbered array, $this->mail_out['from'][0]
			// note that sending it through make_rfc_addy_array will ensure correct formatting of non us-ascii chars (if any) in the use's fullname
			$this->mail_out['from'] = $GLOBALS['phpgw']->msg->make_rfc_addy_array($from_assembled);

			// -----  SENDER  -----
			/*!
			@var sender
			@abstract OPTIONAL only used in the rare event that the person sending the email
			is NOT that email's author.
			@discussion RFC2822 makes clear that the Sender header is ONLY used if some one
			NOT the author (ex. the author's secretary) is sending the author's email.
			RFC2822 considers that "From" = the author and the "Sender" = the person who clicked the
			send button. Generally they are one in the same and generally the Sender header (and hence this
			"sender" var) is NOT needed, not used, not included in the email's headers.
			*/
			if (($GLOBALS['phpgw']->msg->get_isset_arg('sender'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('sender') != ''))
			{
				// clean data of magic_quotes escaping (if any)
				$this->mail_out['sender'] = $GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('sender'));
				// convert general address string into structured data array of addresses, each has properties [plain] and [personal]
				// this array gets filled with functiuon "make_rfc_addy_array", but it will have only 1 numbered array, $this->mail_out['sender'][0]
				$sender_array = $GLOBALS['phpgw']->msg->make_rfc_addy_array($this->mail_out['sender']);
				// realistically sender array should have no more than one member (can there really be more than 1 sender?)
				if (count($sender_array) > 0)
				{
					$this->mail_out['sender'] = $GLOBALS['phpgw']->msg->addy_array_to_str($sender_array);
					// bogus data check
					if (trim($this->mail_out['sender']) == '')
					{
						$this->mail_out['sender'] = '';
					}
				}
				else
				{
					$this->mail_out['sender'] = '';
				}
				// after this, ONLY USE $this->mail_out[] structure for this
				// it will either be blank string OR a string which should be 1 email address
			}
			// -----  DATE  -----
			/*!
			@property date
			@abstract not user specified, not a user var, not an argument, not a paramater.
			@discussion According to RFC2822 the Date header *should* be the local time with the correct
			timezone offset relative to GMT, however this is problematic on many Linux boxen, and
			in general I have found that reliably extracting this data from the host OS can be tricky,
			so instead we use a fallback value which is simply GMT time, which is allowed under RFC2822
			but not preferred.
			UPDATE: NOW "user timezone" is fed as arg "utz" from the users browser as
			a POST value of "minus0500" or "plus0500" for example, utz meaning UserTimeZone.
			.And if that is not available then the xGW API datetime->tz_offset is used, and none of
			those are available nor valid, then +0000 is used.
			*/
			//$this->mail_out['date'] = gmdate('D, d M Y H:i:s').' +0000';
			// debug: use this to test api tz: $GLOBALS['phpgw']->msg->unset_arg('utz');
			if ($GLOBALS['phpgw']->msg->get_isset_arg('utz'))
			{
				$my_tz = $GLOBALS['phpgw']->msg->get_arg_value('utz');
				// ok, so figure this out
				if ((stristr($my_tz,'minus'))
				&& (strlen($my_tz) == 9))
				{
					// ex: "minus0500"
					$this->tz_offset = str_replace('minus', '-', $my_tz);
				}
				elseif ((stristr($my_tz,'plus'))
				&& (strlen($my_tz) == 8))
				{
					// ex: "plus0500"
					$this->tz_offset = str_replace('plus', '+', $my_tz);
				}
				else
				{
					// some kind of error
					//$this->tz_offset = 'error1';
					$this->tz_offset = '+0000';
				}
			}
			// see if the api has a value
			elseif ((isset($GLOBALS['phpgw']->datetime->tz_offset))
			&& ((int)$GLOBALS['phpgw']->datetime->tz_offset != 0))
			{
				// this num starts as a simple int the api multiplies by 3600, so undo that
				$my_tz = ((int)$GLOBALS['phpgw']->datetime->tz_offset/3600);
				if ($my_tz >= 0)
				{
					$my_tz_sign = '+';
				}
				else
				{
					$my_tz_sign = '-';
				}
				$my_tz_abs = abs(((int)$my_tz));
				// format as  "+xxxx" or "-xxxx"
				if (($my_tz_abs < 24)
				&& ($my_tz_abs >= 10))
				{
					$this->tz_offset = $my_tz_sign.(string)$my_tz_abs.'00';
				}
				elseif (($my_tz_abs >= 0)
				&& ($my_tz_abs <= 9))
				{
					$this->tz_offset = $my_tz_sign.'0'.(string)$my_tz_abs.'00';
				}
				else
				{
					// some kind of error
					//$this->tz_offset = 'error1';
					$this->tz_offset = '+0000';
				}
			}
			else
			{
				// invalid input, use fallback
				$this->tz_offset = '+0000';
			}
			// get gmt unix timestamp, change into local timestamp, then make rfc date haeder string
			$tz_data=array();
			$tz_data['timestamp_gmt'] = time();
			$tz_data['time_string_gmt'] = gmdate('D, d M Y H:i:s', $tz_data['timestamp_gmt']);
			$tz_data['tz_seconds_offset'] = (int)$this->tz_offset;
			$tz_data['tz_seconds_offset'] = (($tz_data['tz_seconds_offset']/100) * 3600);
			$tz_data['timestamp_local'] = $tz_data['timestamp_gmt'] + $tz_data['tz_seconds_offset'];
			$tz_data['time_string_local'] = gmdate('D, d M Y H:i:s', $tz_data['timestamp_local']);
			if (!$tz_data['time_string_local'])
			{
				// there must have been an error, use a fallback value
				$this->mail_out['date'] = gmdate('D, d M Y H:i:s').' '.$this->tz_offset;
			}
			else
			{
				// this is RFC spec date, example "Thu, 15 Apr 2004 20:44:30 -0500"
				// where the datetime is in local and the offset indicates "add this offest to that datetime to get to GMT"
				$this->mail_out['date'] = $tz_data['time_string_local'].' '.$this->tz_offset;
			}
			if ($this->debug_send > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.send('.__LINE__.'): $this->tz_offset ['.$this->tz_offset.']  <br>'); }
			if ($this->debug_send > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.send('.__LINE__.'): $tz_data DUMP:', $tz_data); }
			if ($this->debug_send > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.send('.__LINE__.'): $this->mail_out[date] ['.$this->mail_out['date'].']  <br>'); }
			//if ($this->debug_send > 3) { $GLOBALS['phpgw']->msg->dbug->out('email.bosend.send('.__LINE__.'): $this->debug_send > 3 PREMATURE EXIT, returning...'); return; }

			// -----  IN-REPLY-TO  -----
			/*!
			@property in-reply-to
			@abstract if REPLY then this is the message ID of the msg we are replying to
			@discussion If this is a REPLY or REPLYALL then this is the main header value
			is the message ID of the msg we are replying to. Otherwise empty and not used.
			Note: requires a mailserver_call.
			*/
			if
			(
				(	($GLOBALS['phpgw']->msg->recall_desired_action()== 'reply')
					|| ($GLOBALS['phpgw']->msg->recall_desired_action()== 'replyall')
				)
				&&
				(
					$msgball != $not_set
				)
			)
			{
				// ===MAILSERVER_CALL===
				$msg_headers = $GLOBALS['phpgw']->msg->phpgw_header($msgball);
				if ($this->debug_send > 2) { $GLOBALS['phpgw']->msg->dbug->out('class.bosend.send('.__LINE__.'): in-reply-to handling: $msg_headers DUMP:', $msg_headers);  }
				if ($this->debug_send > 1) { $GLOBALS['phpgw']->msg->dbug->out('class.bosend.send('.__LINE__.'): in-reply-to handling: $msg_headers->message_id is: '.htmlspecialchars($msg_headers->message_id) .'<br>'); }
				$this->mail_out['in_reply_to'] = $this->not_set;
				if (isset($msg_headers->message_id))
				{
					$this->mail_out['in_reply_to'] = $msg_headers->message_id;
				}
				if ($this->debug_send > 1) { $GLOBALS['phpgw']->msg->dbug->out('class.bosend.send('.__LINE__.'): in-reply-to handling: $this->mail_out[in_reply_to] is: '.htmlspecialchars($this->mail_out['in_reply_to']) .'<br>'); }
			}
			else
			{
				if ($this->debug_send > 2) { $GLOBALS['phpgw']->msg->dbug->out('class.bosend.send('.__LINE__.'): reply BUT no msgball so NO msg_headers, and $this->mail_out[in_reply_to] = $this->not_set <br>');  }
				$this->mail_out['in_reply_to'] = $this->not_set;
			}


			// -----  MYMACHINE - The MTA HELO/ELHO DOMAIN ARG  -----
			/*!
			@property elho SMTP handshake domain value
			@abstract not user specified, not a user var, not an argument, not a paramater.
			@discussion when class.msg_send conducts the handshake with the SMTP server, this
			will be the required domain value that we supply to the SMTP server. Phpgw is considered
			the client to the SMTP server.
			RFC2821 sect 4.1.1.1 specifies this value is almost always the Fully Qualified Domain Name
			of the SMTP client machine, but rarely, when said client machine has dynamic FQDN or no reverse
			mapping is available, this value *should* be "address leteral" (see sect 4.1.3).
			Refer to the documentation for BIND for further reading on reverse lookup issues.
			*/
			$this->mail_out['mta_elho_mymachine'] = trim($GLOBALS['phpgw_info']['server']['hostname']);

			// ----  Forwarding Detection  -----
			if (($GLOBALS['phpgw']->msg->get_isset_arg('action'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('action') == 'forward'))
			{
				// fill mail_out[] structure information
				$this->mail_out['is_forward'] = True;
				// after this, ONLY USE $this->mail_out[] structure for this
			}
			if (($GLOBALS['phpgw']->msg->get_isset_arg('fwd_proc'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('fwd_proc') != ''))
			{
				// convert script GPC args into useful mail_out[] structure information
				$this->mail_out['fwd_proc'] = $GLOBALS['phpgw']->msg->get_arg_value('fwd_proc');
				// after this, ONLY USE $this->mail_out[] structure for this
			}

			// ----  Attachment Detection  -----
			// some of this attachment uploading and handling code is from squirrelmail (www.squirrelmail.org)
			$upload_dir = $GLOBALS['phpgw']->msg->att_files_dir;
			if (file_exists($upload_dir))
			{
				// Ralf Becker 2005-04-15:
				// We need to additionaly check if the files are named in $_POST['attached_filenames']!!!
				// Otherwise previously attached file of not send mails, will be send unintensional!!!
				// 
				$attached_filenames = $_POST['attached_filenames'] ? explode(',',$_POST['attached_filenames']) : array();

				// DO WE REALLY need to set_time_limit here?
				//@set_time_limit(0);
				// how many attachments do we need to process?
				$dh = opendir($upload_dir);
				$num_expected = 0;
				while ($file = readdir($dh))
				{
					if (($file != '.')
					&& ($file != '..')
					&& (ereg("\.info",$file)))
					{
						// Ralf Becker 2005-04-15:
						// We remove files NOT named in $_POST['attached_filenames'] !!!
						// Otherwise previously attached file of not send mails, will be send unintensional!!!
						// 
						list($content_type,$content_name) = file($upload_dir.SEP.$file);
						if (!in_array(trim($content_name),$attached_filenames))
						{
							//echo "<p>removed not attached file '$content_name' ($content_type) in file $file</p>\n"; exit;
							unlink($upload_dir.SEP.$file);
							unlink($upload_dir.SET.basename($file,'.info'));
							continue;
						}
						$num_expected++;
					}
				}
				closedir($dh);
				if ($num_expected > 0)
				{
					$this->mail_out['num_attachments'] = $num_expected;
					$this->mail_out['is_multipart'] = True;
				}
			}

			//  ------  get rid of the escape \ that magic_quotes (if enabled) HTTP POST will add, " becomes \" and  '  becomes  \'
			// convert script GPC args into useful mail_out structure information
			$to = $GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('to'));
			$cc = $GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('cc'));
			$bcc = $GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('bcc'));
			$body = $GLOBALS['phpgw']->msg->stripslashes_gpc(trim($GLOBALS['phpgw']->msg->get_arg_value('body')));
			$subject = $GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('subject'));
			// after this,  do NOT use ->msg->get_arg_value() for these anymore

			// since arg "body" *may* be huge (and is now in local var $body), lets clear it now
			$GLOBALS['phpgw']->msg->set_arg_value('body', '');

			// ----  DE-code HTML SpecialChars in the body    and subject -----
			// THIS NEEDS TO BE CHANGED WHEN MULTIPLE PART FORWARDS ARE ENABLED
			// BECAUSE WE CAN ONLY ALTER THE 1ST PART, I.E. THE PART THE USER JUST TYPED IN
			/*  email needs to be sent out as if it were PLAIN text (at least the part we are handling here)
			i.e. with NO ENCODED HTML ENTITIES, so use > instead of $rt; and " instead of &quot; . etc...
			it's up to the endusers MUA to handle any htmlspecialchars, whether to encode them or leave as it, the MUA should decide
			*/
			//$body = $GLOBALS['phpgw']->msg->htmlspecialchars_decode($body, $this->mail_out['feed_charset']);
			$body = $GLOBALS['phpgw']->msg->htmlspecialchars_decode($body);

			//echo '<br> ('.__LINE__.') $GLOBALS[phpgw]->msg DUMP<pre>'; print_r($GLOBALS['phpgw']->msg); echo '] </pre>'."\r\n";
			//echo '<br> ('.__LINE__.') $GLOBALS[phpgw]->msg->get_arg_value(subject) ['.$GLOBALS['phpgw']->msg->get_arg_value('subject').'] <br>'."\r\n";
			//echo '<br> ('.__LINE__.') pre $subject: ['.$subject.'] <br>'."\r\n";
			$subject = $GLOBALS['phpgw']->msg->htmlspecialchars_decode($subject);
			//echo '<br> ('.__LINE__.') post $subject: ['.$subject.'] <br>'."\r\n";
			//echo '<br> ('.__LINE__.') $body DUMP<pre>'; print_r($body); echo '] </pre>'."\r\n";

			// ----  Add Email Sig to Body   -----
			if (($GLOBALS['phpgw']->msg->get_isset_pref('email_sig'))
			&& ($GLOBALS['phpgw']->msg->get_pref_value('email_sig') != '')
			&& ($GLOBALS['phpgw']->msg->get_isset_arg('attach_sig'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('attach_sig') != '')
			// ONLY ADD SIG IF USER PUTS TEXT IN THE BODY
			//&& (strlen(trim($body)) > 3))
			&& ($this->mail_out['is_forward'] == False))
			{
				$user_sig = $GLOBALS['phpgw']->msg->get_pref_value('email_sig');
				// html_quotes_decode may be obsoleted someday:  workaround for a preferences database issue (<=pgpgw ver 0.9.13)
				$user_sig = $GLOBALS['phpgw']->msg->htmlspecialchars_decode($user_sig, $this->mail_out['charset']);
				$body = $body."\r\n"
						."\r\n"
						.'-- '."\r\n"
						.$user_sig ."\r\n";
			}
			if ($this->company_disclaimer)
			{
				$body = $body .$this->company_disclaimer;
			}

			// ----  LINE LENGTH for formatting body   -----
			// LINE LENGTH for "new" and our text of a forwarded text are 78 chars,
			// which is SHORTER than for reply quoted bodies that have ">" chars
			// this is only for text WE have written, not any other part of the body
			// html textbox no longer adds hard wrap on submit, so we handle it here now
			// NOTE reply bodies have already been handled as to length when we quoted the text
			//if (($GLOBALS['phpgw']->msg->get_isset_arg('orig_action'))
			//&& (
			//	($GLOBALS['phpgw']->msg->get_arg_value('orig_action') == 'new')
			//	|| ($GLOBALS['phpgw']->msg->get_arg_value('orig_action') == 'forward')
			//	)
			//)
			if (($GLOBALS['phpgw']->msg->recall_desired_action()== 'new')
			|| ($GLOBALS['phpgw']->msg->recall_desired_action() == 'forward'))
			{
				// WRAP BODY to lines of 78 chars then CRLF
				// IS THIS TOO SHORT? what about code snippets and stuff?or long URLs
				$body = $GLOBALS['phpgw']->msg->body_hard_wrap($body, 78);
			}
			elseif (($GLOBALS['phpgw']->msg->recall_desired_action()== 'reply')
			|| ($GLOBALS['phpgw']->msg->recall_desired_action()== 'replyall'))
			{
				//echo 'entering recall_desired_action == reply line length handling'."\r\n";
				// ok we have already quoted the text of the message we are replying to
				// BUT we have yet to standardize line length for the text WE just typed
				// in this message, our own text,
				// BUT we really should skip doing linebreaking it _again_ for the quoted text, though
				$body_array = array();
				$body_array = explode("\r\n", $body);
				// we do not use this again till we put $new_body into it, so clear the memory
				$body = '';
				// process only our unquoted text
				$body_array_count = count($body_array);
				$in_unquoted_block = False;
				$unquoted_text = '';
				$new_body = '';
				for ($bodyidx = 0; $bodyidx < $body_array_count; ++$bodyidx)
				{
					// skip text that starts with the ">" so called "quoting" char to the original body text
					// because it has already been line length normalized in bocompose
					$this_line = $body_array[$bodyidx];
					if ((strlen($this_line) > 1)
					&& ($this_line[0] == $GLOBALS['phpgw']->msg->reply_prefix[0]))
					{
						// ... this line starts with the quoting char
						if ($in_unquoted_block == True)
						{
							//echo 'line ength handling: processing MY text block'."\r\n";
							// TOGGLE - we are exiting block of our text
							// process the preceeding block of unquoted text, if any
							$unquoted_text = $GLOBALS['phpgw']->msg->body_hard_wrap($unquoted_text, 78);
							// now pass it into the new body var
							$new_body .= $unquoted_text;
							// clear this var
							$unquoted_text = '';
							// toggle this flag
							$in_unquoted_block = False;
							// for THIS line, it is the first in a quoted block, so pass straight to new body var
							//   I _think_ the CRLF is needed before this line because hard_wrap may not
							//   put one at the end of the last line of the unquoted text block ?
							//$new_body .=  "\r\n" . $this_line . "\r\n";
							$new_body .= $this_line . "\r\n";
						}
						else
						{
							// we are in a block of QUOTED text, simply pass it into the new body var
							$new_body .= $this_line . "\r\n";
						}
					}
					elseif (($body_array_count - $bodyidx) == 1)
					{
						// this is the last line, and it is NOT quoted, so if we were in an unquoted block (of our text) process it now
						// even if this is the only single line of unquoted text in the message, process it now
						// otherwise we may leave off the end of the message, if it is our text
						$unquoted_text .= $this_line;
						$unquoted_text = $GLOBALS['phpgw']->msg->body_hard_wrap($unquoted_text, 78);
						$new_body .= $unquoted_text;
						$unquoted_text = '';
						// this really is not needed, but so it anyway
						$in_unquoted_block = False;
					}
					else
					{
						// ... this line does NOT start with the quoting char, i.e. it is text we typed in
						// make sure flag is correct
						if ($in_unquoted_block == False)
						{
							// toggle this flag
							$in_unquoted_block = True;
							// there is just no real special action of a change into this block of our text,
							// the real action is when switching out of a block or our (unqouted) text
						}
						// compile this block of unquoted text, our text, in a var for later processing
						$unquoted_text .= $this_line . "\r\n";
					}
				}
				// cleanup
				$body_array = array();
				// ok we have gone through the whole message, put it in the bldy var
				$body = '';
				$body = $new_body;
				$new_body = '';
				$unquoted_text = '';
				// end reply body line length landling block
			}

			// Step One Addition
			// ---- Request Delivery Notification in Headers ----
			if (($GLOBALS['phpgw']->msg->get_isset_arg('req_notify'))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('req_notify') != ''))
			//cant imagine another check here, feel free to add something
			{
			//cant imagine another place to flag this....its a yes/no thing
				$notify=true;
			}
			// ----  Ensure To: and CC:  and BCC: are properly formatted   -----
			if ($to)
			{
				// mail_out[to] is an array of addresses, each has properties [plain] and [personal]
				$this->mail_out['to'] = $GLOBALS['phpgw']->msg->make_rfc_addy_array($to);
				// this will make a simple comma seperated string of the plain addresses (False sets the "include_personal" arg)
				$mta_to = $GLOBALS['phpgw']->msg->addy_array_to_str($this->mail_out['to'], False);
			}
			if ($cc)
			{
				$this->mail_out['cc'] = $GLOBALS['phpgw']->msg->make_rfc_addy_array($cc);
				$mta_to .= ',' .$GLOBALS['phpgw']->msg->addy_array_to_str($this->mail_out['cc'], False);
			}
			if ($bcc)
			{
				// here we will add bcc addresses to the list of adresses we will feed the MTA via the "RCPT TO" command
				// *however* this bcc data NEVER actually gets put in the message headers
				$this->mail_out['bcc'] = $GLOBALS['phpgw']->msg->make_rfc_addy_array($bcc);
				$mta_to .= ',' .$GLOBALS['phpgw']->msg->addy_array_to_str($this->mail_out['bcc'], False);
			}
			// now make mta_to an array because we will loop through it in class mail_send
			$this->mail_out['mta_to'] = explode(',', $mta_to);

			// RFC2821 - RCPT TO: args (email addresses) should be enclosed in brackets
			// when we constructed the $this->mail_out['mta_to'] var, we set "include_personal" to False, so this array has only "plain" email addys
			for ($i=0; $i<count($this->mail_out['mta_to']); $i++)
			{
				if (!preg_match('/^<.*>$/', $this->mail_out['mta_to'][$i]))
				{
					$this->mail_out['mta_to'][$i] = '<'.$this->mail_out['mta_to'][$i].'>';
				}
			}

			/*
			// ===== DEBUG =====
			echo '<br>';
			//$dubug_info = $to;
			//$dubug_info = ereg_replace("\r\n.", "CRLF_WSP", $dubug_info);
			//$dubug_info = ereg_replace("\r\n", "CRLF", $dubug_info);
			//$dubug_info = ereg_replace(" ", "SP", $dubug_info);
			//$dubug_info = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($dubug_info);
			//echo serialize($dubug_info);

			//$to = $GLOBALS['phpgw']->msg->addy_array_to_str($to, True);
			//echo 'to including personal: '.$GLOBALS['phpgw']->msg->htmlspecialchars_encode($to).'<br>';

			echo '<br> var dump mail_out <br>';
			var_dump($this->mail_out);
			//echo '<br> var dump cc <br>';
			//var_dump($cc);
			echo '<br>';

			$GLOBALS['phpgw']->common->phpgw_exit();
			return;
			// ===== DEBUG =====
			*/

			// ----  Send The Email  ==  via CLASS MAIL SEND 2822  == -----
			// USE CLASS MAIL SEND 2822
			$GLOBALS['phpgw']->mail_send = CreateObject("email.mail_send");
			$GLOBALS['phpgw']->mail_send->send_init();
			// do we need to retain a copy of the sent message for the "Sent" folder?
			if($GLOBALS['phpgw']->msg->get_isset_pref('use_sent_folder'))
			{
				$GLOBALS['phpgw']->mail_send->retain_copy = True;
			}

			// initialize structure for 1st part
			$body_part_num = 0;
			$this->mail_out['body'][$body_part_num]['mime_headers'] = Array();
			$this->mail_out['body'][$body_part_num]['mime_body'] = Array();

			// -----  ADD 1st PART's MIME HEADERS  (if necessary)  -------
			if (($this->mail_out['is_multipart'] == True)
			|| ($this->mail_out['is_forward'] == True))
			{
				// --- Add Mime Part Header to the First Body Part
				// this part _should_ be text
				$m_line = 0;
				$this->mail_out['body'][0]['mime_headers'][$m_line] = 'This is a multipart message in MIME format';
				$m_line++;
				$this->mail_out['body'][0]['mime_headers'][$m_line] = "\r\n";
				$m_line++;
				$this->mail_out['body'][0]['mime_headers'][$m_line] = '--' .$this->mail_out['boundary'];
				$m_line++;
				//if ($this->mail_out['feed_charset'] != '')
				//{
				//	$this->mail_out['body'][0]['mime_headers'][$m_line] = 'Content-Type: text/plain; charset="'.$this->mail_out['feed_charset'].'"';
				//	$m_line++;
				//}
				//else
				//{
					$this->mail_out['body'][0]['mime_headers'][$m_line] = 'Content-Type: text/plain; charset="'.$this->mail_out['charset'].'"';
					$m_line++;
				//}
				if ($this->mail_out['msgtype'] != '')
				{
					// "folded header" opens with a "whitespace"
					$this->mail_out['body'][0]['mime_headers'][$m_line] = '  phpgw-type="'.$this->mail_out['msgtype'].'"';
					$m_line++;
				}
				// 7 BIT vs. 8 BIT Content-Transfer-Encoding
				// 7 bit means that no chars > 127 can be in the email, or else MTA's will get confused
				// if you really want to enforce 7 bit you should qprint encode the email body
				// however, if you are forwarding via MIME encapsulation then I do not believe it's cool to alter
				// the original message's content by qprinting it if it was not already qprinted
				// in which case you should send it 8 bit instead.
				// ALSO, the top most level encoding can not be less restrictive than any embedded part's encoding
				// 7bit is more restrictive than 8 bit
				// OPTIONS:
				// 1) send it out with no encoding header - against RFC's but the MTA will probably put it there for you
				// 2) do a scan for chars > 127, if so, send 8 bit and hope the MTA can handle 8 bit
				// 3) scan for > 127 then qprint what we can (not embeded) then send out 7 bit
				// 4) listen to the initial string from the MTA indicating if it can handle MIME8BIT
				// 5) just send it out 8 bit and hope for the best (for now do this)
				//$this->mail_out['body'][0]['mime_headers'][$m_line] = 'Content-Transfer-Encoding: 7bit';
				//$m_line++;
				$this->mail_out['body'][0]['mime_headers'][$m_line] = 'Content-Transfer-Encoding: 8bit';
				$m_line++;
				$this->mail_out['body'][0]['mime_headers'][$m_line] = 'Content-Disposition: inline';
				$m_line++;
			}

			// -----  MAIN BODY PART (1st Part)  ------
			// Explode Body into Array of strings
			$body = $GLOBALS['phpgw']->msg->normalize_crlf($body);
			// test convert to feed_charset
			//if ($this->mail_out['feed_charset'] != '')
			//{
			//	$body = $GLOBALS['phpgw']->translation->convert($body,$this->mail_out['feed_charset']);
			//}
			$this->mail_out['body'][$body_part_num]['mime_body'] = explode ("\r\n",$body);
			//$this->mail_out['body'][$body_part_num]['mime_body'] = $GLOBALS['phpgw']->msg->explode_linebreaks(trim($body));
			// for no real reason, I add a CRLF to the end of the body
			//$this->mail_out['body'][$body_part_num]['mime_body'][count($this->mail_out['body'][$body_part_num]['mime_body'])] = " \r\n";
			// since var $body *may* be huge, lets clear it now
			$body = '';

			// -----  FORWARD HANDLING  ------
			// Sanity Check - we can not "pushdown" a multipart/mixed original mail, it must be encaposulated
			// PUSHDOWN NOT YET IMPLEMENTED
			if (($this->mail_out['is_forward'] == True)
			&& ($this->mail_out['fwd_proc'] == 'pushdown'))
			{
				// ===MAILSERVER_CALL===
				$msg_headers = $GLOBALS['phpgw']->msg->phpgw_header('');
				$msg_struct = $GLOBALS['phpgw']->msg->phpgw_fetchstructure('');

				// === PGW_MSG_STRUCT CALL ===
				$this->mail_out['fwd_info'] = $GLOBALS['phpgw']->msg->pgw_msg_struct($msg_struct, $not_set, '1', 1, 1, 1);
				if (($this->mail_out['fwd_info']['type'] == 'multipart')
				|| ($this->mail_out['fwd_info']['subtype'] == 'mixed'))
				{
					$this->mail_out['fwd_proc'] = 'encapsulate';
				}
			}

			// Add Forwarded Mail as An Additional Encapsulated "message/rfc822" MIME Part
			// PUSHDOWN NOT IMLEMENTED YET!!!
			if (($this->mail_out['is_forward'] == True)
			&& ($this->mail_out['fwd_proc'] == 'pushdown'))
			{
				// -----   INCOMPLETE CODE HERE  --------
				// -----   INCOMPLETE CODE HERE  --------
				// -----   INCOMPLETE CODE HERE  --------
				$body_part_num++;
				$this->mail_out['body'][$body_part_num]['mime_headers'] = Array();
				$this->mail_out['body'][$body_part_num]['mime_body'] = Array();

				// ----  General Information about The Original Message  -----
				// ===MAILSERVER_CALL===
				$msg_headers = $GLOBALS['phpgw']->msg->phpgw_header('');
				$msg_struct = $GLOBALS['phpgw']->msg->phpgw_fetchstructure('');

				// use the "pgw_msg_struct" function to get the orig message main header info
				// === PGW_MSG_STRUCT CALL ===
				$this->mail_out['fwd_info'] = $GLOBALS['phpgw']->msg->pgw_msg_struct($msg_struct, $not_set, '1', 1, 1, 1);
				// add some more info
				$this->mail_out['fwd_info']['from'] = $GLOBALS['phpgw']->msg->make_rfc2822_address($msg_headers->from[0]);
				$this->mail_out['fwd_info']['date'] = $GLOBALS['phpgw']->common->show_date($msg_headers->udate);
				// the third empty param says no html encoding of return data
				$this->mail_out['fwd_info']['subject'] = $GLOBALS['phpgw']->msg->get_subject($msg_headers,'','');

				// normalize data to rfc2046 defaults, in the event data is not provided
				if ($this->mail_out['fwd_info']['type'] == $not_set)
				{
					$this->mail_out['fwd_info']['type'] = 'text';
				}
				if ($this->mail_out['fwd_info']['subtype'] == $not_set)
				{
					$this->mail_out['fwd_info']['subtype'] = 'plain';
				}
				if ($this->mail_out['fwd_info']['disposition'] == $not_set)
				{
					$this->mail_out['fwd_info']['disposition'] = 'inline';
				}

				$this->mail_out['fwd_info']['boundary'] = $not_set;
				for ($p = 0; $p < $part_nice['ex_num_param_pairs']; $p++)
				{
					//echo '<br>params['.$p.']: '.$part_nice['params'][$p]['attribute'].'='.$part_nice['params'][$p]['value'] .'<br>';
					if (($part_nice['params'][$p]['attribute'] == 'boundary')
					  && ($part_nice['params'][$p]['value'] != $not_set))
					{
						$this->mail_out['fwd_info']['boundary'] = $part_nice['params'][$p]['value'];
						break;
					}
				}
				if ($this->mail_out['fwd_info']['boundary'] != $not_set)
				{
					// original email ALREADY HAS a boundary., so use it!
					$this->mail_out['boundary'] = $this->mail_out['fwd_info']['boundary'];
				}
				//echo '<br>part_nice[boundary] ' .$this->mail_out['fwd_info']['boundary'] .'<br>';
				//echo '<br>part_nice: <br>' .$GLOBALS['phpgw']->msg->htmlspecialchars_encode(serialize($this->mail_out)) .'<br>';

				// prepare the mime part headers
				// original body gets pushed down one part, i.e. was part 1, now is part 2
				$m_line = 0;
				$this->mail_out['body'][$body_part_num]['mime_headers'][$m_line] = '--' .$this->mail_out['boundary'];
				$m_line++;
				$this->mail_out['body'][$body_part_num]['mime_headers'][$m_line] = 'Content-Type: '.$this->mail_out['fwd_info']['type'].'/'.$this->mail_out['fwd_info']['subtype'].';';
				$m_line++;
				if ($this->mail_out['fwd_info']['encoding'] != 'other')
				{
					$this->mail_out['body'][$body_part_num]['mime_headers'][$m_line] = 'Content-Transfer-Encoding: '.$this->mail_out['fwd_info']['encoding'];
					$m_line++;
				}
				for ($p = 0; $p < $part_nice['ex_num_param_pairs']; $p++)
				{
					//echo '<br>params['.$p.']: '.$part_nice['params'][$p]['attribute'].'='.$part_nice['params'][$p]['value'] .'<br>';
					if ($part_nice['params'][$p]['attribute'] != 'boundary')
					{
						$this->mail_out['body'][$body_part_num]['mime_headers'][$m_line] = '  '.$part_nice['params'][$p]['attribute'].'="'.$part_nice['params'][$p]['value'].'"';
						$m_line++;
					}
				}
				$this->mail_out['body'][$body_part_num]['mime_headers'][$m_line] = 'Content-Disposition: '.$this->mail_out['fwd_info']['disposition'];
				$m_line++;

				// dump the original BODY (with out its headers) here
				// ===MAILSERVER_CALL===
				$fwd_this = $GLOBALS['phpgw']->msg->phpgw_body();
				// Explode Body into Array of strings
				$this->mail_out['body'][$body_part_num]['mime_body'] = $GLOBALS['phpgw']->msg->explode_linebreaks(trim($fwd_this));
				$fwd_this = '';
			}
			elseif (($this->mail_out['is_forward'] == True)
			&& ($this->mail_out['fwd_proc'] == 'encapsulate'))
			{
				// RIGHT NOW THIS IS THE ONLY REALLY USED FORWARD METHOD
				// even the "foward as quoted test" preference ALSO sends an encalsulated msg with whatever it quoted for forward in main body
				// generate the message/rfc822 part that is the container for the forwarded mail
				$body_part_num++;
				$this->mail_out['body'][$body_part_num]['mime_headers'] = Array();
				$this->mail_out['body'][$body_part_num]['mime_body'] = Array();

				// mime headers define this as a message/rfc822 part
				// following RFC2046 recommendations
				$this->mail_out['body'][$body_part_num]['mime_headers'][0] = '--' .$this->mail_out['boundary'];
				$this->mail_out['body'][$body_part_num]['mime_headers'][1] = 'Content-Type: message/rfc822'.';';
				$this->mail_out['body'][$body_part_num]['mime_headers'][2] = 'Content-Disposition: inline';

				// DUMP the original message verbatim into this part's "body" - i.e. encapsulate the original mail
				// ===MAILSERVER_CALL===
				$fwd_this['sub_header'] = trim($GLOBALS['phpgw']->msg->phpgw_fetchheader());
				$fwd_this['sub_header'] = $GLOBALS['phpgw']->msg->normalize_crlf($fwd_this['sub_header']);

				// CLENSE headers of offensive artifacts that can confuse dumb MUAs
				$fwd_this['sub_header'] = preg_replace("/^[>]{0,1}From\s.{1,}\r\n/i", "", $fwd_this['sub_header']);
				$fwd_this['sub_header'] = preg_replace("/Received:\s(.{1,}\r\n\s){0,6}.{1,}\r\n(?!\s)/m", "", $fwd_this['sub_header']);
				$fwd_this['sub_header'] = preg_replace("/.{0,3}Return-Path.*\r\n/m", "", $fwd_this['sub_header']);
				$fwd_this['sub_header'] = trim($fwd_this['sub_header']);

				// get the body
				// ===MAILSERVER_CALL===
				$fwd_this['sub_body'] = trim($GLOBALS['phpgw']->msg->phpgw_body());
				//$fwd_this['sub_body'] = $GLOBALS['phpgw']->msg->normalize_crlf($fwd_this['sub_body']);

				// -- "expect_good_body_crlf" --
				// we expect modern email equipment to give us properly formatted CRLF
				// although this is not always the case
				// WE USED TO ASSUME FALSE, now changing this to assume true.
				// we can do this here because this body is coming from a mailserver, not a POST html form
				if ($GLOBALS['phpgw']->msg->expect_good_body_crlf == False)
				{
					// Make Sure ALL INLINE BOUNDARY strings actually have CRLF CRLF preceeding them
					// because some lame MUA's don't properly format the message with CRLF CRLF BOUNDARY
					// in which case when we encapsulate such a malformed message, it *may* not be understood correctly
					// by the receiving MUA, so we attempt to correct such a malformed message before we encapsulate it
					// ---- not yet complete ----
					$char_quot = '"';
					preg_match("/boundary=[$char_quot]{0,1}.*[$char_quot]{0,1}\r\n/",$fwd_this['sub_header'],$fwd_this['matches']);
					if (stristr($fwd_this['matches'][0], 'boundary='))
					{
						$fwd_this['boundaries'] = trim($fwd_this['matches'][0]);
						$fwd_this['boundaries'] = str_replace('boundary=', '', $fwd_this['boundaries']);
						$fwd_this['boundaries'] = str_replace('"', '', $fwd_this['boundaries']);
						$this_boundary = $fwd_this['boundaries'];
						//$fwd_this['sub_body'] = preg_replace("/(?<!(\r\n\r\n))[-]{2}$this_boundary/m", "\r\n\r\n".'DASHDASH'.$this_boundary, $fwd_this['sub_body']);
						//$fwd_this['sub_body'] = preg_replace("/(?<!(\r\n\r\n))[-]{2}$this_boundary/m", "\r\n\r\n".'DASHDASH'.$this_boundary, $fwd_this['sub_body']);
						//$fwd_this['sub_body'] = preg_replace("/(?<!(\r\n\r\n))[-]{2}".$this_boundary."[-]{0,2}/m", "\r\n\r\n".'DASHDASH'.$this_boundary, $fwd_this['sub_body']);
						//$fwd_this['sub_body'] = preg_replace("/(?<!(\r\n\r\n))[-]{2}$this_boundary/m", "\r\n\r\n".'DASHDASH'.$this_boundary, $fwd_this['sub_body']);
						$fwd_this['sub_body'] = preg_replace("/(?<!(\r\n\r\n))[-]{2}".$this_boundary."/m", "\r\n\r\n".'--'.$this_boundary, $fwd_this['sub_body']);
						$dash_dash = '--';
						$fwd_this['sub_body'] = preg_replace("/(?<!(\r\n\r\n))[-]{2}$dash_dash$this_boundary$dash_dash/", "\r\n\r\n".'--'.$this_boundary.'--', $fwd_this['sub_body']);
						$fwd_this['sub_body'] = trim($fwd_this['sub_body']);
					}
				}

				// assemble it and add the blank line that seperates the headers from the body
				$fwd_this['processed'] = $fwd_this['sub_header']."\r\n"."\r\n".$fwd_this['sub_body'];
				// memory mgt, doing this can really save memory with big attachments
				$fwd_this['sub_body'] = '';
				unset($fwd_this['sub_body']);


				/*
				//echo 'fwd_this[sub_header]: <br><pre>'.$GLOBALS['phpgw']->msg->htmlspecialchars_encode($fwd_this['sub_header']).'</pre><br>';
				//echo 'fwd_this[matches]: <br><pre>'.$GLOBALS['phpgw']->msg->htmlspecialchars_encode(serialize($fwd_this['matches'])).'</pre><br>';
				//echo 'fwd_this[boundaries]: <br><pre>'.$GLOBALS['phpgw']->msg->htmlspecialchars_encode($fwd_this['boundaries']).'</pre><br>';
				//echo '=== var dump    fwd_this <br><pre>';
				//var_dump($fwd_this);
				//echo '</pre><br>';
				echo 'fwd_this[processed]: <br><pre>'.$GLOBALS['phpgw']->msg->htmlspecialchars_encode($fwd_this['processed']).'</pre><br>';
				unset($fwd_this);
				exit;
				*/


				// Explode Body into Array of strings
				if ($GLOBALS['phpgw']->msg->expect_good_body_crlf == False)
				{
					//$fwd_this['processed'] = $GLOBALS['phpgw']->msg->normalize_crlf($fwd_this['processed']);
					//$this->mail_out['body'][$body_part_num]['mime_body'] = explode("\r\n", $fwd_this['processed']);
					$this->mail_out['body'][$body_part_num]['mime_body'] = $GLOBALS['phpgw']->msg->explode_linebreaks(trim($fwd_this['processed']));
				}
				else
				{
					$this->mail_out['body'][$body_part_num]['mime_body'] = explode("\r\n",(trim($fwd_this['processed'])));
				}
				// clear this no longer needed var
				$fwd_this = '';
				unset($fwd_this);
			}

			/*
			// ===== DEBUG =====
			echo '<br>';
			echo '<br>=== mail_out ===<br>';
			$dubug_info = serialize($this->mail_out);
			$dubug_info = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($dubug_info);
			echo $dubug_info;
			echo '<br>';

			$GLOBALS['phpgw']->common->phpgw_footer();
			exit;
			// ===== DEBUG =====
			*/


			// ---  ATTACHMENTS -- Add each of them as an additional mime part ---
			if ($this->mail_out['num_attachments'] > 0)
			{
				// DO WE REALLY need to set_time_limit here?
				//@set_time_limit(0);
				// process (encode) attachments and add to the email body
				$total_files = 0;
				$dh = opendir($upload_dir);
				while ($file = readdir($dh))
				{
					if (($file != '.')
					&& ($file != '..'))
					{
						if (! ereg("\.info",$file))
						{
							$total_files++;
							$size = filesize($upload_dir.SEP.$file);

							$info_file = $upload_dir.SEP.$file.'.info';
							$file_info = file($info_file);
							if ($this->debug_struct > 2) { echo 'FILE INFO: '.htmlspecialchars(serialize($file_info)).'<br>'; }
							$content_type = trim($file_info[0]);
							$content_name = trim($file_info[1]);
							
							// testing i18n handling of filenames
							$max_ord = 0;
							$needs_rfc_encode = False;
							for( $i = 0 ; $i < strlen($content_name) ; $i++ )
							{
								if (ord($content_name[$i]) > $max_ord)
								{
									$max_ord = ord($content_name[$i]);
								}
							}
							$hdr_ready_content_name = $content_name;
							if ($max_ord > 123)
							{
								$needs_rfc_encode = True;
								$hdr_ready_content_name = $GLOBALS['phpgw']->msg->encode_header($content_name);
							}

							$body_part_num++;
							$this->mail_out['body'][$body_part_num]['mime_headers'] = Array();
							$this->mail_out['body'][$body_part_num]['mime_body'] = Array();

							$m_line = 0;
							$this->mail_out['body'][$body_part_num]['mime_headers'][$m_line] = '--' .$this->mail_out['boundary'];
							$m_line++;
							$this->mail_out['body'][$body_part_num]['mime_headers'][$m_line] = 'Content-Type: '.$content_type.'; name="'.$content_name.'"';
							$m_line++;
							$this->mail_out['body'][$body_part_num]['mime_headers'][$m_line] = 'Content-Transfer-Encoding: base64';
							$m_line++;
							//$this->mail_out['body'][$body_part_num]['mime_headers'][$m_line] = 'Content-Disposition: attachment; filename="'.$content_name.'"';
							$this->mail_out['body'][$body_part_num]['mime_headers'][$m_line] = 'Content-Disposition: attachment; filename="'.$hdr_ready_content_name.'"';

							/*
							// BASE64 ENCODE method 1 - entire file loaded into memory
							// get the file and base 64 encode it
							$fh = fopen($upload_dir.SEP.$file,'rb');
							// $rawfile = fread($fh,$size);
							$b64_part = chunk_split(base64_encode(fread($fh,$size)));
							$this->mail_out['body'][$body_part_num]['mime_body'] = explode("\r\n", $b64_part);
							$b64_part = '';
							fclose($fh);
							*/

							// BASE64 ENCODE method 2 - small chunks of file limit memory usage during encoding
							// base64 encoded data should be split into lines of 76 chars for the outgoing message (not including the CRLF)
							// reading 3 bytes from the file makes 4 bytes of encoded data
							// 76 encoded chars = (19 x 4 byte groups) per line
							// data must be fed in [bytes div 3] chunks (i.e. 30 bytes is divisible by 3 so is good) to avoid string padding
							// reading 19 x 3 bytes (57 chars) from source file produces the 76 char encoded single line of data
							// 57 is, of course, divisible by 3, so the resulting encoded line will not be padded with "=" chars (good)
							// for initial testing, it may be inefficient but do the file reading in 57 byte chunks
							$fh = fopen($upload_dir.SEP.$file,'rb');
							$next_pos = 0;
							while ($datachunk = fread($fh, 57))
							{
								if ($this->debug_struct > 2) { echo '$next_pos ['.$next_pos.'] :: string ['.$datachunk.'] :: b64 version ['.base64_encode($datachunk).']<br>'."\r\n"; }
								$this->mail_out['body'][$body_part_num]['mime_body'][$next_pos] = base64_encode($datachunk);
								$next_pos++;
							}
							$b64_part = '';
							fclose($fh);


							/*
							/ /  * * * * MOVE THIS INTO MAIL SEND 2822 PROC * * * * *
							// IF LAST PART - GIVE THE "FINAL" boundary
							if ($total_files >= $num_expected)
							{
								// attachments (parts) have their own boundary preceeding them (see below)
								// this is: "--"boundary
								// all boundary strings are have 2 dashes "--" added to their begining
								// and the FINAL boundary string (after all other parts) ALSO has
								// 2 dashes "--" tacked on tho the end of it, very important !!
								// the next available array number
								$m_line = count($this->mail_out['body'][$body_part_num]['mime_body']);
								$this->mail_out['body'][$body_part_num]['mime_body'][$m_line] = '--' .$this->mail_out['boundary'].'--';
							}
							//echo 'tot: '.$total_files .' expext: '.$num_expected; // for debugging
							*/

							// delete the temp file (the attachment)
							unlink($upload_dir.SEP.$file);
							// delete the other temp file (the .info file)
							unlink($upload_dir.SEP.$file.'.info');
						}
					}
				}
				// get rid of the temp dir we used for the above
				closedir($dh);
				rmdir($upload_dir);
			}

			// --- MAIN HEADERS  -------
			$hdr_line = 0;
			$this->mail_out['main_headers'][$hdr_line] = 		'X-Originating-IP: '.$this->mail_out['originating_ip'];
			$hdr_line++;
			$this->mail_out['main_headers'][$hdr_line] = 		'From: '.$GLOBALS['phpgw']->msg->addy_array_to_str($this->mail_out['from']);
			$hdr_line++;
			if ($this->mail_out['sender'] != '')
			{
				// rfc2822 - sender is only used if some one NOT the author (ex. the author's secretary) is sending the authors email
				// $this->mail_out['sender'] is initialized as an empty array in the begining of this file
				// then, it will be filled if the ->msg->args['sender'] was passed to the script,
				// where it would have been converted to the appropriate format and put in the $this->mail_out['sender'] array
				$this->mail_out['main_headers'][$hdr_line] = 	'Sender: '.$this->mail_out['sender'];
				$hdr_line++;
			}
			//$this->mail_out['main_headers'][$hdr_line] = 		'Reply-To: '.$GLOBALS['phpgw']->msg->addy_array_to_str($this->mail_out['from']);
			//$hdr_line++;
			$this->mail_out['main_headers'][$hdr_line] = 		'To: '.$GLOBALS['phpgw']->msg->addy_array_to_str($this->mail_out['to']);
			$hdr_line++;
			if (count($this->mail_out['cc']) > 0)
			{
				$this->mail_out['main_headers'][$hdr_line] = 	'Cc: '.$GLOBALS['phpgw']->msg->addy_array_to_str($this->mail_out['cc']);
				$hdr_line++;
			}
			if ($this->mail_out['in_reply_to'] != $this->not_set)
			{
				$this->mail_out['main_headers'][$hdr_line] = 	'In-Reply-To: '.$this->mail_out['in_reply_to'];
				$hdr_line++;
			}
			// if feed_charset is empty then the called function knows what to do
			//$this->mail_out['main_headers'][$hdr_line] = 		'Subject: '.$GLOBALS['phpgw']->msg->encode_header($subject, $this->mail_out['feed_charset']);
			$this->mail_out['main_headers'][$hdr_line] = 		'Subject: '.$GLOBALS['phpgw']->msg->encode_header($subject);
			$hdr_line++;
			$this->mail_out['main_headers'][$hdr_line] = 		'Date: '.$this->mail_out['date'];
			$hdr_line++;
			$this->mail_out['main_headers'][$hdr_line] = 		'Message-ID: '.$this->mail_out['message_id'];
			$hdr_line++;
			//Step One Addition
			//There is no other way to put this headers for request notify, so here we go
			//Qmail servers use Notice-Requested-Upon-Delivery-To: so thats what we are going to use now
			//its the correct and nice way to support it
			//AFAIK, sendmail servers use Return-Receipt-To: which suck but are widly supported so....here goes as weelll
			if($notify)
			{
				$this->mail_out['main_headers'][$hdr_line] = 'Notice-Requested-Upon-Delivery-To: '.$GLOBALS['phpgw']->msg->addy_array_to_str($this->mail_out['to']);
				$hdr_line++;
				$this->mail_out['main_headers'][$hdr_line] = 'Return-Receipt-To: '.$this->mail_out['sender'];
				$hdr_line++;

			}

			// RFC2045 REQUIRES this header in even if no embedded mime parts are in the body
			// MTA's, MUA's *should* assume the following as default (RFC2045) if not included
			$this->mail_out['main_headers'][$hdr_line] = 		'MIME-Version: 1.0';
			$hdr_line++;

			if (($this->mail_out['is_multipart'] == True)
			|| ($this->mail_out['is_forward'] == True))
			{
				// THIS MAIL INCLUDES EMBEDED MIME PARTS
				$this->mail_out['main_headers'][$hdr_line] =	'Content-Type: multipart/mixed;';
				$hdr_line++;
				$this->mail_out['main_headers'][$hdr_line] =	$this->mail_out['whitespace'].'boundary="'.$this->mail_out['boundary'].'"';
				$hdr_line++;
			}
			else
			{
				// NO MIME SUBPARTS - SIMPLE 1 PART MAIL
				// headers = mime part 0 and  body = mime part 1
				$this->mail_out['main_headers'][$hdr_line] =	'Content-Type: text/plain;';
				$hdr_line++;
				//if ($this->mail_out['feed_charset'] != '')
				//{
				//	$this->mail_out['main_headers'][$hdr_line] =	$this->mail_out['whitespace'].'charset="'.$this->mail_out['feed_charset'].'"';
				//	$hdr_line++;
				//}
				//else
				//{
					$this->mail_out['main_headers'][$hdr_line] =	$this->mail_out['whitespace'].'charset="'.$this->mail_out['charset'].'"';
					$hdr_line++;
				//}
				// RFC2045 - the next line is *assumed* as default 7bit if it is not included
				// FUTURE: Content-Transfer-Encoding:  Needs To Match What is In the Body, i.e. may be qprint
				//$this->mail_out['main_headers'][$hdr_line] =	'Content-Transfer-Encoding: 7bit';
				//$hdr_line++;
				/*!
				@concept 7bit vs. 8bit encoding value in top level headers
				@discussion top level 7bit requires qprinting the body if the body has 8bit chars in it
				ISSUE 1: "it's unnecessary"
				nowdays, most all MTAs and IMAP/POP servers can handle 8bit
				by todays usage, 7bit is quite restrictive, when considering the variety of
				things that may be attached to or carried in a message (and growing)
				[begin digression]
				However, stuffing RFC822 email thru a X500 (?) gateway requires 7bit body,
				which we could do here, at the MUA level, and may possibly require other
				alterations of the message that occur at the gateway, some of which may actually drop
				portions of the message, indeed it's complicated, but rare in terms of total mail volume (?)
				[end digression]
				ISSUE 2: "risks violating RFCs and confusing MTAs"
				setting top level encoding to 7bit when the body actually has 8bit chars is "TOTALLY BAD"
				MTA's will be totally confused by that mis-match, and it violates RFCs
				**More Importantly** this is a coding and functionality issue involved in forwarding:
				in general, when you forward a message you should not alter that message
				if that forwarded message has 8bit chars, I don't think that can be altered
				even to quote-print that forwarded part (i.e. to convert it to 7bit) would be altering it
				I suppose you could base64 encode it, on the theory that it decodes exactly back into
				it's original form, but the practice of base64 encoding non-attachments (i.e. text parts)
				is EXTREMELY rare in my experience (Angles) and still problematic in coding for this.
				I suppose this assumes qprint is possible "lossy" in that the exact original may not be
				exactly the same as said pre-encoded forwarded part, and, after all, it's still altering the part.
				CONCLUSION: Set Top Level Header "Content-Transfer-Encoding" to "8bit"
				because it's easier to code for and less likely to violate RFCs.
				for now send out as 8bit and hope for the best.
				*/
				$this->mail_out['main_headers'][$hdr_line] =	'Content-Transfer-Encoding: 8bit';
				$hdr_line++;

				$this->mail_out['main_headers'][$hdr_line] =	'Content-Disposition: inline';
				$hdr_line++;
				// Content-Description: this is not really a "technical" header
				// it can be used to inform the person reading some summary info
				//$header .= 'Content-description: Mail message body'."\r\n";
			}

			// finish off the main headers
			if ($this->mail_out['msgtype'] != '')
			{
				$this->mail_out['main_headers'][$hdr_line] = 	'X-phpGW-Type: '.$this->mail_out['msgtype'];
				$hdr_line++;
			}
			$this->mail_out['main_headers'][$hdr_line] = 	'X-Mailer: AngleMail for eGroupWare (http://www.egroupware.org) v '.$GLOBALS['phpgw_info']['server']['versions']['phpgwapi'];
			$hdr_line++;

			/*
			// ===== DEBUG =====
			//echo '<br>';
			//echo '<br>=== mail_out ===<br>';
			//$dubug_info = serialize($this->mail_out);
			//$dubug_info = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($dubug_info);
			//echo $dubug_info;
			//echo '<br>';
			echo '<br> var dump mail_out <br><pre>';
			var_dump($this->mail_out);
			echo '</pre>';
			$GLOBALS['phpgw']->common->phpgw_exit();
			return;
			// ===== DEBUG =====
			*/

			// ----  Send It   -----
			$returnccode = $GLOBALS['phpgw']->mail_send->smail_2822($this->mail_out);

			/*
			// ===== DEBUG =====
			echo '<br>';
			echo 'retain_copy: '.serialize($GLOBALS['phpgw']->mail_send->retain_copy);
			echo '<br>=== POST SEND ===<br>';
			echo '<pre>'.$GLOBALS['phpgw']->msg->htmlspecialchars_encode($GLOBALS['phpgw']->mail_send->assembled_copy).'</pre>';
			echo '<br>';
			// ===== DEBUG =====
			*/


			//  -------  Put in "Sent" Folder, if Applicable  -------
			$skip_this = False;
			//$skip_this = True;

			if (($skip_this == False)
			&& ($returnccode)
			&& ($GLOBALS['phpgw']->msg->get_isset_pref('use_sent_folder')))
			{
					$success = $this->copy_to_sent_folder();
			}

			// use for DEBUGGING prevents page redirect
			//$returnccode = False;
			//$success = False;

			// ----  Redirect on Success, else show Error Report   -----
			// what folder to go back to (the one we came from)
			// Personally, I think people should go back to the INBOX after sending an email
			// HOWEVER, we will go back to the folder this message came from (if available)
			if (($GLOBALS['phpgw']->msg->get_isset_arg('["msgball"]["folder"]'))
			&& ($GLOBALS['phpgw']->msg->get_isset_arg('["msgball"]["acctnum"]')))
			{
				$fldball_candidate['folder'] = $GLOBALS['phpgw']->msg->get_arg_value('["msgball"]["folder"]');
				$fldball_candidate['acctnum'] = (int)$GLOBALS['phpgw']->msg->get_arg_value('["msgball"]["acctnum"]');
			}
			elseif (($GLOBALS['phpgw']->msg->get_isset_arg('["fldball"]["folder"]'))
			&& ($GLOBALS['phpgw']->msg->get_isset_arg('["fldball"]["acctnum"]')))
			{
				$fldball_candidate['folder'] = $GLOBALS['phpgw']->msg->get_arg_value('["fldball"]["folder"]');
				$fldball_candidate['acctnum'] = (int)$GLOBALS['phpgw']->msg->get_arg_value('["fldball"]["acctnum"]');
			}
			// did we get useful data
			if ( (isset($fldball_candidate))
			&& ($fldball_candidate['folder'] != '') )
			{
				$fldball_candidate['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($fldball_candidate['folder']);
			}
			else
			{
				$fldball_candidate['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out('INBOX');
				$fldball_candidate['acctnum'] = (int)$GLOBALS['phpgw']->msg->get_acctnum();
			}
			$return_to_folder_href = $GLOBALS['phpgw']->link(
						'/index.php',
						'menuaction=email.uiindex.index'
						.'&fldball[folder]='.$fldball_candidate['folder']
						.'&fldball[acctnum]='.$fldball_candidate['acctnum']
						.'&sort='.$GLOBALS['phpgw']->msg->get_arg_value('sort')
						.'&order='.$GLOBALS['phpgw']->msg->get_arg_value('order')
						.'&start='.$GLOBALS['phpgw']->msg->get_arg_value('start'));

			if ($returnccode)
			{
				// Success
				if ($GLOBALS['phpgw']->mail_send->trace_flag > 0)
				{
					// for debugging
					echo '<html><body>'."\r\n";
					echo '<h2>Here is the communication from the MUA(phpgw) <--> MTA(smtp server) trace data dump</h2>'."\r\n";
					echo '<h3>trace data flag set to ['.(string)$GLOBALS['phpgw']->mail_send->trace_flag.']</h3>'."\r\n";
					echo '<pre>'."\r\n";
					print_r($GLOBALS['phpgw']->mail_send->trace_data);
					echo '</pre>'."\r\n";
					echo '<p>&nbsp;<br></p>'."\r\n";
					echo '<p>To go back to the msg list, click <a href="'.$return_to_folder_href.'">here</a></p><br>';
					echo '</body></html>';
					$this->send_message_cleanup();
				}
				else
				{
					// unset some vars (is this necessary?)
					$this->send_message_cleanup();
					// redirect the browser to the index page for the appropriate folder
					//header('Location: '.$return_to_folder_href);
					$GLOBALS['phpgw']->redirect($return_to_folder_href);
					// kill the rest of this script
					if (is_object($GLOBALS['phpgw']->msg))
					{
						// close down ALL mailserver streams
						$GLOBALS['phpgw']->msg->end_request();
						// destroy the object
						$GLOBALS['phpgw']->msg = '';
						unset($GLOBALS['phpgw']->msg);
					}
					// shut down this transaction
					$GLOBALS['phpgw']->common->phpgw_exit(False);
				}
			}
			else
			{
				// ERROR - mail NOT sent
				echo '<html><body>'."\r\n";
				echo '<h2>Your message could <b>not</b> be sent!</h2>'."\r\n";
				echo '<h3>The mail server returned:</h3>'."\r\n";
				echo '<pre>';
				print_r($GLOBALS['phpgw']->mail_send->err);
				echo '</pre>'."\r\n";
				echo '<p>To go back to the msg list, click <a href="'.$return_to_folder_href.'">here</a> </p>'."\r\n";
				echo '</body></html>';
				$this->send_message_cleanup();
			}
		}

	}
?>

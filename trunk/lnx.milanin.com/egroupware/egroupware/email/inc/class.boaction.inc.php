<?php
	/**************************************************************************\
	* AngleMail - email BO Class for Message Actions					*
	* http://www.anglemail.org									*
	* Written by Angelo (Angles) Puglisi <angles@aminvestments.com>		*
	* Copyright (C) 2001, 2002 Angelo Tony Puglisi (Angles)				*
	* --------------------------------------------							*
	*  This program is free software; you can redistribute it and/or modify it		*
	*  under the terms of the GNU General Public License as published by the	*
	*  Free Software Foundation; either version 2 of the License, or (at your		*
	*  option) any later version.								*
	\**************************************************************************/

	/* $Id: class.boaction.inc.php,v 1.25 2004/04/18 11:43:08 angles Exp $ */
	
	/*!
	@class boaction
	@abstract Processes client side requests for actions like deleting or moving mail, getting attachments and 
	for viewing html mail. Also contains code for an alternative to the redirect as a way to go to the next page view. 
	*/	
	class boaction
	{
		var $public_functions = array(
			'delmov' => True,
			'get_attach' => True,
			'view_html' => True,
			'clearcache' => True
		);
		// class var to hold content to be downloaded
		var $output_data='';
		// if bomessage wants this preserves, we detect that and store it here
		var $no_fmt='';
		// debug level 0 is none, levels  1, 2, 3 is typical
		// debug level 4 has special side effects
		var $debug = 0;
		
		var $debug_new_env = 0;
		//var $debug_new_env = 3;
		//var $debug_new_env = 4;
		
		var $msg_bootstrap;
		// if moving or deleting more than "big_move_threshold", smart caching temporarily is disabled
		// that much activity is going to be slower no matter what, might as well get fresh data on next page view
		//var $big_move_threshold = 95;
		// reduce this until is is proven that a larger number actually makes something faster
		// MOVED TO MSG CLASS
		//var $big_move_threshold = 10;
		var $browser;
		var $redirect_to = '';
		var $redirect_if_error = '';
		var $error_str = '';
		
		var $expected_args=array();
		var $new_args_uri='';
		var $new_args_env=array();
		
		// if getting the next page is a problem, set this to true
		var $use_old_redirect_method=False;
		//var $use_old_redirect_method=True;
		
		var $next_obj;
		
		function boaction()
		{
			//return;
		}
		
		/*!
		@function delmov
		@abstract used to delete or move messages, single message or groups of messages
		@author Angles and previous authors
		@discussion after a message delete or move is handled, this function redirect the browser back to 
		the appropiate page, and puts some data in the URI which the mail_msg class translates into a 
		report to the user recapping what actions just took place. ALSO, note with multiple accounts or with 
		filtering, it may be possible this function is processing a batch of messages that may be from or going to 
		different folders, accounts, and servers. Both here and at the mail_msg move and delete functions, some 
		grouping of message numbers is attempted, but this could be improved upon. 
		@access private
		*/
		function delmov()
		{
			//if ($this->debug > 0) { echo 'ENTERING email.boaction.delmov'.'<br>'; }
			
			// make sure we have msg object and a server stream
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			$this->msg_bootstrap->ensure_mail_msg_exists('email.boaction.delmov', $this->debug);
			if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('ENTERING email.boaction.delmov'.'<br>'); }
			
			// initialize this to an "ignore me" value, we change it later only if it should have a meaning
			// MOVED TO MSG CLASS
			//$initial_session_cache_extreme = '-1';
			
			// get the not set value, usually '-1', 
			// because php False, empty and 0 are too similar we use this instead, where a value of -1 is unlikely
			if (isset($GLOBALS['phpgw']->msg->not_set))
			{
				$not_set = $GLOBALS['phpgw']->msg->not_set;
			}
			else
			{
				$not_set = '-1';
			}
			
			// make an error report URL
			$this->redirect_if_error = $GLOBALS['phpgw']->link('/index.php',$GLOBALS['phpgw']->msg->get_arg_value('index_menuaction'));
			
			$folder_info = array();
			$folder_info = $GLOBALS['phpgw']->msg->get_folder_status_info();
			$totalmessages = $folder_info['number_all'];
			
			// ---- MOVE (Multiple) Messages from folder to folder   -----
			if ($GLOBALS['phpgw']->msg->get_arg_value('what') == "move")
			{
				/*!
				@capability boaction.delmov code for moving messages 
				@abstract Inside function "delmov", when GPC var "what" has value "move" 
				@param get_arg_value("delmov_list") (structured array) list of msgball items that will be moved. 
				@param get_arg_value("to_fldball") (array of type fldball) the destination folder for (all) the move(s). 
				@param get_arg_value("move_postmove_goto") complete URI that we should redirect 
				the browser to after the move is done. Provided by uimessage page, it is a "smart" value, 
				the result of some logic in bomessage to determine what message the user should read 
				after the current message is moved, based on data from the user prefs and the msgball list 
				and the nav data used to make the prev next arrows on that uimessage page. NOTE: if 
				this function is NOT called by the uimessage page, this param "move_postmove_goto" 
				will NOT exist, see discussion for that case.
				@discussion This function "delmov" has one code block to handle moving messages, 
				either one or more messages, the same code block is used. Note that the code that 
				handles deletes has different blocks for single message delete vs. multiple message 
				deletes, not the case with moves. The params are gathered from GPC values (typically) 
				and put into OOP accessable "arg" values, use "get_arg_value" to get the params. 
				The inline docparser may number the params in some order, but in reality the 
				params used in this code block are not expected to be in any particular order. 
				WHEN CALLED: 
				(1) generally called by the uiindex "move selected messages to" listbox onChange action. 
				(2) also can be called by uimessage "move this message into" listbox onChange action. 
				If NOT called by uimessage, then param "move_postmove_goto" does not exist, 
				since we were called probably by a folder index page we should return so _some_ related 
				folder index page after the move is done, i.e. redirect the browser to a folder index page instead 
				of a uimessage page. That folder name  is hackishly obtained by getting the "folder" value from the first 
				msgball in the "delmov_list", on the assumption that this is the folder the user was looking at 
				before the move was issued. This works as long as the message list is all from the same folder, 
				which is most likely at this point. HOWEVER, future search results with messages from different 
				folders and or accounts will require a little better handling of this. NOTE that if an folder index 
				page had all its messages moved, and there are no other messages to show for that particular 
				"nextmatches" view, then the code _SHOULD_ page back to where there are messages to show, 
				BUT this is not done yet. 
				*/
				if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: get_arg_value(what) == "move") <br>'); }
				
				/*
				$fromacctnum = (int)$GLOBALS['phpgw']->msg->get_arg_value('acctnum');
				$tofolder = $GLOBALS['phpgw']->msg->prep_folder_in($GLOBALS['phpgw']->msg->get_arg_value('tofolder'));
				$toacctnum = (int)$GLOBALS['phpgw']->msg->get_arg_value('toacctnum');
				// report number messages moved (will be made = 0 if error below)
				//$tm = count($GLOBALS['phpgw']->msg->get_arg_value('msglist'));
				// mail_move accepts a single number (5); a comma seperated list of numbers (5,6,7,8); or a range with a colon (5:8)
				//$msgs = $GLOBALS['phpgw']->msg->get_arg_value('msglist') ? implode($GLOBALS['phpgw']->msg->get_arg_value('msglist'), ",") : $GLOBALS['phpgw']->msg->get_arg_value('msglist');
				$delmov_list = $GLOBALS['phpgw']->msg->get_arg_value('delmov_list');
				// tm = "Total Moved" indicator
				$tm = count($delmov_list);
				$msgs = '';
				for($i=0;$i<count($delmov_list);$i++)
				{
					if ($msgs != '')
					{
						$prefix = ',';
					}
					$msgs .= $prefix . $delmov_list[$i]['msgball']['msgnum'];
				}
				$did_move = $GLOBALS['phpgw']->msg->phpgw_mail_move($msgs, $tofolder);
				*/
				
				$delmov_list = $GLOBALS['phpgw']->msg->get_arg_value('delmov_list');
				$to_fldball = $GLOBALS['phpgw']->msg->get_arg_value('to_fldball');
				// WHY URLDECODE SO SOON?
				//$to_fldball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_in($to_fldball['folder']);
				$to_fldball['acctnum'] = (int)$to_fldball['acctnum'];
				
				// tm = "Total Moved" indicator
				$tm = count($delmov_list);
				// is this a "big move" as far as the "smart caching" is concerned?
				// MOVED TO MSG CLASS
				//if (count($delmov_list) > $this->big_move_threshold)
				//{
				//	if ($this->debug > 0) { echo 'email.boaction.delmov: LINE '.__LINE__.' $this->big_move_threshold ['.$this->big_move_threshold.'] exceeded, call "->msg->event_begin_big_move" to notice event of impending big batch moves or deletes<br>'; }
				//	$initial_session_cache_extreme = $GLOBALS['phpgw']->msg->event_begin_big_move(array(), 'email.boaction.delmov: LINE '.__LINE__);
				//}
				//else
				//{
				//	// this "-1" tells us no big move was done
				//	$initial_session_cache_extreme = '-1';
				//}
				
				for ($i = 0; $i < count($delmov_list); $i++)
				{
					if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: in mail move loop ['.(string)($i+1).'] of ['.$tm.']<br>'); }
					$mov_msgball = $delmov_list[$i];
					// WHY URLDECODE SO SOON?
					//$mov_msgball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_in($mov_msgball['folder']);
					$mov_msgball['acctnum'] = (int)$mov_msgball['acctnum'];
					$did_move = False;
					//if ($this->debug > 2) { echo 'email.boaction.delmov: calling  $GLOBALS[phpgw]->msg->interacct_mail_move('.serialize($mov_msgball).', '.serialize($to_fldball).'<br>'; }
					//$did_move = $GLOBALS['phpgw']->msg->interacct_mail_move($mov_msgball, $to_fldball);
					if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: calling  $GLOBALS[phpgw]->msg->industrial_interacct_mail_move('.serialize($mov_msgball).', '.serialize($to_fldball).'<br>'); }
					// single move, NO NEED to use the move grouping stuff, NOTE $tm was filled above as count($delmov_list)
					// MOVED TO FLUSH MOVES LOGIG
					//if ($tm == 1)
					//{
					//	if ($this->debug > 1) { echo 'email.boaction.delmov: (single move $tm: ['.$tm.']) calling  $GLOBALS[phpgw]->msg->single_interacct_mail_move('.serialize($mov_msgball).', '.serialize($to_fldball).'<br>'; }
					//	$did_move = $GLOBALS['phpgw']->msg->single_interacct_mail_move($mov_msgball, $to_fldball);
					//}
					//else
					//{
						if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: calling  $GLOBALS[phpgw]->msg->industrial_interacct_mail_move('.serialize($mov_msgball).', '.serialize($to_fldball).'<br>'); }
						$did_move = $GLOBALS['phpgw']->msg->industrial_interacct_mail_move($mov_msgball, $to_fldball);
					//}
					if ($did_move == False)
					{
						// error
						if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): ***ERROR**** $GLOBALS[phpgw]->msg->industrial_interacct_mail_move() returns FALSE, ERROR, break out of loop<br>'
								.' * * Server reports error: '.$GLOBALS['phpgw']->msg->phpgw_server_last_error().'<br>'); }
						break;
					}
					else
					{
						if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): $GLOBALS[phpgw]->msg->industrial_interacct_mail_move() returns True<br>'); }
						//$did_expunge = False;
						//$did_expunge = $GLOBALS['phpgw']->msg->phpgw_expunge($mov_msgball['acctnum'], $mov_msgball);
						//if ($this->debug > 2) { echo 'email.boaction.delmov: $GLOBALS[phpgw]->msg->phpgw_expunge() returns '.serialize($did_expunge).'<br>'; }
					}
				}
				
				// ok, done moving, now expunge, "industrial_interacct_mail_move" uses ""
				if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): done moving, now call $GLOBALS[phpgw]->msg->expunge_expungable_folders<br>'); }
				$did_expunge = False;
				$did_expunge = $GLOBALS['phpgw']->msg->expunge_expungable_folders('email.boaction.delmov LINE '.__LINE__);
				if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): $GLOBALS[phpgw]->msg->expunge_expungable_folders() returns ['.serialize($did_expunge).']<br>'); }
				
				if (! $did_move)
				{
					// ERROR: report ZERO messages moved
					$tm = 0;
					//echo 'Server reports error: '.$GLOBALS['phpgw']->msg->dcom->server_last_error();
				}
				// report folder messages were moved to
				//$tf = $GLOBALS['phpgw']->msg->prep_folder_out($to_fldball['folder']);
				// folder in this array was never changed from its "prepped out" state, it is still urlencoded from when we first picked it up
				$tf = $to_fldball['folder'];
				//echo 'boaction: $tf ['.$tf.'] <br>';
				
				// folder or message we should go back to
				if (($GLOBALS['phpgw']->msg->get_isset_arg('move_postmove_goto'))
				&& ($GLOBALS['phpgw']->msg->get_arg_value('move_postmove_goto') != ''))
				{
					// THIS MEANS WE WERE CALLED BY UIMESSAGE
					// treat the post-move navigation like a "delete_single_msg", as per data passed to us from that page
					$move_postmove_goto = $GLOBALS['phpgw']->msg->get_arg_value('move_postmove_goto');
					if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): move single *called by uimessage*: $move_postmove_goto: : '.$move_postmove_goto.'<br>'); }
					// ----  "Go To Previous Message" Handling  -----
					// these insrustions passed from uimessage when prev_next_navigation is obtained anyway
					$this->redirect_to = $move_postmove_goto;
					if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: ('.__LINE__.') move single *called by uimessage*: determination of $this->redirect_to : ['.$this->redirect_to.']<br>'); }
				}
				else
				{
					//$return_to_fldball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($delmov_list[0]['folder']);
					// folder in this array was never changed from its "prepped out" state, it is still urlencoded from when we first picked it up
					$return_to_fldball['folder'] = $delmov_list[0]['folder'];
					//echo 'boaction: $return_to_fldball[folder] ['.$return_to_fldball['folder'].'] <br>';
					$return_to_fldball['acctnum'] = $delmov_list[0]['acctnum'];
					
					$this->redirect_to = $GLOBALS['phpgw']->link(
									'/index.php',
									 'menuaction=email.uiindex.index'
									.'&fldball[folder]='.$return_to_fldball['folder']
									.'&fldball[acctnum]='.$return_to_fldball['acctnum']
									.'&tm='.$tm
									.'&tf='.$tf
									.'&sort='.$GLOBALS['phpgw']->msg->get_arg_value('sort')
									.'&order='.$GLOBALS['phpgw']->msg->get_arg_value('order')
									.'&start='.$GLOBALS['phpgw']->msg->get_arg_value('start'));
					
					if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): NOT called by uimessage, determination of $this->redirect_to : ['.$this->redirect_to.']<br>'); }
				}
			}
			// ---- DELETE (MULTIPLE) MESSAGES ----
			elseif ($GLOBALS['phpgw']->msg->get_arg_value('what') == 'delall')
			{
				/*!
				@capability boaction.delmov code for deleting multiple messages 
				@abstract Inside function "delmov", when GPC var "what" has value "delall" 
				@param get_arg_value("delmov_list") (structured array) list of msgball items that will be deleted. 
				@discussion WHEN CALLED: this is called from the index page after you check some boxes and click "delete" button. 
				Folder index page to resirect to after the deletes are done is obtained using the LAST msgball in the "delmov_list", 
				using its "folder" and "acctnum" values. 
				*/
				
				if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: get_arg_value(what) == "delall") <br>'); }
				// this is called from the index pge after you check some boxes and click "delete" button
				
				$delmov_list = $GLOBALS['phpgw']->msg->get_arg_value('delmov_list');
				// is this a "big move" as far as the "smart caching" is concerned?
				// MOVED TOP MSG CLASS
				//if (count($delmov_list) > $this->big_move_threshold)
				//{
				//	if ($this->debug > 0) { echo 'email.boaction.delmov: LINE '.__LINE__.' $this->big_move_threshold ['.$this->big_move_threshold.'] exceeded, call "->msg->event_begin_big_move" to notice event of impending big batch moves or deletes<br>'; }
				//	$initial_session_cache_extreme = $GLOBALS['phpgw']->msg->event_begin_big_move(array(), 'email.boaction.delmov: LINE '.__LINE__);
				//}
				//else
				//{
				//	// this "-1" tells us no big move was done
				//	$initial_session_cache_extreme = '-1';
				//}
				
				$loops = count($delmov_list);
				for ($i = 0; $i < $loops; $i++)
				{
					if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: (delete) in mail delete loop ['.(string)($i+1).'] of ['.$loops.']<br>'); }
					$this_msgnum = $delmov_list[$i]['msgnum'];
					// was_in_folder is used in Trash handling in the ->phpgw_delete function
					// if a message "was_in_folder" Trash, it gets deleted for real, no option to move to Trash in that case
					$was_in_folder = $delmov_list[$i]['folder'];
					$was_in_folder_acctnum = (int)$delmov_list[$i]['acctnum'];
					$did_delete = $GLOBALS['phpgw']->msg->phpgw_delete($this_msgnum,'',$was_in_folder);
					if ($did_delete == False)
					{
						// error
						if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: (delete) ***ERROR**** $GLOBALS[phpgw]->msg->phpgw_delete() returns FALSE, ERROR, break out of loop<br>'
								.' * * Server reports error: '.$GLOBALS['phpgw']->msg->phpgw_server_last_error().'<br>'); }
						break;
					}
					else
					{
						if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: (delete) $GLOBALS[phpgw]->msg->phpgw_delete() returns True (so it buffered the command, really does not mean anything not that we buffer commands)<br>'); }
						
						//if ($this->debug > 0) { echo 'email.boaction.delmov: (delete) calling $GLOBALS[phpgw]->msg->phpgw_expunge('.$delmov_list[$i]['acctnum'].', $delmov_list[$i])<br>'; }
						//$did_expunge = False;
						//$did_expunge = $GLOBALS['phpgw']->msg->phpgw_expunge((int)$delmov_list[$i]['acctnum'], $delmov_list[$i]);
						//if ($this->debug > 2) { echo 'email.boaction.delmov: (delete) $GLOBALS[phpgw]->msg->phpgw_expunge('.$delmov_list[$i]['acctnum'].') returns '.serialize($did_expunge).'<br>'; }
					}
				}
				
				// ok, done deleting, now expunge
				if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): done deleting, now call $GLOBALS[phpgw]->msg->expunge_expungable_folders<br>'); }
				$did_expunge = False;
				$did_expunge = $GLOBALS['phpgw']->msg->expunge_expungable_folders('email.boaction.delmov LINE '.__LINE__);
				if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): $GLOBALS[phpgw]->msg->expunge_expungable_folders() returns ['.serialize($did_expunge).']<br>'); }

				$totaldeleted = $i;
				//$GLOBALS['phpgw']->msg->phpgw_expunge();
				$this->redirect_to = $GLOBALS['phpgw']->link(
								'/index.php',
								 'menuaction=email.uiindex.index'
								.'&fldball[folder]='.$GLOBALS['phpgw']->msg->prep_folder_out($was_in_folder)
								.'&fldball[acctnum]='.$was_in_folder_acctnum
								.'&td='.$totaldeleted
								.'&sort='.$GLOBALS['phpgw']->msg->get_arg_value('sort')
								.'&order='.$GLOBALS['phpgw']->msg->get_arg_value('order')
								.'&start='.$GLOBALS['phpgw']->msg->get_arg_value('start'));
				/*
				// Experimental:
				// NO REDIRECT - DIRECTLY MANUFACTURE THE NEXT PAGE VIEW RIGHT NOW
				// NAME  THE DESIRED FLDBALL
				$new_fldball = array();
				$new_fldball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($was_in_folder);
				$new_fldball['acctnum'] = $was_in_folder_acctnum;
				// GATHER THE OTHER ARGS WE WANT IN THE NEW PAGEVIEW
				$new_args_env = array(
					'td'	=> $totaldeleted,
					'sort'  => $GLOBALS['phpgw']->msg->get_arg_value('sort'),
					'order'  => $GLOBALS['phpgw']->msg->get_arg_value('order'),
					'start'  => $GLOBALS['phpgw']->msg->get_arg_value('start')
				);
				// UNSET ARGS WE USED IN THIS PAGE BUT ARE NO LONGER NEEDED
				$GLOBALS['phpgw']->msg->unset_arg('delmov_list', $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->unset_arg('to_fldball_fake_uri', $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->unset_arg('to_fldball', $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->unset_arg('sort', $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->unset_arg('order', $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->unset_arg('start', $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->unset_arg('what', $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->unset_arg('folder', $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->unset_arg('acctnum', $new_fldball['acctnum']);
				// REFILL ARGS WITH NEW PAGE VIEW VALUES
				$GLOBALS['phpgw']->msg->set_acctnum($new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->set_arg_value('fldball', $new_fldball, $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->set_arg_value('folder', $new_fldball['folder'], $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->set_arg_value('td', $new_args_env['td'], $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->set_arg_value('sort', $new_args_env['sort'], $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->set_arg_value('order', $new_args_env['order'], $new_fldball['acctnum']);
				$GLOBALS['phpgw']->msg->set_arg_value('start', $new_args_env['start'], $new_fldball['acctnum']);
				// IF A "BIG MOVE", THEN TURN BACK ON THE SMART CACHE
				if ((isset($initial_session_cache_extreme))
				&& ($initial_session_cache_extreme != '-1'))
				{
					$GLOBALS['phpgw']->msg->session_cache_extreme = $initial_session_cache_extreme;
				}
				// MAKE THE INDEX PAGE OBJECT
				$this->next_obj = CreateObject('email.uiindex');
				// CALL THE FUNCTION THAT DISPLAYS THE PAGE VIEW
				$this->next_obj->index();
				// close down ALL mailserver streams
				if (is_object($GLOBALS['phpgw']->msg))
				{
					$GLOBALS['phpgw']->msg->end_request();
				}
				// shut down this transaction
				$GLOBALS['phpgw']->common->phpgw_exit(False);
				*/
				
			}
			// ---- DELETE A SINGLE MESSAGE  ----
			elseif ($GLOBALS['phpgw']->msg->get_arg_value('what') == "delete_single_msg")
			{
				/*!
				@capability boaction.delmov code for deleting a single messages 
				@abstract Inside function "delmov", when GPC var "what" has value "delete_single_msg" 
				@param get_arg_value("msgball") (array of type msgball) the msgball item to delete. 
				@discussion WHEN CALLED: this is called from the uimessage page when you click "delete" image, 
				(the image grouped with the repy, replyall, and forward actions) 
				Redirects to a uimessage page (if possible) afrer the delete, which is determined by using 
				the "msg->prev_next_navigation" return data. Will redirect to the folder index page if 
				that was the last message to view (with respect to user prefs on sort and order). 
				*/
				
				// if preserving no_fmt then add it to every navigation (prev, next) links
				// if no_fmt=1 is in the args it is because bomesage wants us to preserve it
				if (($GLOBALS['phpgw']->msg->get_isset_arg('no_fmt'))
				&& ($GLOBALS['phpgw']->msg->get_arg_value('no_fmt') != ''))
				{
					$this->no_fmt = '&no_fmt=1';
				}
				if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): get_arg_value(what) == "delete_single_msg") <br>'); }
				// called by clicking the "X" dutton while reading an individual message
				$msgball = $GLOBALS['phpgw']->msg->get_arg_value('msgball');
				if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): delete_single_msg: pre-delete $msgball[] DUMP:', $msgball); }
				
				// BEFORE we delete, if there is no mext message, then we will go back to index page
				$nav_data = $GLOBALS['phpgw']->msg->prev_next_navigation($folder_info['number_all']);
				if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): delete_single_msg: pre-delete $nav_data[] DUMP:', $nav_data); }
				// ----  "Go To Previous Message" Handling  -----
				/*if ($nav_data['prev_msg'] != $not_set)
				{

					$this->redirect_to = $GLOBALS['phpgw']->link(
						'/index.php',
						 'menuaction=email.uimessage.message'
						.'&'.$nav_data['prev_msg']['msgball']['uri']
						.'&sort='.$GLOBALS['phpgw']->msg->get_arg_value('sort')
						.'&order='.$GLOBALS['phpgw']->msg->get_arg_value('order')
						.'&start='.$GLOBALS['phpgw']->msg->get_arg_value('start')
						.$this->no_fmt);
				}
				else
				{*/
					// post-delete back to index page - 
					// LEX: I changed this cause i think its nicer behaviour
					// to move to the nex message instead of going to index....this way we save clicks
					// if anyone has an issue with this, ill make a preference for it
					$this->clearcache();
					/*
					$this->redirect_to = $GLOBALS['phpgw']->link(
							'/index.php',
							'menuaction=email.uiindex.index'
							.'&fldball[folder]='.$GLOBALS['phpgw']->msg->prep_folder_out()
							.'&fldball[acctnum]='.$GLOBALS['phpgw']->msg->get_acctnum()
							.'&sort='.$GLOBALS['phpgw']->msg->get_arg_value('sort')
							.'&order='.$GLOBALS['phpgw']->msg->get_arg_value('order')
							.'&start='.$GLOBALS['phpgw']->msg->get_arg_value('start'));
					*/
				//}
				if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): delete_single_msg: pre-delete determination of $this->redirect_to : ['.$this->redirect_to.']<br>'); }
				
				
				if ($this->debug > 3)
				{
					$GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: delete_single_msg ('.__LINE__.'): debug flag = 4 or higher, _SKIP_ the delete and expunge action<br>');
				}
				else
				{
					// ok, now do the delete
					if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: delete_single_msg: ('.__LINE__.') : (single delete) calling $GLOBALS[phpgw]->msg->phpgw_delete('.$msgball['msgnum'].', " ",'.$msgball['folder'].', '.$msgball['acctnum'].', True) '); } 
					// True = just a single delete call, don't use the buffer commands
					// " just a single delete call" logic MOVED TO BUFFERED COMMANDS function
					//$GLOBALS['phpgw']->msg->phpgw_delete($msgball['msgnum'],'',$msgball['folder'], (int)$msgball['acctnum'], True);
					$GLOBALS['phpgw']->msg->phpgw_delete($msgball['msgnum'],'',$msgball['folder'], (int)$msgball['acctnum']);
					// now do the expunge, both IMAP and POP3 require this, or the message is not really deleted
					//if ($this->debug > 1) { echo 'email.boaction.delmov: delete_single_msg: ('.__LINE__.') : calling $GLOBALS[phpgw]->msg->phpgw_expunge('.$msgball['acctnum'].', $msgball) '; } 
					// MOVED to "expunge_expungable_folders"
					//$GLOBALS['phpgw']->msg->phpgw_expunge((int)$msgball['acctnum'], $msgball);
					
					// ok, done deleting, now expunge
					if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: delete_single_msg: ('.__LINE__.'): done deleting, now call $GLOBALS[phpgw]->msg->expunge_expungable_folders<br>'); }
					$did_expunge = False;
					$did_expunge = $GLOBALS['phpgw']->msg->expunge_expungable_folders('email.boaction.delmov (delete_single_msg) LINE '.__LINE__);
					if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: delete_single_msg: ('.__LINE__.'): $GLOBALS[phpgw]->msg->expunge_expungable_folders() returns ['.serialize($did_expunge).']<br>'); }
				}
			}
			// ---- EMPTY FOLDER ex. EMPTY TRASH ----
			elseif ($GLOBALS['phpgw']->msg->get_arg_value('what') == 'empty_folder')
			{
				/*!
				@capability boaction.empty_folder  code for emptying trash folder for example
				@abstract Inside function "delmov", when GPC var "what" has value "empty_folder" 
				@param get_arg_value("fldball") (array of type fldball) the folder to delete all messages in.
				@discussion WHEN CALLED: call from trash folder, and provide these 2 params.
				*/
				
				if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): get_arg_value(what) == "empty_folder") <br>'); }
				// this is called from the index pge after you check some boxes and click "delete" button
				$fldball = array();
				$fldball = $GLOBALS['phpgw']->msg->get_arg_value('fldball');
				if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): empty_folder: pre-delete $fldball[] DUMP:', $fldball); }
				
				// get general stats on the folder
				$folder_info = $GLOBALS['phpgw']->msg->get_folder_status_info($fldball);
				if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): empty_folder: pre-delete $folder_info DUMP:', $folder_info); }
				
				if ($folder_info['number_all'] == 0)
				{
					// nothing to delete, folder already empty
					if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): empty_folder: nothing to delete, folder already empty <br>'); }
					// used for number deleted message
					$i = 0;
					// used for the redirection back to the page
					$this_acctnum = (int)$fldball['acctnum'];
					// do we need to prep folder out on a fldball we just got?
					$this_folder = $GLOBALS['phpgw']->msg->prep_folder_out($fldball['folder']);
				}
				else
				{
					// get msgball list of the folder we are to clean
					// although we MUST be in the folder in question,  we still supply folder and acctnum specifically
					$del_msgball_list = array();
					$del_msgball_list = $GLOBALS['phpgw']->msg->get_msgball_list($fldball['acctnum'], $fldball['folder']);
					if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): empty_folder: pre-delete $del_msgball_list DUMP:', $del_msgball_list); }
					
					$loops = count($del_msgball_list);
					for ($i = 0; $i < $loops; $i++)
					{
						if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: (empty_folder) in mail delete loop ['.(string)($i+1).'] of ['.$loops.']<br>'); }
						// turn the string URI type data into a single msgball item in its full  array form.
						$uri_msgball = $del_msgball_list[$i];
						if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): empty_folder: $uri_msgball ['.htmlspecialchars($uri_msgball).']: <br>'); }
						$full_msgball = $GLOBALS['phpgw']->msg->ball_data_parse_str($uri_msgball);
						if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: (empty_folder) in mail delete loop ['.(string)($i+1).'] of ['.$loops.'], $full_msgball DUMP:', $full_msgball); }
						$this_msgnum = $full_msgball['msgnum'];
						$this_folder = $full_msgball['folder'];
						$this_acctnum = (int)$full_msgball['acctnum'];
						$did_delete = $GLOBALS['phpgw']->msg->phpgw_delete($this_msgnum, '', $this_folder,$this_acctnum);
						if ($did_delete == False)
						{
							// error
							if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: (empty_folder) ***ERROR**** $GLOBALS[phpgw]->msg->phpgw_delete() returns FALSE, ERROR, break out of loop<br>'
									.' * * Server reports error: '.$GLOBALS['phpgw']->msg->phpgw_server_last_error().'<br>'); }
							break;
						}
						else
						{
							if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov: (empty_folder) $GLOBALS[phpgw]->msg->phpgw_delete() returns True (so it buffered the command, really does not mean anything not that we buffer commands)<br>'); }
						}
					}
					
					// ok, done deleting, now expunge
					if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): done empty_folder, now call $GLOBALS[phpgw]->msg->expunge_expungable_folders<br>'); }
					$did_expunge = False;
					$did_expunge = $GLOBALS['phpgw']->msg->expunge_expungable_folders('email.boaction.delmov empty_folder LINE '.__LINE__);
					if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov "empty_folder"('.__LINE__.'): $GLOBALS[phpgw]->msg->expunge_expungable_folders() returns ['.serialize($did_expunge).']<br>'); }
				}
				
				$totaldeleted = $i;
				$this->redirect_to = $GLOBALS['phpgw']->link(
								'/index.php',
								 'menuaction=email.uiindex.index'
								.'&fldball[folder]='.$this_folder
								.'&fldball[acctnum]='.$this_acctnum
								.'&td='.$totaldeleted
								.'&sort='.$GLOBALS['phpgw']->msg->get_arg_value('sort')
								.'&order='.$GLOBALS['phpgw']->msg->get_arg_value('order')
								.'&start='.$GLOBALS['phpgw']->msg->get_arg_value('start'));
			}
			else
			{
				if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): get_arg_value(what) == unknown_value<br>'); }
				$error_str = '<p><center><b>'.lang('UNKNOWN ACTION')."<br> \r\n"
						.'called from '.$GLOBALS['PHP_SELF'].', delmov()'."<br> \r\n"
						.'</b></center></p>'."<br> \r\n";
				$this->redirect_to = $this->redirect_if_error;
			}
			
			// GOTO NECT PAGEVIEW VIA REDIRECT OR OBJECT CALL
			if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): about to enter logic to display page defined in this URI: $this->redirect_to ['.$this->redirect_to.']<br>'); }
			
			/*!
			@capability use_old_redirect_method
			@abstract very old school and wasteful way to feed the next page
			@discussion if this->use_old_redirect_method is set to true, then old style redirect will 
			be used to doto the next page. This is slow and wasteful but may be necessary if the 
			bew object call method is causing problems.
			*/
			if ($this->use_old_redirect_method == True)
			{
				if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): EXITING with OLD REDIRECT CODE : $this->use_old_redirect_method: ['.serialize($this->use_old_redirect_method).']<br>'); }
				$GLOBALS['phpgw']->redirect($this->redirect_to);
				// kill this script, we re outa here...
				if (is_object($GLOBALS['phpgw']->msg))
				{
					$GLOBALS['phpgw']->msg->end_request();
					$GLOBALS['phpgw']->msg = '';
					unset($GLOBALS['phpgw']->msg);
				}
				$GLOBALS['phpgw']->common->phpgw_exit(False);
			}
			
			
			// IF WE GET HERE... continue with new method ...
			// args we expect to see which would be needed by the next pageview 
			// NOTE unlike the others, the menuaction element must ne stristr tested
			$expected_args = 
				'index_php?menuaction'.','.
				'fldball'.','.
				'msgball'.','.
				'td'.','.
				'tm'.','.
				'tf'.','.
				'sort'.','.
				'order'.','.
				'start'.','.
				'no_fmt';
				//sessionid
				//kp3
				//domain
			if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): calling $this->set_expected_args($expected_args) ; $expected_args DUMP:', $expected_args); }
			$this->set_expected_args($expected_args);
			// the URI of the redirect string contains data needed for the next page view
			if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): calling $this->set_new_args_uri($this->redirect_to) ; $this->redirect_to ['.$this->redirect_to.']<br>'); }
			$this->set_new_args_uri($this->redirect_to);
			// clear existing args, apply the new arg enviornment, 
			// we get back the menuaction the redirect would have asked for
			if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): calling $this->apply_new_args_env()<br>'); }
			$my_menuaction = $this->apply_new_args_env();
			if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): $my_menuaction is ['.$my_menuaction.'] which was returned from $this->apply_new_args_env()<br>'); }
			// (c) IF A "BIG MOVE", THEN TURN BACK ON THE SMART CACHE
			// MOVED TO MSG CLASS
			//if ((isset($initial_session_cache_extreme))
			//&& ($initial_session_cache_extreme != '-1'))
			//{
			//	if ($this->debug > 1) { echo 'email.boaction.delmov ('.__LINE__.'): $initial_session_cache_extreme is set and is NOT "-1", meaning we issued a "big move" cache event, $initial_session_cache_extreme is ['.serialize($initial_session_cache_extreme).'] <br>'; }
			//	if ($this->debug > 1) { echo 'email.boaction.delmov ('.__LINE__.'): "big move" will turn off session_cache_extreme if it was TRUE, so we undo that for the next page view with: $GLOBALS[phpgw]->msg->session_cache_extreme = $initial_session_cache_extreme<br>'; }
			//	$GLOBALS['phpgw']->msg->session_cache_extreme = $initial_session_cache_extreme;
			//}
			//else
			//{
			//	if ($this->debug > 1) { echo 'email.boaction.delmov ('.__LINE__.'): $initial_session_cache_extreme is either NOT set or is "-1", meaning we did NOT issued a "big move" cache event earlier<br>'; }
			//}
			
			// imitate the next menuaction command with direct object calls
			if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): LEAVING by creating "next_obj" and calling its menuaction verb ...<br>'); }
			if (stristr($my_menuaction, 'uimessage'))
			{
				//// NEW: mail so object group fill may need to be reset
				//// we DO NOT use group data capability on the message view page
				//// this allows the SO class to query for each individual thing it needs
				//// which is OK for a message view, but not for an index page
				//$GLOBALS['phpgw']->msg->so->so_prop_use_group_data(False);
				// NEW: mail so object group fill may need to be reset
				//clear existing data, if any, and reset the query attempt excess counter
				$GLOBALS['phpgw']->msg->so->so_prop_use_group_data(False);
				// then make sure group data capability it turned on
				$GLOBALS['phpgw']->msg->so->so_prop_use_group_data(True);
				// MAKE THE UIMESSAGE PAGE OBJECT
				$this->next_obj = CreateObject('email.uimessage');
				// CALL THE FUNCTION THAT DISPLAYS THE PAGE VIEW
				$this->next_obj->message();
			}
			// else just ASSUME uiindex, it is the most forgiving about missing values, it has fallback defaults to use if needed
			else
			{
				// NEW: mail so object group fill may need to be reset
				//clear existing data, if any, and reset the query attempt excess counter
				$GLOBALS['phpgw']->msg->so->so_prop_use_group_data(False);
				// then make sure group data capability it turned on
				$GLOBALS['phpgw']->msg->so->so_prop_use_group_data(True);
				// MAKE THE INDEX PAGE OBJECT
				$this->next_obj = CreateObject('email.uiindex');
				// CALL THE FUNCTION THAT DISPLAYS THE PAGE VIEW
				$this->next_obj->index();
			}
			// (e) cleanup
			if (is_object($GLOBALS['phpgw']->msg))
			{
				if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): oops, not LEFT yet, cleanup and unset ->msg object<br>'); }
				// close down ALL mailserver streams
				$GLOBALS['phpgw']->msg->end_request();
				// destroy the object
				$GLOBALS['phpgw']->msg = '';
				unset($GLOBALS['phpgw']->msg);
			}
			// shut down this transaction
			if ($this->debug > 0 && is_object($GLOBALS['phpgw']->msg)) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.delmov ('.__LINE__.'): LEAVING for <b>real</b> with $GLOBALS[phpgw]->common->phpgw_exit(False)<br>'); }
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}
		
		/*
		// placeholder for previous test code
		function just_a_placeholder()
		{	
			// NOW GOTO THE NEXT PAGE SPECIFIED IN THIS->REDIRECT_TO 
			// VIA REDIRECT OR DIRECT OBJECT CALL
			if ($this->redirect_to != '')
			{
				if ($this->debug > 0) { echo 'email.boaction.delmov ('.__LINE__.'): next pageview redirect data to use is: ['.$this->redirect_to.']<br>'; } 
				
				// Experimental:
				// NO REDIRECT - DIRECTLY MANUFACTURE THE NEXT PAGE VIEW RIGHT NOW
				// RECOVED ENV DATA FROM THE REDIRECT URI STRING
				$recovered_data = array();
				parse_str($this->redirect_to, $recovered_data);
				
				if ($this->debug > 1) { echo 'email.boaction.delmov ('.__LINE__.'): redirect_to parsed_str $recovered_data DUMP:<pre>'; print_r($recovered_data); echo '</pre>'; } 
				// ALL POSSIBLE VARS WE MIGHT FIND IN THE REDIRECT URI:
				$new_args_env = array(
					'/mail/index_php?menuaction'  => '-1',
					'fldball'	=> '-1',
					'msgball'	=> '-1',
					'td'		=> '-1',
					'tm'		=> '-1',
					'tf'		=> '-1',
					'sort'  	=> '-1',
					'order'		=> '-1',
					'start' 	=> '-1'
				);
				if ($this->debug > 1) { echo 'email.boaction.delmov ('.__LINE__.'): known possible recovered_data elements init $new_args_env DUMP:<pre>'; print_r($new_args_env); echo '</pre>'; } 
				// loop thru KNOWN POSSIBLE new_args_env elements, GATHER the ones that are filled for later use
				reset($new_args_env);
				while(list($key,$value) = each($new_args_env))
				{
					$known_arg = $key;
					if ((isset($recovered_data[$known_arg]))
					&& ((string)$recovered_data[$known_arg] != ''))
					{
						// we have a arg to use for the next page view
						$new_args_env[$key] = $recovered_data[$known_arg];
					}
				}
				reset($new_args_env);
				
				// GET GOOD ACCTNUM FOR THE UNSET COMMANDS BELOW
				// (and also get other useful info while we are at it
				if ($new_args_env['fldball'] != '-1')
				{
					$new_acctnum = (int)$new_args_env['fldball']['acctnum'];
					$new_folder = $new_args_env['fldball']['folder'];
				}
				elseif ($new_args_env['msgball'] != '-1')
				{
					$new_acctnum = (int)$new_args_env['msgball']['acctnum'];
					$new_folder = $new_args_env['msgball']['folder'];
					// IMITATION: during grab_args_gpc, the code add an element [uri] to the existing msgball
					// NOTE that for this uri element, the "folder" string shoulf be urlencoded
					$new_uri_element =	 'msgball[msgnum]='.$new_args_env['msgball']['msgnum']
									.'&msgball[folder]='.urlencode($new_args_env['msgball']['folder'])
									.'&msgball[acctnum]='.$new_args_env['msgball']['acctnum'];
					$new_args_env['msgball']['uri'] = $new_uri_element;
				}
				else
				{
					echo 'email.boaction.delmov: LINE '.__LINE__.': ERROR getting valid acctnum for goto pageview, NO fldball NO msgball found <br>';
				}
				if ($this->debug > 1) { echo 'email.boaction.delmov ('.__LINE__.'): known possible recovered_data elements <b>Post-Gather</b> $new_args_env DUMP:<pre>'; print_r($new_args_env); echo '</pre>'; } 
				if ($this->debug > 1) { echo 'email.boaction.delmov ('.__LINE__.'): will use $new_acctnum ['.$new_acctnum.'];  and $new_folder ['.$new_folder.'], BUT FIRST unset selected existing class args <br>'; }
				
				// UNSET ARGS WE USED IN THIS PAGE BUT ARE NO LONGER NEEDED
				$GLOBALS['phpgw']->msg->unset_arg('delmov_list', $new_acctnum);
				$GLOBALS['phpgw']->msg->unset_arg('to_fldball_fake_uri', $new_acctnum);
				$GLOBALS['phpgw']->msg->unset_arg('to_fldball', $new_acctnum);
				$GLOBALS['phpgw']->msg->unset_arg('move_postmove_goto', $new_acctnum);
				$GLOBALS['phpgw']->msg->unset_arg('sort', $new_acctnum);
				$GLOBALS['phpgw']->msg->unset_arg('order', $new_acctnum);
				$GLOBALS['phpgw']->msg->unset_arg('start', $new_acctnum);
				$GLOBALS['phpgw']->msg->unset_arg('what', $new_acctnum);
				$GLOBALS['phpgw']->msg->unset_arg('folder', $new_acctnum);
				$GLOBALS['phpgw']->msg->unset_arg('acctnum', $new_acctnum);
				
				// REFILL ARGS WITH NEW PAGE VIEW VALUES
				// (a) set the very important acctnum arg we collected earlier, and also the folder arg, because these values are _derived_ values
				// they are not plainly in the $new_args_env as simple key and value elements, they were derived from some of them, though
				$GLOBALS['phpgw']->msg->set_acctnum($new_acctnum);
				$GLOBALS['phpgw']->msg->set_arg_value('folder', $new_folder, $new_acctnum);
				
				// (b) LOOP thru Gathered Args, setting the class args to those values
				reset($new_args_env);
				while(list($key,$value) = each($new_args_env))
				{
					$arg_name = $key;
					$arg_value = $new_args_env[$key];
					// we do not set mail_msg class arg for 'index_php?menuaction'
					if (($arg_name != 'index_php?menuaction')
					&& ($arg_value != '-1'))
					{
						$GLOBALS['phpgw']->msg->set_arg_value($arg_name, $arg_value, $new_acctnum);
					}
				}
				// (c) IF A "BIG MOVE", THEN TURN BACK ON THE SMART CACHE
				// MOVED TO MSG CLASS
				//if ((isset($initial_session_cache_extreme))
				//&& ($initial_session_cache_extreme != '-1'))
				//{
				//	$GLOBALS['phpgw']->msg->session_cache_extreme = $initial_session_cache_extreme;
				//}
				// (d) make object and issue command
				$new_menuaction = $new_args_env['index_php?menuaction'];
				if ($this->debug > 1 || $this->debug_new_env > 1) { echo 'email.boaction.delmov ('.__LINE__.'): $new_menuaction ['.$new_menuaction.'] <br>'; } 
				if (stristr($new_menuaction, 'uimessage'))
				{
					// MAKE THE UIMESSAGE PAGE OBJECT
					$this->next_obj = CreateObject('email.uimessage');
					// CALL THE FUNCTION THAT DISPLAYS THE PAGE VIEW
					$this->next_obj->message();
				}
				// else just ASSUME uiindex, it is the most forgiving about missing values, it has fallback defaults to use if needed
				else
				{
					// MAKE THE INDEX PAGE OBJECT
					$this->next_obj = CreateObject('email.uiindex');
					// CALL THE FUNCTION THAT DISPLAYS THE PAGE VIEW
					$this->next_obj->index();
				}
				// (e) cleanup
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
			else
			{
				if ($this->debug > 0) { echo 'email.boaction.delmov ('.__LINE__.'): LEAVING, with ERROR, unhandled "where to go from here" condition<br>'; }
				echo 'error: no redirect specified in '.$GLOBALS['PHP_SELF'].', delmov()'."<br> \r\n"
					.'error_str: '.$error_str."<br> \r\n";
				// close down ALL mailserver streams
				$GLOBALS['phpgw']->msg->end_request();
				// destroy the object
				$GLOBALS['phpgw']->msg = '';
				unset($GLOBALS['phpgw']->msg);
				// shut down this transaction
				$GLOBALS['phpgw']->common->phpgw_exit(False);
			}
		}
		*/
		
		/*!
		@function set_expected_args
		@abstract Tells "new_args_env" what args may be present in the "set_new_args_uri" data. WHY 
		is this necessary? Because the "expected" args *may* be gathered BEFORE the existing args are cleared. 
		This gives us a list of args to keep from the existing args before we batch clear the existing args,
		So we loop thru the "expected" args, examine any existing args that are "expected", and preserve their 
		@param $comma_set_str (comma seperated string) any arg we axpect to be needed in the next 
		page view, seperated by commas, no spaces. 
		@author Angles
		@discussion The comma seperated string will be exploded into an array where each expected arg name 
		is a KEY and gets an initial value of "-1". When collecting the expected args from wither the existing arg 
		enviornment or the "set_new_args_uri" data, any arg name in the expected array will have the initial 
		value of "-1" replaced with the value we found in those sources. This "expected_args" data will be the 
		data we use to set the new arg enviornment after unsetting the existing "external" args.
		@access Public
		*/
		function set_expected_args($comma_set_str='-1')
		{
			if ($this->debug_new_env > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.set_expected_args ('.__LINE__.'): ENTERING<br>'); } 
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.set_expected_args ('.__LINE__.'): param $comma_set_str: ['.$comma_set_str.'] <br>'); } 
			$exploded_expected_args = array();
			$exploded_expected_args = explode(',',$comma_set_str);
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.set_expected_args ('.__LINE__.'): $exploded_expected_args DUMP:', $exploded_expected_args); } 
			
			//$this->expected_args = array();
			$this->expected_args = array();
			$loops = count($exploded_expected_args);
			for ($i = 0; $i < $loops; $i++)
			{
				$arg_name = $exploded_expected_args[$i];
				$this->expected_args[$arg_name] = '-1';
			}
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.set_expected_args ('.__LINE__.'): $this->expected_args DUMP:', $this->expected_args); } 
			if ($this->debug_new_env > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.set_expected_args ('.__LINE__.'): LEAVING<br>'); } 
		}
		
		/*!
		@function set_new_args_uri
		@abstract Takes a URI such as used in a redirect, extracts any args that are listed in the "set_expected_args" data. 
		@param $new_args_uri (URI type string) a string the same as would be used in a redirect.
		page view, seperated by commas, no spaces. 
		@author Angles
		@discussion Eases the transition from redirect to using "apply_new_args_env" by accepting that redirect URI 
		string as a data source for the "apply_new_args_env" function.
		@access Public
		*/
		function set_new_args_uri($new_args_uri='-1')
		{
			if ($this->debug_new_env > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.set_new_args_uri ('.__LINE__.'): ENTERING, $new_args_uri ['.$new_args_uri.']<br>'); } 
			$this->new_args_uri = $new_args_uri;
			if ($this->debug_new_env > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): LEAVING<br>'); } 
		}
		
		/*!
		@function apply_new_args_env
		@abstract unsets ALL known internal args then sets new values for "expected" args that exist. 
		Used as an alternative to redirect, the expected and actual args must be set via "set_expected_args" and 
		"set_new_args_uri" companion functions. "apply_new_args_env" will set (or apply) the new arg enviornment. 
		@result (string) Returns the menuaction var from the processed data, tells calling function what to do 
		now that the new arg enviornment is set.
		@author Angles
		@discussion Alternative to redirect. That is why "set_new_args_uri" is in the format of a URI, so that 
		functions that previously used redirects can easily transition to this new way of preparing the next page 
		view arg enviornment. The calling process should call "set_expected_args" and "set_new_args_uri", which 
		defines (a) what args we are looking to gather and (b) gives a data source for those args, with the existing 
		args being a secondary data source if "set_new_args_uri" is not provided or is missing information. 
		NOTE: I AM CONSIDERING NOT USING THE EXISTING ARGS AS A SOURCE, but for now they are. 
		Thenm the calling process should call this function "apply_new_args_env", which unsets all existing _known_ 
		_external_ args, hen the calling function can create the desired UI object and then call its main function, 
		imitating exactly what would happen if the redirect had occured with the menuaction command in that URI. 
		NOTE terms "known" means the arg is listed in msg class ->known_external_args which is set in 
		mail_msg_base constructor function. "external" means these are args we would normally look for at the 
		start of any page view, as source data for the mail_msg class OOP "get_arg_value" way of accessing args 
		as seperate from the actual GPC data, so that other data sources (XML-RPC) can be used to fill those 
		args, and the mail_msg class does not care where they came from in that respect.
		@access Public
		*/
		function apply_new_args_env()
		{
			if ($this->debug_new_env > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): ENTERING<br>'); } 
			$recovered_data = array();
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): source data is $this->new_args_uri DUMP:', $this->new_args_uri); } 
			parse_str($this->new_args_uri, $recovered_data);
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): used command "parsed_str" on that to get this $recovered_data DUMP:', $recovered_data); } 
			
			// NOTE PARSE_STR ***WILL ADD SLASHES*** TO ESCAPE QUOTES
			// NO MATTER WHAT YOUR MAGIC SLASHES SETTING IS
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): NOTE PARSE_STR ***WILL ADD SLASHES*** TO ESCAPE QUOTES NO MATTER WHAT YOUR MAGIC SLASHES SETTING IS **stripping slashes NOW*** from any folder names'.'<br>'); }  
			if (isset($recovered_data['fldball']['folder']))
			{
				$recovered_data['fldball']['folder'] = stripslashes($recovered_data['fldball']['folder']);
				$recovered_data['fldball']['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($recovered_data['fldball']['folder']);
			}
			if (isset($recovered_data['msgball']['folder']))
			{
				$recovered_data['msgball']['folder'] = stripslashes($recovered_data['msgball']['folder']);
				$recovered_data['msgball']['folder'] = $GLOBALS['phpgw']->msg->prep_folder_out($recovered_data['msgball']['folder']);
			}
			if (isset($recovered_data['tf']))
			{
				$recovered_data['tf'] = stripslashes($recovered_data['tf']);
				$recovered_data['tf'] = $GLOBALS['phpgw']->msg->prep_folder_out($recovered_data['tf']);
			}
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): AFTER STRIPSLASH: used command "parsed_str" on that to get this $recovered_data DUMP:', $recovered_data); } 
			
			$new_args_env = array();
			$new_args_env = $this->expected_args;
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): $this->expected_args DUMP:', $new_args_env); } 
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): initial $new_args_env DUMP:', $new_args_env); } 
			
			// loop thru KNOWN POSSIBLE new_args_env elements, GATHER the ones that are filled for later use
			reset($new_args_env);
			while(list($key,$value) = each($new_args_env))
			{
				if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out(' * ('.__LINE__.') $key: ['.$key.']  $value: ['.$value.']<br>'); } 
				$known_arg = $key;
				//handle the special URI not match case
				if ($key == 'index_php?menuaction')
				{
					//find the menuaction
					reset($recovered_data);
					while(list($recovered_key,$recovered_value) = each($recovered_data))
					{
						if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out(' * * ('.__LINE__.') $recovered_key: ['.$recovered_key.']  $recovered_value: ['.$recovered_value.']<br>'); } 
						if (stristr($recovered_key, 'menuaction'))
						{
							$new_args_env[$key] = $recovered_data[$recovered_key];
							$my_menuaction = $new_args_env[$key];
							break;
						}
					}
					if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out(' ** found menuaction ** ('.__LINE__.') ['.$recovered_data[$recovered_key].']<br>'); } 
				}
				elseif ((isset($recovered_data[$known_arg]))
				&& ((string)$recovered_data[$known_arg] != ''))
				{
					// we have a arg to use for the next page view
					$new_args_env[$key] = $recovered_data[$known_arg];
				}
			}
			reset($new_args_env);
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): FIRST PASS filled available known possible recovered_data elements <b>Post-Gather</b> $new_args_env DUMP:', $new_args_env); } 
			
			// we are NOT DONE yet, extract some more args that we derive from what we gathered so far
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): ADDITIONALLY extract embedded "*ball" items "acctnum" and "folder" AND if a "msgball", add a "uri" element to it<br>'); } 
			// GET GOOD ACCTNUM FOR THE UNSET COMMANDS BELOW
			// and also get other useful info while we are at it (folder), and if we find a "msgball", make and add to it a "uri" element
			if ($new_args_env['fldball'] != '-1')
			{
				$new_acctnum = (int)$new_args_env['fldball']['acctnum'];
				$new_folder = $new_args_env['fldball']['folder'];
			}
			elseif ($new_args_env['msgball'] != '-1')
			{
				$new_acctnum = (int)$new_args_env['msgball']['acctnum'];
				$new_folder = $new_args_env['msgball']['folder'];
				// IMITATION: during grab_args_gpc, the code add an element [uri] to the existing msgball
				// NOTE that for this uri element, the "folder" string shoulf be urlencoded
				$new_uri_element =	 'msgball[msgnum]='.$new_args_env['msgball']['msgnum']
								//.'&msgball[folder]='.urlencode($new_args_env['msgball']['folder'])
								//.'&msgball[folder]='.$GLOBALS['phpgw']->msg->prep_folder_out($new_args_env['msgball']['folder'])
								.'&msgball[folder]='.$new_args_env['msgball']['folder']
								.'&msgball[acctnum]='.$new_args_env['msgball']['acctnum'];
				$new_args_env['msgball']['uri'] = $new_uri_element;
			}
			else
			{
				echo 'email.boaction.apply_new_args_env: LINE '.__LINE__.': ERROR getting valid acctnum for goto pageview, NO fldball NO msgball found <br>';
				$GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env: LINE '.__LINE__.': ERROR getting valid acctnum for goto pageview, NO fldball NO msgball found <br>');
			}
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): extracted additional data: will use $new_acctnum ['.$new_acctnum.'];  and $new_folder ['.$new_folder.'] <br>'); }
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): FINAL filled available known possible recovered_data elements <b>Post-Gather</b> $new_args_env DUMP:', $new_args_env); } 
			
			// UNSET ARGS WE USED IN THIS PAGE BUT ARE NO LONGER NEEDED
			// HELL, JUST UNSET ALL EXTERNAL ARGS
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): unset ALL known external class args as defined in array $GLOBALS[phpgw]->msg->known_external_args<br>'); }
			$loops = count($GLOBALS['phpgw']->msg->known_external_args);
			for ($i = 0; $i < $loops; $i++)
			{
				$arg_name = $GLOBALS['phpgw']->msg->known_external_args[$i];
				if ($GLOBALS['phpgw']->msg->get_isset_arg($arg_name, $new_acctnum) == True)
				{
					if ($this->debug_new_env > 2) { $GLOBALS['phpgw']->msg->dbug->out(' * email.boaction.apply_new_args_env ('.__LINE__.'):UNSETTING with $GLOBALS[phpgw]->msg->unset_arg('.$arg_name.', '.$new_acctnum.') <br>'); }
					$GLOBALS['phpgw']->msg->unset_arg($arg_name, $new_acctnum);
				}
				else
				{
					if ($this->debug_new_env > 2) { $GLOBALS['phpgw']->msg->dbug->out(' * email.boaction.apply_new_args_env ('.__LINE__.'): <i>was not set $arg_name ['.$arg_name.'];  and $new_acctnum ['.$new_acctnum.']</i><br>'); }
				}
			}
			
			// REFILL ARGS WITH NEW PAGE VIEW VALUES
			// (a) set the very important acctnum arg we collected earlier, and also the folder arg, because these values are _derived_ values
			// they are not plainly in the $new_args_env as simple key and value elements, they were derived from some of them, though
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): BEGIN setting new arg env by setting the "new_acctnum" and "new_folder"<br>'); } 
			$GLOBALS['phpgw']->msg->set_acctnum($new_acctnum);
			$GLOBALS['phpgw']->msg->set_arg_value('folder', $new_folder, $new_acctnum);
			
			// (b) LOOP thru Gathered Args, setting the class args to those values
			if ($this->debug_new_env > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): continue by LOOPING thru Final Gathered Args, setting the class args to those values that are not still "-1" (that data was found for)<br>'); } 
			reset($new_args_env);
			while(list($key,$value) = each($new_args_env))
			{
				$arg_name = $key;
				$arg_value = $new_args_env[$key];
				// we do not set mail_msg class arg for 'index_php?menuaction'
				if ((!stristr($arg_name, 'index_php?menuaction'))
				&& ($arg_value != '-1'))
				{
					if ($this->debug_new_env > 2) { $GLOBALS['phpgw']->msg->dbug->out(' * email.boaction.apply_new_args_env ('.__LINE__.'): calling $GLOBALS[phpgw]->msg->set_arg_value('.$arg_name.', '.$arg_value.', '.$new_acctnum.') <br>'); }
					$GLOBALS['phpgw']->msg->set_arg_value($arg_name, $arg_value, $new_acctnum);
				}
			}
			//$my_menuaction = $new_args_env['/mail/index_php?menuaction'];
			if ($this->debug_new_env > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.apply_new_args_env ('.__LINE__.'): LEAVING, returning next menuaction command $my_menuaction ['.$my_menuaction.'] <br>'); } 
			return $my_menuaction;
		}
		
		/*!
		@function content_header2
		@abstract similar to API browser class function but add Content-description to the headers
		@example
			Content-Disposition: attachment; filename=something
			Content-Description: something
		??	Connection: close
		??	Transfer-Encoding: chunked
			Content-Type: application/octet-stream
		*/
		function content_header2($fn='',$mime='',$length='',$nocache=True)
		{
			if (!is_object($this->browser))
			{
				if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.content_header2: creating $this->browser <br>'); }
				$this->browser = CreateObject('phpgwapi.browser');
			}
			// if no mime-type is given or it's the default binary-type, guess it from the extension
			if(empty($mime) || $mime == 'application/octet-stream')
			{
				$mime_magic = createObject('phpgwapi.mime_magic');
				$mime = $mime_magic->filename2mime($fn);
			}
			if($fn)
			{
				if($this->browser->get_agent() == 'IE') // && browser_get_version() == "5.5")
				{
					$attachment = '';
				}
				else
				{
					$attachment = ' attachment;';
				}
				// Show this for all
				header('Content-Disposition: '.$attachment.' filename='.$fn);
				header('Content-Description: '.$fn);
				//header('Connection: close');
				//header('Transfer-Encoding: chunked');
				if($length)
				{
					header('Content-Length: '.$length);
				}
				//header('Content-Type: '.$mime.'; filename="foo.bar"');
				header('Content-Type: '.$mime.'; name="'.$fn.'"');
				if($nocache)
				{
					header('Pragma: no-cache');
					header('Pragma: public');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				}
			}
		}
		
		/*!
		@function get_attach
		@abstract browser requests a specific MIME part number, this function will get it from the mail server 
		and send it to the browser.
		@author Angles and previous maintainers
		@discussion Uses the phpgw api browser class to generate the headers which preceed the actual attachment 
		data. Note this is tricky because different browsers, especially the MSIE versions, require different headers 
		to be present in order to handle the data effectively at the clients browser. Incorrect headers can cause things 
		like the browser not getting the name of the attachment, or not starting the associated viewer for the attachment, 
		or even several save_or_open dialogs in a row.
		*/
		function get_attach()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = True;
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = True;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = True;
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			
			$this->msg_bootstrap = CreateObject('email.msg_bootstrap');
			$this->msg_bootstrap->ensure_mail_msg_exists('email.boaction.get_attach', $this->debug);
			if (!is_object($this->browser))
			{
				if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.get_attach: creating $this->browser <br>'); }
				$this->browser = CreateObject('phpgwapi.browser');
			}
			$msgball = $GLOBALS['phpgw']->msg->get_arg_value('msgball');
			if (!isset($msgball['part_no']))
			{
				$part_no = $GLOBALS['phpgw']->msg->get_arg_value('part_no');
				$msgball['part_no'] = $part_no;
			}
			
			// decode rfc2047 encoded attachment name MIME part headers
			// see RFC2047 "Message Header Extensions for Non-ASCII Text"
			/*
			//$att_name = $GLOBALS['phpgw']->msg->decode_rfc_header($GLOBALS['phpgw']->msg->get_arg_value('name'));
			$att_name = $GLOBALS['phpgw']->msg->decode_header_string($GLOBALS['phpgw']->msg->get_arg_value('name'));
			if (($att_name != $GLOBALS['phpgw']->msg->get_arg_value('name'))
			&& (trim($att_name) != '')
			&& (trim($GLOBALS['phpgw']->msg->get_arg_value('name')) != ''))
			{
				$GLOBALS['phpgw']->msg->set_arg_value('name', $att_name);
			}
			// altered as per PATCH 2770 from lex "fix-filename-encoding.diff"
 			//Check if the name changed thru the header decoding im not shure
			//if (($header_decoded_name != urldecode($GLOBALS['phpgw']->msg->get_arg_value('name')))
  			//&& (trim($header_decoded_name) != '')
 			//&& (trim(urldecode($GLOBALS['phpgw']->msg->get_arg_value('name'))) != ''))
			//{
			//	$GLOBALS['phpgw']->msg->set_arg_value('name', $header_decoded_name);
			//}
			*/
			$urlencoded_name = '';
			$urldecoded_name = '';
			$header_decoded_name = '';
			$final_att_name = '';
 			$urlencoded_name = $GLOBALS['phpgw']->msg->get_arg_value('name');
			$urldecoded_name = urldecode($GLOBALS['phpgw']->msg->get_arg_value('name'));
			// it is possible we have this now: "=?ISO-8859-1?Q?instala=E7=E3o_padr=E3o_esta=E7=F5es_windows=2Edoc?="
			// therefor we my also need to header decode
 			$header_decoded_name = $GLOBALS['phpgw']->msg->decode_header_string($urldecoded_name);
			$max_ord = 0;
			$max_char = '';
			for( $i = 0 ; $i < strlen($header_decoded_name) ; $i++ )
			{
				$val = ord($header_decoded_name[$i]);
				//if ($this->debug > 1) { echo '$header_decoded_name['.$i.'] ['.$header_decoded_name[$i].'], $val ['.$val.'] <br>'."\r\n"; }
				if ($val > $max_ord)
				{
					$max_ord = $val;
					$max_char = $header_decoded_name[$i];
				}
			}
			if ($this->debug > 1) { echo '$header_decoded_name: $max_ord ['.$max_ord.'], $max_char ['.$max_char.']  <br>'."\r\n"; }
			// now we have header decoded we can again urlencode and this time is is directly from the actual string, not an RFC2047 header encoding of it
			// fallback value
			$final_att_name = urlencode($header_decoded_name);
			// handle non us-ascii filenames
			if ($max_ord > 123)
			{
				// out us US-ASCII common chars range, this is NOT en_US name
				//$final_att_name = urlencode($header_decoded_name);
				// MORE RESEARCH NEEDED, i18n ENCODING NEEDS TO GO HERE
				//if ((ini_get('output_buffering'))
				//&& (ini_get('output_handler') == 'mb_output_handler'))
				//default_charset
				$has_mbstring = False;
				$has_mbstring = extension_loaded('mbstring') || @dl(PHP_SHLIB_PREFIX.'mbstring.'.PHP_SHLIB_SUFFIX);
				//if (($has_mbstring)
				//&& (ini_get('mbstring.internal_encoding'))
				//&& (ini_get('mbstring.func_overload') > 2))
				// just hope for the best :)
				if ($has_mbstring)
				{
					// WE CAN HANDLE THIS CHARSET, SEND OUT WITH NO ENCODING
					$final_att_name = $header_decoded_name;
				}
				else
				{
					// if not then we need to do something here may be to make it look more human readable than straight urlencoding
					// there is NOTHING we can do here, there are too many charsets in the world to make substitutions
				}
			}
			
			// FOR TESTING
			//$final_att_name = $urlencoded_name;
			//$final_att_name = $urldecoded_name;
			//$final_att_name = rawurlencode($header_decoded_name);
			//$final_att_name = $header_decoded_name;
			//$final_att_name = urlencode($header_decoded_name);
			//$final_att_name = htmlspecialchars($header_decoded_name);
			//$final_att_name = htmlentities($header_decoded_name);
			
			// set the new name param
			$GLOBALS['phpgw']->msg->set_arg_value('name', $final_att_name);
			
			$mime = strtolower($GLOBALS['phpgw']->msg->get_arg_value('type')) .'/' .strtolower($GLOBALS['phpgw']->msg->get_arg_value('subtype'));
			// do not do this until we get a length
			//$this->browser->content_header($GLOBALS['phpgw']->msg->get_arg_value('name'), $mime);
			if ($this->debug > 1)
			{
				echo 'get all args dump<pre> '; print_r($GLOBALS['phpgw']->msg->get_all_args()); echo ' </pre>'."\r\n";
				echo '$urlencoded_name: ['.$urlencoded_name.'] <br>'."\r\n";
				echo '$urldecoded_name ['.$urldecoded_name.']  <br>'."\r\n";
				echo '$header_decoded_name ['.$header_decoded_name.']  <br>'."\r\n";
				echo '$mime ['.$mime.']  <br>'."\r\n";
				echo '$GLOBALS[phpgw]->msg->get_arg_value(encoding): ['.$GLOBALS['phpgw']->msg->get_arg_value('encoding').'] <br>'."\r\n";
				echo '$final_att_name ['.$final_att_name.']  <br>'."\r\n";
				echo '$GLOBALS[phpgw]->msg->get_arg_value(name): ['.$GLOBALS['phpgw']->msg->get_arg_value('name').'] <br>'."\r\n";
				echo 'get_cfg_var(output_buffering) ['.serialize(get_cfg_var('output_buffering')).']  <br>'."\r\n";
				echo 'ini_get(output_buffering) ['.serialize(ini_get('output_buffering')).']  <br>'."\r\n";
				echo 'get_cfg_var(cfg_file_path) ['.serialize(get_cfg_var('cfg_file_path')).']  <br>'."\r\n";
				set_time_limit(40);
				echo 'get_cfg_var(max_execution_time) ['.serialize(get_cfg_var('max_execution_time')).']  <br>'."\r\n";
				echo 'ini_get(max_execution_time) ['.serialize(ini_get('max_execution_time')).']  <br>'."\r\n";
				echo 'get_cfg_var(default_charset) ['.serialize(get_cfg_var('default_charset')).']  <br>'."\r\n";
				echo 'ini_get(default_charset) ['.serialize(ini_get('default_charset')).']  <br>'."\r\n";
				//$charset = $GLOBALS['phpgw']->translation->system_charset ? $GLOBALS['phpgw']->translation->system_charset : 'iso-8859-1';
				echo 'is_object($GLOBALS[phpgw]->translation) ['.serialize(is_object($GLOBALS['phpgw']->translation)).']  <br>'."\r\n";
				echo '$GLOBALS[phpgw]->translation->system_charset ['.serialize($GLOBALS['phpgw']->translation->system_charset).']  <br>'."\r\n";
				echo '$GLOBALS[phpgw]->translation->charset() ['.serialize($GLOBALS['phpgw']->translation->charset()).']  <br>'."\r\n";
				echo '$GLOBALS[phpgw_info][server][system_charset] ['.serialize($GLOBALS['phpgw_info']['server']['system_charset']).']  <br>'."\r\n";
				echo '$GLOBALS[phpgw]->translation->mbstring ['.serialize($GLOBALS['phpgw']->translation->mbstring).']  <br>'."\r\n";
				$we_have_mbstring = extension_loaded('mbstring') || @dl(PHP_SHLIB_PREFIX.'mbstring.'.PHP_SHLIB_SUFFIX);
				echo '$we_have_mbstring ['.serialize($we_have_mbstring).']  <br>'."\r\n";
			}
			
			// is this only a header PEEK?
			if ((string)$msgball['part_no'] == '0')
			{
				// just getting headers should be via PEEK so the "seen" flag is left alone
				$this->output_data = $GLOBALS['phpgw']->msg->phpgw_fetchheader($msgball);
				$size = strlen($this->output_data);
				$this->browser->content_header($GLOBALS['phpgw']->msg->get_arg_value('name'), $mime, $size);
				echo $this->output_data;
				$this->output_data = '';
			}
			// ----  'irregular' "view raw message" functionality  ----
			elseif ($msgball['part_no'] == 'raw_message')
			{
				// NOTE no length for this purpose
				$this->browser->content_header($GLOBALS['phpgw']->msg->get_arg_value('name'), $mime);
				// to dump out the whole raw message, do this:
				// 1) output the headers, 2) output the raw body 3) output a "closing" CRLF
				//headers_msgball will be used get the message headers, by specifying "part_no" = 0
				$headers_msgball = $msgball;
				$headers_msgball['part_no'] = 0;
				// that can also be used to ge tthe raw message body because phpgw_body() doesn't care about "part_no" 
				echo $GLOBALS['phpgw']->msg->phpgw_fetchbody($headers_msgball);
				echo $GLOBALS['phpgw']->msg->phpgw_body($headers_msgball);
				echo "\r\n";
			}
			// ---- "regular" attachment handling  ----
			elseif ($GLOBALS['phpgw']->msg->get_arg_value('encoding') == 'base64')
			{
				//echo $GLOBALS['phpgw']->msg->de_base64($GLOBALS['phpgw']->msg->phpgw_fetchbody($msgball));
				$this->output_data = $GLOBALS['phpgw']->msg->de_base64($GLOBALS['phpgw']->msg->phpgw_fetchbody($msgball));
				$size = strlen($this->output_data);
				//$this->browser->content_header($GLOBALS['phpgw']->msg->get_arg_value('name'), $mime, $size);
				$this->content_header2($GLOBALS['phpgw']->msg->get_arg_value('name'), $mime, $size);
				echo $this->output_data;
				$this->output_data = '';
			}
			elseif ($GLOBALS['phpgw']->msg->get_arg_value('encoding') == 'qprint')
			{
				//echo $GLOBALS['phpgw']->msg->qprint($GLOBALS['phpgw']->msg->phpgw_fetchbody($msgball));
				$this->output_data = $GLOBALS['phpgw']->msg->qprint($GLOBALS['phpgw']->msg->phpgw_fetchbody($msgball));
				$size = strlen($this->output_data);
				//$this->browser->content_header($GLOBALS['phpgw']->msg->get_arg_value('name'), $mime, $size);
				$this->content_header2($GLOBALS['phpgw']->msg->get_arg_value('name'), $mime, $size);
				echo $this->output_data;
				$this->output_data = '';
			}
			else
			{
				//echo $GLOBALS['phpgw']->msg->phpgw_fetchbody($msgball);
				$this->output_data = $GLOBALS['phpgw']->msg->phpgw_fetchbody($msgball);
				$size = strlen($this->output_data);
				//$this->browser->content_header($GLOBALS['phpgw']->msg->get_arg_value('name'), $mime, $size);
				$this->content_header2($GLOBALS['phpgw']->msg->get_arg_value('name'), $mime, $size);
				echo $this->output_data;
				$this->output_data = '';
			}
			// you may feed "end_request" a msgball or a fldball and "end_request" will close the acctnum specified therein
			//$GLOBALS['phpgw']->msg->end_request($msgball);
			//$GLOBALS['phpgw']->common->phpgw_footer();
			// close down ALL mailserver streams
			$GLOBALS['phpgw']->msg->end_request();
			// destroy the object
			$GLOBALS['phpgw']->msg = '';
			unset($GLOBALS['phpgw']->msg);
			// shut down this transaction
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}
		
		/*!
		@function view_html
		@abstract special handler to view certain types of html mail
		@author Angles
		@discussion Used in special cases when simply giving the client browser unprocessed html MIME part(s) 
		is not a good idea or needs special attention. Not a good idea means that there is CSS in the html part which would 
		totally b0rk the look of the clients browser, or certain unusual script. Special Handling means a MULTIPART 
		RELATED message such as Outkrook stationary or Evolutions version of the same, where the html has IMG 
		tags in it that are not real HREFs but rather a reference to another MIME part in the same message. To handle 
		that this message display code swaps that IMG mime part reference with an actual URL used by "get_attach" function 
		to retrieve that particular MIME part from the email server and send it to the browser, this the IMG appears in the 
		HTML message as intended. In that case the processing is done before the message is dispayed, and the processed HTML 
		part is stored as a form hiddenvar in base64 encoded format. The user sees a button saying "View HTML", if clicked this 
		processed HTML part is submitted to this function which base64 decodes it and sends it to the browser as a simgle 
		html page, not a part of a mail like is typical. In either case the user gets that button and the buttons associated form 
		submits data to this function. If the part is not RELATED, i.e. did not require special IMG tag swapping, then this 
		function gets submitted to is a reference to the particular HTML part of the message and it is sent to the browser 
		as a page unto itself. In either case, the part is not displayed inline with other MIME parts, not displayed in the same 
		browser window as the rest of the groupware template, instead this part will be viewed in its own page.
		*/
		function view_html()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = True;
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = True;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = True;
			$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
			$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
			
			$this->msg_bootstrap = CreateObject('email.msg_bootstrap');
			$this->msg_bootstrap->ensure_mail_msg_exists('email.boaction.get_attach', $this->debug);
			
			if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.view_html: creating $this->browser <br>'); }
			$this->browser = CreateObject('phpgwapi.browser');
			
			//$this->browser->content_header($name,$mime);
			if ((($GLOBALS['phpgw']->msg->get_isset_arg('html_part')))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('html_part') != ''))
			{
				$this->browser->content_header('','');
				$html_part = $GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('html_part'));
				echo $GLOBALS['phpgw']->msg->de_base64($html_part);
				$GLOBALS['phpgw']->msg->end_request();
			}
			elseif ((($GLOBALS['phpgw']->msg->get_isset_arg('html_reference')))
			&& ($GLOBALS['phpgw']->msg->get_arg_value('html_reference') != ''))
			{
				$html_reference = $GLOBALS['phpgw']->msg->stripslashes_gpc($GLOBALS['phpgw']->msg->get_arg_value('html_reference'));
				$GLOBALS['phpgw']->msg->end_request();
				//header('Location: ' . $html_reference);
				$GLOBALS['phpgw']->redirect($html_reference);
				//$GLOBALS['phpgw']->common->phpgw_footer();
			}
			else
			{
				$GLOBALS['phpgw']->msg->end_request();
				//$GLOBALS['phpgw']->common->phpgw_footer();
			}
			// close down ALL mailserver streams
			$GLOBALS['phpgw']->msg->end_request();
			// destroy the object
			$GLOBALS['phpgw']->msg = '';
			unset($GLOBALS['phpgw']->msg);
			// shut down this transaction
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}

		/*!
		@function clearcache
		@abstract will remove all cached data for this user, all cahced data for all email accounts
		@author Angles
		@discussion if caching is enabled, this function will clear the cache for this user, 
		That is all data for all email accounts this user has that email app has cached, this will 
		be wiped clean. Mostly good for debugging.
		It expects a fldball just to know where to redirect to after the wipe is done.
		@access public
		*/
		function clearcache()
		{
			// make sure we have msg object and a server stream
			$this->msg_bootstrap = CreateObject("email.msg_bootstrap");
			// NO LOGIN wanted at this moment, because preferences may not be set yet. Login will occur later if needed automatically
			$this->msg_bootstrap->set_do_login(BS_LOGIN_NEVER);
			$this->msg_bootstrap->ensure_mail_msg_exists('email.boaction.clearcache', 0);
			if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('ENTERING email.boaction.clearcache line('.__LINE__.')'.'<br>'); }
			//if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache line('.__LINE__.') GLOBALS DUMP:', $GLOBALS); }
			//if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache line('.__LINE__.'): get_defined_constants DUMP:', get_defined_constants()); }
			//if ($this->debug > 2) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache line('.__LINE__.') GLOBALS[phpgw_info] DUMP:', $GLOBALS['phpgw_info']); }

			// make an error report URL
			$this->redirect_if_error = $GLOBALS['phpgw']->link('/home.php');
			if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache ('.__LINE__.'): $this->redirect_if_error is ['.htmlspecialchars($this->redirect_if_error).']<br>'); }
			
			// where do we goto after done here
			// if coming from the email app itself, use fldball
			// function called from preferences or admin page, go back to referer
			$came_from = '';
			$came_from = get_var('HTTP_REFERER', 'SERVER');
			if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache ('.__LINE__.'): $came_from is ['.htmlspecialchars($came_from).']<br>'); }
			
			if ((stristr($came_from, '/admin/index.php?'))
			&& (isset($GLOBALS['phpgw_info']['apps']['admin'])))
			{
				$this->redirect_to = $GLOBALS['phpgw']->link('/admin/index.php');
			}
			elseif ((stristr($came_from, '/preferences/index.php?'))
			&& (isset($GLOBALS['phpgw_info']['apps']['preferences'])))
			{
				$this->redirect_to = $GLOBALS['phpgw']->link('/preferences/index.php');
			}
			elseif (stristr($came_from, 'index.php?menuaction=email.'))
			{
				$goback_fldball = array();
				// we sent param "target_fldball" because that param does not lookup list verify at initialization time
				// thus no unwanted login attempt is triggered which could happen if we sent param fldball instead
				if ($GLOBALS['phpgw']->msg->get_isset_arg('target_fldball'))
				{
					$goback_fldball = $GLOBALS['phpgw']->msg->get_arg_value('target_fldball');
					if (isset($goback_fldball['folder']))
					{
						// this is the first time we do anything that might require a login, because we are lookup list verifying the folder name
						$goback_fldball['folder'] = $GLOBALS['phpgw']->msg->prep_folder_in($goback_fldball['folder']);
					}
					else
					{
						$goback_fldball['folder'] = 'INBOX';
					}
				}
				else
				{
					$goback_fldball['acctnum'] = $GLOBALS['phpgw']->msg->get_acctnum();
					// does this need to be langed?
					$goback_fldball['folder'] = 'INBOX';
				}
				if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache ('.__LINE__.'): goback_fldball[] DUMP:', $goback_fldball); }
				// $totaldeleted gets displayed in the report to the user, typically "X messages have been moved", we use a string here though
				$totaldeleted = 'cachedata';
				$this->redirect_to = $GLOBALS['phpgw']->link('/index.php',array(
								'menuaction' => 'email.uiindex.index',
								'fldball[folder]' => $GLOBALS['phpgw']->msg->prep_folder_out($goback_fldball['folder']),
								'fldball[acctnum]' => $goback_fldball['acctnum'],
								'td' => $totaldeleted,
								'sort' => $GLOBALS['phpgw']->msg->get_arg_value('sort'),
								'order' => $GLOBALS['phpgw']->msg->get_arg_value('order'),
								'start' => $GLOBALS['phpgw']->msg->get_arg_value('start')
								));
			}
			else
			{
				// we have no idea where we came from, just goto home page
				$this->redirect_to = $this->redirect_if_error;
			}
			if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache ('.__LINE__.'): $GLOBALS[phpgw]->msg->session_cache_enabled is ['.serialize($GLOBALS['phpgw']->msg->session_cache_enabled).']  $$GLOBALS[phpgw]->msg->session_cache_extreme is ['.serialize($GLOBALS['phpgw']->msg->session_cache_extreme).'] <br>'); } 
			if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache ('.__LINE__.'): decision: $this->redirect_to is ['.htmlspecialchars($this->redirect_to).']<br>'); }
			
			
			// is there any cache to delete
			//if (($GLOBALS['phpgw']->msg->session_cache_enabled == True)
			//|| ($GLOBALS['phpgw']->msg->session_cache_extreme == True))
			
			if ($this->debug > 1) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache ('.__LINE__.'): about to call $GLOBALS[phpgw]->msg->clearcache_all(email.boaction.clearcache line(LINENUM)) <br>'); }
			$GLOBALS['phpgw']->msg->clearcache_all('email.boaction.clearcache line('.__LINE__.')');
			
			// we ALWAYS use classic redirect after this function is done, no need for speed here
			if ($this->debug > 0) { $GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache ('.__LINE__.'): EXITING ... about redirect to: $this->redirect_to ['.$this->redirect_to.']<br>'); }
			
			if ($this->debug > 3)
			{
				$GLOBALS['phpgw']->msg->dbug->out('email.boaction.clearcache ('.__LINE__.'): $this->debug ['.$this->debug.'] means SKIP redirect <br>');
			}
			else
			{
				$GLOBALS['phpgw']->redirect($this->redirect_to);
			}
			// kill this script, we re outa here...
			if (is_object($GLOBALS['phpgw']->msg))
			{
				$GLOBALS['phpgw']->msg->end_request();
				$GLOBALS['phpgw']->msg = '';
				unset($GLOBALS['phpgw']->msg);
			}
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}
	
	}
?>

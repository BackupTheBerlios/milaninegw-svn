<?php
	/**************************************************************************\
	* eGroupWare - E-Mail *
	* http://www.egroupware.org *
	* Based on Aeromail by Mark C3ushman <mark@cushman.net> *
	* http://the.cushman.net/ *
	* Currently maintained by Angles <angles@aminvestments.com> *
	* -------------------------------------------- *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the *
	* Free Software Foundation; either version 2 of the License, or (at your *
	* option) any later version.  *
	\**************************************************************************/
	/* $Id: compose.php,v 1.54 2004/01/27 15:45:19 reinerj Exp $ */
	
	Header('Cache-Control: no-cache');
	Header('Pragma: no-cache');
	Header('Expires: Sat, Jan 01 2000 01:01:01 GMT');
  
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'email',
		'noheader' => True,
		'nofooter' => True,
		'nonavbar' => True,
		'noappheader' => True,
		'noappfooter' => True
	);
	include('../header.inc.php');
	
	// we need a msg object BUT NO LOGIN IS NEEDED
	$my_msg_bootstrap = '';
	$my_msg_bootstrap = CreateObject("email.msg_bootstrap");
	$my_msg_bootstrap->set_do_login(False);
	$my_msg_bootstrap->ensure_mail_msg_exists('email: compose.php', 0);
	
	// time limit should be controlled elsewhere
	//@set_time_limit(0);
	$pass_the_ball_uri = '';
	
	if ($GLOBALS['phpgw']->msg->get_isset_arg('fldball'))
	{
		$my_fldball = $GLOBALS['phpgw']->msg->get_arg_value('fldball');
		$pass_the_ball_uri = '&fldball[folder]='.$my_fldball['folder']
						.'&fldball[acctnum]='.$my_fldball['acctnum'];
	}
	elseif ($GLOBALS['phpgw']->msg->get_isset_arg('msgball'))
	{
		$my_msgball = $GLOBALS['phpgw']->msg->get_arg_value('msgball');
		$pass_the_ball_uri = '&msgball[folder]='.$my_msgball['folder']
						.'&msgball[acctnum]='.$my_msgball['acctnum']
						.'&msgball[msgnum]='.$my_msgball['msgnum'];
	}
	else
	{
		$pass_the_ball_uri = '&fldball[folder]=INBOX'
						.'&fldball[acctnum]=0';
	}
	
	header('Location: '.$GLOBALS['phpgw']->link(
				'/index.php',
				'menuaction=email.uicompose.compose'.
				 $pass_the_ball_uri.
				'&to='.$_GET['to'].
				'&cc='.$_GET['cc'].
				'&bcc='.$_GET['bcc'].
				'&subject='.$_GET['subject'].
				'&body='.$_GET['body'].
				'&personal='.$_GET['personal'].
				'&sort='.$_GET['sort'].
				'&order='.$_GET['order'].
				'&start='.$_GET['start']));
	
	if (is_object($GLOBALS['phpgw']->msg))
	{
		$terminate = True;
	}
	else
	{
		$terminate = False;
	}
	
	if ($terminate == True)
	{
		// close down ALL mailserver streams
		$GLOBALS['phpgw']->msg->end_request();
		// destroy the object
		$GLOBALS['phpgw']->msg = '';
		unset($GLOBALS['phpgw']->msg);
	}
	// shut down this transaction
	$GLOBALS['phpgw']->common->phpgw_exit(False);

?>

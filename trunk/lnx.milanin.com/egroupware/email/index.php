<?php
	/**************************************************************************\
	* eGroupWare - E-Mail                                                      *
	* http://www.egroupware.org                                                *
	* Based on Aeromail by Mark C3ushman <mark@cushman.net>                    *
	*          http://the.cushman.net/                                         *
	* Currently maintained by Angles <angles@aminvestments.com>                *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php,v 1.93 2004/01/27 15:45:19 reinerj Exp $ */

	Header('Cache-Control: no-cache');
	Header('Pragma: no-cache');
	Header('Expires: Sat, Jan 01 2000 01:01:01 GMT');
  
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'  => 'email',
		'noheader'    => True,
		'nofooter'    => True,
		'nonavbar'    => True,
		'noappheader' => True,
		'noappfooter' => True
	);
	include('../header.inc.php');

	// redirect to mail-admin-page if mail is not configured
	//
	if ((empty($GLOBALS['phpgw_info']['server']['mail_server'])||
	     empty($GLOBALS['phpgw_info']['server']['smtp_server']) ||
	     empty($GLOBALS['phpgw_info']['server']['mail_server_type'])) &&
        $GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
		$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=admin.uiconfig.index&appname=email');
	}

	/*
	time limit should be controlled elsewhere
	@set_time_limit(0);

	this index page is acting like a calling app which wants the HTML produced by mail.uiindex.index
	but DOES NOT want mail.uiindex.index to actually echo or print out any HTML
	we, the calling app, will handle the outputting of the HTML
	$is_modular = True;
	*/
	
	$simple_redirect = True;
	//$simple_redirect = False;
	
	if ($simple_redirect == True)
	{
		//header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=email.uiindex.index'));
		$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=email.uiindex.index');
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
		/* 
		// OBSOLETED CODE
		// pretend we are a calling app outputting some HTML, including the header and navbar
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		// retrieve the html data from class uiindex
		$obj = CreateObject('email.uiindex');
		$obj->set_is_modular(True);
		$retured_html = $obj->index();
		// time for us to output the returned html data
		echo $retured_html;
		// now as the calling app, it's time to output the bottom of the page
		$GLOBALS['phpgw']->common->phpgw_footer();
		*/
		
		/*
		// NOTE: this does NOT WORK
		// make a uiinex object and make it do its job
		// it will output the header, navbar, class HTML data, and footer
		class uiindex_holder
		{
			var $uiindex_obj = '';
		}
		
		$my_msg_bootstrap = '';
		$my_msg_bootstrap = CreateObject('email.msg_bootstrap');
		$my_msg_bootstrap->ensure_mail_msg_exists('index.php', 3);
		
		echo 'calling CreateObject email.uiindex <br>';
		$GLOBALS['phphw_uiindex'] = new uiindex_holder;
		$GLOBALS['phphw_uiindex']->uiindex_obj = CreateObject('email.uiindex');
		echo 'done calling CreateObject email.uiindex <br>';
		$GLOBALS['phphw_uiindex']->uiindex_obj->index();
		// STRANGEly enough, menuaction=email.uiindex.index as non-module STILL requires an
		// outside-the-class entity to call common->phpgw_footer(), eventhough the class itself will
		// output the header and navbar, but it may not output common->phpgw_footer() else page gets 2 footers
		//$GLOBALS['phpgw']->common->phpgw_footer();
		*/
	}
	
?>

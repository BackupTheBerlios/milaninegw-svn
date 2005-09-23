<?php
	/**************************************************************************\
	* AngleMail - email UI Class for Attaching Files						*
	* http://www.anglemail.org									*
	* File adapted directly from phpGroupWare file email/attach_file.php		*
	* http://www.phpgroupware.org									*
	* This file deals only with the UI display of the bo class file			*
	* Copyright 2002 Angles Puglisi							*
	* --------------------------------------------							*
	*  This program is free software; you can redistribute it and/or modify it		*
	*  under the terms of the GNU General Public License as published by the	*
	*  Free Software Foundation; either version 2 of the License, or (at your		*
	*  option) any later version.								*
	\**************************************************************************/

	/* $Id: class.uiattach_file.inc.php,v 1.3.2.1 2005/04/15 13:06:30 ralfbecker Exp $ */

	class uiattach_file
	{
		var $public_functions = array(
			'attach'	=> True
			//'show_ui'	=> True
		);
		var $tpl;
		var $bo;
		
		var $debug = 0;
		//var $debug = 3;
		//var $debug = 4;
		
		function uiattach_file()
		{
			//return;
		}
		
		function attach()
		{
			if ($this->debug > 0) { echo 'ENTERING emai.uiattach_file.attach'.'<br>'; }
			if ($this->debug > 2) { echo 'emai.uiattach_file.attach: initial $GLOBALS[phpgw_info][flags] DUMP<pre>'; print_r($GLOBALS['phpgw_info']['flags']);  echo '</pre>'; }
			//return;
			
			
			$phpgw_flags = Array(
				'currentapp' => 'email',
				'enable_network_class' => True,
				'noheader'   => True,
				'nonavbar'   => True
			);
			
			$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;
			
			$GLOBALS['phpgw']->template->set_file(
				Array(
					'T_attach_file' => 'attach_file.tpl',
					'T_attach_file_blocks' => 'attach_file_blocks.tpl'
				)
			);
			$GLOBALS['phpgw']->template->set_block('T_attach_file_blocks','B_alert_msg','V_alert_msg');
			$GLOBALS['phpgw']->template->set_block('T_attach_file_blocks','B_attached_list','V_attached_list');
			$GLOBALS['phpgw']->template->set_block('T_attach_file_blocks','B_attached_none','V_attached_none');
			$GLOBALS['phpgw']->template->set_block('T_attach_file_blocks','B_delete_btn','V_delete_btn');
			
			// create boattach_file object
			$this->bo = CreateObject('email.boattach_file');
			// tell it we want it to fill the global template we establisted above
			// DO NOT USE AMPERSAND because we declare the param as a reference when we made the function 
			$this->bo->set_ref_var_holder($GLOBALS['phpgw']->template);
			// now run the code
			$this->bo->attach();
			
			// ... the boattach_file class all the work ...
			
			// output the HTML
			$GLOBALS['phpgw']->common->phpgw_header();
			$GLOBALS['phpgw']->template->pfp('out','T_attach_file');
			
			//$GLOBALS['phpgw']->common->phpgw_exit();
			if (is_object($GLOBALS['phpgw']->msg))
			{
				// close down ALL mailserver streams
				$GLOBALS['phpgw']->msg->end_request();
				// destroy the object
				$GLOBALS['phpgw']->msg = '';
				unset($GLOBALS['phpgw']->msg);
			}
			
			// shut down this transaction
			if ($this->debug > 0) { echo 'LEAVING emai.uiattach_file.attach with call to phpgw_exit'.'<br>'; }
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}
	
	
	}
?>

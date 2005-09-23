<?php
	/**************************************************************************\
	* eGroupWare API - php IMAP SO access object constructor			*
	* This file written by Mark Peters <skeeter@phpgroupware.org>			*
	* and Angelo Tony Puglisi (Angles) <angles@aminvestments.com>		*
	* Handles initializing the appropriate class dcom object				*
	* Copyright (C) 2001 Mark Peters							*
	* Copyright (C) 2001, 2002 Angelo "Angles" Puglisi 				*
	* -------------------------------------------------------------------------			*
	* This library is part of the eGroupWare API					*
	* http://www.egroupware.org/api						* 
	* ------------------------------------------------------------------------ 			*
	* This library is free software; you can redistribute it and/or modify it		*
	* under the terms of the GNU Lesser General Public License as published by	*
	* the Free Software Foundation; either version 2.1 of the License,		*
	* or any later version.								*
	* This library is distributed in the hope that it will be useful, but		*
	* WITHOUT ANY WARRANTY; without even the implied warranty of		*
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	*
	* See the GNU Lesser General Public License for more details.			*
	* You should have received a copy of the GNU Lesser General Public License 	*
	* along with this library; if not, write to the Free Software Foundation,  	*
	* Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA		*
	\**************************************************************************/

	
	// Debugging or force_sockets 
	// force_sockets True make use of sockets data communication code eventhough php-imap is available
	if ((is_object($GLOBALS['phpgw']->msg))
	&& ($GLOBALS['phpgw']->msg->force_sockets == True))
	{
		$force_sockets = True;
	}
	else
	{
		//$force_sockets = True;
		$force_sockets = False;
	}
	//$debug_dcom = True;
	$debug_dcom = False;

	
	/*!
	@class MAIL_DCOM
	@abstract implements communication with the mail server. (not related to anything else called "dcom")
	@discussion php may or may not have IMAP extension built in. This class will AUTO-DETECT that and 
	load either (a) a class which mostly wraps the available builtin functions, or (b) a TOTAL REPLACEMENT 
	to PHPs builtin imap extension. Currently, the POP3 socket class is fully implemented, basically a re-write 
	of the UWash c-client, because all the logic contained in an imap server had to be emulated locally here, 
	since a pop server provides only the most basic information, the rest must be deduced.
	NOTE: the imap socket class is NOT COMPLETE!
	@author Angles and others, each function has an authors list
	@access private, only mail_msg access this directly
	*/
	/* -----  any constructor params? ---- */
	if (isset($p1)
	&& ($p1)
	&& ( (stristr($p1, 'imap') || stristr($p1, 'pop3') || stristr($p1, 'nntp')) )
	)
	{
		$mail_server_type = $p1;
		if ($debug_dcom) { echo 'DCOM DEBUG: found class feed arg $p1 ['.serialize($p1).']<br>'; }
		//{ echo 'DCOM DEBUG: found class feed arg $p1 ['.serialize($p1).']<br>'; }
	}
	else
	{
		if ($debug_dcom) { echo 'DCOM DEBUG: did NOT find class feed arg $p1 ['.serialize($p1).']<br>'; }
		//{ echo 'DCOM DEBUG: did NOT find class feed arg $p1 ['.serialize($p1).']<br>'; }
		$mail_server_type = $GLOBALS['phpgw_info']['user']['preferences']['email']['mail_server_type'];
	}

	/* -----  is IMAP compiled into PHP */
	//if (($debug_dcom == True)
	//&& ((stristr($mail_server_type, 'pop'))
	//	|| (stristr($mail_server_type, 'imap')))
	//)
	
	//if (($force_sockets == True)
	//&& ((strtolower($mail_server_type) == 'pop3')
	//	|| (strtolower($mail_server_type) == 'imap'))
	//)
	if ($force_sockets == True)
	{
		$imap_builtin = False;
		$sock_fname = '_sock';
		if ($debug_dcom) { echo 'DCOM DEBUG: force socket class for $mail_server_type ['.$mail_server_type.']<br>'; }
	}
	elseif (extension_loaded('imap') && function_exists('imap_open'))
	{
		$imap_builtin = True;
		$sock_fname = '';
		if ($debug_dcom) { echo 'imap builtin extension is available<br>'; }
	}
	else
	{
		$imap_builtin = False;
		$sock_fname = '_sock';
		if ($debug_dcom) { echo 'imap builtin extension NOT available, using socket class<br>'; }
	}

	/* -----  include SOCKET or PHP-BUILTIN classes as necessary */
	if ($imap_builtin == False)
	{
		include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_sock_defs.inc.php');
		if ($debug_dcom) { echo 'include_once :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_sock_defs.inc.php<br>'; }
		CreateObject('phpgwapi.network');
		if ($debug_dcom) { echo 'created phpgwapi network class used with sockets<br>'; }
	}

	//CreateObject('email.mail_dcom_base'.$sock_fname);
	include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_base'.$sock_fname.'.inc.php');
	if ($debug_dcom) { echo 'include_once :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_base'.$sock_fname.'.inc.php<br>'; }

	if (($mail_server_type == 'imap')
	|| ($mail_server_type == 'imaps'))
        {
		include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_imap'.$sock_fname.'.inc.php');
		if ($debug_dcom) { echo 'include_once :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_imap'.$sock_fname.'.inc.php<br>'; }
	}
	elseif (($mail_server_type == 'pop3')
	|| ($mail_server_type == 'pop3s'))
	{
		include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_pop3'.$sock_fname.'.inc.php');
		if ($debug_dcom) { echo 'include_once :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_pop3'.$sock_fname.'.inc.php<br>'; }
	}
	elseif ($mail_server_type == 'nntp')
	{
		include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_nntp'.$sock_fname.'.inc.php');
		if ($debug_dcom) { echo 'include_once :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_nntp'.$sock_fname.'.inc.php<br>'; }
	}
	elseif ((isset($mail_server_type))
	&& ($mail_server_type != ''))
	{
		/* educated guess based on info being available: */
		include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_'.$GLOBALS['phpgw_info']['user']['preferences']['email']['mail_server_type'].$sock_fname.'.inc.php');
		if ($debug_dcom) { echo 'Educated Guess: include_once :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_'.$GLOBALS['phpgw_info']['user']['preferences']['email']['mail_server_type'].$sock_fname.'.inc.php<br>'; }
  	}
	else
	{
		/* DEFAULT FALL BACK: */
		include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_imap.inc.php');
		if ($debug_dcom) { echo 'NO INFO DEFAULT: include_once :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_imap.inc.php<br>'; }
	}
?>

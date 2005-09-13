<?php
	/**************************************************************************\
	* eGroupWare - E-Mail                                                    *
	* http://www.egroupware.org                                              *
	* --------------------------------------------                             *
	*	This program is free software; you can redistribute it and/or modify it*
	*	under the terms of the GNU General Public License as published by the  *
	*	Free Software Foundation; either version 2 of the License, or (at your *
	*	option) any later version.                                             *
	\**************************************************************************/

	/* $Id: hook_notifywindow.inc.php,v 1.14.2.1 2004/08/04 02:04:04 alpeb Exp $ */

	$d1 = strtolower(substr(APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo "Failed attempt to break in via an old Security Hole!<br>\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	unset($d1);

	if (@$GLOBALS['phpgw_info']['user']['apps']['email'])
	{
		$my_msg_bootstrap = '';
		$my_msg_bootstrap = CreateObject("email.msg_bootstrap");
		$my_msg_bootstrap->ensure_mail_msg_exists('email.hook_notifywindow', 0);
	
		/*  // this is the structure you will get
		  $inbox_data['is_imap'] boolean - pop3 server do not know what is "new" or not
		  $inbox_data['folder_checked'] string - the folder checked, as processed by the msg class
		  $inbox_data['alert_string'] string - what to show the user about this inbox check
		  $inbox_data['number_new'] integer - for IMAP is number "unseen"; for pop3 is number messages
		  $inbox_data['number_all'] integer - for IMAP and pop3 is total number messages in that inbox
		*/
		$inbox_data = Array();
		$inbox_data = $GLOBALS['phpgw']->msg->new_message_check();
		// end the mailserver request (i.e. logout of the mail server)
		$GLOBALS['phpgw']->msg->end_request();

		$current_uid=$inbox_data['uidnext'];
		$old_uid=$GLOBALS['phpgw']->common->appsession();
		if(!empty($old_uid))
		{
			$new_msgs=$current_id-$old_id;
		}
		else
		{
		 	$new_msgs=$inbox_data['number_new'];
		}
		
		if ($inbox_data['alert_string'] != '')
		{
			echo '<script language="JavaScript">'."\n";
			echo '	<!-- Activate Cloaking Device'."\n";
			echo '	function CheckEmail()'."\n";
			echo '	{'."\n";
			echo '		window.opener.document.location.href="'.$GLOBALS['phpgw']->link('/index.php','menuaction=email.uiindex.index').'";'."\n";
			echo '	}'."\n";
			echo '	//-->'."\n";
			echo '	</script>'."\n";
			echo "\r\n" . '<tr><td align="left"><!-- Mailbox info X10 -->' . "\r\n";
			echo '<table width="100%" style="border-color:#000000;border-style:solid;border-width:1px;"><tr>'."\r\n";
			echo '<td width="20%" valign="middle" align="center">'."\r\n";
			echo '<a href="JavaScript:CheckEmail();"><img src="email/templates/default/images/navbar.png" alt="email icon" border=0></a>'."\r\n";
			echo "<td>\r\n";
			
			if($new_msgs>0)
			{
			 	echo '<a href="JavaScript:CheckEmail();"><b>New:</b> '.$new_msgs.'</a><br>';
				$urgent=true;
			}
			else
			{
			 	echo '<a href="JavaScript:CheckEmail();"><b>New:</b> None</a><br>'."\r\n";
			}
			
			if($inbox_data['number_new']>0)
			{
			 	echo '<a href="JavaScript:CheckEmail();"><b>Unread:</b> '.$inbox_data['number_new'].'</a><br>'."\r\n";
			}
			else
			{
			 	echo '<a href="JavaScript:CheckEmail();"><b>Unread:</b> None</a><br>'."\r\n";
			}

			echo '<a href="JavaScript:CheckEmail();"><b>INBOX:</b> '.$inbox_data['number_all'].'</a>'."\r\n";

			if($urgent)
			{
				echo '<script type="text/javascript" language="Javascript 1.3">'."\r\n";
				echo '<!--'."\r\n";
				echo 'document.bgcolor="#ff6666";'."\r\n";
				echo 'window.focus();'."\r\n";
				echo '// -->'."\r\n";
				echo '</script>'."\r\n";
			}

			echo "</td></tr></table>\r\n";
			echo "\r\n".'<!-- Mailox info --></td></tr>'."\r\n";
		}
		$GLOBALS['phpgw']->common->appsession($current_uid);
	}
?>

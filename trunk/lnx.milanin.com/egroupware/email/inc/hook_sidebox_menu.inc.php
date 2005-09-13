<?php
  /**************************************************************************\
  * eGroupWare - Email's Sidebox-Menu for idots-template                     *
  * http://www.egroupware.org                                                *
  * Written by edave <bigmudcake@hotmail.com>                                *
  * Contributions by trike <tim@timmadden.com.au>                            *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_sidebox_menu.inc.php,v 1.7 2004/04/20 09:25:19 mila76 Exp $ */
{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	Main Code is at the bottom of this page.

	All bug fixes and changes have been added from CVS up to 4-Feb-2004 version 1.4

	Added extra code to check for valid email access before sideboxes can be displayed.

 */





/*!
@function CreateSidebox_MenuLink
@abstract A utility function to creates properly formatted urls for the associated sidebox menu items. 
@param $mailacct (integer) the mail account we want to refer to. 
@param $mailfolder (string) the mail folder we want to view, or leave empty if not applicable.
@param $mailpage (string) the page we want to the user to go to.
@result (string) a formated url we can simply plug into the list of menu items. 
@author edave 
@discussion This utility function is used by the functions CreateSidebox_EmailMenu, 
CreateSidebox_FolderMenu, to easily construct the url a user clicks on when they want 
to select a particular menu item from each sidebox. 
*/
function CreateSidebox_MenuLink($mailacct,$mailfolder='INBOX',$mailpage='email.uiindex.index')
{
	$return_link = $GLOBALS['phpgw']->link('/index.php',array(
						'menuaction' => $mailpage,
						'fldball[folder]' => $GLOBALS['phpgw']->msg->prep_folder_out($mailfolder),
						'fldball[acctnum]' => $mailacct));
	return $return_link;
}





/*!
@function CreateSidebox_EmailMenu
@abstract A function to generate the main email menu items normally shown accross the top. 
@param $mailacct (integer) the mail account we want to refer to. 
@result (array) list of menu items used to construct the sidebox menu. 
@author edave 
@discussion This function creates the sidebox menu that can be used to navigate around 
email module of egroupware and replaces the navigation buttons normally shown accross the top. 
*/
function CreateSidebox_EmailMenu($mailacct)
{
	// Check to see if mailserver supports folders.
	$has_folders = $GLOBALS['phpgw']->msg->get_mailsvr_supports_folders();

	// Create Links for all the menu items
	$compose_link = $GLOBALS['phpgw']->link('/index.php',array(
						'menuaction' => 'email.uicompose.compose',
						// this data tells us where to return to after sending a message
						'fldball[folder]' => $GLOBALS['phpgw']->msg->prep_folder_out(),
						'fldball[acctnum]' => $mailacct,
						'sort' => $GLOBALS['phpgw']->msg->get_arg_value('sort'),
						'order' => $GLOBALS['phpgw']->msg->get_arg_value('order'),
						'start' => $GLOBALS['phpgw']->msg->get_arg_value('start')));
	// going to the folder list page, we only need log into the INBOX folder
	$folders_link = CreateSidebox_MenuLink($mailacct,'INBOX','email.uifolder.folder');
	$search_link = CreateSidebox_MenuLink($mailacct,'','email.uisearch.form');
	$filters_link = CreateSidebox_MenuLink($mailacct,'','email.uifilters.filters_list');
	$accounts_link = $GLOBALS['phpgw']->link('/index.php',array(
						'menuaction' => 'email.uipreferences.ex_accounts_list'));
	$email_prefs_link = $GLOBALS['phpgw']->link('/index.php',array(
						'menuaction' => 'email.uipreferences.preferences',
						'ex_acctnum' => $mailacct));					

	// Construct the Menu Contents in array $file
	$file = array();
	$file['Compose'] = $compose_link;
	if($has_folders) { 
		$file['Folders'] = $folders_link;
		$file['Search'] = $search_link;
	}
	$file['Filters'] = $filters_link;
	$file['Accounts'] = $accounts_link;
	$file['Settings'] = $email_prefs_link;
	//	$file[] = '_NewLine_'; // give a newline
	return $file;
}








/*!
@function CreateSidebox_FolderMenu
@abstract A function to generate a list of email folders shown as menu items in a sidebox. 
@param $mailacct (integer) the mail account we want to refer to. 
@result (array) list of menu items used to construct the sidebox menu. 
@author edave 
@discussion This function creates the sidebox menu that contains email folders as a flat list. 
Shown first are the special email boxes (INBOX, Sent and Trash) as defined in the users
email settings.  Then the user created email folders (no subfolders are shown). 
*/
function CreateSidebox_FolderMenu($mailacct)
{
	// get a flat list of all email folders including all subfolders.
	$folder_list = $GLOBALS['phpgw']->msg->get_arg_value('folder_list', $mailacct);
	$delimiter = $GLOBALS['phpgw']->msg->get_arg_value('mailsvr_delimiter', $mailacct);

	// Initialise special folders to be blank. 
	// Dont worry about inbox as it always exists.
	$trash_folder_long = '';
	$sent_folder_long = '';

	// set $trash_folder_long or leave blank if no trash folder or trash folder pref is off
	$trash_folder_long = $GLOBALS['phpgw']->msg->get_arg_value('verified_trash_folder_long', $mailacct);

	// set $sent_folder_long or leave blank if no sent folder or sent folder pref is off
	if ($GLOBALS['phpgw']->msg->get_isset_pref('use_sent_folder', $mailacct) != False) 
	{
		// see if sent folder actually exists by searching through all valid folders
		$sent_folder_name = $GLOBALS['phpgw']->msg->get_pref_value('sent_folder_name', $mailacct);
		for ($i=0; (($i<count($folder_list))&&($sent_folder_long == ''));$i++) {
			if(($folder_list[$i]['acctnum'] == $mailacct) &&
				(($folder_list[$i]['folder_short'] == $sent_folder_name) || 
				($folder_list[$i]['folder_long'] == $sent_folder_name))) {
					$sent_folder_long = $folder_list[$i]['folder_long'];
			}
		}  
	}

	// Create Language specific titles for the special email folders
	$inbox_title = lang($GLOBALS['phpgw']->msg->get_common_langs('lang_inbox'));
	$trash_title = $GLOBALS['phpgw']->msg->get_pref_value('trash_folder_name', $mailacct);
	$sent_title = $GLOBALS['phpgw']->msg->get_pref_value('sent_folder_name', $mailacct);

	// Add new mail indicator at the end of INBOX title
	$inbox_info = $GLOBALS['phpgw']->msg->get_folder_status_info(array('folder'=>'INBOX','acctnum'=>$mailacct));
	$inbox_newcount = number_format($inbox_info['number_new']);
	if($inbox_newcount > 0) {
		$inbox_title .= ' ('.$inbox_newcount.')';
	}

	// Construct the special email folders as menu items in array $file
	$file = array();
	$file[] = array( 'text' => $inbox_title, 'no_lang' => True, 'link' => CreateSidebox_MenuLink($mailacct) );
	//$file[$inbox_title] = CreateSidebox_MenuLink($mailacct);
	if($sent_folder_long != '') { 
		// add only if sent folder is set
		$file[$sent_title] = CreateSidebox_MenuLink($mailacct,$sent_folder_long);
	}
	if($trash_folder_long != '') {  
		// add only if trash folder is set
		$file[$trash_title] = CreateSidebox_MenuLink($mailacct,$trash_folder_long);
	}

	// Add the rest of the user created folders to the array $file.
	// All subfolders are ignored to simplify the list.
	// Only show all user created folders if pref value is set to showall.
	// if ($GLOBALS['phpgw']->msg->get_pref_value('idots_folders',$mailacct) == 'showall') {
	// above user pref if statement disabled for the moment until option added to account settings page
		$subfolder_name = '';
		for ($i=0; $i<count($folder_list);$i++) {
			// leave suffix blank if folder is normal (no subfolders).
			$folder_suffix = ''; 
			$folder_page = 'email.uiindex.index';
			if($folder_list[$i]['acctnum'] == $mailacct) {
				// check for the special folders, we dont want to add them a second time.
   	        	if((($trash_folder_long != '') && ($folder_list[$i]['folder_long'] == $trash_folder_long)) ||
					(($send_folder_long != '') && ($folder_list[$i]['folder_long'] == $send_folder_long)) ||
					($folder_list[$i]['folder_long'] == 'INBOX') ) {
						// inbox, sent and trash folders are skipped;
				} else {
					$folder_title = $folder_list[$i]['folder_short'];
					// check if folder title includes subfolder names
					$subfolder_pos = strpos($folder_title,$delimiter);
					if($subfolder_pos === false) {
						// normal folder, doesn't contain subfolders.
						// so reset subfolder name to be blank;
						$subfolder_name = '';
					} else {
						$folder_title = substr($folder_title,0,$subfolder_pos);
						if($folder_title == $subfolder_name) {
							// skip duplicate subfolders if parent has been
							// already included by making folder title blank.
							$folder_title = '';
						} else {
							// this folder contains subfolders so add a 
							// suffix and point url to folder list page.
							$folder_suffix = ' [f]';
							$subfolder_name = $folder_title;
							$folder_page = 'email.uifolder.folder';
						}
					}
					// skip folders with blank titles
					if($folder_title != '') {
						// cut off title if its too long and add suffix         
						$folder_title = substr($folder_title,0,15).$folder_suffix;
						$folder_long = $folder_list[$i]['folder_long'];
						$file[] = array( 'text' => $folder_title, 'no_lang' => True, 'link' => CreateSidebox_MenuLink($mailacct,$folder_long,$folder_page) );
						//$file[$folder_title] = CreateSidebox_MenuLink($mailacct,$folder_long,$folder_page);
					}
				}
			}
		}
	// }  see comment at start of if statement 
	return $file;
}



/*!
@function Sidebox_EmailDataCheck
@abstract A function to test to see if we are able to have access to email data. 
@param $mailacct (integer) the mail account we want to refer to. 
@result (Boolean) If valid email data can be accessed successfully. 
@author edave 
@discussion In order to display any sideboxes we need to ensure that you have a 
successful connection to the email server. This success can be determinded by 
either a previous email access that cached the data or the current page attempted 
a successful login and a valid mail stream is present.  If there are no email 
settings then there can neither be cached data or a mail stream so this function
will then correctly return a false value. 
*/
function Sidebox_EmailDataCheck($mailacct)
{
	// return true if we have cached folder list
	$cached_folder_list = $GLOBALS['phpgw']->msg->_direct_access_arg_value('folder_list', $mailacct);
	if (count($cached_folder_list) > 0)
	{
		return True;
	}
	if (($GLOBALS['phpgw']->msg->get_isset_arg('mailsvr_stream', $mailacct) == True)
		&& ((string)$GLOBALS['phpgw']->msg->get_arg_value('mailsvr_stream', $mailacct) != ''))
	{
		return True;
	}
	// no valid email login assumed if failed above tests
	return False;
}







 /*
	Main code for constructing all this apps
	sideboxes as needed and if its possible. 

 */

	// Init some global variables with known values
	$allow_sidebox = False; 
	$sidebox_mailacct = 0;

	// Only allow email sideboxes if displaying a page 
	// where the msg object has been initialised and
	// there is valid email data present.
	if (is_object($GLOBALS['phpgw']->msg))
	{
		$sidebox_mailacct = $GLOBALS['phpgw']->msg->get_acctnum();
		$allow_sidebox = Sidebox_EmailDataCheck($sidebox_mailacct);
	}

	if ($allow_sidebox == True)
	{
		// Generate sidebox for navigation of email app
		// Only show if user pref is set to sidebox.
		if ($GLOBALS['phpgw']->msg->ok_toshow_sidemenu('basic') == True)
		//	&& ($GLOBALS['phpgw']->msg->get_pref_value('toolbar_type',$sidebox_mailacct) == 'sidebox')) 
		// above user pref disabled for the moment until option added to account settings page
		{
			$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
			display_sidebox($appname,$menu_title,CreateSidebox_EmailMenu($sidebox_mailacct));
		}

		// Generate sidebox to show email folders
		// Only show if user pref is not set to hidden.
		if ($GLOBALS['phpgw']->msg->ok_toshow_sidemenu('folderlist') == True)
		//	&& ($GLOBALS['phpgw']->msg->get_pref_value('idots_folders',$sidebox_mailacct) != 'hidden'))
		// above user pref disabled for the moment until option added to account settings page
		{
			$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Folders');
			display_sidebox($appname,$menu_title,CreateSidebox_FolderMenu($sidebox_mailacct));
		}
	}




}
?>

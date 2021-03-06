<?php
	/**************************************************************************\
	* AngleMail - E-Mail Module for eGroupWare					*
	* http://www.anglemail.org									*
	* http://www.egroupware.org									* 
	*/
	/**************************************************************************\
	* AngleMail - Bootstrap the mail_msg object						*
	* This file written by "Angles" Angelo Puglisi <angles@aminvestments.com>	*
	* Bootstrap the mail_	msg object									*
	* Copyright (C) 2002 Angelo Tony Puglisi (Angles)					*
	* -------------------------------------------------------------------------		*
	* This library is free software; you can redistribute it and/or modify it		*
	* under the terms of the GNU Lesser General Public License as published by	*
	* the Free Software Foundation; either version 2.1 of the License,			*
	* or any later version.											*
	* This library is distributed in the hope that it will be useful, but			*
	* WITHOUT ANY WARRANTY; without even the implied warranty of	*
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	*
	* See the GNU Lesser General Public License for more details.			*
	* You should have received a copy of the GNU Lesser General Public License	*
	* along with this library; if not, write to the Free Software Foundation,		*
	* Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA			*
	\**************************************************************************/
	
	/* $Id: class.msg_bootstrap.inc.php,v 1.6 2004/03/13 21:08:41 angles Exp $ */
	
	/*!
	@class msg_bootstrap
	@abstract Utility class shared throught out the email app which ensures the GLOBALS->msg exists and is logged in
	@param $do_login (boolean) defaults to True, most often you do not need to change this.
	@author Angles
	@discussion  only has one function which bootstraps the GLOBALS->msg  (if necessary). It should be safe to call 
	CreateObject on this class at any time for object $GLOBALS[phpgw]->msg_bootstrap because api is smart 
	enough to not re-create it and theres nothing in the constructor, so this bootstrap capability should be available 
	at any time to any code file. If there is a problem logging in, function GLOBALS["phpgw"]->msg->login_error() is called, 
	giving an error message, and the script exits.
	@example 
	$GLOBALS["phpgw"]->msg_bootstrap = CreateObject("email.msg_bootstrap");
	$GLOBALS['phpgw']->msg_bootstrap->login();
	## OR, if you care about debug info, an alternative is to use this, does the same thing but gives debug info.
	$GLOBALS['phpgw']->msg_bootstrap->ensure_mail_msg_exists('name of my function');
	@access public
	*/
	class msg_bootstrap
	{
		var $do_login = True;
		//var $do_login = False;
		var $do_login_ex = 0;
		
		// 0 = no debug; 1,2,3 will show increasingly verbose debug info
		var $debug_level=0;
		
		function msg_bootstrap()
		{
			if (defined("BS_LOGIN_NEVER") == False)
			{
				define('BS_LOGIN_NOT_SPECIFIED',0);
				// never log in no matter what
				define('BS_LOGIN_NEVER',1);
				// do not login unless required (caching in effect)
				define('BS_LOGIN_ONLY_IF_NEEDED',2);
				// login definately do it (if not caching)
				define('BS_LOGIN_YES',3);
				//define('BS_LOGIN_DEMAND_ONLY',3);
				//define('BS_LOGIN_NEEDED',4);
			}
			//return;
		}
		
		
		/* * * * 
		@function set_do_login
		@abstract whether to try to login to the mail server or not during a call to "ensure_mail_msg_exists". 
		OPTIONAL, default is True. Behavior depends on caching method.
		@param $do_login (boolean) 
		@author Angles
		@result (boolean) whatever the value var $this->do_login has on exiting the function.
		@discussion OPTONAL, default of True works for most situations. This do_login value is 
		used in this objects function "ensure_mail_msg_exists" where it is passed to the mail_msg class. 
		Again, this is OPTONAL, default of True works for most situations, such as 
		(1a) If session_cache_extreme is True, and do_login=True, this will _allow_ a server login, 
		if needed, only if the app needs to get data that is not already cached.
		(1b) If session_cache_extreme is False, and do_login=True, this will _always_ try to establish 
		a mail server stream at the beginning of every script run. 
		(2a and 2b) Setting do_login to False is useful in certain limited situations, such as the email settings page, 
		or the preferences page. There you want to set or get email preference data but you do NOT 
		require a login, or when there may be no preference data set yet, such as the first time a user 
		sets the preferences, so a login is not even possible. The preference data will be handled by the 
		mail_msg class as usual. Setting do_login to False for these occasions is OK no matter if 
		session_cache_extreme is True or False. 
		@access public
		*
		function set_do_login($do_login='##NOTHING##')
		{
			if (is_bool($do_login))
			{
				$this->do_login = $do_login;
			}
			return $this->do_login;
		}
		*/
		
		/*!
		@function set_do_login REIMPLEMENTATION
		@abstract whether to try to login to the mail server or not during a call to "ensure_mail_msg_exists". 
		OPTIONAL, default is True. Behavior depends on caching method.
		@param $do_login (boolean) 
		@author Angles
		@result (boolean) whatever the value var $this->do_login has on exiting the function.
		@discussion OPTONAL, default of True works for most situations. This do_login value is 
		used in this objects function "ensure_mail_msg_exists" where it is passed to the mail_msg class. 
		Again, this is OPTONAL, default of True works for most situations, such as 
		(1a) If session_cache_extreme is True, and do_login=True, this will _allow_ a server login, 
		if needed, only if the app needs to get data that is not already cached.
		(1b) If session_cache_extreme is False, and do_login=True, this will _always_ try to establish 
		a mail server stream at the beginning of every script run. 
		(2a and 2b) Setting do_login to False is useful in certain limited situations, such as the email settings page, 
		or the preferences page. There you want to set or get email preference data but you do NOT 
		require a login, or when there may be no preference data set yet, such as the first time a user 
		sets the preferences, so a login is not even possible. The preference data will be handled by the 
		mail_msg class as usual. Setting do_login to False for these occasions is OK no matter if 
		session_cache_extreme is True or False. 
		@access public
		*/
		function set_do_login($do_login='##NOTHING##', $called_by='not_provided')
		{
			if ($this->debug_level > 0) { echo 'ENTERING: msg_bootstrap: set_do_login: (called_by: '.$called_by.') param $do_login: ['.serialize($do_login).']'.'<br>'; } 
			// backward compat, when this was only true or false
			if (is_bool($do_login))
			{
				if ($do_login == True)
				{
					$this->do_login = True;
					$this->do_login_ex = BS_LOGIN_ONLY_IF_NEEDED;
				}
				else
				{
					$this->do_login = False;
					$this->do_login_ex = BS_LOGIN_NEVER;
				}
				// LEAVING HERE
				if ($this->debug_level > 0) { echo 'LEAVING: msg_bootstrap: set_do_login: (bool input) (called_by: '.$called_by.') $this->do_login: ['.$this->do_login.'] $this->do_login_ex: ['.$this->do_login_ex.'] '.'<br>'; }
				return $this->do_login;
			}
			elseif (is_int($do_login))
			{
				// new way has 3 possibilities
				switch($do_login)
				{
					case BS_LOGIN_NEVER:
						{
							$this->do_login = False;
							$this->do_login_ex = BS_LOGIN_NEVER;
							break;
						}
					case BS_LOGIN_ONLY_IF_NEEDED:
						{
							$this->do_login = True;
							$this->do_login_ex = BS_LOGIN_ONLY_IF_NEEDED;
							break;
						}
					case BS_LOGIN_YES:
						{
							$this->do_login = True;
							$this->do_login_ex = BS_LOGIN_YES;
							break;
						}
					default:
						{
							$this->do_login = True;
							$this->do_login_ex = BS_LOGIN_ONLY_IF_NEEDED;
						}
				}
			}
			else
			{
				$this->do_login = True;
				$this->do_login_ex = BS_LOGIN_ONLY_IF_NEEDED;
			}
			if ($this->debug_level > 0) { echo 'LEAVING: msg_bootstrap: set_do_login: (not bool input) (called_by: '.$called_by.') $this->do_login: ['.$this->do_login.'] $this->do_login_ex: ['.$this->do_login_ex.'] '.'<br>'; }
			return $this->do_login_ex;
		}
		
		/*!
		@function get_do_login
		@abstract get the value of var $this->do_login
		@result (boolean) the value var $this->do_login 
		@author Angles
		@discussion ?
		@access public
		*/
		function get_do_login()
		{
			return $this->do_login;
		}
		
		/*!
		@function get_do_login_ex
		@abstract ?
		@result (defined integer) the value var $this->do_login 
		@author Angles
		@discussion ?
		@access public
		*/
		function get_do_login_ex()
		{
			return $this->do_login_ex;
		}
		
		/*!
		@function login
		@abstract If you do not care to do complicated things with email, create this object and call this login function.
		@author Angles
		@discussion Alias to "ensure_mail_msg_exists", if you want debugging output capability then use that 
		function. If you just want to get email working quickly, use this function.
		@example 
			## email quickstart:
			$GLOBALS["phpgw"]->msg_bootstrap = CreateObject("email.msg_bootstrap");
			$GLOBALS['phpgw']->msg_bootstrap->login();
		*/
		function login()
		{
			return $this->ensure_mail_msg_exists('whatever called msg_bootstrap->login', 0);
		}
		
		/*!
		@function ensure_mail_msg_exists
		@abstract standard function to make sure a mail_msg object exists and is logged into the mailserver
		@param $called_by (string) used for debug output
		@param $debug_level (int) value 0 to 3, the prevailing debug level for the calling function. 
		@author Angles
		@discussion This process os the same for any email related code that needs the mail_msg object and 
		an open stream. This function calls msg->begin_request and thus all the complicated logic 
		associated with multiple accounts is handled there.
		*/
		function ensure_mail_msg_exists($called_by='not_provided', $debug_level=0)
		{
			if ($debug_level > $this->debug_level)
			{
				$this->debug_level = $debug_level;
			}
			if ($this->debug_level > 0) { echo 'ENTERING: msg_bootstrap: ensure_mail_msg_exists: (called_by: '.$called_by.')'.'<br>'; }
			
			// make sure do_login has been set
			if ($this->get_do_login_ex() == BS_LOGIN_NOT_SPECIFIED)
			{
				// this gives us a good general default value
				$tmp_prev_value = $this->get_do_login();
				$this->set_do_login($tmp_prev_value);
			}
			
			// make sure utility classes (like html widgets) exist for global access
			//$this->ensure_utility_classes($debug_level);
			
			if (is_object($GLOBALS['phpgw']->msg))
			//if ((isset($GLOBALS['phpgw']->msg))
			//&& (isset($GLOBALS['phpgw']->msg->been_constructed))
			//&& ($GLOBALS['phpgw']->msg->been_constructed == True)
			//)
			{
				if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_mail_msg_exists('.__LINE__.'): (called_by: '.$called_by.'): is_object test: $GLOBALS[phpgw]->msg is already set, do not create again<br>'; }
			}
			else
			{
				if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_mail_msg_exists('.__LINE__.'): (called_by: '.$called_by.'): $GLOBALS[phpgw]->msg is NOT set, creating mail_msg object<br>'; }
				$GLOBALS['phpgw']->msg = CreateObject("email.mail_msg");
				//$GLOBALS['phpgw']->msg =& CreateObject("email.mail_msg");
				//include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_msg_base.inc.php');
				//include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_msg_wrappers.inc.php');
				//include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_msg_display.inc.php');
				//$GLOBALS['phpgw']->msg =& new mail_msg;
				
				//hdr_info_envelope
				if
				(
					($GLOBALS['phpgw']->msg->force_sockets == True)
				|| 	(
					(extension_loaded('imap') == False)
					&& (defined("TYPETEXT") == False)
					)
				)
				{
					if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_mail_msg_exists('.__LINE__.'): imap is NOT builtin, or $force_sockets is True, and basic mail defines are not yet in namespace<br>'; }
					if ($this->debug_level > 1) { echo 'include_once :'.PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_sock_defs.inc.php<br>'; }
					include_once(PHPGW_INCLUDE_ROOT.'/email/inc/class.mail_dcom_sock_defs.inc.php');
				}
				
				if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_mail_msg_exists('.__LINE__.'): fyi, $GLOBALS[phpgw]->msg->been_constructed ['.serialize($GLOBALS['phpgw']->msg->been_constructed).']  <br>'; } 
				if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_mail_msg_exists('.__LINE__.'): $GLOBALS[phpgw]->msg created mail_msg object, now calling needed initialization function aka manual constructor function, "initialize_mail_msg"<br>'; } 
				$GLOBALS['phpgw']->msg->initialize_mail_msg();
			}
			
			if ($GLOBALS['phpgw']->msg->get_isset_arg('already_grab_class_args_gpc'))
			{
				// mail_msg had already run thru "begin_request", do not call it again
				if ($this->debug_level > 0) { echo 'msg_bootstrap: ensure_mail_msg_exists('.__LINE__.'): (called_by: '.$called_by.'): LEAVING , msg object already initialized<br>'; }
				return True;
			}
			
			$args_array = Array();
			// should we log in or not
			if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_mail_msg_exists: (called_by: '.$called_by.'): $this->do_login: ['.serialize($this->do_login).']<br>'; }
			$args_array['do_login'] = $this->do_login;
			if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_mail_msg_exists: (called_by: '.$called_by.'): $this->do_login_ex: ['.serialize($this->do_login_ex).']<br>'; }
			$args_array['do_login_ex'] = $this->do_login_ex;
			if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_mail_msg_exists: (called_by: '.$called_by.'): $args_array: ['.serialize($args_array).']<br>'; }
			
			// "start your engines"
			if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_mail_msg_exists: (called_by: '.$called_by.'): call msg->begin_request with args array:<pre>'; print_r($args_array); echo '</pre>'; }
			$some_stream = $GLOBALS['phpgw']->msg->begin_request($args_array);
			// error if login failed
			if (($args_array['do_login'] == True)
			&& (!$some_stream))
			{
				$GLOBALS['phpgw']->msg->login_error($GLOBALS['PHP_SELF'].', msg_bootstrap: ensure_mail_msg_exists(), called_by: '.$called_by);
			}
			// login error will halt this script execution
			// else all is good to go and script continues... 
			if ($this->debug_level > 2) { echo 'msg_bootstrap: about to leave ensure_mail_msg_exists, $GLOBALS[] DUMP:<pre>'; print_r($GLOBALS); echo '</pre>'; }
			if ($this->debug_level > 0) { echo 'EXIT: msg_bootstrap: ensure_mail_msg_exists: (called_by: '.$called_by.')'.'<br>'; }
		}
		
		/*!
		@function ensure_utility_classes
		@abstract utility function for bootstraping, makes sure ancillary global objects are in existance.
		@param $debug_level (int) the bootstrap code adopts the debug level of the calling object, it is passed as a param.
		@author Angles
		@discussion This is a utility function called by this-> ensure_mail_msg_exists. Email uses 
		utility c lasses from both email and api utility objects, such as html_widgets, 
		this function makes sure the most commonly used of these are available for global access throughout 
		the email code. Right now this is private, used only by this class itself.
		@access private 
		*/
		function ensure_utility_classes($debug_level=0)
		{
			// DEBUG - override debug_level param
			//$debug_level = 3;
			
			if ($this->debug_level > 0) { echo 'ENTERING: msg_bootstrap: ensure_utility_classes: <br>'; }
			
			if (is_object($GLOBALS['phpgw']->widgets))
			{
				if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_utility_classes: is_object test: $GLOBALS[phpgw]->widgets is already set, do not create again<br>'; }
			}
			else
			{
				if ($this->debug_level > 1) { echo 'msg_bootstrap: ensure_utility_classes: $GLOBALS[phpgw]->widgets is NOT set, creating html_widgets object<br>'; }
				$my_widgets = CreateObject("email.html_widgets");
				$GLOBALS['phpgw']->widgets = $my_widgets;
			}
			
			
			if ($this->debug_level > 0) { echo 'EXIT: msg_bootstrap: ensure_utility_classes: <br>'; }
		}

	}
	
?>

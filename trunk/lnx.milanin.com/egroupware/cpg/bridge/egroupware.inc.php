<?php
// ------------------------------------------------------------------------- //
//  Coppermine Photo Gallery                                                 //
// ------------------------------------------------------------------------- //
//  Copyright (C) 2002,2003  Gr�ory DEMAR <gdemar@wanadoo.fr>               //
//  http://www.chezgreg.net/coppermine/                                      //
// ------------------------------------------------------------------------- //
//  Based on PHPhotoalbum by Henning Stverud <henning@stoverud.com>         //
//  http://www.stoverud.com/PHPhotoalbum/                                    //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
// ------------------------------------------------------------------------- //
//
//  eGw Integration for Coppermine
//
// ------------------------------------------------------------------------- //

// ------------------------------------------------------------------------- //
//  Modify the values below according to your egroupware installation
// ------------------------------------------------------------------------- //

// Login and Logout links
$cms_login_tgt =  $GLOBALS['phpgw_info']['server']['webserver_url'] ."/login.php";
$cms_logout_tgt = $GLOBALS['phpgw_info']['server']['webserver_url'] ."/logout.php";

// debug("cms_login_tgt", $cms_login_tgt);

// Authenticate a user using phplib session, auth, and perm.
function cms_authenticate() {
   global $USER_DATA;
   // debug("cms_authenticate", "start");
   $USER_DATA =  egw_get_userdata($USER_DATA);
   
   $USER_DATA['user_password'] = '********';

      define('USER_ID', (int)$USER_DATA['user_id']);
      define('USER_NAME', $USER_DATA['user_name']);
      define('USER_GROUP', $USER_DATA['group_name']);
      if(!empty($USER_DATA['user_group'])) {
         define('USER_GROUP_SET', '(' . $USER_DATA['user_group'] . ($USER_DATA['user_lang'] != '' ? ',' . $USER_DATA['user_lang'] : '') . ')');
      } else {
         define('USER_GROUP_SET', '(' . $USER_DATA['group_id'] . ')');
      }
      if(isset($USER_DATA['has_admin_access'])) {
         define('USER_IS_ADMIN', (int)$USER_DATA['has_admin_access']);
      }
      define('USER_CAN_SEND_ECARDS', (int)$USER_DATA['can_send_ecards']);
      define('USER_CAN_RATE_PICTURES', (int)$USER_DATA['can_rate_pictures']);
      define('USER_CAN_POST_COMMENTS', (int)$USER_DATA['can_post_comments']);
      define('USER_CAN_UPLOAD_PICTURES', (int)$USER_DATA['can_upload_pictures']);
      define('USER_CAN_CREATE_ALBUMS', (int)$USER_DATA['can_create_albums']);
      define('USER_UPLOAD_FORM', (int)$USER_DATA['upload_form_config']);
      define('CUSTOMIZE_UPLOAD_FORM', (int)$USER_DATA['custom_user_upload']);
      define('NUM_FILE_BOXES', (int)$USER_DATA['num_file_upload']);
      define('NUM_URI_BOXES', (int)$USER_DATA['num_URI_upload']);

   
   // debug("cms_authenticate", "done");
}

// ----------------------------------------------------
// egw_get_userdata ()
//
// attempts to retrieve userdata..
// 
// ----------------------------------------------------
function egw_get_userdata ($USER_DATA) {

   // debug('egw_get_userdata::begin', $USER_DATA['user_name']);
   // start out anonymous
   $username = 'Anonymous';
   
  // set username to eGw username
  $username = $GLOBALS['phpgw_info']['user']['account_lid'];      
   
   if(!is_array($USER_DATA) || !array_key_exists('user_name',$USER_DATA)) {
      $USER_DATA['user_name'] = '';
   }
   
   // debug("egw_get_userdata::getting username", $username);
   // debug("egw_get_userdata::not getting userdata username", $USER_DATA['user_name']);
   if($USER_DATA['user_name'] != $username) {
      // get user data from coppermine
      // debug("egw_get_userdata::getting userdata for", $username);
      // debug("egw_get_userdata::not getting userdata for", $USER_DATA['user_name']);
      $USER_DATA = egw_get_userdbdata ($username);
   }
   
   if(!$GLOBALS['phpgw']->session->appsession('session_data','cpgUSER_DATA')) {
      // debug("egw_get_userdata::session", 'registering');
      $GLOBALS['phpgw']->session->appsession('session_data','cpgUSER_DATA',$USER_DATA);
   } 
   
    
   // debug("sess", $sess);
   
   // debug("egw_get_userdata::userdata",$USER_DATA['user_name']);
   
   // use language and charset from eGW
   global $USER;
   $lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
   $egw2cpg_lang = array(
      'bg' => 'bulgarian',
      'cz' => 'czech',
      'da' => 'danish',
      'de' => 'german',
      'en' => 'english',
      'el' => 'greek',
      'es-ca' => 'spanish',
      'es-es' => 'spanish',
      'fi' => 'finnish',
      'fr' => 'french',
      'hu' => 'hungarian',
      'it' => 'italian',
      'ja' => 'japanese',
      'nl' => 'dutch',
      'no' => 'norwegian',
      'pl' => 'polish',
      'pt-br' => 'brazilian_protuguese',
      'ru' => 'russian',
      'sl' => 'slovenian',
      'sv' => 'swedish',
      'zh' => 'chinese_gb',
      'zt' => 'chinese_big5',
   );
   $cpg_lang = $egw2cpg_lang[$lang] . ($GLOBALS['phpgw']->translation->charset() == 'utf-8' ? '-utf-8' : '');
   if (file_exists('lang/'.$cpg_lang.'.php'))
   {
      $USER['lang'] = $cpg_lang;
      //echo "<p>useing lang='$cpg_lang'</p>\n";
   }
   return $USER_DATA;
}


// ----------------------------------------------------
// egw_get_userdbdata ()
//
// attempts to retrieve userdata from db.
// 
// ----------------------------------------------------
function egw_get_userdbdata ($username) {
   
   global $phpgw, $CONFIG;
   
   $USER_DATA = null;
   
   // check if logged in
   if(true) {

      $sql = "SELECT * " . "FROM {$CONFIG['TABLE_USERS']}, {$CONFIG['TABLE_USERGROUPS']} " . "WHERE user_group = group_id " . "AND user_active = 'YES' " . "AND user_name = '$username' ";

      // debug("sql", $sql);
      $phpgw->db->query($sql);
      if ( $phpgw->db->next_record() ) {
         $USER_DATA = $phpgw->db->Record;
         // debug("user", "found");
         // debug("USER_DATA", $USER_DATA);
      } elseif( ($USER_DATA = add_egw_user($username))) {  
         //egw_debug('user','added');
      } else {
         //egw_debug("user", "not found");
      }
   
      // debug("egw_get_userdbdata::user","logged in");
      $current_time = time();
      //unset($USER_DATA['user_password']);
      /*
      $USER_DATA['user_password'] = '********';

      define('USER_ID', (int)$USER_DATA['user_id']);
      define('USER_NAME', $USER_DATA['user_name']);
      define('USER_GROUP', $USER_DATA['group_name']);
      define('USER_GROUP_SET', '(' . $USER_DATA['user_group'] . ($USER_DATA['user_lang'] != '' ? ',' . $USER_DATA['user_lang'] : '') . ')');
      define('USER_IS_ADMIN', (int)$USER_DATA['has_admin_access']);
      define('USER_CAN_SEND_ECARDS', (int)$USER_DATA['can_send_ecards']);
      define('USER_CAN_RATE_PICTURES', (int)$USER_DATA['can_rate_pictures']);
      define('USER_CAN_POST_COMMENTS', (int)$USER_DATA['can_post_comments']);
      define('USER_CAN_UPLOAD_PICTURES', (int)$USER_DATA['can_upload_pictures']);
      define('USER_CAN_CREATE_ALBUMS', (int)$USER_DATA['can_create_albums']);
      define('USER_UPLOAD_FORM', (int)$USER_DATA['upload_form_config']);
      define('CUSTOMIZE_UPLOAD_FORM', (int)$USER_DATA['custom_user_upload']);
      define('NUM_FILE_BOXES', (int)$USER_DATA['num_file_upload']);
      define('NUM_URI_BOXES', (int)$USER_DATA['num_URI_upload']);
      
     */ 
      $last_visit = time();
      // debug("last_visit", $last_visit);
      $sql = "UPDATE ". $CONFIG['TABLE_USERS'] ." 
	      SET user_lastvisit =  NOW()
			WHERE user_id = '". $USER_DATA['user_id'] ."'";
      if ( !$phpgw->db->query($sql) ) {
         // debug('sql', $sql, "now");
      }

   } else {
      // not logged in - public/anon user.
      $USER_DATA = egw_get_anondbdata();
   }
   // debug("egw_get_userdbdata::USER_DATA",$USER_DATA);
   return $USER_DATA; 
}

// ----------------------------------------------------
// add_egw_user()
//
// Adds new user to cpg database, basing on the information
// found in eGw array.
// ----------------------------------------------------
function add_egw_user ($username) {
       
   global $phpgw, $CONFIG;

   $USER_DATA = null;
	
   $active = 'YES';
   $act_key = '';
   $password = md5($username);
   $email = $GLOBALS['phpgw_info']['user']['email'];
   $location = '';
   $interests = '';
   $website = '';
   $occupation = '';
   $group = !isset($GLOBALS['phpgw_info']['user']['apps']['admin']) || !$GLOBALS['phpgw_info']['user']['apps']['admin'] ? 2 : 1;

   // creating new user
   $sql = "INSERT INTO {$CONFIG['TABLE_USERS']} " . 
	        	"(user_regdate, 
		  	user_active, 
		  	user_actkey, 
		  	user_name, 
		  	user_password, 
		  	user_email, 
		  	user_location, 
		  	user_interests, 
		  	user_website, 
		  	user_occupation,
			user_group) " . 
		  "VALUES (
		  	NOW(), 
			'$active', 
			'$act_key', 
			'$username', 
			'$password', 
			'$email', 
			'$location', 
			'$interests', 
			'$website', 
			'$occupation',
			$group )";

   //egw_debug("sql", $sql);
   $phpgw->db->query($sql);
   
   // get info back from db
   $sql = sprintf("SELECT * FROM %s WHERE user_name = '%s'", $CONFIG['TABLE_USERS'] , $username);
  //egw_debug("sql", $sql);
   $phpgw->db->query($sql);
   if ( $phpgw->db->next_record() ) {
      $USER_DATA = $phpgw->db->Record;
      //egw_debug("added user", "found");
   } else {
      //egw_debug("added user", "not found");
   }	
	
   return $USER_DATA;
}

// ----------------------------------------------------
// egw_get_anondbdata ()
//
// attempts to retrieve public user data from db.
// 
// ----------------------------------------------------
function egw_get_anondbdata () {
   
   global $phpgw, $CONFIG;
   
   //egw_debug("thispage_id", $thispage_id);
   
   $USER_DATA = null;
   
   $sql = "SELECT * FROM {$CONFIG['TABLE_USERGROUPS']} WHERE group_id = 3";

   //egw_debug("sql", $sql);
   $phpgw->db->query($sql);
   if ( $phpgw->db->next_record() ) {
      $USER_DATA = $phpgw->db->Record;
      // debug("anon group", "found");
      // debug("USER_DATA", $USER_DATA);
   } elseif( ($USER_DATA = add_egw_user($username))) {  
      //egw_debug('user','added');
   } else {
      //egw_debug("user", "not found");
   }
   $USER_DATA['user_name'] = $USER_DATA['group_name'];
   $USER_DATA['user_id'] = 0;

  /* 
   define('USER_ID', 0);
   define('USER_NAME', $USER_DATA['group_name']);
   $USER_DATA['user_name'] = $USER_DATA['group_name'];
   $USER_DATA['username'] = $USER_DATA['group_name'];
   define('USER_GROUP', $USER_DATA['group_name']);
   define('USER_GROUP_SET', '(' . $USER_DATA['group_id'] . ')');
   define('USER_IS_ADMIN', 0);
   define('USER_CAN_SEND_ECARDS', (int)$USER_DATA['can_send_ecards']);
   define('USER_CAN_RATE_PICTURES', (int)$USER_DATA['can_rate_pictures']);
   define('USER_CAN_POST_COMMENTS', (int)$USER_DATA['can_post_comments']);
   define('USER_CAN_UPLOAD_PICTURES', (int)$USER_DATA['can_upload_pictures']);
   define('USER_CAN_CREATE_ALBUMS', 0);
   define('USER_UPLOAD_FORM', (int)$USER_DATA['upload_form_config']);
   define('CUSTOMIZE_UPLOAD_FORM', (int)$USER_DATA['custom_user_upload']);
   define('NUM_FILE_BOXES', (int)$USER_DATA['num_file_upload']);
   define('NUM_URI_BOXES', (int)$USER_DATA['num_URI_upload']);
    */  
   // //egw_debug("egw_get_anondbdata::USER_DATA",$USER_DATA);
   return $USER_DATA; 
}

function cms_pageheader($section='', $meta = '') {
   
   $output = '';

   return $output;

}

function cms_pagefooter() {
   // debug("cms_pagefooter", "start", "delayed");

   $output = '';
  
   ob_start();
  
   $GLOBALS['phpgw']->common->phpgw_footer();
  
   $output = ob_get_contents();
              
   ob_end_clean();
  
   return $output;

}

// Not needed
function cms_user_get_profile() {

   global $USER;

   // $_SERVER['HTTP_ACCEPT_LANGUAGE'] = what does eGw use for lang setting?;

   if($sessiondata = $GLOBALS['phpgw']->session->appsession('session_data','cpgUSER')) {
      $USER = $sessiondata;
   }
      

   if (!isset($USER['am'])) $USER['am'] = 1;
}

// Not needed
function cms_user_save_profile() {

   global $USER;

   $GLOBALS['phpgw']->session->appsession('session_data','cpgUSER',$USER);

}

?>

<?php
// ------------------------------------------------------------------------- //
//  Coppermine Photo Gallery                                                 //
// ------------------------------------------------------------------------- //
//  Copyright (C) 2002,2003  Grégory DEMAR <gdemar@wanadoo.fr>               //
//  http://www.chezgreg.net/coppermine/                                      //
// ------------------------------------------------------------------------- //
//  Based on PHPhotoalbum by Henning Støverud <henning@stoverud.com>         //
//  http://www.stoverud.com/PHPhotoalbum/                                    //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
// ------------------------------------------------------------------------- //
//
//  back-end Integration for Coppermine
//
// ------------------------------------------------------------------------- //

// ------------------------------------------------------------------------- //
//  Modify the values below according to your back-end installation
// ------------------------------------------------------------------------- //


// don't use cache
$cachetimeout = -1;

// load psl/be configuration
$pwd = getcwd();
chdir('/path/to/public_html');
include_once("config.php");
chdir($pwd);

// $_PSL['debug'] = true;

// Login and Logout links
$cms_login_tgt = $_PSL['absoluteurl'] . '/login.php';
$cms_logout_tgt = $_PSL['absoluteurl'] . '/login.php?logout=yes&redirect=' . urlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);


// debug("cms_login_tgt", $cms_login_tgt);

// Authenticate a user using phplib session, auth, and perm.
function cms_authenticate() {
   global $USER_DATA;
   // debug("cms_authenticate", "start");
   $USER_DATA =  be_get_userdata($USER_DATA);
   
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
// be_get_userdata ()
//
// attempts to retrieve userdata..
// 
// ----------------------------------------------------
function be_get_userdata ($USER_DATA) {
   global $sess, $auth, $perm;
 
   // debug('be_get_userdata::begin', $USER_DATA['user_name']);
   // start out anonymous
   $username = 'Anonymous';
   
   // check if logged in
   if($perm->have_perm('user')) {
      // set username from auth array
      $username = $auth->auth['uname'];      
   }
   
   if(!is_array($USER_DATA) || !array_key_exists('user_name',$USER_DATA)) {
      $USER_DATA['user_name'] = '';
   }
   
   // debug("be_get_userdata::getting username", $username);
   // debug("be_get_userdata::not getting userdata username", $USER_DATA['user_name']);
   if($USER_DATA['user_name'] != $username) {
      // get user data from coppermine
      // debug("be_get_userdata::getting userdata for", $username);
      // debug("be_get_userdata::not getting userdata for", $USER_DATA['user_name']);
      $USER_DATA = be_get_userdbdata ($username);
   }
   
   if(!$sess->is_registered('USER_DATA')) {
      // debug("be_get_userdata::session", 'registering');
      $sess->register('USER_DATA');
   } 
   
    
  // //be_debug("sess", $sess);
   
   // debug("be_get_userdata::userdata",$USER_DATA['user_name']);
   return $USER_DATA;
}


// ----------------------------------------------------
// be_get_userdbdata ()
//
// attempts to retrieve userdata from db.
// 
// ----------------------------------------------------
function be_get_userdbdata ($username) {
   
   global $sess, $auth, $perm, $CONFIG;
   
   $USER_DATA = null;
   
   // check if logged in
   if($perm->have_perm('user')) {

      $sql = "SELECT * " . "FROM {$CONFIG['TABLE_USERS']}, {$CONFIG['TABLE_USERGROUPS']} " . "WHERE user_group = group_id " . "AND user_active = 'YES' " . "AND user_name = '$username' ";

      //be_debug("sql", $sql);
      $db = pslNew('slashDB');
      $db->query($sql);
      if ( $db->next_record() ) {
         $USER_DATA = $db->Record;
         // debug("user", "found");
         // debug("USER_DATA", $USER_DATA);
      } elseif( ($USER_DATA = add_backend_user($username))) {  
         //be_debug('user','added');
      } else {
         //be_debug("user", "not found");
      }
   
      //be_debug("be_get_userdbdata::user","logged in");
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
      if ( !$db->query($sql) ) {
         //be_debug('sql', $sql);
      }

   } else {
      // not logged in - public/anon user.
      $USER_DATA = be_get_anondbdata();
   }
   // //be_debug("be_get_userdbdata::USER_DATA",$USER_DATA);
   return $USER_DATA; 
}

// ----------------------------------------------------
// add_backend_user()
//
// Adds new user to phpBB database, basing on the information
// found in back-end auth array.
// ----------------------------------------------------
function add_backend_user ($username) {
       
   global $auth, $CONFIG;

   $USER_DATA = null;
	
   $db = pslNew("slashDB");
	
   $active = 'YES';
   $act_key = '';
   $password = md5($username);
   $email = $auth->auth['email'];
   $location = '';
   $interests = '';
   $website = '';
   $occupation = '';

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
		  	user_occupation) " . 
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
			'$occupation' )";

   //be_debug("sql", $sql);
   $db->query($sql);
   
   // get info back from db
   $sql = sprintf("SELECT * FROM %s WHERE user_name = '%s'", $CONFIG['TABLE_USERS'] , $username);
  //be_debug("sql", $sql);
   $db->query($sql);
   if ( $db->next_record() ) {
      $USER_DATA = $db->Record;
      //be_debug("added user", "found");
   } else {
      //be_debug("added user", "not found");
   }	
	
   return $USER_DATA;
}

// ----------------------------------------------------
// be_get_anondbdata ()
//
// attempts to retrieve public user data from db.
// 
// ----------------------------------------------------
function be_get_anondbdata () {
   
   global $sess, $auth, $perm, $CONFIG;
   
   //be_debug("thispage_id", $thispage_id);
   
   $USER_DATA = null;
   
   $sql = "SELECT * FROM {$CONFIG['TABLE_USERGROUPS']} WHERE group_id = 3";

   //be_debug("sql", $sql);
   $db = pslNew('slashDB');
   $db->query($sql);
   if ( $db->next_record() ) {
      $USER_DATA = $db->Record;
      // debug("anon group", "found");
      // debug("USER_DATA", $USER_DATA);
   } elseif( ($USER_DATA = add_backend_user($username))) {  
      //be_debug('user','added');
   } else {
      //be_debug("user", "not found");
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
   // //be_debug("be_get_anondbdata::USER_DATA",$USER_DATA);
   return $USER_DATA; 
}

function cms_pageheader($section, $meta = '') {
   global $_PSL;
   
   $pagetitle   = pslgetText('Gallery'); // The title to be displayed in the browser top
   
   $output = getHeader($pagetitle,$_PSL['metatags']);

   return $output;

}

function cms_pagefooter() {
   // debug("cms_pagefooter", "start", "delayed");
   $output = getFooter();
   page_close();
   return $output;

}

// Not needed
function cms_user_get_profile() {
   global $_PSL;

   $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $_PSL['lang'];
}

// Not needed
function cms_user_save_profile() {

}


?>

-- phpMyAdmin SQL Dump
-- version 2.6.4-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: 62.149.150.38
-- Generation Time: Feb 16, 2006 at 03:31 PM
-- Server version: 4.0.25
-- PHP Version: 4.3.11
-- 
-- Database: `Sql73134_1`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_Category`
-- 

CREATE TABLE `LUM_Category` (
  `CategoryID` int(2) NOT NULL auto_increment,
  `Name` varchar(100) NOT NULL default '',
  `Description` text NOT NULL,
  `Order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`CategoryID`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_CategoryBlock`
-- 

CREATE TABLE `LUM_CategoryBlock` (
  `CategoryID` int(11) NOT NULL default '0',
  `UserID` int(11) NOT NULL default '0',
  `Blocked` enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (`CategoryID`,`UserID`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_CategoryRoleBlock`
-- 

CREATE TABLE `LUM_CategoryRoleBlock` (
  `CategoryID` int(11) NOT NULL default '0',
  `RoleID` int(11) NOT NULL default '0',
  `Blocked` enum('1','0') NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_Clipping`
-- 

CREATE TABLE `LUM_Clipping` (
  `ClippingID` int(11) NOT NULL auto_increment,
  `UserID` int(11) NOT NULL default '0',
  `Label` varchar(30) NOT NULL default '',
  `Contents` text NOT NULL,
  PRIMARY KEY  (`ClippingID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_Comment`
-- 

CREATE TABLE `LUM_Comment` (
  `CommentID` int(8) NOT NULL auto_increment,
  `DiscussionID` int(8) NOT NULL default '0',
  `AuthUserID` int(10) NOT NULL default '0',
  `DateCreated` datetime default NULL,
  `EditUserID` int(10) default NULL,
  `DateEdited` datetime default NULL,
  `WhisperUserID` int(11) default NULL,
  `Body` text,
  `FormatType` varchar(20) default NULL,
  `Deleted` enum('1','0') NOT NULL default '0',
  `DateDeleted` datetime default NULL,
  `DeleteUserID` int(10) NOT NULL default '0',
  `RemoteIp` varchar(100) default '',
  PRIMARY KEY  (`CommentID`,`DiscussionID`)
) TYPE=MyISAM AUTO_INCREMENT=1024 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_CommentBlock`
-- 

CREATE TABLE `LUM_CommentBlock` (
  `BlockingUserID` int(11) NOT NULL default '0',
  `BlockedCommentID` int(11) NOT NULL default '0',
  `Blocked` enum('1','0') NOT NULL default '1'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_Discussion`
-- 

CREATE TABLE `LUM_Discussion` (
  `DiscussionID` int(8) NOT NULL auto_increment,
  `AuthUserID` int(10) NOT NULL default '0',
  `WhisperUserID` int(11) NOT NULL default '0',
  `FirstCommentID` int(11) NOT NULL default '0',
  `LastUserID` int(11) NOT NULL default '0',
  `Active` enum('1','0') NOT NULL default '1',
  `Closed` enum('1','0') NOT NULL default '0',
  `Sticky` enum('1','0') NOT NULL default '0',
  `Name` varchar(100) NOT NULL default '',
  `DateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `DateLastActive` datetime NOT NULL default '0000-00-00 00:00:00',
  `CountComments` int(4) NOT NULL default '1',
  `CategoryID` int(11) default NULL,
  `WhisperToLastUserID` int(11) default NULL,
  `WhisperFromLastUserID` int(11) default NULL,
  `DateLastWhisper` datetime default NULL,
  `TotalWhisperCount` int(11) NOT NULL default '0',
  PRIMARY KEY  (`DiscussionID`)
) TYPE=MyISAM AUTO_INCREMENT=130 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_DiscussionUserWhisperFrom`
-- 

CREATE TABLE `LUM_DiscussionUserWhisperFrom` (
  `DiscussionID` int(11) NOT NULL default '0',
  `WhisperFromUserID` int(11) NOT NULL default '0',
  `LastUserID` int(11) NOT NULL default '0',
  `CountWhispers` int(11) NOT NULL default '0',
  `DateLastActive` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`DiscussionID`,`WhisperFromUserID`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_DiscussionUserWhisperTo`
-- 

CREATE TABLE `LUM_DiscussionUserWhisperTo` (
  `DiscussionID` int(11) NOT NULL default '0',
  `WhisperToUserID` int(11) NOT NULL default '0',
  `LastUserID` int(11) NOT NULL default '0',
  `CountWhispers` int(11) NOT NULL default '0',
  `DateLastActive` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`DiscussionID`,`WhisperToUserID`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_IpHistory`
-- 

CREATE TABLE `LUM_IpHistory` (
  `IpHistoryID` int(11) NOT NULL auto_increment,
  `RemoteIp` varchar(30) NOT NULL default '',
  `UserID` int(11) NOT NULL default '0',
  `DateLogged` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`IpHistoryID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_Role`
-- 

CREATE TABLE `LUM_Role` (
  `RoleID` int(2) NOT NULL auto_increment,
  `Name` varchar(100) NOT NULL default '',
  `Icon` varchar(155) NOT NULL default '',
  `Description` varchar(200) NOT NULL default '',
  `CanLogin` enum('1','0') NOT NULL default '1',
  `CanPostDiscussion` enum('1','0') NOT NULL default '1',
  `CanPostComment` enum('1','0') NOT NULL default '1',
  `CanPostHTML` enum('1','0') NOT NULL default '1',
  `AdminUsers` enum('1','0') NOT NULL default '0',
  `AdminCategories` enum('1','0') NOT NULL default '0',
  `MasterAdmin` enum('1','0') NOT NULL default '0',
  `ShowAllWhispers` enum('1','0') NOT NULL default '0',
  `CanViewIps` enum('1','0') NOT NULL default '0',
  `Active` enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (`RoleID`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_Style`
-- 

CREATE TABLE `LUM_Style` (
  `StyleID` int(3) NOT NULL auto_increment,
  `AuthUserID` int(11) NOT NULL default '0',
  `Name` varchar(50) NOT NULL default '',
  `Url` varchar(255) NOT NULL default '',
  `PreviewImage` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`StyleID`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_User`
-- 

CREATE TABLE `LUM_User` (
  `UserID` int(10) NOT NULL auto_increment,
  `RoleID` int(2) NOT NULL default '0',
  `StyleID` int(3) NOT NULL default '1',
  `CustomStyle` varchar(255) default NULL,
  `FirstName` varchar(50) NOT NULL default '',
  `LastName` varchar(50) NOT NULL default '',
  `Name` varchar(20) NOT NULL default '',
  `Password` varchar(32) NOT NULL default '',
  `VerificationKey` varchar(50) NOT NULL default '',
  `EmailVerificationKey` varchar(50) default NULL,
  `Email` varchar(200) NOT NULL default '',
  `UtilizeEmail` enum('1','0') NOT NULL default '0',
  `ShowName` enum('1','0') NOT NULL default '1',
  `Icon` varchar(255) default NULL,
  `Picture` varchar(255) default NULL,
  `Attributes` text NOT NULL,
  `CountVisit` int(8) NOT NULL default '0',
  `CountDiscussions` int(8) NOT NULL default '0',
  `CountComments` int(8) NOT NULL default '0',
  `DateFirstVisit` datetime NOT NULL default '0000-00-00 00:00:00',
  `DateLastActive` datetime NOT NULL default '0000-00-00 00:00:00',
  `RemoteIp` varchar(100) NOT NULL default '',
  `LastDiscussionPost` datetime default NULL,
  `DiscussionSpamCheck` int(11) NOT NULL default '0',
  `LastCommentPost` datetime default NULL,
  `CommentSpamCheck` int(11) NOT NULL default '0',
  `UserBlocksCategories` enum('1','0') NOT NULL default '0',
  `DefaultFormatType` varchar(20) default NULL,
  `SendNewApplicantNotifications` enum('1','0') NOT NULL default '0',
  `Discovery` text,
  `Settings` text,
  PRIMARY KEY  (`UserID`)
) TYPE=MyISAM AUTO_INCREMENT=345 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_UserBlock`
-- 

CREATE TABLE `LUM_UserBlock` (
  `BlockingUserID` int(11) NOT NULL default '0',
  `BlockedUserID` int(11) NOT NULL default '0',
  `Blocked` enum('1','0') NOT NULL default '1'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_UserBookmark`
-- 

CREATE TABLE `LUM_UserBookmark` (
  `UserID` int(10) NOT NULL default '0',
  `DiscussionID` int(8) NOT NULL default '0',
  PRIMARY KEY  (`UserID`,`DiscussionID`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_UserDiscussionWatch`
-- 

CREATE TABLE `LUM_UserDiscussionWatch` (
  `UserID` int(10) NOT NULL default '0',
  `DiscussionID` int(8) NOT NULL default '0',
  `CountComments` int(11) NOT NULL default '0',
  `LastViewed` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`UserID`,`DiscussionID`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_UserRoleHistory`
-- 

CREATE TABLE `LUM_UserRoleHistory` (
  `UserID` int(10) NOT NULL default '0',
  `RoleID` int(2) NOT NULL default '0',
  `Date` datetime NOT NULL default '0000-00-00 00:00:00',
  `AdminUserID` int(10) NOT NULL default '0',
  `Notes` varchar(200) default NULL,
  `RemoteIp` varchar(100) default NULL,
  KEY `UserID` (`UserID`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `LUM_UserSearch`
-- 

CREATE TABLE `LUM_UserSearch` (
  `SearchID` int(11) NOT NULL auto_increment,
  `Label` varchar(30) NOT NULL default '',
  `UserID` int(11) NOT NULL default '0',
  `Keywords` varchar(100) NOT NULL default '',
  `Type` enum('Users','Topics','Comments') NOT NULL default 'Topics',
  PRIMARY KEY  (`SearchID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_bannlist`
-- 

CREATE TABLE `arsc_bannlist` (
  `id` int(11) NOT NULL auto_increment,
  `ip` char(15) NOT NULL default '',
  `until` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `ip` (`ip`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_guestbooks`
-- 

CREATE TABLE `arsc_guestbooks` (
  `id` int(11) NOT NULL auto_increment,
  `link_user` int(11) NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `author` varchar(64) NOT NULL default '',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_user` (`link_user`),
  KEY `datum` (`date`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_layouts`
-- 

CREATE TABLE `arsc_layouts` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `default_font_face` varchar(64) NOT NULL default '',
  `default_font_color` varchar(24) NOT NULL default '',
  `default_font_size` varchar(24) NOT NULL default '',
  `small_font_face` varchar(64) NOT NULL default '',
  `small_font_color` varchar(24) NOT NULL default '',
  `small_font_size` varchar(24) NOT NULL default '',
  `default_background_color` varchar(24) NOT NULL default '',
  `default_foreground_color` varchar(24) NOT NULL default '',
  `template_languageselection` text NOT NULL,
  `template_home` text NOT NULL,
  `template_register` text NOT NULL,
  `template_roomlist` text NOT NULL,
  `template_userlist` text NOT NULL,
  `template_input` text NOT NULL,
  `template_input_nojavascript` text NOT NULL,
  `template_queue` text NOT NULL,
  `template_browser_default_index` text NOT NULL,
  `template_browser_socket_index` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_levels`
-- 

CREATE TABLE `arsc_levels` (
  `command` char(8) NOT NULL default '0',
  `level0` tinyint(4) NOT NULL default '0',
  `level10` tinyint(4) NOT NULL default '0',
  `level20` tinyint(4) NOT NULL default '0',
  `level30` tinyint(4) NOT NULL default '0',
  `level99` tinyint(4) NOT NULL default '0',
  KEY `command` (`command`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_moderation_queue`
-- 

CREATE TABLE `arsc_moderation_queue` (
  `id` int(11) NOT NULL auto_increment,
  `rooms_id` int(11) NOT NULL default '0',
  `user` varchar(64) NOT NULL default '',
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_parameters`
-- 

CREATE TABLE `arsc_parameters` (
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  `description` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_registered_users`
-- 

CREATE TABLE `arsc_registered_users` (
  `id` int(11) NOT NULL auto_increment,
  `user` varchar(64) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `admin_sessionid` varchar(40) NOT NULL default '',
  `language` varchar(32) NOT NULL default '',
  `level` tinyint(4) NOT NULL default '0',
  `color` varchar(6) NOT NULL default '000000',
  `template` varchar(32) NOT NULL default '',
  `layout` int(11) NOT NULL default '0',
  `email` varchar(128) NOT NULL default '',
  `sex` tinyint(4) NOT NULL default '0',
  `location` varchar(255) NOT NULL default '',
  `hobbies` text NOT NULL,
  `flag_guestbook` tinyint(4) NOT NULL default '0',
  `flag_locked` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `admin_sessionid` (`admin_sessionid`)
) TYPE=MyISAM AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_room_free_for_all`
-- 

CREATE TABLE `arsc_room_free_for_all` (
  `id` int(11) NOT NULL auto_increment,
  `message` text NOT NULL,
  `user` varchar(64) NOT NULL default '',
  `flag_ripped` tinyint(4) NOT NULL default '0',
  `flag_gotmsg` tinyint(4) NOT NULL default '0',
  `flag_moderated` tinyint(4) NOT NULL default '0',
  `sendtime` time NOT NULL default '00:00:00',
  `timeid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `timeid` (`timeid`),
  KEY `flag_ripped` (`flag_ripped`)
) TYPE=MyISAM AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_room_inroom_chat`
-- 

CREATE TABLE `arsc_room_inroom_chat` (
  `id` int(11) NOT NULL auto_increment,
  `message` text NOT NULL,
  `user` varchar(64) NOT NULL default '',
  `flag_ripped` tinyint(4) NOT NULL default '0',
  `flag_gotmsg` tinyint(4) NOT NULL default '0',
  `flag_moderated` tinyint(4) NOT NULL default '0',
  `sendtime` time NOT NULL default '00:00:00',
  `timeid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `timeid` (`timeid`),
  KEY `flag_ripped` (`flag_ripped`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_room_vip_lounge`
-- 

CREATE TABLE `arsc_room_vip_lounge` (
  `id` int(11) NOT NULL auto_increment,
  `message` text NOT NULL,
  `user` varchar(64) NOT NULL default '',
  `flag_ripped` tinyint(4) NOT NULL default '0',
  `flag_gotmsg` tinyint(4) NOT NULL default '0',
  `flag_moderated` tinyint(4) NOT NULL default '0',
  `sendtime` time NOT NULL default '00:00:00',
  `timeid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `timeid` (`timeid`),
  KEY `flag_ripped` (`flag_ripped`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_rooms`
-- 

CREATE TABLE `arsc_rooms` (
  `id` int(11) NOT NULL auto_increment,
  `roomname` varchar(32) NOT NULL default '',
  `roomname_nice` varchar(64) NOT NULL default '',
  `description` text NOT NULL,
  `owner` varchar(64) NOT NULL default '0',
  `password` varchar(6) NOT NULL default '',
  `type` smallint(6) NOT NULL default '0',
  `layout_id` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `roomname_nice` (`roomname_nice`),
  UNIQUE KEY `roomname` (`roomname`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_smilies`
-- 

CREATE TABLE `arsc_smilies` (
  `id` int(11) NOT NULL default '0',
  `value` char(32) NOT NULL default '',
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_templates`
-- 

CREATE TABLE `arsc_templates` (
  `template` varchar(32) NOT NULL default '',
  `name` varchar(32) NOT NULL default '',
  `value` text NOT NULL,
  KEY `name` (`name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_traffic`
-- 

CREATE TABLE `arsc_traffic` (
  `incoming` int(11) NOT NULL default '0',
  `outgoing` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `arsc_users`
-- 

CREATE TABLE `arsc_users` (
  `id` int(11) NOT NULL auto_increment,
  `user` varchar(64) NOT NULL default '',
  `lastping` int(11) NOT NULL default '0',
  `flag_idle` enum('0','1') NOT NULL default '0',
  `ip` varchar(15) NOT NULL default '',
  `room` varchar(32) NOT NULL default '',
  `language` varchar(32) NOT NULL default '',
  `version` varchar(32) NOT NULL default '',
  `template` varchar(32) NOT NULL default '0',
  `layout` int(11) NOT NULL default '0',
  `color` varchar(6) NOT NULL default '000000',
  `level` tinyint(11) NOT NULL default '0',
  `flag_ripped` enum('0','1') NOT NULL default '0',
  `sid` varchar(40) NOT NULL default '',
  `lastmessageping` int(11) NOT NULL default '0',
  `showsince` int(11) NOT NULL default '0',
  `flood_count` tinyint(4) NOT NULL default '0',
  `flood_lastmessage` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `sid` (`sid`),
  UNIQUE KEY `user` (`user`),
  KEY `lastping` (`lastping`),
  KEY `flag_idle` (`flag_idle`)
) TYPE=MyISAM AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_file_folders`
-- 

CREATE TABLE `members_file_folders` (
  `ident` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL default '0',
  `files_owner` int(11) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `access` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ident`),
  KEY `owner` (`owner`,`parent`,`name`,`access`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_file_metadata`
-- 

CREATE TABLE `members_file_metadata` (
  `ident` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  `file_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ident`),
  KEY `name` (`name`,`file_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_files`
-- 

CREATE TABLE `members_files` (
  `ident` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL default '0',
  `files_owner` int(11) NOT NULL default '0',
  `folder` int(11) NOT NULL default '-1',
  `community` int(11) NOT NULL default '-1',
  `title` varchar(255) NOT NULL default '',
  `originalname` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `location` varchar(255) NOT NULL default '',
  `access` varchar(255) NOT NULL default '',
  `size` int(11) NOT NULL default '0',
  `time_uploaded` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ident`),
  KEY `owner` (`owner`,`folder`,`access`),
  KEY `size` (`size`),
  KEY `time_uploaded` (`time_uploaded`),
  KEY `originalname` (`originalname`),
  KEY `community` (`community`),
  KEY `files_owner` (`files_owner`)
) TYPE=MyISAM AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_friends`
-- 

CREATE TABLE `members_friends` (
  `ident` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL default '0',
  `friend` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ident`),
  KEY `owner` (`owner`,`friend`)
) TYPE=MyISAM AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_group_membership`
-- 

CREATE TABLE `members_group_membership` (
  `ident` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ident`),
  KEY `user_id` (`user_id`,`group_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_groups`
-- 

CREATE TABLE `members_groups` (
  `ident` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `access` varchar(255) NOT NULL default 'PUBLIC',
  PRIMARY KEY  (`ident`),
  KEY `owner` (`owner`,`name`),
  KEY `access` (`access`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_home_data`
-- 

CREATE TABLE `members_home_data` (
  `ident` int(10) unsigned NOT NULL auto_increment,
  `owner` int(10) unsigned NOT NULL default '0',
  `access` varchar(16) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`ident`),
  KEY `owner` (`owner`,`access`,`name`)
) TYPE=MyISAM AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_icons`
-- 

CREATE TABLE `members_icons` (
  `ident` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL default '0',
  `filename` varchar(128) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ident`),
  KEY `owner` (`owner`,`filename`,`description`)
) TYPE=MyISAM AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_invitations`
-- 

CREATE TABLE `members_invitations` (
  `ident` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `code` varchar(128) NOT NULL default '',
  `owner` int(11) NOT NULL default '0',
  `added` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ident`),
  KEY `name` (`name`,`email`,`code`,`owner`,`added`)
) TYPE=MyISAM AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_profile_data`
-- 

CREATE TABLE `members_profile_data` (
  `ident` int(10) unsigned NOT NULL auto_increment,
  `owner` int(10) unsigned NOT NULL default '0',
  `access` varchar(16) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`ident`),
  KEY `owner` (`owner`,`access`,`name`)
) TYPE=MyISAM AUTO_INCREMENT=1172 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_tags`
-- 

CREATE TABLE `members_tags` (
  `ident` int(11) NOT NULL auto_increment,
  `tag` varchar(128) NOT NULL default '',
  `tagtype` varchar(128) NOT NULL default '',
  `ref` int(11) NOT NULL default '0',
  `access` varchar(128) NOT NULL default '',
  `owner` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ident`),
  KEY `tag` (`tag`,`tagtype`,`ref`,`access`),
  KEY `owner` (`owner`),
  FULLTEXT KEY `tag_2` (`tag`)
) TYPE=MyISAM AUTO_INCREMENT=2392 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_template_elements`
-- 

CREATE TABLE `members_template_elements` (
  `ident` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `content` text NOT NULL,
  `template_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ident`),
  KEY `name` (`name`,`template_id`)
) TYPE=MyISAM AUTO_INCREMENT=25192 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_templates`
-- 

CREATE TABLE `members_templates` (
  `ident` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `owner` int(11) NOT NULL default '0',
  `public` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`ident`),
  KEY `name` (`name`,`owner`,`public`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_users`
-- 

CREATE TABLE `members_users` (
  `ident` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(32) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `name` varchar(128) NOT NULL default '',
  `icon` int(11) NOT NULL default '-1',
  `active` enum('yes','no') NOT NULL default 'yes',
  `alias` varchar(128) NOT NULL default '',
  `code` varchar(32) NOT NULL default '',
  `icon_quota` int(11) NOT NULL default '10',
  `file_quota` int(11) NOT NULL default '10000000',
  `template_id` int(11) NOT NULL default '-1',
  `owner` int(11) NOT NULL default '-1',
  `user_type` varchar(128) NOT NULL default 'person',
  PRIMARY KEY  (`ident`),
  KEY `username` (`username`,`password`,`name`,`active`),
  KEY `code` (`code`),
  KEY `icon` (`icon`),
  KEY `icon_quota` (`icon_quota`),
  KEY `file_quota` (`file_quota`),
  KEY `email` (`email`),
  KEY `template_id` (`template_id`),
  KEY `community` (`owner`),
  KEY `user_type` (`user_type`),
  FULLTEXT KEY `name` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=185 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_weblog_comments`
-- 

CREATE TABLE `members_weblog_comments` (
  `ident` int(11) NOT NULL auto_increment,
  `post_id` int(11) NOT NULL default '0',
  `owner` int(11) NOT NULL default '0',
  `postedname` varchar(128) NOT NULL default '',
  `body` text NOT NULL,
  `posted` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ident`),
  KEY `owner` (`owner`),
  KEY `posted` (`posted`),
  KEY `post_id` (`post_id`),
  KEY `postedname` (`postedname`)
) TYPE=MyISAM AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `members_weblog_posts`
-- 

CREATE TABLE `members_weblog_posts` (
  `ident` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL default '0',
  `weblog` int(11) NOT NULL default '-1',
  `access` varchar(255) NOT NULL default '',
  `posted` int(11) NOT NULL default '0',
  `title` text NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY  (`ident`),
  KEY `owner` (`owner`,`access`,`posted`),
  KEY `community` (`weblog`)
) TYPE=MyISAM AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `other_data`
-- 

CREATE TABLE `other_data` (
  `id` bigint(20) NOT NULL default '0',
  `lang` char(2) NOT NULL default 'en',
  `name` varchar(32) NOT NULL default '',
  `data` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_access_log`
-- 

CREATE TABLE `phpgw_access_log` (
  `sessionid` varchar(32) NOT NULL default '',
  `loginid` varchar(30) NOT NULL default '',
  `ip` varchar(30) NOT NULL default '',
  `li` int(11) NOT NULL default '0',
  `lo` int(11) default '0',
  `account_id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_accounts`
-- 

CREATE TABLE `phpgw_accounts` (
  `account_id` int(11) NOT NULL auto_increment,
  `account_lid` varchar(25) NOT NULL default '',
  `account_pwd` varchar(100) NOT NULL default '',
  `account_firstname` varchar(50) default NULL,
  `account_lastname` varchar(50) default NULL,
  `account_lastlogin` int(11) default NULL,
  `account_lastloginfrom` varchar(255) default NULL,
  `account_lastpwd_change` int(11) default NULL,
  `account_status` char(1) NOT NULL default 'A',
  `account_expires` int(11) default NULL,
  `account_type` char(1) default NULL,
  `person_id` int(11) default NULL,
  `account_primary_group` int(11) NOT NULL default '0',
  `account_email` varchar(100) default NULL,
  `account_linkedin` int(11) NOT NULL default '0',
  `account_membership_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`account_id`),
  UNIQUE KEY `account_lid` (`account_lid`)
) TYPE=MyISAM AUTO_INCREMENT=393 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_acl`
-- 

CREATE TABLE `phpgw_acl` (
  `acl_appname` varchar(50) NOT NULL default '',
  `acl_location` varchar(255) NOT NULL default '',
  `acl_account` int(11) NOT NULL default '0',
  `acl_rights` int(11) default NULL,
  PRIMARY KEY  (`acl_appname`,`acl_location`,`acl_account`),
  KEY `acl_account` (`acl_account`),
  KEY `acl_location` (`acl_location`,`acl_account`),
  KEY `acl_appname` (`acl_appname`,`acl_account`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_addressbook`
-- 

CREATE TABLE `phpgw_addressbook` (
  `id` int(11) NOT NULL auto_increment,
  `lid` varchar(32) default NULL,
  `tid` char(1) default NULL,
  `owner` bigint(20) default NULL,
  `access` varchar(7) default NULL,
  `cat_id` varchar(32) default NULL,
  `fn` varchar(64) default NULL,
  `n_family` varchar(64) default NULL,
  `n_given` varchar(64) default NULL,
  `n_middle` varchar(64) default NULL,
  `n_prefix` varchar(64) default NULL,
  `n_suffix` varchar(64) default NULL,
  `sound` varchar(64) default NULL,
  `bday` varchar(32) default NULL,
  `note` text,
  `tz` varchar(8) default NULL,
  `geo` varchar(32) default NULL,
  `url` varchar(128) default NULL,
  `pubkey` text,
  `org_name` varchar(64) default NULL,
  `org_unit` varchar(64) default NULL,
  `title` varchar(64) default NULL,
  `adr_one_street` varchar(64) default NULL,
  `adr_one_locality` varchar(64) default NULL,
  `adr_one_region` varchar(64) default NULL,
  `adr_one_postalcode` varchar(64) default NULL,
  `adr_one_countryname` varchar(64) default NULL,
  `adr_one_type` varchar(32) default NULL,
  `label` text,
  `adr_two_street` varchar(64) default NULL,
  `adr_two_locality` varchar(64) default NULL,
  `adr_two_region` varchar(64) default NULL,
  `adr_two_postalcode` varchar(64) default NULL,
  `adr_two_countryname` varchar(64) default NULL,
  `adr_two_type` varchar(32) default NULL,
  `tel_work` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_home` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_voice` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_fax` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_msg` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_cell` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_pager` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_bbs` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_modem` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_car` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_isdn` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_video` varchar(40) NOT NULL default '+1 (000) 000-0000',
  `tel_prefer` varchar(32) default NULL,
  `email` varchar(64) default NULL,
  `email_type` varchar(32) default 'INTERNET',
  `email_home` varchar(64) default NULL,
  `email_home_type` varchar(32) default 'INTERNET',
  `last_mod` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `tid` (`tid`,`owner`,`access`,`n_family`,`n_given`,`email`),
  KEY `tid_2` (`tid`,`cat_id`,`owner`,`access`,`n_family`,`n_given`,`email`)
) TYPE=MyISAM AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_addressbook_extra`
-- 

CREATE TABLE `phpgw_addressbook_extra` (
  `contact_id` int(11) NOT NULL default '0',
  `contact_owner` bigint(20) default NULL,
  `contact_name` varchar(255) NOT NULL default '',
  `contact_value` text,
  PRIMARY KEY  (`contact_id`,`contact_name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_anglemail`
-- 

CREATE TABLE `phpgw_anglemail` (
  `account_id` varchar(20) NOT NULL default '',
  `data_key` varchar(255) NOT NULL default '',
  `content` blob NOT NULL,
  PRIMARY KEY  (`account_id`,`data_key`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_app_sessions`
-- 

CREATE TABLE `phpgw_app_sessions` (
  `sessionid` varchar(128) NOT NULL default '',
  `loginid` int(11) NOT NULL default '0',
  `app` varchar(25) NOT NULL default '',
  `location` varchar(128) NOT NULL default '',
  `content` longtext,
  `session_dla` int(11) default NULL,
  PRIMARY KEY  (`sessionid`,`loginid`,`app`,`location`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_applications`
-- 

CREATE TABLE `phpgw_applications` (
  `app_id` int(11) NOT NULL auto_increment,
  `app_name` varchar(25) NOT NULL default '',
  `app_title` varchar(64) default NULL,
  `app_enabled` int(11) NOT NULL default '0',
  `app_order` int(11) NOT NULL default '0',
  `app_tables` text NOT NULL,
  `app_version` varchar(20) NOT NULL default '0',
  PRIMARY KEY  (`app_id`),
  UNIQUE KEY `app_name` (`app_name`),
  KEY `app_enabled` (`app_enabled`,`app_order`)
) TYPE=MyISAM AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_async`
-- 

CREATE TABLE `phpgw_async` (
  `id` varchar(255) NOT NULL default '',
  `next` int(11) NOT NULL default '0',
  `times` varchar(255) NOT NULL default '',
  `method` varchar(80) NOT NULL default '',
  `data` text NOT NULL,
  `account_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_bookmarks`
-- 

CREATE TABLE `phpgw_bookmarks` (
  `bm_id` int(11) NOT NULL auto_increment,
  `bm_owner` int(11) default NULL,
  `bm_access` varchar(255) default NULL,
  `bm_url` varchar(255) default NULL,
  `bm_name` varchar(255) default NULL,
  `bm_desc` text,
  `bm_keywords` varchar(255) default NULL,
  `bm_category` int(11) default NULL,
  `bm_rating` int(11) default NULL,
  `bm_info` varchar(255) default NULL,
  `bm_visits` int(11) default NULL,
  PRIMARY KEY  (`bm_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cal`
-- 

CREATE TABLE `phpgw_cal` (
  `cal_id` int(11) NOT NULL auto_increment,
  `uid` varchar(255) NOT NULL default '',
  `owner` bigint(20) NOT NULL default '0',
  `category` varchar(30) default NULL,
  `groups` varchar(255) default NULL,
  `datetime` bigint(20) default NULL,
  `mdatetime` bigint(20) default NULL,
  `edatetime` bigint(20) default NULL,
  `priority` bigint(20) NOT NULL default '2',
  `cal_type` varchar(10) default NULL,
  `is_public` bigint(20) NOT NULL default '1',
  `title` varchar(80) NOT NULL default '1',
  `description` text,
  `location` varchar(255) default NULL,
  `reference` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`cal_id`)
) TYPE=MyISAM AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cal_extra`
-- 

CREATE TABLE `phpgw_cal_extra` (
  `cal_id` int(11) NOT NULL default '0',
  `cal_extra_name` varchar(40) NOT NULL default '',
  `cal_extra_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cal_id`,`cal_extra_name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cal_holidays`
-- 

CREATE TABLE `phpgw_cal_holidays` (
  `hol_id` int(11) NOT NULL auto_increment,
  `locale` char(2) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `mday` bigint(20) NOT NULL default '0',
  `month_num` bigint(20) NOT NULL default '0',
  `occurence` bigint(20) NOT NULL default '0',
  `dow` bigint(20) NOT NULL default '0',
  `observance_rule` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`hol_id`),
  KEY `locale` (`locale`)
) TYPE=MyISAM AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cal_repeats`
-- 

CREATE TABLE `phpgw_cal_repeats` (
  `cal_id` bigint(20) NOT NULL default '0',
  `recur_type` bigint(20) NOT NULL default '0',
  `recur_use_end` bigint(20) default '0',
  `recur_enddate` bigint(20) default NULL,
  `recur_interval` bigint(20) default '1',
  `recur_data` bigint(20) default '1',
  `recur_exception` varchar(255) default '',
  KEY `cal_id` (`cal_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cal_user`
-- 

CREATE TABLE `phpgw_cal_user` (
  `cal_id` bigint(20) NOT NULL default '0',
  `cal_login` bigint(20) NOT NULL default '0',
  `cal_status` char(1) default 'A',
  `cal_type` char(1) NOT NULL default 'u',
  PRIMARY KEY  (`cal_id`,`cal_login`,`cal_type`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_categories`
-- 

CREATE TABLE `phpgw_categories` (
  `cat_id` int(11) NOT NULL auto_increment,
  `cat_main` int(11) NOT NULL default '0',
  `cat_parent` int(11) NOT NULL default '0',
  `cat_level` smallint(6) NOT NULL default '0',
  `cat_owner` int(11) NOT NULL default '0',
  `cat_access` varchar(7) default NULL,
  `cat_appname` varchar(50) NOT NULL default '',
  `cat_name` varchar(150) NOT NULL default '',
  `cat_description` varchar(255) NOT NULL default '',
  `cat_data` text,
  `last_mod` bigint(20) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cat_id`),
  KEY `cat_appname` (`cat_appname`,`cat_owner`,`cat_parent`,`cat_level`)
) TYPE=MyISAM AUTO_INCREMENT=79 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_comic`
-- 

CREATE TABLE `phpgw_comic` (
  `comic_id` int(11) NOT NULL auto_increment,
  `comic_owner` varchar(32) NOT NULL default '',
  `comic_list` blob NOT NULL,
  `comic_scale` smallint(6) NOT NULL default '0',
  `comic_perpage` smallint(6) NOT NULL default '4',
  `comic_frontpage` int(11) NOT NULL default '0',
  `comic_fpscale` smallint(6) NOT NULL default '0',
  `comic_censorlvl` smallint(6) NOT NULL default '0',
  `comic_template` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`comic_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_comic_admin`
-- 

CREATE TABLE `phpgw_comic_admin` (
  `admin_imgsrc` smallint(6) NOT NULL default '0',
  `admin_rmtenabled` smallint(6) NOT NULL default '0',
  `admin_censorlvl` smallint(6) NOT NULL default '0',
  `admin_coverride` smallint(6) NOT NULL default '0',
  `admin_filesize` int(11) NOT NULL default '120000',
  PRIMARY KEY  (`admin_imgsrc`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_comic_data`
-- 

CREATE TABLE `phpgw_comic_data` (
  `data_id` int(11) NOT NULL auto_increment,
  `data_enabled` char(1) NOT NULL default 'T',
  `data_name` varchar(25) NOT NULL default '',
  `data_author` varchar(128) NOT NULL default '',
  `data_title` varchar(255) NOT NULL default '',
  `data_prefix` varchar(25) NOT NULL default '',
  `data_date` int(11) NOT NULL default '0',
  `data_comicid` int(11) NOT NULL default '0',
  `data_linkurl` varchar(255) NOT NULL default '',
  `data_baseurl` varchar(255) NOT NULL default '',
  `data_parseurl` varchar(255) NOT NULL default '',
  `data_parsexpr` varchar(255) NOT NULL default '',
  `data_imageurl` varchar(255) NOT NULL default '',
  `data_pubdays` varchar(25) NOT NULL default 'Su:Mo:Tu:We:Th:Fr:Sa',
  `data_parser` varchar(32) NOT NULL default 'None',
  `data_class` varchar(32) NOT NULL default 'General',
  `data_censorlvl` smallint(6) NOT NULL default '0',
  `data_resolve` varchar(32) NOT NULL default 'Remote',
  `data_daysold` int(11) NOT NULL default '0',
  `data_width` int(11) NOT NULL default '0',
  `data_swidth` int(11) NOT NULL default '0',
  PRIMARY KEY  (`data_id`)
) TYPE=MyISAM AUTO_INCREMENT=122 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_config`
-- 

CREATE TABLE `phpgw_config` (
  `config_app` varchar(50) NOT NULL default '',
  `config_name` varchar(255) NOT NULL default '',
  `config_value` text,
  PRIMARY KEY  (`config_app`,`config_name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_albums`
-- 

CREATE TABLE `phpgw_cpg_albums` (
  `aid` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `visibility` int(11) NOT NULL default '0',
  `uploads` enum('YES','NO') NOT NULL default 'NO',
  `comments` enum('YES','NO') NOT NULL default 'YES',
  `votes` enum('YES','NO') NOT NULL default 'YES',
  `pos` int(11) NOT NULL default '0',
  `category` int(11) NOT NULL default '0',
  `pic_count` int(11) NOT NULL default '0',
  `thumb` int(11) NOT NULL default '0',
  `last_addition` datetime NOT NULL default '0000-00-00 00:00:00',
  `stat_uptodate` enum('YES','NO') NOT NULL default 'NO',
  `keyword` varchar(50) default NULL,
  PRIMARY KEY  (`aid`),
  KEY `alb_category` (`category`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_banned`
-- 

CREATE TABLE `phpgw_cpg_banned` (
  `ban_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `ip_addr` tinytext,
  `expiry` datetime default NULL,
  PRIMARY KEY  (`ban_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_categories`
-- 

CREATE TABLE `phpgw_cpg_categories` (
  `cid` int(11) NOT NULL auto_increment,
  `owner_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `pos` int(11) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `thumb` int(11) NOT NULL default '0',
  `subcat_count` int(11) NOT NULL default '0',
  `alb_count` int(11) NOT NULL default '0',
  `pic_count` int(11) NOT NULL default '0',
  `stat_uptodate` enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (`cid`),
  KEY `cat_parent` (`parent`),
  KEY `cat_pos` (`pos`),
  KEY `cat_owner_id` (`owner_id`)
) TYPE=MyISAM AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_comments`
-- 

CREATE TABLE `phpgw_cpg_comments` (
  `pid` mediumint(10) NOT NULL default '0',
  `msg_id` mediumint(10) NOT NULL auto_increment,
  `msg_author` varchar(25) NOT NULL default '',
  `msg_body` text NOT NULL,
  `msg_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `msg_raw_ip` tinytext,
  `msg_hdr_ip` tinytext,
  `author_md5_id` varchar(32) NOT NULL default '',
  `author_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`msg_id`),
  KEY `com_pic_id` (`pid`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_config`
-- 

CREATE TABLE `phpgw_cpg_config` (
  `name` varchar(40) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_ecards`
-- 

CREATE TABLE `phpgw_cpg_ecards` (
  `eid` int(11) NOT NULL auto_increment,
  `sender_name` varchar(50) NOT NULL default '',
  `sender_email` text NOT NULL,
  `recipient_name` varchar(50) NOT NULL default '',
  `recipient_email` text NOT NULL,
  `link` text NOT NULL,
  `date` tinytext NOT NULL,
  `sender_ip` tinytext NOT NULL,
  PRIMARY KEY  (`eid`)
) TYPE=MyISAM COMMENT='Used to log ecards' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_exif`
-- 

CREATE TABLE `phpgw_cpg_exif` (
  `filename` varchar(255) NOT NULL default '',
  `exifData` text NOT NULL,
  UNIQUE KEY `filename` (`filename`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_filetypes`
-- 

CREATE TABLE `phpgw_cpg_filetypes` (
  `extension` char(7) NOT NULL default '',
  `mime` char(30) default NULL,
  `content` char(15) default NULL,
  PRIMARY KEY  (`extension`)
) TYPE=MyISAM COMMENT='Used to store the file extensions';

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_pictures`
-- 

CREATE TABLE `phpgw_cpg_pictures` (
  `pid` int(11) NOT NULL auto_increment,
  `aid` int(11) NOT NULL default '0',
  `filepath` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `filesize` int(11) NOT NULL default '0',
  `total_filesize` int(11) NOT NULL default '0',
  `pwidth` smallint(6) NOT NULL default '0',
  `pheight` smallint(6) NOT NULL default '0',
  `hits` int(10) NOT NULL default '0',
  `mtime` timestamp(14) NOT NULL,
  `ctime` int(11) NOT NULL default '0',
  `owner_id` int(11) NOT NULL default '0',
  `owner_name` varchar(40) NOT NULL default '',
  `pic_rating` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `caption` text NOT NULL,
  `keywords` varchar(255) NOT NULL default '',
  `approved` enum('YES','NO') NOT NULL default 'NO',
  `user1` varchar(255) NOT NULL default '',
  `user2` varchar(255) NOT NULL default '',
  `user3` varchar(255) NOT NULL default '',
  `user4` varchar(255) NOT NULL default '',
  `url_prefix` tinyint(4) NOT NULL default '0',
  `randpos` int(11) NOT NULL default '0',
  `pic_raw_ip` tinytext,
  `pic_hdr_ip` tinytext,
  PRIMARY KEY  (`pid`),
  KEY `owner_id` (`owner_id`),
  KEY `pic_hits` (`hits`),
  KEY `pic_rate` (`pic_rating`),
  KEY `aid_approved` (`aid`,`approved`),
  KEY `randpos` (`randpos`),
  KEY `pic_aid` (`aid`),
  FULLTEXT KEY `search` (`title`,`caption`,`keywords`,`filename`)
) TYPE=MyISAM AUTO_INCREMENT=80 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_temp_data`
-- 

CREATE TABLE `phpgw_cpg_temp_data` (
  `unique_ID` varchar(8) NOT NULL default '',
  `encoded_string` blob NOT NULL,
  `timestamp` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`unique_ID`)
) TYPE=MyISAM COMMENT='Holds temporary file data for multiple file uploads';

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_usergroups`
-- 

CREATE TABLE `phpgw_cpg_usergroups` (
  `group_id` int(11) NOT NULL auto_increment,
  `group_name` varchar(255) NOT NULL default '',
  `group_quota` int(11) NOT NULL default '0',
  `has_admin_access` tinyint(4) NOT NULL default '0',
  `can_rate_pictures` tinyint(4) NOT NULL default '0',
  `can_send_ecards` tinyint(4) NOT NULL default '0',
  `can_post_comments` tinyint(4) NOT NULL default '0',
  `can_upload_pictures` tinyint(4) NOT NULL default '0',
  `can_create_albums` tinyint(4) NOT NULL default '0',
  `pub_upl_need_approval` tinyint(4) NOT NULL default '1',
  `priv_upl_need_approval` tinyint(4) NOT NULL default '1',
  `upload_form_config` tinyint(4) NOT NULL default '3',
  `custom_user_upload` tinyint(4) NOT NULL default '0',
  `num_file_upload` tinyint(4) NOT NULL default '5',
  `num_URI_upload` tinyint(4) NOT NULL default '3',
  PRIMARY KEY  (`group_id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_users`
-- 

CREATE TABLE `phpgw_cpg_users` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_group` int(11) NOT NULL default '2',
  `user_active` enum('YES','NO') NOT NULL default 'NO',
  `user_name` varchar(25) NOT NULL default '',
  `user_password` varchar(25) NOT NULL default '',
  `user_lastvisit` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_group_list` varchar(255) NOT NULL default '',
  `user_email` varchar(255) NOT NULL default '',
  `user_website` varchar(255) NOT NULL default '',
  `user_location` varchar(255) NOT NULL default '',
  `user_interests` varchar(255) NOT NULL default '',
  `user_occupation` varchar(255) NOT NULL default '',
  `user_actkey` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) TYPE=MyISAM AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_cpg_votes`
-- 

CREATE TABLE `phpgw_cpg_votes` (
  `pic_id` mediumint(9) NOT NULL default '0',
  `user_md5_id` varchar(32) NOT NULL default '',
  `vote_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pic_id`,`user_md5_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_emailadmin`
-- 

CREATE TABLE `phpgw_emailadmin` (
  `profileID` int(11) NOT NULL auto_increment,
  `smtpServer` varchar(80) default NULL,
  `smtpType` int(11) default NULL,
  `smtpPort` int(11) default NULL,
  `smtpAuth` char(3) default NULL,
  `smtpLDAPServer` varchar(80) default NULL,
  `smtpLDAPBaseDN` varchar(200) default NULL,
  `smtpLDAPAdminDN` varchar(200) default NULL,
  `smtpLDAPAdminPW` varchar(30) default NULL,
  `smtpLDAPUseDefault` char(3) default NULL,
  `imapServer` varchar(80) default NULL,
  `imapType` int(11) default NULL,
  `imapPort` int(11) default NULL,
  `imapLoginType` varchar(20) default NULL,
  `imapTLSAuthentication` char(3) default NULL,
  `imapTLSEncryption` char(3) default NULL,
  `imapEnableCyrusAdmin` char(3) default NULL,
  `imapAdminUsername` varchar(40) default NULL,
  `imapAdminPW` varchar(40) default NULL,
  `imapEnableSieve` char(3) default NULL,
  `imapSieveServer` varchar(80) default NULL,
  `imapSievePort` int(11) default NULL,
  `description` varchar(200) default NULL,
  `defaultDomain` varchar(100) default NULL,
  `organisationName` varchar(100) default NULL,
  `userDefinedAccounts` char(3) default NULL,
  `imapoldcclient` char(3) default NULL,
  PRIMARY KEY  (`profileID`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_etemplate`
-- 

CREATE TABLE `phpgw_etemplate` (
  `et_name` varchar(80) NOT NULL default '',
  `et_template` varchar(20) NOT NULL default '',
  `et_lang` varchar(5) NOT NULL default '',
  `et_group` int(11) NOT NULL default '0',
  `et_version` varchar(20) NOT NULL default '',
  `et_data` text,
  `et_size` varchar(128) default NULL,
  `et_style` text,
  `et_modified` int(11) NOT NULL default '0',
  PRIMARY KEY  (`et_name`,`et_template`,`et_lang`,`et_group`,`et_version`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_felamimail_cache`
-- 

CREATE TABLE `phpgw_felamimail_cache` (
  `accountid` int(11) NOT NULL default '0',
  `hostname` varchar(60) NOT NULL default '',
  `accountname` varchar(25) NOT NULL default '',
  `foldername` varchar(200) NOT NULL default '',
  `uid` int(11) NOT NULL default '0',
  `subject` text,
  `striped_subject` text,
  `sender_name` varchar(120) default NULL,
  `sender_address` varchar(120) default NULL,
  `to_name` varchar(120) default NULL,
  `to_address` varchar(120) default NULL,
  `date` bigint(20) default NULL,
  `size` int(11) default NULL,
  `attachments` varchar(120) default NULL,
  PRIMARY KEY  (`accountid`,`hostname`,`accountname`,`foldername`,`uid`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_felamimail_displayfilter`
-- 

CREATE TABLE `phpgw_felamimail_displayfilter` (
  `accountid` int(11) NOT NULL default '0',
  `filter` text,
  PRIMARY KEY  (`accountid`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_felamimail_folderstatus`
-- 

CREATE TABLE `phpgw_felamimail_folderstatus` (
  `accountid` int(11) NOT NULL default '0',
  `hostname` varchar(60) NOT NULL default '',
  `accountname` varchar(200) NOT NULL default '',
  `foldername` varchar(200) NOT NULL default '',
  `messages` int(11) default NULL,
  `recent` int(11) default NULL,
  `unseen` int(11) default NULL,
  `uidnext` int(11) default NULL,
  `uidvalidity` int(11) default NULL,
  PRIMARY KEY  (`accountid`,`hostname`,`accountname`,`foldername`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_forum_body`
-- 

CREATE TABLE `phpgw_forum_body` (
  `id` int(11) NOT NULL auto_increment,
  `cat_id` int(11) NOT NULL default '0',
  `for_id` int(11) NOT NULL default '0',
  `message` blob NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_forum_categories`
-- 

CREATE TABLE `phpgw_forum_categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `descr` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_forum_forums`
-- 

CREATE TABLE `phpgw_forum_forums` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `perm` smallint(6) NOT NULL default '0',
  `groups` varchar(255) NOT NULL default '',
  `descr` varchar(255) NOT NULL default '',
  `cat_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_forum_threads`
-- 

CREATE TABLE `phpgw_forum_threads` (
  `id` int(11) NOT NULL auto_increment,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `main` int(11) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `cat_id` int(11) NOT NULL default '0',
  `for_id` int(11) NOT NULL default '0',
  `thread_owner` int(11) NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `stat` smallint(6) NOT NULL default '0',
  `thread` int(11) NOT NULL default '0',
  `depth` int(11) NOT NULL default '0',
  `pos` int(11) NOT NULL default '0',
  `n_replies` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_action_log`
-- 

CREATE TABLE `phpgw_fud_action_log` (
  `id` int(11) NOT NULL auto_increment,
  `logtime` bigint(20) NOT NULL default '0',
  `logaction` varchar(100) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `a_res` varchar(100) default NULL,
  `a_res_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`logtime`)
) TYPE=MyISAM AUTO_INCREMENT=193 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_ann_forums`
-- 

CREATE TABLE `phpgw_fud_ann_forums` (
  `ann_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  KEY `ann_id` (`ann_id`,`forum_id`),
  KEY `ann_id_2` (`ann_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_announce`
-- 

CREATE TABLE `phpgw_fud_announce` (
  `id` int(11) NOT NULL auto_increment,
  `date_started` bigint(20) NOT NULL default '0',
  `date_ended` bigint(20) NOT NULL default '0',
  `subject` varchar(255) default NULL,
  `text` text,
  PRIMARY KEY  (`id`),
  KEY `date_started` (`date_started`,`date_ended`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_attach`
-- 

CREATE TABLE `phpgw_fud_attach` (
  `id` int(11) NOT NULL auto_increment,
  `location` varchar(255) default NULL,
  `original_name` varchar(255) default NULL,
  `owner` int(11) NOT NULL default '0',
  `attach_opt` int(11) NOT NULL default '0',
  `message_id` int(11) NOT NULL default '0',
  `dlcount` int(11) NOT NULL default '0',
  `mime_type` int(11) NOT NULL default '0',
  `fsize` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `message_id` (`message_id`,`attach_opt`)
) TYPE=MyISAM AUTO_INCREMENT=118 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_avatar`
-- 

CREATE TABLE `phpgw_fud_avatar` (
  `id` int(11) NOT NULL auto_increment,
  `img` varchar(255) default NULL,
  `descr` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `img` (`img`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_blocked_logins`
-- 

CREATE TABLE `phpgw_fud_blocked_logins` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_buddy`
-- 

CREATE TABLE `phpgw_fud_buddy` (
  `id` int(11) NOT NULL auto_increment,
  `bud_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`bud_id`)
) TYPE=MyISAM AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_cat`
-- 

CREATE TABLE `phpgw_fud_cat` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `description` varchar(255) default NULL,
  `cat_opt` int(11) NOT NULL default '0',
  `view_order` int(11) NOT NULL default '3',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_custom_tags`
-- 

CREATE TABLE `phpgw_fud_custom_tags` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_email_block`
-- 

CREATE TABLE `phpgw_fud_email_block` (
  `id` int(11) NOT NULL auto_increment,
  `email_block_opt` int(11) NOT NULL default '1',
  `string` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `string` (`string`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_ext_block`
-- 

CREATE TABLE `phpgw_fud_ext_block` (
  `id` int(11) NOT NULL auto_increment,
  `ext` varchar(32) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_fc_view`
-- 

CREATE TABLE `phpgw_fud_fc_view` (
  `id` int(11) NOT NULL auto_increment,
  `c` int(11) NOT NULL default '0',
  `f` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `f` (`f`)
) TYPE=MyISAM AUTO_INCREMENT=988 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_forum`
-- 

CREATE TABLE `phpgw_fud_forum` (
  `id` int(11) NOT NULL auto_increment,
  `cat_id` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `descr` text,
  `post_passwd` varchar(32) default NULL,
  `forum_icon` varchar(255) default NULL,
  `date_created` bigint(20) NOT NULL default '0',
  `thread_count` int(11) NOT NULL default '0',
  `post_count` int(11) NOT NULL default '0',
  `last_post_id` int(11) NOT NULL default '0',
  `view_order` int(11) NOT NULL default '0',
  `max_attach_size` int(11) NOT NULL default '0',
  `max_file_attachments` int(11) NOT NULL default '1',
  `moderators` text,
  `message_threshold` int(11) NOT NULL default '0',
  `forum_opt` int(11) NOT NULL default '16',
  PRIMARY KEY  (`id`),
  KEY `cat_id` (`cat_id`),
  KEY `last_post_id` (`last_post_id`)
) TYPE=MyISAM AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_forum_notify`
-- 

CREATE TABLE `phpgw_fud_forum_notify` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`forum_id`),
  KEY `forum_id` (`forum_id`)
) TYPE=MyISAM AUTO_INCREMENT=4664 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_forum_read`
-- 

CREATE TABLE `phpgw_fud_forum_read` (
  `id` int(11) NOT NULL auto_increment,
  `forum_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `last_view` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `forum_id` (`forum_id`,`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=519 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_group_cache`
-- 

CREATE TABLE `phpgw_fud_group_cache` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `resource_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `group_cache_opt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `resource_id` (`resource_id`,`user_id`),
  KEY `group_id` (`group_id`)
) TYPE=MyISAM AUTO_INCREMENT=2354 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_group_members`
-- 

CREATE TABLE `phpgw_fud_group_members` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `group_members_opt` int(11) NOT NULL default '65536',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `group_id` (`group_id`,`user_id`),
  KEY `group_members_opt` (`group_members_opt`)
) TYPE=MyISAM AUTO_INCREMENT=227 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_group_resources`
-- 

CREATE TABLE `phpgw_fud_group_resources` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL default '0',
  `resource_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `group_id` (`group_id`,`resource_id`),
  KEY `resource_id` (`resource_id`)
) TYPE=MyISAM AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_groups`
-- 

CREATE TABLE `phpgw_fud_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `inherit_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  `groups_opt` int(11) NOT NULL default '0',
  `groups_opti` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `inherit_id` (`inherit_id`),
  KEY `forum_id` (`forum_id`)
) TYPE=MyISAM AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_index`
-- 

CREATE TABLE `phpgw_fud_index` (
  `id` int(11) NOT NULL auto_increment,
  `word_id` int(11) NOT NULL default '0',
  `msg_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `word_id` (`word_id`,`msg_id`),
  KEY `msg_id` (`msg_id`)
) TYPE=MyISAM AUTO_INCREMENT=80352 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_ip_block`
-- 

CREATE TABLE `phpgw_fud_ip_block` (
  `id` int(11) NOT NULL auto_increment,
  `ca` int(11) NOT NULL default '0',
  `cb` int(11) NOT NULL default '0',
  `cc` int(11) NOT NULL default '0',
  `cd` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_level`
-- 

CREATE TABLE `phpgw_fud_level` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `post_count` int(11) NOT NULL default '0',
  `img` varchar(255) default NULL,
  `level_opt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `post_count` (`post_count`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_mime`
-- 

CREATE TABLE `phpgw_fud_mime` (
  `id` int(11) NOT NULL auto_increment,
  `fl_ext` varchar(10) default NULL,
  `mime_hdr` varchar(255) default NULL,
  `descr` varchar(255) default NULL,
  `icon` varchar(100) NOT NULL default 'unknown.gif',
  PRIMARY KEY  (`id`),
  KEY `fl_ext` (`fl_ext`)
) TYPE=MyISAM AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_mlist`
-- 

CREATE TABLE `phpgw_fud_mlist` (
  `id` int(11) NOT NULL auto_increment,
  `forum_id` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `subject_regex_haystack` text,
  `subject_regex_needle` text,
  `body_regex_haystack` text,
  `body_regex_needle` text,
  `additional_headers` text,
  `mlist_opt` int(11) NOT NULL default '76',
  PRIMARY KEY  (`id`),
  KEY `forum_id` (`forum_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_mod`
-- 

CREATE TABLE `phpgw_fud_mod` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`,`forum_id`)
) TYPE=MyISAM AUTO_INCREMENT=240 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_mod_que`
-- 

CREATE TABLE `phpgw_fud_mod_que` (
  `id` int(11) NOT NULL auto_increment,
  `msg_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_msg`
-- 

CREATE TABLE `phpgw_fud_msg` (
  `id` int(11) NOT NULL auto_increment,
  `thread_id` int(11) NOT NULL default '0',
  `poster_id` int(11) NOT NULL default '0',
  `reply_to` int(11) NOT NULL default '0',
  `ip_addr` varchar(15) NOT NULL default '0.0.0.0',
  `host_name` varchar(255) default NULL,
  `post_stamp` bigint(20) NOT NULL default '0',
  `update_stamp` bigint(20) NOT NULL default '0',
  `updated_by` int(11) NOT NULL default '0',
  `icon` varchar(100) default NULL,
  `subject` varchar(100) default NULL,
  `attach_cnt` int(11) NOT NULL default '0',
  `poll_id` int(11) NOT NULL default '0',
  `foff` bigint(20) NOT NULL default '0',
  `length` int(11) NOT NULL default '0',
  `file_id` int(11) NOT NULL default '1',
  `offset_preview` bigint(20) NOT NULL default '0',
  `length_preview` int(11) NOT NULL default '0',
  `file_id_preview` int(11) NOT NULL default '0',
  `attach_cache` text,
  `poll_cache` text,
  `mlist_msg_id` varchar(100) default NULL,
  `msg_opt` int(11) NOT NULL default '1',
  `apr` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `thread_id` (`thread_id`,`apr`),
  KEY `poster_id` (`poster_id`,`apr`),
  KEY `apr` (`apr`),
  KEY `post_stamp` (`post_stamp`),
  KEY `attach_cnt` (`attach_cnt`),
  KEY `poll_id` (`poll_id`),
  KEY `ip_addr` (`ip_addr`,`post_stamp`),
  KEY `subject` (`subject`),
  KEY `mlist_msg_id` (`mlist_msg_id`)
) TYPE=MyISAM AUTO_INCREMENT=1745 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_msg_report`
-- 

CREATE TABLE `phpgw_fud_msg_report` (
  `id` int(11) NOT NULL auto_increment,
  `msg_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `reason` varchar(255) default NULL,
  `stamp` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `msg_id` (`msg_id`,`user_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_nntp`
-- 

CREATE TABLE `phpgw_fud_nntp` (
  `id` int(11) NOT NULL auto_increment,
  `forum_id` int(11) NOT NULL default '0',
  `nntp_opt` int(11) NOT NULL default '44',
  `server` varchar(255) default NULL,
  `newsgroup` varchar(255) default NULL,
  `port` int(11) NOT NULL default '0',
  `timeout` int(11) NOT NULL default '0',
  `login` varchar(255) default NULL,
  `pass` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `forum_id` (`forum_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_pmsg`
-- 

CREATE TABLE `phpgw_fud_pmsg` (
  `id` int(11) NOT NULL auto_increment,
  `to_list` text,
  `ouser_id` int(11) NOT NULL default '0',
  `duser_id` int(11) NOT NULL default '0',
  `pdest` int(11) NOT NULL default '0',
  `ip_addr` varchar(15) NOT NULL default '0.0.0.0',
  `host_name` varchar(255) default NULL,
  `post_stamp` bigint(20) NOT NULL default '0',
  `read_stamp` bigint(20) NOT NULL default '0',
  `icon` varchar(100) default NULL,
  `subject` varchar(100) default NULL,
  `attach_cnt` int(11) NOT NULL default '0',
  `foff` bigint(20) NOT NULL default '0',
  `length` int(11) NOT NULL default '0',
  `ref_msg_id` varchar(11) default NULL,
  `fldr` int(11) NOT NULL default '0',
  `pmsg_opt` int(11) NOT NULL default '49',
  PRIMARY KEY  (`id`),
  KEY `duser_id` (`duser_id`,`fldr`,`read_stamp`),
  KEY `duser_id_2` (`duser_id`,`fldr`,`id`)
) TYPE=MyISAM AUTO_INCREMENT=200 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_poll`
-- 

CREATE TABLE `phpgw_fud_poll` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `owner` int(11) NOT NULL default '0',
  `creation_date` bigint(20) NOT NULL default '0',
  `expiry_date` bigint(20) NOT NULL default '0',
  `max_votes` int(11) default NULL,
  `total_votes` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `owner` (`owner`)
) TYPE=MyISAM AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_poll_opt`
-- 

CREATE TABLE `phpgw_fud_poll_opt` (
  `id` int(11) NOT NULL auto_increment,
  `poll_id` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `poll_id` (`poll_id`)
) TYPE=MyISAM AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_poll_opt_track`
-- 

CREATE TABLE `phpgw_fud_poll_opt_track` (
  `id` int(11) NOT NULL auto_increment,
  `poll_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `poll_opt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `poll_id` (`poll_id`,`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=90 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_read`
-- 

CREATE TABLE `phpgw_fud_read` (
  `id` int(11) NOT NULL auto_increment,
  `thread_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `msg_id` int(11) NOT NULL default '0',
  `last_view` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `thread_id` (`thread_id`,`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=2275 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_replace`
-- 

CREATE TABLE `phpgw_fud_replace` (
  `id` int(11) NOT NULL auto_increment,
  `replace_str` varchar(255) default NULL,
  `with_str` varchar(255) default NULL,
  `from_post` varchar(255) default NULL,
  `to_msg` varchar(255) default NULL,
  `replace_opt` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_search`
-- 

CREATE TABLE `phpgw_fud_search` (
  `id` int(11) NOT NULL auto_increment,
  `word` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `word` (`word`)
) TYPE=MyISAM AUTO_INCREMENT=11042 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_search_cache`
-- 

CREATE TABLE `phpgw_fud_search_cache` (
  `srch_query` varchar(32) default NULL,
  `query_type` int(11) NOT NULL default '0',
  `expiry` int(11) NOT NULL default '0',
  `msg_id` int(11) NOT NULL default '0',
  `n_match` int(11) NOT NULL default '0',
  KEY `srch_query` (`srch_query`,`query_type`),
  KEY `expiry` (`expiry`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_ses`
-- 

CREATE TABLE `phpgw_fud_ses` (
  `id` int(11) NOT NULL auto_increment,
  `ses_id` varchar(32) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `action` varchar(255) default NULL,
  `forum_id` int(11) NOT NULL default '0',
  `time_sec` bigint(20) NOT NULL default '0',
  `data` text,
  `returnto` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ses_id` (`ses_id`),
  KEY `time_sec` (`time_sec`,`user_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=2289 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_smiley`
-- 

CREATE TABLE `phpgw_fud_smiley` (
  `id` int(11) NOT NULL auto_increment,
  `img` varchar(255) default NULL,
  `descr` varchar(255) default NULL,
  `code` varchar(25) default NULL,
  `vieworder` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_stats_cache`
-- 

CREATE TABLE `phpgw_fud_stats_cache` (
  `user_count` int(11) NOT NULL default '0',
  `last_user_id` int(11) NOT NULL default '0',
  `online_users_reg` int(11) NOT NULL default '0',
  `online_users_anon` int(11) NOT NULL default '0',
  `online_users_hidden` int(11) NOT NULL default '0',
  `online_users_text` text,
  `cache_age` bigint(20) NOT NULL default '0',
  KEY `cache_age` (`cache_age`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_themes`
-- 

CREATE TABLE `phpgw_fud_themes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `theme` varchar(255) default NULL,
  `lang` varchar(255) default NULL,
  `locale` varchar(32) default NULL,
  `pspell_lang` varchar(32) default NULL,
  `theme_opt` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `theme_opt` (`theme_opt`),
  KEY `lang` (`lang`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_thr_exchange`
-- 

CREATE TABLE `phpgw_fud_thr_exchange` (
  `id` int(11) NOT NULL auto_increment,
  `th` int(11) NOT NULL default '0',
  `frm` int(11) NOT NULL default '0',
  `req_by` int(11) NOT NULL default '0',
  `reason_msg` text,
  PRIMARY KEY  (`id`),
  KEY `frm` (`frm`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_thread`
-- 

CREATE TABLE `phpgw_fud_thread` (
  `id` int(11) NOT NULL auto_increment,
  `forum_id` int(11) NOT NULL default '0',
  `root_msg_id` int(11) NOT NULL default '0',
  `last_post_date` bigint(20) NOT NULL default '0',
  `replies` int(11) NOT NULL default '0',
  `views` int(11) NOT NULL default '0',
  `rating` int(11) NOT NULL default '0',
  `n_rating` int(11) NOT NULL default '0',
  `last_post_id` int(11) NOT NULL default '0',
  `moved_to` int(11) NOT NULL default '0',
  `orderexpiry` bigint(20) NOT NULL default '0',
  `thread_opt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `forum_id` (`forum_id`,`last_post_date`,`moved_to`),
  KEY `root_msg_id` (`root_msg_id`),
  KEY `replies` (`replies`),
  KEY `thread_opt` (`thread_opt`)
) TYPE=MyISAM AUTO_INCREMENT=258 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_thread_notify`
-- 

CREATE TABLE `phpgw_fud_thread_notify` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `thread_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`thread_id`),
  KEY `thread_id` (`thread_id`)
) TYPE=MyISAM AUTO_INCREMENT=525 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_thread_rate_track`
-- 

CREATE TABLE `phpgw_fud_thread_rate_track` (
  `id` int(11) NOT NULL auto_increment,
  `thread_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `stamp` bigint(20) NOT NULL default '0',
  `rating` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `thread_id` (`thread_id`,`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_thread_view`
-- 

CREATE TABLE `phpgw_fud_thread_view` (
  `forum_id` int(11) NOT NULL default '0',
  `page` int(11) NOT NULL default '0',
  `thread_id` int(11) NOT NULL default '0',
  `pos` int(11) NOT NULL auto_increment,
  `tmp` int(11) default NULL,
  PRIMARY KEY  (`forum_id`,`page`,`pos`),
  KEY `forum_id` (`forum_id`,`thread_id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_title_index`
-- 

CREATE TABLE `phpgw_fud_title_index` (
  `id` int(11) NOT NULL auto_increment,
  `word_id` int(11) NOT NULL default '0',
  `msg_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `word_id` (`word_id`,`msg_id`),
  KEY `msg_id` (`msg_id`)
) TYPE=MyISAM AUTO_INCREMENT=3250 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_user_ignore`
-- 

CREATE TABLE `phpgw_fud_user_ignore` (
  `id` int(11) NOT NULL auto_increment,
  `ignore_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`,`ignore_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_fud_users`
-- 

CREATE TABLE `phpgw_fud_users` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(50) default NULL,
  `alias` varchar(50) default NULL,
  `passwd` varchar(32) default NULL,
  `name` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `location` varchar(255) default NULL,
  `interests` varchar(255) default NULL,
  `occupation` varchar(255) default NULL,
  `avatar` int(11) NOT NULL default '0',
  `avatar_loc` text,
  `icq` bigint(20) default NULL,
  `aim` varchar(255) default NULL,
  `yahoo` varchar(255) default NULL,
  `msnm` varchar(255) default NULL,
  `jabber` varchar(255) default NULL,
  `affero` varchar(255) default NULL,
  `posts_ppg` int(11) NOT NULL default '0',
  `time_zone` varchar(255) NOT NULL default 'America/Montreal',
  `bday` int(11) NOT NULL default '0',
  `join_date` bigint(20) NOT NULL default '0',
  `conf_key` varchar(32) NOT NULL default '0',
  `user_image` varchar(255) default NULL,
  `theme` int(11) NOT NULL default '0',
  `posted_msg_count` int(11) NOT NULL default '0',
  `last_visit` bigint(20) NOT NULL default '0',
  `referer_id` int(11) NOT NULL default '0',
  `last_read` bigint(20) NOT NULL default '0',
  `custom_status` text,
  `sig` text,
  `level_id` int(11) NOT NULL default '0',
  `reset_key` varchar(32) NOT NULL default '0',
  `u_last_post_id` int(11) NOT NULL default '0',
  `home_page` varchar(255) default NULL,
  `bio` text,
  `cat_collapse_status` text,
  `custom_color` varchar(255) default NULL,
  `buddy_list` text,
  `ignore_list` text,
  `group_leader_list` text,
  `users_opt` int(11) NOT NULL default '4488117',
  `egw_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `alias` (`alias`),
  UNIQUE KEY `egw_id` (`egw_id`),
  KEY `conf_key` (`conf_key`),
  KEY `last_visit` (`last_visit`),
  KEY `referer_id` (`referer_id`),
  KEY `reset_key` (`reset_key`),
  KEY `users_opt` (`users_opt`),
  KEY `email` (`email`)
) TYPE=MyISAM AUTO_INCREMENT=330 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_headlines_cached`
-- 

CREATE TABLE `phpgw_headlines_cached` (
  `site` int(11) NOT NULL default '0',
  `title` varchar(255) default NULL,
  `link` varchar(255) default NULL
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_headlines_sites`
-- 

CREATE TABLE `phpgw_headlines_sites` (
  `con` int(11) NOT NULL auto_increment,
  `display` varchar(255) default NULL,
  `base_url` varchar(255) default NULL,
  `newsfile` varchar(255) default NULL,
  `lastread` int(11) default NULL,
  `newstype` varchar(15) default NULL,
  `cachetime` int(11) default NULL,
  `listings` int(11) default NULL,
  PRIMARY KEY  (`con`)
) TYPE=MyISAM AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_history_log`
-- 

CREATE TABLE `phpgw_history_log` (
  `history_id` int(11) NOT NULL auto_increment,
  `history_record_id` int(11) NOT NULL default '0',
  `history_appname` varchar(64) NOT NULL default '',
  `history_owner` int(11) NOT NULL default '0',
  `history_status` char(2) NOT NULL default '',
  `history_new_value` text NOT NULL,
  `history_timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `history_old_value` text NOT NULL,
  PRIMARY KEY  (`history_id`),
  KEY `history_appname` (`history_appname`,`history_record_id`,`history_status`,`history_timestamp`)
) TYPE=MyISAM AUTO_INCREMENT=2057 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_hooks`
-- 

CREATE TABLE `phpgw_hooks` (
  `hook_id` int(11) NOT NULL auto_increment,
  `hook_appname` varchar(255) default NULL,
  `hook_location` varchar(255) default NULL,
  `hook_filename` varchar(255) default NULL,
  PRIMARY KEY  (`hook_id`)
) TYPE=MyISAM AUTO_INCREMENT=1829 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_infolog`
-- 

CREATE TABLE `phpgw_infolog` (
  `info_id` int(11) NOT NULL auto_increment,
  `info_type` varchar(40) NOT NULL default 'task',
  `info_from` varchar(255) default NULL,
  `info_addr` varchar(255) default NULL,
  `info_subject` varchar(255) default NULL,
  `info_des` text,
  `info_owner` int(11) NOT NULL default '0',
  `info_responsible` int(11) NOT NULL default '0',
  `info_access` varchar(10) default 'public',
  `info_cat` int(11) NOT NULL default '0',
  `info_datemodified` int(11) NOT NULL default '0',
  `info_startdate` int(11) NOT NULL default '0',
  `info_enddate` int(11) NOT NULL default '0',
  `info_id_parent` int(11) NOT NULL default '0',
  `info_pri` varchar(10) default 'normal',
  `info_time` int(11) NOT NULL default '0',
  `info_bill_cat` int(11) NOT NULL default '0',
  `info_status` varchar(40) default 'done',
  `info_confirm` varchar(10) default 'not',
  `info_modifier` int(11) NOT NULL default '0',
  `info_link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`info_id`),
  KEY `info_owner` (`info_owner`,`info_responsible`,`info_status`,`info_startdate`),
  KEY `info_id_parent` (`info_id_parent`,`info_owner`,`info_responsible`,`info_status`,`info_startdate`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_infolog_extra`
-- 

CREATE TABLE `phpgw_infolog_extra` (
  `info_id` int(11) NOT NULL default '0',
  `info_extra_name` varchar(32) NOT NULL default '',
  `info_extra_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`info_id`,`info_extra_name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_interserv`
-- 

CREATE TABLE `phpgw_interserv` (
  `server_id` int(11) NOT NULL auto_increment,
  `server_name` varchar(64) default NULL,
  `server_host` varchar(255) default NULL,
  `server_url` varchar(255) default NULL,
  `trust_level` int(11) default NULL,
  `trust_rel` int(11) default NULL,
  `username` varchar(64) default NULL,
  `password` varchar(255) default NULL,
  `admin_name` varchar(255) default NULL,
  `admin_email` varchar(255) default NULL,
  `server_mode` varchar(16) NOT NULL default 'xmlrpc',
  `server_security` varchar(16) default NULL,
  PRIMARY KEY  (`server_id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_jinn_acl`
-- 

CREATE TABLE `phpgw_jinn_acl` (
  `site_id` int(11) default NULL,
  `site_object_id` int(11) default NULL,
  `uid` int(11) default NULL,
  `rights` int(11) default NULL
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_jinn_adv_field_conf`
-- 

CREATE TABLE `phpgw_jinn_adv_field_conf` (
  `parent_object` int(11) NOT NULL default '0',
  `field_name` varchar(50) NOT NULL default '',
  `field_type` varchar(20) NOT NULL default '',
  `field_alt_name` varchar(50) NOT NULL default '',
  `field_help_info` text NOT NULL,
  `field_read_protection` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`parent_object`,`field_name`),
  KEY `parent_object` (`parent_object`),
  KEY `field_name` (`field_name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_jinn_site_objects`
-- 

CREATE TABLE `phpgw_jinn_site_objects` (
  `object_id` int(11) NOT NULL auto_increment,
  `parent_site_id` int(11) default NULL,
  `name` varchar(50) NOT NULL default '',
  `table_name` varchar(30) default NULL,
  `upload_path` varchar(250) NOT NULL default '',
  `relations` text,
  `plugins` text,
  `help_information` text,
  `dev_upload_path` varchar(255) default NULL,
  `max_records` int(11) default NULL,
  `serialnumber` int(11) default NULL,
  PRIMARY KEY  (`object_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_jinn_sites`
-- 

CREATE TABLE `phpgw_jinn_sites` (
  `site_id` int(11) NOT NULL auto_increment,
  `site_name` varchar(100) default NULL,
  `site_db_name` varchar(50) NOT NULL default '',
  `site_db_host` varchar(50) NOT NULL default '',
  `site_db_user` varchar(30) NOT NULL default '',
  `site_db_password` varchar(30) NOT NULL default '',
  `site_db_type` varchar(10) NOT NULL default '',
  `upload_path` varchar(250) NOT NULL default '',
  `dev_site_db_name` varchar(100) NOT NULL default '',
  `dev_site_db_host` varchar(50) NOT NULL default '',
  `dev_site_db_user` varchar(30) NOT NULL default '',
  `dev_site_db_password` varchar(30) NOT NULL default '',
  `dev_site_db_type` varchar(10) NOT NULL default '',
  `dev_upload_path` varchar(250) NOT NULL default '',
  `website_url` varchar(250) NOT NULL default '',
  `serialnumber` int(11) default NULL,
  PRIMARY KEY  (`site_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_kb_articles`
-- 

CREATE TABLE `phpgw_kb_articles` (
  `art_id` int(11) NOT NULL auto_increment,
  `q_id` bigint(20) NOT NULL default '0',
  `title` text NOT NULL,
  `topic` text NOT NULL,
  `text` text NOT NULL,
  `cat_id` int(11) NOT NULL default '0',
  `published` smallint(6) NOT NULL default '0',
  `keywords` text NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `views` int(11) NOT NULL default '0',
  `created` int(11) default NULL,
  `modified` int(11) default NULL,
  `modified_user_id` int(11) NOT NULL default '0',
  `files` text NOT NULL,
  `urls` text NOT NULL,
  `votes_1` int(11) NOT NULL default '0',
  `votes_2` int(11) NOT NULL default '0',
  `votes_3` int(11) NOT NULL default '0',
  `votes_4` int(11) NOT NULL default '0',
  `votes_5` int(11) NOT NULL default '0',
  PRIMARY KEY  (`art_id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_kb_comment`
-- 

CREATE TABLE `phpgw_kb_comment` (
  `comment_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `comment` text NOT NULL,
  `entered` int(11) default NULL,
  `art_id` int(11) NOT NULL default '0',
  `published` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`comment_id`),
  KEY `art_id` (`art_id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_kb_questions`
-- 

CREATE TABLE `phpgw_kb_questions` (
  `question_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `summary` text NOT NULL,
  `details` text NOT NULL,
  `cat_id` int(11) NOT NULL default '0',
  `creation` int(11) default NULL,
  `published` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`question_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_kb_ratings`
-- 

CREATE TABLE `phpgw_kb_ratings` (
  `user_id` int(11) NOT NULL default '0',
  `art_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`art_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_kb_related_art`
-- 

CREATE TABLE `phpgw_kb_related_art` (
  `art_id` int(11) NOT NULL default '0',
  `related_art_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`art_id`,`related_art_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_kb_search`
-- 

CREATE TABLE `phpgw_kb_search` (
  `keyword` varchar(30) NOT NULL default '',
  `art_id` int(11) NOT NULL default '0',
  `score` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`keyword`,`art_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_lang`
-- 

CREATE TABLE `phpgw_lang` (
  `lang` varchar(5) NOT NULL default '',
  `app_name` varchar(100) NOT NULL default 'common',
  `message_id` varchar(255) NOT NULL default '',
  `content` text,
  PRIMARY KEY  (`lang`,`app_name`,`message_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_languages`
-- 

CREATE TABLE `phpgw_languages` (
  `lang_id` varchar(5) NOT NULL default '',
  `lang_name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`lang_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_links`
-- 

CREATE TABLE `phpgw_links` (
  `link_id` int(11) NOT NULL auto_increment,
  `link_app1` varchar(25) NOT NULL default '',
  `link_id1` varchar(50) NOT NULL default '',
  `link_app2` varchar(25) NOT NULL default '',
  `link_id2` varchar(50) NOT NULL default '',
  `link_remark` varchar(50) default NULL,
  `link_lastmod` int(11) NOT NULL default '0',
  `link_owner` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `link_app1` (`link_app1`,`link_id1`,`link_lastmod`),
  KEY `link_app2` (`link_app2`,`link_id2`,`link_lastmod`)
) TYPE=MyISAM AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_log`
-- 

CREATE TABLE `phpgw_log` (
  `log_id` int(11) NOT NULL auto_increment,
  `log_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `log_user` int(11) NOT NULL default '0',
  `log_app` varchar(50) NOT NULL default '',
  `log_severity` char(1) NOT NULL default '',
  PRIMARY KEY  (`log_id`)
) TYPE=MyISAM AUTO_INCREMENT=764 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_log_msg`
-- 

CREATE TABLE `phpgw_log_msg` (
  `log_msg_log_id` int(11) NOT NULL default '0',
  `log_msg_seq_no` int(11) NOT NULL default '0',
  `log_msg_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `log_msg_tx_fid` varchar(4) default NULL,
  `log_msg_tx_id` varchar(4) default NULL,
  `log_msg_severity` char(1) NOT NULL default '',
  `log_msg_code` varchar(30) NOT NULL default '',
  `log_msg_msg` text NOT NULL,
  `log_msg_parms` text NOT NULL,
  `log_msg_file` varchar(255) NOT NULL default '',
  `log_msg_line` int(11) NOT NULL default '0',
  PRIMARY KEY  (`log_msg_log_id`,`log_msg_seq_no`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_messenger_messages`
-- 

CREATE TABLE `phpgw_messenger_messages` (
  `message_id` int(11) NOT NULL auto_increment,
  `message_owner` int(11) NOT NULL default '0',
  `message_from` int(11) NOT NULL default '0',
  `message_status` char(1) NOT NULL default '',
  `message_date` int(11) NOT NULL default '0',
  `message_subject` text NOT NULL,
  `message_content` text NOT NULL,
  `message_folder` varchar(32) NOT NULL default 'inbox',
  PRIMARY KEY  (`message_id`),
  UNIQUE KEY `message_id` (`message_id`)
) TYPE=MyISAM AUTO_INCREMENT=2219 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_news`
-- 

CREATE TABLE `phpgw_news` (
  `news_id` int(11) NOT NULL auto_increment,
  `news_date` int(11) default NULL,
  `news_subject` varchar(255) default NULL,
  `news_submittedby` varchar(255) default NULL,
  `news_content` blob,
  `news_begin` int(11) default NULL,
  `news_end` int(11) default NULL,
  `news_cat` int(11) default NULL,
  `news_teaser` varchar(255) default NULL,
  `is_html` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`news_id`),
  KEY `news_date` (`news_date`),
  KEY `news_subject` (`news_subject`)
) TYPE=MyISAM AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_news_export`
-- 

CREATE TABLE `phpgw_news_export` (
  `cat_id` int(11) NOT NULL default '0',
  `export_type` smallint(6) default NULL,
  `export_itemsyntax` smallint(6) default NULL,
  `export_title` varchar(255) default NULL,
  `export_link` varchar(255) default NULL,
  `export_description` text,
  `export_img_title` varchar(255) default NULL,
  `export_img_url` varchar(255) default NULL,
  `export_img_link` varchar(255) default NULL,
  PRIMARY KEY  (`cat_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_nextid`
-- 

CREATE TABLE `phpgw_nextid` (
  `id` int(11) default NULL,
  `appname` varchar(25) NOT NULL default '',
  UNIQUE KEY `appname` (`appname`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_activities`
-- 

CREATE TABLE `phpgw_p_activities` (
  `id` int(11) NOT NULL auto_increment,
  `a_number` varchar(20) NOT NULL default '',
  `descr` varchar(255) NOT NULL default '',
  `remarkreq` char(1) NOT NULL default 'N',
  `minperae` int(11) NOT NULL default '0',
  `billperae` decimal(20,2) NOT NULL default '0.00',
  `category` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `a_number` (`a_number`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_alarm`
-- 

CREATE TABLE `phpgw_p_alarm` (
  `alarm_id` int(11) NOT NULL auto_increment,
  `alarm_type` varchar(20) NOT NULL default '',
  `project_id` int(11) default '0',
  `alarm_extra` int(11) default '0',
  `alarm_send` char(1) default '1',
  PRIMARY KEY  (`alarm_id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_budget`
-- 

CREATE TABLE `phpgw_p_budget` (
  `budget_id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL default '0',
  `budget_amount` decimal(20,2) NOT NULL default '0.00',
  `budget_year` int(11) default '0',
  `budget_month` int(11) default '0',
  PRIMARY KEY  (`budget_id`),
  KEY `project_id` (`project_id`),
  KEY `budget_year` (`budget_year`),
  KEY `budget_month` (`budget_month`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_costs`
-- 

CREATE TABLE `phpgw_p_costs` (
  `cost_id` int(11) NOT NULL auto_increment,
  `cost_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cost_id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_delivery`
-- 

CREATE TABLE `phpgw_p_delivery` (
  `id` int(11) NOT NULL auto_increment,
  `d_number` varchar(20) NOT NULL default '',
  `d_date` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `customer` int(11) NOT NULL default '0',
  `owner` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `d_number` (`d_number`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_deliverypos`
-- 

CREATE TABLE `phpgw_p_deliverypos` (
  `id` int(11) NOT NULL auto_increment,
  `delivery_id` int(11) NOT NULL default '0',
  `hours_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_events`
-- 

CREATE TABLE `phpgw_p_events` (
  `event_id` int(11) NOT NULL auto_increment,
  `event_name` varchar(255) NOT NULL default '',
  `event_type` varchar(20) NOT NULL default '',
  `event_extra` smallint(6) default '0',
  PRIMARY KEY  (`event_id`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_hours`
-- 

CREATE TABLE `phpgw_p_hours` (
  `id` int(11) NOT NULL auto_increment,
  `employee` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `activity_id` int(11) NOT NULL default '0',
  `entry_date` int(11) NOT NULL default '0',
  `start_date` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  `remark` text,
  `minutes` int(11) NOT NULL default '0',
  `status` varchar(6) NOT NULL default 'done',
  `hours_descr` varchar(255) NOT NULL default '',
  `dstatus` char(1) default 'o',
  `pro_parent` int(11) NOT NULL default '0',
  `pro_main` int(11) NOT NULL default '0',
  `billable` char(1) NOT NULL default 'Y',
  `km_distance` decimal(20,2) default '0.00',
  `t_journey` decimal(20,2) default '0.00',
  `cost_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `cost_id` (`cost_id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_invoice`
-- 

CREATE TABLE `phpgw_p_invoice` (
  `id` int(11) NOT NULL auto_increment,
  `i_number` varchar(20) NOT NULL default '',
  `i_date` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `customer` int(11) NOT NULL default '0',
  `i_sum` decimal(20,2) NOT NULL default '0.00',
  `owner` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `i_number` (`i_number`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_invoicepos`
-- 

CREATE TABLE `phpgw_p_invoicepos` (
  `id` int(11) NOT NULL auto_increment,
  `invoice_id` int(11) NOT NULL default '0',
  `hours_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_mstones`
-- 

CREATE TABLE `phpgw_p_mstones` (
  `s_id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` varchar(255) default NULL,
  `edate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`s_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_projectactivities`
-- 

CREATE TABLE `phpgw_p_projectactivities` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL default '0',
  `activity_id` int(11) NOT NULL default '0',
  `billable` char(1) NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_projectmembers`
-- 

CREATE TABLE `phpgw_p_projectmembers` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL default '0',
  `account_id` int(11) NOT NULL default '0',
  `type` varchar(20) default NULL,
  `accounting` decimal(20,2) default '0.00',
  `role_id` int(11) default '0',
  `events` varchar(255) default NULL,
  `d_accounting` decimal(20,2) default '0.00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_projects`
-- 

CREATE TABLE `phpgw_p_projects` (
  `project_id` int(11) NOT NULL auto_increment,
  `p_number` varchar(255) NOT NULL default '',
  `owner` int(11) NOT NULL default '0',
  `access` varchar(7) default NULL,
  `entry_date` int(11) NOT NULL default '0',
  `start_date` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  `coordinator` int(11) NOT NULL default '0',
  `customer` int(11) NOT NULL default '0',
  `status` varchar(9) NOT NULL default 'active',
  `descr` text,
  `title` varchar(255) NOT NULL default '',
  `budget` decimal(20,2) NOT NULL default '0.00',
  `category` int(11) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `time_planned` int(11) NOT NULL default '0',
  `date_created` int(11) NOT NULL default '0',
  `processor` int(11) NOT NULL default '0',
  `investment_nr` varchar(50) default NULL,
  `main` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `previous` int(11) NOT NULL default '0',
  `customer_nr` varchar(50) default NULL,
  `reference` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `result` text,
  `test` text,
  `quality` text,
  `accounting` varchar(8) default NULL,
  `acc_factor` decimal(20,2) NOT NULL default '0.00',
  `billable` char(1) NOT NULL default 'N',
  `psdate` int(11) NOT NULL default '0',
  `pedate` int(11) NOT NULL default '0',
  `priority` smallint(6) default '0',
  `discount` decimal(20,2) default '0.00',
  `e_budget` decimal(20,2) default '0.00',
  `inv_method` text,
  `acc_factor_d` decimal(20,2) default '0.00',
  `discount_type` varchar(7) default NULL,
  PRIMARY KEY  (`project_id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_resources`
-- 

CREATE TABLE `phpgw_p_resources` (
  `employee` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `resource` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`project_id`),
  KEY `employee` (`employee`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_roles`
-- 

CREATE TABLE `phpgw_p_roles` (
  `role_id` int(11) NOT NULL auto_increment,
  `role_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`role_id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_p_ttracker`
-- 

CREATE TABLE `phpgw_p_ttracker` (
  `track_id` int(11) NOT NULL auto_increment,
  `employee` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `activity_id` int(11) NOT NULL default '0',
  `start_date` int(11) NOT NULL default '0',
  `end_date` int(11) NOT NULL default '0',
  `remark` text,
  `hours_descr` varchar(255) NOT NULL default '',
  `status` varchar(8) default NULL,
  `minutes` int(11) NOT NULL default '0',
  `km_distance` decimal(20,2) default '0.00',
  `t_journey` decimal(20,2) default '0.00',
  `stopped` char(1) default 'N',
  `cost_id` int(11) NOT NULL default '0',
  `billable` char(1) default 'Y',
  PRIMARY KEY  (`track_id`),
  KEY `project_id` (`project_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_polls_data`
-- 

CREATE TABLE `phpgw_polls_data` (
  `poll_id` int(11) NOT NULL default '0',
  `option_text` varchar(100) NOT NULL default '',
  `option_count` int(11) NOT NULL default '0',
  `vote_id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_polls_desc`
-- 

CREATE TABLE `phpgw_polls_desc` (
  `poll_id` int(11) NOT NULL auto_increment,
  `poll_title` varchar(100) NOT NULL default '',
  `poll_timestamp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`poll_id`)
) TYPE=MyISAM AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_polls_settings`
-- 

CREATE TABLE `phpgw_polls_settings` (
  `setting_name` varchar(255) default NULL,
  `setting_value` varchar(255) default NULL
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_polls_user`
-- 

CREATE TABLE `phpgw_polls_user` (
  `poll_id` int(11) NOT NULL default '0',
  `vote_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `vote_timestamp` int(11) default NULL
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_preferences`
-- 

CREATE TABLE `phpgw_preferences` (
  `preference_owner` int(11) NOT NULL default '0',
  `preference_app` varchar(25) NOT NULL default '',
  `preference_value` text NOT NULL,
  PRIMARY KEY  (`preference_owner`,`preference_app`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_reg_accounts`
-- 

CREATE TABLE `phpgw_reg_accounts` (
  `reg_id` varchar(32) NOT NULL default '',
  `reg_lid` varchar(255) NOT NULL default '',
  `reg_info` text NOT NULL,
  `reg_dla` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_reg_fields`
-- 

CREATE TABLE `phpgw_reg_fields` (
  `field_name` varchar(255) NOT NULL default '',
  `field_text` text NOT NULL,
  `field_type` varchar(255) NOT NULL default '',
  `field_values` text,
  `field_required` char(1) NOT NULL default '',
  `field_order` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sessions`
-- 

CREATE TABLE `phpgw_sessions` (
  `session_id` varchar(128) NOT NULL default '',
  `session_lid` varchar(128) default NULL,
  `session_ip` varchar(32) default NULL,
  `session_logintime` int(11) default NULL,
  `session_dla` int(11) default NULL,
  `session_action` varchar(255) default NULL,
  `session_flags` char(2) default NULL,
  UNIQUE KEY `session_id` (`session_id`),
  KEY `session_flags` (`session_flags`,`session_dla`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_active_modules`
-- 

CREATE TABLE `phpgw_sitemgr_active_modules` (
  `area` varchar(50) NOT NULL default '',
  `cat_id` int(11) NOT NULL default '0',
  `module_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`area`,`cat_id`,`module_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_blocks`
-- 

CREATE TABLE `phpgw_sitemgr_blocks` (
  `block_id` int(11) NOT NULL auto_increment,
  `area` varchar(50) default NULL,
  `cat_id` int(11) default NULL,
  `page_id` int(11) default NULL,
  `module_id` int(11) NOT NULL default '0',
  `sort_order` int(11) default NULL,
  `viewable` int(11) default NULL,
  PRIMARY KEY  (`block_id`)
) TYPE=MyISAM AUTO_INCREMENT=89 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_blocks_lang`
-- 

CREATE TABLE `phpgw_sitemgr_blocks_lang` (
  `block_id` int(11) NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `title` varchar(255) default NULL,
  PRIMARY KEY  (`block_id`,`lang`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_categories_lang`
-- 

CREATE TABLE `phpgw_sitemgr_categories_lang` (
  `cat_id` int(11) NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `name` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`cat_id`,`lang`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_categories_state`
-- 

CREATE TABLE `phpgw_sitemgr_categories_state` (
  `cat_id` int(11) NOT NULL default '0',
  `state` smallint(6) default NULL,
  `index_page_id` int(11) default '0',
  PRIMARY KEY  (`cat_id`),
  KEY `cat_id` (`cat_id`,`state`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_content`
-- 

CREATE TABLE `phpgw_sitemgr_content` (
  `version_id` int(11) NOT NULL auto_increment,
  `block_id` int(11) NOT NULL default '0',
  `arguments` text,
  `state` smallint(6) default NULL,
  PRIMARY KEY  (`version_id`)
) TYPE=MyISAM AUTO_INCREMENT=89 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_content_lang`
-- 

CREATE TABLE `phpgw_sitemgr_content_lang` (
  `version_id` int(11) NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `arguments_lang` text,
  PRIMARY KEY  (`version_id`,`lang`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_modules`
-- 

CREATE TABLE `phpgw_sitemgr_modules` (
  `module_id` int(11) NOT NULL auto_increment,
  `module_name` varchar(25) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`module_id`)
) TYPE=MyISAM AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_pages`
-- 

CREATE TABLE `phpgw_sitemgr_pages` (
  `page_id` int(11) NOT NULL auto_increment,
  `cat_id` int(11) default NULL,
  `sort_order` int(11) default NULL,
  `hide_page` int(11) default NULL,
  `name` varchar(100) default NULL,
  `state` smallint(6) default NULL,
  PRIMARY KEY  (`page_id`),
  KEY `cat_id` (`cat_id`),
  KEY `state` (`state`,`cat_id`,`sort_order`),
  KEY `name` (`name`,`cat_id`)
) TYPE=MyISAM AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_pages_lang`
-- 

CREATE TABLE `phpgw_sitemgr_pages_lang` (
  `page_id` int(11) NOT NULL default '0',
  `lang` varchar(5) NOT NULL default '',
  `title` varchar(255) default NULL,
  `subtitle` varchar(255) default NULL,
  PRIMARY KEY  (`page_id`,`lang`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_properties`
-- 

CREATE TABLE `phpgw_sitemgr_properties` (
  `area` varchar(50) NOT NULL default '',
  `cat_id` int(11) NOT NULL default '0',
  `module_id` int(11) NOT NULL default '0',
  `properties` text,
  PRIMARY KEY  (`area`,`cat_id`,`module_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_sitemgr_sites`
-- 

CREATE TABLE `phpgw_sitemgr_sites` (
  `site_id` int(11) NOT NULL default '0',
  `site_name` varchar(255) default NULL,
  `site_url` varchar(255) default NULL,
  `site_dir` varchar(255) default NULL,
  `themesel` varchar(50) default NULL,
  `site_languages` varchar(50) default NULL,
  `home_page_id` int(11) default NULL,
  `anonymous_user` varchar(50) default NULL,
  `anonymous_passwd` varchar(50) default NULL,
  PRIMARY KEY  (`site_id`),
  KEY `site_url` (`site_url`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_tts_states`
-- 

CREATE TABLE `phpgw_tts_states` (
  `state_id` int(11) NOT NULL auto_increment,
  `state_name` varchar(64) NOT NULL default '',
  `state_description` varchar(255) NOT NULL default '',
  `state_initial` int(11) NOT NULL default '0',
  PRIMARY KEY  (`state_id`)
) TYPE=MyISAM AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_tts_tickets`
-- 

CREATE TABLE `phpgw_tts_tickets` (
  `ticket_id` int(11) NOT NULL auto_increment,
  `ticket_group` varchar(40) default NULL,
  `ticket_priority` smallint(6) NOT NULL default '0',
  `ticket_owner` varchar(10) default NULL,
  `ticket_assignedto` varchar(10) default NULL,
  `ticket_subject` varchar(255) default NULL,
  `ticket_category` varchar(25) default NULL,
  `ticket_billable_hours` decimal(8,2) NOT NULL default '0.00',
  `ticket_billable_rate` decimal(8,2) NOT NULL default '0.00',
  `ticket_groupnotification` tinyint(1) unsigned default '0',
  `ticket_status` char(1) NOT NULL default '',
  `ticket_details` text NOT NULL,
  `ticket_state` int(11) NOT NULL default '-1',
  PRIMARY KEY  (`ticket_id`)
) TYPE=MyISAM AUTO_INCREMENT=172 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_tts_transitions`
-- 

CREATE TABLE `phpgw_tts_transitions` (
  `transition_id` int(11) NOT NULL auto_increment,
  `transition_name` varchar(64) NOT NULL default '',
  `transition_description` varchar(255) NOT NULL default '',
  `transition_source_state` int(11) NOT NULL default '0',
  `transition_target_state` int(11) NOT NULL default '0',
  PRIMARY KEY  (`transition_id`)
) TYPE=MyISAM AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_tts_views`
-- 

CREATE TABLE `phpgw_tts_views` (
  `view_id` int(11) NOT NULL default '0',
  `view_account_id` varchar(40) default NULL,
  `view_time` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_vfs`
-- 

CREATE TABLE `phpgw_vfs` (
  `file_id` int(11) NOT NULL auto_increment,
  `owner_id` int(11) NOT NULL default '0',
  `createdby_id` int(11) default NULL,
  `modifiedby_id` int(11) default NULL,
  `created` date NOT NULL default '1970-01-01',
  `modified` date default NULL,
  `size` int(11) default NULL,
  `mime_type` varchar(64) default NULL,
  `deleteable` char(1) default 'Y',
  `comment` varchar(255) default NULL,
  `app` varchar(25) default NULL,
  `directory` varchar(255) default NULL,
  `name` varchar(128) NOT NULL default '',
  `link_directory` varchar(255) default NULL,
  `link_name` varchar(128) default NULL,
  `version` varchar(30) NOT NULL default '0.0.0.0',
  `content` longtext,
  PRIMARY KEY  (`file_id`),
  KEY `directory` (`directory`,`name`,`mime_type`)
) TYPE=MyISAM AUTO_INCREMENT=61 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_wiki_interwiki`
-- 

CREATE TABLE `phpgw_wiki_interwiki` (
  `wiki_id` int(11) NOT NULL default '0',
  `prefix` varchar(80) NOT NULL default '',
  `where_defined_page` varchar(80) NOT NULL default '',
  `where_defined_lang` varchar(5) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`wiki_id`,`prefix`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_wiki_links`
-- 

CREATE TABLE `phpgw_wiki_links` (
  `wiki_id` smallint(6) NOT NULL default '0',
  `page` varchar(80) NOT NULL default '',
  `lang` varchar(5) NOT NULL default '',
  `link` varchar(80) NOT NULL default '',
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wiki_id`,`page`,`lang`,`link`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_wiki_pages`
-- 

CREATE TABLE `phpgw_wiki_pages` (
  `wiki_id` smallint(6) NOT NULL default '0',
  `name` varchar(80) NOT NULL default '',
  `lang` varchar(5) NOT NULL default '',
  `version` int(11) NOT NULL default '1',
  `time` int(11) default NULL,
  `supercede` int(11) default NULL,
  `readable` int(11) NOT NULL default '0',
  `writable` int(11) NOT NULL default '0',
  `username` varchar(80) default NULL,
  `hostname` varchar(80) NOT NULL default '',
  `comment` varchar(80) NOT NULL default '',
  `title` varchar(80) default NULL,
  `body` text,
  PRIMARY KEY  (`wiki_id`,`name`,`lang`,`version`),
  KEY `title` (`title`),
  FULLTEXT KEY `body` (`body`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_wiki_rate`
-- 

CREATE TABLE `phpgw_wiki_rate` (
  `ip` char(20) NOT NULL default '',
  `time` int(11) default NULL,
  `viewLimit` smallint(6) default NULL,
  `searchLimit` smallint(6) default NULL,
  `editLimit` smallint(6) default NULL,
  PRIMARY KEY  (`ip`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_wiki_remote_pages`
-- 

CREATE TABLE `phpgw_wiki_remote_pages` (
  `page` varchar(80) NOT NULL default '',
  `site` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`page`,`site`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `phpgw_wiki_sisterwiki`
-- 

CREATE TABLE `phpgw_wiki_sisterwiki` (
  `wiki_id` int(11) NOT NULL default '0',
  `prefix` varchar(80) NOT NULL default '',
  `where_defined_page` varchar(80) NOT NULL default '',
  `where_defined_lang` varchar(5) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`wiki_id`,`prefix`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `privacy_confirmations`
-- 

CREATE TABLE `privacy_confirmations` (
  `id` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `state` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `owner` (`owner`,`state`)
) TYPE=MyISAM AUTO_INCREMENT=176 ;

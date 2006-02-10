<?php
$GLOBALS['MSG_STORE_DIR']='/home/gfdsa/milanin/';

function read_msg_body($off, $len, $file_id)
{
        $fp = register_fp($file_id);
        fseek($fp, $off);
        return fread($fp, $len);
}
function register_fp($id)
{
        if (!isset($GLOBALS['__MSG_FP__'][$id])) {
                $GLOBALS['__MSG_FP__'][$id] = fopen($GLOBALS['MSG_STORE_DIR'].'msg_'.$id, 'rb');
}

        return $GLOBALS['__MSG_FP__'][$id];
}
$link = mysql_connect('localhost', 'egw', '')
   or die('Could not connect: ' . mysql_error());
mysql_select_db('egroupware_trunk') or die('Could not select database');

$query = 'SELECT * FROM `phpgw_fud_forum`';
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

// highest cat_id
$i=51;
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
  $i++;
//   print_r($line); 
  $vanilla_insert='INSERT INTO phpgw_categories
 (cat_id,cat_main,cat_parent,cat_level,cat_owner,cat_access,cat_appname,cat_name,cat_description,cat_data,last_mod,`order`) 
    VALUES ('.$i.','.$i.',0,0,7,\'public\',\'vanilla\',\''.mysql_escape_string($line['name']).'\',\''.mysql_escape_string($line['descr']).'\',\'\',\'\',\'\')';
  //mysql_query($vanilla_insert) or die('['.$vanilla_insert.'] Query failed: ' . mysql_error());
  $fud_threads='SELECT t.* ,
                m.subject, 
                u.egw_id AS owner, 
                lu.egw_id AS last_poster,
                FROM_UNIXTIME(m.post_stamp) as date_created,
                FROM_UNIXTIME(t.last_post_date) as date_last
                FROM `phpgw_fud_thread` t
                INNER JOIN phpgw_fud_msg m ON t.root_msg_id = m.id
                LEFT JOIN phpgw_fud_users u ON m.poster_id = u.id
                LEFT JOIN phpgw_fud_msg l ON t.last_post_id = l.id
                LEFT JOIN phpgw_fud_users lu ON l.poster_id = lu.id
                WHERE forum_id='.$line['id'];
  $threads_result=mysql_query($fud_threads) or die('['.$fud_threads.'] Query failed: ' . mysql_error());
  while ($thread=mysql_fetch_array($threads_result, MYSQL_ASSOC)) {
//     print_r($thread);
    $vanila_d_sql='INSERT INTO LUM_Discussion VALUES('.
                    $thread['id'].','.
                    $thread['owner'].','.
                    '0,'.
                    $thread['root_msg_id'].','.
                    $thread['last_poster'].','.
                    '1,'.
                    (($thread['thread_opt'] % 2)==0 ? '0,' : '1,').
                    (($thread['thread_opt'] > 1) ? '1,' : '0,'). 
                    '\''.mysql_escape_string($thread['subject']).'\','.
                    '\''.$thread['date_created'].'\','.
                    '\''.$thread['date_last'].'\','.
                    $thread['replies'].','.
                    $i.','.
                    '0,0,0,0)';
    //mysql_query($vanila_d_sql) or die('['.$vanila_d_sql.'] Query failed: ' . mysql_error());
  }
                    

}
mysql_free_result($result);
mysql_free_result($threads_result);
$fud_msg_sql="SELECT 
             fm.*,
             fu.egw_id as poster,
             fu2.egw_id as updater,
             FROM_UNIXTIME(fm.post_stamp) as date_created, 
             FROM_UNIXTIME(fm.update_stamp) as date_edited 
             FROM `phpgw_fud_msg` fm 
             left join phpgw_fud_users fu on fm.`poster_id`=fu.id
             left join phpgw_fud_users fu2 on fm.`updated_by`=fu2.id"
;
$result=mysql_query($fud_msg_sql) or die('['.$fud_msg_sql.'] Query failed: ' . mysql_error());
while ($msg=mysql_fetch_array($result, MYSQL_ASSOC)) {
  $vanilla_comments_sql='INSERT INTO LUM_Comment VALUES('.
    $msg['id'].','.
    $msg['thread_id'].','.
    $msg['poster'].','.
    '\''.$msg['date_created'].'\','.
    '\''.$msg['updater'].'\','.
    '\''.$msg['date_edited'].'\','.
    '\'\','.
    '\''.mysql_escape_string(read_msg_body($msg['foff'],$msg['length'],$msg['file_id'])).'\','.
    '\'html\','.
    '0,'.
    '\'\','.
    '\'\','.
    '\''.$msg['ip_addr'].'\')';
    mysql_query($vanilla_comments_sql) or die('['.$vanilla_comments_sql.'] Query failed: ' . mysql_error());
}    
mysql_free_result($result);
mysql_close($link);
/*
 id   	  thread_id   	  poster_id   	  reply_to   	  ip_addr   	  host_name   	  post_stamp   	  update_stamp   	  updated_by   	  icon   	  subject   	  attach_cnt   	  poll_id   	  foff   	  length   	  file_id   	  offset_preview   	  length_preview   	  file_id_preview   	  attach_cache   	  poll_cache   	  mlist_msg_id   	  msg_opt   	  apr   	  poster
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
) TYPE=MyISAM AUTO_INCREMENT=13 ;

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
) TYPE=MyISAM AUTO_INCREMENT=5 ;

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
) TYPE=MyISAM AUTO_INCREMENT=130 ;

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
*/
?>
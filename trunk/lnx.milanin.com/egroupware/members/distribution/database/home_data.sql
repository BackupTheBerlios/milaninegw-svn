 
-- 
-- Table structure for table `home_data`
-- 

CREATE TABLE `home_data` (
  `ident` int(10) unsigned NOT NULL auto_increment,
  `owner` int(10) unsigned NOT NULL default '0',
  `access` varchar(16) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`ident`),
  KEY `owner` (`owner`,`access`,`name`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

 
CREATE TABLE `privacy_confirmations` (
  `id` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `state` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `owner` (`owner`,`state`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;
-- 
-- Table structure for table `other_data`
-- 

CREATE TABLE `other_data` (
  `id` bigint(20) NOT NULL default '0',
  `lang` char(2) NOT NULL default 'en',
  `name` varchar(32) NOT NULL default '',
  `data` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
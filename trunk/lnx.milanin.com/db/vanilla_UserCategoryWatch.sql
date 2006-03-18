
CREATE TABLE `LUM_UserCategoryWatch` (
  `UserID` int(10) NOT NULL default '0',
    `CategoryID` int(8) NOT NULL default '0',
      PRIMARY KEY  (`UserID`,`CategoryID`)
      ) TYPE=MyISAM;


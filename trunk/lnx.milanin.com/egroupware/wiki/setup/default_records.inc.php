<?php
  /**************************************************************************\
  * eGroupWare - Setup                                                       *
  * http://www.eGroupWare.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: default_records.inc.php,v 1.9.2.1 2004/07/25 01:34:59 ralfbecker Exp $ */


	$time = time();
	$oProc->query("DELETE FROM phpgw_wiki_pages");
	$pages = array(
		'RecentChanges' => '[[! *]]',
		'eGroupWare' => "Welcome to '''Wiki''' - the eGroupWare Version of '''WikkiTikkiTavi'''. Wikis are a revolutionary new form of collaboration and online community.

'''eGroupWare''' is the groupware suite you are useing right now. For further information see http://www.eGroupWare.org",
		'WikkiTikkiTavi' => "= WikkiTikkiTavi =

WikkiTikkiTavi is the original version this documentation system.
Their documentation is usable for the ((eGroupWare)) '''Wiki''' too.

The documentation of WikkiTikkiTavi is online availible at: http://tavi.sourceforge.net
You can learn about Wiki formatting at http://tavi.sourceforge.net/FormattingRules",
	);
	foreach($pages as $name => $body)
	{
		$name = $GLOBALS['phpgw_setup']->db->db_addslashes($name);
		$body = $GLOBALS['phpgw_setup']->db->db_addslashes($body);
		$oProc->query("INSERT INTO phpgw_wiki_pages (wiki_id,name,lang,version,time,supercede,readable,writable,username,hostname,title,body,comment) VALUES (0,'$name','en',1,$time,$time,0,0,'setup','localhost','$name','$body','added by setup')");
	}

	

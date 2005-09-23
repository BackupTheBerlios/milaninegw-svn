<?php
	/**************************************************************************\
	* eGroupWare - Setup                                                       *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: default_records.inc.php,v 1.8 2004/01/27 18:35:52 reinerj Exp $ */

	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('Slashdot','http://slashdot.org','/slashdot.rdf',0,'rdf',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('Freshmeat','http://freshmeat.net','/backend/fm.rdf',0,'fm',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('Linux&nbsp;Today','http://linuxtoday.com','/backend/linuxtoday.xml',0,'lt',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('Linux&nbsp;Game&nbsp;Tome','http://happypenguin.org','/html/news.rdf',0,'rdf',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('linux-at-work.de','http://linux-at-work.de','/backend.php',0,'rdf',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('Segfault','http://segfault.org','/stories.xml',0,'sf',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('KDE&nbsp;News','http://www.kde.org','/news/kdenews.rdf',0,'rdf',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('Gnome&nbsp;News','http://news.gnome.org','/gnome-news/rdf',0,'rdf',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('Gimp&nbsp;News','http://www.xach.com','/gimp/news/channel.rdf',0,'rdf-chan',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('Mozilla','http://www.mozilla.org','/news.rdf',0,'rdf-chan',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('MozillaZine','http://www.mozillazine.org','/contents.rdf',0,'rdf',60,20)");
	$oProc->query("insert into phpgw_headlines_sites (display,base_url,newsfile,lastread,newstype,cachetime,listings) values ('Security Forums','http://www.security-forums.com','/forum/rdf.php',0,'rdf',60,20)");
?>

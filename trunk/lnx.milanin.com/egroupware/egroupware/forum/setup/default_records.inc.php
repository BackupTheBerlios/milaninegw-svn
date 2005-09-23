<?php
	/**************************************************************************\
	* phpGroupWare - Setup                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: default_records.inc.php,v 1.5.2.1 2004/07/25 01:34:56 ralfbecker Exp $ */

	$oProc->query("INSERT INTO phpgw_forum_categories (name,descr) VALUES ('Just a sample', 'This is a sample category')");
	$oProc->query("INSERT INTO phpgw_forum_categories (name,descr) VALUES ('Another sample category', 'Just another sample')");

	$oProc->query("INSERT INTO phpgw_forum_forums (name,perm,groups,descr, cat_id) VALUES ('Sample', '0', '0', 'This is a sample', '1')");
	$oProc->query("INSERT INTO phpgw_forum_forums (name,perm,groups,descr, cat_id) VALUES ('This is another sample', '0', '0', 'sub-category', '1')");
	$oProc->query("INSERT INTO phpgw_forum_forums (name,perm,groups,descr, cat_id) VALUES ('Sample', '0', '0', 'Wow, what a suprise, another sample :)', '2')");

	$oProc->query("INSERT INTO phpgw_forum_body (cat_id, for_id, message) VALUES ('1', '1','Here is an example message.')");
	$oProc->query("INSERT INTO phpgw_forum_body (cat_id, for_id, message) VALUES ('1', '1', 'Here is an example of a reply.')");
	$oProc->query("INSERT INTO phpgw_forum_body (cat_id, for_id, message) VALUES ('1', '2', 'Yup, another example')");
	$oProc->query("INSERT INTO phpgw_forum_body (cat_id, for_id, message) VALUES ('2', '3', 'I ran out of ideas ... so, heres another sample.')");

	$oProc->query("INSERT INTO phpgw_forum_threads (main,parent,cat_id,for_id,thread_owner,subject,stat,thread,depth,pos,n_replies) VALUES ('1', '-1', '1', '1',0, 'Example','0', '1', '0', '0', '1')");
	$oProc->query("INSERT INTO phpgw_forum_threads (main,parent,cat_id,for_id,thread_owner,subject,stat,thread,depth,pos,n_replies) VALUES ('2', '1', '1', '1',0, 'Re: Example','0', '1', '1', '1', '1')");
	$oProc->query("INSERT INTO phpgw_forum_threads (main,parent,cat_id,for_id,thread_owner,subject,stat,thread,depth,pos,n_replies) VALUES ('3', '-1', '1', '2',0, 'Example message','0', '3', '0', '0', '0')");
	$oProc->query("INSERT INTO phpgw_forum_threads (main,parent,cat_id,for_id,thread_owner,subject,stat,thread,depth,pos,n_replies) VALUES ('4', '-1', '2', '3',0, '','0', '4', '0', '0', '0')");

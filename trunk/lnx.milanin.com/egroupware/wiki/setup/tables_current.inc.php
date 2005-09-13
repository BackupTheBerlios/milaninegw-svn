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

  /* $Id: tables_current.inc.php,v 1.3.2.2 2004/07/31 14:06:03 ralfbecker Exp $ */


	$phpgw_baseline = array(
		'phpgw_wiki_links' => array(
			'fd' => array(
				'wiki_id' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'page' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'lang' => array('type' => 'varchar','precision' => '5','nullable' => False,'default' => ''),
				'link' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'count' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('wiki_id','page','lang','link'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wiki_pages' => array(
			'fd' => array(
				'wiki_id' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'name' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'lang' => array('type' => 'varchar','precision' => '5','nullable' => False,'default' => ''),
				'version' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '1'),
				'time' => array('type' => 'int','precision' => '4'),
				'supercede' => array('type' => 'int','precision' => '4'),
				'readable' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'writable' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'username' => array('type' => 'varchar','precision' => '80'),
				'hostname' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'comment' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'title' => array('type' => 'varchar','precision' => '80'),
				'body' => array('type' => 'text')
			),
			'pk' => array('wiki_id','name','lang','version'),
			'fk' => array(),
			'ix' => array('title',array('body','options' => array('mysql' => 'FULLTEXT','mssql' => False,'pgsql' => False))),
			'uc' => array()
		),
		'phpgw_wiki_rate' => array(
			'fd' => array(
				'ip' => array('type' => 'char','precision' => '20','nullable' => False,'default' => ''),
				'time' => array('type' => 'int','precision' => '4','nullable' => True),
				'viewLimit' => array('type' => 'int','precision' => '2','nullable' => True),
				'searchLimit' => array('type' => 'int','precision' => '2','nullable' => True),
				'editLimit' => array('type' => 'int','precision' => '2','nullable' => True)
			),
			'pk' => array('ip'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wiki_interwiki' => array(
			'fd' => array(
				'wiki_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'prefix' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'where_defined_page' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'where_defined_lang' => array('type' => 'varchar','precision' => '5','nullable' => False,'default' => ''),
				'url' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => '')
			),
			'pk' => array('wiki_id','prefix'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wiki_sisterwiki' => array(
			'fd' => array(
				'wiki_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'prefix' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'where_defined_page' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'where_defined_lang' => array('type' => 'varchar','precision' => '5','nullable' => False,'default' => ''),
				'url' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => '')
			),
			'pk' => array('wiki_id','prefix'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_wiki_remote_pages' => array(
			'fd' => array(
				'page' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => ''),
				'site' => array('type' => 'varchar','precision' => '80','nullable' => False,'default' => '')
			),
			'pk' => array('page','site'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

<?php
  /**************************************************************************\
  * phpGroupWare - Setup                                                     *
  * http://www.eGroupWare.org                                                *
  * Created by eTemplates DB-Tools written by ralfbecker@outdoor-training.de *
  * --------------------------------------------                             *
  * This program is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU General Public License as published by the    *
  * Free Software Foundation; either version 2 of the License, or (at your   *
  * option) any later version.                                               *
  \**************************************************************************/

  /* $Id: tables_update.inc.php,v 1.3.2.1 2004/07/31 14:06:03 ralfbecker Exp $ */

	$test[] = '0.9.15.001';
	function wiki_upgrade0_9_15_001()
	{
		// this will also create the new colums, with its default values and discards the not longer used mutable column
		$GLOBALS['phpgw_setup']->oProc->RefreshTable('phpgw_wiki_pages',array(
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
				'body' => array('type' => 'text'),
			),
			'pk' => array('wiki_id','name','lang','version'),
			'fk' => array(),
			'ix' => array('title',array('body', 'options' => array('mysql' => 'FULLTEXT'))),
			'uc' => array()
		),array(
			'name' => 'title',		// new name column with same content as the title
			'writable' => "CASE WHEN mutable != 'on' THEN -2 ELSE 0 END",	// migrate mutable to new acl
			'hostname' => 'author',	// rename column
		));

		$GLOBALS['setup_info']['wiki']['currentver'] = '0.9.15.002';
		return $GLOBALS['setup_info']['wiki']['currentver'];
	}


	$test[] = '0.9.15.002';
	function wiki_upgrade0_9_15_002()
	{
		$GLOBALS['phpgw_setup']->oProc->RefreshTable('phpgw_wiki_links',array(
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
		));

		$GLOBALS['setup_info']['wiki']['currentver'] = '0.9.15.003';
		return $GLOBALS['setup_info']['wiki']['currentver'];
	}


	$test[] = '0.9.15.003';
	function wiki_upgrade0_9_15_003()
	{
		$GLOBALS['phpgw_setup']->oProc->RefreshTable('phpgw_wiki_interwiki',array(
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
		),array(
			'where_defined_page' => 'where_defined'
		));

		$GLOBALS['setup_info']['wiki']['currentver'] = '0.9.15.004';
		return $GLOBALS['setup_info']['wiki']['currentver'];
	}


	$test[] = '0.9.15.004';
	function wiki_upgrade0_9_15_004()
	{
		$GLOBALS['phpgw_setup']->oProc->RefreshTable('phpgw_wiki_sisterwiki',array(
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
		),array(
			'where_defined_page' => 'where_defined'
		));

		$GLOBALS['setup_info']['wiki']['currentver'] = '0.9.15.005';
		return $GLOBALS['setup_info']['wiki']['currentver'];
	}


	$test[] = '0.9.15.005';
	function wiki_upgrade0_9_15_005()
	{
		$GLOBALS['setup_info']['wiki']['currentver'] = '1.0.0';
		return $GLOBALS['setup_info']['wiki']['currentver'];
	}


	$test[] = '1.0.0';
	function wiki_upgrade1_0_0()
	{
		// drop the index on the page-content, as it limites the content to 2700 chars
		if ($GLOBALS['phpgw_setup']->oProc->sType == 'pgsql')
		{
			// we need to do this in sql, as schemaproc has no function for that atm.
			$GLOBALS['phpgw_setup']->oProc->query('DROP INDEX phpgw_wiki_pages_body_idx');
		}
		$GLOBALS['setup_info']['wiki']['currentver'] = '1.0.0.001';
		return $GLOBALS['setup_info']['wiki']['currentver'];
	}
?>

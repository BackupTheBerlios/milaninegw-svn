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

  /**************************************************************************\
  * This file should be generated for you by setup. It should not need to be *
  * edited by hand.                                                          *
  \**************************************************************************/

  /* $Id: tables_baseline.inc.php,v 1.4 2004/01/15 00:53:49 mipmip Exp $ */

  /* table array for jinn */
	$phpgw_baseline = array(
		'phpgw_jinn_acl' => array(
			'fd' => array(
				'site_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'site_object_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'uid' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'rights' => array('type' => 'int', 'precision' => 4,'nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_jinn_sites' => array(
			'fd' => array(
				'site_id' => array('type' => 'auto','nullable' => False),
				'site_name' => array('type' => 'varchar', 'precision' => 100,'nullable' => True),
				'site_db_name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
				'site_db_host' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
				'site_db_user' => array('type' => 'varchar', 'precision' => 30,'nullable' => False),
				'site_db_password' => array('type' => 'varchar', 'precision' => 30,'nullable' => False),
				'site_db_type' => array('type' => 'varchar', 'precision' => 10,'nullable' => False),
				'upload_path' => array('type' => 'varchar', 'precision' => 250,'nullable' => False),
				'upload_url' => array('type' => 'varchar', 'precision' => 250,'nullable' => False)
			),
			'pk' => array('site_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_jinn_site_objects' => array(
			'fd' => array(
				'object_id' => array('type' => 'auto','nullable' => False),
				'parent_site_id' => array('type' => 'int', 'precision' => 4,'nullable' => True),
				'name' => array('type' => 'varchar', 'precision' => 50,'nullable' => False),
				'table_name' => array('type' => 'varchar', 'precision' => 30,'nullable' => True),
				'upload_path' => array('type' => 'varchar', 'precision' => 250,'nullable' => False),
				'upload_url' => array('type' => 'varchar', 'precision' => 250,'nullable' => False),
				'relations' => array('type' => 'text','nullable' => True),
				'plugins' => array('type' => 'text','nullable' => True)
			),
			'pk' => array('object_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>

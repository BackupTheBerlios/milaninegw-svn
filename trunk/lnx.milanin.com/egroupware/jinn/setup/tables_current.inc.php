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

  /* $Id: tables_current.inc.php,v 1.10.2.1 2004/07/24 18:36:33 ralfbecker Exp $ */

  /* table array for jinn */
	$phpgw_baseline = array(
		'phpgw_jinn_acl' => array(
			'fd' => array(
				'site_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'site_object_id' => array('type' => 'int','precision' => '4','nullable' => True),
				'uid' => array('type' => 'int','precision' => '4','nullable' => True),
				'rights' => array('type' => 'int','precision' => '4','nullable' => True)
			),
			'pk' => array(),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_jinn_sites' => array(
			'fd' => array(
				'site_id' => array('type' => 'auto','nullable' => False),
				'site_name' => array('type' => 'varchar','precision' => '100'),
				'site_db_name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'site_db_host' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'site_db_user' => array('type' => 'varchar','precision' => '30','nullable' => False),
				'site_db_password' => array('type' => 'varchar','precision' => '30','nullable' => False),
				'site_db_type' => array('type' => 'varchar','precision' => '10','nullable' => False),
				'upload_path' => array('type' => 'varchar','precision' => '250','nullable' => False),
				'dev_site_db_name' => array('type' => 'varchar','precision' => '100','nullable' => False),
				'dev_site_db_host' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'dev_site_db_user' => array('type' => 'varchar','precision' => '30','nullable' => False),
				'dev_site_db_password' => array('type' => 'varchar','precision' => '30','nullable' => False),
				'dev_site_db_type' => array('type' => 'varchar','precision' => '10','nullable' => False),
				'dev_upload_path' => array('type' => 'varchar','precision' => '250','nullable' => False),
				'website_url' => array('type' => 'varchar','precision' => '250','nullable' => False),
				'serialnumber' => array('type' => 'int','precision' => '4')
			),
			'pk' => array('site_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_jinn_site_objects' => array(
			'fd' => array(
				'object_id' => array('type' => 'auto','nullable' => False),
				'parent_site_id' => array('type' => 'int','precision' => '4'),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'table_name' => array('type' => 'varchar','precision' => '30'),
				'upload_path' => array('type' => 'varchar','precision' => '250','nullable' => False),
				'relations' => array('type' => 'text'),
				'plugins' => array('type' => 'text'),
				'help_information' => array('type' => 'text'),
				'dev_upload_path' => array('type' => 'varchar','precision' => '255'),
				'max_records' => array('type' => 'int','precision' => '4'),
				'serialnumber' => array('type' => 'int','precision' => '4')
			),
			'pk' => array('object_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_jinn_adv_field_conf' => array(
			'fd' => array(
				'parent_object' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'field_name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'field_type' => array('type' => 'varchar','precision' => '20','nullable' => False),
				'field_alt_name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'field_help_info' => array('type' => 'text','nullable' => False),
				'field_read_protection' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0')
			),
			'pk' => array('parent_object','field_name'),
			'fk' => array(),
			'ix' => array('parent_object','field_name'),
			'uc' => array()
		)
	);
?>

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

   /* $Id: tables_update.inc.php,v 1.9 2004/06/23 19:53:32 mipmip Exp $ */

   $test[] = '0.5.2';
   function jinn_upgrade0_5_2()
   {
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_sites','dev_site_db_name',array(
		 'type' => 'varchar',
		 'precision' => '100',
		 'nullable' => False
	  ));
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_sites','dev_site_db_host',array(
		 'type' => 'varchar',
		 'precision' => '50',
		 'nullable' => False
	  ));
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_sites','dev_site_db_user',array(
		 'type' => 'varchar',
		 'precision' => '30',
		 'nullable' => False
	  ));
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_sites','dev_site_db_password',array(
		 'type' => 'varchar',
		 'precision' => '30',
		 'nullable' => False
	  ));
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_sites','dev_site_db_type',array(
		 'type' => 'varchar',
		 'precision' => '10',
		 'nullable' => False
	  ));
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_sites','dev_upload_path',array(
		 'type' => 'varchar',
		 'precision' => '250',
		 'nullable' => False
	  ));
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_sites','dev_upload_url',array(
		 'type' => 'varchar',
		 'precision' => '250',
		 'nullable' => False
	  ));

	  $GLOBALS['setup_info']['jinn']['currentver'] = '0.6.001';
	  return $GLOBALS['setup_info']['jinn']['currentver'];
   }

   $test[] = '0.6.001';
   function jinn_upgrade0_6_001()
   {
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_site_objects','help_information',array(
		 'type' => 'text'
	  ));
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_site_objects','dev_upload_url',array(
		 'type' => 'varchar',
		 'precision' => '255'
	  ));
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_site_objects','dev_upload_path',array(
		 'type' => 'varchar',
		 'precision' => '255'
	  ));

	  $GLOBALS['setup_info']['jinn']['currentver'] = '0.6.002';
	  return $GLOBALS['setup_info']['jinn']['currentver'];
   }


   $test[] = '0.6.002';
   function jinn_upgrade0_6_002()
   {
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_site_objects','max_records',array(
		 'type' => 'int',
		 'precision' => '4'
	  ));


	  $GLOBALS['setup_info']['jinn']['currentver'] = '0.6.003';
	  return $GLOBALS['setup_info']['jinn']['currentver'];
   }


   $test[] = '0.6.003';
   function jinn_upgrade0_6_003()
   {
	  $GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_sites','website_url',array(
		 'type' => 'varchar',
		 'precision' => '250',
		 'nullable' => False
	  ));

	  $GLOBALS['setup_info']['jinn']['currentver'] = '0.6.004';
	  return $GLOBALS['setup_info']['jinn']['currentver'];
   }


   $test[] = '0.6.004';
   function jinn_upgrade0_6_004()
   {
	  $GLOBALS['phpgw_setup']->oProc->CreateTable('egw_jinn_mail_list',array(
		 'fd' => array(
			'id' => array('type' => 'auto'),
			'name' => array('type' => 'varchar','precision' => '40'),
			'email_table' => array('type' => 'varchar','precision' => '40'),
			'email_field' => array('type' => 'varchar','precision' => '40')
		 ),
		 'pk' => array('id'),
		 'fk' => array(),
		 'ix' => array(),
		 'uc' => array()
	  ));

	  $GLOBALS['phpgw_setup']->oProc->CreateTable('egw_jinn_mail_data',array(
		 'fd' => array(
			'id' => array('type' => 'auto','nullable' => False),
			'subject' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'body_text' => array('type' => 'text','nullable' => False),
			'body_html' => array('type' => 'text','nullable' => False),
			'attachments' => array('type' => 'text','nullable' => False),
			'reply_address' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'reply_name' => array('type' => 'varchar','precision' => '255','nullable' => False),
			'email_type' => array('type' => 'varchar','precision' => '10','nullable' => False)
		 ),
		 'pk' => array('id'),
		 'fk' => array(),
		 'ix' => array(),
		 'uc' => array()
	  ));


	  $GLOBALS['setup_info']['jinn']['currentver'] = '0.6.005';
	  return $GLOBALS['setup_info']['jinn']['currentver'];
   }



	$test[] = '0.6.005';
	function jinn_upgrade0_6_005()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('egw_jinn_mail_list','email_table','email_object_id');

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.6.006';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	}


	$test[] = '0.6.006';
	function jinn_upgrade0_6_006()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('egw_jinn_mail_data','list_id',array(
			'type' => 'varchar',
			'precision' => '255'
		));

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.6.007';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	}


	$test[] = '0.6.007';
	function jinn_upgrade0_6_007()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_jinn_sites',array(
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
				'dev_upload_url' => array('type' => 'varchar','precision' => '250','nullable' => False),
				'website_url' => array('type' => 'varchar','precision' => '250','nullable' => False)
			),
			'pk' => array('site_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),'upload_url');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_jinn_sites',array(
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
				'website_url' => array('type' => 'varchar','precision' => '250','nullable' => False)
			),
			'pk' => array('site_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),'dev_upload_url');

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.6.008';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	}


	$test[] = '0.6.008';
	function jinn_upgrade0_6_008()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_jinn_site_objects',array(
			'fd' => array(
				'object_id' => array('type' => 'auto','nullable' => False),
				'parent_site_id' => array('type' => 'int','precision' => '4'),
				'name' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'table_name' => array('type' => 'varchar','precision' => '30'),
				'upload_path' => array('type' => 'varchar','precision' => '250','nullable' => False),
				'relations' => array('type' => 'text'),
				'plugins' => array('type' => 'text'),
				'help_information' => array('type' => 'text'),
				'dev_upload_url' => array('type' => 'varchar','precision' => '255'),
				'dev_upload_path' => array('type' => 'varchar','precision' => '255'),
				'max_records' => array('type' => 'int','precision' => '4')
			),
			'pk' => array('object_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),'upload_url');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_jinn_site_objects',array(
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
				'max_records' => array('type' => 'int','precision' => '4')
			),
			'pk' => array('object_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),'dev_upload_url');

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.6.009';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	}


	$test[] = '0.6.009';
	function jinn_upgrade0_6_009()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_sites','last_edit_date',array(
			'type' => 'timestamp'
		));

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.6.010';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	}


	$test[] = '0.6.010';
	function jinn_upgrade0_6_010()
	{
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_jinn_site_objects','last_edit_date',array(
			'type' => 'timestamp'
		));

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.6.011';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	}


	$test[] = '0.6.011';
	function jinn_upgrade0_6_011()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_jinn_sites','last_edit_date',array(
			'type' => 'int',
			'precision' => '4'
		));

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.6.012';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	}

	$test[] = '0.6.012';
	function jinn_upgrade0_6_012()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_jinn_site_objects','last_edit_date',array(
			'type' => 'int',
			'precision' => '4'
		));

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.6.013';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	}


	$test[] = '0.6.013';
	function jinn_upgrade0_6_013()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_jinn_sites','last_edit_date','serialnumber');

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.7.000';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	}

	$test[] = '0.7.000';
	function jinn_upgrade0_7_000()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_jinn_site_objects','last_edit_date','serialnumber');

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.7.001';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	}

	$test[] = '0.7.001';
	function jinn_upgrade0_7_001()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_jinn_adv_field_conf',array(
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
		));

		$GLOBALS['setup_info']['jinn']['currentver'] = '0.7.002';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	 }

	 $test[] = '0.7.002';
	 function jinn_upgrade0_7_002()
	 {
		$GLOBALS['phpgw_setup']->oProc->DropTable('egw_jinn_mail_data');
		$GLOBALS['phpgw_setup']->oProc->DropTable('egw_jinn_mail_list');
		
		$GLOBALS['setup_info']['jinn']['currentver'] = '0.7.003';
		return $GLOBALS['setup_info']['jinn']['currentver'];
	 }



	 
?>

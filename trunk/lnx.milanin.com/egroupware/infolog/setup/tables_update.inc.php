<?php
  /**************************************************************************\
  * eGroupWare - Setup                                                     *
  * http://www.egroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: tables_update.inc.php,v 1.10.2.1 2004/08/28 14:36:27 ralfbecker Exp $ */

	$test[] = '0.9.11';
	function infolog_upgrade0_9_11()
	{
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_infolog','info_datecreated','info_datemodified');
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_infolog','info_event_id',array(
			'type' => 'int',
			'precision' => '4',
			'default' => '0',
			'nullable' => False
		));


		$GLOBALS['setup_info']['infolog']['currentver'] = '0.9.15.001';
		return $GLOBALS['setup_info']['infolog']['currentver'];
	}


	$test[] = '0.9.15.001';
	function infolog_upgrade0_9_15_001()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_links',array(
			'fd' => array(
				'link_id' => array('type' => 'auto','nullable' => False),
				'link_app1' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'link_id1' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'link_app2' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'link_id2' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'link_remark' => array('type' => 'varchar','precision' => '50','nullable' => True),
				'link_lastmod' => array('type' => 'int','precision' => '4','nullable' => False),
				'link_owner' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('link_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));


		$GLOBALS['setup_info']['infolog']['currentver'] = '0.9.15.002';
		return $GLOBALS['setup_info']['infolog']['currentver'];
	}


	$test[] = '0.9.15.002';
	function infolog_upgrade0_9_15_002()
	{
		//echo "<p>infolog_upgrade0_9_15_002</p>\n";
		$insert = 'INSERT INTO phpgw_links (link_app1,link_id1,link_app2,link_id2,link_remark,link_lastmod,link_owner) ';
		$select = "SELECT 'infolog',info_id,'addressbook',info_addr_id,info_from,info_datemodified,info_owner FROM phpgw_infolog WHERE info_addr_id != 0";
		//echo "<p>copying address-links: $insert.$select</p>\n";
		$GLOBALS['phpgw_setup']->oProc->query($insert.$select);
		$select = "SELECT 'infolog',info_id,'projects',info_proj_id,'',info_datemodified,info_owner FROM phpgw_infolog WHERE info_proj_id != 0";
		//echo "<p>copying projects-links: $insert.$select</p>\n";
		$GLOBALS['phpgw_setup']->oProc->query($insert.$select);
		$select = "SELECT 'infolog',info_id,'calendar',info_event_id,'',info_datemodified,info_owner FROM phpgw_infolog WHERE info_event_id != 0";
		//echo "<p>copying calendar-links: $insert.$select</p>\n";
		$GLOBALS['phpgw_setup']->oProc->query($insert.$select);

		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_infolog',array(
			'fd' => array(
				'info_id' => array('type' => 'auto','nullable' => False),
				'info_type' => array('type' => 'varchar','precision' => '255','default' => 'task','nullable' => False),
				'info_proj_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_from' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'info_addr' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'info_subject' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'info_des' => array('type' => 'text','nullable' => True),
				'info_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'info_responsible' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_access' => array('type' => 'varchar','precision' => '10','nullable' => True,'default' => 'public'),
				'info_cat' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_datemodified' => array('type' => 'int','precision' => '4','nullable' => False),
				'info_startdate' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_enddate' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_id_parent' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_pri' => array('type' => 'varchar','precision' => '255','nullable' => True,'default' => 'normal'),
				'info_time' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_bill_cat' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_status' => array('type' => 'varchar','precision' => '255','nullable' => True,'default' => 'done'),
				'info_confirm' => array('type' => 'varchar','precision' => '255','nullable' => True,'default' => 'not'),
				'info_event_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False)
			),
			'pk' => array('info_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),'info_addr_id');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_infolog',array(
			'fd' => array(
				'info_id' => array('type' => 'auto','nullable' => False),
				'info_type' => array('type' => 'varchar','precision' => '255','default' => 'task','nullable' => False),
				'info_from' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'info_addr' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'info_subject' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'info_des' => array('type' => 'text','nullable' => True),
				'info_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'info_responsible' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_access' => array('type' => 'varchar','precision' => '10','nullable' => True,'default' => 'public'),
				'info_cat' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_datemodified' => array('type' => 'int','precision' => '4','nullable' => False),
				'info_startdate' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_enddate' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_id_parent' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_pri' => array('type' => 'varchar','precision' => '255','nullable' => True,'default' => 'normal'),
				'info_time' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_bill_cat' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_status' => array('type' => 'varchar','precision' => '255','nullable' => True,'default' => 'done'),
				'info_confirm' => array('type' => 'varchar','precision' => '255','nullable' => True,'default' => 'not'),
				'info_event_id' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False)
			),
			'pk' => array('info_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),'info_proj_id');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_infolog',array(
			'fd' => array(
				'info_id' => array('type' => 'auto','nullable' => False),
				'info_type' => array('type' => 'varchar','precision' => '255','default' => 'task','nullable' => False),
				'info_from' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'info_addr' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'info_subject' => array('type' => 'varchar','precision' => '64','nullable' => True),
				'info_des' => array('type' => 'text','nullable' => True),
				'info_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'info_responsible' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_access' => array('type' => 'varchar','precision' => '10','nullable' => True,'default' => 'public'),
				'info_cat' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_datemodified' => array('type' => 'int','precision' => '4','nullable' => False),
				'info_startdate' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_enddate' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_id_parent' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_pri' => array('type' => 'varchar','precision' => '255','nullable' => True,'default' => 'normal'),
				'info_time' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_bill_cat' => array('type' => 'int','precision' => '4','default' => '0','nullable' => False),
				'info_status' => array('type' => 'varchar','precision' => '255','nullable' => True,'default' => 'done'),
				'info_confirm' => array('type' => 'varchar','precision' => '255','nullable' => True,'default' => 'not')
			),
			'pk' => array('info_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),'info_event_id');


		$GLOBALS['setup_info']['infolog']['currentver'] = '0.9.15.003';
		return $GLOBALS['setup_info']['infolog']['currentver'];
	}


	$test[] = '0.9.15.003';
	function infolog_upgrade0_9_15_003()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_infolog','info_type',array(
			'type' => 'varchar',
			'precision' => '10',
			'nullable' => False,
			'default' => 'task'
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_infolog','info_pri',array(
			'type' => 'varchar',
			'precision' => '10',
			'nullable' => True,
			'default' => 'normal'
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_infolog','info_status',array(
			'type' => 'varchar',
			'precision' => '10',
			'nullable' => True,
			'default' => 'done'
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_infolog','info_confirm',array(
			'type' => 'varchar',
			'precision' => '10',
			'nullable' => True,
			'default' => 'not'
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_infolog','info_modifier',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => False,
			'default' => '0'
		));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_infolog','info_link_id',array(
			'type' => 'int',
			'precision' => '4',
			'nullable' => False,
			'default' => '0'
		));

		// ORDER BY link_app2 DESC gives addressbook the highes precedens, use ASC for projects
		$GLOBALS['phpgw_setup']->oProc->query("SELECT link_id,link_id1 FROM phpgw_links WHERE link_app1='infolog' ORDER BY link_app2 DESC");
		$links = array();
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$links[$GLOBALS['phpgw_setup']->oProc->f(1)] = $GLOBALS['phpgw_setup']->oProc->f(0);
		}
		reset($links);
		while (list($info_id,$link_id) = each($links))
		{
			$GLOBALS['phpgw_setup']->oProc->query("UPDATE phpgw_infolog SET info_link_id=$link_id WHERE info_id=$info_id");
		}

		$GLOBALS['setup_info']['infolog']['currentver'] = '0.9.15.004';
		return $GLOBALS['setup_info']['infolog']['currentver'];
	}
	
	$test[] = '0.9.15.004';
	function infolog_upgrade0_9_15_004()
	{
		// this update correctes wrong escapes of ' and " in the past
		//
		$db2 = $GLOBALS['phpgw_setup']->db;	// we need a 2. result-set
		
		$to_correct = array('info_from','info_subject','info_des');
		foreach ($to_correct as $col)
		{
			$GLOBALS['phpgw_setup']->oProc->query("SELECT info_id,$col FROM phpgw_infolog WHERE $col LIKE '%\\'%' OR $col LIKE '%\"%'");
			while ($GLOBALS['phpgw_setup']->oProc->next_record())
			{
				$db2->query("UPDATE phpgw_infolog SET $col='".$db2->db_addslashes(stripslashes($GLOBALS['phpgw_setup']->oProc->f($col))).
					"' WHERE info_id=".$GLOBALS['phpgw_setup']->oProc->f('info_id'));
			}
		}
		
		$GLOBALS['setup_info']['infolog']['currentver'] = '0.9.15.005';
		return $GLOBALS['setup_info']['infolog']['currentver'];
	}


	$test[] = '0.9.15.005';
	function infolog_upgrade0_9_15_005()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_infolog_extra',array(
			'fd' => array(
				'info_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'info_extra_name' => array('type' => 'varchar','precision' => '32','nullable' => False),
				'info_extra_value' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => '')
			),
			'pk' => array('info_id','info_extra_name'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));


		$GLOBALS['setup_info']['infolog']['currentver'] = '0.9.15.006';
		return $GLOBALS['setup_info']['infolog']['currentver'];
	}


	// the following series of updates add some indices, to speedup the selects

	$test[] = '0.9.15.006';
	function infolog_upgrade0_9_15_006()
	{
		$GLOBALS['phpgw_setup']->oProc->RefreshTable('phpgw_links',array(
			'fd' => array(
				'link_id' => array('type' => 'auto','nullable' => False),
				'link_app1' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'link_id1' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'link_app2' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'link_id2' => array('type' => 'varchar','precision' => '50','nullable' => False),
				'link_remark' => array('type' => 'varchar','precision' => '50'),
				'link_lastmod' => array('type' => 'int','precision' => '4','nullable' => False),
				'link_owner' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('link_id'),
			'fk' => array(),
			'ix' => array(array('link_app1','link_id1','link_lastmod'),array('link_app2','link_id2','link_lastmod')),
			'uc' => array()
		));

		$GLOBALS['setup_info']['infolog']['currentver'] = '0.9.15.007';
		return $GLOBALS['setup_info']['infolog']['currentver'];
	}


	$test[] = '0.9.15.007';
	function infolog_upgrade0_9_15_007()
	{
		$GLOBALS['phpgw_setup']->oProc->RefreshTable('phpgw_infolog',array(
			'fd' => array(
				'info_id' => array('type' => 'auto','nullable' => False),
				'info_type' => array('type' => 'varchar','precision' => '10','nullable' => False,'default' => 'task'),
				'info_from' => array('type' => 'varchar','precision' => '64'),
				'info_addr' => array('type' => 'varchar','precision' => '64'),
				'info_subject' => array('type' => 'varchar','precision' => '64'),
				'info_des' => array('type' => 'text'),
				'info_owner' => array('type' => 'int','precision' => '4','nullable' => False),
				'info_responsible' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'info_access' => array('type' => 'varchar','precision' => '10','default' => 'public'),
				'info_cat' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'info_datemodified' => array('type' => 'int','precision' => '4','nullable' => False),
				'info_startdate' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'info_enddate' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'info_id_parent' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'info_pri' => array('type' => 'varchar','precision' => '10','default' => 'normal'),
				'info_time' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'info_bill_cat' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'info_status' => array('type' => 'varchar','precision' => '10','default' => 'done'),
				'info_confirm' => array('type' => 'varchar','precision' => '10','default' => 'not'),
				'info_modifier' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'info_link_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('info_id'),
			'fk' => array(),
			'ix' => array(array('info_owner','info_responsible','info_status','info_startdate'),array('info_id_parent','info_owner','info_responsible','info_status','info_startdate')),
			'uc' => array()
		));
		
		// we dont need to do update 0.9.15.008, as UpdateSequenze is called now by RefreshTable
		$GLOBALS['setup_info']['infolog']['currentver'] = '1.0.0';
		return $GLOBALS['setup_info']['infolog']['currentver'];
	}

	
	$test[] = '0.9.15.008';
	function infolog_upgrade0_9_15_008()
	{
		// update the sequenzes for refreshed tables (postgres only)
		$GLOBALS['phpgw_setup']->oProc->UpdateSequence('phpgw_infolog','info_id');
		$GLOBALS['phpgw_setup']->oProc->UpdateSequence('phpgw_links','link_id');

		$GLOBALS['setup_info']['infolog']['currentver'] = '1.0.0';
		return $GLOBALS['setup_info']['infolog']['currentver'];
	}


	$test[] = '1.0.0';
	function infolog_upgrade1_0_0()
	{
		// longer columns to cope with multibyte charsets
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_infolog','info_type',array(
			'type' => 'varchar',
			'precision' => '40',
			'nullable' => False,
			'default' => 'task'
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_infolog','info_from',array(
			'type' => 'varchar',
			'precision' => '255'
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_infolog','info_addr',array(
			'type' => 'varchar',
			'precision' => '255'
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_infolog','info_subject',array(
			'type' => 'varchar',
			'precision' => '255'
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_infolog','info_status',array(
			'type' => 'varchar',
			'precision' => '40',
			'default' => 'done'
		));

		$GLOBALS['setup_info']['infolog']['currentver'] = '1.0.0.001';
		return $GLOBALS['setup_info']['infolog']['currentver'];
	}
?>

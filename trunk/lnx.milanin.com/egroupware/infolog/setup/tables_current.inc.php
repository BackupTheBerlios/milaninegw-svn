<?php
  /**************************************************************************\
  * eGroupWare                                                               *
  * http://www.egroupware.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: tables_current.inc.php,v 1.14.2.1 2004/08/28 14:36:27 ralfbecker Exp $ */

	$phpgw_baseline = array(
		'phpgw_infolog' => array(
			'fd' => array(
				'info_id' => array('type' => 'auto','nullable' => False),
				'info_type' => array('type' => 'varchar','precision' => '40','nullable' => False,'default' => 'task'),
				'info_from' => array('type' => 'varchar','precision' => '255'),
				'info_addr' => array('type' => 'varchar','precision' => '255'),
				'info_subject' => array('type' => 'varchar','precision' => '255'),
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
				'info_status' => array('type' => 'varchar','precision' => '40','default' => 'done'),
				'info_confirm' => array('type' => 'varchar','precision' => '10','default' => 'not'),
				'info_modifier' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'info_link_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0')
			),
			'pk' => array('info_id'),
			'fk' => array(),
			'ix' => array(array('info_owner','info_responsible','info_status','info_startdate'),array('info_id_parent','info_owner','info_responsible','info_status','info_startdate')),
			'uc' => array()
		),
		'phpgw_links' => array(
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
		),
		'phpgw_infolog_extra' => array(
			'fd' => array(
				'info_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'info_extra_name' => array('type' => 'varchar','precision' => '32','nullable' => False),
				'info_extra_value' => array('type' => 'varchar','precision' => '255','nullable' => False,'default' => '')
			),
			'pk' => array('info_id','info_extra_name'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

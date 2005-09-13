<?php
  /**************************************************************************\
  * eGroupWare                                                             *
  * http://www.egroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: tables_baseline.inc.php,v 1.2 2004/01/25 21:10:36 reinerj Exp $ */

	$phpgw_baseline = array(
		'phpgw_infolog' => array(
			'fd' => array(
				'info_id' => array('type' => 'auto','nullable' => False),
				'info_type' => array('type' => 'varchar', 'precision' => 255,'nullable' => False,'default' => 'task'),
				'info_addr_id' => array('type' => 'int', 'precision' => 4,'nullable' => False, 'default' => 0),
				'info_proj_id' => array('type' => 'int', 'precision' => 4,'nullable' => False, 'default' => 0),
				'info_from' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'info_addr' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'info_subject' => array('type' => 'varchar', 'precision' => 64,'nullable' => True),
				'info_des' => array('type' => 'text','nullable' => True),
				'info_owner' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'info_responsible' => array('type' => 'int', 'precision' => 4,'nullable' => False, 'default' => 0),
				'info_access' => array('type' => 'varchar', 'precision' => 10,'nullable' => True,'default' => 'public'),
				'info_cat' => array('type' => 'int', 'precision' => 4,'nullable' => False, 'default' => 0),
				'info_datecreated' => array('type' => 'int', 'precision' => 4,'nullable' => False),
				'info_startdate' => array('type' => 'int', 'precision' => 4,'nullable' => False, 'default' => 0),
				'info_enddate' => array('type' => 'int', 'precision' => 4,'nullable' => False, 'default' => 0),
				'info_id_parent' => array('type' => 'int', 'precision' => 4,'nullable' => False,'default' => 0),
				'info_pri' => array('type' => 'varchar', 'precision' => 255,'nullable' => True,'default' => 'normal'),
				'info_time' => array('type' => 'int', 'precision' => 4,'nullable' => False, 'default' => 0),
				'info_bill_cat' => array('type' => 'int', 'precision' => 4,'nullable' => False, 'default' => 0),
				'info_status' => array('type' => 'varchar', 'precision' => 255,'nullable' => True,'default' => 'done'),
				'info_confirm' => array('type' => 'varchar', 'precision' => 255,'nullable' => True,'default' => 'not')
			),
			'pk' => array('info_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

?>

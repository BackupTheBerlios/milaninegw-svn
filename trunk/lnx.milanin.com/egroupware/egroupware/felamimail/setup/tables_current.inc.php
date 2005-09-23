<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: tables_current.inc.php,v 1.8 2004/06/25 14:05:14 ralfbecker Exp $ */

	$phpgw_baseline = array(
		'phpgw_felamimail_cache' => array(
			'fd' => array(
				'accountid' => array('type' => 'int','precision' => '4','nullable' => False),
				'hostname' => array('type' => 'varchar','precision' => '60','nullable' => False),
				'accountname' => array('type' => 'varchar','precision' => '25','nullable' => False),
				'foldername' => array('type' => 'varchar','precision' => '200','nullable' => False),
				'uid' => array('type' => 'int','precision' => '4','nullable' => False),
				'subject' => array('type' => 'text'),
				'striped_subject' => array('type' => 'text'),
				'sender_name' => array('type' => 'varchar','precision' => '120'),
				'sender_address' => array('type' => 'varchar','precision' => '120'),
				'to_name' => array('type' => 'varchar','precision' => '120'),
				'to_address' => array('type' => 'varchar','precision' => '120'),
				'date' => array('type' => 'int','precision' => '8'),
				'size' => array('type' => 'int','precision' => '4'),
				'attachments' => array('type' => 'varchar','precision' => '120')
			),
			'pk' => array('accountid','hostname','accountname','foldername','uid'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_felamimail_folderstatus' => array(
			'fd' => array(
				'accountid' => array('type' => 'int','precision' => '4','nullable' => False),
				'hostname' => array('type' => 'varchar','precision' => '60','nullable' => False),
				'accountname' => array('type' => 'varchar','precision' => '200','nullable' => False),
				'foldername' => array('type' => 'varchar','precision' => '200','nullable' => False),
				'messages' => array('type' => 'int','precision' => '4'),
				'recent' => array('type' => 'int','precision' => '4'),
				'unseen' => array('type' => 'int','precision' => '4'),
				'uidnext' => array('type' => 'int','precision' => '4'),
				'uidvalidity' => array('type' => 'int','precision' => '4')
			),
			'pk' => array('accountid','hostname','accountname','foldername'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'phpgw_felamimail_displayfilter' => array(
			'fd' => array(
				'accountid' => array('type' => 'int','precision' => '4','nullable' => False),
				'filter' => array('type' => 'text')
			),
			'pk' => array('accountid'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>

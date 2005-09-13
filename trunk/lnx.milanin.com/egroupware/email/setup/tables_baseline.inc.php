<?php
	/**************************************************************************\
	* Anglemail - setup files for eGroupWare - DB Table                        *
	* http://www.anglemail.org                                                 *
	* eGroupWare                                                               *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: tables_baseline.inc.php,v 1.4 2004/03/26 15:56:00 reinerj Exp $ */

	$phpgw_baseline = array(
		'phpgw_anglemail' => array(
			'fd' => array(
				'account_id' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
				'data_key' => array('type' => 'varchar', 'precision' => 255, 'nullable' => False, 'default' => ''),
				'content' => array('type' => 'blob', 'nullable' => False, 'default' => '')
			),
			'pk' => array('account_id', 'data_key'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
?>

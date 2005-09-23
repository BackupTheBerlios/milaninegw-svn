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

  /* $Id: tables_update.inc.php,v 1.2 2004/07/02 22:39:11 ralfbecker Exp $ */

	$test[] = '0.8.1';
	function polls_upgrade0_8_1()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_polls_desc','poll_title',array(
			'type' => 'varchar',
			'precision' => '120',
			'nullable' => False
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_polls_data','option_text',array(
			'type' => 'varchar',
			'precision' => '100',
			'nullable' => False
		));

		$GLOBALS['setup_info']['polls']['currentver'] = '0.9.1';
		return $GLOBALS['setup_info']['polls']['currentver'];
	}

	$test[] = '0.9.1';
	function polls_upgrade0_9_1()
	{
		$GLOBALS['setup_info']['polls']['currentver'] = '1.0.0';
		return $GLOBALS['setup_info']['polls']['currentver'];
	}
?>

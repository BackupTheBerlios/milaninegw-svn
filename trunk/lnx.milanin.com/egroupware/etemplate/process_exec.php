<?php
	/**************************************************************************\
	* eGroupWare - eTemplates - process_exec                                   *
	* http://www.egroupware.org                                                *
	* Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: process_exec.php,v 1.5 2004/01/27 16:58:15 reinerj Exp $ */

	list($app) = explode('.',$_GET['menuaction']);

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'	=> $app,
		'noheader'		=> True,
		'nonavbar'		=> True
	);
	include('../header.inc.php');

	ExecMethod('etemplate.etemplate.process_exec');

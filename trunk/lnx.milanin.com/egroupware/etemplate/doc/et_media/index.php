<?php
	/**************************************************************************\
	* eGroupWare - eTemplates - Example App et_media                           *
	* http://www.egroupware.org                                                *
	* Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php,v 1.4 2004/01/27 16:58:15 reinerj Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'	=> 'et_media',
		'noheader'	=> True,
		'nonavbar'	=> True
	);
	include('../header.inc.php');

	header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=et_media.et_media.edit'));
	$GLOBALS['phpgw_info']['flags']['nodisplay'] = True;
	exit;

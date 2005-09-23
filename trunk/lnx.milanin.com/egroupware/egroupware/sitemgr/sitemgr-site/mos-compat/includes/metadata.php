<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - SiteMgr support for Mambo Open Source templates     *
	* http://www.egroupware.org                                                *
	* Written and (c)) by RalfBecker@outdoor-training.de                       *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: metadata.php,v 1.1 2004/02/22 01:46:25 ralfbecker Exp $ */

	// This file echos the sites metadata
	global $objui;
	$objui->t->loadfile(realpath(dirname(__FILE__).'/../metadata.tpl'));
	echo $objui->t->parse();

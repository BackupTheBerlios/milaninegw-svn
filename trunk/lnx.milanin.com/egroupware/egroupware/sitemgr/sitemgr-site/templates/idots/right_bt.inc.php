<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* Copyright (c) 2004 by RalfBecker@outdoor-training.de                     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: right_bt.inc.php,v 1.3 2004/02/10 14:56:34 ralfbecker Exp $ */

class right_bt
{
	function apply_transform($title,$content)
	{
		return '
<div class="divSidebox">
	<div class="divSideboxHeader"><span>'. $title .'</span></div>
	<div class="divSideboxEntry">
		'. str_replace('&middot;','<img src="templates/idots/images/orange-ball.png" alt="+" />',$content). '</div>
</div>
<div class="sideboxSpace"></div>';
	}
}

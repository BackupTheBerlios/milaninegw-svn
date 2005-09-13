<?php
    /**************************************************************************\
    * eGroupWare                                                               *
    * http://www.egroupware.org                                                *
    * -----------------------------------------------                          *
    * This program is free software; you can redistribute it and/or modify it  *
    * under the terms of the GNU General Public License as published by the    *
    * Free Software Foundation; either version 2 of the License, or (at your   *
    * option) any later version.                                               *
    \**************************************************************************/
	/* $Id: hook_add_def_pref.inc.php,v 1.11.2.1 2004/11/06 12:15:56 ralfbecker Exp $ */

	global $pref;
	$pref->change('projects','tax','16');
	$pref->change('projects','ifont','Tahoma,Verdana,Arial,Helvetica,sans-serif');
	$pref->change('projects','mysize','1');
	$pref->change('projects','allsize','3');
?>

<?php
    /**************************************************************************\
    * eGroupWare - Stock Quotes                                                *
    * http://www.egroupware.org                                                *
    * --------------------------------------------                             *
    * This program is free software; you can redistribute it and/or modify it  *
    * under the terms of the GNU General Public License as published by the    *
    * Free Software Foundation; either version 2 of the License, or (at your   *
    * option) any later version.                                               *
    \**************************************************************************/
	/* $Id: hook_add_def_pref.inc.php,v 1.5 2004/01/27 19:26:33 reinerj Exp $ */

	global $pref;
	$pref->change('stocks','disabled','True');
	$pref->change('stocks','LNUX','VA%20Linux');
	$pref->change('stocks','RHAT','RedHat');
?>

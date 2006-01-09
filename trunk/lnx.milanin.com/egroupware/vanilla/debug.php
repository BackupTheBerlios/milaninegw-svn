<?php
/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Vanilla.
* Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Vanilla is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
*
* Description: Uses cookies to turn debugging information on and off
*/
include("library/Utility.Functions.php");

$Mode = ForceIncomingCookieString("Mode", "RELEASE");

$PageAction = ForceIncomingString("PageAction", "");
if ($PageAction == "ToggleDebug") {
	if ($Mode == "DEBUG") {
		$Mode = "RELEASE";
	} elseif ($Mode == "RELEASE") {
		$Mode = "UPGRADE";
	} else {
		$Mode = "DEBUG";
	}
	setcookie("Mode", $Mode, time()+31104000,"/");
} 

//////////////////////
// Display the page //
//////////////////////

if ($PageAction == "ToggleDebug") {
	echo("processing...
	<script>
	setTimeout(\"document.location='debug.php';\",600);
	</script>");
} else {
	echo("The application mode is currently ".$Mode.".
   <br /><a href=\"debug.php?PageAction=ToggleDebug\">Click here to switch the application mode.</a>
	<br /><br /><a href=\"./\">Click here to go to the application</a>");
}
?>

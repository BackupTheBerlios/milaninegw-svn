<?php
	/*****************************************************************************\
	* phpGroupWare - Forums                                                       *
	* http://www.phpgroupware.org                                                 *
	* Written by Jani Hirvinen <jpkh@shadownet.com>                               *
	* -------------------------------------------                                 *
	*  This program is free software; you	can redistribute it and/or modify it   *
	*  under the terms of	the GNU	General	Public License as published by the  *
	*  Free Software Foundation; either version 2	of the License,	or (at your *
	*  option) any later version.                                                 *
	\*****************************************************************************/

	/* $Id: bar.inc.php,v 1.12 2003/08/28 14:26:08 ralfbecker Exp $ */

 // Forums bar is created here

 echo "<font size=-1>";
 echo "[ <a href=" . $phpgw->link("/forum/post.php","$catfor&type=new&col=$col") . ">" . lang("New Topic") . "</a> | ";
 if(!$col) echo "<a href=" . $phpgw->link("/forum/threads.php","$catfor&col=1") . ">" . lang("View Threads") . "</a>  ";
 if($col) echo "<a href=" . $phpgw->link("/forum/threads.php","$catfor&col=0") . ">" . lang("Collapse Threads") . "</a> ";
 //echo "<a href=" . $phpgw->link("/forum/search.php","$catfor") . ">" . lang("Search") . "</a> ";

 echo "]</font><br><br>";

?>


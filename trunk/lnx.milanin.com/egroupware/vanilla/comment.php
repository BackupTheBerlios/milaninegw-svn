<?
/*
* This file is part of milaninegw Vanilla versaion.
* Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Vanilla is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
* The code of milaninegw modification is hosted on developer.berlios.de/projects/milaninegw
*
*
* Description: get a single comment or comment's body by id
*/


include("appg/settings.php");
include(sgLIBRARY."Input.Select.class.php");
include(sgLIBRARY."Input.Radio.class.php");
include(sgLIBRARY."Utility.Pagelist.class.php");
include(sgLIBRARY."Vanilla.Discussion.class.php");
include(sgLIBRARY."Vanilla.Comment.class.php");
include(sgLIBRARY."Vanilla.Category.class.php");
include(sgLIBRARY."Vanilla.Search.class.php");
include("appg/init_internal.php");

// 1. DEFINE VARIABLES AND PROPERTIES SPECIFIC TO THIS PAGE

// Ensure the user is allowed to view this page
$Context->Session->Check(agSAFE_REDIRECT);

// Instantiate data managers to be used in this page
$DiscussionManager = $Context->ObjectFactory->NewContextObject($Context, "DiscussionManager");

// Create the comment grid
$CommentID = ForceIncomingInt("CommentID", 0);
if ($CommentID>0){
  $c=$Context->ObjectFactory->NewContextObject($Context, "GetComment");
  echo $c->get_body($Context,$CommentID);
}

?>
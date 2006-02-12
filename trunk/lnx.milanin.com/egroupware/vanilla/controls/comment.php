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
class GetComment {

  function get_body(&$Context,$CommentID) {
    $this->Context=&$Context;
    $this->Name="GetComment";
    //if (!$s) 
    $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
    $s->SetMainTable("Comment", "m");
    $s->AddSelect("Body", "m");
    $s->AddJoin("Discussion","d","DiscussionID","m","DiscussionID","left join");
    $s->AddJoin("categories","c","cat_id","d","CategoryID","left join","phpgw_");
    $s->AddWhere("CommentID", $CommentID, "=");
    $s->AddWhere("c.cat_owner","(".
        implode(",",array_keys($this->Context->Session->GetVariable("UserGroups","Array"))).
        ")","IN","and","",0);
    $result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetCommentBodyById", "An error occurred while attempting to retrieve the requested comment.");
    if ($this->Context->Database->RowCount($result) == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrCommentNotFound"));
    while ($rows = $this->Context->Database->GetRow($result)) {
      $CommentBody=$rows['Body'];
    }
    return (isset($CommentBody)) ? $CommentBody : '<p><font color="red"><b>Access to comment denied, or comment not found</b></font></p>';
  }
}
?>

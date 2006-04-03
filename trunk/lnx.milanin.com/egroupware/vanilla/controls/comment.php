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

var $Name;
var $Context;

function GetComment(&$Context)
{
  $this->Name='GetComment';
  $this->Context = &$Context;
}

function get_body(&$Context,$CommentID) {
  if (!isset($_GET['FullComment']))
  {
    $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
    $s->SetMainTable("Comment", "m");
    $s->AddSelect(Array("Body","DateCreated"), "m");
    $s->AddSelect("DiscussionID","d");
    $s->AddSelect("account_firstname", "a", "AuthFullName", "concat", "' ',a.account_lastname");
    $s->AddJoin("Discussion","d","DiscussionID","m","DiscussionID","left join");
    $s->AddJoin("categories","c","cat_id","d","CategoryID","left join","phpgw_");
    $s->AddJoin("accounts", "a", "account_id", "m", "AuthUserID", "left join","phpgw_");
    $s->AddWhere("CommentID", $CommentID, "=");
    $s->AddWhere("c.cat_owner","(".
        implode(",",array_keys($this->Context->Session->GetVariable("UserGroups","Array"))).
        ")","IN","and","",0);
    $result = $this->Context->Database->Select($this->Context, $s, $this->Name,
                                               "GetCommentBodyById",
                                               "An error occurred while attempting to retrieve the requested comment.");
    if ($this->Context->Database->RowCount($result) == 0)
          $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrCommentNotFound"));
    while ($rows = $this->Context->Database->GetRow($result)) {
      $CommentBody=$rows['Body'];
      $CommentDate=$rows['DateCreated'];
      $CommentAuthor=$rows['AuthFullName'];
      $CommentDiscussionID=$rows['DiscussionID'];
    }
    if (isset($CommentBody)){
        if (isset($_GET['quote']) && $_GET['quote']=="1"){
              return '<div  class="quote"><p><em class="SmallText"><b>'.
                        $CommentAuthor." ".$this->Context->GetDefinition("Wrote")." ".TimeDiff(UnixTimestamp($CommentDate)).'</b>'.
                        '</em><br/>'.
                        $CommentBody.'</div></p><div><br/></div>';
        }else{
             return $CommentBody."<br/><input type=\"button\" onclick=\"addQuoteToCommentBody(".
                    $CommentID.")\" class=\"Button QuoteButton\" id=\"CommentQuote_".$CommentID."\"  value=\"".
                    $this->Context->GetDefinition("Quote")."\" />";
        }
    }else{
             return '<p><font color="red"><b>Access to comment denied, or comment not found</b></font></p>';
    }
  }elseif (isset($_GET['FullComment']))
{
  return $this->FullComment($CommentID);
}
}

function FullComment($CommentID)
{
  $cm = $this->Context->ObjectFactory->NewContextObject($this->Context, "CommentManager");
  $Comment=$cm->GetCommentById($CommentID,"0");
  
  $sReturn = "<a name=\"Comment_".$Comment->CommentID."\"></a>
  <a name=\"Item_\"></a>
  <div class=\"Comment \">";
  $sReturn .="<div class=\"CommentHeader\">\n";
  if ($Comment->Deleted) {
      $sReturn .= "<div class=\"ErrorContainer CommentHidden\">
                    <div class=\"Error\">".$this->Context->GetDefinition("CommentHiddenOn")." ".
                      date("F jS Y \a\\t g:ia", $Comment->DateDeleted)." ".$this->Context->GetDefinition("By").
                      " ".$Comment->DeleteUsername.".</div>
                    </div>";
  }
  $ShowIcon = 0;
  if ($this->Context->Session->User->Setting("HtmlOn", 1)) $ShowIcon = 1;
  $sReturn .= "<div onclick=\"toggleCommentBody('".
              $CommentID.
              "')\" class=\"ShowHide\" id=\"CommBodySwitcher_".$Comment->CommentID."\">".
              "Hide".
              "</div>\n";
  $sReturn .= "<div class=\"CommentAuthor".
              ($ShowIcon?" CommentAuthorWithIcon":"").
              "\">";
  if ($ShowIcon) 
      $sReturn .= "<span class=\"CommentIcon\" style=\"background-image:url('".
      (($Comment->AuthIcon!=="") ? $Comment->AuthIcon : "images/def_icon.png")."')\"></span>";
  $sReturn .= "<a href=\"account.php?u=".$Comment->AuthUserID."\">".
              $Comment->AuthFullName."</a></div>";
  if ($Comment->WhisperUserID > 0) 
  {
    $sReturn .= "<div class=\"CommentWhisper\">".$this->Context->GetDefinition("To")." ";
    if ($Comment->WhisperUserID == $this->Context->Session->UserID && 
        $Comment->AuthUserID == $this->Context->Session->UserID) 
    {
      $sReturn .= $this->Context->GetDefinition("Yourself");
    } elseif ($Comment->WhisperUserID == $this->Context->Session->UserID) {
      $sReturn .= $this->Context->GetDefinition("You");
    } else {
      $this->Context->UserManager=$this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
      $WhisperUser=$this->Context->UserManager->GetUserById($Comment->WhisperUserID);
      $sReturn .= $WhisperUser->FullName;
    }
    $sReturn .= "</div>\n";
  }
  $sReturn .= "<div class=\"CommentTime\">#".$Comment->CommentID.
              " ".TimeDiff($Comment->DateCreated);
  if ($Comment->DateEdited != "") 
      $sReturn .= " <em>".$this->Context->GetDefinition("Edited")."</em>\n";
  $sReturn .= "</div>
              <div class=\"CommentOptions\">";
  if ($this->Context->Session->UserID > 0) 
  {
    if ($this->Context->Session->User->CanViewIps) 
        $sReturn .= "<div class=\"CommentIp\">".$this->Context->GetDefinition("CommentPostedFrom").
        " ".$Comment->RemoteIp."</div>\n";
    if ($Comment->AuthUserID == $this->Context->Session->UserID || 
        $this->Context->Session->User->AdminCategories) 
    {
        $sReturn .= "<div class=\"CommentEdit\"><a href=\"post.php?CommentID=".
                    $Comment->CommentID."\">".$this->Context->GetDefinition("Edit")."</a></div>\n";
      if ($this->Context->Session->User->AdminCategories) 
        $sReturn .= "<div class=\"CommentHide\"><a href=\"javascript:ManageComment('".
                    ($Comment->Deleted?"0":"1").
                    "', '".$this->Discussion->DiscussionID."', '".$Comment->CommentID.
                    "', '".$this->Context->GetDefinition("ShowConfirm").
                    "','".$this->Context->GetDefinition("HideConfirm").
                    "');\">".$this->Context->GetDefinition($Comment->Deleted?"Show":"Hide").
                    "</a></div>\n";
    }
  }
  $sReturn .= "</div>";
  if ($Comment->AuthRoleDesc != "") 
      $sReturn .= "<div class=\"CommentNotice\">".$Comment->AuthRoleDesc."</div>";
  $sReturn .= "</div><div class=\"CommentBody\" id=\"CommentBody_".$Comment->CommentID."\">".
              $Comment->Body .
              "</div>";
  $sReturn.= "<input type=\"button\"  onclick=\"addQuoteToCommentBody(".
            $Comment->CommentID.
            ")\" class=\"Button QuoteButton\" id=\"CommentQuote_".
            $Comment->CommentID."\" value=\"".
            $this->Context->GetDefinition("Quote")."\" />" ;
  if ($Comment->WhisperUserID > 0 && 
      $Comment->WhisperUserID == $this->Context->Session->UserID) 
      $sReturn .= "<div class=\"WhisperBack\"><a href=\"Javascript:WhisperBack('".
                  $Comment->DiscussionID."', '".
                  str_replace("'", "\'", $Comment->AuthUsername).
                  "');\">".$this->Context->GetDefinition("WhisperBack").
                  "</a></div>";
  $sReturn .= "</div>";
  return $sReturn;
}

}
?>

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
* Description: Controls for comments.php
*/
function AddDiscussionOptions(&$Context, &$Panel, $Discussion) {
	if ($Context->Session->UserID > 0) {
		$Options = $Context->GetDefinition("Options");
		$Panel->AddList($Options, 1);
		$BookmarkText = $Context->GetDefinition($Discussion->Bookmarked ? "UnbookmarkThisDiscussion" : "BookmarkThisDiscussion");
		$Panel->AddListItem($Options, $BookmarkText, "javascript:SetBookmark(".$Discussion->Bookmarked.", '".$Discussion->DiscussionID."', '".$Context->GetDefinition("BookmarkText")."', '".$Context->GetDefinition("UnbookmarkThisDiscussion")."');", "", "", "id=\"SetBookmark\"");

		if ($Context->Session->User->AdminCategories) {
			$HideText = $Context->GetDefinition(($Discussion->Active?"Hide":"Unhide")."ThisDiscussion");
			$Panel->AddListItem($Options, $HideText, "javascript:DiscussionSwitch('Active', '".$Discussion->DiscussionID."', '".FlipBool($Discussion->Active)."');", "", "", "onclick=\"return confirm('".$Context->GetDefinition($Discussion->Active?"ConfirmHideDiscussion":"ConfirmUnhideDiscussion")."');\"");
			$CloseText = $Context->GetDefinition(($Discussion->Closed?"ReOpen":"Close")."ThisDiscussion");
			$Panel->AddListItem($Options, $CloseText, "javascript:DiscussionSwitch('Closed', '".$Discussion->DiscussionID."', '".FlipBool($Discussion->Closed)."');", "", "", "onclick=\"return confirm('".$Context->GetDefinition($Discussion->Closed?"ConfirmReopenDiscussion":"ConfirmCloseDiscussion")."');\"");
			$StickyText = $Context->GetDefinition("MakeThisDiscussion".($Discussion->Sticky?"Unsticky":"Sticky"));
			$Panel->AddListItem($Options, $StickyText, "javascript:DiscussionSwitch('Sticky', '".$Discussion->DiscussionID."', '".FlipBool($Discussion->Sticky)."');", "", "", "onclick=\"return confirm('".$Context->GetDefinition($Discussion->Sticky?"ConfirmUnsticky":"ConfirmSticky")."');\"");
		}
	}
}

// Displays a comment grid
class CommentGrid extends ControlCollection {
	var $PageJump;
	var $CurrentPage;
	var $Discussion;
	var $CommentData;
	var $CommentDataCount;
	var $pl;
	var $ShowForm;
	
	function CommentGrid(&$Context, $DiscussionManager, $DiscussionID) {
		$this->Context = &$Context;
		$this->CurrentPage = ForceIncomingInt("page", 1);
		
		// Load information about this discussion
		$RecordDiscussionView = 1;
		if ($this->Context->Session->UserID == 0) $RecordDiscussionView = 0;
      $this->Discussion = $DiscussionManager->GetDiscussionById($DiscussionID, $RecordDiscussionView);
		if ($this->Discussion) {
			$this->Discussion->FormatPropertiesForDisplay();
			if (!$this->Discussion->Active && !$this->Context->Session->User->AdminCategories) {
				$this->Discussion = false;
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrDiscussionNotFound"));
			}
		}
		
		if ($this->Context->WarningCollector->Count() > 0) {
			$this->CommentData = false;
			$this->CommentDataCount = 0;
		} else {
			// Load the data
			$CommentManager = $Context->ObjectFactory->NewContextObject($Context, "CommentManager");
			$this->CommentData = $CommentManager->GetCommentList(agCOMMENTS_PER_PAGE, $this->CurrentPage, $DiscussionID);
			$this->CommentDataCount = $CommentManager->GetCommentCount($DiscussionID);
		}
		
		// Set up the pagelist
		$this->pl = $this->Context->ObjectFactory->NewContextObject($this->Context, "PageList");
		$this->pl->NextText = $this->Context->GetDefinition("Next");
		$this->pl->PreviousText = $this->Context->GetDefinition("Previous");
		$this->pl->CssClass = "PageList";
		$this->pl->TotalRecords = $this->CommentDataCount;
		$this->pl->CurrentPage = $this->CurrentPage;
		$this->pl->RecordsPerPage = agCOMMENTS_PER_PAGE;
		$this->pl->PagesToDisplay = 10;
		$this->pl->PageParameterName = "page";
		$this->pl->DefineProperties();


		$this->ShowForm = 0;
		if ($this->Context->Session->UserID > 0
			&& ($this->pl->PageCount == 1 || $this->pl->PageCount == $this->CurrentPage)
			&& ((!$this->Discussion->Closed && $this->Discussion->Active)
			|| $this->Context->Session->User->AdminCategories)) $this->ShowForm = 1;
	}
	
	function Prefix() {
		if ($this->Context->WarningCollector->Count() > 0) {
			$sReturn = "<div class=\"ErrorContainer\">
				<div class=\"ErrorTitle\">".$this->Context->GetDefinition("ErrorTitle")."</div>"
				.$this->Context->WarningCollector->GetMessages()
			."</div>";
		} else {
			$PageDetails = $this->pl->GetPageDetails($this->Context);
			$PageList = $this->pl->GetNumericList();
			
			// Format the discussion information
			$this->Discussion->ForceNameSpaces();
	
			$sReturn = "<a class=\"PageJump Bottom\" href=\"#pgbottom\">".$this->Context->GetDefinition("BottomOfPage")."</a>"
				."<div class=\"Title\">";
				if (agUSE_CATEGORIES) $sReturn .= "<a href=\"./?CategoryID=".$this->Discussion->CategoryID."\">".$this->Discussion->Category."</a>:<br/> ";
				$sReturn .= DiscussionPrefix($this->Discussion)." ";
				if ($this->Discussion->WhisperUserID > 0) {
					$sReturn .= $this->Discussion->WhisperUsername.": ";
				}
				$sReturn .= $this->Discussion->Name
				."</div>"
				.$PageList
				."<div class=\"PageDetails\">".$PageDetails."</div>";
				$Comment = $this->Context->ObjectFactory->NewObject($Context, "Comment");
				$RowNumber = 0;
				$CommentID = 0;
				while ($Row = $this->Context->Database->GetRow($this->CommentData)) {
					$RowNumber++;			
					$Comment->Clear();
					$Comment->GetPropertiesFromDataSet($Row, $this->Context->Session->UserID);
					$ShowHtml = $Comment->FormatPropertiesForDisplay($this->Context);
					
					$sReturn .= "<a name=\"Comment_".$Comment->CommentID."\"></a>
						<a name=\"Item_".$RowNumber."\"></a>
						<div class=\"Comment ".$Comment->Status.($RowNumber==1?" FirstComment":"")."\">";
						$sReturn .="<div class=\"CommentHeader\">\n";
						if ($Comment->Deleted) {
							$sReturn .= "<div class=\"ErrorContainer CommentHidden\">
								<div class=\"Error\">".$this->Context->GetDefinition("CommentHiddenOn")." ".date("F jS Y \a\\t g:ia", $Comment->DateDeleted)." ".$this->Context->GetDefinition("By")." ".$Comment->DeleteUsername.".</div>
							</div>";
						}
						$ShowIcon = 0;
						if ($this->Context->Session->User->Setting("HtmlOn", 1)) $ShowIcon = 1;
						$sReturn .= "<div onclick=\"toggleCommentBody('".$Comment->CommentID."')\" class=\"ShowHide\" id=\"CommBodySwitcher_".$Comment->CommentID."\">".
                                                ( ($RowNumber>=$this->Discussion->LastViewCountComments) ? "Hide" : "Show").
                                                "</div>\n";
						$sReturn .= "<div class=\"CommentAuthor".($ShowIcon?" CommentAuthorWithIcon":"")."\">";
						if ($ShowIcon) $sReturn .= "<span class=\"CommentIcon\" style=\"background-image:url('".(($Comment->AuthIcon!=="") ? $Comment->AuthIcon : "images/def_icon.png")."')\"></span>";
						echo "<!-- icon is: [".$Comment->AuthIcon."]-->";
						$sReturn .= "<a href=\"account.php?u=".$Comment->AuthUserID."\">".$Comment->AuthFullName."</a></div>";
						if ($Comment->WhisperUserID > 0) {
							$sReturn .= "<div class=\"CommentWhisper\">".$this->Context->GetDefinition("To")." ";
							if ($Comment->WhisperUserID == $this->Context->Session->UserID && $Comment->AuthUserID == $this->Context->Session->UserID) {
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
						$sReturn .= "<div class=\"CommentTime\">".TimeDiff($Comment->DateCreated);
						if ($Comment->DateEdited != "") $sReturn .= " <em>".$this->Context->GetDefinition("Edited")."</em>\n";
					$sReturn .= "</div>
					<div class=\"CommentOptions\">";
						if ($this->Context->Session->UserID > 0) {
							if ($this->Context->Session->User->CanViewIps) $sReturn .= "<div class=\"CommentIp\">".$this->Context->GetDefinition("CommentPostedFrom")." ".$Comment->RemoteIp."</div>\n";
							if ($Comment->AuthUserID == $this->Context->Session->UserID || $this->Context->Session->User->AdminCategories) {
								if ((!$this->Discussion->Closed && $this->Discussion->Active) || $this->Context->Session->User->AdminCategories) $sReturn .= "<div class=\"CommentEdit\"><a href=\"post.php?CommentID=".$Comment->CommentID."\">".$this->Context->GetDefinition("Edit")."</a></div>\n";
								if ($this->Context->Session->User->AdminCategories) $sReturn .= "<div class=\"CommentHide\"><a href=\"javascript:ManageComment('".($Comment->Deleted?"0":"1")."', '".$this->Discussion->DiscussionID."', '".$Comment->CommentID."', '".$this->Context->GetDefinition("ShowConfirm")."', '".$this->Context->GetDefinition("HideConfirm")."');\">".$this->Context->GetDefinition($Comment->Deleted?"Show":"Hide")."</a></div>\n";
                                                        }/*
							if ($this->Context->Session->User->Setting("HtmlOn", 1) && !$Comment->Deleted) $sReturn .= "<div class=\"CommentBlockUser\"><a id=\"BlockUser_".$Comment->AuthUserID."_Comment_".$Comment->CommentID."\" href=\"javascript:BlockUser('".$Comment->AuthUserID."', '".FlipBool($Comment->AuthBlocked)."', '".$this->Context->GetDefinition("UnblockUser")."', '".$this->Context->GetDefinition("UnblockUserTitle")."', '".$this->Context->GetDefinition("BlockUser")."', '".$this->Context->GetDefinition("BlockUserTitle")."', '".$this->Context->GetDefinition("UnblockComment")."', '".$this->Context->GetDefinition("UnblockCommentTitle")."', '".$this->Context->GetDefinition("BlockComment")."', '".$this->Context->GetDefinition("BlockCommentTitle")."');\" title=\"".$this->Context->GetDefinition(GetBool(!$Comment->AuthBlocked,"BlockUserHtml","AllowUserHtml"))."\">".$this->Context->GetDefinition(GetBool(!$Comment->AuthBlocked,"BlockUser","UnblockUser"))."</a></div>";
							$sReturn .= "<div class=\"CommentBlockComment\"><a id=\"BlockComment_".$Comment->CommentID."\" href=\"javascript:BlockComment('".$Comment->CommentID."', '".$ShowHtml."', 1, false, '".$this->Context->GetDefinition("UnblockComment")."', '".$this->Context->GetDefinition("UnblockCommentTitle")."', '".$this->Context->GetDefinition("BlockComment")."', '".$this->Context->GetDefinition("BlockCommentTitle")."');\" title=\"".$this->Context->GetDefinition(GetBool($ShowHtml,"BlockHtml","AllowHtml"))."\">".$this->Context->GetDefinition(GetBool($ShowHtml,"BlockComment","UnblockComment"))."</a></div>";*/
						}
						$sReturn .= "</div>";
						if ($Comment->AuthRoleDesc != "") $sReturn .= "<div class=\"CommentNotice\">".$Comment->AuthRoleDesc."</div>";
						$sReturn .= "</div><div class=\"".
                                                ( ($RowNumber>=$this->Discussion->LastViewCountComments) ? "CommentBody" : "CommentBodyHidden").
                                                "\" id=\"CommentBody_".$Comment->CommentID."\">".
                                                ( ($RowNumber>=$this->Discussion->LastViewCountComments) ? $Comment->Body : "").
                                                "</div>";
						//id=\"Comment_".$Comment->CommentID."\">".$Comment->Body."</div>";
						if ($Comment->WhisperUserID > 0 && $Comment->WhisperUserID == $this->Context->Session->UserID) $sReturn .= "<div class=\"WhisperBack\"><a href=\"Javascript:WhisperBack('".$Comment->DiscussionID."', '".str_replace("'", "\'", $Comment->AuthUsername)."');\">".$this->Context->GetDefinition("WhisperBack")."</a></div>";
					$sReturn .= "</div>";
				}
				$sReturn .= $PageList
				."<div class=\"PageDetails\">".$PageDetails."</div>";
				if ($this->ShowForm) {
					$sReturn .= "<a name=\"addcomments\"></a>
					<div class=\"Title AddCommentsTitle\">".$this->Context->GetDefinition("AddYourComments")."</div>";
				}
		}
		return $sReturn;
	}
	
   function Render() {
      $this->Context->Writer->Write($this->Prefix());
		if ($this->Context->WarningCollector->Count() == 0) $this->RenderControls($this->Controls);
      $this->Context->Writer->Write($this->Suffix());
   }	
	
	function Suffix() {
		return "<a class=\"PageJump Top\" href=\"#pgtop\">".$this->Context->GetDefinition("TopOfPage")."</a>"
		."<a class=\"PageNav\" href=\"./\">".$this->Context->GetDefinition("BackToDiscussions")."</a>"
		."<a name=\"pgbottom\"></a>";
	}
}

?>
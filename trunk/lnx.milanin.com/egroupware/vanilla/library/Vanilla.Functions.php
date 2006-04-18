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
* Description: Utility functions specific to Vanilla
*/

function DiscussionPrefix($Discussion) {
	$Prefix = array();
	if (!$Discussion->Active && agTEXT_HIDDEN != "") $Prefix[] = agTEXT_HIDDEN;
	if ($Discussion->Sticky && agTEXT_STICKY != "") $Prefix[] = agTEXT_STICKY;
	if ($Discussion->Closed && agTEXT_CLOSED != "") $Prefix[] = agTEXT_CLOSED;
	if ($Discussion->Bookmarked && agTEXT_BOOKMARKED != "") $Prefix[] = agTEXT_BOOKMARKED;
	if ($Discussion->WhisperUserID > 0 && agTEXT_WHISPERED != "") $Prefix[] = agTEXT_WHISPERED;
	$sPrefix = implode(", ", $Prefix);
	if ($sPrefix != "") return agTEXT_PREFIX.$sPrefix.agTEXT_SUFFIX." ";
}

function GetCommentResult(&$Context, $Comment, $HighlightWords, $FirstRow = "0") {
	return "<dl class=\"SearchComment ".$Comment->Status.($FirstRow?" FirstComment":"")."\">
		<dt class=\"DataItemLabel DiscussionTopicLabel SearchCommentTopicLabel\">".$Context->GetDefinition("DiscussionTopic")."</dt>
		<dd class=\"DataItem DiscussionTopic SearchCommentTopic\"><a href=\"comments.php?DiscussionID=".$Comment->DiscussionID."\">".$Comment->Discussion."</a></dd>
		<dt class=\"ExtendedMetaItemLabel SearchCommentBodyLabel\">".$Context->GetDefinition("Comment")."</dt>
		<dd class=\"ExtendedMetaItem SearchCommentBody\"><a href=\"./comments.php?DiscussionID=".$Comment->DiscussionID."&Focus=".$Comment->CommentID."#Comment_".$Comment->CommentID."\">".HighlightTrimmedString($Comment->Body, $HighlightWords, 300)."</a></dd>
		<dt class=\"MetaItemLabel SearchCommentInformationLabel SearchCommentCategoryLabel\">".$Context->GetDefinition("Category")."</dt>
		<dd class=\"MetaItem SearchCommentInformation SearchCommentCategory\"><a href=\"./?CategoryID=".$Comment->CategoryID."\">".$Comment->Category."</a></dd>
		<dt class=\"MetaItemLabel SearchCommentInformationLabel SearchCommentAuthorLabel\">".$Context->GetDefinition("WrittenBy")."</dt>
		<dd class=\"MetaItem SearchCommentInformation SearchCommentAuthor\"><a href=\"./account.php?u=".$Comment->AuthUserID."\">".$Comment->AuthUsername."</a></dd>
		<dt class=\"MetaItemLabel SearchCommentInformationLabel SearchCommentTimeLabel\">".$Context->GetDefinition("Added")."</dt>
		<dd class=\"MetaItem SearchCommentInformation SearchCommentTime\">".TimeDiff($Comment->DateCreated,mktime())."</dd>
	</dl>\n";
}

function GetDiscussion(&$Context, $Discussion, $FirstRow = "0") {
	// Prefix the discussion name with the whispered-to username if this is a whisper
   if ($Discussion->WhisperUserID > 0) {
		$Discussion->Name = @$Discussion->WhisperUsername.": ".$Discussion->Name;
	}
	$UnreadQS = ($Context->Session->User->Settings['comments_order']=='asc') ? GetUnreadQuerystring($Discussion) : '';
	$LastQS = ($Context->Session->User->Settings['comments_order']=='asc') ? GetLastCommentQuerystring($Discussion):'';
	$sReturn = "<dl class=\"Discussion".$Discussion->Status.($FirstRow?" FirstDiscussion":"").($Discussion->CountComments == 1?" NoReplies":"").(agUSE_CATEGORIES?" Category_".$Discussion->CategoryID:"")."\">
		<dt class=\"DataItemLabel DiscussionTopicLabel\">".$Context->GetDefinition("DiscussionTopic")."</dt>
		<dd class=\"DataItem DiscussionTopic\">".DiscussionPrefix($Discussion)."<a href=\"comments.php?DiscussionID=".$Discussion->DiscussionID.($Context->Session->User->Setting("JumpToLastReadComment", 1)?$UnreadQS:"")."\">".$Discussion->Name."</a></dd>";
		if (agUSE_CATEGORIES) {
			$sReturn .= "
			<dt class=\"MetaItemLabel DiscussionInformationLabel DiscussionCategoryLabel\">".$Context->GetDefinition("Category")."</dt>
			<dd class=\"MetaItem DiscussionInformation DiscussionCategory\"><a href=\"./?CategoryID=".$Discussion->CategoryID."\">".$Discussion->Category."</a></dd>
			";
		}
		$sReturn .= "<dt class=\"MetaItemLabel DiscussionInformationLabel StarterLabel\"><a href=\"./comments.php?DiscussionID=".$Discussion->DiscussionID."#Item_1\">".$Context->GetDefinition("StartedBy")."</a></dt>
		<dd class=\"MetaItem DiscussionInformation Starter\"><a href=\"./account.php?u=".$Discussion->AuthUserID."\">".$Discussion->AuthUsername."</a></dd>
		<dt class=\"MetaItemLabel DiscussionInformationLabel CommentCountLabel\">".$Context->GetDefinition("Comments")."</dt>
		<dd class=\"MetaItem DiscussionInformation CommentCount\">".$Discussion->CountComments."</dd>
		<dt class=\"MetaItemLabel DiscussionInformationLabel LastReplierLabel\"><a href=\"./comments.php?DiscussionID=".$Discussion->DiscussionID.$LastQS."\">".$Context->GetDefinition("LastCommentBy")."</a></dt>
		<dd class=\"MetaItem DiscussionInformation LastReplier\"><a href=\"./account.php?u=".$Discussion->LastUserID."\">".$Discussion->LastUsername."</a></dd>
		<dt class=\"MetaItemLabel DiscussionInformationLabel LastActiveLabel\"><a href=\"./comments.php?DiscussionID=".$Discussion->DiscussionID.$LastQS."\">".$Context->GetDefinition("LastActive")."</a></dt>
		<dd class=\"MetaItem DiscussionInformation LastActive\">".TimeDiff($Discussion->DateLastActive,mktime())."</dd>";
		if ($Context->Session->UserID > 0) {
			$sReturn .= "<dt class=\"MetaItemLabel DiscussionInformationLabel NewCommentCountLabel".($Discussion->NewComments>0?" NewCommentsPresentLabel":"")."\"><a href=\"comments.php?DiscussionID=".$Discussion->DiscussionID.$UnreadQS."\">".$Context->GetDefinition("New")."</a></dt>
			<dd class=\"MetaItem DiscussionInformation NewCommentCount".($Discussion->NewComments>0?" NewCommentsPresent":"")."\"><a href=\"comments.php?DiscussionID=".$Discussion->DiscussionID.$UnreadQS."\">".$Discussion->NewComments."</a></dd>";
		}
	$sReturn .= "</dl>\n";
	return $sReturn;
}

function GetLastCommentQuerystring($Discussion) {
	$sReturn = "";
	$PageNumber = CalculateNumberOfPages($Discussion->CountComments, agCOMMENTS_PER_PAGE);
	$JumpToItem = $Discussion->CountComments - (($PageNumber-1) * agCOMMENTS_PER_PAGE);
	if ($JumpToItem < 0) $JumpToItem = 0;
	if ($PageNumber > 0) $sReturn = "&amp;page=".$PageNumber;
	$sReturn .= "#Item_".$JumpToItem;
	return $sReturn;
}

function GetUnreadQuerystring($Discussion) {
	$sReturn = "";
	$UnreadCommentCount = $Discussion->CountComments - $Discussion->NewComments + 1;
	$ReadCommentCount = $Discussion->CountComments - $Discussion->NewComments;
	$PageNumber = CalculateNumberOfPages($ReadCommentCount, agCOMMENTS_PER_PAGE);
	$JumpToItem = $ReadCommentCount - (($PageNumber-1) * agCOMMENTS_PER_PAGE);
	if ($JumpToItem < 0) $JumpToItem = 0;
	if ($PageNumber > 0) $sReturn = "&amp;page=".$PageNumber;
	$sReturn .= "#Item_".$JumpToItem;
	return $sReturn;
}

function HighlightTrimmedString($Haystack, $Needles, $TrimLength = "") {	
	$TrimLength = ForceInt($TrimLength, 0);
	if ($TrimLength > 0) {
		$Haystack = SliceString($Haystack, $TrimLength);
	}
	$WordsToHighlight = count($Needles);
	if ($WordsToHighlight > 0) {
		for ($i = 0; $i < $WordsToHighlight; $i++) {
			$CurrentWord = quotemeta(ForceString($Needles[$i], ""));
			if ($CurrentWord != "") $Haystack = eregi_replace($CurrentWord, "<span class=\"Highlight\">".$Needles[$i]."</span>", $Haystack);
		}
	}
	return $Haystack;
}

function ParseQueryForHighlighting(&$Context, $Query) {
	if ($Query != "") {
		$Query = eregi_replace("\"", "", $Query);
		$Query = eregi_replace(" ".$Context->GetDefinition("And")." ", " ", $Query);
		$Query = eregi_replace(" ".$Context->GetDefinition("Or")." ", " ", $Query);
		return explode(" ", $Query);
	} else {
		return array();
	}	
}

// Panel Functions

function AddAppendixToPanel(&$Context, &$Panel, $AppendixType) {
	if ($Context->Session->UserID > 0 && $Context->Session->User->Setting("ShowAppendices", 1)) {
		switch ($AppendixType) {
			case "Discussion":
				$Panel->AddString("<h2>".$Context->GetDefinition("Appendix")."</h2>
					<ul class=\"LinkedList Appendix\">
						<li class=\"Appendix NewComments\">".$Context->GetDefinition("NewComments")."</li>
						<li class=\"Appendix NoNewComments\">".$Context->GetDefinition("NoNewComments")."</li>
					</ul>");
				break;
			case "Category":
				$Panel->AddString("<h2>".$Context->GetDefinition("Appendix")."</h2>
					<ul class=\"LinkedList Appendix\">
						<li class=\"Appendix UnblockedCategory\">".$Context->GetDefinition("UnblockedCategory")."</li>
						<li class=\"Appendix BlockedCategory\">".$Context->GetDefinition("BlockedCategory")."</li>
					</ul>");
				break;
		}
		$Panel->AddString($Context->ObjectFactory->RenderControlStrings("VanillaFunctions", "AddAppendixToPanel"));
	}
}


function AddBookmarksToPanel(&$Context, &$Panel, &$DiscussionManager, $OptionalDiscussionID = "0") {
	if ($Context->Session->User->Setting("ShowBookmarks")) {
		$sReturn = "";
		$UserBookmarks = $DiscussionManager->GetBookmarkedDiscussionsByUserID($Context->Session->UserID, agPANEL_BOOKMARK_COUNT, $OptionalDiscussionID);
		$Count = $Context->Database->RowCount($UserBookmarks);
		$OtherBookmarksExist = 0;
		$ThisDiscussionIsBookmarked = 0;
		if ($Count > 0) {
			$Discussion = $Context->ObjectFactory->NewObject($Context, "Discussion");
			while ($Row = $Context->Database->GetRow($UserBookmarks)) {
				$Discussion->Clear();
				$Discussion->GetPropertiesFromDataSet($Row);
				$Discussion->FormatPropertiesForDisplay();
				if ($Discussion->DiscussionID != $OptionalDiscussionID) $OtherBookmarksExist = 1;
				if ($Discussion->DiscussionID == $OptionalDiscussionID && $Discussion->Bookmarked) $ThisDiscussionIsBookmarked = 1;
				$sReturn .= "<li id=\"Bookmark_".$Discussion->DiscussionID."\"".(($Discussion->DiscussionID == $OptionalDiscussionID && !$Discussion->Bookmarked)?" style=\"display: none;\"":"")."><a class=\"PanelLink\" href=\"comments.php?DiscussionID=".$Discussion->DiscussionID."\">".$Discussion->Name."</a>";
				if ($Discussion->NewComments > 0) $sReturn .= " <small><strong>".FormatPlural($Discussion->NewComments, "new comment", "new comments")."</strong></small>";
				$sReturn .= "</li>";
			}
			$sReturn = "<h2 id=\"BookmarkTitle\"".(($OtherBookmarksExist || $ThisDiscussionIsBookmarked)?"":" style=\"display: none;\"").">".$Context->GetDefinition("Bookmarks")."</h2>
			<ul class=\"LinkedList\" id=\"BookmarkList\"".(($OtherBookmarksExist || $ThisDiscussionIsBookmarked)?"":" style=\"display: none;\"").">"
			.$sReturn
			."</ul>";

		}
		$sReturn .= "<form name=\"frmBookmark\" action=\"\"><input type=\"hidden\" name=\"OtherBookmarksExist\" value=\"".$OtherBookmarksExist."\" /></form>";
		if ($Count >= agPANEL_BOOKMARK_COUNT) $sReturn .= "<div class=\"LinkedListFootNote\"><a href=\"./?View=YourBookmarks\">".$Context->GetDefinition("ViewAll")."</a></div>";
		$Panel->AddString($sReturn);
	} else {
		$Panel->AddString("<form name=\"frmBookmark\" action=\"\"><input type=\"hidden\" name=\"OtherBookmarksExist\" value=\"1\" /></form>");
	}
}

function AddDiscussionOptionsToPanel(&$Context, &$Panel) {
	if ($Context->Session->UserID > 0) {
		$DiscussionFilters = $Context->GetDefinition("DiscussionFilters");
		if (!$Context->Session->User->Setting("ShowBookmarks")) $Panel->AddListItem($DiscussionFilters, $Context->GetDefinition("BookmarkedDiscussions"), "./?View=Bookmarks");
		if (!$Context->Session->User->Setting("ShowRecentDiscussions")) $Panel->AddListItem($DiscussionFilters, $Context->GetDefinition("YourDiscussions"), "./?View=YourDiscussions");
	}
}

function AddDiscussionsToPanel(&$Context, &$Panel, $DataManager, $GetDataMethod, $MaxRecords, $ListTitle, $UrlAction, $PermissionRequirement) {
	if ($PermissionRequirement && $Context->Session->UserID > 0) {
		$Data = $DataManager->$GetDataMethod($Context->Session->UserID, $MaxRecords);
		$ActualRecords = $Context->Database->RowCount($Data);
		if ($ActualRecords > 0) {
			$Discussion = $Context->ObjectFactory->NewObject($Context, "Discussion");
			while ($Row = $Context->Database->GetRow($Data)) {
				$Discussion->Clear();
				$Discussion->GetPropertiesFromDataSet($Row);
				$Discussion->FormatPropertiesForDisplay();
				$Suffix = "";
				if ($Discussion->NewComments > 0) $Suffix .= $Discussion->NewComments." ".$Context->GetDefinition("New");
				$Panel->AddListItem($ListTitle, $Discussion->Name, "comments.php?DiscussionID=".$Discussion->DiscussionID, $Suffix);
			}
			if ($ActualRecords >= $MaxRecords) $Panel->AddListItem($ListTitle, $Context->GetDefinition("ShowAll"), $Context->SelfUrl."?View=".$UrlAction);
		}
   }
}

function AddGuestInfoToPanel(&$Context, &$Panel) {
   if ($Context->Session->UserID == 0) {
		$String = "<div class=\"PanelTitle\">".$Context->GetDefinition("GuestWelcomeTitle")."</div>
		<div class=\"PanelInformation\" id=\"GuestInfo\">".$Context->GetDefinition("GuestWelcomeBody")."</div>";
		$Panel->AddString($String, 1);
	}
}

function AddSearchesToPanel(&$Context, &$Panel, &$SearchManager, $MaxRecords, $AllowEdit = "0", $CssClass = "") {
	$AllowEdit = ForceBool($AllowEdit, 0);
   if ($Context->Session->UserID > 0 && $Context->Session->User->Setting("ShowSavedSearches", 1)) {
      $Data = $SearchManager->GetSearchList($MaxRecords, $Context->Session->UserID);
		if ($CssClass != "") $CssClass = " ".$CssClass;
		$SearchCount = 0;
		$String = "<h2>".$Context->GetDefinition("Searches")."</h2>";
		if ($Data) $SearchCount = $Context->Database->RowCount($Data);
		if ($SearchCount > 0) {
			if ($AllowEdit) $String .= "<input type=\"hidden\" id=\"SavedSearchCount\" value=\"".$SearchCount."\" />";

			if ($SearchCount > 0) {
				$String .= "<ul class=\"LinkedList".$CssClass."\">";
					$s = $Context->ObjectFactory->NewObject($Context, "Search");
					while ($Row = $Context->Database->GetRow($Data)) {
						$s->Clear();
						$s->GetPropertiesFromDataSet($Row);
						$s->FormatPropertiesForDisplay();
						$String .= "<li id=\"SavedSearch_".$s->SearchID."\"><a class=\"PanelLink\" href=\"search.php?SearchID=".$s->SearchID."\">".$s->Label."</a>";
						if ($AllowEdit) $String .= " (<a href=\"javascript:RemoveSearch(".$s->SearchID.");\">".$Context->GetDefinition("RemoveLower")."</a>)";
						$String .= "</li>";
					}
				$String .= "</ul>";
			}
		}
		$String .= "<p id=\"SearchSavingHelp\" ".(($SearchCount > 0) ? "style=\"display: none;\"":"").">".$Context->GetDefinition("NoSavedSearches")."</p>";
		$Panel->AddString($String);
	}
}

function AddTextModeToPanel(&$Context, &$Panel) {
	if ($Context->Session->UserID > 0 && $Context->Session->User->Setting("ShowTextToggle", 1)) {
		$Params = $Context->ObjectFactory->NewObject($Context, "Parameters");
		$Params->DefineCollection($_GET);
		$Params->Remove("PageAction");
		if ($Context->Session->User->Setting("HtmlOn", 1)) {
			$Params->Set("h", 0);
			$CurrentMode = $Context->GetDefinition("OffCaps");
			$OppositeMode = $Context->GetDefinition("On");
		} else {
			$Params->Set("h", 1);
			$CurrentMode = $Context->GetDefinition("OnCaps");
			$OppositeMode = $Context->GetDefinition("Off");
		}		
		$Panel->AddString("<div class=\"PanelInformation TextMode".$CurrentMode."\">".$Context->GetDefinition("TextOnlyModeIs")." ".$CurrentMode." (<a class=\"PanelLink\" href=\"".$Context->SelfUrl.$Params->GetQueryString()."\">".$Context->GetDefinition("Turn")." ".$OppositeMode."</a>)</div>");
	}
}
// Append a folder (or file) to an existing path (ensures the / exists)
function AppendFolder($RootPath, $FolderToAppend) {
	if (substr($RootPath, strlen($RootPath)-1, strlen($RootPath)) == "/") $RootPath = substr($RootPath, 0, strlen($RootPath) - 1);
	if (substr($FolderToAppend,0,1) == "/") $FolderToAppend = substr($FolderToAppend,1,strlen($FolderToAppend));
	return $RootPath."/".$FolderToAppend;
}
?>
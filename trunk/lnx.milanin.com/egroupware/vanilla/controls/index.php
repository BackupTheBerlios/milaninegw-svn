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
* Description: Controls for index.php
*/

// Displays a discussion grid
class DiscussionGrid extends Control {
	var $PageJump;
	var $CurrentPage;
	var $DiscussionData;
	var $DiscussionDataCount;
	
	function DiscussionGrid(&$Context, $DiscussionManager, $CategoryID, $View) {
		$DiscussionStarterUserID = 0;
		$BookmarkedDiscussionsOnly = 0;
		$PrivateDiscussionsOnly = 0;
		
		$this->Context = &$Context;
		$this->CurrentPage = ForceIncomingInt("page", 1);
		$this->View = $View;
		
		// Get the category if filtered
		$Category = false;
		if ($CategoryID > 0) {
			include_once(sgLIBRARY."Vanilla.Category.class.php");
			$cm = $this->Context->ObjectFactory->NewContextObject($this->Context, "CategoryManager");
			$Category = $cm->GetCategoryById($CategoryID);
		}
		$this->PageJump = "<a class=\"PageJump AllDiscussions\" href=\"./\">Show all discussions</a>";
		switch ($View) {
			case "Bookmarks":
				$this->Context->PageTitle = $this->Context->GetDefinition("BookmarkedDiscussions");
				$BookmarkedDiscussionsOnly = 1;
				break;
			case "YourDiscussions":
				$this->Context->PageTitle = $this->Context->GetDefinition("YourDiscussions");
				$DiscussionStarterUserID = $this->Context->Session->UserID;
				break;
			case "Private":
				$this->Context->PageTitle = $this->Context->GetDefinition("PrivateDiscussions");
				$PrivateDiscussionsOnly = 1;
				break;
			default:
				if ($Category) {
					$this->Context->PageTitle = $Category->Name." ".$this->Context->GetDefinition("Discussions");
				} else {
					if ($this->Context->Session->User->BlocksCategories) {
						$this->Context->PageTitle = $this->Context->GetDefinition("WatchedDiscussions");
					} else {
						$this->Context->PageTitle = $this->Context->GetDefinition("AllDiscussions");
					}
					$this->PageJump = "";
				}
				break;
		}
		$this->DiscussionData = $DiscussionManager->GetDiscussionList(agDISCUSSIONS_PER_PAGE, $this->CurrentPage, $CategoryID, $BookmarkedDiscussionsOnly, $PrivateDiscussionsOnly, $DiscussionStarterUserID);
		$this->DiscussionDataCount = $DiscussionManager->GetDiscussionCount($CategoryID, $BookmarkedDiscussionsOnly, $PrivateDiscussionsOnly, $DiscussionStarterUserID);
	}
	
	function Render() {
		// Set up the pagelist
		$pl = $this->Context->ObjectFactory->NewContextObject($this->Context, "PageList");
		$pl->NextText = $this->Context->GetDefinition("Next");
		$pl->PreviousText = $this->Context->GetDefinition("Previous");
		$pl->CssClass = "PageList";
		$pl->TotalRecords = $this->DiscussionDataCount;
		$pl->CurrentPage = $this->CurrentPage;
		$pl->RecordsPerPage = agDISCUSSIONS_PER_PAGE;
		$pl->PagesToDisplay = 10;
		$pl->PageParameterName = "page";
		$pl->DefineProperties();
		$PageDetails = $pl->GetPageDetails($this->Context);
		$PageList = $pl->GetNumericList();
		$this->Context->Writer->Add($this->PageJump);
		$this->Context->Writer->Add("<div class=\"Title\">".$this->Context->PageTitle."</div>");
		$this->Context->Writer->Add($PageList);
		$this->Context->Writer->Add("<div class=\"PageDetails\">".($PageDetails == 0 ? $this->Context->GetDefinition("NoDiscussionsFound") : $PageDetails)."</div>");
		$Discussion = $this->Context->ObjectFactory->NewObject($this->Context, "Discussion");
		$FirstRow = 1;
		while ($Row = $this->Context->Database->GetRow($this->DiscussionData)) {
			$Discussion->Clear();
			$Discussion->GetPropertiesFromDataSet($Row);
			$Discussion->FormatPropertiesForDisplay();
			$Discussion->ForceNameSpaces();
			$this->Context->Writer->Add(GetDiscussion($this->Context, $Discussion, $FirstRow));
			$FirstRow = 0;
		}
		if ($this->DiscussionDataCount > 0) {
			$this->Context->Writer->Add($PageList);
			$this->Context->Writer->Add("<div class=\"PageDetails\">".$pl->GetPageDetails($this->Context)."</div>");
			$this->Context->Writer->Add("<a class=\"PageJump Top\" href=\"#pgtop\">".$this->Context->GetDefinition("TopOfPage")."</a>");
		}
		$this->Context->Writer->Write();
	}	
}
?>
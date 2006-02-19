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
* Description: Controls for search.php
*/

include_once(sgLIBRARY."Vanilla.Category.class.php");
class SearchForm extends PostBackControl {
   var $FormName;				// The name of this form
   var $Search;            // A search object (contains all parameters related to the search: keywords, etc)
   var $SearchID;          // The id of the search to load
   var $Data;              // Search result data
   var $DataCount;			// The number of records returned by a search
   
	// Search form controls
   var $CategorySelect;
	var $OrderSelect;
	var $TypeRadio;
	var $RoleSelect;   
	
	function SearchForm(&$Context, $FormName = "") {
		$this->ValidActions = array("Search", "SaveSearch");
		$this->FormName = $FormName;
      $this->SearchID = ForceIncomingInt("SearchID", 0);
      $this->DataCount = 0;
		$this->Constructor($Context);
	}
	
	function LoadData(&$SearchManager) {
		$CurrentPage = ForceIncomingInt("page", 1);
		
      // Load a search object
      $this->Search = $this->Context->ObjectFactory->NewObject($this->Context, "Search");
      if ($this->SearchID > 0 && $this->PostBackAction != "SaveSearch") {
         $this->Search = $SearchManager->GetSearchById($this->SearchID);
         if (!$this->Search) {
            $this->Search = $this->Context->ObjectFactory->NewObject($this->Context, "Search");
         } else {
            $this->PostBackAction = "Search";
         }
      } else {
         $this->Search->GetPropertiesFromForm();
      }

      // Load selectors
      // Category Filter
      $cm = $this->Context->ObjectFactory->NewContextObject($this->Context, "CategoryManager");
      $CategorySet = $cm->GetCategories();
      $this->CategorySelect = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
      $this->CategorySelect->Name = "Categories";
      $this->CategorySelect->CssClass = "SearchSelect";
      $this->CategorySelect->Attributes = " id=\"CategoryFilter\"";
      $this->CategorySelect->AddOption("", $this->Context->GetDefinition("AllCategories"));
      $this->CategorySelect->AddOptionsFromDataSet($this->Context->Database, $CategorySet, "Name", "Name");
      $this->CategorySelect->SelectedID = $this->Search->Categories;

      // UserOrder
      $this->OrderSelect = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
      $this->OrderSelect->Name = "UserOrder";
      $this->OrderSelect->CssClass = "SearchSelect";
      $this->OrderSelect->Attributes = " id=\"UserOrder\"";
      $this->OrderSelect->AddOption("", $this->Context->GetDefinition("Username"));
      $this->OrderSelect->AddOption("Date", $this->Context->GetDefinition("DateLastActive"));
      $this->OrderSelect->SelectedID = $this->Search->UserOrder;

      // Type
      $this->TypeRadio = $this->Context->ObjectFactory->NewObject($this->Context, "Radio");
      $this->TypeRadio->Name = "Type";
      $this->TypeRadio->CssClass = "SearchType";
      $this->TypeRadio->Attributes = " align=\"middle\"";
      $this->TypeRadio->AddOption("Topics", $this->Context->GetDefinition("Topics"));
      $this->TypeRadio->AddOption("Comments", $this->Context->GetDefinition("Comments"));
      $this->TypeRadio->AddOption("Users", $this->Context->GetDefinition("Users"));
      $this->TypeRadio->SelectedID = $this->Search->Type;
      
      $rm = $this->Context->ObjectFactory->NewContextObject($this->Context, "RoleManager");
      $RoleSet = $rm->GetRoles();
      $this->RoleSelect = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
      $this->RoleSelect->Name = "Roles";
      $this->RoleSelect->CssClass = "SearchSelect";
      $this->RoleSelect->Attributes = " id=\"RoleFilter\"";
      $this->RoleSelect->AddOption("", $this->Context->GetDefinition("AllRoles"));
		if ($this->Context->Session->User->AdminUsers) $this->RoleSelect->AddOption("Applicant", $this->Context->GetDefinition("Applicant"));
      $this->RoleSelect->AddOptionsFromDataSet($this->Context->Database, $RoleSet, "Name", "Name");
      $this->RoleSelect->SelectedID = $this->Search->Roles;

      // Handle saving
      if ($this->PostBackAction == "SaveSearch") {
         $SearchManager->SaveSearch($this->Search);
         $this->PostBackAction = "Search";
      }
      // Handle Searching
      if ($this->PostBackAction == "Search") {
         $this->Data = false;
         // Handle searches
         if ($this->Search->Type == "Users") {
            $um = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
            $this->Data = $um->GetUserSearch($this->Search, agSEARCH_RESULTS_PER_PAGE, $CurrentPage);
            $this->Search->FormatPropertiesForDisplay();      
            
         } else if ($this->Search->Type == "Topics") {
            $tm = $this->Context->ObjectFactory->NewContextObject($this->Context, "DiscussionManager");
            $this->Data = $tm->GetDiscussionSearch(agSEARCH_RESULTS_PER_PAGE, $CurrentPage, $this->Search);
            $this->Search->FormatPropertiesForDisplay();
            
         } else if ($this->Search->Type == "Comments") {
            $mm = $this->Context->ObjectFactory->NewContextObject($this->Context, "CommentManager");
            $this->Data = $mm->GetCommentSearch(agSEARCH_RESULTS_PER_PAGE, $CurrentPage, $this->Search);
            $this->Search->FormatPropertiesForDisplay();
         }
         
         if ($this->Data) $this->DataCount = $this->Context->Database->RowCount($this->Data);
			
			$pl = $this->Context->ObjectFactory->NewContextObject($this->Context, "PageList");
			$pl->NextText = $this->Context->GetDefinition("Next");
			$pl->PreviousText = $this->Context->GetDefinition("Previous");
			$pl->Totalled = 0;
			$pl->CssClass = "PageList";
			$pl->TotalRecords = $this->DataCount;
			$pl->PageParameterName = "page";
			$pl->CurrentPage = $CurrentPage;
			$pl->RecordsPerPage = agSEARCH_RESULTS_PER_PAGE;
			$pl->PagesToDisplay = 10;
			$this->PageList = $pl->GetLiteralList();
			$this->PageDetails = $pl->GetPageDetails($this->Context, 0);
      }
	}
   
   function Render_SearchForm() {
  		$this->PostBackParams->Add("PostBackAction", "Search");

      $this->Context->Writer->Add("<div class=\"SearchForm\" id=\"SimpleSearch\">");
      $this->Render_PostBackForm("frmSearch", "get");
      $this->Context->Writer->Add("<input type=\"text\" name=\"Keywords\" value=\"".$this->Search->Keywords."\" class=\"SearchInput\" id=\"SearchKeywords\" />
         <input type=\"submit\" name=\"btnSubmit\" value=\"".$this->Context->GetDefinition("Search")."\" class=\"SearchButton\" id=\"SearchButton\" />
         <a href=\"Javascript:ShowAdvancedSearch();\" id=\"AdvancedSearchButton\">".$this->Context->GetDefinition("Advanced")."</a>
         <div class=\"SearchTypeLabel\">".$this->Context->GetDefinition("ChooseSearchType")."</div>
         ".$this->TypeRadio->Get()."
         </form>
      </div>
      <table id=\"AdvancedSearch\" class=\"AdvancedSearchTable\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"display: none;\">
         <tr>
            <td colspan=\"2\" class=\"SearchTitle\">".$this->Context->GetDefinition("DiscussionTopicSearch")."</td>
         </tr>
			<tr>
            <td class=\"SearchLabels\">");
      $this->PostBackParams->Add("Type", "Discussions");
      $this->PostBackParams->Add("Advanced", "1");
      $this->Render_PostBackForm("frmSearch", "get");
		$Colspan = "";
		//if (agUSE_CATEGORIES) $Colspan = " colspan=\"2\"";
      $this->Context->Writer->Add($this->Context->GetDefinition("FindDiscussionsContaining")."</td>");
      $this->Context->Writer->Add("<td class=\"SearchInputs\"><input type=\"text\" name=\"Keywords\" value=\"".($this->Search->Type == "Topics"?$this->Search->Query:"")."\" class=\"AdvancedSearchInput\" id=\"SearchKeywords\" /></td></tr>");
            if (agUSE_CATEGORIES){ 
             $this->Context->Writer->Add("<tr><td class=\"SearchLabels\">".$this->Context->GetDefinition("InTheCategory")."</td>");
             $this->CategorySelect->SelectedID = ($this->Search->Type == "Topics" ? $this->Search->Categories : "");
					$this->Context->Writer->Add("<td class=\"SearchInputs\">"
					.$this->CategorySelect->Get()
					."</td>");
            $this->Context->Writer->Add("</tr>");
            }
            $this->Context->Writer->Add("<tr><td class=\"SearchLabels\">".$this->Context->GetDefinition("WhereTheAuthorWas")."</td>");
            $this->Context->Writer->Add("<td class=\"SearchInputs\">
               <input type=\"text\" name=\"AuthUsername\" value=\"".($this->Search->Type == "Topics"?$this->Search->AuthUsername:"")."\" class=\"AdvancedUserInput\" id=\"UsernameKeywords\" onKeyUp=\"return stac.LoadData(this, event, 'SearchTopicsACContainer');\" onblur=\"stac.HideAutoComplete();\" autocomplete=\"off\" />
               <div id=\"SearchTopicsACContainer\" class=\"AutoCompleteContainer\" style=\"display: none;\"></div>
            </td></tr>");
            $this->Context->Writer->Add("<tr><td colspan=\"2\" class=\"SearchInputs\"><input type=\"submit\" name=\"btnSubmit\" value=\""
              .$this->Context->GetDefinition("Search")."\" class=\"SearchButton\" id=\"SearchButton\" /></form></td></tr>");
         
         $this->Context->Writer->Add("<tr>
            <td colspan=\"4\" class=\"SearchTitle\">".$this->Context->GetDefinition("DiscussionCommentSearch")."</td>
         </tr>
			<tr id=\"CommentLabels\">
            <td class=\"SearchLabels\">");
      $this->PostBackParams->Set("Type", "Comments");
      $this->Render_PostBackForm("frmSearch", "get");
      $this->Context->Writer->Add($this->Context->GetDefinition("FindCommentsContaining")."</td>");
      $this->Context->Writer->Add("<td class=\"SearchInputs\"><input type=\"text\" name=\"Keywords\" value=\"".($this->Search->Type == "Comments"?$this->Search->Query:"")."\" class=\"AdvancedSearchInput\" id=\"SearchKeywords\" /></td></tr>");
            if (agUSE_CATEGORIES) {
              $this->Context->Writer->Add("<tr><td class=\"SearchLabels\">".$this->Context->GetDefinition("InTheCategory")."</td>");
              $this->CategorySelect->SelectedID = ($this->Search->Type == "Comments" ? $this->Search->Categories : "");
					$this->Context->Writer->Add("<td class=\"SearchInputs\">"
					.$this->CategorySelect->Get()
					."</td></tr>\n");
            }
            $this->Context->Writer->Add("<tr><td class=\"SearchLabels\">".$this->Context->GetDefinition("WhereTheAuthorWas")."</td>");
            $this->Context->Writer->Add("<td class=\"SearchInputs\">
               <input type=\"text\" name=\"AuthUsername\" value=\"".($this->Search->Type == "Comments"?$this->Search->AuthUsername:"")."\" class=\"AdvancedUserInput\" id=\"UsernameKeywords\" onKeyUp=\"return scac.LoadData(this, event, 'SearchCommentsACContainer');\" onblur=\"scac.HideAutoComplete();\" autocomplete=\"off\" />
               <div id=\"SearchCommentsACContainer\" class=\"AutoCompleteContainer\" style=\"display: none;\"></div>
            </td></tr>");
            $this->Context->Writer->Add("<tr><td colspan=\"2\" class=\"SearchInputs\"><input type=\"submit\" name=\"btnSubmit\" value=\""
              .$this->Context->GetDefinition("Search")."\" class=\"SearchButton\" id=\"SearchButton\" /></form></td></tr>");
              
        
      $this->Context->Writer->Add("</table>");
   }
	
	function Render_NoPostBack() {
		$this->Render_SearchForm();
		if ($this->PostBackAction == "Search") {
			
			$this->PageDetails = "<div class=\"PageDetails\">".($this->PageDetails?($this->Context->GetDefinition("Results").$this->PageDetails):$this->Context->GetDefinition("NoResultsFound")).($this->Search->Query == ""?"":" ".$this->Context->GetDefinition("for")." <strong>".$this->Search->Query."</strong>")."</div>";

			// Set up the "save search" form
         $this->PostBackParams->Clear();
			$this->PostBackParams->Add("Type", $this->Search->Type);
			$this->PostBackParams->Add("Keywords", $this->Search->Keywords, 0);
			$this->PostBackParams->Add("SearchID", $this->Search->SearchID);
			$this->PostBackParams->Add("PostBackAction", "SaveSearch");
			$this->Context->Writer->Add("<div class=\"SearchLabelForm\">");
			if ($this->Context->Session->UserID > 0) {
				$this->Render_PostBackForm("frmLabelSearch", "post");
				$this->Context->Writer->Add("<input type=\"text\" name=\"Label\" class=\"SearchLabelInput\" value=\"".$this->Search->Label."\" maxlength=\"30\" />
					<input type=\"submit\" name=\"btnLabel\" value=\"".$this->Context->GetDefinition("SaveSearch")."\" class=\"SearchLabelButton\" />
					</form>");
			} else {
				$this->Context->Writer->Add("&nbsp;");
			}
			$this->Context->Writer->Add("</div>"
			."<div class=\"Title\">".$this->Context->GetDefinition($this->Search->Type)."</div>"
			.$this->PageList
			.$this->PageDetails);
			
			if ($this->DataCount > 0) {
				$Switch = 0;
				$FirstRow = 1;
				$Counter = 0;
				if ($this->Search->Type == "Topics") {
					$Discussion = $this->Context->ObjectFactory->NewObject($this->Context, "Discussion");
					while ($Row = $this->Context->Database->GetRow($this->Data)) {
						$Discussion->Clear();
						$Discussion->GetPropertiesFromDataSet($Row);
						$Discussion->FormatPropertiesForDisplay();
						$Discussion->ForceNameSpaces();
						if ($Counter < agSEARCH_RESULTS_PER_PAGE) $this->Context->Writer->Add(GetDiscussion($this->Context, $Discussion, $FirstRow));
						$FirstRow = 0;
						$Counter++;
					}
				} elseif ($this->Search->Type == "Comments") {
					$Comment = $this->Context->ObjectFactory->NewObject($this->Context, "Comment");
					$HighlightWords = ParseQueryForHighlighting($this->Context, $this->Search->Query);
					while ($Row = $this->Context->Database->GetRow($this->Data)) {
						$Comment->Clear();
						$Comment->GetPropertiesFromDataSet($Row, $this->Context->Session->UserID);
						$Comment->FormatPropertiesForSafeDisplay();
						if ($Counter < agSEARCH_RESULTS_PER_PAGE) $this->Context->Writer->Add(GetCommentResult($this->Context, $Comment, $HighlightWords, $FirstRow));
						$FirstRow = 0;
						$Counter++;
					}
				} else {
					$u = $this->Context->ObjectFactory->NewContextObject($this->Context, "User");
					while ($Row = $this->Context->Database->GetRow($this->Data)) {
						$Switch = ($Switch == 1?0:1);
						$u->Clear();
						$u->GetPropertiesFromDataSet($Row);
						$u->FormatPropertiesForDisplay();
						
						if ($Counter < agSEARCH_RESULTS_PER_PAGE) {
							$ShowIcon = ($u->DisplayIcon != "" && $this->Context->Session->User->Setting("HtmlOn", 1));
							$this->Context->Writer->Add("<dl class=\"User".($Switch == 1?"":"Alternate").($FirstRow?" FirstUser":"")."\">
								<dt class=\"DataItemLabel SearchUserLabel\">".$this->Context->GetDefinition("User")."</dt>
								<dd class=\"DataItem SearchUser".($ShowIcon?" SearchUserWithIcon":"")."\">");
									if ($ShowIcon) $this->Context->Writer->Add("<span class=\"SearchIcon\" style=\"background-image:url('".$u->DisplayIcon."');\"></span>");
									$this->Context->Writer->Add("<a href=\"account.php?u=".$u->UserID."\">".$u->Name."</a> (".$u->Role.")
								</dd>
								<dt class=\"MetaItemLabel SearchUserInformationLabel SearchUserAccountCreatedLabel\">".$this->Context->GetDefinition("AccountCreated")."</dt>
								<dd class=\"MetaItem SearchUserInformation SearchUserAccountCreated\">".TimeDiff($u->DateFirstVisit,mktime())."</dd>
								<dt class=\"MetaItemLabel SearchUserInformationLabel SearchUserLastActiveLabel\">".$this->Context->GetDefinition("LastActive")."</dt>
								<dd class=\"MetaItem SearchUserInformation SearchUserLastActive\">".TimeDiff($u->DateLastActive,mktime())."</dd>
								<dt class=\"MetaItemLabel SearchUserInformationLabel SearchUserVisitCountLabel\">".$this->Context->GetDefinition("VisitCount")."</dt>
								<dd class=\"MetaItem SearchUserInformation SearchUserVisitCount\">".$u->CountVisit."</dd>
								<dt class=\"MetaItemLabel SearchUserInformationLabel SearchUserDiscussionsCreatedLabel\">".$this->Context->GetDefinition("DiscussionsCreated")."</dt>
								<dd class=\"MetaItem SearchUserInformation SearchUserDiscussionsCreated\">".$u->CountDiscussions."</dd>
								<dt class=\"MetaItemLabel SearchUserInformationLabel SearchUserCommentsAddedLabel\">".$this->Context->GetDefinition("CommentsAdded")."</dt>
								<dd class=\"MetaItem SearchUserInformation SearchUserCommentsAdded\">".$u->CountComments."</dd>
							</dl>");
						}
						$FirstRow = 0;
						$Counter++;
					}
				}
			}
			if ($this->DataCount > 0) {
				$this->Context->Writer->Add($this->PageList
				.$this->PageDetails
				."<a class=\"PageJump Top\" href=\"#pgtop\">".$this->Context->GetDefinition("TopOfPage")."</a>");
			}
		}
		$this->Context->Writer->Write();
	}

	function Render_ValidPostBack() {
	}
}
?>
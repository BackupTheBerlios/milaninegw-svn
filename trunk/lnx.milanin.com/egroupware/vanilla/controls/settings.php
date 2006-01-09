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
* Description: Controls for settings.php that handle manipulating user & application settings
*/

// Options for the control panel
function AddSettingOptionsToPanel(&$Context, &$Panel) {
	$AdminOptions = $Context->GetDefinition("AdministrativeOptions");
	if ($Context->Session->User->MasterAdmin) {
		$Panel->AddListItem($AdminOptions, $Context->GetDefinition("ApplicationSettings"), "settings.php?PostBackAction=Globals");
		$Panel->AddListItem($AdminOptions, $Context->GetDefinition("ManageExtensions"), "settings.php?PostBackAction=Extensions");
		$Panel->AddListItem($AdminOptions, $Context->GetDefinition("LanguageManagement"), "settings.php?PostBackAction=LanguageChange");
	}
	if ($Context->Session->User->AdminUsers) $Panel->AddListItem($AdminOptions, $Context->GetDefinition("RoleManagement"), "settings.php?PostBackAction=Roles");
	if ($Context->Session->User->AdminCategories && agUSE_CATEGORIES) $Panel->AddListItem($AdminOptions, $Context->GetDefinition("CategoryManagement"), "settings.php?PostBackAction=Categories");
	if ($Context->Session->User->AdminUsers || $Context->Session->User->MasterAdmin) $Panel->AddListItem($AdminOptions, $Context->GetDefinition("RegistrationManagement"), "settings.php?PostBackAction=RegistrationChange");
	if ($Context->Session->User->AdminUsers) {
		$UserManager = $Context->ObjectFactory->NewContextObject($Context, "UserManager");
		$ApplicantCount = $UserManager->GetApplicantCount();
		$Panel->AddListItem($AdminOptions, $Context->GetDefinition("MembershipApplicants"), "search.php?PostBackAction=Search&Type=Users&Keywords=roles:Applicant;sort:Date;", $ApplicantCount." ".$Context->GetDefinition("New"));
	}
}


class GlobalsForm extends PostBackControl {
	
	var $ConstantManager;

	function GlobalsForm(&$Context) {
		$this->ValidActions = array("Globals", "ProcessGlobals");
		$this->Constructor($Context);
		if (!$this->Context->Session->User->MasterAdmin) {
			$this->IsPostBack = 0;
		} elseif ($this->IsPostBack) {
			$ConstantsFile = agAPPLICATION_PATH."appg/settings.php";
			
			$this->ConstantManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "ConstantManager");
			if ($this->PostBackAction == "ProcessGlobals") {
				$this->ConstantManager->GetConstantsFromForm($ConstantsFile);
				// Checkboxes aren't posted back if unchecked, so make sure that they are saved properly
            $this->ConstantManager->SetConstant("agALLOW_NAME_CHANGE", ForceIncomingBool("agALLOW_NAME_CHANGE", 0), 0);
            $this->ConstantManager->SetConstant("agPUBLIC_BROWSING", ForceIncomingBool("agPUBLIC_BROWSING", 0), 0);
            $this->ConstantManager->SetConstant("agUSE_CATEGORIES", ForceIncomingBool("agUSE_CATEGORIES", 0), 0);
            $this->ConstantManager->SetConstant("agLOG_ALL_IPS", ForceIncomingBool("agLOG_ALL_IPS", 0), 0);
				// And save everything
				if ($this->ConstantManager->SaveConstantsToFile($ConstantsFile)) {
					$this->PostBackValidated = 1;
				} else {
					$this->PostBackAction = "Globals";
				}
			} else {
				$this->ConstantManager->DefineConstantsFromFile($ConstantsFile);
			}
		}
	}
	
	function Render_ValidPostBack() {
		$this->Context->Writer->Add("<div class=\"SettingsForm\">
			<h1>".$this->Context->GetDefinition("GlobalApplicationSettings")."</h1>
			<div class=\"Form LanguageChange\">
				<div class=\"InputNote\">".$this->Context->GetDefinition("GlobalApplicationChangesSaved")."</div>
				<div class=\"FormLink\"><a href=\"./settings.php?PostBackAction=Globals\">".$this->Context->GetDefinition("ClickHereToContinue")."</a></div>
			</div>
		</div>");
	}
	
	function Render_NoPostBack() {
		if ($this->IsPostBack) {
			$this->PostBackParams->Clear();
			$this->PostBackParams->Set("PostBackAction", "ProcessGlobals");
			$this->Context->Writer->Write("<div class=\"SettingsForm\">
				<h1>".$this->Context->GetDefinition("GlobalApplicationSettings")."</h1>
				<div class=\"Form GlobalsForm\">
					".$this->Get_Warnings()."
					".$this->Get_PostBackForm("frmApplicationGlobals")."
					<h2>".$this->Context->GetDefinition("Warning")."</h2>
					<div class=\"InputNote\">
						".$this->Context->GetDefinition("GlobalApplicationSettingsNotes")."
					</div>


					<h2>".$this->Context->GetDefinition("ApplicationTitles")."</h2>
					<dl>
						<dt>".$this->Context->GetDefinition("ApplicationTitle")."</dt>
						<dd><input type=\"text\" name=\"agAPPLICATION_TITLE\" value=\"".$this->ConstantManager->GetConstant("agAPPLICATION_TITLE")."\" maxlength=\"30\" class=\"SmallInput\" /></dd>
					</dl>
					<dl>
						<dt>".$this->Context->GetDefinition("BannerTitle")."</dt>
						<dd><input type=\"text\" name=\"agBANNER_TITLE\" value=\"".$this->ConstantManager->GetConstant("agBANNER_TITLE")."\" class=\"SmallInput\" /></dd>
					</dl>
					<div class=\"InputNote\">".$this->Context->GetDefinition("ApplicationTitlesNotes")."</div>



					<h2>".$this->Context->GetDefinition("ForumOptions")."</h2>
					<div class=\"InputBlock\">
						<div class=\"CheckBox\">".GetDynamicCheckBox("agALLOW_NAME_CHANGE", 1, $this->ConstantManager->GetConstant("agALLOW_NAME_CHANGE"), "", $this->Context->GetDefinition("AllowNameChange"))."</div>
						<div class=\"CheckBox\">".GetDynamicCheckBox("agPUBLIC_BROWSING", 1, $this->ConstantManager->GetConstant("agPUBLIC_BROWSING"), "", $this->Context->GetDefinition("AllowPublicBrowsing"))."</div>
						<div class=\"CheckBox\">".GetDynamicCheckBox("agUSE_CATEGORIES", 1, $this->ConstantManager->GetConstant("agUSE_CATEGORIES"), "", $this->Context->GetDefinition("UseCategories"))."</div>
					</div>
					
					
					
					
					<h2>".$this->Context->GetDefinition("CountsTitle")."</h2>
					<dl>
						<dt>".$this->Context->GetDefinition("DiscussionsPerPage")."</dt>
						<dd>");
						$Selector = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
						$Selector->Name = "agDISCUSSIONS_PER_PAGE";
						$i = 10;
						while ($i < 101) {
							$Selector->AddOption($i, $i);
							$i += 10;
						}
						$Selector->SelectedID = $this->ConstantManager->GetConstant("agDISCUSSIONS_PER_PAGE");
						$this->Context->Writer->Write($Selector->Get()."</dd>
						<dt>".$this->Context->GetDefinition("CommentsPerPage")."</dt>
						<dd>");
						$Selector->Name = "agCOMMENTS_PER_PAGE";
						$Selector->SelectedID = $this->ConstantManager->GetConstant("agCOMMENTS_PER_PAGE");
						$this->Context->Writer->Write($Selector->Get()."</dd>
						<dt>".$this->Context->GetDefinition("SearchResultsPerPage")."</dt>
						<dd>");
						$Selector->Name = "agSEARCH_RESULTS_PER_PAGE";
						$Selector->SelectedID = $this->ConstantManager->GetConstant("agSEARCH_RESULTS_PER_PAGE");
						$this->Context->Writer->Write($Selector->Get()."</dd>
						<dt>".$this->Context->GetDefinition("MaxBookmarksInPanel")."</dt>
						<dd>");
						$Selector->Name = "agPANEL_BOOKMARK_COUNT";
						$Selector->Clear();
						for ($i = 3; $i < 11; $i++) {
							$Selector->AddOption($i, $i);
						}
						for ($i = 15; $i < 51; $i++) {
							$Selector->AddOption($i, $i);
							$i += 4;
						}
						$Selector->SelectedID = $this->ConstantManager->GetConstant("agPANEL_BOOKMARK_COUNT");
						$this->Context->Writer->Write($Selector->Get()."</dd>
						<dt>".$this->Context->GetDefinition("MaxPrivateInPanel")."</dt>
						<dd>");
						$Selector->Name = "agPANEL_PRIVATE_COUNT";
						$Selector->SelectedID = $this->ConstantManager->GetConstant("agPANEL_PRIVATE_COUNT");
						$this->Context->Writer->Write($Selector->Get()."</dd>
						<dt>".$this->Context->GetDefinition("MaxBrowsingHistoryInPanel")."</dt>
						<dd>");
						$Selector->Name = "agPANEL_HISTORY_COUNT";
						$Selector->SelectedID = $this->ConstantManager->GetConstant("agPANEL_HISTORY_COUNT");
						$this->Context->Writer->Write($Selector->Get()."</dd>
						<dt>".$this->Context->GetDefinition("MaxDiscussionsInPanel")."</dt>
						<dd>");
						$Selector->Name = "agPANEL_USERDISCUSSIONS_COUNT";
						$Selector->SelectedID = $this->ConstantManager->GetConstant("agPANEL_USERDISCUSSIONS_COUNT");
						$this->Context->Writer->Write($Selector->Get()."</dd>
						<dt>".$this->Context->GetDefinition("MaxSavedSearchesInPanel")."</dt>
						<dd>");
						$Selector->Name = "agPANEL_SEARCH_COUNT";
						$Selector->SelectedID = $this->ConstantManager->GetConstant("agPANEL_SEARCH_COUNT");
						$this->Context->Writer->Write($Selector->Get()."</dd>
					</dl>
					<div class=\"InputNote\">".$this->Context->GetDefinition("CountsNotes")."</div>



					<h2>".$this->Context->GetDefinition("SpamProtectionTitle")."</h2>
					<dl>
						<dt>".$this->Context->GetDefinition("MaxCommentLength")."</dt>
						<dd><input type=\"text\" name=\"agMAX_COMMENT_LENGTH\" value=\"".$this->ConstantManager->GetConstant("agMAX_COMMENT_LENGTH")."\" maxlength=\"255\" class=\"SmallInput\" /></dd>
					</dl>
					<div class=\"InputNote\">
						".$this->Context->GetDefinition("MaxCommentLengthNotes"));
						$Selector->Clear();
						$Selector->CssClass = "InlineSelect";
						for ($i = 1; $i < 31; $i++) {
							$Selector->AddOption($i, $i);
						}
						$Selector->Name = "agDISCUSSION_POST_THRESHOLD";
						$Selector->SelectedID = $this->ConstantManager->GetConstant("agDISCUSSION_POST_THRESHOLD");
						
						$SecondsSelector = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
						$SecondsSelector->CssClass = "InlineSelect";
						for ($i = 10; $i < 601; $i++) {
							$SecondsSelector->AddOption($i, $i);
							$i += 9;							
						}
						$SecondsSelector->Name = "agDISCUSSION_TIME_THRESHOLD";
						$SecondsSelector->SelectedID = $this->ConstantManager->GetConstant("agDISCUSSION_TIME_THRESHOLD");
						
						$this->Context->Writer->Write($this->Context->GetDefinition("MembersCannotPostMoreThan")
							.$Selector->Get()
							.$this->Context->GetDefinition("DiscussionsWithin")
							.$SecondsSelector->Get()
							.$this->Context->GetDefinition("SecondsOrAccountFrozen"));
						
						$SecondsSelector->Name = "agDISCUSSION_THRESHOLD_PUNISHMENT";
						$SecondsSelector->SelectedID = $this->ConstantManager->GetConstant("agDISCUSSION_THRESHOLD_PUNISHMENT");
						
						$this->Context->Writer->Write($SecondsSelector->Get()
							.$this->Context->GetDefinition("Seconds"));
						
						$Selector->Name = "agCOMMENT_POST_THRESHOLD";
						$Selector->SelectedID = $this->ConstantManager->GetConstant("agCOMMENT_POST_THRESHOLD");
						
						$SecondsSelector->Name = "agCOMMENT_TIME_THRESHOLD";
						$SecondsSelector->SelectedID = $this->ConstantManager->GetConstant("agCOMMENT_TIME_THRESHOLD");
						
						$this->Context->Writer->Write($this->Context->GetDefinition("MembersCannotPostMoreThan")
							.$Selector->Get()
							.$this->Context->GetDefinition("CommentsWithin")
							.$SecondsSelector->Get()
							.$this->Context->GetDefinition("SecondsOrAccountFrozen"));
						
						$SecondsSelector->Name = "agCOMMENT_THRESHOLD_PUNISHMENT";
						$SecondsSelector->SelectedID = $this->ConstantManager->GetConstant("agCOMMENT_THRESHOLD_PUNISHMENT");
						$this->Context->Writer->Write($SecondsSelector->Get()
							.$this->Context->GetDefinition("Seconds")
						."<div class=\"CheckBox\">".GetDynamicCheckBox("agLOG_ALL_IPS", 1, $this->ConstantManager->GetConstant("agLOG_ALL_IPS"), "", $this->Context->GetDefinition("LogAllIps"))."</div>
					</div>
					
					
					<h2>".$this->Context->GetDefinition("SupportContactTitle")."</h2>
					<dl>
						<dt>".$this->Context->GetDefinition("SupportName")."</dt>
						<dd><input type=\"text\" name=\"agSUPPORT_NAME\" value=\"".$this->ConstantManager->GetConstant("agSUPPORT_NAME")."\" maxlength=\"255\" class=\"SmallInput\" /></dd>
					</dl>
					<dl>
						<dt>".$this->Context->GetDefinition("SupportEmail")."</dt>
						<dd><input type=\"text\" name=\"agSUPPORT_EMAIL\" value=\"".$this->ConstantManager->GetConstant("agSUPPORT_EMAIL")."\" maxlength=\"255\" class=\"SmallInput\" /></dd>
					</dl>
					<div class=\"InputNote\">".$this->Context->GetDefinition("SupportContactNotes")."</div>
					
					
					
					<h2>".$this->Context->GetDefinition("DiscussionLabelsTitle")."</h2>
					<dl>
						<dt>".$this->Context->GetDefinition("LabelPrefix")."</dt>
						<dd><input type=\"text\" name=\"agTEXT_PREFIX\" value=\"".$this->ConstantManager->GetConstant("agTEXT_PREFIX")."\" maxlength=\"20\" class=\"SmallInput\" /></dd>
						<dt>".$this->Context->GetDefinition("LabelSuffix")."</dt>
						<dd><input type=\"text\" name=\"agTEXT_SUFFIX\" value=\"".$this->ConstantManager->GetConstant("agTEXT_SUFFIX")."\" maxlength=\"20\" class=\"SmallInput\" /></dd>
						<dt>".$this->Context->GetDefinition("WhisperLabel")."</dt>
						<dd><input type=\"text\" name=\"agTEXT_WHISPERED\" value=\"".$this->ConstantManager->GetConstant("agTEXT_WHISPERED")."\" maxlength=\"30\" class=\"SmallInput\" /></dd>
						<dt>".$this->Context->GetDefinition("StickyLabel")."</dt>
						<dd><input type=\"text\" name=\"agTEXT_STICKY\" value=\"".$this->ConstantManager->GetConstant("agTEXT_STICKY")."\" maxlength=\"30\" class=\"SmallInput\" /></dd>
						<dt>".$this->Context->GetDefinition("ClosedLabel")."</dt>
						<dd><input type=\"text\" name=\"agTEXT_CLOSED\" value=\"".$this->ConstantManager->GetConstant("agTEXT_CLOSED")."\" maxlength=\"30\" class=\"SmallInput\" /></dd>
						<dt>".$this->Context->GetDefinition("HiddenLabel")."</dt>
						<dd><input type=\"text\" name=\"agTEXT_HIDDEN\" value=\"".$this->ConstantManager->GetConstant("agTEXT_HIDDEN")."\" maxlength=\"30\" class=\"SmallInput\" /></dd>
						<dt>".$this->Context->GetDefinition("BookmarkedLabel")."</dt>
						<dd><input type=\"text\" name=\"agTEXT_BOOKMARKED\" value=\"".$this->ConstantManager->GetConstant("agTEXT_BOOKMARKED")."\" maxlength=\"30\" class=\"SmallInput\" /></dd>
					</dl>
					<div class=\"InputNote\">".$this->Context->GetDefinition("DiscussionLabelsNotes")."</div>
					
					
					<h2>".$this->Context->GetDefinition("ApplicationSettings")."</h2>
					<dl>
						<dt>".$this->Context->GetDefinition("DefaultStyleFolder")."</dt>
						<dd><input type=\"text\" name=\"agDEFAULT_STYLE\" value=\"".$this->ConstantManager->GetConstant("agDEFAULT_STYLE")."\" maxlength=\"255\" class=\"SmallInput\" /></dd>
					</dl>
					<dl>
						<dt>".$this->Context->GetDefinition("WebPathToVanilla")."</dt>
						<dd><input type=\"text\" name=\"agDOMAIN\" value=\"".$this->ConstantManager->GetConstant("agDOMAIN")."\" maxlength=\"255\" class=\"SmallInput\" /></dd>
					</dl>
					<dl>
						<dt>".$this->Context->GetDefinition("CookieDomain")."</dt>
						<dd><input type=\"text\" name=\"agCOOKIE_DOMAIN\" value=\"".$this->ConstantManager->GetConstant("agCOOKIE_DOMAIN")."\" maxlength=\"255\" class=\"SmallInput\" /></dd>
					</dl>
					<div class=\"InputNote\">".$this->Context->GetDefinition("ApplicationSettingsNotes")."</div>
					<div class=\"FormButtons\">
						<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Save")."\" class=\"Button SubmitButton\" />
						<a href=\"./settings.php\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
					</div>
					</form>
				</div>
			</div>");
		}
	}
}

// Default help text when the page is loaded
class SettingsHelp extends Control {
	
	function AdminOptions(&$Context) {
		$this->Context = &$Context;
	}
	
	function Render() {
		if ($this->PostBackAction == "") {
			$this->Context->Writer->Write("<div class=\"Title\">".$this->Context->GetDefinition("AboutSettings")."</div>
			 <div class=\"SettingsBody\">
				".$this->Context->GetDefinition("AboutSettingsNotes")."
			 </div>");
		}
	}
}

class CategoryForm extends PostBackControl {
	
	var $CategoryManager;
	var $CategoryData;
	var $CategorySelect;
	var $CategoryRoles;
	var $Category;

	function CategoryForm(&$Context) {
		$this->ValidActions = array("Categories", "ProcessCategories", "Category", "ProcessCategory", "CategoryRemove", "ProcessCategoryRemove");
		$this->Constructor($Context);
		if (!$this->Context->Session->User->AdminCategories) {
			$this->IsPostBack = 0;
		} elseif ($this->IsPostBack) {
			$CategoryID = ForceIncomingInt("CategoryID", 0);
			$ReplacementCategoryID = ForceIncomingInt("ReplacementCategoryID", 0);
			$this->CategoryManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "CategoryManager");
			
			if ($this->PostBackAction == "ProcessCategories") {
				$this->CategoryManager->SaveCategoryOrder();
				header("location: settings.php");
			} elseif ($this->PostBackAction == "ProcessCategory") {
				$this->Category = $this->Context->ObjectFactory->NewObject($this->Context, "Category");
				$this->Category->GetPropertiesFromForm($this->Context);
				if ($this->CategoryManager->SaveCategory($this->Category)) {
					header("location: settings.php?PostBackAction=Categories");
				}
			} elseif ($this->PostBackAction == "ProcessCategoryRemove") {
				if ($this->CategoryManager->RemoveCategory($CategoryID, $ReplacementCategoryID)) {
					header("location: settings.php?PostBackAction=Categories");
				}
			}
			
			if (in_array($this->PostBackAction, array("CategoryRemove", "Categories", "Category", "ProcessCategory", "ProcessCategoryRemove"))) {
				$this->CategoryData = $this->CategoryManager->GetCategories(1);
			}
			if (in_array($this->PostBackAction, array("CategoryRemove", "Category", "ProcessCategoryRemove"))) {
				$this->CategorySelect = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
				$this->CategorySelect->Name = "CategoryID";
				$this->CategorySelect->CssClass = "SmallInput";
				$this->CategorySelect->AddOption("", $this->Context->GetDefinition("Choose"));
				$this->CategorySelect->AddOptionsFromDataSet($this->Context->Database, $this->CategoryData, "CategoryID", "Name");
			}
			if (in_array($this->PostBackAction, array("Category", "ProcessCategory"))) {
				$this->CategoryRoles = $this->Context->ObjectFactory->NewObject($this->Context, "Checkbox");
				$this->CategoryRoles->Name = "CategoryRoleBlock";
				$CategoryRoleData = $this->CategoryManager->GetCategoryRoleBlocks($CategoryID);
				$this->CategoryRoles->AddOptionsFromDataSet($this->Context->Database, $CategoryRoleData, "RoleID", "Name", "Blocked", 1);
				$this->CategoryRoles->CssClass = "CheckBox";
			}
			if ($this->PostBackAction == "Category") {
				if ($CategoryID > 0) {
					$this->Category = $this->CategoryManager->GetCategoryById($CategoryID);
				} else {
					$this->Category = $this->Context->ObjectFactory->NewObject($this->Context, "Category");
				}
			}
			if (in_array($this->PostBackAction, array("ProcessCategory", "ProcessCategoryRemove"))) {
				// Show the form again with errors
				$this->PostBackAction = str_replace("Process", "", $this->PostBackAction);
			}
		}
	}
	
	function Render() {
		if ($this->IsPostBack) {
			$this->PostBackParams->Clear();
			$CategoryID = ForceIncomingInt("CategoryID", 0);
			
			if ($this->PostBackAction == "Category") {
				$this->PostBackParams->Set("PostBackAction", "ProcessCategory");
				$this->Context->Writer->Write("<div class=\"SettingsForm\">
					<h1>".$this->Context->GetDefinition("CategoryManagement")."</h1>");
					if ($CategoryID > 0) {
						$this->CategorySelect->Attributes = "onchange=\"document.location='?PostBackAction=Category&CategoryID='+this.options[this.selectedIndex].value;\"";
						$this->CategorySelect->SelectedID = $CategoryID;
						$this->Context->Writer->Write("<div class=\"Form\" id=\"Categories\">
							".$this->Get_Warnings()."
							".$this->Get_PostBackForm("frmCategory")."
							<h2>".$this->Context->GetDefinition("GetCategoryToEdit")."</h2>
							<dl>
								<dt>".$this->Context->GetDefinition("Categories")."</dt>
								<dd>".$this->CategorySelect->Get()." ".$this->Context->GetDefinition("Required")."</dd>
							</dl>
							<h2>".$this->Context->GetDefinition("ModifyCategoryDefinition")."</h2>");
					} else {
						$this->Context->Writer->Write("<div class=\"Form\" id=\"Categories\">
							".$this->Get_Warnings()."
							".$this->Get_PostBackForm("frmCategory")."
							<h2>".$this->Context->GetDefinition("DefineNewCategory")."</h2>");
					}
					$this->Context->Writer->Write("<dl>
						<dt>".$this->Context->GetDefinition("CategoryName")."</dt>
						<dd><input type=\"text\" name=\"Name\" value=\"".$this->Category->Name."\" maxlength=\"80\" class=\"SmallInput\" id=\"txtCategoryName\" /> ".$this->Context->GetDefinition("Required")."</dd>
					</dl>
					<div class=\"InputNote\">".$this->Context->GetDefinition("CategoryNameNotes")."</div>
					<dl>
						<dt>".$this->Context->GetDefinition("CategoryDescription")."</dt>
						<dd><textarea name=\"Description\" class=\"LargeTextbox\">".$this->Category->Description."</textarea></dd>
					</dl>
					<div class=\"InputNote\">".$this->Context->GetDefinition("CategoryDescriptionNotes")."</div>
					<div class=\"InputBlock\" id=\"CategoryRoles\">
						<div class=\"InputLabel\">".$this->Context->GetDefinition("Roles")."</div>
						<div class=\"InputNote\">".$this->Context->GetDefinition("RolesInCategory")."</div>
						".$this->CategoryRoles->Get()."
					</div>
					<div class=\"FormButtons\">
						<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Save")."\" class=\"Button SubmitButton\" />
						<a href=\"./settings.php?PostBackAction=Categories\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
					</div>
					</form>
					</div>
				</div>");			
				
			} elseif ($this->PostBackAction == "CategoryRemove") {
				$this->PostBackParams->Set("PostBackAction", "ProcessCategoryRemove");
				$this->CategorySelect->Attributes = "onchange=\"document.location='?PostBackAction=CategoryRemove&CategoryID='+this.options[this.selectedIndex].value;\"";
				$this->CategorySelect->SelectedID = $CategoryID;
				$this->Context->Writer->Write("<div class=\"SettingsForm\">
					<h1>".$this->Context->GetDefinition("CategoryManagement")."</h1>
					<div class=\"Form\" id=\"CategoryRemove\">
						".$this->Get_Warnings()."
						".$this->Get_PostBackForm("frmCategoryRemove")."
						<h2>".$this->Context->GetDefinition("SelectCategoryToRemove")."</h2>
						<dl>
							<dt>".$this->Context->GetDefinition("Categories")."</dt>
							<dd>".$this->CategorySelect->Get()." ".$this->Context->GetDefinition("Required")."</dd>
						</dl>");
						if ($CategoryID > 0) {
							$this->CategorySelect->Attributes = "";
							$this->CategorySelect->RemoveOption($this->CategorySelect->SelectedID);
							$this->CategorySelect->Name = "ReplacementCategoryID";
							$this->CategorySelect->SelectedID = ForceIncomingInt("ReplacementCategoryID", 0);
							$this->Context->Writer->Write("<h2>".$this->Context->GetDefinition("SelectReplacementCategory")."</h2>
							<dl>
								<dt>".$this->Context->GetDefinition("ReplacementCategory")."</dt>
								<dd>".$this->CategorySelect->Get()." ".$this->Context->GetDefinition("Required")."</dd>
							</dl>
							<div class=\"InputNote\">".$this->Context->GetDefinition("ReplacementCategoryNotes")."</div>
							<div class=\"FormButtons\">
								<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Remove")."\" class=\"Button SubmitButton\" />
								<a href=\"./settings.php?PostBackAction=Categories\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
							</div>");
						}
						$this->Context->Writer->Write("</form>
					</div>
				</div>");				
			} else {
				$this->PostBackParams->Set("PostBackAction", "ProcessCategories");
				$this->Context->Writer->Write("<div class=\"SettingsForm\">
					".$this->Get_Warnings()."
					".$this->Get_PostBackForm("frmCategorySort")."
					<h1>".$this->Context->GetDefinition("CategoryManagement")."</h1>
					<div class=\"Form\" id=\"Categories\">
						<h2>".$this->Context->GetDefinition("Categories")."</h2>
						<ul class=\"SortList\">");
							$Counter = 0;
							if ($this->CategoryData) {
								$c = $this->Context->ObjectFactory->NewObject($this->Context, "Category");
								
								while ($Row = $this->Context->Database->GetRow($this->CategoryData)) {
									$Counter++;
									$c->Clear();
									$c->GetPropertiesFromDataSet($Row);
									$c->FormatPropertiesForDisplay();
									$this->Context->Writer->Write("<li class=\"SortListItem\">
										<a class=\"SortRemove\" href=\"Javascript:ActOnItem('frmCategorySort', ".$Counter.", './settings.php?PostBackAction=CategoryRemove&CategoryID=');\"><img src=\"".$this->Context->Session->User->StyleUrl."btn.remove.gif\" height=\"13\" width=\"13\" border=\"0\" alt=\"".$this->Context->GetDefinition("Remove")."\" /></a>
										<a class=\"SortOption SortUp\" href=\"Javascript:MoveUp('frmCategorySort', ".$Counter.");\"><img src=\"".$this->Context->Session->User->StyleUrl."btn.up.gif\" height=\"13\" width=\"13\" border=\"0\" alt=\"".$this->Context->GetDefinition("Up")."\" /></a>
										<a class=\"SortOption SortDown\" href=\"Javascript:MoveDown('frmCategorySort', ".$Counter.");\"><img src=\"".$this->Context->Session->User->StyleUrl."btn.down.gif\" height=\"13\" width=\"13\" border=\"0\" alt=\"".$this->Context->GetDefinition("Down")."\" /></a>
										<a class=\"SortOption SortTop\" href=\"Javascript:MoveTop('frmCategorySort', ".$Counter.");\"><img src=\"".$this->Context->Session->User->StyleUrl."btn.top.gif\" height=\"13\" width=\"13\" border=\"0\" alt=\"".$this->Context->GetDefinition("Top")."\" /></a>
										<a class=\"SortOption SortBottom\" href=\"Javascript:MoveBottom('frmCategorySort', ".$Counter.");\"><img src=\"".$this->Context->Session->User->StyleUrl."btn.bottom.gif\" height=\"13\" width=\"13\" border=\"0\" alt=\"".$this->Context->GetDefinition("Bottom")."\" /></a>
										<a class=\"SortItem\" id=\"Slot_".$Counter."\" href=\"Javascript:ActOnItem('frmCategorySort', ".$Counter.", './settings.php?PostBackAction=Category&CategoryID=');\">".$c->Name."</a>
									</li>
									<input type=\"hidden\" name=\"Sort_".$Counter."\" value=\"".$c->CategoryID."\" />");
								}
							}
						$this->Context->Writer->Write("</ul>
						<div class=\"FormLink\"><a href=\"settings.php?PostBackAction=Category\">".$this->Context->GetDefinition("CreateNewCategory")."</a></div>
						<div class=\"FormButtons\">
							<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("SaveChanges")."\" class=\"Button SubmitButton\" />
							<a href=\"./settings.php\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
						</div>
					</div>
					<input type=\"hidden\" name=\"SortItemCount\" value=\"".$Counter."\" />
					</form>
				</div>");
			}
		}
	}
}


class RoleForm extends PostBackControl {
	
	var $RoleManager;
	var $RoleData;
	var $RoleSelect;
	var $Role;
	var $CategoryBoxes;

	function RoleForm(&$Context) {
		$this->CategoryBoxes = "";
		$this->ValidActions = array("Roles", "Role", "ProcessRole", "RoleRemove", "ProcessRoleRemove");
		$this->Constructor($Context);
		if (!$this->Context->Session->User->AdminUsers
			&& !$this->Context->Session->User->MasterAdmin) {
			$this->IsPostBack = 0;
		} elseif ($this->IsPostBack) {
			$RoleID = ForceIncomingInt("RoleID", 0);
			$ReplacementRoleID = ForceIncomingInt("ReplacementRoleID", 0);
			$this->RoleManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "RoleManager");
			
			if ($this->PostBackAction == "ProcessRole") {
				$this->Role = $this->Context->ObjectFactory->NewObject($this->Context, "Role");
				$this->Role->GetPropertiesFromForm($this->Context);
				$NewRole = $this->RoleManager->SaveRole($this->Role);
				if ($NewRole) {
					if ($RoleID == 0) {
						$IncomingCategories = ForceIncomingArray("AllowedCategoryID", array());
						$IncomingCategories[] = 0;
						// Look for incoming category role blocks to assign.
						$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
						$s->SetMainTable("Category", "c");
						$s->AddSelect("CategoryID", "c");
						$s->AddWhere("CategoryID", "(".implode(",",$IncomingCategories).")", "not in", "and", "", 0);
						$BlockedCategories = $this->Context->Database->Select($this->Context, $s, "RoleForm", "Constructor", "An error occurred while retrieving blocked categories.");
						
						while ($Row = $this->Context->Database->GetRow($BlockedCategories)) {
							$CategoryID = ForceInt($Row["CategoryID"], 0);
							if ($CategoryID > 0) {
								$s->Clear();
								$s->SetMainTable("CategoryRoleBlock", "crb");
								$s->AddFieldNameValue("CategoryID", $CategoryID);
								$s->AddFieldNameValue("RoleID", $NewRole->RoleID);
								$s->AddFieldNameValue("Blocked", 1);
								$this->Context->Database->Insert($this->Context, $s, $this->Name, "SaveCategory", "An error occurred while adding new category block definitions for this role.");
							}
						}
                  
					}
					header("location: settings.php?PostBackAction=Roles");
				}
			} elseif ($this->PostBackAction == "ProcessRoleRemove") {
				if ($this->RoleManager->RemoveRole($RoleID, $ReplacementRoleID)) {
					header("location: settings.php?PostBackAction=Roles");
				}
			}
			
			if (in_array($this->PostBackAction, array("RoleRemove", "Roles", "Role", "ProcessRole", "ProcessRoleRemove"))) {
				$this->RoleData = $this->RoleManager->GetRoles();
			}
			if (in_array($this->PostBackAction, array("RoleRemove", "Role", "ProcessRoleRemove"))) {
				$this->RoleSelect = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
				$this->RoleSelect->Name = "RoleID";
				$this->RoleSelect->CssClass = "SmallInput";
				$this->RoleSelect->AddOption("", $this->Context->GetDefinition("Choose"));
				$this->RoleSelect->AddOptionsFromDataSet($this->Context->Database, $this->RoleData, "RoleID", "Name");
			}
			if ($this->PostBackAction == "Role") {
				if ($RoleID > 0) {
					$this->Role = $this->RoleManager->GetRoleById($RoleID);
				} else {
					$this->Role = $this->Context->ObjectFactory->NewObject($this->Context, "Role");
				}
			}
			if (in_array($this->PostBackAction, array("ProcessRole", "ProcessRoleRemove"))) {
				// Show the form again with errors
				$this->PostBackAction = str_replace("Process", "", $this->PostBackAction);
			}
			
			if ($this->PostBackAction == "Role" && $RoleID == 0) {
				// Load all Categories
            $cm = $this->Context->ObjectFactory->NewContextObject($this->Context, "CategoryManager");
				$CategoryData = $cm->GetCategories();
				while ($Row = $Context->Database->GetRow($CategoryData)) {
					$this->CategoryBoxes .= "<div class=\"CheckBox\">".GetDynamicCheckBox("AllowedCategoryID[]", $Row["CategoryID"], in_array($Row["CategoryID"], ForceIncomingArray("AllowedCategoryID", array())), "", $Row["Name"])."</div>\r\n";
				}
			}
		}
	}
	
	function Render() {
		if ($this->IsPostBack) {
			$this->PostBackParams->Clear();
			$RoleID = ForceIncomingInt("RoleID", 0);
			
			if ($this->PostBackAction == "Role") {
				$this->PostBackParams->Set("PostBackAction", "ProcessRole");
				$this->Context->Writer->Write("<div class=\"SettingsForm\">
					<h1>".$this->Context->GetDefinition("RoleManagement")."</h1>");
					if ($RoleID > 0) {
						$this->RoleSelect->Attributes = "onchange=\"document.location='?PostBackAction=Role&RoleID='+this.options[this.selectedIndex].value;\"";
						$this->RoleSelect->SelectedID = $RoleID;
						$this->Context->Writer->Write("<div class=\"Form\" id=\"Roles\">
							".$this->Get_Warnings()."
							".$this->Get_PostBackForm("frmRole")."
							<h2>".$this->Context->GetDefinition("SelectRoleToEdit")."</h2>
							<dl>
								<dt>".$this->Context->GetDefinition("Roles")."</dt>
								<dd>".$this->RoleSelect->Get()." ".$this->Context->GetDefinition("Required")."</dd>
							</dl>
							<h2>".$this->Context->GetDefinition("ModifyRoleDefinition")."</h2>");
					} else {
						$this->Context->Writer->Write("<div class=\"Form\" id=\"Roles\">
							".$this->Get_Warnings()."
							".$this->Get_PostBackForm("frmRole")."
							<h2>".$this->Context->GetDefinition("DefineNewRole")."</h2>");
					}
					$this->Context->Writer->Write("<dl>
						<dt>".$this->Context->GetDefinition("RoleName")."</dt>
						<dd><input type=\"text\" name=\"Name\" value=\"".$this->Role->Name."\" maxlength=\"80\" class=\"SmallInput\" id=\"txtRoleName\" /> ".$this->Context->GetDefinition("Required")."</dd>
					</dl>
					<div class=\"InputNote\">".$this->Context->GetDefinition("RoleNameNotes")."</div>
					<dl>
						<dt>".$this->Context->GetDefinition("RoleIcon")."</dt>
						<dd><input type=\"text\" name=\"Icon\" value=\"".$this->Role->Icon."\" maxlength=\"130\" class=\"SmallInput\" id=\"txtRoleIcon\" /></dd>
					</dl>
					<div class=\"InputNote\">
						".$this->Context->GetDefinition("RoleIconNotes")."
					</div>
					<dl>
						<dt>".$this->Context->GetDefinition("RoleTagline")."</dt>
						<dd><input type=\"text\" name=\"Description\" value=\"".$this->Role->Description."\" maxlength=\"180\" class=\"SmallInput\" id=\"txtRoleDescription\" /></dd>
					</dl>
					<div class=\"InputNote\">".$this->Context->GetDefinition("RoleTaglineNotes")."</div>
					<div class=\"InputBlock\" id=\"RoleAbilities\">
						<div class=\"InputLabel\">".$this->Context->GetDefinition("RoleAbilities")."</div>
						<div class=\"InputNote\">".$this->Context->GetDefinition("RoleAbilitiesNotes")."</div>
						<div class=\"CheckBox\">".GetDynamicCheckBox("CanLogin", 1, $this->Role->CanLogin, "", $this->Context->GetDefinition("SignInAbility"))."</div>
						<div class=\"CheckBox\">".GetDynamicCheckBox("CanPostComment", 1, $this->Role->CanPostComment, "", $this->Context->GetDefinition("AddCommentsToDiscussions"))."</div>
						<div class=\"CheckBox\">".GetDynamicCheckBox("CanPostDiscussion", 1, $this->Role->CanPostDiscussion, "", $this->Context->GetDefinition("StartANewDiscussion"))."</div>
						<div class=\"CheckBox\">".GetDynamicCheckBox("CanPostHTML", 1, $this->Role->CanPostHTML, "", $this->Context->GetDefinition("HtmlAllowedInComments"))."</div>
						<div class=\"CheckBox\">".GetDynamicCheckBox("CanViewIps", 1, $this->Role->CanViewIps, "", $this->Context->GetDefinition("CanViewIpAddresses"))."</div>
						<div class=\"CheckBox\">".GetDynamicCheckBox("AdminUsers", 1, $this->Role->AdminUsers, "", $this->Context->GetDefinition("AdminForUsersAndRoles"))."</div>");
						if ($this->Context->Session->User->AdminCategories) $this->Context->Writer->Write("<div class=\"CheckBox\">".GetDynamicCheckBox("AdminCategories", 1, $this->Role->AdminCategories, "", $this->Context->GetDefinition("AdminForDiscussionsAndCategories"))."</div>");
						if ($this->Context->Session->User->MasterAdmin) $this->Context->Writer->Write("<div class=\"CheckBox\">".GetDynamicCheckBox("MasterAdmin", 1, $this->Role->MasterAdmin, "", $this->Context->GetDefinition("MasterAdministrator"))."</div>");
						if ($this->Context->Session->User->AdminCategories) $this->Context->Writer->Write("<div class=\"CheckBox\">".GetDynamicCheckBox("ShowAllWhispers", 1, $this->Role->ShowAllWhispers, "", $this->Context->GetDefinition("MakeAllWhispersVisible"))."</div>");
						// Add the option of specifying which categories this role can see if creating a new role
						if ($this->Role->RoleID == 0 && $this->CategoryBoxes != "") {
							$this->Context->Writer->Write("<div class=\"InputNote\">".$this->Context->GetDefinition("RoleCategoryNotes")."</div>"
							.$this->CategoryBoxes);
						}
					$this->Context->Writer->Write("</div>						
					<div class=\"FormButtons\">
						<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Save")."\" class=\"Button SubmitButton\" />
						<a href=\"./settings.php?PostBackAction=Roles\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
					</div>
					</form>
				</div>");			
				
			} elseif ($this->PostBackAction == "RoleRemove") {
				$this->PostBackParams->Set("PostBackAction", "ProcessRoleRemove");
				$this->RoleSelect->Attributes = "onchange=\"document.location='?PostBackAction=RoleRemove&RoleID='+this.options[this.selectedIndex].value;\"";
				$this->RoleSelect->SelectedID = $RoleID;
				$this->Context->Writer->Write("<div class=\"SettingsForm\">
					<h1>".$this->Context->GetDefinition("RoleManagement")."</h1>
					<div class=\"Form\" id=\"RoleRemove\">
						".$this->Get_Warnings()."
						".$this->Get_PostBackForm("frmRoleRemove")."
						<h2>".$this->Context->GetDefinition("SelectRoleToRemove")."</h2>
						<dl>
							<dt>".$this->Context->GetDefinition("Roles")."</dt>
							<dd>".$this->RoleSelect->Get()." ".$this->Context->GetDefinition("Required")."</dd>
						</dl>");
						if ($RoleID > 0) {
							$this->RoleSelect->Attributes = "";
							$this->RoleSelect->RemoveOption($this->RoleSelect->SelectedID);
							$this->RoleSelect->Name = "ReplacementRoleID";
							$this->RoleSelect->SelectedID = ForceIncomingInt("ReplacementRoleID", 0);
							$this->Context->Writer->Write("<h2>".$this->Context->GetDefinition("SelectReplacementRole")."</h2>
							<dl>
								<dt>".$this->Context->GetDefinition("ReplacementRole")."</dt>
								<dd>".$this->RoleSelect->Get()." ".$this->Context->GetDefinition("Required")."</dd>
							</dl>
							<div class=\"InputNote\">".$this->Context->GetDefinition("ReplacementRoleNotes")."</div>
							<div class=\"FormButtons\">
								<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Remove")."\" class=\"Button SubmitButton\" />
								<a href=\"./settings.php?PostBackAction=Roles\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
							</div>");
						}
						$this->Context->Writer->Write("</form>
					</div>
				</div>");				
			} else {
				$this->Context->Writer->Write("<div class=\"SettingsForm\">
					".$this->Get_Warnings()."
					<h1>".$this->Context->GetDefinition("RoleManagement")."</h1>
					<div class=\"Form\" id=\"Roles\">
						<h2>".$this->Context->GetDefinition("Roles")."</h2>
						<ul class=\"SortList\">");
							if ($this->RoleData) {
								$r = $this->Context->ObjectFactory->NewObject($this->Context, "Role");
								
								while ($Row = $this->Context->Database->GetRow($this->RoleData)) {
									$r->Clear();
									$r->GetPropertiesFromDataSet($Row);
									$r->FormatPropertiesForDisplay();
									$this->Context->Writer->Write("<li class=\"SortListItem\">
										<a class=\"SortRemove\" href=\"./settings.php?PostBackAction=RoleRemove&RoleID=".$r->RoleID."\"><img src=\"".$this->Context->Session->User->StyleUrl."btn.remove.gif\" height=\"13\" width=\"13\" border=\"0\" alt=\"".$this->Context->GetDefinition("Remove")."\" /></a>
										<a class=\"SortItem\" href=\"./settings.php?PostBackAction=Role&RoleID=".$r->RoleID."\">".$r->Name."</a>
									</li>");
								}
							}
						$this->Context->Writer->Write("</ul>
						<div class=\"FormLink\"><a href=\"settings.php?PostBackAction=Role\">".$this->Context->GetDefinition("CreateANewRole")."</a></div>
					</div>
				</div>");
			}
		}
	}
}

class Extension {
   var $Name;
   var $Url;
   var $Description;
   var $Version;
   var $Author;
   var $AuthorUrl;
	var $FileName;
   var $InUse;
   
   function Clear() {
      $this->Name = "";
      $this->Url = "";
      $this->Description = "";
      $this->Version = "";
      $this->Author = "";
      $this->AuthorUrl = "";
		$this->FileName = "";
      $this->InUse = 0;
   }
   
   function Extension() {
      $this->Clear();
   }
   
   function IsValid() {
      $Valid = 1;
      if ($this->Name == "") $Valid = 0;
      if ($this->Url == "") $Valid = 0;
      if ($this->Description == "") $Valid = 0;
      if ($this->Version == "") $Valid = 0;
      if ($this->Author == "") $Valid = 0;
      if ($this->AuthorUrl == "") $Valid = 0;
      return $Valid;
   }
}


class ExtensionForm extends PostBackControl {
	
   var $Extensions;
	
	function DefineExtensions() {
      // Look in the provided path for files
      $FolderHandle = @opendir(agEXTENSIONS);
      if (!$FolderHandle) {
         $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrOpenDirectoryExtensionsStart").agEXTENSIONS.$this->Context->GetDefinition("ErrOpenDirectoryExtensionsEnd"));
      } else {
         $Extension = $this->Context->ObjectFactory->NewObject($this->Context, "Extension");
         
         // Loop through each file
         while (false !== ($Item = readdir($FolderHandle))) {
            $RecordItem = true;
            if ($Item == "." || $Item == ".." || is_dir(agEXTENSIONS.$Item)) {
               // do nothing
            } else {
               // Retrieve extension properties
               $Lines = @file(agEXTENSIONS.$Item);
               if (!$Lines) {
                  $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrReadExtensionDefinition")." ".$Item);
               } else {
                  $CurrentLine = "";
                  $MaxLines = 30;
                  
                  if (count($Lines) < $MaxLines) $MaxLines = count($Lines);
                  $Extension->Clear();
						$Extension->FileName = $Item;
                  for ($i = 0; $i < $MaxLines; $i++) {
                     $CurrentLine = trim($Lines[$i]);
                     
                     $NamePos = strpos($CurrentLine, "Extension Name: ");
                     if ($NamePos !== false && $NamePos == 0) $Extension->Name = trim(substr($CurrentLine, 15));
                     $UrlPos = strpos($CurrentLine, "Extension Url: ");
                     if ($UrlPos !== false && $UrlPos == 0) $Extension->Url = trim(substr($CurrentLine, 14));
                     $DescPos = strpos($CurrentLine, "Description: ");
                     if ($DescPos !== false && $DescPos == 0) $Extension->Description = trim(substr($CurrentLine, 12));
                     $VersionPos = strpos($CurrentLine, "Version: ");
                     if ($VersionPos !== false && $VersionPos == 0) $Extension->Version = trim(substr($CurrentLine, 8));
                     $AuthPos = strpos($CurrentLine, "Author: ");
                     if ($AuthPos !== false && $AuthPos == 0) $Extension->Author = trim(substr($CurrentLine, 7));
                     $AuthUrlPos = strpos($CurrentLine, "Author Url: ");
                     if ($AuthUrlPos !== false && $AuthUrlPos == 0) $Extension->AuthorUrl = trim(substr($CurrentLine, 11));
                     
                     if ($Extension->IsValid()) {
                        // Check to see if this extension is currently being used by the system
                        $CurrentExtensions = @file(agAPPLICATION_PATH."appg/extensions.php");
                        if (!$CurrentExtensions) {
                           $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrReadFileExtensions").agAPPLICATION_PATH."extensions.php");
                        } else {
                           for ($j = 0; $j < count($CurrentExtensions); $j++) {
                              // If the extension is found, mark it as "in use"
                              if (trim($CurrentExtensions[$j]) == "include(agEXTENSIONS.\"".$Item."\");") {
                                 $Extension->InUse = 1;
                                 $j = count($CurrentExtensions);
                              }
                           }                           
                           $this->Extensions[] = $Extension;
                        }
                        // End the loop
                        $i = $MaxLines;
                     }                     
                  }
               }
            }
         }
      }
   }
	
	function ExtensionForm(&$Context) {
		$this->ValidActions = array("Extensions", "ProcessExtension");
		$this->Constructor($Context);
		if (!$this->Context->Session->User->MasterAdmin) {
			$this->IsPostBack = 0;
		} elseif ($this->IsPostBack) {
	      $this->Extensions = array();
			$this->DefineExtensions();
			if ($this->PostBackAction == "ProcessExtension") {
				$ExtensionKey = ForceIncomingInt("ExtensionKey", 0);
				// Grab that extension from the extension array
            $Extension = $this->Extensions[$ExtensionKey];
				if ($Extension) {
					// Open the extensions file for editing
               $ExtensionsFile = agAPPLICATION_PATH."appg/extensions.php";
					$CurrentExtensions = @file($ExtensionsFile);
					if (!$CurrentExtensions) {
						$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrReadFileExtensions")." ".agAPPLICATION_PATH."extensions.php");
					} else {
						// Loop through the lines
						for ($j = 0; $j < count($CurrentExtensions); $j++) {
							if ($Extension->InUse) {
								if (trim($CurrentExtensions[$j]) == "include(agEXTENSIONS.\"".$Extension->FileName."\");") {
									// If the extension is currently in use, remove it
									array_splice($CurrentExtensions, $j, 1);
									$j = count($CurrentExtensions);
								}
							} elseif (trim($CurrentExtensions[$j]) == "?>") {
								// If the extension is NOT currently in use, add it
								$CurrentExtensions[$j] = "include(agEXTENSIONS.\"".$Extension->FileName."\");\r\n";
								$CurrentExtensions[] = "?>";
								$j = count($CurrentExtensions);
							}
						}
						// Save the extensions file
						// Open for writing only.
						// Place the file pointer at the beginning of the file and truncate the file to zero length. 
						$FileHandle = @fopen($ExtensionsFile, "wb");
						$FileContents = implode("", $CurrentExtensions);
						if (!$FileHandle) {
							$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrOpenFileStart")." ".$ExtensionsFile." ".$this->Context->GetDefinition("ErrOpenFileEnd"));
						} else {
							if (!@fwrite($FileHandle, $FileContents)) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrWriteFile"));
						}
						@fclose($FileHandle);
						
						// If everything was successful, redirect back to this page
						if ($this->Context->WarningCollector->Iif()) {
							header("Location: settings.php?PostBackAction=Extensions");
							die();
						} else {
							$this->PostBackAction = "Extensions";
						}
					}
				}
			}
		}
	}
	
	function Render() {
		if ($this->IsPostBack) {
			$this->Context->Writer->Write("<div class=\"SettingsForm\">
				<h1>".$this->Context->GetDefinition("Extensions")."</h1>
				<div class=\"ExtensionsForm\">
					".$this->Get_Warnings());
					for ($i = 0; $i < count($this->Extensions); $i++) {
						$Extension = $this->Extensions[$i];
						$this->Context->Writer->Add("<h2>".$Extension->Name."</h2>
						<div class=\"InputBlock\">
							<dl>
								<dt>".$this->Context->GetDefinition("ExtensionUrl")."</dt>
								<dd>".FormatHyperlink($Extension->Url)."</dd>
								<dt>".$this->Context->GetDefinition("Description")."<dt>
								<dd>".$Extension->Description."</dd>
								<dt>".$this->Context->GetDefinition("Version")."<dt>
								<dd>".$Extension->Version."</dd>
								<dt>".$this->Context->GetDefinition("Author")."<dt>
								<dd>".FormatHyperlink($Extension->AuthorUrl,1,$Extension->Author)."</dd>
							</dl>
							<div class=\"".($Extension->InUse?"Disable":"Enable")."Extension\"><a href=\"settings.php?PostBackAction=ProcessExtension&ExtensionKey=".$i."\">".$this->Context->GetDefinition($Extension->InUse?"Disable":"Enable")."</a></div>
						</div>");
					}				
					$this->Context->Writer->Write("</div>
				</div>
			</div>");
		}
	}
}

class RegistrationForm extends PostBackControl {
	var $RoleManager;
	var $RoleSelect;
	
	function RegistrationForm (&$Context) {
		$this->ValidActions = array("ProcessRegistrationChange", "RegistrationChange");
		$this->Constructor($Context);
		if (!$this->Context->Session->User->AdminUsers
			&& !$this->Context->Session->User->MasterAdmin) {
			$this->IsPostBack = 0;
		} elseif ($this->IsPostBack) {
			$RoleID = ForceIncomingString("RoleID", "");
			if ($RoleID == "") $RoleID = agDEFAULT_ROLE;
			$this->RoleManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "RoleManager");
			$this->RoleSelect = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
			$this->RoleSelect->Name = "RoleID";
			// Add the applicant faux-role
			$this->RoleSelect->AddOption(0, $this->Context->GetDefinition("Applicant"));
			// Add all other roles
			$this->RoleSelect->AddOptionsFromDataSet($this->Context->Database, $this->RoleManager->GetRoles(), "RoleID", "Name");
			$this->RoleSelect->SelectedID = $RoleID;
			
			$ApprovedRoleID = ForceIncomingInt("ApprovedRoleID", agAPPROVAL_ROLE);
			$this->ApprovedRoleSelect = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
			$this->ApprovedRoleSelect->Name = "ApprovedRoleID";
			$this->ApprovedRoleSelect->AddOptionsFromDataSet($this->Context->Database, $this->RoleManager->GetRoles(), "RoleID", "Name");
			$this->ApprovedRoleSelect->SelectedID = $ApprovedRoleID;
			
			if ($this->PostBackAction == "ProcessRegistrationChange") {
				// Make the immediate access option default to "0" if the "default" role
            // for new members is "0" (applicant)
				$AllowImmediateAccess = 0;
				if ($RoleID > 0) {
					$Role = $this->RoleManager->GetRoleById($RoleID);
					$AllowImmediateAccess = $Role->CanLogin?"1":"0";
				}
				
				$ConstantsFile = agAPPLICATION_PATH."appg/settings.php";
				$ConstantManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "ConstantManager");
				$ConstantManager->DefineConstantsFromFile($ConstantsFile);
				// Set the constants to their new values
				$ConstantManager->SetConstant("agDEFAULT_ROLE", $RoleID);
				$ConstantManager->SetConstant("agALLOW_IMMEDIATE_ACCESS", $AllowImmediateAccess);
				$ConstantManager->SetConstant("agAPPROVAL_ROLE", $ApprovedRoleID);
				// Save the settings file
				$ConstantManager->SaveConstantsToFile($ConstantsFile);
				if ($this->Context->WarningCollector->Iif()) $this->PostBackValidated = 1;
			}
		}
	}
	
	function Render_ValidPostBack() {
		$this->Context->Writer->Add("<div class=\"SettingsForm\">
			<h1>".$this->Context->GetDefinition("RegistrationManagement")."</h1>
			<div class=\"Form RegistrationChange\">
				<div class=\"InputNote\">".$this->Context->GetDefinition("RoleChangesSaved")."</div>
				<div class=\"FormLink\"><a href=\"./settings.php?PostBackAction=RegistrationChange\">".$this->Context->GetDefinition("ClickHereToContinue")."</a></div>
			</div>
		</div>");
	}
	
	function Render_NoPostBack() {
		if ($this->IsPostBack) {
			if (!$this->Context->Session->User->AdminUsers) {
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("PermissionError"));
				$this->Context->Writer->Add("<div class=\"SettingsForm\">
						".$this->Get_Warnings()."
				</div>");				
			} else {				
				$this->PostBackParams->Set("PostBackAction", "ProcessRegistrationChange");
				$this->Context->Writer->Add("<div class=\"SettingsForm\">
					<h1>".$this->Context->GetDefinition("RegistrationManagement")."</h1>
					<div class=\"Form RegistrationChange\">
						".$this->Get_Warnings()."
						".$this->Get_PostBackForm("frmRegistrationChange")."
						<dl>
							<dt>".$this->Context->GetDefinition("NewMemberRole")."</dt>
							<dd>".$this->RoleSelect->Get()."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("NewMemberRoleNotes")."</div>
						<dl>
							<dt>".$this->Context->GetDefinition("ApprovedMemberRole")."</dt>
							<dd>".$this->ApprovedRoleSelect->Get()."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("ApprovedMemberRoleNotes")."</div>
						<div class=\"FormButtons\">
							<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Save")."\" class=\"Button SubmitButton\" />
							<a href=\"./settings.php\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
						</div>
						</form>
					</div>
				</div>");
			}
		}
	}
}

class LanguageForm extends PostBackControl {
	var $Languages;
	var $LanguageSelect;
	var $CurrentLanguageKey;
	
	function DefineLanguages() {
      // Look in the provided path for files
      $FolderHandle = @opendir(agLANGUAGES);
      if (!$FolderHandle) {
         $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrOpenDirectoryLanguagesStart").agLANGUAGES.$this->Context->GetDefinition("ErrOpenDirectoryLanguagesEnd"));
      } else {
			$this->Languages = array();
         
         // Loop through each file
         while (false !== ($Item = readdir($FolderHandle))) {
            $RecordItem = true;
            if ($Item == "." || $Item == ".." || is_dir(agLANGUAGES.$Item)) {
               // do nothing
            } else {
               // Retrieve languages names
					$FileParts = explode(".", $Item);
					$this->Languages[] = $FileParts[0];
            }
         }
      }
   }
	function LanguageForm(&$Context) {
		$this->ValidActions = array("LanguageChange", "ProcessLanguageChange");
		$this->Constructor($Context);
		if (!$this->Context->Session->User->MasterAdmin) {
			$this->IsPostBack = 0;
		} elseif ($this->IsPostBack) {
			$this->DefineLanguages();
			$this->LanguageSelect = $this->Context->ObjectFactory->NewObject($Context, "Select");
			$this->LanguageSelect->Name = "LanguageKey";
			for ($i = 0; $i < count($this->Languages); $i++) {
				$this->LanguageSelect->AddOption($i, $this->Languages[$i]);
				if ($this->Languages[$i] == $this->Context->GetDefinition("ThisLanguageName")) $this->LanguageSelect->SelectedID = $i;
			}
			if ($this->PostBackAction == "ProcessLanguageChange") {
				$LanguageKey = ForceIncomingInt("LanguageKey", 0);
				// Grab that language from the languages array
            $Language = $this->Languages[$LanguageKey];
				if ($Language) {
					// Open the language file for editing
               $LanguageFile = agAPPLICATION_PATH."appg/language.php";
					$LanguageFileContents = "<?php
/*
DO NOT EDIT THIS FILE
This file is managed by Vanilla. It is completely erased
and rebuilt when the language is defined.
*/
include(agLANGUAGES.\"".$Language.".php\");
?>";
					// Save the extensions file
					// Open for writing only.
					// Place the file pointer at the beginning of the file and truncate the file to zero length. 
					$FileHandle = @fopen($LanguageFile, "wb");
					if (!$FileHandle) {
						$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrOpenFileStart")." ".$LanguageFile." ".$this->Context->GetDefinition("ErrOpenFileEnd"));
					} else {
						if (!@fwrite($FileHandle, $LanguageFileContents)) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrWriteFile"));
					}
					@fclose($FileHandle);
					
					// If everything was successful, mark the postback as validated
					if ($this->Context->WarningCollector->Iif()) $this->PostBackValidated = 1;
				}
			}
		}
	}
	function Render_ValidPostBack() {
		$this->Context->Writer->Add("<div class=\"SettingsForm\">
			<h1>".$this->Context->GetDefinition("LanguageManagement")."</h1>
			<div class=\"Form LanguageChange\">
				<div class=\"InputNote\">".$this->Context->GetDefinition("LanguageChangesSaved")."</div>
				<div class=\"FormLink\"><a href=\"./settings.php?PostBackAction=LanguageChange\">".$this->Context->GetDefinition("ClickHereToContinue")."</a></div>
			</div>
		</div>");
	}
	
	function Render_NoPostBack() {
		if ($this->IsPostBack) {
			if (!$this->Context->Session->User->AdminUsers) {
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("PermissionError"));
				$this->Context->Writer->Add("<div class=\"SettingsForm\">
						".$this->Get_Warnings()."
				</div>");				
			} else {				
				$this->PostBackParams->Set("PostBackAction", "ProcessLanguageChange");
				$this->Context->Writer->Add("<div class=\"SettingsForm\">
					<h1>".$this->Context->GetDefinition("LanguageManagement")."</h1>
					<div class=\"Form LanguageChange\">
						".$this->Get_Warnings()."
						".$this->Get_PostBackForm("frmLanguageChange")."
						<dl>
							<dt>".$this->Context->GetDefinition("ChangeLanguage")."</dt>
							<dd>".$this->LanguageSelect->Get()."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("ChangeLanguageNotes")."</div>
						<div class=\"FormButtons\">
							<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Save")."\" class=\"Button SubmitButton\" />
							<a href=\"./settings.php\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
						</div>
						</form>
					</div>
				</div>");
			}
		}
	}
}


?>

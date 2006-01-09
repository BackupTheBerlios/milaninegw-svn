<?php
/*
Extension Name: Custom Styles
Extension Url: http://lussumo.com/docs/
Description: Allows administrators to define and create multiple styles for Vanilla. Users can then change their style.
Version: 1.0
Author: Mark O'Sullivan
Author Url: http://www.markosullivan.ca/
*/

/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Vanilla.
* Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Vanilla is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
* 
* Description: Container for style properties and a style management class.
*/

// Let it skip these classes if it doesn't need them
if (in_array($Context->SelfUrl, array("settings.php", "account.php"))) {
	class Style {
		var $StyleID;
		var $AuthUserID;
		var $AuthUsername;
		var $AuthFullName;
		var $Name;				// The name of the style itself
		var $Url;
		var $PreviewImage;
		var $Context;
		
		function Style(&$Context) {
			$this->Context = &$Context;
		}
		function Clear() {
			$this->StyleID = 0;
			$this->AuthUserID = 0;
			$this->AuthUsername = "";
			$this->AuthFullName = "";
			$this->Name = "";
			$this->Url = "";
			$this->PreviewImage = "";
		}
		
		function FormatPropertiesForDatabaseInput() {
			$this->Name = FormatStringForDatabaseInput($this->Name, 1);
			$this->Url = FormatStringForDatabaseInput($this->Url, 1);
			$this->PreviewImage = FormatStringForDatabaseInput($this->PreviewImage, 1);
		}
		
		function FormatPropertiesForDisplay() {
			$this->AuthUsername = FormatStringForDisplay($this->AuthUsername);
			$this->AuthFullName = FormatStringForDisplay($this->AuthFullName);
			$this->Name = FormatStringForDisplay($this->Name);
			$this->Url = FormatStringForDisplay($this->Url);
			$this->PreviewImage = FormatStringForDisplay($this->PreviewImage);
		}
		
		function GetPropertiesFromDataSet($DataSet) {
			$this->StyleID = ForceInt(@$DataSet["StyleID"],0);
			$this->AuthUserID = ForceInt(@$DataSet["AuthUserID"],0);
			if ($this->AuthUserID == 0) {
				$this->AuthUsername = $this->Context->GetDefinition("System");
				$this->AuthFullName = $this->Context->GetDefinition("System");
			} else {
				$this->AuthUsername = ForceString(@$DataSet["AuthUsername"],"");
				$this->AuthFullName = ForceString(@$DataSet["AuthFullName"],"");
			}
			$this->Name = ForceString(@$DataSet["Name"],"");
			$this->Url = ForceString(@$DataSet["Url"],"");
			$this->PreviewImage = ForceString(@$DataSet["PreviewImage"], "");
		}
		
		function GetPropertiesFromForm() {
			$this->StyleID = ForceIncomingInt("StyleID", 0);
			$this->AuthUserID = ForceIncomingInt("AuthUserID", 0);
			$this->AuthUsername = ForceIncomingString("AuthUsername", "");
			$this->Name = ForceIncomingString("Name", "");
			$this->Url = ForceIncomingString("Url", "");
			$this->PreviewImage = ForceIncomingString("PreviewImage", "");
		}
	}
	
	class StyleManager {
		var $Name;				// The name of this class
		var $Context;			// The context object that contains all global objects (database, error manager, warning collector, session, etc)
		
		// Returns a SqlBuilder object with all of the user properties already defined in the select
		function GetStyleBuilder() {
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("Style", "s");
			$s->AddSelect(array("StyleID", "AuthUserID", "Name", "Url", "PreviewImage"), "s");
			$s->AddJoin("User", "u", "UserID", "s", "AuthUserID", "left join");
			$s->AddSelect("Name", "u", "AuthUsername");
			$s->AddSelect("FirstName", "u", "AuthFullName", "concat", "' ',u.LastName");
			return $s;
		}
		
		function GetStyleById($StyleID) {
			$s = $this->GetStyleBuilder();
			$s->AddWhere("s.StyleID", $StyleID, "=");
	
			$Style = $this->Context->ObjectFactory->NewContextObject($this->Context, "Style");
			$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetStyleById", "An error occurred while attempting to retrieve the requested style.");
			if ($this->Context->Database->RowCount($result) == 0) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrStyleNotFound"));
			while ($rows = $this->Context->Database->GetRow($result)) {
				$Style->GetPropertiesFromDataSet($rows);
			}
			
			return $this->Context->WarningCollector->Iif($Style, false);
		}
		
		function GetStyleCount() {
			$TotalNumberOfRecords = 0;
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("Style", "s");
			$s->AddSelect("StyleID", "s", "Count", "count");
			
			$result = $this->Context->Database->Select($this->Context, $s, $this->Name, "GetStyleCount", "An error occurred while retrieving the style count.");
			while ($rows = $this->Context->Database->GetRow($result)) {
				$TotalNumberOfRecords = $rows['Count'];
			}
			return $TotalNumberOfRecords;
		}
		
		function GetStyleList($CurrentPage = "1", $RowsPerPage = "0") {
			$s = $this->GetStyleBuilder();
			$CurrentPage = ForceInt($CurrentPage, 1);
			if ($CurrentPage < 1) $CurrentPage == 1;
			$RowsPerPage = ForceInt($RowsPerPage, 0);
			$FirstRecord = ($CurrentPage * $RowsPerPage) - $RowsPerPage;
			$s->AddOrderBy("StyleID", "s", "asc");
			if ($RowsPerPage > 0) $s->Limit($FirstRecord, $RowsPerPage);
				
			return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetDataList", "An error occurred while attempting to retrieve styles.");
		}
		
		// Returns the styles in a format more suitable for the select list
		function GetStylesForSelectList() {
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("Style", "s");
			$s->AddSelect("StyleID", "s");
			$s->AddJoin("User", "u", "UserID", "s", "AuthUserID", "left join");
			$s->AddSelect("Name", "s", "Name", "concat", "' ".$this->Context->GetDefinition("By")." ',coalesce(u.Name,'".$this->Context->GetDefinition("System")."')");
			$s->AddOrderBy("Name", "s", "asc");
			return $this->Context->Database->Select($this->Context, $s, $this->Name, "GetStylesForSelectList", "An error occurred while attempting to retrieve styles.");
		}
	
		function RemoveStyle($RemoveStyleID, $ReplacementStyleID) {
			// Reassign the user-chosen styles
			$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
			$s->SetMainTable("User", "u");
			$s->AddFieldNameValue("StyleID", $ReplacementStyleID);
			$s->AddWhere("StyleID", $RemoveStyleID, "=");
			$this->Context->Database->Update($this->Context, $s, $this->Name, "RemoveStyle", "An error occurred while attempting to re-assign user styles.");
			// Now remove the style itself
			$s->Clear();
			$s->SetMainTable("Style", "s");
			$s->AddWhere("StyleID", $RemoveStyleID, "=");
			$this->Context->Database->Delete($this->Context, $s, $this->Name, "RemoveStyle", "An error occurred while attempting to remove the style.");
		}
		
		function SaveStyle(&$Style) {
			// Ensure that the person performing this action has access to do so
			if (!$this->Context->Session->User->AdminUsers) $this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionInsufficient"));
			
			if ($this->Context->WarningCollector->Count() == 0) {
				// Retrieve the AuthUserID based on the supplied AuthUsername
				$um = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
				$Style->AuthUserID = $um->GetUserIdByName($Style->AuthUsername);
				// Validate the properties
				if($this->ValidateStyle($Style)) {
					$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
					$s->SetMainTable("Style", "s");
					$s->AddFieldNameValue("Name", $Style->Name);
					$s->AddFieldNameValue("AuthUserID", $Style->AuthUserID);
					$s->AddFieldNameValue("Url", $Style->Url);
					$s->AddFieldNameValue("PreviewImage", $Style->PreviewImage);
					if ($Style->StyleID > 0) {
						$s->AddWhere("StyleID", $Style->StyleID, "=");
						$this->Context->Database->Update($this->Context, $s, $this->Name, "SaveStyle", "An error occurred while attempting to update the style.");
					} else {
						$Style->StyleID = $this->Context->Database->Insert($this->Context, $s, $this->Name, "SaveStyle", "An error occurred while creating a new style.");
					}
				}
			}
			return $this->Context->WarningCollector->Iif($Style, false);
		}
		
		function StyleManager(&$Context) {
			$this->Name = "StyleManager";
			$this->Context = &$Context;
		}
		
		// Validates and formats properties ensuring they're safe for database input
		// Returns: boolean value indicating success
		function ValidateStyle(&$Style) {
			$ValidatedStyle = $Style;
			$ValidatedStyle->FormatPropertiesForDatabaseInput();
					
			Validate($this->Context->GetDefinition("StyleNameLower"), 1, $ValidatedStyle->Name, 50, "", $this->Context);
			Validate($this->Context->GetDefinition("StyleUrlLower"), 1, $ValidatedStyle->Url, 255, "", $this->Context);
			
			// If validation was successful, then reset the properties to db safe values for saving
			if ($this->Context->WarningCollector->Count() == 0) $Style = $ValidatedStyle;
			return $this->Context->WarningCollector->Iif();
		}
	}
}

// If looking at the settings page, include the styleform control and instantiate it
if (($Context->SelfUrl == "settings.php") && ($Context->Session->User->AdminUsers || $Context->Session->User->AdminCategories)) {
	class StyleForm extends PostBackControl {
		
		var $StyleManager;
		var $StyleData;
		var $StyleSelect;
		var $Style;
	
		function StyleForm(&$Context) {
			$this->ValidActions = array("Styles", "Style", "ProcessStyle", "StyleRemove", "ProcessStyleRemove");
			$this->Constructor($Context);
			if (!$this->Context->Session->User->MasterAdmin) {
				$this->IsPostBack = 0;
			} elseif ($this->IsPostBack) {
				$StyleID = ForceIncomingInt("StyleID", 0);
				$ReplacementStyleID = ForceIncomingInt("ReplacementStyleID", 0);
				$this->StyleManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "StyleManager");
				
				if ($this->PostBackAction == "ProcessStyle") {
					$this->Style = $this->Context->ObjectFactory->NewContextObject($this->Context, "Style");
					$this->Style->GetPropertiesFromForm($this->Context);
					if ($this->StyleManager->SaveStyle($this->Style)) {
						header("location: settings.php?PostBackAction=Styles");
					}
				} elseif ($this->PostBackAction == "ProcessStyleRemove") {
					if ($this->StyleManager->RemoveStyle($StyleID, $ReplacementStyleID)) {
						header("location: settings.php?PostBackAction=Styles");
					}
				}
				
				if (in_array($this->PostBackAction, array("StyleRemove", "Styles", "Style", "ProcessStyle", "ProcessStyleRemove"))) {
					$this->StyleData = $this->StyleManager->GetStylesForSelectList();
				}
				if (in_array($this->PostBackAction, array("StyleRemove", "Style", "ProcessStyleRemove"))) {
					$this->StyleSelect = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
					$this->StyleSelect->Name = "StyleID";
					$this->StyleSelect->CssClass = "SmallInput";
					if ($this->PostBackAction != "Style") $this->StyleSelect->AddOption("", "Choose...");
					$this->StyleSelect->AddOptionsFromDataSet($this->Context->Database, $this->StyleData, "StyleID", "Name");
				}
				if ($this->PostBackAction == "Style") {
					if ($StyleID > 0) {
						$this->Style = $this->StyleManager->GetStyleById($StyleID);
					} else {
						$this->Style = $this->Context->ObjectFactory->NewContextObject($this->Context, "Style");
					}
				}
				if (in_array($this->PostBackAction, array("ProcessStyle", "ProcessStyleRemove"))) {
					// Show the form again with errors
					$this->PostBackAction = str_replace("Process", "", $this->PostBackAction);
				}
			}
		}
		
		function Render() {
			if ($this->IsPostBack) {
				$this->PostBackParams->Clear();
				$StyleID = ForceIncomingInt("StyleID", 0);
				
				if ($this->PostBackAction == "Style") {
					$this->PostBackParams->Set("PostBackAction", "ProcessStyle");
					$this->Context->Writer->Write("<script language=\"javascript\">
						var sac = new AutoComplete('sac');
						</script>
						<div class=\"SettingsForm\">
						<h1>".$this->Context->GetDefinition("StyleManagement")."</h1>");
						if ($StyleID > 0) {
							$this->StyleSelect->Attributes = "onchange=\"document.location='?PostBackAction=Style&StyleID='+this.options[this.selectedIndex].value;\"";
							$this->StyleSelect->SelectedID = $StyleID;
							$this->Context->Writer->Write("<div class=\"Form\" id=\"Styles\">
								".$this->Get_Warnings()."
								".$this->Get_PostBackForm("frmStyle")."
								<h2>".$this->Context->GetDefinition("SelectStyleToEdit")."</h2>
								<dl>
									<dt>".$this->Context->GetDefinition("Styles")."</dt>
									<dd>".$this->StyleSelect->Get()." ".$this->Context->GetDefinition("Required")."</dd>
								</dl>
								<h2>".$this->Context->GetDefinition("ModifyStyleDefinition")."</h2>");
						} else {
							$this->Context->Writer->Write("<div class=\"Form\" id=\"Styles\">
								".$this->Get_Warnings()."
								".$this->Get_PostBackForm("frmStyle")."
								<h2>".$this->Context->GetDefinition("DefineTheNewStyle")."</h2>");
						}
						$this->Context->Writer->Write("<dl>
							<dt>".$this->Context->GetDefinition("StyleName")."</dt>
							<dd><input type=\"text\" name=\"Name\" value=\"".$this->Style->Name."\" maxlength=\"40\" class=\"SmallInput\" id=\"txtStyleName\" /> ".$this->Context->GetDefinition("Required")."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("StyleNameNotes")."</div>
						<dl>
							<dt>".$this->Context->GetDefinition("StyleAuthor")."</dt>
							<dd>
								<input type=\"text\" name=\"AuthUsername\" value=\"".($this->Style->AuthUserID == 0?"":$this->Style->AuthUsername)."\" maxlength=\"20\" class=\"SmallInput\" id=\"txtStyleAuthor\" onKeyUp=\"return sac.LoadData(this, event, 'StyleAuthorACContainer');\" onblur=\"sac.HideAutoComplete();\" autocomplete=\"off\" />
								<div id=\"StyleAuthorACContainer\" class=\"AutoCompleteContainer\" style=\"display: none;\"></div>
							</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("StyleAuthorNotes")."</div>
						<dl>
							<dt>".$this->Context->GetDefinition("StyleUrl")."</dt>
							<dd><input type=\"text\" name=\"Url\" value=\"".$this->Style->Url."\" maxlength=\"200\" class=\"SmallInput\" id=\"txtStyleUrl\" /> ".$this->Context->GetDefinition("Required")."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("StyleUrlNotes")."</div>
						<dl>
							<dt>".$this->Context->GetDefinition("PreviewImageFilename")."</dt>
							<dd><input type=\"text\" name=\"PreviewImage\" value=\"".$this->Style->PreviewImage."\" maxlength=\"20\" class=\"SmallInput\" id=\"txtStylePreviewImage\" /></dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("PreviewImageFilenameNotes")."</div>
						<div class=\"FormButtons\">
							<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Save")."\" class=\"Button SubmitButton\" />
							<a href=\"./settings.php?PostBackAction=Styles\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
						</div>
					</div></div>");			
					
				} elseif ($this->PostBackAction == "StyleRemove") {
					$this->PostBackParams->Set("PostBackAction", "ProcessStyleRemove");
					$this->StyleSelect->Attributes = "onchange=\"document.location='?PostBackAction=StyleRemove&StyleID='+this.options[this.selectedIndex].value;\"";
					$this->StyleSelect->SelectedID = $StyleID;
					$this->Context->Writer->Write("<div class=\"SettingsForm\">
						<h1>".$this->Context->GetDefinition("StyleManagement")."</h1>
						<div class=\"Form\" id=\"StyleRemove\">
							".$this->Get_Warnings()."
							".$this->Get_PostBackForm("frmStyleRemove")."
							<h2>".$this->Context->GetDefinition("SelectStyleToRemove")."</h2>
							<dl>
								<dt>".$this->Context->GetDefinition("Styles")."</dt>
								<dd>".$this->StyleSelect->Get()." ".$this->Context->GetDefinition("Required")."</dd>
							</dl>");
							if ($StyleID > 0) {
								$this->StyleSelect->Attributes = "";
								$this->StyleSelect->RemoveOption($this->StyleSelect->SelectedID);
								$this->StyleSelect->Name = "ReplacementStyleID";
								$this->StyleSelect->SelectedID = ForceIncomingInt("ReplacementStyleID", 0);
								$this->Context->Writer->Write("<h2>".$this->Context->GetDefinition("SelectAReplacementStyle")."</h2>
								<dl>
									<dt>Replacement style</dt>
									<dd>".$this->StyleSelect->Get()." ".$this->Context->GetDefinition("Required")."</dd>
								</dl>
								<div class=\"InputNote\">".$this->Context->GetDefinition("ReplacementStyleNotes")."</div>
								<div class=\"FormButtons\">
									<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Remove")."\" class=\"Button SubmitButton\" />
									<a href=\"./settings.php?PostBackAction=Styles\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
								</div>");
							}
							$this->Context->Writer->Write("</form>
						</div>
					</div>");				
				} else {
					$this->Context->Writer->Write("<div class=\"SettingsForm\">
						".$this->Get_Warnings()."
						<h1>".$this->Context->GetDefinition("StyleManagement")."</h1>
						<div class=\"Form\" id=\"Styles\">
							<h2>".$this->Context->GetDefinition("Styles")."</h2>
							<ul class=\"SortList\">");
								if ($this->StyleData) {
									$s = $this->Context->ObjectFactory->NewContextObject($this->Context, "Style");
									
									while ($Row = $this->Context->Database->GetRow($this->StyleData)) {
										$s->Clear();
										$s->GetPropertiesFromDataSet($Row);
										$s->FormatPropertiesForDisplay();
										$this->Context->Writer->Write("<li class=\"SortListItem\">
											<ul>
												<li class=\"SortRemove\"><a href=\"./settings.php?PostBackAction=StyleRemove&StyleID=".$s->StyleID."\"><img src=\"".$this->Context->Session->User->StyleUrl."btn.remove.gif\" height=\"13\" width=\"13\" border=\"0\" alt=\"".$this->Context->GetDefinition("Remove")."\" /></a></li>
												<li class=\"SortItem\"><a href=\"./settings.php?PostBackAction=Style&StyleID=".$s->StyleID."\">".$s->Name."</a></li>
											</ul>
										</li>");
									}
								}
							$this->Context->Writer->Write("</ul>
							<div class=\"FormLink\"><a href=\"settings.php?PostBackAction=Style\">".$this->Context->GetDefinition("CreateANewStyle")."</a></div>
						</div>
					</div>");
				}
			}
		}
	}
	
	$StyleForm = $Context->ObjectFactory->NewContextObject($Context, "StyleForm");
	$Body->AddControl($StyleForm);
	if ($Context->Session->User->MasterAdmin) $Panel->AddListItem($Context->GetDefinition("AdministrativeOptions"), $Context->GetDefinition("StyleManagement"), "settings.php?PostBackAction=Styles");
} elseif ($Context->SelfUrl == "account.php" && $Context->Session->UserID > 0) {
	$AccountUserID = ForceIncomingInt("u", $Context->Session->UserID);
   if ($AccountUserID == $Context->Session->UserID) {
		// If looking at the account page, include the styleuserform control and instantiate it
		class UserStyleForm extends PostBackControl {
			
			var $StyleManager;
			var $StyleData;
		
			function UserStyleForm(&$Context) {
				$this->ValidActions = array("Style");
				$this->Constructor($Context);
				if ($this->IsPostBack) {
					$this->StyleManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "StyleManager");
					$this->StyleData = $this->StyleManager->GetStyleList();
				}
			}
			
			function Render() {
				if ($this->IsPostBack) {
					$this->Context->Writer->Add("<div class=\"SettingsForm\">
					<h1>".$this->Context->GetDefinition("ChangeYourStylesheet")."</h1>
						<div class=\"Form\">
							".$this->Get_Warnings()."
							<h2>".$this->Context->GetDefinition("ForumAppearance")."</h2>
							<div class=\"InputBlock\">
								<div class=\"InputNote\">".$this->Context->GetDefinition("ForumAppearanceNotes")."</div>
							</div>");
							$Style = $this->Context->ObjectFactory->NewContextObject($this->Context, "Style");
							while ($Row = $this->Context->Database->GetRow($this->StyleData)) {
								$Style->Clear();
								$Style->GetPropertiesFromDataSet($Row);
								$Style->FormatPropertiesForDisplay();
								$this->Context->Writer->Add("<div class=\"Preview\">
									<div class=\"PreviewTitle\">".$Style->Name.($Style->AuthUserID > 0?" ".$this->Context->GetDefinition("By")." <a href=\"account.php?u=".$Style->AuthUserID."\">".$Style->AuthUsername."</a>":"")."</div>");
									if ($Style->PreviewImage != "") {
										$this->Context->Writer->Add("<a class=\"PreviewImage\" href=\"javascript:SetStyle('".$Style->StyleID."', '');\"><img src=\"".$Style->Url.$Style->PreviewImage."\" border=\"0\" height=\"200\" width=\"370\" /></a>");
									} else {
										$this->Context->Writer->Add("<a class=\"PreviewEmpty\" href=\"javascript:SetStyle('".$Style->StyleID."', '');\">".$this->Context->GetDefinition("NoPreview")."</a>");
									}
								$this->Context->Writer->Add("</div>");
							}					
							$this->Context->Writer->Write("<h2>".$this->Context->GetDefinition("CustomStyle")."</h2>
							<form name=\"frmCustomStyle\">
							<dl>
								<dt>".$this->Context->GetDefinition("CustomStyleUrl")."</dt>
								<dd><input type=\"text\" name=\"CustomStyle\" value=\"".$this->Context->Session->User->CustomStyle."\" maxlength=\"200\" class=\"SmallInput\" id=\"txtCustomStyle\" /></dd>
							</dl>
							<div class=\"InputNote\">
								".$this->Context->GetDefinition("CustomStyleNotes")."
								<div class=\"FormLink\"><a href=\"javascript:SetStyle('0', document.frmCustomStyle.CustomStyle.value);\">".$this->Context->GetDefinition("UseCustomStyle")."</a></div>
							</div>
							</form>
							
						</div>
					</div>");
				}
			}
		}
			
		$UserStyleForm = $Context->ObjectFactory->NewContextObject($Context, "UserStyleForm");
		$Body->AddControl($UserStyleForm);
		$Panel->AddListItem($Context->GetDefinition("AccountOptions"), $Context->GetDefinition("ChangeYourStylesheet"), "account.php?PostBackAction=Style");
	}
	// Include the style definition on the user's profile & the account profile is being display
	$PostBackAction = ForceIncomingString("PostBackAction", "");
	if ($PostBackAction == "") {
		$um = $Context->ObjectFactory->NewContextObject($Context, "UserManager");
		$AccountUser = $um->GetUserById($AccountUserID);
		if ($AccountUser) {
			$StyleOption = "<dt>".$Context->GetDefinition("Style")."</dt>
				<dd>";
			if ($Context->Session->UserID > 0 && $Context->Session->User->StyleID != $AccountUser->StyleID && $Context->Session->UserID != $AccountUser->UserID) {
				$StyleOption .= "<a href=\"javascript:SetStyle('".$AccountUser->StyleID."', '".($AccountUser->StyleID == 0?urlencode($AccountUser->CustomStyle):"")."');\">".$AccountUser->Style."</a>";
			} else {
				$StyleOption .= $AccountUser->Style;
			}
			$StyleOption .= "</dd>";
			$Context->ObjectFactory->AddControlString("Account", "RenderUserProperties", $StyleOption);
		}
	}
}
?>
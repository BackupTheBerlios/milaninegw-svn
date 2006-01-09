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
* Description: Controls for account.php
*/

// Displays a user's account information
class Account extends Control {
	var $User;	// The user object to be displayed
   
	function Account(&$Context, &$User) {
		$this->PostBackAction = ForceIncomingString("PostBackAction", "");
		$this->Context = &$Context;
		$this->User = &$User;
	}
	
	function Render() {
		// Don't render anything but warnings if there are any warnings or if there is a postback
      if ($this->PostBackAction == "") {
			if ($this->Context->WarningCollector->Count() > 0) {
				$this->Context->Writer->Write($this->Get_Warnings());
			} else {
				$this->Context->Writer->Add("<div class=\"Account\">");
					if ($this->Context->Session->User->Setting("HtmlOn", 1) && $this->User->DisplayIcon != "") {
						$this->Context->Writer->Add("<h1 class=\"AccountWithIcon\"><span class=\"AccountIcon\" style=\"background-image:url('".$this->User->DisplayIcon."')\"></span>");
					} else {
						$this->Context->Writer->Add("<h1>");
					}
						$this->Context->Writer->Add($this->User->FullName
					."</h1>
					<small>".$this->User->Role."</small>
					<div class=\"AccountBody\">");
						if ($this->User->RoleDescription != "") $this->Context->Writer->Add("<blockquote>".$this->User->RoleDescription."</blockquote>");
						if ($this->User->Picture != "" && $this->User->CanPostHTML && $this->Context->Session->User->Setting("HtmlOn", 1)) $this->Context->Writer->Add("<div class=\"Picture\">".GetImage($this->User->Picture,"","","Picture","")."</div>");
						$this->Context->Writer->Add("<div class=\"AccountData\">\n<dl>
							<dt>".$this->Context->GetDefinition("Account")."</dt>
							<dd>".(($this->User->ShowName||$this->Context->Session->User->AdminUsers)?$this->User->Name:"n/a")."</dd>
							<dt>".$this->Context->GetDefinition("Email")."</dt>
							<dd>".(($this->Context->Session->UserID > 0 && ($this->User->UtilizeEmail||$this->Context->Session->User->AdminUsers))?GetEmail($this->User->Email):"n/a")."</dd>
							<dt>".$this->Context->GetDefinition("AccountCreated")."</dt>");
							$this->Context->Writer->Add("<dd>".TimeDiff($this->User->DateFirstVisit, mktime())."</dd>\n");
							$this->Context->Writer->Add("<dt>".$this->Context->GetDefinition("MemberOfGroups")."</dt>\n<dd>".implode("</br>",$this->User->UserGroups)."</dd>");
                                                        $this->Context->Writer->Add("<dt>".$this->Context->GetDefinition("LastActive")."</dt>
							<dd>".TimeDiff($this->User->DateLastActive, mktime())."</dd>
							<dt>".$this->Context->GetDefinition("VisitCount")."</dt>
							<dd>".$this->User->CountVisit."</dd>
							<dt>".$this->Context->GetDefinition("DiscussionsStarted")."</dt>
							<dd>".$this->User->CountDiscussions."</dd>
							<dt>".$this->Context->GetDefinition("CommentsAdded")."</dt>
							<dd>".$this->User->CountComments."</dd>"
							.$this->Context->ObjectFactory->RenderControlStrings("Account", "RenderUserProperties"));
							
							if ($this->Context->Session->User->AdminUsers) {
								$this->Context->Writer->Add("
								<dt>".$this->Context->GetDefinition("LastKnownIp")."</dt>
								<dd>".$this->User->RemoteIp."</dd>
								");
							}
							
							if (count($this->User->Attributes) > 0) {
								for ($i = 0; $i < count($this->User->Attributes); $i++) {
									$this->Context->Writer->Add("
										<dt>".$this->User->Attributes[$i]["Label"]."</dt>
										<dd>".FormatHyperlink($this->User->Attributes[$i]["Value"])."</dd>
									");
								}
							}
						$this->Context->Writer->Add("</dl>
					</div></div>
				</div>");
				$this->Context->Writer->Write();
			}
		}		
	}
}

// Displays a user's discovery information (for admins only)
class Discovery extends Control {
	var $User;		// The user who's history is being reviewed
   
	function Discovery(&$Context, &$User) {
		$this->PostBackAction = ForceIncomingString("PostBackAction", "");
		$this->Context = &$Context;
		$this->User = &$User;
	}
	
	function Render() {
		if ($this->Context->WarningCollector->Count() == 0 && $this->PostBackAction == "") {
			if ($this->User->RoleID == 0 && $this->User->Discovery != "" && $this->Context->Session->User->AdminUsers) {
				$this->Context->Writer->Add("<div class=\"Discovery\">
					<h1>".$this->Context->GetDefinition("Discovery")."</h1>
					<blockquote>".FormatHtmlStringInline($this->User->Discovery)."</blockquote>
				</div>");
			}
		}
	}
}

// Displays a user's role history
class RoleHistory extends Control {
	var $History;	// The history data for the specified user
   
	function RoleHistory(&$Context, &$UserManager, $UserID) {
		$this->PostBackAction = ForceIncomingString("PostBackAction", "");
		$this->Context = &$Context;
		if ($this->PostBackAction == "") $this->History = $UserManager->GetUserRoleHistoryByUserId($UserID);
	}
	
	function Render() {
		if ($this->Context->WarningCollector->Count() == 0 && $this->PostBackAction == "") {
			$this->Context->Writer->Add("<div class=\"RoleHistory\">
				<h1>".$this->Context->GetDefinition("RoleHistory")."</h1>");
				// Loop through the user's role history
				$UserHistory = $this->Context->ObjectFactory->NewObject($this->Context, "UserRoleHistory");
				if ($this->Context->Database->RowCount($this->History) == 0) {
					$this->Context->Writer->Add("<blockquote>".$this->Context->GetDefinition("NoRoleHistory")."</blockquote>");
				} else {
					while ($Row = $this->Context->Database->GetRow($this->History)) {
						$UserHistory->Clear();
						$UserHistory->GetPropertiesFromDataSet($Row);
						$UserHistory->FormatPropertiesForDisplay($this->Context);
						
						$this->Context->Writer->Add("<blockquote>
							<h2>".$UserHistory->Role."</strong></h2> <small>(".TimeDiff($UserHistory->Date, mktime()).")</small>
							<h3>".$this->Context->GetDefinition("RoleAssignedBy")." ".($UserHistory->AdminUserID == 0?$this->Context->GetDefinition("Applicant"):"<a href=\"account.php?u=".$UserHistory->AdminUserID."\">".$UserHistory->AdminUsername."</a>")." ".$this->Context->GetDefinition("WithTheFollowingNotes")."</h3>
							<p>".$UserHistory->Notes."</p>
						</blockquote>");
					}
				}
			$this->Context->Writer->Write("</div>");			
		}
	}
}

// Displays a user's IP history (for admins only)
class IpHistory extends Control {
	var $History;		// The user's IP history data
   
	function IpHistory(&$Context, &$UserManager, $UserID) {
		$this->PostBackAction = ForceIncomingString("PostBackAction", "");
		$this->Context = &$Context;
		$this->History = false;
		if ($this->Context->Session->User) {
			if ($this->Context->Session->User->CanViewIps && $this->PostBackAction == "") $this->History = $UserManager->GetIpHistory($UserID);
		}
	}
	
	function Render() {
		if ($this->History && $this->PostBackAction == "") {
			$this->Context->Writer->Add("<div class=\"IpHistory\">
				<h1>".$this->Context->GetDefinition("IpHistory")."</h1>");
				// Loop through the user's ip history
				$SharedCount = 0;
				$HistoryCount = count($this->History);
				if ($HistoryCount > 0) {
					for ($i = 0; $i < $HistoryCount; $i++) {
						$SharedCount = count($this->History[$i]["SharedWith"]);
						$this->Context->Writer->Add("<blockquote>
							<h2>".$this->History[$i]["IP"]."</h2>
							<small>(".FormatPlural($this->History[$i]["UsageCount"], $this->Context->GetDefinition("time"), $this->Context->GetDefinition("times")).")</small>");
							if ($SharedCount > 0) {
								$this->Context->Writer->Add("<h3>".$this->Context->GetDefinition("IpAlsoUsedBy")."</h3>
								<p>");
									for ($j = 0; $j < $SharedCount; $j++) {
										$SharedUserName = $this->History[$i]["SharedWith"][$j]["Name"];
										$SharedUserID = $this->History[$i]["SharedWith"][$j]["UserID"];
										if ($j > 0) $this->Context->Writer->Add(", ");
										$this->Context->Writer->Add("<a href=\"account.php?u=".$SharedUserID."\">".$SharedUserName."</a>");
									}
									$this->Context->Writer->Add("</p>");
							} else {
								$this->Context->Writer->Add("<h3>".$this->Context->GetDefinition("IpNotShared")."</h3>");
							}
						$this->Context->Writer->Add("</blockquote>");
					}
				} else {
					$this->Context->Writer->Add("<blockquote>".$this->Context->GetDefinition("NoIps")."</blockquote>");
				}
			$this->Context->Writer->Write("</div>");		
		}
	}
}

// A postback control that allows a user/admin to change user account info
class IdentityForm extends PostBackControl {
	var $UserManager;
	var $User;
	
	function IdentityForm (&$Context, &$UserManager, &$User) {
		$this->ValidActions = array("ProcessIdentity", "Identity");
		$this->Constructor($Context);
		if ($this->IsPostBack) {
			$this->UserManager = &$UserManager;
			$this->User = &$User;
			if ($this->PostBackAction == "ProcessIdentity") {
				$this->User->Clear();
				$this->User->GetPropertiesFromForm();
				if ($this->UserManager->SaveIdentity($this->User)) header("location: ".$this->Context->SelfUrl.($this->User->UserID != $this->Context->Session->UserID ? "?u=".$this->User->UserID:""));
			}
		}
	}
	
	function Render() {
		if ($this->IsPostBack) {
			if ($this->Context->Session->UserID != $this->User->UserID && !$this->Context->Session->User->AdminUsers) {
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("PermissionError"));
				$this->Context->Writer->Add("<div class=\"AccountForm\">
						".$this->Get_Warnings()."
				</div>");				
			} else {				
				$this->PostBackParams->Set("PostBackAction", "ProcessIdentity");
				$this->PostBackParams->Set("u", $this->User->UserID);
				$this->PostBackParams->Set("LabelValuePairCount", (count($this->User->Attributes) > 0? count($this->User->Attributes):1));
				$Required = $this->Context->GetDefinition("Required");
				$this->Context->Writer->Add("<div class=\"AccountForm\">
					<h1>".$this->Context->GetDefinition("ChangePersonalInfo")."</h1>
					<div class=\"Form AccountPersonal\">
						".$this->Get_Warnings()."
						".$this->Get_PostBackForm("frmAccountPersonal")."
						<h2>".$this->Context->GetDefinition("DefineYourAccountProfile")."</h2>");
						if (agALLOW_NAME_CHANGE == "1") {
							$this->Context->Writer->Add("<dl>
								<dt>".$this->Context->GetDefinition("YourUsername")."</dt>
								<dd><input type=\"text\" name=\"Name\" value=\"".$this->User->Name."\" maxlength=\"20\" class=\"SmallInput\" id=\"txtUsername\" /> ".$Required."</dd>
							</dl>
							<div class=\"InputNote\">".$this->Context->GetDefinition("YourUsernameNotes")."</div>");
						}
						$this->Context->Writer->Add("<dl>
							<dt>".$this->Context->GetDefinition("YourFirstName")."</dt>
							<dd><input type=\"text\" name=\"FirstName\" value=\"".$this->User->FirstName."\" maxlength=\"50\" class=\"SmallInput\" id=\"txtFirstName\" /> ".$Required."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("YourFirstNameNotes")."</div>
						<dl>
							<dt>".$this->Context->GetDefinition("YourLastName")."</dt>
							<dd><input type=\"text\" name=\"LastName\" value=\"".$this->User->LastName."\" maxlength=\"50\" class=\"SmallInput\" id=\"txtLastName\" /> ".$Required."</dd>
						</dl>
						<div class=\"InputNote\">
							".$this->Context->GetDefinition("YourLastNameNotes")."
							<div class=\"CheckBox\">".GetDynamicCheckBox("ShowName", 1, $this->User->ShowName, "", $this->Context->GetDefinition("MakeRealNameVisible"))."</div>
						</div>
						<dl>
							<dt>".$this->Context->GetDefinition("YourEmailAddress")."</dt>
							<dd><input type=\"text\" name=\"Email\" value=\"".$this->User->Email."\" maxlength=\"200\" class=\"SmallInput\" id=\"txtEmail\" /> ".$Required."</dd>
						</dl>
						<div class=\"InputNote\">
							".$this->Context->GetDefinition("YourEmailAddressNotes")."
							<div class=\"CheckBox\">".GetDynamicCheckBox("UtilizeEmail", 1, $this->User->UtilizeEmail, "", $this->Context->GetDefinition("CheckForVisibleEmail"))."</div>
						</div>					
						<dl>
							<dt>".$this->Context->GetDefinition("AccountPicture")."</dt>
							<dd><input type=\"text\" name=\"Picture\" value=\"".$this->User->Picture."\" maxlength=\"255\" class=\"SmallInput\" id=\"txtPicture\" /></dd>
						</dl>
						<div class=\"InputNote\">
							".$this->Context->GetDefinition("AccountPictureNotes")."
						</div>
						<dl>
							<dt>".$this->Context->GetDefinition("Icon")."</dt>
							<dd><input type=\"text\" name=\"Icon\" value=\"".$this->User->Icon."\" maxlength=\"255\" class=\"SmallInput\" id=\"txtIcon\" /></dd>
						</dl>
						<div class=\"InputNote\">
							".$this->Context->GetDefinition("IconNotes")."
						</div>
						<h2>".$this->Context->GetDefinition("AddCustomInformation")."</h2>
						<div class=\"InputNote\">".$this->Context->GetDefinition("AddCustomInformationNotes")."</div>
						<dl class=\"InputCustom\" id=\"LabelValuePairContainer\">");
							$CurrentItem = 1;
							if (count($this->User->Attributes) > 0) {
								for ($i = 0; $i < count($this->User->Attributes); $i++) {
									if ($i == 0) {
										$this->Context->Writer->Add("<dt class=\"DefinitionHeading\">".$this->Context->GetDefinition("Label")."</dt>
										<dd class=\"DefinitionHeading\">".$this->Context->GetDefinition("Value")."</dd>");
									}
									$this->Context->Writer->Add("<dt><input type=\"text\" name=\"Label".$CurrentItem."\" value=\"".$this->User->Attributes[$i]["Label"]."\" maxlength=\"20\" class=\"LVLabelInput\" /></dt>
									<dd><input type=\"text\" name=\"Value".$CurrentItem."\" value=\"".$this->User->Attributes[$i]["Value"]."\" maxlength=\"200\" class=\"LVValueInput\" /></dd>");
									$CurrentItem++;
								}
							} else {
								$this->Context->Writer->Add("<dt class=\"DefinitionHeading\">".$this->Context->GetDefinition("Label")."</dt>
								<dd class=\"DefinitionHeading\">".$this->Context->GetDefinition("Value")."</dd>
								<dt class=\"DefinitionItem\"><input type=\"text\" name=\"Label".$CurrentItem."\" value=\"\" maxlength=\"20\" class=\"LVLabelInput\" /></dt>
								<dd class=\"DefinitionItem\"><input type=\"text\" name=\"Value".$CurrentItem."\" value=\"\" maxlength=\"200\" class=\"LVValueInput\" /></dd>");
							}
						$this->Context->Writer->Add("</dl>
						<div class=\"FormLink\"><a href=\"javascript:AddLabelValuePair();\">".$this->Context->GetDefinition("AddLabelValuePair")."</a></div>
						<div class=\"FormButtons\">
							<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Save")."\" class=\"Button SubmitButton\" />
							<a href=\"./account.php?u=".$this->User->UserID."\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
						</div>
						</form>
					</div>
				</div>");
			}
		}
	}
}

class PasswordForm extends PostBackControl {
	var $UserManager;
	var $User;
	
	function PasswordForm (&$Context, &$UserManager, $UserID) {
		$this->ValidActions = array("ProcessPassword", "Password");
		$this->Constructor($Context);
		if ($this->IsPostBack) {
			$this->UserManager = &$UserManager;
			$this->User = $this->Context->ObjectFactory->NewContextObject($Context, "User");
			$this->User->GetPropertiesFromForm();
			$this->User->UserID = $UserID;
			if ($this->PostBackAction == "ProcessPassword") {
				if ($this->UserManager->ChangePassword($this->User)) header("location: ".$this->Context->SelfUrl);
			}
		}
	}
	
	function Render() {
		if ($this->IsPostBack) {
			if ($this->Context->Session->UserID != $this->User->UserID && !$this->Context->Session->User->AdminUsers) {
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("PermissionError"));
				$this->Context->Writer->Add("<div class=\"AccountForm\">
						".$this->Get_Warnings()."
				</div>");				
			} else {				
				$this->PostBackParams->Set("PostBackAction", "ProcessPassword");
				$this->PostBackParams->Set("u", $this->User->UserID);
				$Required = $this->Context->GetDefinition("Required");
				
				$this->Context->Writer->Add("<div class=\"AccountForm\">
					<h1>".$this->Context->GetDefinition("ChangeYourPassword")."</h1>
					<div class=\"Form AccountPassword\">
						".$this->Get_Warnings()."
						".$this->Get_PostBackForm("frmAccountPassword")."
						<dl>
							<dt>".$this->Context->GetDefinition("YourOldPassword")."</dt>
							<dd><input type=\"password\" name=\"OldPassword\" value=\"".$this->User->OldPassword."\" maxlength=\"100\" class=\"SmallInput\" id=\"txtOldPassword\" /> ".$Required."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("YourOldPasswordNotes")."</div>
						<dl>
							<dt>".$this->Context->GetDefinition("YourNewPassword")."</dt>
							<dd><input type=\"password\" name=\"NewPassword\" value=\"".$this->User->NewPassword."\" maxlength=\"100\" class=\"SmallInput\" id=\"txtNewPassword\" /> ".$Required."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("YourNewPasswordNotes")."</div>
						<dl>
							<dt>".$this->Context->GetDefinition("YourNewPasswordAgain")."</dt>
							<dd><input type=\"password\" name=\"ConfirmPassword\" value=\"".$this->User->ConfirmPassword."\" maxlength=\"100\" class=\"SmallInput\" id=\"txtConfirmPassword\" /> ".$Required."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("YourNewPasswordAgainNotes")."</div>
						<div class=\"FormButtons\">
							<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("Save")."\" class=\"Button SubmitButton\" />
							<a href=\"./account.php?u=".$this->User->UserID."\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
						</div>
						</form>
					</div>
				</div>");
			}
		}
	}
}

class FunctionalityForm extends PostBackControl {
	var $UserManager;
	var $User;
	
	function FunctionalityForm(&$Context, &$UserManager, $User) {
		$this->ValidActions = array("Functionality");
		$this->Constructor($Context);
		if ($this->IsPostBack) {
			$this->UserManager = &$UserManager;
			$this->User = $User;
		}
	}
	
	function Render() {
		if ($this->IsPostBack) {
			if ($this->Context->Session->UserID != $this->User->UserID && !$this->Context->Session->User->AdminUsers) {
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("PermissionError"));
				$this->Context->Writer->Add("<div class=\"AccountForm\">
					".$this->Get_Warnings()."
				</div>");				
			} else {				
			
				$FormatCount = count($this->Context->StringManipulator->Formatters);
				if ($FormatCount > 1) {
					$f = $this->Context->ObjectFactory->NewObject($this->Context, "Radio");
					$f->Name = "DefaultFormatType";
					$f->CssClass = "Radio";
					$f->Attributes = " onchange=\"SetFormatType('frmFunctionality');\"";
					$f->LabelOnClick = "SetFormatType();";
					$f->SelectedID = $this->Context->Session->User->DefaultFormatType;
					while (list($Name, $Object) = each($this->Context->StringManipulator->Formatters)) {
						$f->AddOption($Name, $this->Context->GetDefinition($Name));
					}
				}
				
				$this->Context->Writer->Write("<div class=\"AccountForm FunctionalityForm\">
					<h1>".$this->Context->GetDefinition("ForumFunctionality")."</h1>
					<div class=\"Form\">
						<form name=\"frmFunctionality\">
						<div class=\"InputNote\">".$this->Context->GetDefinition("ForumFunctionalityNotes")."</div>
						<h2>".$this->Context->GetDefinition("ControlPanel")."</h2>
						<div class=\"InputBlock\">
							<div class=\"InputNote\">".$this->Context->GetDefinition("ControlPanelNotes")."</div>
							<div class=\"CheckBox\">".GetDynamicCheckBox("ShowAppendices", 1, $this->Context->Session->User->Setting("ShowAppendices", 1), "PanelSwitch('ShowAppendices');", $this->Context->GetDefinition("DisplayListAppendices"))."</div>
							<div class=\"CheckBox\">".GetDynamicCheckBox("ShowTextToggle", 1, $this->Context->Session->User->Setting("ShowTextToggle", 1), "PanelSwitch('ShowTextToggle', 1);", $this->Context->GetDefinition("DisplayTextOnlyToggle"))."</div>
							<div class=\"CheckBox\">".GetDynamicCheckBox("ShowSavedSearches", 1, $this->Context->Session->User->Setting("ShowSavedSearches", 1), "PanelSwitch('ShowSavedSearches');", $this->Context->GetDefinition("DisplaySavedSearches"))."</div>
							<div class=\"CheckBox\">".GetDynamicCheckBox("ShowBookmarks", 1, $this->Context->Session->User->Setting("ShowBookmarks"), "PanelSwitch('ShowBookmarks');", $this->Context->GetDefinition("DisplayBookmarks"))."</div>
							<div class=\"CheckBox\">".GetDynamicCheckBox("ShowRecentDiscussions", 1, $this->Context->Session->User->Setting("ShowRecentDiscussions"), "PanelSwitch('ShowRecentDiscussions');", $this->Context->GetDefinition("DisplayYourDiscussions"))."</div>
							<div class=\"CheckBox\">".GetDynamicCheckBox("ShowBrowsingHistory", 1, $this->Context->Session->User->Setting("ShowBrowsingHistory"), "PanelSwitch('ShowBrowsingHistory');", $this->Context->GetDefinition("DisplayBrowsingHistory"))."</div>
							".$this->Context->ObjectFactory->RenderControlStrings("FunctionalityForm", "RenderPreferences")."
						</div>
						<h2>".$this->Context->GetDefinition("DiscussionIndex")."</h2>
						<div class=\"InputBlock\">
							<div class=\"CheckBox\">".GetDynamicCheckBox("JumpToLastReadComment", 1, $this->Context->Session->User->Setting("JumpToLastReadComment", 1), "PanelSwitch('JumpToLastReadComment');", $this->Context->GetDefinition("JumpToLastReadComment"))."</div>
						</div>
						<h2>".$this->Context->GetDefinition("CommentsForm")."</h2>
						<div class=\"InputBlock\">
							<div class=\"CheckBox\">".GetDynamicCheckBox("ShowLargeCommentBox", 1, $this->Context->Session->User->Setting("ShowLargeCommentBox"), "PanelSwitch('ShowLargeCommentBox');", $this->Context->GetDefinition("ShowLargeCommentBox"))."</div>
							<div class=\"CheckBox\">".GetDynamicCheckBox("ShowFormatSelector", 1, $this->Context->Session->User->Setting("ShowFormatSelector", 1), "PanelSwitch('ShowFormatSelector');", $this->Context->GetDefinition("ShowFormatTypeSelector"))."</div>");
							if ($FormatCount > 1) {
								$this->Context->Writer->Write("<div class=\"InputNote\">
									".$this->Context->GetDefinition("ChooseDefaultFormatType")."</div>
									".$f->Get());
							}
						$this->Context->Writer->Write("</div>");
						if ($this->Context->Session->User->AdminUsers) {
							$this->Context->Writer->Write("<h2>".$this->Context->GetDefinition("NewUsers")."</h2>
							<div class=\"InputBlock\">
								<div class=\"CheckBox\">".GetDynamicCheckBox("SendNewApplicantNotifications", 1, $this->Context->Session->User->SendNewApplicantNotifications, "PanelSwitch('SendNewApplicantNotifications');", $this->Context->GetDefinition("NewApplicantNotifications"))."</div>
							</div>");
						}
						if ($this->Context->Session->User->AdminCategories) {
							$this->Context->Writer->Write("<h2>".$this->Context->GetDefinition("HiddenInformation")."</h2>
							<div class=\"InputBlock\">
								<div class=\"CheckBox\">".GetDynamicCheckBox("ShowDeletedDiscussions", 1, $this->Context->Session->User->Setting("ShowDeletedDiscussions"), "PanelSwitch('ShowDeletedDiscussions');", $this->Context->GetDefinition("DisplayHiddenDiscussions"))."</div>
								<div class=\"CheckBox\">".GetDynamicCheckBox("ShowDeletedComments", 1, $this->Context->Session->User->Setting("ShowDeletedComments"), "PanelSwitch('ShowDeletedComments');", $this->Context->GetDefinition("DisplayHiddenComments"))."</div>
							</div>");
						}
						$this->Context->Writer->Write($this->Context->ObjectFactory->RenderControlStrings("FunctionalityForm", "RenderCustomPreferences")
						."</form>
					</div>
				</div>");
			}
		}
	}
}

// Options for the control panel
function AddAccountOptionsToPanel(&$Context, &$Panel, &$User) {
	if ($Context->Session->UserID > 0) {
		$ApplicantOptions = $Context->GetDefinition("ApplicantOptions");
		$AccountOptions = $Context->GetDefinition("AccountOptions");
		if (($User->UserID == $Context->Session->UserID || $Context->Session->User->AdminUsers) && $User) {
			if ($User->UserID == $Context->Session->UserID) {
				$Panel->AddListItem($AccountOptions, $Context->GetDefinition("ChangeYourPersonalInformation"), $Context->SelfUrl."?PostBackAction=Identity", "", 0);
				$Panel->AddListItem($AccountOptions, $Context->GetDefinition("ChangeYourPassword"), $Context->SelfUrl."?PostBackAction=Password", "", 1);				
			} elseif ($User->UserID != $Context->Session->UserID && $Context->Session->User->AdminUsers && $User) {
				$Username = FormatPossessive($User->Name);
				$Panel->AddListItem($Username." ".$AccountOptions, $Context->GetDefinition("ChangePersonalInformation"), $Context->SelfUrl."?PostBackAction=Identity&u=".$User->UserID, "", 0);
				if ($User->RoleID == 0) {
					$Panel->AddListItem($ApplicantOptions, $Context->GetDefinition("ApproveForMembership"), $Context->SelfUrl."?u=".$User->UserID."&PostBackAction=ApproveUser");
					$Panel->AddListItem($ApplicantOptions, $Context->GetDefinition("DeclineForMembership"), $Context->SelfUrl."?u=".$User->UserID."&PostBackAction=DeclineUser");
				} else {
					$Panel->AddListItem($Username." ".$AccountOptions, $Context->GetDefinition("ChangeRole"), $Context->SelfUrl."?PostBackAction=Role&u=".$User->UserID);
					$Panel->AddListItem($ApplicantOptions, $Context->GetDefinition("NewApplicantSearch"), "search.php?PostBackAction=Search&Keywords=roles:Applicant;sort:Date;&Type=Users");
				}
			}
		}
		if ($User->UserID == $Context->Session->UserID) {
			$Panel->AddListItem($AccountOptions, $Context->GetDefinition("ChangeForumFunctionality"), $Context->SelfUrl."?PostBackAction=Functionality", "", 2);
		}		
	}
}

class RoleForm extends PostBackControl {
	var $User;
	var $RoleSelect;
	
	function RoleForm (&$Context, &$UserManager, $User) {
		$this->ValidActions = array("ApproveUser", "DeclineUser", "Role", "ProcessRole");
		$this->Constructor($Context);
		if ($this->IsPostBack) {
			$this->User = &$User;
			if ($this->Context->Session->User->AdminUsers && $this->Context->Session->UserID != $User->UserID) {
				$Redirect = 0;
				if ($this->PostBackAction == "ApproveUser") {
					$UserManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
					if ($UserManager->ApproveApplicant($User->UserID)) $Redirect = 1;
				} elseif ($this->PostBackAction == "DeclineUser") {
					$UserManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
					if ($UserManager->RemoveApplicant($User->UserID)) $Redirect = 1;
				} elseif ($this->PostBackAction == "ProcessRole") {
					$urh = $this->Context->ObjectFactory->NewObject($this->Context, "UserRoleHistory");
					$urh->GetPropertiesFromForm();
					if ($UserManager->AssignRole($urh)) $Redirect = 1;
				}
				if ($Redirect) {
					if ($this->PostBackAction == "DeclineUser") {
						// Send back to the applicants
						header("location: search.php?PostBackAction=Search&Keywords=roles:Applicant;sort:Date;&Type=Users");
					} else {
						header("location: ".$this->Context->SelfUrl."?u=".$User->UserID);
					}
					die();
				} else {
					$this->PostBackAction = str_replace("Process", "", $this->PostBackAction);
				}
				if ($this->PostBackAction == "Role") {
					$RoleManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "RoleManager");
					$RoleData = $RoleManager->GetRoles();
	
					$this->RoleSelect = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
					$this->RoleSelect->Name = "RoleID";
					$this->RoleSelect->CssClass = "PanelInput";
					$this->RoleSelect->AddOptionsFromDataSet($this->Context->Database, $RoleData, "RoleID", "Name");
					$this->RoleSelect->SelectedID = $this->User->RoleID;	
				}
			}
		}
	}
	
	function Render() {
		if ($this->PostBackAction == "Role") {
			if (!$this->Context->Session->User->AdminUsers) {
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("PermissionError"));
				$this->Context->Writer->Add("<div class=\"AccountForm\">
					".$this->Get_Warnings()."
				</div>");				
			} else {				
				$this->PostBackParams->Set("PostBackAction", "ProcessRole");
				$this->PostBackParams->Set("u", $this->User->UserID);
				$Required = $this->Context->GetDefinition("Required");
				
				$this->Context->Writer->Add("<div class=\"AccountForm\">
					<h1>".$this->Context->GetDefinition("ChangeRole")."</h1>
					<div class=\"Form AccountRole\">
						".$this->Get_Warnings()."
						".$this->Get_PostBackForm("frmRole")."
						<dl>
							<dt>".$this->Context->GetDefinition("AssignToRole")."</dt>
							<dd>".$this->RoleSelect->Get()." ".$Required."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("AssignToRoleNotes")."</div>
						<dl>
							<dt>".$this->Context->GetDefinition("RoleChangeInfo")."</dt>
							<dd><input type=\"text\" name=\"Notes\" value=\"\" class=\"PanelInput\" /> ".$Required."</dd>
						</dl>
						<div class=\"InputNote\">".$this->Context->GetDefinition("RoleChangeInfoNotes")."</div>
						<div class=\"FormButtons\">
							<input type=\"submit\" name=\"btnSave\" value=\"".$this->Context->GetDefinition("ChangeRole")."\" class=\"Button SubmitButton\" />
							<a href=\"./account.php?u=".$this->User->UserID."\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
						</div>
						</form>
					</div>
				</div>");
			}
		}
	}
}

?>
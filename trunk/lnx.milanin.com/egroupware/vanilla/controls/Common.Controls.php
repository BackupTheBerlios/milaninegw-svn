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
* Description: Controls common to various pages in the application
*/

// Page body control collection
class Body extends ControlCollection {
	var $CssClass;
	
	function Body(&$Context) {
		$this->Context = &$Context;
	}
	
   function Prefix() {
		if ($this->CssClass != "") $this->CssClass = " ".$this->CssClass;
		return "<div class=\"PageBodyShadow\"><div class=\"PageBody".$this->CssClass."\" id=\"Body\">";
   }
	
   function Suffix() {
      return "</div></div>";
   }
}

class DiscussionForm extends PostBackControl {
	var $FatalError;		// If a fatal error occurs, only the warning messages should be displayed
	var $EditDiscussionID;
   var $Discussion;
	var $DiscussionFormattedForDisplay;
	var $DiscussionID;
	var $Comment;
	var $CommentID;
	var $Form;
	var $Title;				// The title of the form
	
	function DiscussionForm(&$Context) {
		$this->FatalError = 0;
		$this->EditDiscussionID = 0;
		$this->Context = &$Context;
		$this->Constructor($this->Context);
		$this->CommentID = ForceIncomingInt("CommentID", 0);
		$this->DiscussionID = ForceIncomingInt("DiscussionID", 0);
		$this->DiscussionFormattedForDisplay = 0;
	}
	
	function LoadData() {
		// Check permissions and make sure that the user can add comments/discussions
      // Make sure user can post
		if ($this->DiscussionID == 0 && $this->Context->Session->UserID == 0) {
			$this->Context->WarningCollector->Add($this->Context->GetDefinition("NoDiscussionsNotSignedIn"));
			$this->FatalError = 1;
		}

		$this->Comment = $this->Context->ObjectFactory->NewObject($this->Context, "Comment");
		$this->Discussion = $this->Context->ObjectFactory->NewObject($this->Context, "Discussion");
		
		$cm = $this->Context->ObjectFactory->NewContextObject($this->Context, "CommentManager");
		$dm = $this->Context->ObjectFactory->NewContextObject($this->Context, "DiscussionManager");
		$PrivateDiscussion = 0;

		// If editing a comment, define it and validate the user's permissions
		if ($this->CommentID > 0) {
			$this->Comment = $cm->GetCommentById($this->CommentID, $this->Context->Session->UserID);
			if (!$this->Comment) {
				$this->FatalError = 1;
			} else {
				$this->DiscussionID = $this->Comment->DiscussionID;
				$this->Discussion = $dm->GetDiscussionById($this->Comment->DiscussionID);
				if (!$this->Discussion) {
					$this->FatalError = 1;
				} else {
					if ($this->Discussion->WhisperUserID > 0) $PrivateDiscussion = 1;
				
					// if editing a discussion
					if (($this->Context->Session->UserID == $this->Discussion->AuthUserID || $this->Context->Session->User->AdminCategories) && $this->Discussion->FirstCommentID == $this->CommentID) {
						$this->EditDiscussionID = $this->Discussion->DiscussionID;
						$this->Discussion->Comment = $this->Comment;
					}
					// Set the page title
               $this->DiscussionFormattedForDisplay = 1;
					$this->Discussion->FormatPropertiesForDisplay();
					$this->Context->PageTitle = $this->Discussion->Name;
				}
			}
			// Ensure that this user has sufficient priviledges to edit the comment
			if ($this->Comment
				&& $this->Discussion
				&& !$this->Context->Session->User->AdminCategories
				&& $this->Context->Session->UserID != $this->Comment->AuthUserID) {
				$this->Context->WarningCollector->Add($this->Context->GetDefinition("ErrPermissionCommentEdit"));
				$this->FatalError = 1;
			}
		} elseif ($this->DiscussionID > 0 && $this->PostBackAction == "Reply") {
			$this->Comment->GetPropertiesFromForm($this->Context);
		}
	// If saving a discussion
		if ($this->PostBackAction == "SaveDiscussion") {
			$this->Discussion->Clear();
			$this->Discussion->GetPropertiesFromForm($this->Context);
			// If we are editing a discussion, the following line
			// will make sure we save the proper discussion topic & message
			$this->Discussion->DiscussionID = $this->EditDiscussionID;
			
			$ResultDiscussion = $dm->SaveDiscussion($this->Discussion);
			if ($ResultDiscussion) {
				// Saved successfully, so send back to the discussion
				header("location:comments.php?DiscussionID=".$ResultDiscussion->DiscussionID);
				die();
			}
		} elseif ($this->PostBackAction == "SaveComment") {
			$this->Comment->Clear();
			$this->Comment->GetPropertiesFromForm($this->Context);
			$this->Comment->DiscussionID = $this->DiscussionID;
			$this->Discussion = $dm->GetDiscussionById($this->Comment->DiscussionID);
			$ResultComment = $cm->SaveComment($this->Comment);
			if ($ResultComment) {
				// Saved successfully, so send back to the discussion
            // print_r($this->Discussion);
				header("location:comments.php?DiscussionID=".$ResultComment->DiscussionID."&page=".$this->Discussion->LastPage.($ResultComment->CommentID > 0 ? "#Comment_".$ResultComment->CommentID:"#pgbottom"));
				die();
			}
		} 
		
		if (!$this->IsPostBack && $this->Comment->DiscussionID == 0 && $this->Comment->CommentID == 0) {
			if (!$this->Discussion->Comment) $this->Discussion->Comment = $this->Context->ObjectFactory->NewObject($this->Context, "Comment");
			$this->Discussion->Comment->FormatType = $this->Context->Session->User->DefaultFormatType;
			$this->Comment->FormatType = $this->Context->Session->User->DefaultFormatType;
		}
		
		$this->PostBackParams->Set("CommentID", $this->Comment->CommentID);
		$this->PostBackParams->Set("DiscussionID", $this->DiscussionID);
		$this->Title = $this->Context->GetDefinition("StartANewDiscussion");
		if ($this->EditDiscussionID > 0 || ($this->CommentID == 0 && $this->DiscussionID == 0)) {
			$this->Form = $this->GetDiscussionForm($this->Discussion);
		} else {
			$this->Form = $this->GetCommentForm($this->Comment, $PrivateDiscussion);
			if ($this->Comment->CommentID > 0) {
				$this->Title = $this->Context->GetDefinition("EditYourComments");
			} else {
				$this->Title = $this->Context->GetDefinition("AddYourComments");
			}
		}
	}
	
	function GetCommentForm($Comment, $PrivateDiscussion = "0") {
		// Encode everything properly
      $Comment->FormatPropertiesForDisplay($this->Context, 1);
		
		$PrivateDiscussion = ForceBool($PrivateDiscussion, 0);
		$this->PostBackParams->Set("PostBackAction", "SaveComment");
		$this->PostBackParams->Set("UserCommentCount", $this->Context->Session->User->CountComments);
		$sReturn = "<div class=\"CommentForm\">"
				.$this->Get_PostBackForm("frmPostComment", "post", "post.php")
				.$this->Get_Warnings()
				."<dl>"
					.$this->Context->ObjectFactory->RenderControlStrings("DiscussionForm", "GetCommentForm")
					./*"<dt class=\"CommentInputLabel\">
						".$this->Context->GetDefinition("EnterYourComments")."
						<a id=\"CommentBoxController\" href=\"Javascript:ToggleCommentBox();\">".($this->Context->Session->User->Setting("ShowLargeCommentBox")?$this->Context->GetDefinition("SmallInput"):$this->Context->GetDefinition("BigInput"))."</a>
					</dt>*/"
					<dd class=\"CommentInput\">
						<textarea name=\"Body\" class=\"".($this->Context->Session->User->Setting("ShowLargeCommentBox")?"LargeCommentBox":"SmallCommentBox")."\" id=\"CommentBox\">".$Comment->Body."</textarea>"
						.$this->GetPostFormatting($Comment->FormatType)
					."</dd>"
				."</dl>
				<div class=\"FormButtons CommentButtons\">
					<input type=\"button\" name=\"btnSave\" value=\"".($Comment->CommentID > 0?$this->Context->GetDefinition("SaveYourChanges"):$this->Context->GetDefinition("AddYourComments"))."\" class=\"Button SubmitButton\" onclick=\"SubmitForm('frmPostComment', this);\" />";
					if ($this->PostBackAction == "SaveComment" || ($this->PostBackAction == "" && $Comment->CommentID > 0)) {
						if ($this->Comment->DiscussionID > 0) {
							$sReturn .= "<a href=\"./comments.php?DiscussionID=".$this->Comment->DiscussionID."\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>";
						} else {
							$sReturn .= "<a href=\"./\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>";
						}
					}
				$sReturn .= "</div>
				</form>			
			</div>";
		return $sReturn;
	}
	
	function GetDiscussionForm($Discussion) {
		if (!$this->DiscussionFormattedForDisplay) $Discussion->FormatPropertiesForDisplay();
		$Discussion->Comment->FormatPropertiesForDisplay($this->Context, 1);
		
		// Load the category selector
		$cm = $this->Context->ObjectFactory->NewContextObject($this->Context, "CategoryManager");
		$CategoryData = $cm->GetCategories(0, 1);
		$cs = $this->Context->ObjectFactory->NewObject($this->Context, "Select");
		$cs->Name = "CategoryID";
		$cs->CssClass = "CategorySelect";
		$cs->SelectedID = ForceIncomingInt("CategoryID", $Discussion->CategoryID);
		$cat = $this->Context->ObjectFactory->NewObject($this->Context, "Category");
		$LastBlocked = -1;
		while ($Row = $this->Context->Database->GetRow($CategoryData)) {
			$cat->Clear();
			$cat->GetPropertiesFromDataSet($Row);
			$cat->FormatPropertiesForDisplay();
			if ($cat->Blocked != $LastBlocked && $LastBlocked != -1) {
				$cs->AddOption("-1", "---", " disabled=\"true\"");
			}
			$cs->AddOption($cat->CategoryID, $cat->Name);
			$LastBlocked = $cat->Blocked;
		}
		
		$this->PostBackParams->Set("CommentID", $Discussion->FirstCommentID);
		$this->PostBackParams->Set("UserDiscussionCount", $this->Context->Session->User->CountDiscussions);
		$this->PostBackParams->Set("PostBackAction", "SaveDiscussion");
		
		
		$sReturn = "
		<script>
		var wac = new AutoComplete('wac');
		</script>
		
		<div class=\"DiscussionForm\">"
			.$this->Get_PostBackForm("frmPostDiscussion", "post", "post.php")
			.$this->Get_Warnings()
			."<dl>";
			if (agUSE_CATEGORIES) {
				$sReturn .= "<dt class=\"CategoryInputLabel\">".$this->Context->GetDefinition("SelectDiscussionCategory")."</dt>
				<dd class=\"CategoryInput\">".$cs->Get()."</dd>";
			} else {
				$sReturn .= "<input type=\"hidden\" name=\"CategoryID\" value=\"".$cs->aOptions[0]["IdValue"]."\" />";
			}
			$sReturn .= "<dt class=\"TopicInputLabel\">".$this->Context->GetDefinition(($Discussion->DiscussionID == 0?"EnterYourDiscussionTopic":"EditYourDiscussionTopic"))."</dt>
				<dd class=\"TopicInput\"><input type=\"text\" name=\"Name\" class=\"DiscussionBox\" maxlength=\"100\" value=\"".$Discussion->Name."\" /></dd>";
			
			$sReturn .= $this->Context->ObjectFactory->RenderControlStrings("DiscussionForm", "GetDiscussionForm");
			$sReturn .= "<dt class=\"CommentInputLabel\">".$this->Context->GetDefinition(($Discussion->DiscussionID == 0?"EnterYourComments":"EditYourComments"))."
					<a id=\"CommentBoxController\" href=\"Javascript:ToggleCommentBox();\" onmouseover=\"window.status='';return true;\">".$this->Context->GetDefinition($this->Context->Session->User->Setting("ShowLargeCommentBox")?"SmallInput":"BigInput")."</a>
				</dt>
				<dd class=\"CommentInput\">
					<textarea name=\"Body\" class=\"".($this->Context->Session->User->Setting("ShowLargeCommentBox")?"LargeCommentBox":"SmallCommentBox")."\" id=\"CommentBox\">".$Discussion->Comment->Body."</textarea>"
					.$this->GetPostFormatting($Discussion->Comment->FormatType)
				."</dd>
			</dl>
			<div class=\"FormButtons DiscussionButtons\">
				<input type=\"button\" name=\"btnSave\" value=\"".$this->Context->GetDefinition(($Discussion->DiscussionID > 0) ? "SaveYourChanges" : "StartYourDiscussion")."\" class=\"Button SubmitButton\" onclick=\"SubmitForm('frmPostDiscussion', this);\" />
				<a href=\"".($Discussion->DiscussionID == 0?"javascript:history.back();":"./comments.php?DiscussionID=".$Discussion->DiscussionID)."\" class=\"CancelButton\">".$this->Context->GetDefinition("Cancel")."</a>
			</div>
			</form>
		</div>";
		return $sReturn;
	}
	
	function GetPostFormatting($SelectedFormatType) {
		/*$FormatCount = count($this->Context->StringManipulator->Formatters);
		if ($this->Context->Session->User->Setting("ShowFormatSelector", 1) && $FormatCount > 1) {
			$f = $this->Context->ObjectFactory->NewObject($this->Context, "Radio");
			$f->Name = "FormatType";
			$f->CssClass = "FormatTypeRadio";
			$f->SelectedID = $SelectedFormatType;
			while (list($Name, $Object) = each($this->Context->StringManipulator->Formatters)) {
				$f->AddOption($Name, $this->Context->GetDefinition($Name));
			}
			return "<div class=\"FormatType\">".$this->Context->GetDefinition("FormatCommentsAs")
				.$f->Get()
			."</div>";
		} else {
			$FormatTypeToUse = $this->Context->Session->User->DefaultFormatType;
			if (!array_key_exists($FormatTypeToUse, $this->Context->StringManipulator->Formatters)) {
				$FormatTypeToUse = agDEFAULTSTRINGFORMAT;
			}
		*/	
			return "<input type=\"hidden\" name=\"FormatType\" value=\"Html\" />";
		//}
	}
	
	function Render() {
		if ($this->FatalError) {
			$this->Render_Warnings();
		} else {
			$this->Context->Writer->Write($this->Form);
		}
	}
}

// Writes the page footer
class Foot extends Control {
	var $CssClass;
	
	function Foot(&$Context, $CssClass = "") {
		$this->Context = &$Context;
		if ($CssClass != "") $this->CssClass = " ".$CssClass;
	}
	
	function Render() {
		$this->Context->Writer->Add("</div>
		</div>
		<div class=\"Foot".$this->CssClass."\">".
			/*<div class=\"Links\">
				<a href=\"feeds/\">".$this->Context->GetDefinition("Feeds")."</a>
				| <a href=\"bugreport.php\">".$this->Context->GetDefinition("ReportABug")."</a>
				| <a href=\"javascript:PopTermsOfService();\">".$this->Context->GetDefinition("TermsOfService")."</a>
				| <a href=\"http://lussumo.com/docs/\" target=\"_blank\">".$this->Context->GetDefinition("Documentation")."</a>
			</div>*/
			"<div class=\"Copyright\"><a href=\"http://lussumo.com\">Lussumo Vanilla (".agVANILLA_VERSION.")</a> ".$this->Context->GetDefinition("Copyright")."</div>
		</div>");
		$IsAdmin = 0;
		if ($this->Context->Session->User) {
			if ($this->Context->Session->User->MasterAdmin) $IsAdmin = 1;
		}
//		if ($this->Context->Mode == agMODE_DEBUG && $IsAdmin) {
		if ($this->Context->Mode == agMODE_DEBUG) {
			$this->Context->Writer->Add("<div class=\"DebugBar\" id=\"DebugBar\">
			<b>Debug Options</b> | Resize: <a href=\"javascript:window.resizeTo(800,600);\">800x600</a>, <a href=\"javascript:window.resizeTo(1024, 768);\">1024x768</a> | <a href=\"javascript:HideElement('DebugBar');\">Hide This</a>");
			$this->Context->Writer->Add($this->Context->SqlCollector->GetMessages());
			$this->Context->Writer->Add("</div>");
		}
		$this->Context->Writer->Write();
	}
}

// Writes out the page head
class Head extends Control {
	var $Scripts;			// Script collection
	var $StyleSheets;		// Stylesheet collection
   var $Strings;			// String collection
	
	function AddScript($ScriptLocation) {
		if (!is_array($this->Scripts)) $this->Scripts = array();
		$this->Scripts[] = $ScriptLocation;
	}
	
	function AddStyleSheet($StyleSheetLocation, $Media = "") {
		if (!is_array($this->StyleSheets)) $this->StyleSheets = array();
		$this->StyleSheets[] = array("Sheet" => $StyleSheetLocation, "Media" => $Media);
	}
	
	function AddString($String) {
		if (!is_array($this->Strings)) $this->Strings = array();
		$this->Strings[] = $String;
	}

	function Head(&$Context) {
		$this->Context = &$Context;
	}
	
   function Render() {
      $this->Context->Writer->Add("<".chr(63)."xml version=\"1.0\" encoding=\"utf-8\"".chr(63).">
      <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
      <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-ca\">
         <head>
            <title>".agAPPLICATION_TITLE." - ".$this->Context->PageTitle."</title>
            <link rel=\"shortcut icon\" href=\"/favicon.ico\" />");
				if (is_array($this->StyleSheets)) {
					$StyleSheetCount = count($this->StyleSheets);
					for ($i = 0; $i < $StyleSheetCount; $i++) {
						$this->Context->Writer->Add("\r\n<link rel=\"stylesheet\" type=\"text/css\" href=\"".$this->StyleSheets[$i]["Sheet"]."\"".($this->StyleSheets[$i]["Media"] == ""?"":" media=\"".$this->StyleSheets[$i]["Media"]."\"")." />");
					}
				}
				if (is_array($this->Scripts)) {
					$ScriptCount = count($this->Scripts);
					for ($i = 0; $i < $ScriptCount; $i++) {
						$this->Context->Writer->Add("\r\n<script type=\"text/javascript\" src=\"".$this->Scripts[$i]."\"></script>");
					}
				}
				if (is_array($this->Strings)) {
					$StringCount = count($this->Strings);
					for ($i = 0; $i < $StringCount; $i++) {
						$this->Context->Writer->Add($this->Strings[$i]);
					}
				}
      $this->Context->Writer->Write("</head>
         <body".$this->Context->BodyAttributes.
          '><table align="center" table width="898"  border="0" cellpadding="0" cellspacing="0"><tr><td>'."\n");
   }
}

// The Menu control handles building the main menu
class Menu extends Control {
	var $Tabs;				// Tab collection
   var $CurrentTab;		// The current tab
	
	function AddTab($Text, $Value, $Url, $CssClass, $Attributes = "", $Position = "0", $ForcePosition = "0") {
		$this->AddItemToCollection($this->Tabs, array("Text" => $Text, "Value" => $Value, "Url" => $Url, "CssClass" => $CssClass, "Attributes" => $Attributes), $Position, $ForcePosition);
	}
	
	function ClearTabs() {
		$this->Tabs = array();
	}
	
	function FormatTab($Text, $Value, $Url, $CssClass, $Attributes = "") {
		return "<li><a class=\"".$this->TabClass($this->CurrentTab, $Value)." ".$CssClass."\" href=\"".$Url."\" ".$Attributes.">".$Text."</a></li>";
	}
	
	function Menu(&$Context) {
		$this->Context = &$Context;
		$this->ClearTabs();
	}
	
	function TabClass($CurrentTab, $ComparisonTab) {
		return ($CurrentTab == $ComparisonTab) ? "TabOn" : "TabOff";
	}
	
   function Render() {
		// First sort the tabs by key
      ksort($this->Tabs);
		// Now write the Menu
      $this->Context->Writer->Add("<div class=\"SiteContainer\">
				<a name=\"pgtop\"></a>
				<div id=\"LoadStatus\" style=\"display: none;\">Loading...</div>
				
				<div class=\"Head\">
					<div class=\"Logo\">".agBANNER_TITLE."</div>
					<ul id=\"MenuForum\">");
					while (list($Key, $Tab) = each($this->Tabs)) {
						$this->Context->Writer->Add($this->FormatTab($Tab["Text"], $Tab["Value"], $Tab["Url"], $Tab["CssClass"], $Tab["Attributes"]));
					}
					$this->Context->Writer->Add("</ul>
				</div>
				<div class=\"Body\">");
		$this->Context->Writer->Write();
   }
}

// Panel control collection
class Panel extends Control {
	var $CssClass;			// The CSS Class to be applied to the containing panel element
	var $Lists;				// A collection of list items to be placed in the panel
	var $Strings;			// A collection of customized strings to be placed in the panel
	var $PanelElements;	// A collection of elements to be placed in the panel (strings, lists, etc)
   var $NewDiscussionText;	// The text that appears in the "start a new discussion" button
   var $NewDiscussionAttributes; // Attributes to add to the "start a new discussion" link
	
	function Panel(&$Context) {
		$this->Lists = array();
		$this->Strings = array();
		$this->PanelElements = array();
		$this->Context = &$Context;
		$this->NewDiscussionText = "";
		$this->NewDiscussionAttributes = "";
	}
	
	function AddList($ListName, $AddToPanelStart = "0") {
		$AddToPanelStart = ForceBool($AddToPanelStart, 0);
		if (!array_key_exists($ListName, $this->Lists)) {
			if ($AddToPanelStart) {
				array_unshift($this->PanelElements, array("Type" => "List", "Key" => $ListName));
			} else {
				$this->PanelElements[] = array("Type" => "List", "Key" => $ListName);
			}
			$this->Lists[$ListName] = array();
		}
	}
	
	// ListName is the name of the list you want to add this item to (if the list does not exist, it will be created)
	function AddListItem($ListName, $Item, $Link, $Suffix = "", $Position = "", $LinkAttributes = "") {
		$this->AddList($ListName);
		$Position = is_numeric($Position) ? $Position : -1;
		$ListItem = array("Item" => $Item, "Link" => $Link, "Suffix" => $Suffix, "LinkAttributes" => $LinkAttributes);
		if ($Position >= 0 && $Position <= count($this->Lists[$ListName])) {
			$this->InsertItemAt($this->Lists[$ListName], $ListItem, $Position);
		} else {
			$this->Lists[$ListName][] = $ListItem;
		}
	}
	
	function InsertItemAt(&$Collection, $ListItem, $Position) {
		if (array_key_exists($Position, $Collection)) {
			$this->InsertItemAt($Collection, $Collection[$Position], $Position+1);
		}
		$Collection[$Position] = $ListItem;
	}
	
	function AddString($String, $AddToPanelStart = "0") {
		$AddToPanelStart = ForceBool($AddToPanelStart, 0);
		$StringKey = count($this->Strings);
		$this->Strings[] = $String;
		if ($AddToPanelStart) {
			array_unshift($this->PanelElements, array("Type" => "String", "Key" => $StringKey));
		} else {
			$this->PanelElements[] = array("Type" => "String", "Key" => $StringKey);
		}		
	}
	
   function Render() {
		if ($this->CssClass != "") $this->CssClass = " ".$this->CssClass;
      $this->Context->Writer->Write("<div class=\"PanelShadow\">\n<div class=\"Panel".$this->CssClass."\" id=\"Panel\">");
		
      if ($this->Context->Session->UserID > 0) {
	      $CategoryID = ForceIncomingInt("CategoryID", 0);
	      $this->Context->Writer->Write("<div class=\"Session\">");
		if ($this->Context->Session->UserID > 0) {
                  $this->Context->Writer->Add($this->Context->GetDefinition("SignedInAs")." ".$this->Context->Session->User->Name."<br/> (<a href=\"leave.php\">".$this->Context->GetDefinition("SignOut")."</a>)");
                } else {
                  $this->Context->Writer->Add($this->Context->GetDefinition("NotSignedIn")." (<a href=\"signin.php\">".$this->Context->GetDefinition("SignIn")."</a>)");
                }
                $this->Context->Writer->Add("</div>");
         $this->Context->Writer->Write("<a class=\"PanelButton StartDiscussionButton\" href=\"post.php".($CategoryID > 0?"?CategoryID=".$CategoryID:"")."\" ".$this->NewDiscussionAttributes.">".($this->NewDiscussionText == ""?$this->Context->GetDefinition("StartANewDiscussion"):$this->NewDiscussionText)."</a>");
      }
		for ($i = 0; $i < count($this->PanelElements); $i++) {
			$Type = $this->PanelElements[$i]["Type"];
			$Key = $this->PanelElements[$i]["Key"];
			if ($Type == "List") {
				$Links = $this->Lists[$Key];
				if (count($Links) > 0) {
					$this->Context->Writer->Write("<h2>".$Key."</h2>
					<ul class=\"LinkedList\">");
					for ($j = 0; $j < count($Links); $j++) {
						$this->Context->Writer->Add("<li><a class=\"PanelLink\" href=\"".$Links[$j]["Link"]."\" ".$Links[$j]["LinkAttributes"].">".$this->Context->GetDefinition($Links[$j]["Item"])."</a>");
						if ($Links[$j]["Suffix"] != "") $this->Context->Writer->Add("<small><strong>".$this->Context->GetDefinition($Links[$j]["Suffix"])."</strong></small>");
						$this->Context->Writer->Add("</li>");
					}
					$this->Context->Writer->Write("</ul>");
				}
			} elseif ($Type == "String") {
				$this->Context->Writer->Add($this->Strings[$Key]);
			}
		}
		
      $this->Context->Writer->Write("</div></div>");
   }
}

// Ends the page body
class PageEnd extends Control {
	function Render() {
		$this->Context->Writer->Write("</td></tr></table></body>
		</html>");
	}
}

class ExternalBody extends ControlCollection {
	var $CssClass;
	function ExternalBody(&$Context) {
		$this->Context = &$Context;
	}
	function Prefix() {
		if ($this->CssClass != "") $this->CssClass = " ".$this->CssClass;
		return "<div class=\"SiteContainer".$this->CssClass."\">
			<div class=\"PageTitle\">".agBANNER_TITLE."</div>
			<div class=\"FormContainer\">";
	}
	function Suffix() {
		return "</div>
		</div>";
	}
}
// Writes the page footer
class ExternalFoot extends Control {
	var $CssClass;
	
	function ExternalFoot(&$Context, $CssClass = "") {
		$this->Context = &$Context;
	}
	
	function Render() {
		if ($this->CssClass != "") $this->CssClass = " ".$this->CssClass;
		$this->Context->Writer->Add("<div class=\"Foot".$this->CssClass."\">
			<div class=\"Copyright\"><a href=\"http://lussumo.com\">Lussumo Vanilla</a> Copyright &copy; 2001-2005</div>
		</div>");
		$IsAdmin = 0;
		if ($this->Context->Session->User) {
			if ($this->Context->Session->User->MasterAdmin) $IsAdmin = 1;
		}
		if ($this->Context->Mode == agMODE_DEBUG && $IsAdmin) {
			$this->Context->Writer->Add("<div class=\"DebugBar\" id=\"DebugBar\">
			<b>Debug Options</b> | Resize: <a href=\"javascript:window.resizeTo(800,600);\">800x600</a>, <a href=\"javascript:window.resizeTo(1024, 768);\">1024x768</a> | <a href=\"javascript:HideElement('DebugBar');\">Hide This</a>");
			$this->Context->Writer->Add($this->Context->SqlCollector->GetMessages());
			$this->Context->Writer->Add("</div>");
		}
		$this->Context->Writer->Write();
	}
}
?>
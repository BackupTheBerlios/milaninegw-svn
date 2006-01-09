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
  
// Add a new custom name/value pair input to the account form
function AddLabelValuePair() {
	var frm = document.frmAccountPersonal;
	var Counter = document.frmAccountPersonal.LabelValuePairCount;
	var Container = document.getElementById("LabelValuePairContainer");
	if (frm && Counter && Container) {
		Counter.value++;

		// Create the label container
		var Label = document.createElement("dt");
		Label.className = "DefinitionItem";
		
		// Create the label input
		var LabelInput = document.createElement("input");
		LabelInput.type = "text";
		LabelInput.name = "Label"+Counter.value;
		LabelInput.maxLength = "20";
		LabelInput.className = "LVLabelInput";
		
		// Create the value container		
		var Value = document.createElement("dd");
		Value.className = "DefinitionItem";

		// Create the value input
		var ValueInput = document.createElement("input");
		ValueInput.type = "text";
		ValueInput.name = "Value"+Counter.value;
		ValueInput.maxLength = "200";
		ValueInput.className = "LVValueInput";
		
		// Add the items to the page
		Label.appendChild(LabelInput);
		Value.appendChild(ValueInput);
		Container.appendChild(Label);
		Container.appendChild(Value);
	}
}

// Assign a category to a discussion
function AssignCategory(Sender, DiscussionID) {
	ChangeLoaderText("Processing...");
	SwitchLoader(1);
	var dm = new DataManager();
	dm.RequestCompleteEvent = ProcessCategory;
	dm.RequestFailedEvent = HandleFailure;
	dm.LoadData("./tools/categorize.php?DiscussionID="+DiscussionID+"&CategoryID="+Sender.options[Sender.selectedIndex].value);
}

function DiscussionSwitch(SwitchType, DiscussionID, SwitchValue) {
	ChangeLoaderText("Processing...");
	SwitchLoader(1);
	var dm = new DataManager();
	dm.RequestCompleteEvent = HandleDiscussionSwitch;
	dm.RequestFailedEvent = HandleDiscussionSwitch;
	dm.LoadData("./tools/switch.php?Type="+SwitchType+"&DiscussionID="+DiscussionID+"&Switch="+SwitchValue);
}

// Insert clipboard data
function GetClipping(ClippingSelect) {
	var ClippingID = ClippingSelect.options[ClippingSelect.selectedIndex].value;
	ClippingSelect.selectedIndex = 0;
   ChangeLoaderText("Loading...");
   SwitchLoader(1);
	var dm = new DataManager();
	dm.RequestCompleteEvent = InsertClipping;
	dm.RequestFailedEvent = HandleFailure;
	dm.LoadData("./tools/getclipping.php?c="+ClippingID);
}

function HandleFailure(Request) {
	ChangeLoaderText("Failed");
	CloseLoader();
}

function HandleSwitch(Request) {
	ChangeLoaderText("Complete");
	CloseLoader();
}

function HandleDiscussionSwitch(Request) {
	ChangeLoaderText("Refreshing...");
	setTimeout("document.location.reload();",600);
}

function InsertClipping(Request) {
	ChangeLoaderText("Complete");
	var CommentBox;
	if (document.frmPostComment) CommentBox = document.frmPostComment.Body;
	if (document.frmPostDiscussion) CommentBox = document.frmPostDiscussion.Body;
	if (CommentBox) CommentBox.value += Request.responseText;
	CloseLoader();
}

// Delete or Undelete a comment
function ManageComment(Switch, DiscussionID, CommentID, ShowText, HideText) {
	var ConfirmText = (Switch==1?HideText:ShowText);
	if (confirm(ConfirmText)) {
		ChangeLoaderText("Processing...");
		SwitchLoader(1);
		var dm = new DataManager();
		dm.RequestCompleteEvent = ProcessComment;
		dm.RequestFailedEvent = ProcessComment;
		dm.LoadData("./tools/switch.php?Type=Comment&Switch="+Switch+"&DiscussionID="+DiscussionID+"&CommentID="+CommentID);
	}
}

function ManageCustomStyle(NewValue) {
	var Radio = document.frmGeneralSettings.StyleID;
	var Custom = document.frmGeneralSettings.CustomStyle;
	if (Radio) {
		for (i = 0; i < Radio.length; i++) {
			if (Radio[i].value == NewValue) {
				Radio[i].checked = 1;
			} else {
				Radio[i].checked = 0;
			}
		}
	}
	if (NewValue == 0) {
		Custom.disabled = false;
		Custom.focus();
	} else {
		Custom.disabled = true;
		Custom.blur();
	}
}

function PanelSwitch(PanelItem, RefreshPageWhenComplete) {
	var chkBox = document.getElementById(PanelItem+"ID");
	if (chkBox) {
		ChangeLoaderText("Processing...");
		SwitchLoader(1);
		var dm = new DataManager();
		if (RefreshPageWhenComplete == 1) {
			dm.RequestCompleteEvent = RefreshPage;
			dm.RequestFailedEvent = RefreshPage;
		} else {
			dm.RequestCompleteEvent = HandleSwitch;
			dm.RequestFailedEvent = HandleFailure;
		}
		dm.LoadData("./tools/switch.php?Type="+PanelItem+"&Switch="+chkBox.checked);
		// Debug: document.location = "./tools/switch.php?Type="+PanelItem+"&Switch="+chkBox.checked;
	}
}

function PopTermsOfService() {
	if (document.ApplicationForm && document.ApplicationForm.ReadTerms) document.ApplicationForm.ReadTerms.value = 1;
	window.open("./termsofservice.php", "TermsOfService", "toolbar=no,status=yes,location=no,menubar=no,resizable=yes,height=600,width=400,scrollbars=yes");
}

function ProcessCategory(Request) {
	ChangeLoaderText("Complete");
	var CategorizeContainer = document.getElementById("CategorizeContainer");
	if (CategorizeContainer) CategorizeContainer.innerHTML = "<div class=\"CategorizeLabel\">Your categorization has been saved successfully</div>";
	CloseLoader();
}

function ProcessComment(Request) {
	ChangeLoaderText("Refreshing...");
	setTimeout("document.location.reload();",600);
}

function RefreshPage() {
	document.location.reload();
}

function RemoveSearch(SearchID) {
	var SavedSearchCount = document.getElementById("SavedSearchCount");
	var SearchSavingHelp = document.getElementById("SearchSavingHelp");
	var SearchToRemove = document.getElementById("SavedSearch_"+SearchID);
	if (SavedSearchCount && SearchSavingHelp && SearchToRemove) {
		if (confirm("Are you sure you want to remove this search?")) {
			var dm = new DataManager();
			dm.RequestCompleteEvent = HandleSwitch;
			dm.RequestFailedEvent = HandleFailure;
			dm.LoadData("./tools/switch.php?Type=RemoveSearch&Switch=1&SearchID="+SearchID);
			SearchToRemove.style.display = "none";
			SavedSearchCount.value = SavedSearchCount.value - 1;
			if (SavedSearchCount.value <= 0) SearchSavingHelp.style.display = "block";			
		}
	}
}

// Apply or remove a bookmark
function SetBookmark(CurrentSwitchVal, Identifier, BookmarkText, UnbookmarkText) {
	SetSwitch('SetBookmark', CurrentSwitchVal, 'Bookmark', BookmarkText, UnbookmarkText, Identifier, "&DiscussionID="+Identifier);
	var Sender = document.getElementById('SetBookmark');
	var BookmarkTitle = document.getElementById("BookmarkTitle");
	var BookmarkList = document.getElementById("BookmarkList");
	var Bookmark = document.getElementById("Bookmark_"+Identifier);
	var OtherBookmarksExist = document.frmBookmark.OtherBookmarksExist;
	if (Sender && BookmarkList) {
		if (Sender.name == 0) {
			// removed bookmark
			if (Bookmark) {
				Bookmark.style.display = "none";
				if (OtherBookmarksExist) {
					var Display = OtherBookmarksExist.value == 0 ? "none" : "block" ;
					if (BookmarkTitle) BookmarkTitle.style.display = Display;
					if (BookmarkList) BookmarkList.style.display = Display;
				}
			}
		} else {
			if (Bookmark) {
				Bookmark.style.display = "block";
				if (BookmarkTitle) BookmarkTitle.style.display = "block";
				if (BookmarkList) BookmarkList.style.display = "block";
			}
		}
	}
}

function SetFormatType(FormName) {
	var FormatRadio = document[FormName].DefaultFormatType;
	if (FormatRadio) {
		var Val = -1;
		for (i = 0; i < FormatRadio.length; i++) {
			if (FormatRadio[i].checked) {
				Val = FormatRadio[i].value;
				break;
			}
		}
		if (Val != -1) {
			ChangeLoaderText("Processing...");
			SwitchLoader(1);
			var dm = new DataManager();
			dm.RequestCompleteEvent = HandleSwitch;
			dm.RequestFailedEvent = HandleFailure;
			dm.LoadData("./tools/switch.php?Type=DefaultFormatType&Switch="+Val);
		}
	} else {
		alert("Error");
	}
}

// Handle style definition
function SetStyle(StyleID, CustomStyle) {
	ChangeLoaderText("Processing...");
	SwitchLoader(1);
	var dm = new DataManager();
	dm.RequestCompleteEvent = SetStyleComplete;
	dm.RequestFailedEvent = SetStyleComplete;
	dm.LoadData("./tools/style.php?StyleID="+StyleID+"&Style="+escape(CustomStyle));
}

function SetStyleComplete(Request) {
	ChangeLoaderText("Refreshing...");
	setTimeout("document.location.reload();",600);
}

// Generic Switch
function SetSwitch(SenderName, CurrentSwitchVal, SwitchType, CommentOn, CommentOff, Identifier, Attributes) {
	var Sender = document.getElementById(SenderName);
	if (Sender) {
      ChangeLoaderText("Processing...");
		SwitchLoader(1);
		var Switch = Sender.name == '' ? CurrentSwitchVal : Sender.name;
		var FlipSwitch = Switch == 1 ? 0 : 1;
		Sender.innerHTML = (FlipSwitch==0?CommentOn:CommentOff);
		Sender.name = FlipSwitch;
		var dm = new DataManager();
		dm.RequestCompleteEvent = HandleSwitch;
		dm.RequestFailedEvent = HandleFailure;
		dm.LoadData("./tools/switch.php?Type="+SwitchType+"&Switch="+FlipSwitch+Attributes);
	}
}

function ShowAdvancedSearch() {
	var SimpleSearch = document.getElementById("SimpleSearch");
	var AdvancedSearch = document.getElementById("AdvancedSearch");
	if (SimpleSearch && AdvancedSearch) {
		SimpleSearch.style.display = "none";
		AdvancedSearch.style.display = "block";
	}
}

function ToggleCategoryBlock(CategoryID, Block) {
	ChangeLoaderText("Processing...");
	SwitchLoader(1);
	var dm = new DataManager();
	dm.RequestCompleteEvent = RefreshPage;
	dm.RequestFailedEvent = RefreshPage;
	dm.LoadData("./tools/block.php?BlockCategoryID="+CategoryID+"&Block="+Block);
}

function ToggleClipboard() {
   var ClipboardButton = document.getElementById("ClipboardButton");
   var ClipboardContainer = document.getElementById("ClipboardContainer");
   if (ClipboardButton && ClipboardContainer) {
      if (ClipboardContainer.style.display == "block") {
         ClipboardContainer.style.display = "none";
         ClipboardButton.className = "ClipboardOff";
      } else {
         ClipboardContainer.style.display = "block";
         ClipboardButton.className = "ClipboardOn";         
         var WhisperButton = document.getElementById("WhisperButton");
         var WhisperContainer = document.getElementById("WhisperContainer");
         if (WhisperButton && WhisperContainer) {
            WhisperButton.className = "WhisperOff";
            WhisperContainer.style.display = "none";   
         }
      }
   }
}
function ToggleCommentBox() {
   SwitchElementClass('CommentBox', 'CommentBoxController', 'SmallCommentBox', 'LargeCommentBox', 'big input', 'small input')
}


function ToggleTextOnly() {
	var Help = document.getElementById("TextOnlyAbout");
	if (Help) {
		if (Help.style.display == "block") {
			Help.style.display = "none";
		} else {
			Help.style.display = "block";
		}
	}
}

function ToggleWhisper() {
   var WhisperButton = document.getElementById("WhisperButton");
   var WhisperContainer = document.getElementById("WhisperContainer");
   if (WhisperButton && WhisperContainer) {
      if (WhisperContainer.style.display == "block") {
         WhisperContainer.style.display = "none";
         WhisperButton.className = "WhisperOff";
      } else {
         WhisperButton.className = "WhisperOn";
         WhisperContainer.style.display = "block";
         var ClipboardButton = document.getElementById("ClipboardButton");
         var ClipboardContainer = document.getElementById("ClipboardContainer");
         if (ClipboardButton && ClipboardContainer) {
            ClipboardButton.className = "ClipboardOff";
            ClipboardContainer.style.display = "none";   
         }
      }
   }
}

function WhisperBack(DiscussionID, WhisperTo) {
	var frm = document.frmPostComment;
	if (!frm) {
		document.location = "post.php?PageAction=Reply&DiscussionID="+DiscussionID+"&WhisperUsername="+WhisperTo;
	} else {
		frm.WhisperUsername.value = WhisperTo;
		frm.Body.focus();
	}
}
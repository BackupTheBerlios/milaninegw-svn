/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Vanilla.
* Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Vanilla is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
*
* Description: Auto-completion class that handles loading data dynamically from a database and displaying autocomplete dropdowns underneath form inputs
*/

function AutoComplete(id) {
	// Properties (Private)
	var self = this;
	self.id = id;
	self.KeyCode = 0;
	self.CurrentAutoCompleteItem = 0;
	self.AutoCompleteContainer = null;
	self.Input = null;

	// Methods (Public)
	this.ClickItem = function(SenderID) {
		var Sender = document.getElementById(SenderID);
		if (Sender) self.Input.value = Sender.innerHTML;
		// Override the onblur hide effect with an instant hide
		this.HideAutoComplete(1);
	}
	this.HideAutoComplete = function(WaitTime) {
		if (typeof(WaitTime) == "undefined") WaitTime = 500;
		if (self.AutoCompleteContainer) setTimeout("HideElement('"+self.AutoCompleteContainer.id+"', 1);",WaitTime);
	}
	this.FillData = function(Request) {
		if (self.AutoCompleteContainer) {
			// Display the loaded data
			if (Request.responseText != "") {
				self.AutoCompleteContainer.innerHTML = Request.responseText;
				self.AutoCompleteContainer.style.display = "block";
				self.CurrentAutoCompleteItem = 1;
			} else {
				self.AutoCompleteContainer.style.display = "none";				
				self.CurrentAutoCompleteItem = 0;
			}
			if (self.KeyCode != 8) {
				var TypedLetter = String.fromCharCode(self.KeyCode);
				var SelectedItem = self.GetSelectedItem(self.CurrentAutoCompleteItem);
				if (SelectedItem) {
					if (document.selection && document.selection.createRange) {
						var oRange = document.selection.createRange(); 
						oRange.text = TypedLetter; 
						oRange.collapse(true); 
						oRange.select();
						var len = self.Input.value.length-1;
						self.Input.value = SelectedItem;
						
						var nRange = self.Input.createTextRange(); 
						nRange.moveStart("character", len); 
						nRange.moveEnd("character", 0);      
						nRange.select();                                              
					} else if (self.Input.selectionStart) {
						 var iStart = self.Input.selectionStart;
						 var iEnd = SelectedItem.length;
						 self.Input.value = SelectedItem;
						 self.Input.setSelectionRange(iStart, iEnd); 
					} 
				}
			}
		}
	}
	this.LoadData = function(Sender, KeyUpEvent, AutoCompleteContainerID) {
		// Assign the sender for manipulation later
		self.Input = Sender;
		self.AutoCompleteContainer = document.getElementById(AutoCompleteContainerID);

		// Save the pressed keycode
		if (KeyUpEvent) {
			if (KeyUpEvent.which) {
				self.KeyCode = KeyUpEvent.which;
			} else if (KeyUpEvent.keyCode) {
				self.KeyCode = KeyUpEvent.keyCode;
			}
		}
		
		// Handle navigating the list
		switch (self.KeyCode) {
			case 38: //up arrow
				this.NavigateUp();
				break;
			case 40: //down arrow
				this.NavigateDown();
				break;
			case 37: //left arrow 
			case 39: //right arrow 
			case 33: //page up  
			case 34: //page down  
			case 36: //home  
			case 35: //end                  
			case 13: //enter
				this.Navigate(self.CurrentAutoCompleteItem);
				break;
			case 9: //tab  
			case 27: //esc  
			case 16: //shift  
			case 17: //ctrl  
			case 18: //alt  
			case 20: //caps lock 
			// case 8: //backspace  
			case 46: //delete
			case 32: // space
				 break; 
			default:
				// Load the data
				var dm = new DataManager();
				dm.RequestCompleteEvent = this.FillData;
				dm.RequestFailedEvent = HandleFailure;
				dm.LoadData("./tools/getusers.php?Sender="+self.id+"&Search="+escape(self.Input.value));
				break;
		}
		return true;
	}
	this.MouseOverAutoCompleteItem = function(Sender) {
		// Unhover all items except the current item
		var Counter = 1;
		var CurrentItem = null;
		while (Counter > 0 && Counter < 11) {
			CurrentItem = document.getElementById("AutoCompleteItem"+Counter);
			if (CurrentItem) {
				if (CurrentItem.id == Sender.id) {
					CurrentItem.className = "ListOptionOn";
					self.CurrentAutoCompleteItem = Counter;
				} else {
					CurrentItem.className = "ListOptionOff";
				}
				Counter++;
			} else {
				Counter = 0;
			}		
		}
	}
	this.Navigate = function(ItemNumber) {
		// alert("moving to item "+ItemNumber);
		if (ItemNumber > 0 && self.AutoCompleteContainer.style.display == "block") {
			var NewItem = document.getElementById("AutoCompleteItem"+ItemNumber);
			if (NewItem) {
				this.MouseOverAutoCompleteItem(NewItem);
				self.Input.value = NewItem.innerHTML;
			}
		}
	}
	this.NavigateDown = function() {
		var NextItem = self.CurrentAutoCompleteItem + 1;
		this.Navigate(NextItem);
	}
	this.NavigateUp = function() {
		var LastItem = self.CurrentAutoCompleteItem - 1;
		this.Navigate(LastItem);
	}
	
	// Methods (Private)
	self.GetSelectedItem = function(ItemID) {
		var ItemContainer = document.getElementById("AutoCompleteItem"+ItemID);
		if (ItemContainer) {
			return ItemContainer.innerHTML;
		} else {
			return null;
		}
	}
}
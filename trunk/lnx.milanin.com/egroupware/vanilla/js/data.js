/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Vanilla.
* Vanilla is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Vanilla is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Vanilla is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
*
* Description: Dynamic Data Manager handles loading data dynamically into page
*/
function DataManager() {
	// Properties
	var self = this;
	self.RequestCompleteEvent = null;
	this.RequestCompleteEvent = self.RequestCompleteEvent;
	self.RequestFailedEvent = null;
	this.RequestFailedEvent = self.RequestFailedEvent;
	
	// Methods
	this.CreateDataHandler = function(Request) {
		var DataHandler = function() {
			if (Request.readyState == 4) {
				if (Request.status == 200) {
					self.RequestCompleteEvent(Request);
				} else {
					self.RequestFailedEvent(Request);
				}
			}
		}
		DataHandler.Request = Request;
		DataHandler.RequestCompleteEvent = self.RequestCompleteEvent;
		DataHandler.RequestFailedEvent = self.RequestFailedEvent;
		return DataHandler;
	}
	this.InitiateXmlHttpRequest = function() {
		var Request = null;
		try {
			Request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch(e) {
			try {
				Request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch(oc) {
				Request = null;
			}
		}
		if (!Request && typeof(XMLHttpRequest) != "undefined") Request = new XMLHttpRequest();
		if (!Request) document.location = 'http://lussumo.com/upgrade.html';
		return Request;
	}
	this.LoadData = function(DataSource) {
		var Request = this.InitiateXmlHttpRequest();
		if (Request != null) {
			try {
				Request.onreadystatechange = this.CreateDataHandler(Request);
				Request.open("GET", DataSource, true);
				Request.send(null);
			} catch(oc) {
				alert(oc);
			}
		}
	}
}

// A status indicator to keep the user informed
function ChangeLoaderText(NewText) {
	ChangeElementText("LoadStatus", NewText)
}
function CloseLoader() {
	setTimeout("SwitchLoader(0)",600);	
}
function SwitchLoader(ShowLoader) {
	var Loader = document.getElementById("LoadStatus");
	if (Loader) Loader.style.display = (ShowLoader == 1)?"block":"none";
}

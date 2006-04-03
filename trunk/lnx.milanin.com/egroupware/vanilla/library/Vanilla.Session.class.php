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
* Description: Class that handles user sessions
*/

class Session {
	var $UserID;			// Unique user identifier
	var $User;				// User object containing properties relevant to session

	// Ensure that there is an active session. 
	// If there isn't an active session, send the user to the SignIn Url
	function Check($RedirectUrl) {
//                 echo "<!--\n".$this->UserID."\n-->\n";
		if (($this->UserID == 0 && !agPUBLIC_BROWSING) || ($this->UserID > 0 && !$this->User->CanLogin)) {
			// Fully define the current url
			$CurrentPage = ForceString(@$_SERVER['PHP_SELF'], "index.php");
			$QueryString = ForceString(@$_SERVER['QUERY_STRING'], "");
			if ($QueryString != "") $QueryString = "?".$QueryString;
			$ReturnUrl = urlencode($CurrentPage.$QueryString);
			header("location: ".$RedirectUrl."&ReturnUrl=".$ReturnUrl);
		}
	}
	
	// End a session
	function End() {
		if (session_id()) session_destroy();
		// Destroy the cookies as well
		setcookie("name", " ", time()-3600,"/",agCOOKIE_DOMAIN);
		unset($_COOKIE["name"]);
		setcookie("pass", " ", time()-3600,"/",agCOOKIE_DOMAIN);
		unset($_COOKIE["pass"]);
		return true;
	}
	
	// Get a session variable
	function GetVariable($Name, $DataType = "bool") {
		if ($DataType == "int") {
			return ForceInt(@$_SESSION[$Name], 0);
		} elseif ($DataType == "bool") {
			return ForceBool(@$_SESSION[$Name], 0);
                } elseif ($DataType == "Array") {
			return ForceArray(@$_SESSION[$Name], Array());
		} else {
			return ForceString(@$_SESSION[$Name], "");
		}
	}
	
	function Session() {
	}
	
	// Set a session variable
	function SetVariable($Name, $Value) {
		@$_SESSION[$Name] = $Value;		
	}
	
	// Start a session if required username/password exist in the system
	function Start(&$Context, $UserID = "0") {
		if (!session_id()) session_start();
		$UserID = ForceInt($UserID, 0);
		if ($UserID > 0) {
			$this->UserID = $UserID;
			$this->SetVariable("UserID", $UserID);
		} else {
			$this->UserID = $this->GetVariable("UserID", "int");
		}
		
		$um = false;
		
		// If the session vars are not defined, attempt to revalidate the session from cookies
		if ($this->UserID == 0) {
			// NOTE: the session object is not yet valid in the context object
                        $um = $Context->ObjectFactory->NewContextObject($Context, "UserManager");			
			$this->UserID = $um->ValidateCookieCredentials();
			$this->SetVariable("UserID", $this->UserID);
		}
		
		// Now retrieve user information
		if ($this->UserID > 0) {
			if (!$um) {
				$um = $Context->ObjectFactory->NewContextObject($Context, "UserManager");
			}
			$this->User = $um->GetSessionDataById($this->UserID);
			$GrpTmp=$this->User->UserMainGroup;
			foreach (array_keys($this->User->UserGroups) as $GrpID){
                          $GrpTmp[$GrpID]=$this->User->UserGroups[$GrpID];
                        }
			$this->SetVariable("UserGroups",@$GrpTmp);
			// If the session data retrieval failed for some reason, dump the user
			if (!$this->User) {
				$this->User = $Context->ObjectFactory->NewContextObject($Context, "User");
				$this->User->Clear();
				$this->UserID = 0;				
			}
		} else {
			$FauxContext = 0;
			$this->User = $Context->ObjectFactory->NewObject($FauxContext, "User");
			$this->User->Clear();
		}

		// Handle Work-Safe Mode Switches
		$HtmlOn = ForceIncomingString("h", "1");
		if ($HtmlOn != "" && $this->UserID > 0) {
			if (!$um) {
				$um = $Context->ObjectFactory->NewContextObject($Context, "UserManager", $Context);
			}
			
			if (ForceBool($HtmlOn, 0)) {
				$um->ShowHtml($this->UserID);
			} else {
				$um->HideHtml($this->UserID);
			}
		}		
	}
}
?>
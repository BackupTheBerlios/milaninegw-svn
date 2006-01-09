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
* Description: Controls for signin.php
*/

class SignInForm extends PostBackControl {
	var $Username;
	var $Password;
	var $RememberMe;
	var $FormName;
	var $ApplicantCount;		// The number of applicants currently awaiting approval
   var $ReturnUrl;
	
	function SignInForm(&$Context, $FormName) {
		$this->FormName = $FormName;
		$this->ValidActions = array("SignIn");
		$this->Constructor($Context);
		$this->ReturnUrl = urldecode(ForceIncomingString("ReturnUrl", ""));
	}
	
	function LoadData() {
		$this->Username = ForceIncomingString("Username", "");
		$this->Password = ForceIncomingString("Password", "");
		$this->RememberMe = ForceIncomingBool("RememberMe", 0);
		
		if ($this->IsPostBack) {
			if ($this->PostBackAction == "SignIn") {
				$UserManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
				
				// Check for an already active session
				if ($this->Context->Session->UserID != 0) {
					$this->PostBackValidated = 1;
				} else {
					// Attempt to create a new session for the user
					if ($UserManager->ValidateUserCredentials($this->Username, $this->Password, $this->RememberMe)) {
						$this->PostBackValidated = 1;
						// Automatically redirect if this user isn't a user administrator or master administrator or there aren't any new applicants
                  $AutoRedirect = 1;
						if ($this->Context->Session->User->AdminUsers || $this->Context->Session->User->MasterAdmin) {
							$this->ApplicantCount = $UserManager->GetApplicantCount();
							if ($this->ApplicantCount > 0) $AutoRedirect = 0;
						}
                  if ($AutoRedirect) {
							if ($this->ReturnUrl == "") {
								$this->ReturnUrl = dirname(ForceString(@$_SERVER["PHP_SELF"], ""));
							} else {
								$this->ReturnUrl = urldecode($this->ReturnUrl);
							}							
							$this->ReturnUrl = ForceString(@$_SERVER['HTTP_HOST'], "").$this->ReturnUrl;
							header("location: http://".$this->ReturnUrl);
							die();
						}
					}
					
				}				
			} 
		}
	}
	
	function Render_ValidPostBack() {
		$this->Context->Writer->Add("<div class=\"FormComplete\">
			<h1>".$this->Context->GetDefinition("YouAreSignedIn")."</h1>
			<ul>
				<li><a href=\"./\">".$this->Context->GetDefinition("ClickHereToContinueToDiscussions")."</a></li>
				<li><a href=\"./categories.php\">".$this->Context->GetDefinition("ClickHereToContinueToCategories")."</a></li>");
		if ($this->ApplicantCount > 0) $this->Context->Writer->Add("<li><a href=\"search.php?PostBackAction=Search&Keywords=roles:Applicant;sort:Date;&Type=Users\">".$this->Context->GetDefinition("ReviewNewApplicants")."</a> (<strong>".$this->ApplicantCount." ".$this->Context->GetDefinition("New")."</strong>)</li>");
			$this->Context->Writer->Write("</ul>
		</div>");
	}
	
	function Render_NoPostBack() {
		$this->Username = FormatStringForDisplay($this->Username, 1);
		$this->PostBackParams->Add("PostBackAction", "SignIn");
		$this->PostBackParams->Add("ReturnUrl", $this->ReturnUrl);
		$this->Render_Warnings();
		$this->Context->Writer->Add("<div class=\"About\">
			".$this->Context->GetDefinition("AboutVanilla")."
		</div>
		<div class=\"Form\">
			".$this->Context->GetDefinition("MemberSignIn"));
		$this->Render_PostBackForm($this->FormName);
		$this->Context->Writer->Write("<dl class=\"InputBlock SignInInputs\">
				<dt>".$this->Context->GetDefinition("Username")."</dt>
				<dd><input type=\"text\" name=\"Username\" value=\"".$this->Username."\" class=\"Input\" maxlength=\"20\" /></dd>
				<dt>".$this->Context->GetDefinition("Password")."</dt>
				<dd><input type=\"password\" name=\"Password\" value=\"\" class=\"Input\" /></dd>
			</dl>
			<div class=\"InputBlock RememberMe\">".GetDynamicCheckBox("RememberMe", 1, ForceIncomingBool("RememberMe", 0), "", $this->Context->GetDefinition("RememberMe"))."</div>
			<a class=\"ForgotPasswordLink\" href=\"passwordrequest.php\">".$this->Context->GetDefinition("ForgotYourPassword")."</a>
			<div class=\"FormButtons\"><input type=\"submit\" name=\"btnSignIn\" value=\"".$this->Context->GetDefinition("Proceed")."\" class=\"Button\" /></div>
			</form>
		</div>");
	}
}
?>
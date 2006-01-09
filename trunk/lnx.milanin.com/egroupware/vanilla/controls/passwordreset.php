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
* Description: Controls for passwordreset.php
*/
include_once(sgLIBRARY."Utility.Email.class.php");
class PasswordResetForm extends PostBackControl {
   var $FormName;						// The name of this form
   var $ValidatedCredentials;		// Are the user's password retrieval credentials valid
   
	// Form properties
	var $UserID;
	var $EmailVerificationKey;
	var $NewPassword;
	var $ConfirmPassword;
	
	function FormatPropertiesForDisplay() {
		$this->UserID = ForceInt($this->UserID, 0);
		$this->EmailVerificationKey = ForceString($this->EmailVerificationKey, "");
	}
	
	function PasswordResetForm(&$Context, $FormName = "") {
		$this->ValidActions = array("ResetPassword");
		$this->FormName = $FormName;
		$this->ValidatedCredentials = 0;
		$this->Constructor($Context);
		// Form properties
		$this->UserID = ForceIncomingInt("u", 0);
		$this->EmailVerificationKey = ForceIncomingString("k", "");
		$this->NewPassword = ForceIncomingString("NewPassword", "");
		$this->ConfirmPassword = ForceIncomingString("ConfirmPassword", "");
	}
	
	function LoadData() {
		$um = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
		if ($this->IsPostBack) {
			$this->ValidatedCredentials = 1;
		} else {
			$this->ValidatedCredentials = $um->VerifyPasswordResetRequest($this->UserID, $this->EmailVerificationKey);
		}
		
		if ($this->IsPostBack && $this->ValidatedCredentials) {
			if ($this->PostBackAction == "ResetPassword") {
				$this->PostBackValidated = $um->ResetPassword($this->UserID, $this->EmailVerificationKey, $this->NewPassword, $this->ConfirmPassword);
			} 
		}
	}
	
	function Render_ValidPostBack() {
		$this->Context->Writer->Add("<div class=\"FormComplete\">
			<h1>".$this->Context->GetDefinition("PasswordReset")."</h1>
			<ul>
				<li><a href=\"signin.php\">".$this->Context->GetDefinition("SignInNow")."</a>.</li>
			</ul>
		</div>");
	}
	
	function Render_NoPostBack() {
		$this->FormatPropertiesForDisplay();
		$this->PostBackParams->Add("PostBackAction", "ResetPassword");
		$this->PostBackParams->Add("u", $this->UserID);
		$this->PostBackParams->Add("k", $this->EmailVerificationKey);
		$this->Render_Warnings();
		
		if ($this->ValidatedCredentials) {
			$this->Context->Writer->Add("<div class=\"About\">
				<h1>".$this->Context->GetDefinition("AboutYourPassword")."</h1>
				<p>".$this->Context->GetDefinition("AboutYourPasswordNotes")."</p>
			</div>
			<div class=\"Form\">
				<h1>".$this->Context->GetDefinition("PasswordResetForm")."</h1>
				<p>".$this->Context->GetDefinition("ChooseANewPassword")."</p>");
			$this->Render_PostBackForm($this->FormName);
			$this->Context->Writer->Write("<dl class=\"InputBlock NewPasswordInputs\">
					<dt>".$this->Context->GetDefinition("NewPassword")."</dt>
					<dd><input type=\"password\" name=\"NewPassword\" value=\"\" class=\"Input\" maxlength=\"20\" /></dd>
					<dt>".$this->Context->GetDefinition("ConfirmPassword")."</dt>
					<dd><input type=\"password\" name=\"ConfirmPassword\" value=\"\" class=\"Input\" maxlength=\"20\" /></dd>
				</dl>
				<div class=\"FormButtons\"><input type=\"submit\" name=\"btnPassword\" value=\"".$this->Context->GetDefinition("Proceed")."\" class=\"Button\" /></div>
				</form>
			</div>");
		} else {
			$this->Context->Writer->Write("&nbsp;");
		}
	}
}
?>
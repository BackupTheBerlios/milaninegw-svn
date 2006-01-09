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
* Description: Controls for passwordrequest.php
*/
include_once(sgLIBRARY."Utility.Email.class.php");

class PasswordRequestForm extends PostBackControl {
   var $FormName;				// The name of this form
   var $EmailSentTo;			// The email address to which the password reset request was sent
   
	// Form Properties
   var $Username;
	
	function PasswordRequestForm(&$Context, $FormName = "") {
		$this->ValidActions = array("RequestPasswordReset");
		$this->FormName = $FormName;
		$this->Username = ForceIncomingString("Username", "");
		$this->Constructor($Context);
	}
	
	function LoadData() {
		$this->UserManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
		
		if ($this->IsPostBack) {
			if ($this->PostBackAction == "RequestPasswordReset") {
				$this->EmailSentTo = $this->UserManager->RequestPasswordReset($this->Username);
				if ($this->EmailSentTo) $this->PostBackValidated = 1;
			} 
		}
	}
	
	function Render_ValidPostBack() {
		$this->Context->Writer->Add("<div class=\"FormComplete\">
			<h1>".$this->Context->GetDefinition("RequestProcessed")."</h1>
			<ul>
				<li>".$this->Context->GetDefinition("MessageSentTo")." <strong>".FormatStringForDisplay($this->EmailSentTo, 1)."</strong> ".$this->Context->GetDefinition("ContainingPasswordInstructions")."</li>
			</ul>
		</div>");
	}
	
	function Render_NoPostBack() {
		$this->PostBackParams->Add("PostBackAction", "RequestPasswordReset");
		$this->Render_Warnings();
		$this->Context->Writer->Add("<div class=\"About\">
			<h1>".$this->Context->GetDefinition("AboutYourPassword")."</h1>
			<p>".$this->Context->GetDefinition("AboutYourPasswordRequestNotes")."</p>
			<p><a href=\"signin.php\">".$this->Context->GetDefinition("BackToSignInForm")."</a></p>
		</div>
		<div class=\"Form\">
			<h1>".$this->Context->GetDefinition("PasswordResetRequestForm")."</h1>
			<p>".$this->Context->GetDefinition("PasswordResetRequestFormNotes")."</p>");
		$this->Render_PostBackForm($this->FormName);
		$this->Context->Writer->Write("<dl class=\"InputBlock PasswordRequestInputs\">
				<dt>".$this->Context->GetDefinition("Username")."</dt>
				<dd><input type=\"text\" name=\"Username\" value=\"".FormatStringForDisplay($this->Username, 1)."\" class=\"Input\" maxlength=\"20\" /></dd>
			</dl>
			<div class=\"FormButtons\"><input type=\"submit\" name=\"btnPassword\" value=\"".$this->Context->GetDefinition("SendRequest")."\" class=\"Button\" /></div>
			</form>
		</div>");
	}
}
?>
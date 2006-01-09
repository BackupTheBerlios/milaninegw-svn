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
* Description: Controls for apply.php
*/
include_once(sgLIBRARY."Input.Validator.class.php");
include_once(sgLIBRARY."Utility.Email.class.php");
class ApplyForm extends PostBackControl {
   var $Applicant;			// A user object for the applying user
   var $FormName;				// The name of this form
	
	function ApplyForm(&$Context, $FormName = "") {
		$this->ValidActions = array("Apply");
		$this->FormName = $FormName;
		$this->Constructor($Context);
		$this->Applicant = $Context->ObjectFactory->NewContextObject($Context, "User");
		$this->Applicant->GetPropertiesFromForm();
	}
	
	function LoadData() {
		if ($this->IsPostBack) {
			if ($this->PostBackAction == "Apply") {
				$um = $this->Context->ObjectFactory->NewContextObject($this->Context, "UserManager");
				$this->PostBackValidated = $um->CreateUser($this->Applicant);
			} 
		}
	}
	
	function Render_ValidPostBack() {
		if (agALLOW_IMMEDIATE_ACCESS) {
			$this->Context->Writer->Write("<div class=\"FormComplete\">
				<h1>".$this->Context->GetDefinition("ApplicationComplete")."</h1>
				<ul>
					<li><a href=\"signin.php\">".$this->Context->GetDefinition("SignInNow")."</a></li>
				</ul>
			</div>");
		} else {
			$this->Context->Writer->Write("<div class=\"FormComplete\">
				<h1>".$this->Context->GetDefinition("ThankYouForInterest")."</h1>
				<ul>
					<li>".$this->Context->GetDefinition("ApplicationWillBeReviewed")."</li>
				</ul>
			</div>");
		}
	}
	
	function Render_NoPostBack() {
		$this->Applicant->FormatPropertiesForDisplay();
		$this->PostBackParams->Add("PostBackAction", "Apply");
		$this->PostBackParams->Add("ReadTerms", $this->Applicant->ReadTerms);
		$this->Render_Warnings();		
		
		$this->Context->Writer->Add("<div class=\"About\">
			".$this->Context->GetDefinition("AboutMembership")."
					<p><a href=\"signin.php\">".$this->Context->GetDefinition("BackToSignInForm")."</a></p>
				</div>
				<div class=\"Form\">
					<h1>".$this->Context->GetDefinition("MembershipApplicationForm")."</h1>
               <p>".$this->Context->GetDefinition("AllFieldsRequired")."</p>");
		$this->Render_PostBackForm($this->FormName);
		$this->Context->Writer->Write("<dl class=\"InputBlock ApplyInputs\">
					<dt>".$this->Context->GetDefinition("FirstName")."</dt>
					<dd><input type=\"text\" name=\"FirstName\" value=\"".$this->Applicant->FirstName."\" class=\"Input\" maxlength=\"40\" /></dd>
					<dt>".$this->Context->GetDefinition("LastName")."</dt>
					<dd><input type=\"text\" name=\"LastName\" value=\"".$this->Applicant->LastName."\" class=\"Input\" maxlength=\"40\" /></dd>
					<dt>".$this->Context->GetDefinition("EmailAddress")."</dt>
					<dd><input type=\"text\" name=\"Email\" value=\"".$this->Applicant->Email."\" class=\"Input\" maxlength=\"160\" /></dd>
					<dt>".$this->Context->GetDefinition("Username")."</dt>
					<dd><input type=\"text\" name=\"Name\" value=\"".$this->Applicant->Name."\" class=\"Input\" maxlength=\"20\" /></dd>
					<dt>".$this->Context->GetDefinition("Password")."</dt>
					<dd><input type=\"password\" name=\"NewPassword\" value=\"".$this->Applicant->NewPassword."\" class=\"Input\" /></dd>
					<dt>".$this->Context->GetDefinition("PasswordAgain")."</dt>
					<dd><input type=\"password\" name=\"ConfirmPassword\" value=\"".$this->Applicant->ConfirmPassword."\" class=\"Input\" /></dd>
				</dl>
				<div class=\"InputBlock DiscoveryInput\">
					<div class=\"InputLabel\">".$this->Context->GetDefinition("HowDidYouFindUs")."</div>
					<textarea name=\"Discovery\" class=\"ApplicationTextbox\">".$this->Applicant->Discovery."</textarea>
				</div>
				<div class=\"InputBlock TermsOfServiceCheckbox\">
					<div class=\"CheckboxLabel\">".GetBasicCheckBox("AgreeToTerms", 1, $this->Applicant->AgreeToTerms,"")." ".$this->Context->GetDefinition("IHaveReadAndAgreeTo")." <a href=\"javascript:PopTermsOfService('../');\">".$this->Context->GetDefinition("TermsOfService")."</a>.</div>
				</div>
				<div class=\"FormButtons\"><input type=\"submit\" name=\"btnApply\" value=\"".$this->Context->GetDefinition("Proceed")."\" class=\"Button\" /></div>
				</form>
			</div>");
	}
}
?>
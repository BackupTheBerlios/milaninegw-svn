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

class BugForm extends PostBackControl {
   
   var $Reporter;
   var $ReporterEmail;
   var $BugUrl;
   var $BugHappenedWhen;
   var $BugDescription;
	
	function BugForm(&$Context) {
		$this->ValidActions = array("ReportBug");
		$this->Constructor($Context);
	}
	
	function LoadData() {
      $this->Reporter = ForceIncomingString("ReporterName", "");
      $this->ReporterEmail = ForceIncomingString("ReporterEmail", "");
		$this->BugUrl = ForceIncomingString("BugUrl", "");
		$this->BugHappenedWhen = ForceIncomingString("BugHappenedWhen", "");
		$this->BugDescription = ForceIncomingString("BugDescription", "");
		
		if ($this->IsPostBack) {
			if ($this->PostBackAction == "ReportBug") {
            // Validate the inputs
        		Validate($this->Context->GetDefinition("YourNameLower"), 1, $this->Reporter, 100, "", $this->Context);
        		Validate($this->Context->GetDefinition("YourEmailLower"), 1, $this->ReporterEmail, 200, "(.+)@(.+)\.(.+)", $this->Context);
        		Validate($this->Context->GetDefinition("BugUrlLower"), 1, $this->BugUrl, 255, "", $this->Context);
        		Validate($this->Context->GetDefinition("BugHappenedWhenLower"), 1, $this->BugHappenedWhen, 5000, "", $this->Context);
        		Validate($this->Context->GetDefinition("BugDescriptionLower"), 1, $this->BugDescription, 5000, "", $this->Context);
            $this->PostBackValidated = $this->Context->WarningCollector->Iif();
			}
         
         if ($this->PostBackValidated) {
				$e = $this->Context->ObjectFactory->NewContextObject($this->Context, "Email");
				$e->HtmlOn = 0;
				$e->WarningCollector = &$this->Context->WarningCollector;
				$e->ErrorManager = &$this->Context->ErrorManager;
				$e->AddFrom(agSUPPORT_EMAIL, agSUPPORT_NAME);
				$e->AddRecipient(agSUPPORT_EMAIL, agSUPPORT_NAME);
				$e->Subject = agAPPLICATION_TITLE." ".$this->Context->GetDefinition("BugReport");
				$e->BodyText = $this->Context->GetDefinition("BugReportSubmitted")
					."\r\n".$this->Context->GetDefinition("UserAgent")
					.": ".ForceString(@$_SERVER["HTTP_USER_AGENT"],"")
               ."\r\n".$this->Context->GetDefinition("ReporterName")
               .": ".$this->Reporter
               ."\r\n".$this->Context->GetDefinition("ReporterEmail")
               .": ".$this->ReporterEmail
               ."\r\n".$this->Context->GetDefinition("BugUrl")
               .": ".$this->BugUrl
               ."\r\n".$this->Context->GetDefinition("BugHappenedWhen")
               .": ".$this->BugHappenedWhen
               ."\r\n".$this->Context->GetDefinition("BugDescription")
               .": ".$this->BugDescription
               ."\r\n".$this->Context->GetDefinition("NoteOnBugsForAdmins");
				$e->Send();
         }
		}
	}
	
	function Render_ValidPostBack() {
		$this->Context->Writer->Add("<div class=\"FormComplete\">
			<h1>".$this->Context->GetDefinition("BugReport")."</h1>
			<ul>
				<li><a href=\"./\">".$this->Context->GetDefinition("ClickHereToContinueToDiscussions")."</a></li>
			</ul>
		</div>");
	}
	
	function Render_NoPostBack() {
		$this->Reporter = FormatStringForDisplay($this->Reporter, 0);
		$this->ReporterEmail = FormatStringForDisplay($this->ReporterEmail, 0);
		$this->BugUrl = FormatStringForDisplay($this->BugUrl, 0);
		$this->BugHappenedWhen = FormatStringForDisplay($this->BugHappenedWhen, 0);
		$this->BugDescription = FormatStringForDisplay($this->BugDescription, 0);
		$this->PostBackParams->Add("PostBackAction", "ReportBug");
		$this->Render_Warnings();
		$this->Context->Writer->Add("<div class=\"Form BugForm\">
			".$this->Context->GetDefinition("AboutBugReport"));
		$this->Render_PostBackForm("frmBugReport");
		$this->Context->Writer->Write("<dl class=\"InputBlock BugReportInputs\">
				<dt>".$this->Context->GetDefinition("ReporterName")."</dt>
				<dd><input type=\"text\" name=\"ReporterName\" value=\"".$this->Reporter."\" class=\"BugInput\" maxlength=\"100\" /></dd>
				<dt>".$this->Context->GetDefinition("ReporterEmail")."</dt>
				<dd><input type=\"text\" name=\"ReporterEmail\" value=\"".$this->ReporterEmail."\" class=\"BugInput\" maxlength=\"200\" /></dd>
				<dt>".$this->Context->GetDefinition("BugUrl")."</dt>
				<dd><input type=\"text\" name=\"BugUrl\" value=\"".$this->BugUrl."\" class=\"BugInput\" maxlength=\"255\" /></dd>
				<dt>".$this->Context->GetDefinition("BugHappenedWhen")."</dt>
				<dd><textarea name=\"BugHappenedWhen\" class=\"BugTextBox\">".$this->BugHappenedWhen."</textarea></dd>
				<dt>".$this->Context->GetDefinition("BugDescription")."</dt>
				<dd><textarea name=\"BugDescription\" class=\"BugTextBox\">".$this->BugDescription."</textarea></dd>
			</dl>
			<div class=\"FormButtons\"><input type=\"submit\" name=\"btnReportBug\" value=\"".$this->Context->GetDefinition("Submit")."\" class=\"Button\" /></div>
			</form>
		</div>");
	}
}
?>
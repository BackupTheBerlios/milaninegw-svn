<?php
/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of Lussumo's Software Library.
* Lussumo's Software Library is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Lussumo's Software Library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
* 
* Description: Defines & maintains all configuration settings for the filebrowser.
* Applications utilizing this file: Filebrowser;
*/

class Configuration {
	// Configuration Settings
	var $ConfigFile;  			// Location of the configuration file
	var $FileTypesFile;			// Location of the filetypes file
	var $ErrorManager;			// Handles error messages
	var $Version;	   			// FB Version
	var $Developer;				// FB Developer's name
	var $DeveloperEmail; 		// FB Developer's email address
	var $Date;				   	// Date of FB development completion
	var $StyleUrl;			   	// URL to the stylesheet
	var $PageTitle;				// To appear on the page
	var $PageIntroduction;		// To appear before any images are viewed
	var $UsePageIntroductionInSubFolders;
	var $DisplayHiddenFiles;	// Boolean value indicating if files beginning with underscore should be visible
	var $BrowseSubFolders;		// Boolean value indicating if subfolders should be browsable/visible
	var $SortBy;			   	// Value to sort the files by
	var $SortDirection;			// Direction to sort the files
	var $DateFormat;			   // The format string that will be used to configure the display format of the date for a file
	var $PluginHeight;
	var $PluginWidth;
	var $DefaultFilesPerPage;	// The default number of files to show when someone clicks the >> or << buttons
	var $FitImagesToPage;		// Boolean value indicating if large images should be shrunk to fit on the page
   var $UseThumbnails;			// Use thumbnails (if they exist)
	
	// Browsing Properties
	var $FileID;				   // ID of file currently being viewed
	var $FolderIDs;				// String of comma delimited folder ids
	var $aFolderID;				// Array of folder id's currently being viewed
	var $FilesPerPage;			// Number of files to display per page (as defined by the querystring)
	var $CurrentWorkingDirectory;
	var $CurrentBrowsingDirectory;
	var $SelfUrl;			   	// Name of this file
	var $SelfWebPath;			   // path to the filebrowser execution file (ie. http://mydomain.com/images/)
	var $FolderDelimiter;		// Querystring delimiter to be used between folder ids
	var $FolderNavigator;		// A holder variable containing a folder navigation array
	var $FolderNavigatorLocation;	// Another holder variable containing the querystring values for the folder navigator	
	var $ShowMultipleFiles;		// Boolean value indicating if multiple file should be displayed or not
	var $GetFileID;				// ID of a file to "save as"
   var $HideFiles;				// An array of files that should remain hidden
   var $FullyQualifiedHideFiles; // Same as above, but fully qualified to root
	
   var $Name;					   // The name of this class
	
	
	// Default constructor - define default values for class properties
	function Configuration() {
		// Configuration Settings
		$this->ConfigFile = "_config.xml";
		$this->FileTypesFile = "_filetypes.xml";
		$this->CurrentWorkingDirectory = getcwd();
		
		// Configuration Properties
		$this->Version = "1.3.2";
		$this->Developer = "Mark O'Sullivan";
		$this->Date = "2002-2005"; 
		$this->StyleUrl = "_default.css";
		$this->PageTitle = "Lussumo Filebrowser";
		$this->PageIntroduction = "";
		$this->UsePageIntroductionInSubFolders = false;
		$this->DisplayHiddenFiles = false;
		$this->BrowseSubFolders = true;
		$this->SortBy = "Name";
		$this->SortDirection = "asc";
		$this->DateFormat = "m-d-y";
		$this->PluginHeight = 400;
		$this->PluginWidth = 400;
		$this->DefaultFilesPerPage = 5;
		$this->MaxFilesPerPage = 50;
		$this->FitImagesToPage = 1;
		$this->UseThumbnails = 0;
		$this->HideFiles = array();
		$this->FullyQualifiedHideFiles = array();
		
		// Browsing Properties
		$this->FolderDelimiter = "-";
		$this->FileID = ForceIncomingInt("fid", 0);
		$this->FolderIDs = ForceIncomingString("did", "");
		if ($this->FolderIDs == "") {
			$this->aFolderID = array();
		} else {
			$this->aFolderID = explode($this->FolderDelimiter, $this->FolderIDs);
		}
		$this->CurrentBrowsingDirectory = $this->CurrentWorkingDirectory;
		$this->FolderNavigator = array();
		$this->FolderNavigatorLocation = "";
		$this->ShowMultipleFiles = ForceIncomingBool("smf", false);
		$this->GetFileID = ForceIncomingInt("gid", 0);
		
		$this->Name = "FileBrowser";
	}

	function RetrieveConfigurationPropertiesFromXml($Path) {
		$FauxContext = "0";
		if ($this->ConfigFile == "") $this->ErrorManager->AddError($FauxContext,$this->Name, "RetrieveConfigurationPropertiesFromXml", "You must supply a path to the configuration file");
		// Retrieve config file contents
		$File = new File();
		$File->Name = $this->ConfigFile;
		$File->Path = $Path;
		$FileManager = new FileManager();
		$FileManager->ErrorManager = &$this->ErrorManager;
		$File = $FileManager->Get($File);
		// If there were errors retrieving the config file and we're in the CWD, report an error
		if ($this->ErrorManager->ErrorCount > 0 && $Path == $this->CurrentWorkingDirectory) {
			$this->ErrorManager->Clear();
			$this->ErrorManager->AddError($FauxContext,$this->Name, "RetrieveConfigurationPropertiesFromXml", "The root configuration file could not be found/read (_config.xml).");
		// If failed to retrieve the file from a non-root directory, 
		// just accept the root file
		} elseif ($this->ErrorManager->ErrorCount > 0) {
			$this->ErrorManager->Clear();
		// If no errors occurred, continue to retrieve new configuration settings
		} else {
			// Create an XML Parser to retrieve configuration settings
			$XMan = new XmlManager();
			$XMan->ErrorManager = &$this->ErrorManager;
			$MyConfig = $XMan->ParseNode($File->Body);
			if ($MyConfig && $this->ErrorManager->ErrorCount == 0) {
				$this->StyleUrl = $XMan->GetNodeValueByName($MyConfig, "StyleUrl");
				$this->PageTitle = $XMan->GetNodeValueByName($MyConfig, "PageTitle");
				$this->PageIntroduction = $XMan->GetNodeValueByName($MyConfig, "PageIntroduction");
				$this->PageIntroduction = str_replace("[","<", $this->PageIntroduction);
				$this->PageIntroduction = str_replace("]",">", $this->PageIntroduction);
				$this->PageIntroduction = str_replace("\n","<br />", $this->PageIntroduction);
				$this->DisplayHiddenFiles = $XMan->GetNodeValueByName($MyConfig, "DisplayHiddenFiles");
				$this->BrowseSubFolders = $XMan->GetNodeValueByName($MyConfig, "BrowseSubFolders");
				$this->SortBy = $XMan->GetNodeValueByName($MyConfig, "SortBy");
				$this->SortDirection = $XMan->GetNodeValueByName($MyConfig, "SortDirection");
				$this->DateFormat = $XMan->GetNodeValueByName($MyConfig, "DateFormat");
				$this->UsePageIntroductionInSubFolders = ForceBool($XMan->GetNodeValueByName($MyConfig, "UsePageIntroductionInSubFolders"), false);
				$this->PluginHeight = ForceInt($XMan->GetNodeValueByName($MyConfig, "PluginHeight"), $this->PluginHeight);
				$this->PluginWidth = ForceInt($XMan->GetNodeValueByName($MyConfig, "PluginWidth"), $this->PluginWidth);
				$this->FilesPerPage = ForceIncomingInt("fpp", ForceInt($XMan->GetNodeValueByName($MyConfig, "FilesPerPage"), $this->FilesPerPage));
				$this->MaxFilesPerPage = ForceInt($XMan->GetNodeValueByName($MyConfig, "MaxFilesPerPage"), $this->MaxFilesPerPage);
				$this->FitImagesToPage = ForceBool($XMan->GetNodeValueByName($MyConfig, "FitImagesToPage"), $this->FitImagesToPage);
				$this->UseThumbnails = ForceBool($XMan->GetNodeValueByName($MyConfig, "UseThumbnails"), $this->UseThumbnails);
				$this->HideFiles = explode(",", $XMan->GetNodeValueByName($MyConfig, "HideFiles"));
				for ($i = 0; $i < count($this->HideFiles); $i++) {
					$this->FullyQualifiedHideFiles[] = $this->CurrentBrowsingDirectory."/".$this->HideFiles[$i];
				}
			} 
		}
		return $this->ErrorManager->Iif();
	}
}
?>
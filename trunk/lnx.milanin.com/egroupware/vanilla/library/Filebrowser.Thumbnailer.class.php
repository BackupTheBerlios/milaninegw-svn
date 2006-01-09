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
* Description: Defines & maintains all configuration settings for the thumbnailer. Handles thumbnailing of files.
* Applications utilizing this file: Filebrowser;
*/

class Thumbnailer {
	// Configuration Settings
	var $ConfigFile;			// Location of the configuration file
	var $ErrorManager;			// Handles error messages
	var $Version;				// TN Version
	var $Developer;				// TN Developer's name
	var $DeveloperEmail;		// TN Developer's email address
	var $Date;					// Date of TN development completion
	var $StyleUrl;				// URL to the stylesheet
	var $SortBy;				// Value to sort the files by
	var $SortDirection;			// Direction to sort the files
	var $DateFormat;			// The format string that will be used to configure the display format of the date for a file
	var $ThumbHeight;			// The height of the generated thumbnails
	var $ThumbWidth;			// The width of the generated thumbnails
	var $CropThumbnails;		// Boolean value indicating if the thumbnails should be cropped to fit the thumb 
								// dimensions (above), or not cropped and the dimensions are treated as maximums 
								// (images retain their aspect ratio).
	
	// Browsing Properties
	var $FolderIDs;				// String of comma delimited folder ids
	var $aFolderID;				// Array of folder id's currently being viewed
	var $CurrentWorkingDirectory;
	var $CurrentBrowsingDirectory;
	var $SelfUrl;				// Name of this file
	var $SelfWebPath;			// Path to the Thumbnailer execution file (ie. http://mydomain.com/images/)
	var $FolderDelimiter;		// Querystring delimiter to be used between folder ids
	var $FolderNavigator;		// A holder variable containing a folder navigation array
	var $FolderNavigatorLocation;	// Another holder variable containing the querystring values for the folder navigator	
	
	var $Name;					// The name of this class
   var $ThumbnailableFormats;	// array of file formats that can be thumbnailed
   var $FolderList;
	var $FileList;
	var $ThumbPrefix;
	
   var $HideFiles;				// An array of files that should remain hidden
   var $FullyQualifiedHideFiles; // Same as above, but fully qualified to root
	
	// Private
	var $FolderCollection;
	var $ImageCollection;

	
	function DefineBrowsingDirectory() {
		$this->RetrieveConfigurationPropertiesFromXml($this->CurrentWorkingDirectory);
		
		// Check for subfolder ids if 
		if (count($this->aFolderID) > 0) {
			for ($i = 0; $i < count($this->aFolderID); $i++) {
				$CurrentFolderKey = ForceInt($this->aFolderID[$i], 0);
				$this->CurrentBrowsingDirectory = CheckForFolder($this->CurrentBrowsingDirectory, $CurrentFolderKey, $this);
				if (!$this->CurrentBrowsingDirectory) {
					// IF the current browsing directory wasn't found, wipe out all directory settings and start from the root
					$this->CurrentBrowsingDirectory = $this->CurrentWorkingDirectory;
					$this->FolderIDs = "";
					$this->aFolderID = array();
					break;
				}
			}
		}
	}
	
	// The main method which will define and write the entire file browser
	function DefineProperties() {
		$this->DefineBrowsingDirectory();
		
		// Create some holder variables
		$this->FolderCollection = array();
		$this->ImageCollection = new FileCollection();
		$ThumbnailCollection = array();

		// RETRIEVE FILES
		// Loop through files in the current browsing directory
		$FolderKey = 0;
		$FolderHandle = opendir($this->CurrentBrowsingDirectory);
		$CurrentExtension = "";
		$RecordItem = true;
		while (false !== ($Item = readdir($FolderHandle))) {
			$RecordItem = true;
			if ($Item == "."
				 || $Item == ".."
				 || $Item == $this->SelfUrl
		       || in_array($this->CurrentBrowsingDirectory."/".$Item, $this->FullyQualifiedHideFiles)
			) $RecordItem = false;
			
			if ($RecordItem) {
				// If dealing with a folder, add it to the folder collection
				if (is_dir($this->CurrentBrowsingDirectory."/".$Item)) {
					$this->FolderCollection[] = $Item;
				// If not dealing with a folder, add it to the proper file collection
				} elseif (substr($Item,0,7) == $this->ThumbPrefix) {
					$ThumbnailCollection[] = $Item;
				} elseif (substr($Item,0,1) == "_") {
					// Ignore "hidden" files
				} elseif (in_array($this->GetFileType($this->CurrentBrowsingDirectory, $Item), $this->ThumbnailableFormats)) {
					$this->ImageCollection->AddFile($Item, filesize($this->CurrentBrowsingDirectory."/".$Item), filemtime($this->CurrentBrowsingDirectory."/".$Item), "");
				}
			}
		}
	
		// BUILD THE PAGE ELEMENTS
		$ThumbedList = "";
		$NonThumbedList = "";
		
		// Create a parameters class to manage querystring values
		$Params =  new Parameters();
		$Params->DefineCollection($_GET);
		$Params->Remove("smf");
		if ($this->FolderIDs == "") $Params->Remove("did");
	
		$FileCounter = 0;
		
		// Build the file listing
		// Get the sorted files
		$Files = $this->ImageCollection->GetFiles($this->SortBy, $this->SortDirection, $ThumbnailCollection);
		$ThumbBool = 0;
		$NonThumbBool = 0;
		if (count($Files) > 0) {
			for ($j = 0; $j < count($Files); $j++) {
				$FileCounter += 1;
				$CurrentFileName = $Files[$j]["Name"];
				$CurrentFileSize = $Files[$j]["Size"];
				$CurrentFileDate = $Files[$j]["Date"];
				$CurrentFileHandlerMethod = $Files[$j]["HandlerMethod"];
				
				if ($Files[$j]["ThumbnailPresent"]) {
					$ThumbedList .= $this->FormatListItem($FileCounter, $CurrentFileName, $CurrentFileSize, $CurrentFileDate, $Params, $ThumbBool);
					$ThumbBool = FlipBool($ThumbBool);
				} else {
					$NonThumbedList .= $this->FormatListItem($FileCounter, $CurrentFileName, $CurrentFileSize, $CurrentFileDate, $Params, $NonThumbBool);
					$NonThumbBool = FlipBool($NonThumbBool);
				}
				
			}
		}
		if ($NonThumbedList != "") $NonThumbedList = $this->FormatImageList($NonThumbedList, "Images Without Thumbnails", "Images");
		if ($ThumbedList != "") $ThumbedList = $this->FormatImageList($ThumbedList, "Images With Thumbnails", "ThumbedImages");
		
		$this->FileList = $NonThumbedList.$ThumbedList;
		
		// Define the current folder path
		$RootPath = substr($this->SelfWebPath,0,strlen($this->SelfWebPath)-1);
		$CurrentPath = "<a href=\"".$RootPath."/".$this->SelfUrl."\">".str_replace("http://","",$RootPath)."</a>";
		
		$this->FolderList = "<div class=\"Container Folders\">
			<dl class=\"CurrentFolder\">
				<dt>Current Folder</dt>
				<dd>".$CurrentPath.BuildPath($this->FolderNavigator, $this->SelfUrl, 0)."</dd>
			</dl>";
		
		// Build the folder listing
		if (count($this->FolderCollection) > 0 || count($this->aFolderID) > 0) {

			// Sort the folders
			usort($this->FolderCollection, "strcasecmp");
			reset($this->FolderCollection);
			if ($this->SortDirection == "desc") $this->FolderCollection = array_reverse($this->FolderCollection);
			
			$Params->Remove("fid");
				$this->FolderList .= "<h2>Folders</h2>
				<ul class=\"FolderList\">\r\n";
				
			// Display the updirectory link if necessary
			if (count($this->aFolderID) > 0) {
				$ParentFolder = "";
				if (count($this->aFolderID) > 1) {
					for ($i = 0; $i < count($this->aFolderID)-1; $i++) {
						$ParentFolder  = FormatDelimitedString($ParentFolder, $this->aFolderID[$i], $this->FolderDelimiter);
					}
					$Params->Set("did", $ParentFolder);
				} else {
					$Params->Remove("did");
				}
				$this->FolderList .= "<li><a href=\"".$this->SelfUrl.$Params->GetQueryString()."\">Parent Folder</a></li>\r\n";
			}				
				
			// Add actual folders
			for ($i = 0; $i < count($this->FolderCollection); $i++) {
				$Params->Set("did",FormatDelimitedString($this->FolderIDs,$i,$this->FolderDelimiter));
				$this->FolderList .= "<li><a href=\"".$this->SelfUrl.$Params->GetQueryString()."\">".$this->FolderCollection[$i]."</a></li>\r\n";
			}
			$this->FolderList .= "</ul>";
		}
		$this->FolderList .= "</div>";
	}

	function FilePath($Path, $File) {
		if (substr($Path, strlen($Path) - 1, strlen($Path)) != "/") $Path .= "/";
		return $Path.$File;
	}	
	
	function FormatImageList($List, $Title, $Name) {
		$p = new Parameters();
		$ExcludeByPrefix = "ImageID";
		$p->DefineCollection($_GET, $ExcludeByPrefix, 0, 1);
		$p->DefineCollection($_POST, $ExcludeByPrefix, 0, 1);
		$p->Set("PageAction", "Generate");
		$p->Remove("btnSubmit");

		return "<div class=\"Container ".$Name."\">
			<form name=\"frm".$Name."\" method=\"get\" action=\"".$this->SelfUrl."\">"
		   .$p->GetHiddenInputs()
			."<h2>".$Title."</h2>
			<table class=\"FileTable\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
				<tr class=\"ListItem\">
					<th class=\"ItemOption\">".GetBasicCheckBox($Name."_ItemController", 1, 0, " onclick=\"CheckController('frm".$Name."', this, 'ImageID[]')\"")."</th>
					<th class=\"ItemName\">Image</th>
					<th class=\"ItemSize\">Size</th>
					<th class=\"ItemDate\">Date</th>
				</tr>"
				.$List
			."</table>
			<div class=\"ButtonContainer\">
				<input type=\"submit\" name=\"btnSubmit\" value=\"Generate Thumbnails\" class=\"Button\" />
			</div>
			</form>
		</div>";
	}

	function FormatListItem($ID, $Name, $Size, $Date, &$Params, $Alternate = "0") {
		$Alternate = ForceBool($Alternate, 0);
		return "<tr class=\"ListItem ".($Alternate?"Alternate":"")."\">
			<td class=\"ItemOption\">".GetBasicCheckBox("ImageID[]", $ID, 0)."</td>
			<td class=\"ItemName\">".$Name."</td>
			<td class=\"ItemSize\">$Size</td>
			<td class=\"ItemDateModified\">".date($this->DateFormat, $Date)."</td>
		</tr>\r\n";
	}
	
	function GenerateThumbnails() {
		// $this->DefineBrowsingDirectory();
		
		// Retrieve incoming ImageIDs to thumbnail
      $ImagesToThumbnail = ForceIncomingArray("ImageID", array());
		  
		// Find those images in the current folder
		$ThumbnailCollection = array();
		$FileCounter = 0;
		$Files = $this->ImageCollection->GetFiles($this->SortBy, $this->SortDirection, $ThumbnailCollection);
		$BatchSize = count($ImagesToThumbnail);
		$BatchIncrement = 1;
		if ($BatchSize > 10) $BatchSize = 10;
		if ($BatchSize > 0) {
			for ($j = 0; $j < count($Files); $j++) {
				$FileCounter += 1;
				$CurrentFileName = $Files[$j]["Name"];
				$CurrentFileSize = $Files[$j]["Size"];
				$CurrentFileDate = $Files[$j]["Date"];
				$CurrentFileHandlerMethod = $Files[$j]["HandlerMethod"];
				
				if (in_array($FileCounter, $ImagesToThumbnail)) {
					// Generate the thumbnail
					$this->GenerateThumbnail($this->GetFileType($this->CurrentBrowsingDirectory, $CurrentFileName), $CurrentFileName, $this->CurrentBrowsingDirectory);
					// Remove the item from the array
               $key = array_search($FileCounter, $ImagesToThumbnail);
					if ($key !== false) array_splice($ImagesToThumbnail, $key, 1);
					$BatchIncrement++;
					if ($BatchIncrement > $BatchSize) $j = count($Files);
				}
			}
		}
		return $ImagesToThumbnail;		
	}
	
	function GenerateThumbnail($ImageType, $SourceImage, $Path) {
		$FauxContext = "0";
		$CreateFunction = "imagecreatefrom".$ImageType;
		$SaveFunction = "image".$ImageType;
		if (strtolower($CreateFunction) == "imagecreatefromgif" && !function_exists("imagecreatefromgif")) {
			$this->ErrorManager->AddError($FauxContext, $this->Name, "GenerateThumbnail", "Your version of PHP does not appear to have GIF thumbnailing support.");
		} elseif (strtolower($CreateFunction) == "imagecreatefromjpeg" && !function_exists("imagecreatefromjpeg")) {
			$this->ErrorManager->AddError($FauxContext, $this->Name, "GenerateThumbnail", "Your version of PHP does not appear to have JPEG thumbnailing support.");
		} elseif (!function_exists($CreateFunction)) {
			$this->ErrorManager->AddError($FauxContext, $this->Name, "GenerateThumbnail", "Your version of PHP does not appear to have ".$CreateFunction." thumbnailing support.");
		}
			
		$Original = @$CreateFunction($this->FilePath($Path,$SourceImage));
		if (!$Original) $this->ErrorManager->AddError($FauxContext,$this->Name, "GenerateThumbnail", "An error occurred while attempting to copy the source image \"".$SourceImage."\". Your version of php (".phpversion().") may not have ".$ImageType." support.");
		$OriginalHeight = ImageSY($Original);
		$OriginalWidth = ImageSX($Original);
		if ($OriginalHeight < $this->ThumbHeight && $OriginalWidth < $this->ThumbWidth) {
			// Just copy the file
         copy($this->FilePath($Path,$SourceImage), $this->FilePath($Path, $this->ThumbPrefix.$SourceImage));
		} else {
			if ((($OriginalWidth * $this->ThumbHeight) / $OriginalHeight) > $this->ThumbWidth) {
				$ThumbHeight = intval(($OriginalHeight * $this->ThumbWidth) / $OriginalWidth);
				$ThumbWidth = $this->ThumbHeight;
			} else {
				$ThumbHeight = $this->ThumbWidth;
				$ThumbWidth = intval(($OriginalWidth * $this->ThumbHeight) / $OriginalHeight);
			}
			if ($ThumbWidth == 0) $ThumbWidth = 1;
			if ($ThumbHeight == 0) $ThumbHeight = 1;
			$Thumb = imagecreatetruecolor($ThumbWidth, $ThumbHeight);
			$FauxContext = "0";
			if (!$Thumb) $this->ErrorManager->AddError($FauxContext,$this->Name, "GenerateThumbnail", "An error occurred while attempting to create a new image.");		
			if (!imagecopyresampled($Thumb, $Original, 0, 0, 0, 0, $ThumbWidth, $ThumbHeight, $OriginalWidth, $OriginalHeight)) $this->ErrorManager->AddError($FauxContext,$this->Name, "GenerateThumbnail", "An error occurred while copying the source image to the thumbnail image.");		
			if (!@$SaveFunction($Thumb, $this->FilePath($Path, $this->ThumbPrefix.$SourceImage))) $this->ErrorManager->AddError($FauxContext,$this->Name, "GenerateThumbnail", "An error occurred while saving the thumbnail \"_thumb.".$SourceImage."\" to the filesystem. Are you sure that PHP has been configured with both read and write access on this folder?");
		}
	}
	
	function GetFileType($Path, $FileName) {
		$FileExtension = GetExtension($FileName);
		$Return = "Invalid";
		if (in_array($FileExtension, array("jpg", "gif", "bmp", "png", "jpe", "jpeg"))) {
			$File = $this->FilePath($Path, $FileName);
			$ImageInfo = getimagesize($File);
			
			/*
			 http://ca.php.net/manual/en/function.getimagesize.php
			 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel byte order), 8 = TIFF(motorola byte order), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF, 15 = WBMP, 16 = XBM
			*/
			switch ($ImageInfo[2]) {
				case 1:
					$Return = "gif";
					break;
				case 2:
					$Return = "jpeg";
					break;
				case 3:
					$Return = "png";
					break;
				case 15:
					$Return = "wbmp";
					break;
			}
		}
		return $Return;
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
				$this->StyleUrl = $XMan->GetNodeValueByName($MyConfig, "ThumbnailerStyleUrl");
				$this->PageTitle = $XMan->GetNodeValueByName($MyConfig, "PageTitle");
				$this->SortBy = $XMan->GetNodeValueByName($MyConfig, "SortBy");
				$this->SortDirection = $XMan->GetNodeValueByName($MyConfig, "SortDirection");
				$this->DateFormat = $XMan->GetNodeValueByName($MyConfig, "DateFormat");
				$this->ThumbHeight = ForceInt($XMan->GetNodeValueByName($MyConfig, "MaxThumbHeight"), $this->ThumbHeight);
				$this->ThumbWidth = ForceInt($XMan->GetNodeValueByName($MyConfig, "MaxThumbWidth"), $this->ThumbHeight);
				$this->HideFiles = explode(",", $XMan->GetNodeValueByName($MyConfig, "HideFiles"));
				for ($i = 0; $i < count($this->HideFiles); $i++) {
					$this->FullyQualifiedHideFiles[] = $this->CurrentBrowsingDirectory."/".$this->HideFiles[$i];
				}
			} 
		}
		return $this->ErrorManager->Iif();
	}
	
	// Constructor - define default values for class properties
	function Thumbnailer() {
		// Configuration Settings
		$this->ConfigFile = "_config.xml";
		$this->CurrentWorkingDirectory = getcwd();
		
		// Configuration Properties
		$this->Version = "1.1.0";
		$this->Developer = "Mark O'Sullivan";
		$this->Date = "2004 - 2005"; 
		$this->StyleUrl = "_thumbnailer.css";
		$this->PageTitle = "Lussumo Thumbnailer";
		$this->SortBy = "Name";
		$this->SortDirection = "asc";
		$this->DateFormat = "m-d-y";
		
		// Browsing Properties
		$this->FolderDelimiter = "-";
		$this->FolderIDs = ForceIncomingString("did", "");
		if ($this->FolderIDs == "") {
			$this->aFolderID = array();
		} else {
			$this->aFolderID = explode($this->FolderDelimiter, $this->FolderIDs);
		}
		$this->CurrentBrowsingDirectory = $this->CurrentWorkingDirectory;
		$this->SelfUrl = basename(ForceString(@$_SERVER['PHP_SELF'], "index.php"));
		$this->SelfWebPath = ForceString(@$_SERVER['HTTP_HOST'], "").ForceString(@$_SERVER['PHP_SELF'], "");
		// Strip filename from webpath if exists
		if (strpos($this->SelfWebPath,$this->SelfUrl) !== false) $this->SelfWebPath = substr($this->SelfWebPath,0,strpos($this->SelfWebPath,$this->SelfUrl));
		if ($this->SelfWebPath != "") $this->SelfWebPath = "http://".$this->SelfWebPath;
		$this->FolderNavigator = array();
		$this->FolderNavigatorLocation = "";
		$this->Name = "Thumbnailer";
		$this->ThumbnailableFormats = array("gif","jpeg","wbmp","png");
		$this->FolderList = "";
		$this->FileList = "";
		$this->ThumbPrefix = "_thumb.";
		
		$this->HideFiles = array();
		$this->FullyQualifiedHideFiles = array();
	}
}

?>
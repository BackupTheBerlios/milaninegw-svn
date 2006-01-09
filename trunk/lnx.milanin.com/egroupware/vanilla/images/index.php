<?
/*
* Copyright 2003 - 2005 Mark O'Sullivan
* This file is part of the Lussumo Filebrowser.
* The Lussumo Filebrowser is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* The Lussumo Filebrowser is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with the Lussumo Filebrowser; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code is available at www.lussumo.com
* Contact Mark O'Sullivan at mark [at] lussumo [dot] com
* 
* Description: The main application file that draws the filebrowser.
*/

error_reporting(E_ALL);

// Global Constants
define("sgLIBRARY", "../library/");

// Define XML Node Types
define("XmlNodeTypeContainer", 1);
define("XmlNodeTypeContent", 2);

// Application class files
include(sgLIBRARY."Utility.Functions.php");
include(sgLIBRARY."Utility.ErrorManager.class.php");
include(sgLIBRARY."Utility.Parameters.class.php");
include(sgLIBRARY."Utility.XmlManager.class.php");
include(sgLIBRARY."Utility.File.class.php");
include(sgLIBRARY."Filebrowser.Utility.php");
include(sgLIBRARY."Filebrowser.FileCollection.class.php");
include(sgLIBRARY."Filebrowser.Configuration.class.php");

// Define required variables for the application
$ErrorManager = new ErrorManager();
$Config = new Configuration();
$Config->ErrorManager = &$ErrorManager;

// ------------------------------------
// 1. RETRIEVE CONFIGURATION PROPERTIES
// ------------------------------------
$Config->RetrieveConfigurationPropertiesFromXml($Config->CurrentWorkingDirectory);
// Check for subfolder ids if directory browsing is allowed and some folder ids were supplied
if ($Config->BrowseSubFolders && count($Config->aFolderID) > 0) {
   for ($i = 0; $i < count($Config->aFolderID); $i++) {
      $CurrentFolderKey = ForceInt($Config->aFolderID[$i], 0);
      $Config->CurrentBrowsingDirectory = CheckForFolder($Config->CurrentBrowsingDirectory, $CurrentFolderKey, $Config);
      if (!$Config->CurrentBrowsingDirectory) {
         // IF the current browsing directory wasn't found, wipe out all directory settings and start from the root
         $Config->CurrentBrowsingDirectory = $Config->CurrentWorkingDirectory;
         $Config->FolderIDs = "";
         $Config->aFolderID = array();
         break;
      }
   }
}

// If the folder exists, and there is a _config.xml file in the folder, reconfigure the filebrowser
if ($Config->CurrentWorkingDirectory != $Config->CurrentBrowsingDirectory) $Config->RetrieveConfigurationPropertiesFromXml($Config->CurrentBrowsingDirectory);

// -----------------------------------
// 2. RETRIEVE FILE EXTENSION SETTINGS
// -----------------------------------
$File = new File();
$File->Name = $Config->FileTypesFile;
$File->Path = $Config->CurrentWorkingDirectory;
$FileManager = new FileManager();
$FileManager->ErrorManager = &$Config->ErrorManager;
$File = $FileManager->Get($File);

// Create an XML Parser to retrieve configuration settings
$XmlManager = new XmlManager();
$XmlManager->ErrorManager = &$ErrorManager;
$FileTypes = $XmlManager->ParseNode($File->Body);
      
// Create an array of all defined file types
$FileCollections = array();
$FolderCollection = array();
$ExtensionLibrary = array();
   
for ($i = 0; $i < count($FileTypes->Child); $i++) {
   if ($FileTypes->Child[$i]->Name == "FileGroup") {
      $FileCollections[$i] = new FileCollection($FileTypes->Child[$i]->Attributes["Name"]);
      for ($j = 0; $j < count($FileTypes->Child[$i]->Child); $j++) {
         $Node = $FileTypes->Child[$i]->Child[$j];
         if ($Node->Name == "Extensions") {
            // Ignore all items with a handler method of none
            if (@$Node->Attributes["HandlerMethod"] != "None") {
               $CurrentExtensionArray = explode(",",$Node->Value);
               for ($k = 0; $k < count($CurrentExtensionArray); $k++) {
                  $ExtensionLibrary[strtolower($CurrentExtensionArray[$k])] = array($i,$Node->Attributes["HandlerMethod"]);
               }
            }
         }
      }
   }
}

// -----------------
// 3. RETRIEVE FILES
// -----------------
// Loop through files in the current browsing directory
$FolderKey = 0;
$FolderHandle = opendir($Config->CurrentBrowsingDirectory);
$CurrentExtension = "";
$RecordItem = true;
$ThumbnailCollection = array();
while (false !== ($Item = readdir($FolderHandle))) {
   $RecordItem = true;
   if ($Item == "."
       || $Item == ".."
       || in_array($Config->CurrentBrowsingDirectory."/".$Item, $Config->FullyQualifiedHideFiles)
   ) $RecordItem = false;
   if ($Config->DisplayHiddenFiles == "false" && $Item == $Config->SelfUrl) $RecordItem = false;
   if ($Config->DisplayHiddenFiles == "false" && substr($Item,0,1) == "_") $RecordItem = false;
   if ($Config->UseThumbnails && substr($Item,0,7) == "_thumb.") {
      // Don't record the current item in the regular file collections, dump it into a thumbnail collection
      $RecordItem = false;
      $ThumbnailCollection[] = $Item;
   }
   
   if ($RecordItem) {
      // If dealing with a folder, add it to the folder collection
      if (is_dir($Config->CurrentBrowsingDirectory."/".$Item)) {
         $FolderCollection[] = $Item;
      // If not dealing with a folder, add it to the proper file collection
      } else {
         // Match the current file extension with an item in the extension library
         $CurrentExtension = GetExtension($Item);
         $KeyMatch = @$ExtensionLibrary[$CurrentExtension];
         
         // If the match came back positive, add the file to the collection
         if ($KeyMatch) {
            $FileCollections[$ExtensionLibrary[$CurrentExtension][0]]->AddFile($Item, filesize($Config->CurrentBrowsingDirectory."/".$Item), filemtime($Config->CurrentBrowsingDirectory."/".$Item), $ExtensionLibrary[$CurrentExtension][1]);
            
         // If the match came back false, attempt to add this file to the wildcard group
         } elseif ($ExtensionLibrary["*"]) {
            $FileCollections[$ExtensionLibrary["*"][0]]->AddFile($Item, filesize($Config->CurrentBrowsingDirectory."/".$Item), filemtime($Config->CurrentBrowsingDirectory."/".$Item), $ExtensionLibrary["*"][1], $ExtensionLibrary["*"]);
               
         } // Ignore all other files
      }
   }
}
	
// --------------------------
// 4. BUILD THE PAGE ELEMENTS
// --------------------------
// If in a subfolder, 
// and there is no file currently selected, 
// and we're not supposed to display the root introduction 
// and there is no config file for this folder
// Display the first file
if ($Config->FileID == 0 
&& $Config->CurrentWorkingDirectory != $Config->CurrentBrowsingDirectory
&& !$Config->UsePageIntroductionInSubFolders) $Config->FileID = 1;

$FilesToDisplay = $Config->FilesPerPage;
if (!$Config->ShowMultipleFiles) $FilesToDisplay = 1;
$aItemHistory = array();
$FileDisplay = "";
$FileList = "";

// Create a parameters class to manage querystring values
$Params =  new Parameters();
$Params->DefineCollection($_GET);
$Params->Add("fpp", $Config->FilesPerPage);
$Params->Remove("smf");
if ($Config->FolderIDs == "") $Params->Remove("did");

$FileCounter = 0;

// Build the file listing
while (list(, $CurrentFileCollection) = each ($FileCollections)) {
   // Get the sorted files
   $Files = $CurrentFileCollection->GetFiles($Config->SortBy, $Config->SortDirection, $ThumbnailCollection);
   
   if (count($Files) > 0) {
      $FileList .= "<h2>".$CurrentFileCollection->Name."</h2>
         <ul class=\"List Files\">";
         
      $RootPath = substr(CurrentWebPath(), 0, strlen(CurrentWebPath())-1);
      $CurrentPath = $RootPath."/".BuildLiteralPath($Config->FolderNavigator);
      for ($j = 0; $j < count($Files); $j++) {
         $FileCounter += 1;
         
         $CurrentFileName = $Files[$j]["Name"];
         $CurrentFileSize = $Files[$j]["Size"];
         $CurrentFileDate = $Files[$j]["Date"];
         $CurrentFileHandlerMethod = $Files[$j]["HandlerMethod"];
         $CurrentFileHasThumbnail = $Files[$j]["ThumbnailPresent"];
         
         // Collect the item history
         $aItemHistory[] = $FileCounter;
         
         // Check to see if the current file is selected
         $Highlighted = false;
         if (($Config->FileID == $FileCounter || ($FileCounter > $Config->FileID && $FilesToDisplay < $Config->FilesPerPage && $Config->FileID > 0)) && $FilesToDisplay > 0) {
            $FileDisplay .= FormatDisplayedItem($FileCounter, $CurrentFileName, $CurrentFileSize, $CurrentFileHandlerMethod, $Params, $Config);
            $FilesToDisplay = $FilesToDisplay - 1;
            $Highlighted = true;
         }
         
         $FileList .= FormatListItem($FileCounter, $CurrentFileName, $CurrentPath, $CurrentFileSize, $CurrentFileDate, $Highlighted, $Params, $CurrentFileHasThumbnail, $Config);
         
         // Check for a "save as" request
         if ($Config->GetFileID == $FileCounter) {
				$FolderPath = substr($Config->CurrentBrowsingDirectory, strlen($Config->CurrentWorkingDirectory)+1,(strlen($Config->CurrentBrowsingDirectory)-strlen($Config->CurrentWorkingDirectory)+1));
				SaveAsDialogue($FolderPath, $CurrentFileName);
			}
      }
      $FileList .= "</ul>\r\n";
   }
}	
if ($FileDisplay == "" && $Config->FileID != 0) $FileDisplay = "<div class=\"Introduction\">The requested file could not be found.</div>\r\n";
$FileDisplay = "<form name=\"frmLinkContainer\" action=\"\">".$FileDisplay."</form>\r\n";

// Build the folder listing
if ($Config->BrowseSubFolders && ((count($FolderCollection) > 0) || count($Config->aFolderID) > 0)) {
   $Params->Remove("fid");
   $FileList .= "<h2>Folders</h2>
      <ul class=\"List Folders\">";
      
   // Display the updirectory link if necessary
   if (count($Config->aFolderID) > 0 && $Config->BrowseSubFolders) {
      $ParentFolder = "";
      if (count($Config->aFolderID) > 1) {
         for ($i = 0; $i < count($Config->aFolderID)-1; $i++) {
            $ParentFolder  = FormatDelimitedString($ParentFolder, $Config->aFolderID[$i], $Config->FolderDelimiter);
         }
         $Params->Set("did", $ParentFolder);
      } else {
         $Params->Remove("did");
      }
      $FileList .= "<li><a href=\"".CurrentUrl().$Params->GetQueryString()."\">Parent Folder</a></li>";
   }
      
      
   // Sort the folders
   usort($FolderCollection, "strcasecmp");
   reset($FolderCollection);
   if ($Config->SortDirection == "desc") $FolderCollection = array_reverse($FolderCollection);
   
   // Add actual folders
   for ($i = 0; $i < count($FolderCollection); $i++) {
      $Params->Set("did",FormatDelimitedString($Config->FolderIDs,$i,$Config->FolderDelimiter));
      $FileList .= "<li>
         <a href=\"".CurrentUrl().$Params->GetQueryString()."\">".$FolderCollection[$i]."</a>
         </li>";
   }
   $FileList .= "</ul>\r\n";
}

// ------------------		
// 5. WRITE PAGE HEAD
// ------------------
// Define the current folder path
$RootPath = substr(CurrentWebPath(),0,strlen(CurrentWebPath())-1);

$CurrentPath = "<a href=\"$RootPath?fpp=".$Config->FilesPerPage."\">".str_replace("http://","",$RootPath)."</a>";

echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-ca\">
   <head>
   <title>".$Config->PageTitle."</title>\r\n
   <link rel=\"stylesheet\" type=\"text/css\" href=\"".$Config->StyleUrl."\" />\r\n
   <script language=\"Javascript\" type=\"text/javascript\">
      //<![CDATA[
      var BodyLoaded = 0;
      if (document.all && !document.getElementById) {
          document.getElementById = function(id) {
               return document.all[id];
          }
      }
      function copy(inElement) {
         if (inElement.createTextRange) {
            var range = inElement.createTextRange();
            if (range && BodyLoaded==1) range.execCommand('Copy');
         } else {
            var flashcopier = 'flashcopier';
            if(!document.getElementById(flashcopier)) {
               var divholder = document.createElement('div');
               divholder.id = flashcopier;
               document.body.appendChild(divholder);
            }
            document.getElementById(flashcopier).innerHTML = '';
            var divinfo = '<embed src=\"_clipboard.swf\" FlashVars=\"clipboard='+escape(inElement.value)+'\" width=\"0\" height=\"0\" type=\"application/x-shockwave-flash\"></embed>';
            document.getElementById(flashcopier).innerHTML = divinfo;
         }
      }
      function writeImage(imageName, imageWidth, imageHeight, imageID) {
         var windowWidth = getWindowWidth();
         if (windowWidth < imageWidth) {
            var newWidth = windowWidth - 40;
            var newHeight = Math.round((newWidth*imageHeight)/(imageWidth+40));

            document.write('<span id=\"sm'+imageID+'\">'
               +'<div class=\"Notice\">Note: this image has been shrunk to fit on your screen. <a href=\"Javascript:resizeImage('+imageID+');\">Click here to view the image at actual size.</a></div>'
               +'<img src=\"'+imageName+'\" height=\"'+newHeight+'\" width=\"'+newWidth+'\" />'
            +'</span>'
            +'<span id=\"lg'+imageID+'\" style=\"display: none;\">'
               +'<div class=\"Notice\">Note: this image does not fit on your screen. <a href=\"Javascript:resizeImage('+imageID+');\">Click here to fit the image to your screen.</a></div>'
               +'<img src=\"'+imageName+'\" height=\"'+imageHeight+'\" width=\"'+imageWidth+'\" />'
            +'</span>');
         } else {
            document.write('<img src=\"'+imageName+'\" height=\"'+imageHeight+'\" width=\"'+imageWidth+'\" />');
         }
      }
      function getWindowWidth() {
         var myWidth = 0;
         if( typeof( window.innerWidth ) == 'number' ) {
            myWidth = window.innerWidth - 20;
         } else if (document.documentElement && document.documentElement.clientWidth) {
            myWidth = document.documentElement.clientWidth;
         } else if (document.body && document.body.clientWidth) {
            myWidth = document.body.clientWidth;
         } else {
            myWidth = screen.width;
         }
         return myWidth;
      }
      function resizeImage(imageID, link) {
         var lg = document.getElementById('lg'+imageID);
         var sm = document.getElementById('sm'+imageID);
         if (lg && sm) {
            switchVisibility(lg);
            switchVisibility(sm);
         }
      }
      function switchVisibility(object) {
         if (object.style.display == 'none') {
            object.style.display = 'block';
         } else {
            object.style.display = 'none';
         }
      }
      //]]>
   </script>
   </head>
   <body onload=\"BodyLoaded=1\">
      <div class=\"SiteContainer\">
      <div class=\"Head\">
         <h1>".$Config->PageTitle."</h1>
         ".$CurrentPath.BuildPath($Config->FolderNavigator, $Config->FilesPerPage)."
      </div>");

// ------------------------------------
// 6. CONFIGURE & WRITE NAVIGATION MENU
// ------------------------------------
$TotalItemCount = ForceInt(count($aItemHistory), 0);
if ($TotalItemCount > 0) $TotalItemCount = $TotalItemCount-1;
$FirstItem = ($TotalItemCount == 0)?false:$aItemHistory[0];
$LastItem = ($TotalItemCount == 0)?false:$aItemHistory[$TotalItemCount];
$NextItem = false;
$PreviousItemGroup = false;
$PreviousItem = false;

// If viewing a file, check to see if the file id exists in the item history
if ($TotalItemCount > 0) {
   $FileKey = false;
   if ($Config->FileID > 0) $FileKey = array_search($Config->FileID,$aItemHistory);
   
   if ($FileKey !== false) {
      // Don't go past the end
      if ($Config->ShowMultipleFiles) {
         if ($FileKey + $Config->FilesPerPage <= $TotalItemCount) {
            $NextItem = $aItemHistory[$FileKey+$Config->FilesPerPage];
         } else {
            $NextItem = false;
         }
      } else {
         if ($FileKey + 1 <= $TotalItemCount) {
            $NextItem = $aItemHistory[$FileKey+1];
         } else {
            $NextItem = false;
         }
      }
      // Don't go before the beginning
      if ($FileKey < $Config->FilesPerPage) {
         $PreviousItemGroup = $aItemHistory[0];
      } else {
         $PreviousItemGroup = $aItemHistory[$FileKey-$Config->FilesPerPage];
      }
      if ($FileKey == 0) {
         $FirstItem = $aItemHistory[0];
         $PreviousItem = false;
         $PreviousItemGroup = false;
      } else {
         $PreviousItem = $aItemHistory[$FileKey-1];
      }
   } else {
      $NextItem = $aItemHistory[0];
      $PreviousItemGroup = false;
      $PreviousItem = false;
   }
} 

$Params->Remove("fid");
$Params->Set("did", $Config->FolderIDs);
$Params->Set("fid", $FirstItem);
$Menu = FormatMenuItem("NavFirstItem", $Params, "|&lt;", "First", $FirstItem);

$Params->Set("fid",$PreviousItemGroup);
$Params->Add("smf",1);
$Menu .= FormatMenuItem("NavPreviousItemGroup", $Params, "&lt;&lt;", "Previous ".$Config->FilesPerPage, $PreviousItemGroup);

$Params->Remove("smf");
$Params->Set("fid",$PreviousItem);
$Menu .= FormatMenuItem("NavPreviousItem", $Params, "Previous", "Previous", $PreviousItem);

$Params->Set("fid",$NextItem);
$Menu .= FormatMenuItem("NavNextItem", $Params, "Next", "Next", $NextItem);

$Params->Add("smf",1);
$Params->Set("fid",$NextItem);
$Menu .= FormatMenuItem("NavNextItemGroup", $Params, "&gt;&gt;", "Next ".$Config->FilesPerPage, $NextItem);

$Params->Remove("smf");
$Params->Set("fid",$LastItem);
$Menu .= FormatMenuItem("NavLastItem", $Params, "&gt;|", "Last", $LastItem);

// ----------------------------------
// 7. WRITE THE REMAINDER OF THE PAGE
// ----------------------------------
echo("<ul class=\"NavMenu MainMenu\">".$Menu."</ul>\r\n");

echo("<div class=\"DisplayContainer\">\r\n");
// If a file has not been requested, write out the introductory paragraph (if there is one)
if (
   $Config->FileID == 0 && $Config->PageIntroduction != "" 
      && ($Config->CurrentWorkingDirectory == $Config->CurrentBrowsingDirectory 
      || ($Config->CurrentWorkingDirectory != $Config->CurrentBrowsingDirectory 
         && $Config->UsePageIntroductionInSubFolders))) echo("<div class=\"Introduction\">".$Config->PageIntroduction."</div>\r\n");

// Write out the selected files
echo($FileDisplay);

echo("</div>\r\n" // End DisplayContainer
   ."<ul class=\"NavMenu SubMenu\">\r\n".$Menu."</ul>\r\n"		
   ."<div class=\"ListContainer\">\r\n");

// Write out the file/folder listing
if (trim($FileList) == "") {
   echo("<div class=\"ListEmpty\">No files or folders could be found.</div>\r\n");
} else {
   echo($FileList);
}

// Write out the page footer			
echo("</div>\r\n" // End ListContainer
      ."<div class=\"Foot\">
         <form name=\"frmPager\" action=\"".CurrentUrl()."\" method=\"get\">");
         $p = new Parameters();
         $p->DefineCollection($_GET);
         $p->Remove("fpp");
         echo($p->GetHiddenInputs()
         ."<div class=\"Pager\">
            <div class=\"PagerLabel\">Files/Page: </div>
            <div class=\"PagerValue\"><input type=\"text\" name=\"fpp\" class=\"PagerInput\" value=\"".$Config->FilesPerPage."\" onchange=\"document.frmPager.submit();\" /></div>
         </div>
         </form>
         <div class=\"ApplicationInformation\"><a href=\"http://lussumo.com\">Lussumo</a> <a href=\"http://thefilebrowser.com\">Filebrowser</a> ".$Config->Version." &copy; ".$Config->Date."</div>
         <div class=\"DeveloperInformation\">Developed by ".$Config->Developer."</div>
      </div>
   </div>
   </body>
</html>");
?>
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
* Description: Utility functions specific to the filebrowser
* Applications utilizing this file: Filebrowser;
*/


function CurrentUrl() {
   return basename(ForceString(@$_SERVER["PHP_SELF"], ""));
}

function CurrentWebPath() {
   $SelfWebPath = ForceString(@$_SERVER["HTTP_HOST"], "").ForceString(@$_SERVER["PHP_SELF"], "");
   // Strip filename from webpath if exists
   if (strpos($SelfWebPath, CurrentUrl()) !== false) $SelfWebPath = substr($SelfWebPath,0,strpos($SelfWebPath, CurrentUrl()));
   if ($SelfWebPath != "") $SelfWebPath = "http://".$SelfWebPath;
   return $SelfWebPath;
}

function FilePath($Path, $File) {
   if (substr($Path, strlen($Path) - 1, strlen($Path)) != "/") $Path .= "/";
   return $Path.$File;
}	

function FormatDisplayedItem($ItemID, $FileName, $FileSize, $HandlerMethod, &$Params, $Config) {
   $FolderPath = substr($Config->CurrentBrowsingDirectory, strlen($Config->CurrentWorkingDirectory)+1,(strlen($Config->CurrentBrowsingDirectory)-strlen($Config->CurrentWorkingDirectory)+1));
   $FolderPath = ($FolderPath != "")?$FolderPath."/".$FileName:$FileName;
   $EncodedPath = EncodeLinkUrl(FilePath(CurrentWebPath(),$FolderPath));
   
   $Params->Add("gid", $ItemID);
   $Return = "<input type=\"hidden\" name=\"Item$ItemID\" value=\"".$EncodedPath."\" />
      <div class=\"DisplayItem\">
         <h2><a href=\"".CurrentWebPath().$FolderPath."\">$FileName</a></h2>
         <ul class=\"Options\">
            <li class=\"Save\"><a href=\"".CurrentUrl().$Params->GetQueryString()."\">Save</a></li>
            <li class=\"Copy\"><a href=\"Javascript:copy(document.frmLinkContainer.Item$ItemID);\">Copy url</a></li>\r\n";
   $Handled = false;
   $Params->Remove("gid");

   switch ($HandlerMethod) {
      case "Image":
         $Handled = true;
         $Return .= "<input type=\"hidden\" name=\"ImgTag$ItemID\" value='<img src=\"".$EncodedPath."\" />' />
               <li class=\"CopyImg\"><a href=\"Javascript:copy(document.frmLinkContainer.ImgTag$ItemID);\">Copy img tag</a></li>
            </ul>
            <div class=\"DisplayItemImage\">";
         if ($Config->FitImagesToPage) {
            $ImageSize = @getimagesize($FolderPath);
            if ($ImageSize) {
               $Return .= "<script>writeImage('$EncodedPath', ".$ImageSize[0].", ".$ImageSize[1].", '".$ItemID."');</script>";
            } else {
               $Return .= "<img src=\"$EncodedPath\" alt=\"\" />";
            }
         } else {
            $Return .= "<img src=\"$EncodedPath\" alt=\"\" />";
         }
         $Return .= "</div>\r\n";
         break;
      case "IFrame":
         $Handled = true;
         $Return .= "<li class=\"CopyImg\"><a href=\"Javascript:copy(document.frmLinkContainer.AnchorTag$ItemID);\">Copy anchor tag</a></li>
            <input type=\"hidden\" name=\"AnchorTag$ItemID\" value=\"<a href='".$EncodedPath."'>".CurrentWebPath().$FolderPath."</a>\" />
            <li class=\"View\"><a href=\"".CurrentWebPath().$FolderPath."\" target=\"_blank\">View</a></li>
            </ul>
         <div class=\"DisplayItemIFrame\"><iframe src=\"$FolderPath\"></iframe></div>\r\n";
         break;
      case "TextArea":
         $Handled = true;
         // Retrieve the file contents
         $File = new File();
         $File->Name = $FolderPath;
         $File->Path = "./";
         $FileManager = new FileManager();
         $FileManager->ErrorManager = &$Config->ErrorManager;
         $File = $FileManager->Get($File);
         if (!$File) {
            $FauxContext = "0";
            $Config->ErrorManager->AddError($FauxContext,"Filebrowser", "FormatDisplayedItem", "An error occurred while retrieving the file contents.", "", 0);
            $FileContents = $Config->ErrorManager->GetSimple();
         } else {
            // Make sure that a "</textarea>" tag doesn't kill my textarea
            $FileContents = str_replace("<", "&#60;", $File->Body);
         }
         $Return .= "<li class=\"CopyImg\"><a href=\"Javascript:copy(document.frmLinkContainer.AnchorTag$ItemID);\">Copy anchor tag</a></li>
            <input type=\"hidden\" name=\"AnchorTag$ItemID\" value='<a href=\"".$EncodedPath."\">".CurrentWebPath().$FolderPath."</a>' />
            <li class=\"View\"><a href=\"".CurrentWebPath().$FolderPath."\" target=\"_blank\">View</a></li>
            </ul>
         <div class=\"DisplayItemTextArea\"><textarea>$FileContents</textarea></div>\r\n";
         break;
      case "EmbedFlash":
         $Handled = true;
         $EmbedString = "<object type=\"application/x-shockwave-flash\" data=\"".$EncodedPath."\"";
         if ($Config->PluginHeight > 0 && $Config->PluginWidth > 0) $EmbedString .= " height=\"".$Config->PluginHeight."\" width=\"".$Config->PluginWidth."\"";
         $EmbedString .= "><param name=\"movie\" value=\"".$EncodedPath."\" />You do not appear to have the latest flash plugin installed</object>";
         
         $Return .= "<li class=\"CopyImg\"><a href=\"Javascript:copy(document.frmLinkContainer.EmbedTag$ItemID);\">Copy embed tag</a></li>
            <input type=\"hidden\" name=\"EmbedTag$ItemID\" value='".$EmbedString."' />
            </ul>
            <div class=\"DisplayItemFlash\">".$EmbedString."</div>\r\n";			
         break;
      case "Embed":
         $Handled = true;
         $EmbedString = "<embed src='".CurrentWebPath().$FolderPath."'></embed>";
         $Return .= "<li class=\"CopyImg\"><a href=\"Javascript:copy(document.frmLinkContainer.EmbedTag$ItemID);\">Copy embed tag</a></li>
            <input type=\"hidden\" name=\"EmbedTag$ItemID\" value=\"".$EmbedString."\" />
            </ul>
         <div class=\"DisplayItemEmbed\">".$EmbedString."</div>\r\n";
         break;
      case "EmbedQuicktime":
         $Handled = true;
         $EmbedString = "<embed src='".$EncodedPath."'";
         if ($Config->PluginHeight > 0 && $Config->PluginWidth > 0) $EmbedString .= " height='".$Config->PluginHeight."' width='".$Config->PluginWidth."'";
         $EmbedString .= "></embed>";
         $Return .= "<li class=\"CopyImg\"><a href=\"Javascript:copy(document.frmLinkContainer.EmbedTag$ItemID);\">Copy embed tag</a></li>
            <input type=\"hidden\" name=\"EmbedTag$ItemID\" value=\"".$EmbedString."\" />
            </ul>
         <div class=\"DisplayItemEmbed\">".$EmbedString."</div>\r\n";
         break;
      default: 
         // HyperLink handler method
         $Return .= "</ul>
         <div class=\"DisplayItemBlank\"></div>\r\n";
         $Handled = true;
         break;
   }
   if (!$Handled) {
      $Return = "";
   } else {
      $Return .= "</div>\r\n";
   }
   return $Return;
}	

function FormatListItem($ID, $Name, $Path, $Size, $Date, $Highlighted, &$Params, $CurrentFileHasThumbnail, $Config) {
   $Return = "<li class=\"ListItem".($Config->UseThumbnails?" Thumbed":"")."\">
      <ul>";
   $Params->Set("fid",$ID);
   if ($Config->UseThumbnails) {
      if ($CurrentFileHasThumbnail) {
         $ThumbPath = EncodeLinkUrl(FilePath($Path, "_thumb.".$Name));
         $Return .= "<li class=\"ItemThumb\">"
            ."<a href=\"".CurrentUrl().$Params->GetQueryString()."\" style=\"background:url('".$ThumbPath."') center center no-repeat;\">"
            ."<img src=\"".$ThumbPath."\" border=\"0\" alt=\"\" />"
            ."</a>"
            ."</li>\r\n";
      } else {
         $Return .= "<li class=\"ItemThumb NoPreview\">"
            ."<a href=\"".CurrentUrl().$Params->GetQueryString()."\">"
            ."Preview Unavailable"
            ."</a>"
            ."</li>";
      }
   }
   $Return .= "<li class=\"ItemName\">\r\n";
   if (!$Highlighted) {
      $Return .= "<a href=\"".CurrentUrl().$Params->GetQueryString()."\">$Name</a>";
   } else {
      $Return .= $Name;
   }
   $Return .= "</li>
      <li class=\"ItemSize\">$Size</li>
      <li class=\"ItemDateModified\">".date($Config->DateFormat, $Date)."</li>
      </ul>
      </li>\r\n";
   return $Return;
}
function FormatMenuItem($CssClass, $Params, $Link, $Alt, $Active) {
   $Return = "<li class=\"".$CssClass."\">";
   if ($Active !== false) $Return .= "<a href=\"".CurrentUrl().$Params->GetQueryString()."\" title=\"$Alt\">";
   $Return .= $Link;
   if ($Active !== false) $Return .= "</a>";
   $Return .= "</li>\r\n";
   return $Return;
}

// Append a folder name to the current browsing directory
function AppendFolder($RootPath, $FolderToAppend) {
	if (substr($RootPath, strlen($RootPath)-1, strlen($RootPath)) == "/") $RootPath = substr($RootPath, 0, strlen($RootPath) - 1);
	if (substr($FolderToAppend,0,1) == "/") $FolderToAppend = substr($FolderToAppend,1,strlen($FolderToAppend));
	return $RootPath."/".$FolderToAppend;
}

// Build the navigation path to the current browsing directory
function BuildPath($FolderNavigator, $FilesPerPage = "10") {
	$s = "";
	for ($i = 0; $i < count($FolderNavigator); $i++) {
		$s .= "/<a href=\"".CurrentUrl()."?fpp=".$FilesPerPage."&did=".$FolderNavigator[$i][1]."\">".$FolderNavigator[$i][0]."</a>";
	}
	return $s;
}
function BuildLiteralPath($FolderNavigator) {
	$s = "";
	for ($i = 0; $i < count($FolderNavigator); $i++) {
		$s .= "/".$FolderNavigator[$i][0];
	}
	return $s;
}

// returns the complete path to the folder or false if not found
function CheckForFolder($Path, $FolderKey, &$Config) {
	$FolderHandle = opendir($Path);
	$aCurrentSubFolders = array();
   // Only look at folders
	while (false !== ($Item = readdir($FolderHandle))) {
		if ($Item != '.'
         && $Item != '..'
         && is_dir($Path."/".$Item)
         && !in_array($Path."/".$Item, $Config->FullyQualifiedHideFiles)) $aCurrentSubFolders[] = $Item;
	}
	closedir($FolderHandle);
   // Sort the folders according to the config setting
   usort($aCurrentSubFolders, "strcasecmp");
   reset($aCurrentSubFolders);
   if ($Config->SortDirection == "desc") $aCurrentSubFolders = array_reverse($aCurrentSubFolders);   
   
	// If the key supplied is less than the total count of folders found, append the folder name to the path
	if ($FolderKey < count($aCurrentSubFolders)) {
		$Config->FolderNavigatorLocation = FormatDelimitedString($Config->FolderNavigatorLocation, $FolderKey, $Config->FolderDelimiter);
		$Config->FolderNavigator[] = array($aCurrentSubFolders[$FolderKey], $Config->FolderNavigatorLocation);
		return AppendFolder($Path, $aCurrentSubFolders[$FolderKey]);
	} else {
		return false;
	}
}
function EncodeLinkUrl($Url) {
	return str_replace("'", "%27", $Url);
}
function FormatDelimitedString($String, $Addition, $Delimiter) {
	$String = $String."";
	$Addition = $Addition."";
	$String = trim($String);
	$StringLength = strlen($String);
	if ($String != "") {
		if (substr($String,0,1) == $Delimiter) $String = substr($String, 1, $StringLength-1);
		if (substr($String,$StringLength-1,$StringLength) == $Delimiter) $String = substr($String, 0, $StringLength-1);
	}
	$Addition = ForceString($Addition, "");
	$AdditionLength = strlen($Addition);
	if ($Addition != "") {
		if (substr($Addition,0,1) == $Delimiter) $Addition = substr($Addition, 1, $AdditionLength-1);
		if (substr($Addition,$AdditionLength-1,$AdditionLength) == $Delimiter) $Addition = substr($Addition, 0, $AdditionLength-1);
	}
   $sReturn = "";
	if ($String != "" && $Addition != "") {
		$sReturn = $String.$Delimiter.$Addition;
	} elseif ($String != "" && $Addition == "") {
		$sReturn = $String;
	} elseif ($String == "" && $Addition != "") {
		$sReturn = $Addition;
	}
   
   return $sReturn;
}
function FormatFileSize($FileSize) {
	if ($FileSize > 1048576) {
		return intval((($FileSize / 1048576) * 100) + 0.5) / 100 ."mb";
	} elseif ($FileSize > 1024) {
		return ceil($FileSize / 1024)."kb";
	} else {
		return $FileSize."b";
	}
}
// strip the file extension from a file name
function GetExtension($FileName) {
	if (strstr($FileName, '.')) {
		return strtolower(substr(strrchr($FileName, '.'), 1, strlen($FileName)));
	} else {
		return "";
	}
}
?>
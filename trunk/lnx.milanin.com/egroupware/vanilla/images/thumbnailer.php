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
* Description: Creates and saves thumbnails to the filesystem.
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
include(sgLIBRARY."Filebrowser.Thumbnailer.class.php");

// Instantiate the Error manager
$ErrorManager = new ErrorManager();
$PageAction = ForceIncomingString("PageAction", "");
$DirectoryID = ForceIncomingString("did", "");

$tn = new Thumbnailer();
$tn->ConfigFile = "_config.xml";
$tn->ErrorManager = &$ErrorManager;
$tn->DefineProperties();
if ($PageAction == "Generate") {
   $ImagesLeftToThumbnail = $tn->GenerateThumbnails();
   if (count($ImagesLeftToThumbnail) == 0) $PageAction = "Complete";
}

echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-ca\">
<head>
<title>Lussumo Thumbnailer</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"".$tn->StyleUrl."\" />
<script language=\"Javascript\" type=\"text/javascript\">
	//<![CDATA[
   function CheckController(formName, controller, controlled) {
      var frm = document[formName];
      var CheckedStatus = controller.checked;
      if (frm[controlled]) {
         if (frm[controlled].length) {
            for (i = 0; i < frm[controlled].length; i++) {
               frm[controlled][i].checked = CheckedStatus;
            }
         } else {
            frm[controlled].checked = CheckedStatus;
         }
      }
   }
	//]]>
</script>
</head>
<body>");
   if ($PageAction == "Generate") {
		$p = new Parameters();
      $ExcludeByPrefix = "ImageID";
		$p->DefineCollection($_GET, $ExcludeByPrefix, 0, 1);
		$p->DefineCollection($_POST, $ExcludeByPrefix, 0, 1);
		$p->Set("PageAction", "Generate");
		$p->Remove("btnSubmit");
      echo("<form name=\"frmThumbnailer\" method=\"get\" action=\"".$tn->SelfUrl."\">"
		   .$p->GetHiddenInputs());
      
      $ImagesLeft = count($ImagesLeftToThumbnail);
      for ($i = 0; $i < $ImagesLeft; $i++) {
         echo("<input type=\"hidden\" name=\"ImageID[]\" value=\"".$ImagesLeftToThumbnail[$i]."\" />");
      }
      echo("</form><script language=\"Javascript\">
         setTimeout(\"document.frmThumbnailer.submit();\",300);
      </script>
      <h1>Lussumo Thumbnailer</h1>
      <a class=\"BackToFilebrowser\" href=\"./index.php\">Back to Filebrowser</a>
      <div class=\"Introduction\">
         Processing thumbnail batch
         <br /><a href=\"#\" onclick=\"document.frmThumbnailer.submit();\">".FormatPlural($ImagesLeft, "item", "items")." remaining in batch...</a>
      </div>");
   } elseif ($PageAction == "Complete") {
      echo("<h1>Lussumo Thumbnailer</h1>
      <a class=\"BackToFilebrowser\" href=\"./index.php\">Back to Filebrowser</a>
      <div class=\"Introduction\">
         Batch Process Completed Successfully.
         <br /><a href=\"".$tn->SelfUrl.(($DirectoryID != "")?"?did=".$DirectoryID:"")."\">Click here to continue</a>
      </div>");
   } else {
      echo("<h1>Lussumo Thumbnailer</h1>
      <a class=\"BackToFilebrowser\" href=\"./index.php".($DirectoryID != ""?"?did=".$DirectoryID:"")."\">Back to Filebrowser</a>
      <div class=\"Introduction\">
         <strong>This is the Lussumo Thumbnailer.</strong>
         <br />You can use this application to create thumbnail images for your Lussumo Filebrowser.
         <br /><br />Choose the images for which you would like to create thumbnails and click the \"Generate Thumbnails\" button.
      </div>
      <div class=\"Body\">
      ".$tn->FolderList);
      if ($tn->FileList == "") {
         echo("<div class=\"Container Thumbnailed\">
            <h2>No Images Found</h2>
            <p>There were no thumbnail candidate images found in the selected folder.</p>
         </div>");
      } else {
         echo($tn->FileList);
      }
      echo("</div>");
   }
echo("<div class=\"Foot\">
   <div class=\"ApplicationInformation\"><a href=\"http://lussumo.com\">Lussumo</a> <a href=\"http://thefilebrowser.com\">Filebrowser</a> &copy; ".$tn->Date."</div>
   <div class=\"DeveloperInformation\">Developed by ".$tn->Developer."</div>
</div>
   </body>
</html>");
?>


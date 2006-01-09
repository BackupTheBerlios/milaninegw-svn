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
* Description: Feed Builder for various types of feeds. Current feeds are: Atom
*/

include("../appg/settings.php");
include(agAPPLICATION_PATH."appg/headers.php");
include(sgLIBRARY."Utility.Functions.php");
include(sgLIBRARY."Utility.Database.class.php");
include(sgLIBRARY."Utility.SqlBuilder.class.php");
include(sgLIBRARY."Utility.MessageCollector.class.php");
include(sgLIBRARY."Utility.ErrorManager.class.php");
include(sgLIBRARY."Utility.ObjectFactory.class.php");
include(sgLIBRARY."Utility.StringManipulator.class.php");
include(sgLIBRARY."Utility.Context.class.php");
include(sgLIBRARY."Utility.Page.class.php");
include(sgLIBRARY."Utility.Writer.class.php");
include(sgLIBRARY."Utility.Control.class.php");
include(sgLIBRARY."Vanilla.Functions.php");
include(sgLIBRARY."Vanilla.Session.class.php");
include(sgLIBRARY."Vanilla.User.class.php");

$Context = new Context();

define("DATE_FORMAT", "Y-m-d\TH:i:sO");
$Type = ForceIncomingString("Type", "");
$WritePage = 1;
$UserIsAuthenticated = 1;

// 2005-07-23 - mark@lussumo.com - I've currently only build the feed for Atom.
// Other feed types may be added later.
if (!in_array($Type, array("atom"))) $Type = "";

// Create a class to hold a feed entry
class FeedEntry {
   var $Title;
   var $Link;
   var $Id;
   var $Published;
   var $Updated;
   var $AuthorName;
   var $AuthorUrl;
   var $Summary;
   var $Content;
   var $Category;
   var $CategoryLink;
   
   function Clear() {
      $this->Title = "";
      $this->Link = "";
      $this->Id = "";
      $this->Published = "";
      $this->Updated = "";
      $this->AuthorName = "";
      $this->AuthorUrl = "";
      $this->Summary = "";
      $this->Content = "";
      $this->Category = "";
      $this->CategoryLink = "";
   }
   
   function GetPropertiesFromDataSet($DataSet, &$Context) {
      $this->Title = FormatHtmlStringInline(ForceString($DataSet["Name"], ""));
      $this->Link = PrependString("http://", AppendFolder(agDOMAIN, "comments.php?DiscussionID=".ForceInt($DataSet["DiscussionID"], 0)));
      $this->Id = $this->Link;
      $this->Published = FixDate(@$DataSet["DateCreated"]);
      $this->Updated = FixDate(@$DataSet["DateLastActive"]);
      $this->AuthorName = FormatHtmlStringInline(ForceString($DataSet["AuthUsername"], ""));
      $this->AuthorUrl = PrependString("http://", AppendFolder(agDOMAIN, "account.php?u=".ForceInt($DataSet["AuthUserID"], 0)));
      $this->Content = $this->RemoveHtml(ForceString(@$DataSet["Body"], ""));
      $this->Summary = SliceString($this->Content, 200);
      $this->Summary = str_replace("\r\n", " ", $this->Content);
      $this->Content = str_replace("\r\n", "<br />", $this->Content);
      
      if (agUSE_CATEGORIES) {
         $this->Category = FormatStringForDisplay(ForceString($DataSet["Category"], ""), true);
         $this->CategoryLink = "http://".AppendFolder(agDOMAIN, "?CategoryID=".ForceInt($DataSet["CategoryID"], 0));
      }
   }
   
   function RemoveHtml($InString) {
      $sReturn = strip_tags($InString);
      $sReturn = htmlspecialchars($sReturn);
      return $sReturn;
   }
}
function FixDate($Date = "") {
   if ($Date == "") {
      $NewDate = date(DATE_FORMAT, mktime());
   } else {
      $NewDate = date(DATE_FORMAT, UnixTimestamp($Date));
   }
   
   // Dates that look like this:
   // 2005-07-23T18:44:53-0400
   // Need to look like this:
   // 2005-07-23T18:44:53-04:00
   if (strlen($NewDate) != 24) {
      return $NewDate;
   } else {
      return substr($NewDate, 0, 22).":".substr($NewDate, 22);
   }
}
if ($Type != "") {
   
   // Perform some http authentication if public browsing is not enabled.
   if (!agPUBLIC_BROWSING && $Context->Session->UserID == 0) {
      $UserIsAuthenticated = 0; // Assume user is not authenticated
      $PHP_AUTH_USER = ForceString(@$_SERVER["PHP_AUTH_USER"], "");
      $PHP_AUTH_PW = ForceString(@$_SERVER["PHP_AUTH_PW"], "");
      
      if ($PHP_AUTH_USER != "" && $PHP_AUTH_PW != "") {
         // Validate the inputs
         $s = $Context->ObjectFactory->NewContextObject($Context, "SqlBuilder");
         $s->SetMainTable("User", "u");
         $s->AddSelect("UserID", "u");
         $s->AddWhere("Name", FormatStringForDatabaseInput($PHP_AUTH_USER), "=");
         $s->AddWhere("Password", FormatStringForDatabaseInput($PHP_AUTH_PW), "=", "and", "md5");
         
         $ValidationData = $Context->Database->Select($Context, $s, "Feed", "ValidateCredentials", "An error occurred while validating user credentials.");
         if ($Context->Database->RowCount($ValidationData) > 0) $UserIsAuthenticated = true;
      }         
      
      if (!$UserIsAuthenticated) {
         header('WWW-Authenticate: Basic realm="Private"');
         header('HTTP/1.0 401 Unauthorized');
      }      
   }
   
   if ($UserIsAuthenticated) {
      // Create a new sqlbuilder to retrieve feed data
      $s = $Context->ObjectFactory->NewContextObject($Context, "SqlBuilder");
      $s->SetMainTable("Discussion", "d");
      $s->AddSelect(array("DiscussionID", "CategoryID", "AuthUserID", "Name", "DateCreated", "DateLastActive", "CountComments"), "d");
   
      // Get the first comment
      $s->AddJoin("Comment", "fc", "CommentID", "d", "FirstCommentID", "inner join");
      $s->AddSelect("Body", "fc");
   
      // Get author
      $s->AddJoin("User", "u", "UserID", "d", "AuthUserID", "left join");
      $s->AddSelect("Name", "u", "AuthUsername");
   
      // Get category
      $s->AddJoin("Category", "c", "CategoryID", "d", "CategoryID", "left join");
      $s->AddSelect("Name", "c", "Category");
         
      // Limit to roles with access to this category
      if ($Context->Session->UserID > 0) {
         $s->AddJoin("CategoryRoleBlock", "crb", "CategoryID and crb.RoleID = ".$Context->Session->User->RoleID, "d", "CategoryID", "left join");
      } else {
         $s->AddJoin("CategoryRoleBlock", "crb", "CategoryID and crb.RoleID = 1", "d", "CategoryID", "left join");
      }
      $s->AddWhere("coalesce(crb.Blocked, 0)", "0", "=", "and", "", 0, 0);
      $s->AddGroupBy("DiscussionID", "d");
         
      // Only show active Discussions
      $s->AddWhere("d.Active", "1", "=");
      
      // Apply category blocks for the current user
      if ($Context->Session->UserID > 0) {
         $s->AddJoin("CategoryBlock", "cb", "CategoryID and cb.UserID = ".$Context->Session->UserID, "d", "CategoryID", "left join");
         $s->AddWhere("coalesce(cb.Blocked,0)", 1, "<>");
      }
      
      // Make sure whispers don't come through
      if ($Context->Session->UserID > 0) {
         $s->AddWhere("d.AuthUserID = ".$Context->Session->UserID." or d.WhisperUserID = ".$Context->Session->UserID." or d.WhisperUserID", 0, "=", "and", "", 1, 1);
         $s->EndWhereGroup();
      } else {
         $s->AddWhere("d.WhisperUserID", 0, "=", "and", "", 1, 1);
         $s->AddWhere("d.WhisperUserID", 0, "=", "or", "" ,0);
         $s->AddWhere("d.WhisperUserID", "null", "is", "or", "" ,0);
         $s->EndWhereGroup();
      }
         
      $s->AddOrderBy("d.DateLastActive", "", "desc");
      $s->AddLimit(0, agDISCUSSIONS_PER_FEED);
      $FeedData = $Context->Database->Select($Context, $s, "Feed", "GetData", "An error occurred while retrieving the feed.");
      
      $FeedEntry = new FeedEntry();
      if ($FeedData) {
         $WritePage = 0;
         
         // Set the content-type so it delivers properly
         header("Content-type: text/xml\n");
         $FirstRow = 1;
         if ($Context->Database->RowCount($FeedData) == 0) {
            if ($Type == "atom") {
               // Begin writing the feed
               if ($Type == "atom") {
                  echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>
                  <feed xmlns=\"http://www.w3.org/2005/Atom\">
                    <title type=\"text\">".agAPPLICATION_TITLE."</title>
                    <updated>".FixDate()."</updated>
                    <id>http://".agDOMAIN."</id>
                    <link rel=\"alternate\" type=\"text/html\" hreflang=\"en\" href=\"".agDOMAIN."\"/>
                    <link rel=\"self\" type=\"application/atom+xml\" href=\"".AppendFolder(agDOMAIN, "feeds/?Type=atom")."\"/>
                    <generator uri=\"http://getvanilla.com/\" version=\"".agVANILLA_VERSION."\">
                      Lussumo Vanilla
                    </generator>");
               }
            }
         } else {
            // Loop through entries      
            while ($row = $Context->Database->GetRow($FeedData)) {
               $FeedEntry->Clear();
               $FeedEntry->GetPropertiesFromDataSet($row, $Context);
               if ($Type == "atom") {
                  if ($FirstRow) {
                     $FirstRow = 0;
                     // Begin writing the feed
                     if ($Type == "atom") {
                        echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>
                        <feed xmlns=\"http://www.w3.org/2005/Atom\">
                          <title type=\"text\">".agAPPLICATION_TITLE."</title>
                          <updated>".$FeedEntry->Updated."</updated>
                          <id>http://".agDOMAIN."</id>
                          <link rel=\"alternate\" type=\"text/html\" hreflang=\"en\" href=\"".agDOMAIN."\"/>
                          <link rel=\"self\" type=\"application/atom+xml\" href=\"".AppendFolder(agDOMAIN, "feeds/?Type=atom")."\"/>
                          <generator uri=\"http://getvanilla.com/\" version=\"".agVANILLA_VERSION."\">
                            Lussumo Vanilla
                          </generator>");
                     }
                  }
                  echo("<entry>
                     <title>".$FeedEntry->Title."</title>
                     <link rel=\"alternate\" href=\"".$FeedEntry->Link."\" type=\"application/xhtml+xml\" hreflang=\"en\"/>
                     <id>".$FeedEntry->Link."</id>
                     <published>".$FeedEntry->Published."</published>
                     <updated>".$FeedEntry->Updated."</updated>
                     <author>
                        <name>".$FeedEntry->AuthorName."</name>
                        <uri>".$FeedEntry->AuthorUrl."</uri>
                     </author>
                     <summary type=\"text\" xml:lang=\"en\">
                        ".$FeedEntry->Summary."
                     </summary>
                     <content type=\"html\">
                        <![CDATA[".$FeedEntry->Content."]]>
                     </content>");
                     // if ($FeedEntry->Category != "") echo("<category term=\"/".$FeedEntry->Category."\" scheme=\"".$FeedEntry->CategoryLink."\" label=\"".$FeedEntry->Category."\"/>");
                  echo("</entry>");
               }
            }
         }
         
         // End the feed
         if ($Type == "atom") {
            echo("</feed>");
         }
      }
   }
}

if ($WritePage) {
   // Write up some information about available feeds
   include(agAPPLICATION_PATH."appg/language.php");
   echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-ca\">
<head>
<title>".agAPPLICATION_TITLE." ".$Context->GetDefinition("Feeds")."</title>
<style type=\"text/css\">
   body { 
      background: #ffffff; 
      text-align: center;
      margin: 0px;
      margin-top: 20px;
      margin-bottom: 20px;
   }
   body, div, h1, h2, h3, p, ul, li { 
      font-family: Trebuchet MS, tahoma, arial, verdana; 
      color: #000; 
      line-height: 160%;
   }
   h1 {
      font-size: 22px;
   }
   h2 {
      color: #c00;
      font-size: 14px;
      margin-bottom: 10px;
   }
   h2 a, h2 a:link, h2 a:visited {
      color: #a00;
      background: #fff;
      text-decoration: underline;
   }
   h2 a:hover {
      color: #c00;
      background: #fff;
      text-decoration: none;
   }
   strong {
      font-weight: normal;
      font-style: italic;
   }
   h3, p {
      font-size: 12px;
      padding: 0px;
      margin: 0px;
   
   }
   p, h3 {
      font-size: 12px; 
      color: #333;
   }
   h3 {
      font-weight: bold;
      margin-top: 10px;
   }
   .PageContainer {
      text-align: left;
      margin: auto;
      width: 500px;
   }
   a, a:link, a:visited {
      color: #36f;
      background: #ffc;
      text-decoration: none;
   }
   a:hover {
      color: #36F;
      background: #ffa;
      text-decoration: none;
   }
   ul, li {
      color: #555;
      font-size: 12px;
   }
   li {
      margin-bottom: 4px;
   }
</style>
</head>
<body>
<div class=\"PageContainer\">");
if ($UserIsAuthenticated) {
	echo("<h1>".agAPPLICATION_TITLE." ".$Context->GetDefinition("Feeds")."</h1>
	<h2>".$Context->GetDefinition("FeedIntroduction")."</h2>

	<h3>".$Context->GetDefinition("AvailableFeeds")."</h3>
	<p>".$Context->GetDefinition("AvailableFeedNotes")."</p>

	<ul>
		<li><a href=\"?Type=atom\">Atom</a></li>
	</ul>");
} else {
	echo("<h1>".$Context->GetDefinition("FailedFeedAuthenticationTitle")."</h1>
	<h2>".$Context->GetDefinition("FailedFeedAuthenticationText")."</h2>");
}
echo("</div>
</body>
</html>");
}
?>
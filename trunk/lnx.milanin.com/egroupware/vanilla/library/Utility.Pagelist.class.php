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
* Description: Class that handles drawing a pagelist of data.
* Applications utilizing this file: Vanilla;
*/
class Pagelist {

	// PUBLIC PROPERTIES
	var $RecordsPerPage;		// Number of records per page.
	var $TotalRecords;			// Total number of records in dataset.
	var $PagesToDisplay;		// Maximum number of pages links to display per page.
	var $PageParameterName; 	// Name to be used for the page parameter in the querystring.
	var $CssClass;				// Name of the stylesheet class to be applied to the pagelist.
	var $NextImage;				// Source of the "next page" image.
	var $PreviousImage;			// Source of the "previous page" image.
	var $NextText;
	var $PreviousText;
   var $QueryStringParams;
	var $PageListID;
	var $Totalled;				// Is there going to be a total records value provided (required for numeric list)

	// PRIVATE PROPERTIES
	var $CurrentPage;			// The page currently being displayed.
	var $FirstRecord;			// First record of the current page.
	var $LastRecord;			// Last record of the current page.
	var $PageCount;				// Total number of pages.
	var $isPropertiesDefined; 	// Has the DefineParameters function been called yet?

	// Based on the current page number and the middle position, figure out which page number to start on
	function CalculateFirstPage($MiddlePosition, $CurrentPageNumber) {
		$iReturn = $CurrentPageNumber - $MiddlePosition;
		if ($iReturn < 1) $iReturn = 1;
		return $iReturn;
	}
	
	// Define all required parameters to create the PageList and PageListDetails
	function DefineProperties() {
		if (!$this->isPropertiesDefined) {
			if ($this->CurrentPage == 0) $this->CurrentPage = ForceIncomingInt($this->PageParameterName, 1);
			if ($this->Totalled) {
				$this->PageCount = CalculateNumberOfPages($this->TotalRecords, $this->RecordsPerPage);
				if ($this->CurrentPage > $this->PageCount) $this->CurrentPage = $this->PageCount;
				if ($this->CurrentPage < 1) $this->CurrentPage = 1;
				$this->FirstRecord = (($this->CurrentPage - 1) * $this->RecordsPerPage) + 1;
				$this->LastRecord = $this->FirstRecord + $this->RecordsPerPage - 1;
				if ($this->LastRecord > $this->TotalRecords) $this->LastRecord = $this->TotalRecords;
			} else {
				if ($this->CurrentPage < 1) $this->CurrentPage = 1;
				$this->PageCount = $this->CurrentPage;
				if ($this->TotalRecords > $this->RecordsPerPage) $this->PageCount++;
				$this->FirstRecord = (($this->CurrentPage - 1) * $this->RecordsPerPage) + 1;
				$this->LastRecord = $this->FirstRecord + $this->TotalRecords-1;
				if ($this->LastRecord < $this->FirstRecord) $this->LastRecord = $this->FirstRecord;
				if ($this->PageCount > $this->CurrentPage) $this->LastRecord = $this->LastRecord - 1;
			}
			$this->isPropertiesDefined = 1;
		}
	}
	
	// Builds a literal page list (ie. "previous next")
	function GetLiteralList() {
		$this->DefineProperties();
		$sReturn = "<div class=\"".$this->CssClass."\"";
		if ($this->PageListID != "") $sReturn .= " id=\"".$this->PageListID."\"";
		$sReturn .= ">";

		// Define the querystring
		$iTmpPage = 0;

		if ($this->PageCount > 1) {
			if ($this->CurrentPage > 1) {
				$iTmpPage = $this->CurrentPage - 1;
				$this->QueryStringParams->Set($this->PageParameterName, $iTmpPage);
				$sReturn .= "<a href=\"".$this->QueryStringParams->GetQueryString()."\">".Iif($this->PreviousImage != "", "<img src='".$this->PreviousImage."' border=\"0\" alt=\"".$this->PreviousText."\" />", $this->PreviousText)."</a> ";
			} else {
				$sReturn .= Iif($this->PreviousImage != "", "<img src=\"".$this->PreviousImage."\" border=\"0\" alt=\"".$this->PreviousText."\" />", $this->PreviousText)." ";
			}

			if ($this->CurrentPage != $this->PageCount) {
				$iTmpPage = $this->CurrentPage + 1;
				$this->QueryStringParams->Set($this->PageParameterName, $iTmpPage);
				$sReturn .= " <a href=\"".$this->QueryStringParams->GetQueryString()."\">".Iif($this->NextImage != "", "<img src=\"".$this->NextImage."\" border=\"0\" alt=\"".$this->NextText."\" />", $this->NextText)."</a> ";
			} else {
				$sReturn .= " ".Iif($this->NextImage != "", "<img src=\"".$this->NextImage."\" border=\"0\" alt=\"".$this->NextText."\" />", $this->NextText)." ";
			}
		} else {
			$sReturn .= "&nbsp;";
		}

		$sReturn .= "</div>";
		return $sReturn;
	}
	// Builds a numeric page list (ie. "prev 1 2 3 next").
	function GetNumericList() {
		$this->DefineProperties();

		// Variables that help define which page numbers to display:
		// Subtract the first and last page from the number of pages to display
		$iPagesToDisplay = $this->PagesToDisplay - 2;
		if ($iPagesToDisplay <= 8) $iPagesToDisplay = 8;
		// Middle navigation point for the pagelist
		$MidPoint = ($iPagesToDisplay / 2);
		// First page number to display (Based on the current page number and the middle position, figure out which page number to start on) 
		$FirstPage = $this->CalculateFirstPage($MidPoint, $this->CurrentPage);
		// Last page number to display
		$LastPage = $FirstPage + ($iPagesToDisplay - 1);
		if ($LastPage > $this->PageCount) {
			$LastPage = $this->PageCount;
			$FirstPage = $this->PageCount - $iPagesToDisplay;
			if ($FirstPage < 1) $FirstPage = 1;
		}

		$sReturn = "\r\n<ol class=\"".$this->CssClass.($this->PageCount > 1?"":" PagelistEmpty")."\"";
		if ($this->PageListID != "") $sReturn .= " id=\"".$this->PageListID."\"";
		$sReturn .= ">\r\n";
		$Loop = 0;
		$iTmpPage = 0;

		if ($this->PageCount > 1) {
			if ($this->CurrentPage > 1) {
				$iTmpPage = $this->CurrentPage - 1;
				$this->QueryStringParams->Set($this->PageParameterName, $iTmpPage);
				$sReturn .= "\t<li><a href=\"".$this->QueryStringParams->GetQueryString()."\">".Iif($this->PreviousImage != "", "<img src='".$this->PreviousImage."' border=\"0\" alt=\"".$this->PreviousText."\" />", "&lt;")."</a></li>\r\n";
			} else {
				$sReturn .= "\t<li>".Iif($this->PreviousImage != "", "<img src=\"".$this->PreviousImage."\" border=\"0\" alt=\"".$this->PreviousText."\" />", "&lt;")."</li>\r\n";
			}

			// Display first page & elipsis if we have moved past the second page
			if ($FirstPage > 2) {
				$this->QueryStringParams->Set($this->PageParameterName, "1");
				$sReturn .= "\t<li><a href=\"".$this->QueryStringParams->GetQueryString()."\">1</a></li>\r\n"
					."\t<li>...</li>\r\n";
			} elseif ($FirstPage == 2) {
				$this->QueryStringParams->Set($this->PageParameterName, "1");
				$sReturn .= "\t<li><a href=\"".$this->QueryStringParams->GetQueryString()."\">1</a></li>\r\n";
			}

			for ($Loop = 1; $Loop <= $this->PageCount; $Loop++) {
				if (($Loop >= $FirstPage) && ($Loop <= $LastPage)) {
					if ($Loop == $this->CurrentPage) {
						$sReturn .= "\t<li>".$Loop."</li>\r\n";
					} else {
						$this->QueryStringParams->Set($this->PageParameterName, $Loop);
						$sReturn .= "\t<li><a href=\"".$this->QueryStringParams->GetQueryString()."\">".$Loop."</a></li>\r\n";
					}
				}
			}

			// Display last page & elipsis if we are not yet at the second last page
			if ($this->CurrentPage < ($this->PageCount - $MidPoint) && $this->PageCount > $this->PagesToDisplay+1) {
				$this->QueryStringParams->Set($this->PageParameterName, $this->PageCount);
				$sReturn .= "\t<li>...</li>\r\n"
					."\t<li><a href=\"".$this->QueryStringParams->GetQueryString()."\">".$this->PageCount."</a></li>\r\n";
			} else if ($this->CurrentPage == ($this->PageCount - $MidPoint) && ($this->PageCount > $this->PagesToDisplay)) {
				$this->QueryStringParams->Set($this->PageParameterName, $this->PageCount);
				$sReturn .= "\t<li><a href=\"".$this->QueryStringParams->GetQueryString()."\">".$this->PageCount."</a></li>\r\n";
			}

			if ($this->CurrentPage != $this->PageCount) {
				$iTmpPage = $this->CurrentPage + 1;
				$this->QueryStringParams->Set($this->PageParameterName, $iTmpPage);
				$sReturn .= "\t<li><a href=\"".$this->QueryStringParams->GetQueryString()."\">".Iif($this->NextImage != "", "<img src=\"".$this->NextImage."\" border=\"0\" alt=\"".$this->NextText."\" />", "&gt;")."</a></li>\r\n";
			} else {
				$sReturn .= "\t<li>".Iif($this->NextImage != "", "<img src=\"".$this->NextImage."\" border=\"0\" alt=\"".$this->NextText."\" />", "&gt;")."</li>\r\n";
			}
		} else {
			$sReturn .= "<li>&nbsp;</li>\r\n";
		}

		$sReturn .= "</ol>\r\n";
		return $sReturn;
	}

	// Builds a string with information about the page list's current position (ie. "1 to 15 of 56").
	// Returns the built string.
	function GetPageDetails($Context, $IncludeTotal = "1") {
		$IncludeTotal = ForceBool($IncludeTotal, 0);
		$this->DefineProperties();
		$sReturn = "";
		if ($this->TotalRecords > 0) {
			$sReturn = $this->FirstRecord.$Context->GetDefinition("To").$this->LastRecord;
			if ($IncludeTotal) $sReturn .= $Context->GetDefinition("Of").$this->TotalRecords;
		} else {
			$sReturn = 0;
		}
		return $sReturn;
	}
	
	function Pagelist(&$Context) {
		$this->CurrentPage = 0;
		$this->isPropertiesDefined = 0;
		$this->QueryStringParams = $Context->ObjectFactory->NewObject($Context, "Parameters");
		$this->QueryStringParams->DefineCollection($_GET);
		$this->PageListID = "";
		$this->Totalled = 1;
		$this->NextText = "Next";
		$this->PreviousText = "Prev";
	}
}
?>
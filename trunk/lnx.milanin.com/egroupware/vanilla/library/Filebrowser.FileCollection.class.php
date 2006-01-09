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
* Description: Collects, sorts, and organizes files
* Applications utilizing this file: Filebrowser;
*/

class FileCollection {

	var $Name;		// Name of the current collection
	var $FileNames;		// Array of file names
	var $LowerFileNames;// Array of file names in lowercase (for sorting)
	var $FileSizes;		// Array of file sizes
	var $FileDates;		// Array of file dates
	var $HandlerMethods;// Array of handler methods
	
	function AddFile($Name, $Size, $Date, $HandlerMethod) {
		$this->FileNames[] = $Name;
		$this->LowerFileNames[] = strtolower($Name);
		$this->FileSizes[] = $Size;
		$this->FileDates[] = $Date;
		$this->HandlerMethods[] = $HandlerMethod;
	}
	
	function BuildAssociativeFileArray($OrderedArray, $ThumbnailArray) {
		reset($OrderedArray);
		$AssociativeArray = array();
		while (list($key, $val) = each($OrderedArray)) {
			$AssociativeArray[] = array("Name" => $this->FileNames[$key], "Size" => FormatFileSize($this->FileSizes[$key]), "Date" => $this->FileDates[$key], "HandlerMethod" => $this->HandlerMethods[$key], "ThumbnailPresent" => $this->FindThumbnail($this->FileNames[$key], $ThumbnailArray));
		}
		return $AssociativeArray;
	}
	
	function FileCollection($Name = "") {
		$this->Name = $Name;
		$this->FileNames = array();
		$this->LowerFileNames = array();
		$this->FileDates = array();
		$this->FileSizes = array();
		$this->HandlerMethods = array();
	}
	
	function FindThumbnail($FileName, $ThumbnailArray) {
		// Take the given filename and look for a thumbnail (thumbnails are prefixed with ".thumb.")
		return in_array("_thumb.".$FileName, $ThumbnailArray);
	}
	
	// Get all items in this file collection as an associative array in the specified order & direction
	function GetFiles($OrderBy = "Size", $Direction = "asc", $ThumbnailArray = "") {
		if (!is_array($ThumbnailArray)) $ThumbnailArray = array();
		if ($Direction != "asc") $Direction = "desc";
		if ($OrderBy != "Size" && $OrderBy != "Name" && $OrderBy != "Date") $OrderBy = "Size";
		
		$SortFunction = "asort";
		if ($Direction == "desc") $SortFunction = "arsort";
		
		$ReturnArray = false;
		
		if ($OrderBy == "Date") {
			$SortFunction($this->FileDates);
			$ReturnArray = $this->BuildAssociativeFileArray($this->FileDates, $ThumbnailArray);
		} elseif ($OrderBy == "Name") {
			$SortFunction($this->LowerFileNames);
			$ReturnArray = $this->BuildAssociativeFileArray($this->LowerFileNames, $ThumbnailArray);
		} else {
			$SortFunction($this->FileSizes);
			$ReturnArray = $this->BuildAssociativeFileArray($this->FileSizes, $ThumbnailArray);
		}
		return $ReturnArray;
	}
	
}
?>
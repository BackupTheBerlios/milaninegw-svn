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
* Description: Controls for categories.php
*/
// Displays a category list
include_once(sgLIBRARY."Vanilla.Category.class.php");
class CategoryList extends Control {
	var $Data;
	
	function CategoryList(&$Context) {		
		$this->Context = &$Context;
		$CategoryManager = $this->Context->ObjectFactory->NewContextObject($this->Context, "CategoryManager");
		$this->Data = $CategoryManager->GetCategories(1);
	}
	
	function Render() {
		$this->Context->Writer->Add("<div class=\"Title\">".$this->Context->PageTitle."</div>");
		$Category = $this->Context->ObjectFactory->NewObject($this->Context, "Category");
		$FirstRow = 1;
		while ($Row = $this->Context->Database->GetRow($this->Data)) {
			$Category->Clear();
			$Category->GetPropertiesFromDataSet($Row);
			$Category->FormatPropertiesForDisplay();
			$this->Context->Writer->Add("<dl class=\"Category".($Category->Blocked?" BlockedCategory":" UnblockedCategory").($FirstRow?" FirstCategory":"")." Category_".$Category->CategoryID."\">
				<dt class=\"DataItemLabel CategoryNameLabel\">".$this->Context->GetDefinition("Category")."</dt>
				<dd class=\"DataItem CategoryName\"><a href=\"./?CategoryID=".$Category->CategoryID."\">".$Category->Name."</a></dd>
				<dt class=\"ExtendedMetaItemLabel CategoryInformationLabel CategoryDescriptionLabel\">Description</dt>
				<dd class=\"ExtendedMetaItem CategoryInformation CategoryDescription\">".$Category->Description."</dd>
				<dt class=\"MetaItemLabel CategoryInformationLabel DiscussionCountLabel\">".$this->Context->GetDefinition("Discussions")."</dt>
				<dd class=\"MetaItem CategoryInformation DiscussionCount\">".$Category->DiscussionCount."</dd>");
			if ($this->Context->Session->UserID > 0) {
				$this->Context->Writer->Add("
					<dt class=\"MetaItemLabel CategoryInformationLabel CategoryMonitorLabel\">".$this->Context->GetDefinition("Options")."</dt>
					<dd class=\"MetaItem CategoryInformation CategoryMonitor\">");
					if ($Category->Blocked) {
						$this->Context->Writer->Add("<a href=\"Javascript:ToggleCategoryBlock(".$Category->CategoryID.", 0);\">".$this->Context->GetDefinition("UnblockCategory")."</a>");
					} else {
						$this->Context->Writer->Add("<a href=\"Javascript:ToggleCategoryBlock(".$Category->CategoryID.", 1);\">".$this->Context->GetDefinition("BlockCategory")."</a>");
					}
					$this->Context->Writer->Add("</dd>
				");
			}
			$this->Context->Writer->Add("</dl>\n");
			$FirstRow = 0;
		}
		$this->Context->Writer->Write();
	}	
}
?>
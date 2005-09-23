<?php
	/***************************************************************************\
	* EGroupWare - FeLaMiMail                                                   *
	* http://www.linux-at-work.de                                               *
	* http://www.phpgw.de                                                       *
	* http://www.egroupware.org                                                 *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* Copyright (c) 2004, Lars Kneschke					    *
	* All rights reserved.							    *
	*									    *
	* Redistribution and use in source and binary forms, with or without	    *
	* modification, are permitted provided that the following conditions are    *
	* met:									    *
	*									    *
	*	* Redistributions of source code must retain the above copyright    *
	*	notice, this list of conditions and the following disclaimer.	    *
	*	* Redistributions in binary form must reproduce the above copyright *
	*	notice, this list of conditions and the following disclaimer in the *
	*	documentation and/or other materials provided with the distribution.*
	*	* Neither the name of the FeLaMiMail organization nor the names of  *
	*	its contributors may be used to endorse or promote products derived *
	*	from this software without specific prior written permission.	    *
	*									    *
	* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 	    *
	* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED *
	* TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR*
	* PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 	    *
	* CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,	    *
	* EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 	    *
	* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR 	    *
	* PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF    *
	* LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 	    *
	* NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS 	    *
	* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.		    *
	\***************************************************************************/

	/* $Id: class.uiwidgets.inc.php,v 1.2 2004/06/01 10:38:36 lkneschke Exp $ */

        /**
        * a class containing javascript enhanced html widgets
        *
        * @package FeLaMiMail
        * @author Lars Kneschke
        * @version 1.35
        * @copyright Lars Kneschke 2004
        * @license http://www.opensource.org/licenses/bsd-license.php BSD
        */
	class uiwidgets
	{
		/**
		* the contructor
		*
		*/
		function uiwidgets()
		{
			$template = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$this->template = $template;
			$this->template->set_file(array("body" => 'uiwidgets.tpl'));
		}

		/**
		* @return unknown
		* @param array $_rows 
		* @param string $_valueName
		* @param string $_description The description of the text row
		* @param bool $_readOnly display a readonly(true) or readWrite(false) widget
		* @param timestamp $_startDate
		* @param timestamp $_endDate
		* @param string $_boxWidth
		* @desc creates a table, which contains rows with a textfield per month/year
		*/
		function dateSelectBox($_rows, $_valueName, $_description, $_readOnly=false, $_startDate=false, $_endDate=false, $_boxWidth="100%")
		{
			$currentYear = date('Y');

			$this->template->set_block('body','dateSelectBoxRO');
			$this->template->set_block('body','dateSelectBoxRW');
			$this->template->set_block('body','dateSelectBoxTableRowRO');
			$this->template->set_block('body','dateSelectBoxTableRowRW');
			if($_readOnly)
			{
				$dateSelectBox = 'dateSelectBoxRO';
				$dateSelectBoxTableRow = 'dateSelectBoxTableRowRO';
			}
			else
			{
				$dateSelectBox = 'dateSelectBoxRW';
				$dateSelectBoxTableRow = 'dateSelectBoxTableRowRW';
			}
			
			$dateSelectBoxYear = '<select id="'.$_valueName.'_year" nname="v'.$_valueName.'_year">';
			$dateSelectBoxYear .= "<option value=\"0\" >---</option>";
			for($i=$currentYear-2; $i<$currentYear+6; $i++)
			{
				$dateSelectBoxYear .= "<option value=\"$i\" >".htmlspecialchars($i,ENT_QUOTES)."</option>";
			}
			$dateSelectBoxYear .= '</select>';

			$dateSelectBoxMonth = '<select id="'.$_valueName.'_month" nname="v'.$_valueName.'_month">';
			$dateSelectBoxMonth .= "<option value=\"0\" >---</option>";
			for($i=1; $i<=12; $i++)
			{
				$dateSelectBoxMonth .= "<option value=\"$i\" >".htmlspecialchars($i,ENT_QUOTES)."</option>";
			}
			$dateSelectBoxMonth .= '</select>';

			if(is_array($_rows))
			{
				$rowCounter=1;
				foreach($_rows as $year => $months)
				{
					foreach($months as $month => $textField)
					{
						if($year == 0) $year='---';
						if($month == 0) $month='---';
						$this->template->set_var('dateSelectBoxTableRow_year',$year);
						$this->template->set_var('dateSelectBoxTableRow_month',$month);
						$this->template->set_var('dateSelectBoxTableRow_counter',$rowCounter);
						$this->template->set_var('dateSelectBoxTableRow_nameYear',$_valueName."[$rowCounter][year]");
#						$this->template->set_var('dateSelectBoxTableRow_nameYear',$_valueName."[".$rowCounter."_y]");
						$this->template->set_var('dateSelectBoxTableRow_valueYear',$year);
						$this->template->set_var('dateSelectBoxTableRow_nameMonth',$_valueName."[$rowCounter][month]");
#						$this->template->set_var('dateSelectBoxTableRow_nameMonth',$_valueName."[".$rowCounter."_m]");
						$this->template->set_var('dateSelectBoxTableRow_valueMonth',$month);
						$this->template->set_var('dateSelectBoxTableRow_nameText',$_valueName."[$rowCounter][text]");
#						$this->template->set_var('dateSelectBoxTableRow_nameText',$_valueName."[".$rowCounter."_t]");
						$this->template->set_var('dateSelectBoxTableRow_valueText',$textField);
						$this->template->set_var('lang_delete',lang('delete'));
						
						$this->template->parse('dateSelectBox_tableRows',$dateSelectBoxTableRow,True);
						
						$rowCounter++;
					}
				}
				//$this->template->set_var('multiSelectBox_predefinded_options',$options);
			}

			$this->template->set_var('lang_add',lang('add'));
			$this->template->set_var('lang_year',lang('year'));
			$this->template->set_var('lang_month',lang('month'));
			$this->template->set_var('dateSelectBox_month',$dateSelectBoxMonth);
			$this->template->set_var('dateSelectBox_year',$dateSelectBoxYear);
			$this->template->set_var('dateSelectBox_valueName', $_valueName);
			$this->template->set_var('dateSelectBox_boxWidth', $_boxWidth);
			$this->template->set_var('dateSelectBox_description', $_description);
			
			return $this->template->fp('out',$dateSelectBox);
		}

		/**
		* create multiselectbox
		*
		* this function will create a multiselect box. Hard to describe! :)
		*
		* @param _selectedValues Array of values for already selected values(the left selectbox)
		* @param _predefinedValues Array of values for predefined values(the right selectbox)
		* @param _valueName name for the variable containing the selected values
		* @param _boxWidth the width of the multiselectbox( example: 100px, 100%)
		*
		* @returns the html code, to be added into the template
		*/
		function multiSelectBox($_selectedValues, $_predefinedValues, $_valueName, $_boxWidth="100%")
		{
			$this->template->set_block('body','multiSelectBox');
			
			if(is_array($_selectedValues))
			{
				foreach($_selectedValues as $key => $value)
				{
					$options .= "<option value=\"$key\" selected=\"selected\">".htmlspecialchars($value,ENT_QUOTES)."</option>";
				}
				$this->template->set_var('multiSelectBox_selected_options',$options);
			}

			$options = '';
			if(is_array($_predefinedValues))
			{
				foreach($_predefinedValues as $key => $value)
				{
					if($key != $_selectedValues["$key"])
					$options .= "<option value=\"$key\">".htmlspecialchars($value,ENT_QUOTES)."</option>";
				}
				$this->template->set_var('multiSelectBox_predefinded_options',$options);
			}

			$this->template->set_var('multiSelectBox_valueName', $_valueName);
			$this->template->set_var('multiSelectBox_boxWidth', $_boxWidth);
			
			
			return $this->template->fp('out','multiSelectBox');
		}

		function tableView($_headValues, $_tableWidth="100%")
		{
			$this->template->set_block('body','tableView');
			$this->template->set_block('body','tableViewHead');
			
			if(is_array($_headValues))
			{
				foreach($_headValues as $head)
				{
					if($head == '')
						$head = '&nbsp;';
					$this->template->set_var('tableHeadContent',$head);
					$this->template->parse('tableView_Head','tableViewHead',True);
				}
			}
			
			$rowCSS = array('row_on','row_off');
			
			if(is_array($this->tableViewRows))
			{
				$i=0;
				foreach($this->tableViewRows as $tableRow)
				{
					$rowData .= '<tr class="'.$rowCSS[$i%2].'">';
					foreach($tableRow as $tableData)
					{
						switch($tableData['type'])
						{
							default:
								$rowData .= '<td align="'.$tableData['align'].
										'">'.$tableData['text'].'</td>';
								break;
						}
					}
					$rowData .= "</tr>";
					$i++;
				}
			}
			
			$this->template->set_var('tableView_width', $_tableWidth);
			$this->template->set_var('tableView_Rows', $rowData);
			
			return $this->template->fp('out','tableView');
		}
		
		function tableViewAddRow()
		{
			$this->tableViewRows[] = array();
			end($this->tableViewRows);
			return key($this->tableViewRows);
		}
		
		function tableViewAddTextCell($_rowID, $_text, $_align='left')
		{
			$this->tableViewRows[$_rowID][]= array
			(
				'type'	=> 'text',
				'text'	=> $_text,
				'align'	=> $_align
			);
		}
	}
?>
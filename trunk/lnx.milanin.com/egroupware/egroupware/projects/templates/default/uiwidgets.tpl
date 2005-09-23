<!-- BEGIN dateSelectBoxRW -->
<script LANGUAGE="JavaScript" TYPE="text/javascript">

	var rowCounter=0;

	function addTableRow(_valueName)
	{
		var table	= document.getElementById('dateSelectBox_table');
		var lastRow	= table.rows.length-1;
		if(rowCounter == 0)
		{
			rowCounter = lastRow;
		}

		var newRow	= table.insertRow(lastRow);
		var valueYear	= document.getElementById(_valueName + '_year').value;
		var valueMonth	= document.getElementById('{dateSelectBox_valueName}_month').value;
		var valueText	= document.getElementById('{dateSelectBox_valueName}_text').value;

		var cell1	= newRow.insertCell(0);
		cell1.setAttribute('align','center');
		yearText = valueYear;
		if(valueYear == 0)
		{
			yearText = '---';
		}
		var textNode	= document.createTextNode(yearText);
		cell1.appendChild(textNode);
		
		var el		= document.createElement('input');
		el.setAttribute('type', 'hidden');
		el.setAttribute('name', _valueName + '[' + rowCounter + '][year]');
		el.setAttribute('value', valueYear);
		cell1.appendChild(el);

		var cell2	= newRow.insertCell(1);
		cell2.setAttribute('align','center');
		monthText = valueMonth;
		if(valueMonth == 0)
		{
			monthText = '---';
		}
		var textNode	= document.createTextNode(monthText);
		cell2.appendChild(textNode);
		
		var el		= document.createElement('input');
		el.setAttribute('type', 'hidden');
		el.setAttribute('name', _valueName + '[' + rowCounter + '][month]');
		el.setAttribute('value', valueMonth);
		cell2.appendChild(el);
		
		var cell3	= newRow.insertCell(2);
		var el		= document.createElement('input');
		el.setAttribute('type', 'text');
		el.setAttribute('name', _valueName + '[' + rowCounter + '][text]');
		el.setAttribute('value', valueText);
		el.setAttribute('size', 15);
		cell3.appendChild(el);

		var cell4	= newRow.insertCell(3);
		var el		= document.createElement('a');
		el.setAttribute('href', 'javascript:deleteTableRow(' + lastRow + ');');
		
		var linkText=document.createTextNode('{lang_delete}');
		el.appendChild(linkText);
		
		cell4.appendChild(el);
		
		rowCounter++;
		
		// clear the input field
		document.getElementById('{dateSelectBox_valueName}_text').value = '';
	}
	
	function deleteTableRow(_rowID)
	{
		var table	= document.getElementById('dateSelectBox_table');

		table.deleteRow(_rowID)

		for(var i=1; i < table.rows.length-1; i++)
		{
			var el		= document.createElement('a');
			el.setAttribute('href', 'javascript:deleteTableRow(' + i + ');');
			
			var linkText=document.createTextNode('{lang_delete}');
			el.appendChild(linkText);
			table.rows[i].cells[3].replaceChild(el,table.rows[i].cells[3].firstChild);
		}
	}

</script>
	<table id="dateSelectBox_table" border="0" cellspacing="0" cellpading="0" width="{dateSelectBox_boxWidth}">
		<tr class="th">
			<td align="center">
				{lang_year}
			</td>
			<td align="center">
				{lang_month}
			</td>
			<td align="center">
				{dateSelectBox_description}
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		{dateSelectBox_tableRows}
		<tr>
			<td align="center">
				{dateSelectBox_year}
			</td>
			<td align="center">
				{dateSelectBox_month}
			</td>
			<td>
				<input id="{dateSelectBox_valueName}_text" size="15" type="text">
			</td>
			<td>
				<a href="javascript:addTableRow('{dateSelectBox_valueName}');">{lang_add}</a>
			</td>
		</tr>
	</table>
<!-- END dateSelectBoxRW -->

<!-- BEGIN dateSelectBoxTableRowRW -->
	<tr>
		<td align="center">
			{dateSelectBoxTableRow_year}<INPUT type="hidden" value="{dateSelectBoxTableRow_valueYear}" name="{dateSelectBoxTableRow_nameYear}">
		</td>
		<td align="center">
			{dateSelectBoxTableRow_month}<INPUT type="hidden" value="{dateSelectBoxTableRow_valueMonth}" name="{dateSelectBoxTableRow_nameMonth}">
		</td>
		<td>
			<INPUT type="text" size="15" value="{dateSelectBoxTableRow_valueText}" name="{dateSelectBoxTableRow_nameText}">
		</td>
		<td>
			<a href="javascript:deleteTableRow('{dateSelectBoxTableRow_counter}');">{lang_delete}</a>
		</td>
	</tr>
<!-- END dateSelectBoxTableRowRW -->

<!-- BEGIN dateSelectBoxRO -->
	<table id="dateSelectBox_table" border="0" cellspacing="0" cellpading="0" width="{dateSelectBox_boxWidth}">
		<tr class="th">
			<td align="center">
				{lang_year}
			</td>
			<td align="center">
				{lang_month}
			</td>
			<td align="center">
				{dateSelectBox_description}
			</td>
		</tr>
		{dateSelectBox_tableRows}
	</table>
<!-- END dateSelectBoxRO -->

<!-- BEGIN dateSelectBoxTableRowRO -->
	<tr>
		<td align="center">
			{dateSelectBoxTableRow_year}
		</td>
		<td align="center">
			{dateSelectBoxTableRow_month}
		</td>
		<td align="right">
			{dateSelectBoxTableRow_valueText}
		</td>
	</tr>
<!-- END dateSelectBoxTableRowRO -->

<!-- BEGIN multiSelectBox -->
<script LANGUAGE="JavaScript" TYPE="text/javascript">

	function addCustomValue()
	{
		idCustomValue = document.getElementById("htmlclass_custom_value");
		idSelectedValues = document.getElementById("htmlclass_selected_values");
				
		NewOption = new Option(idCustomValue.value,
					idCustomValue.value,false,true);
		idSelectedValues.options[idSelectedValues.length] = NewOption;
		
		idCustomValue.value = '';
		
		for(i=0;i<idSelectedValues.length;++i)
		{
			idSelectedValues.options[i].selected = true;
		}
		
		sortSelect(idSelectedValues);
	}

	function addPredefinedValue()
	{
		idSelectedValues = document.getElementById("htmlclass_selected_values");
		idPredefinedValues = document.getElementById("htmlclass_predefined_values");
		
		if(idPredefinedValues.selectedIndex != -1)
		{
			NewOption = new Option(idPredefinedValues.options[idPredefinedValues.selectedIndex].text,
						idPredefinedValues.options[idPredefinedValues.selectedIndex].value,false,true);
			idSelectedValues.options[idSelectedValues.length] = NewOption;
			
			idPredefinedValues.options[idPredefinedValues.selectedIndex] = null;
			
			if(idPredefinedValues.length > 0)
			{
				idPredefinedValues.selectedIndex = 0;
			}
			
			sortSelect(idSelectedValues);
		}
	}
	
	function removeValue()
	{
		idSelectedValues = document.getElementById("htmlclass_selected_values");
		idPredefinedValues = document.getElementById("htmlclass_predefined_values");
		
		if(idSelectedValues.selectedIndex != -1)
		{
			NewOption = new Option(idSelectedValues.options[idSelectedValues.selectedIndex].text,
						idSelectedValues.options[idSelectedValues.selectedIndex].value,false,true);
			idPredefinedValues.options[idPredefinedValues.length] = NewOption;
			
			idSelectedValues.options[idSelectedValues.selectedIndex] = null;
			
			for(i=0;i<idSelectedValues.length;++i)
			{
				idSelectedValues.options[i].selected = true;
			}
			
			sortSelect(idPredefinedValues);
		}
	}
	
	function selectAllOptions()
	{
		idSelectedValues = document.getElementById("htmlclass_selected_values");
		
		for(i=0;i<idSelectedValues.length;++i)
		{
			idSelectedValues.options[i].selected = true;
		}
	}
	
	// Author: Matt Kruse <matt@mattkruse.com>
	// WWW: http://www.mattkruse.com/
	//
	// NOTICE: You may use this code for any purpose, commercial or
	// private, without any further permission from the author. You may
	// remove this notice from your final code if you wish, however it is
	// appreciated by the author if at least my web site address is kept.
	function sortSelect(obj) 
	{
		var o = new Array();
		if (obj.options==null) { return; }
		for (var i=0; i<obj.options.length; i++) 
		{
			o[o.length] = new Option( obj.options[i].text, obj.options[i].value, obj.options[i].defaultSelected, obj.options[i].selected) ;
		}
		if (o.length==0) { return; }
		o = o.sort(
			function(a,b) 
			{
				if ((a.text+"") < (b.text+"")) { return -1; }
				if ((a.text+"") > (b.text+"")) { return 1; }
				return 0;
			}
		);
		
		for (var i=0; i<o.length; i++) 
		{
			obj.options[i] = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
		}
	}

</SCRIPT>
<table border="0" cellspacing="0" cellpadding="0" width="{multiSelectBox_boxWidth}">
	<tr>
		<td width="50%" align="right" rowspan="2" valign="top">
			<select name="{multiSelectBox_valueName}[]" id="htmlclass_selected_values" size=8 
				style="width : 250px;" multiple="multiple" ondblclick="removeValue()"
				onblur="selectAllOptions()">
				{multiSelectBox_selected_options}
			</select>
		</td>
		<td align="left" rowspan="2" style="padding-right : 10px; padding-left : 2px;">
			<a href="javascript:removeValue()">>></a>
		</td>
		<td align="right" style="padding-left : 10px; padding-right : 2px;">
			<a href="javascript:addPredefinedValue()"><<</a>
		</td>
		<td width="50%" valign="top">
			<select name="{multiSelectBox_valueName}_predefined_values[]" id="htmlclass_predefined_values" size=6 style="width : 250px;" ondblclick="addPredefinedValue()">
				{multiSelectBox_predefinded_options}
			</select>
		</td>
	</tr>

	<tr>
		<td align="right" style="padding-left : 10px; padding-right : 2px;">
			<a href="javascript:addCustomValue()"><<</a>
		</td>
		<td>
			<input type="text" name="custom_value" id="htmlclass_custom_value" style="width : 250px;">
		</td>
	</tr>
</table>
<!-- END multiSelectBox -->

<!-- BEGIN tableView -->
<table width="{tableView_width}">

<tr class="th">
	{tableView_Head}
</tr>

{tableView_Rows}

</table>
<!-- END tableView -->

<!-- BEGIN tableViewHead -->
<td align="center">{tableHeadContent}</td>
<!-- END tableViewHead -->

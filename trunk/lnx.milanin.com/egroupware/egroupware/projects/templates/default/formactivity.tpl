<!-- $Id: formactivity.tpl,v 1.22 2004/06/01 10:38:39 lkneschke Exp $ -->
<script language="JavaScript">
	var oldNumberInputValue;

	function changeProjectIDInput($_selectBox)
	{
		$numberInput = eval(document.getElementById("id_number"));
		if($_selectBox.checked == true)
		{
			$numberInput.disabled = true;
			$oldNumberInputValue = $numberInput.value;
			$numberInput.value = '';
		}
		else
		{
			$numberInput.disabled = false;
			$numberInput.value = $oldNumberInputValue;
		}
	}
</script>

<center>
<form method="POST" name="activity_form" action="{actionurl}">
{pref_message}<br>{message}
<table width="75%" border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td>{lang_choose}</td>
		<td>{choose}</td>
	</tr>
	<tr>
		<td>{lang_act_number}:</td>
		<td><input type="text" name="values[number]" value="{num}" size="20" maxlength="20" id="id_number"></td>
	</tr>
	<tr>
		<td valign="top">{lang_descr}:</td>
		<td colspan="2"><textarea name="values[descr]" rows=4 cols=50 wrap="VIRTUAL">{descr}</textarea></td>
	</tr>
	<tr>
		<td>{lang_category}:</td>
		<td><select name="values[cat]"><option value="">{lang_none}</option>{cats_list}</select></td>
	</tr>
	<tr>
		<td>{lang_remarkreq}:</td>
		<td><select name="values[remarkreq]">{remarkreq_list}</select></td>
	</tr>
	<tr>
		<td>{lang_billperae}:&nbsp;{currency}</td>
		<td><input type="text" name="values[billperae]" value="{billperae}"></td>
	</tr>
	<tr>
		<td>{lang_minperae}</td>
		<td>{minperae}</td>
	</tr>
	<tr valign="bottom" height="50">
		<td>
			<input type="submit" name="save" value="{lang_save}">
			<input type="hidden" name="values[edit_mode]" value="{edit_mode}">
		</td>
		<td align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
</table>
</form>
</center>

<!-- END edit -->

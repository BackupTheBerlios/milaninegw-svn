<!-- $Id: form.tpl,v 1.50.2.1 2004/11/06 12:15:58 ralfbecker Exp $ -->
<script language="JavaScript">
	self.name="first_Window";
	function abook()
	{
		Window1=window.open('{addressbook_link}',"Search","width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes");
	}
</script>
<script language="JavaScript">
	self.name="second_Window";
	function accounts_popup()
	{
		Window2=window.open('{accounts_link}',"Search","width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes");
	}
</script>

<script language="JavaScript">
	self.name="third_Window";
	function e_accounts_popup()
	{
		Window3=window.open('{e_accounts_link}',"Search","width=800,height=600,toolbar=no,scrollbars=yes,resizable=yes");
	}
	
	function editMilestone(_url)
	{
		window.open(_url,"EditMilestone","width=600,height=220,toolbar=no,scrollbars=yes,resizable=yes,left=200,top=300");
	}
</script>

{app_header}

<center>
<p>{message}</p>
<!-- BEGIN navbar -->
<table width="100%" border="0" cellspacing="0" cellpading="0" bgcolor="white">
	<tr>
		<th width="33%" id="tab1" class="activetab" onclick="javascript:tab.display(1);"><a href="#" tabindex="0" accesskey="1" onfocus="tab.display(1);" onclick="tab.display(1); return(false);" style="font-size:10px;">{lang_project_overview}</a></th>
		<th width="33%" id="tab2" class="activetab" onclick="javascript:tab.display(2);"><a href="#" tabindex="0" accesskey="2" onfocus="tab.display(2);" onclick="tab.display(2); return(false);" style="font-size:10px;">{lang_milestones}</a></th>
		<th width="33%" id="tab3" class="activetab" onclick="javascript:tab.display(3);"><a href="#" tabindex="0" accesskey="3" onfocus="tab.display(3);" onclick="tab.display(3); return(false);" style="font-size:10px;">{lang_files}</a></th>
	</tr>
</table>
<!-- END navbar -->
{message_main}
{message_milestone}
<br>
<div id="tabcontent1" class="inactivetab" bgcolor="white">
<form method="POST" name="app_form" action="{action_url}">
<table width="100%" border="0" cellspacing="2" cellpadding="2">

<!-- BEGIN main -->

	<tr bgcolor="{th_bg}">
		<td width="100%" colspan="7"><b>{lang_main}</b>:&nbsp;<a href="{main_url}">{pro_main}</a></td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td><b>{lang_pbudget}</b>:&nbsp;{currency}</td>
		<td>{lang_main}:</td>
		<td>{budget_main}</td>
		<td>{lang_sum_jobs}:</td>
		<td>{pbudget_jobs}</td>
		<td>{lang_available}:</td>
		<td>{apbudget}</td>
	</tr>
	<tr bgcolor="{th_bg}">
		<td><b>{lang_ptime}</b>:&nbsp;{lang_hours}</td>
		<td>{lang_main}:</td>
		<td>{ptime_main}</td>
		<td>{lang_sum_jobs}:</td>
		<td>{ptime_jobs}</td>
		<td>{lang_available}:</td>
		<td>{atime}</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr bgcolor="{row_on}">
		<td>{lang_parent}:</td>
		<td colspan="3">{parent_select}</td>
	</tr>

<!-- END main -->

	<tr bgcolor="{row_off}">
		<td width="20%">{lang_investment_nr}:</td>
		<td width="30%"><input type="text" name="values[investment_nr]" value="{investment_nr}" size="30"></td>
		<td width="20%">{lang_previous}:</td>
		<td width="30%"><select name="values[previous]"><option value="">{lang_none}</option>{previous_select}</select></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_number}:</td>
		<td><input type="text" name="values[number]" value="{number}" size="30" id="id_number"></td>
		<td>{lang_choose}</td>
		<td>{choose}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_title}:</td>
		<td><input type="text" name="values[title]" size="30" value="{title}"></td>
		<td>{lang_category}:</td>
		<td>{cat}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_descr}:</td>
		<td colspan="3"><textarea name="values[descr]" rows="4" cols="50" wrap="VIRTUAL">{descr}</textarea></td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_start_date_planned}:</td>
		<td>{pstart_date_select}</td>
		<td>{lang_date_due_planned}:</td>
		<td>{pend_date_select}</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_start_date}:</td>
		<td>{start_date_select}</td>
		<td>{lang_date_due}:</td>
		<td>{end_date_select}</td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_status}:</td>
		<td><select name="values[status]">{status_list}</select></td>
		<td valign="top">{lang_access}:</td>
		<td>{access}</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_priority}:</td>
		<td><select name="values[priority]">{priority_list}</select></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_url}:</td>
		<td>http://<input type="text" name="values[url]" size="30" value="{url}"></td>
		<td>{lang_reference}:</td>
		<td>http://<input type="text" name="values[reference]" size="30" value="{reference}"></td>
	</tr>

	<tr height="15">
		<td>&nbsp;</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_customer}:</td>
		<td>
			<input type="hidden" name="abid" value="{abid}">
			<input type="text" name="name" size="30" value="{name}" onClick="abook();" readonly>
			<input type="button" value="{lang_address_book}" onClick="abook();"></td>
		</td>
		<td>{lang_customer_nr}:</td>
		<td><input type="text" name="values[customer_nr]" size="30" value="{customer_nr}"></td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td colspan="3">
		{coordinator_accounts}
		</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td valign="top">{lang_employees}:</td>
		<td colspan="2">
		{employees_accounts}
		</td>
		<td align="right" valign="top">{edit_roles_button}</td>
	</tr>

	<tr height="15">
		<td>&nbsp;</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_ptime}:&nbsp;{lang_hours}</td>
		<td colspan="3"><input type="text" name="values[ptime]" value="{ptime}">&nbsp;[hh]</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_budget}:&nbsp;{currency}</td>
		<td><!--<input type="text" name="values[budget]" value="{budget}">&nbsp;[{currency}.c]<br>-->{budget_select}</td>
		<td>{lang_extra_budget}:&nbsp;{currency}</td>
		<td><input type="text" name="values[e_budget]" value="{e_budget}">&nbsp;[{currency}.c]</td>
	</tr>

<!-- BEGIN accounting_act -->

	<tr bgcolor="{row_on}">
		<td>{lang_bookable_activities}:</td>
		<td colspan="3"><select name="book_activities[]" multiple>{book_activities_list}</select></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_billable_activities}:</td>
		<td colspan="3"><select name="bill_activities[]" multiple>{bill_activities_list}</select></td>
	</tr>

<!-- END accounting_act -->

<!-- BEGIN accounting_own -->

	<tr bgcolor="{row_on}">
		<td valign="top">{lang_accounting}:</td>
		<td valign="top">
			<select id="acc_factor" name="values[accounting]" onchange="updateAccountingForm(this)">
				<option value="project" {acc_project_selected}>{lang_factor_project}</option>
				<option value="employee" {acc_employee_selected}>{lang_factor_employee}</option>
				<option value="non" {acc_non_billable_selected}>{lang_non_billable}</option>
			</select>
		</td>
		<td valign="top">{lang_accounting_factor_for_project}:&nbsp;{currency}</td>
		<td>
			<table border="0" cellspacing="0" cellpadding="1">
				<tr>
					<td>{lang_per_hour}</td>
					<td><input type="text" id="input_acc_factor_hour" name="values[project_accounting_factor]" size="10" value="{project_accounting_factor}"></td>
				</tr>
				<tr>
					<td>{lang_per_day}</td>
					<td><input type="text" id="input_acc_factor_day" name="values[project_accounting_factor_d]" size="10" value="{project_accounting_factor_d}"></td>
				</tr>
			</table>
		</td>
	</tr>

<!-- END accounting_own -->

	<tr bgcolor="{row_on}">
		<td valign="top">{lang_invoicing_method}:</td>
		<td><textarea name="values[inv_method]" rows="4" cols="30" wrap="VIRTUAL">{inv_method}</textarea></td>
		<td valign="top" align="center">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>{lang_discount}:</td>
					<td><input type="radio" name="values[discount_type]" value="percent" {dt_percent}>{lang_percent}&nbsp;[%.%]</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="radio" name="values[discount_type]" value="amount" {dt_amount}>{lang_amount}&nbsp;[{currency}.c]</td>
				</tr>
			</table>
		</td>
		<td valign="top"><input type="text" name="values[discount]" value="{discount}"></td>
	</tr>
	<tr height="15">
		<td>&nbsp;</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_result}:</td>
		<td colspan="3"><textarea name="values[result]" rows="4" cols="50" wrap="VIRTUAL">{result}</textarea></td>
	</tr>
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_test}:</td>
		<td colspan="3"><textarea name="values[test]" rows="4" cols="50" wrap="VIRTUAL">{test}</textarea></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_quality}:</td>
		<td colspan="3"><textarea name="values[quality]" rows="4" cols="50" wrap="VIRTUAL">{quality}</textarea></td>
	</tr>

	<tr height="15">
		<td>&nbsp;</td>
	</tr>

<!-- begin msfield1

	<tr bgcolor="{row_off}">
		<td valign="top">{lang_milestones}:</td>
		<td colspan="2">
			<table width="100%" border="0" cellspacing="2" cellpadding="2">

-- end msfield1 --
-- begin mslist --

				<tr>
					<td width="50%"><a href="{ms_edit_url}">{s_title}</a></td>
					<td width="50%">{s_edateout}</td>
				</tr>

-- end mslist --

-- begin msfield2 --
				</table>
		</td>
		<td valign="top" align="right"><input type="submit" name="mstone" value="{lang_add_mstone}"></td>
	</tr>
end msfield2
	<tr>
		<td align="right" colspan="4">{edit_mstones_button}</td>
	</tr> -->
	<tr valign="bottom" height="50" width="100%">
		<td width="25%"><input type="hidden" name="values[old_status]" value="{old_status}">
			<input type="hidden" name="values[old_parent]" value="{old_parent}">
			<input type="hidden" name="values[old_edate]" value="{old_edate}">
			<input type="hidden" name="values[old_coordinator]" value="{old_coordinator}">
			<input type="submit" name="save" value="{lang_save}"></td>
		<td width="25%"><input type="submit" name="apply" value="{lang_apply}"></td>
		<td width="25%" align="right">{delete_button}</td>
		<td width="25%" align="right"><input type="submit" name="cancel" value="{lang_cancel}"></td>
	</tr>
</table>
</form>
</div>
<div id="tabcontent2" class="inactivetab" bgcolor="white">
<!-- BEGIN project_data -->

<table border="0" width="100%" cellpadding="2" cellspacing="0">
	<tr bgcolor="{th_bg}">
		<td colspan="4"><b>{lang_project}:&nbsp;<a href="{pro_url}">{title_pro}</a></b></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_number}:</td>
		<td>{number_pro}</td>
		<td>{lang_url}:</td>
		<td><a href="http://{url_pro}" taget="_blank">{url_pro}</a></td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td>{coordinator_pro}</td>
		<td>{lang_customer}:</td>
		<td>{customer_pro}</td>
	</tr>
	<tr height="5">
		<td></td>
	</tr>
</table>
<!-- END project_data -->

<table border="0" cellspacing="0" cellpadding="2" width="100%">
	<TR>
		<TD align="right" colspan="3">
			&nbsp;
		</TD>
	</TR>
	<TR>
		<TD align="right" colspan="3">
			<A href="javascript:editMilestone('{add_url}');">{lang_add_milestone}</A>
		</TD>
	</TR>

	<tr bgcolor="{th_bg}">
		<td width="79%">{lang_title}</td>
		<td width="19%" align="center">{lang_date_due}</td>
		<td>&nbsp;</td>
	</tr>

<!-- BEGIN mstone_list -->

	<tr bgcolor="{tr_color}">
		<td><a href="javascript:editMilestone('{edit_url}');">{title}</a></td>
		<td align="center">{datedue}</td>
		<td align="center"><a href="{delete_url}">{delete_img}</a></td>
	</tr>

<!-- END mstone_list -->
</table>
<!-- <form method="POST" action="{action_url}">
<table border="0" cellspacing="0" cellpadding="2">
	<tr height="50" valign="bottom">
		<td><input type="text" name="values[title]" size="50" value="{title}"></td>
		<td>{end_date_select}</td>
		<td>
			<input type="hidden" name="values[old_edate]" value="{old_edate}">
			<input type="hidden" name="s_id" value="{s_id}">
			<input type="submit" name="save" value="{lang_save_mstone}">
		</td>
		<td><input type="checkbox" name="values[new]" value="True" {new_checked}>{lang_new}</td>
	</tr>
	<tr>
		<td colspan="4">
			<textarea name="values[description]" cols="50" rows="5">{description}</textarea>
		</td>
	</tr>
	<tr valign="bottom" height="50">
		<td align="right" colspan="4"><input type="submit" name="done" value="{lang_done}"></td>
	</tr>
</table>
</form> -->
</div>
<div id="tabcontent3" class="inactivetab" bgcolor="white">
<form method="POST" name="form_manage_files" action="{action_url_files}">
{files_table}
<table width="100%">
	<tr>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td align="right">
			<input type="submit" name="delete_files" value="{lang_delete_selected}">
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
	</tr>
<table>
</form>

<form method="POST" name="form_add_file" action="{action_url_files}" ENCTYPE="multipart/form-data">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
	<td width="80%" align="left">
		<INPUT class="input_text" NAME="attachfile" SIZE=48 TYPE="file">
	</td>
	<td align="right" width="20%">
		<input class="input_text" type="submit" name="addfile" value="{lang_add}">
	</td>
	</tr>
</table>
<script language="JavaScript">
	updateAccountingForm(document.getElementById("acc_factor"));
</SCRIPT>
</form>
</div>
</center>

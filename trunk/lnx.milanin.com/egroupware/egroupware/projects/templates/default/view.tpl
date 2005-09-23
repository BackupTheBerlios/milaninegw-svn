<!-- $Id: view.tpl,v 1.17 2004/06/01 10:38:39 lkneschke Exp $ -->

{app_header}

<table width="100%" border="0" cellspacing="0" cellpading="0" bgcolor="white">
	<tr>
		<th width="33%" id="tab1" class="activetab" onclick="javascript:tab.display(1);"><a href="#" tabindex="0" accesskey="1" onfocus="tab.display(1);" onclick="tab.display(1); return(false);" style="font-size:10px;">{lang_project_overview}</a></th>
		<th width="33%" id="tab2" class="activetab" onclick="javascript:tab.display(2);"><a href="#" tabindex="0" accesskey="2" onfocus="tab.display(2);" onclick="tab.display(2); return(false);" style="font-size:10px;">{lang_milestones}</a></th>
		<th width="33%" id="tab3" class="activetab" onclick="javascript:tab.display(3);"><a href="#" tabindex="0" accesskey="3" onfocus="tab.display(3);" onclick="tab.display(3); return(false);" style="font-size:10px;">{lang_files}</a></th>
	</tr>
</table>
<br>

<div id="tabcontent1" class="inactivetab" bgcolor="white">
<table width="100%" border="0" cellspacing="2" cellpadding="2">
<form method="POST" action="{action_url}">
<!-- BEGIN sub -->

	<tr bgcolor="{th_bg}" valign="top">
		<td>{lang_main}:</td>
		<td><a href="{main_url}">{pro_main}</a></td>
		<td>{lang_parent}:</td>
		<td><a href="{parent_url}">{pro_parent}</a></td>
	</tr>

<!-- END sub -->

	<tr bgcolor="{row_off}">
		<td width="20%">{lang_investment_nr}:</td>
		<td width="30%">{investment_nr}</td>
		<td width="20%">{lang_previous}:</td>
		<td width="30%">{previous}</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_number}:</td>
		<td>{number}</td>
		<td>{lang_title}:</td>
		<td>{title}</td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_category}:</td>
		<td colspan="3">{cat}</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_descr}:</td> 
		<td colspan="3">{descr}</td>
	</tr>

	<tr bgcolor="{row_off}">
		<td>{lang_status}:</td>
		<td>{status}</td>
		<td>{lang_access}:</td>
		<td>{access}</td>

	<tr bgcolor="{row_on}">
		<td>{lang_priority}:</td>
		<td><b>{priority}</b></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>

	<tr bgcolor="{row_off}">
		<td>{lang_url}:</td>
		<td>{url}</td>
		<td>{lang_reference}:</td>
		<td>{reference}</td>
	</tr>

	<tr height="15">
		<td>&nbsp;</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_start_date_planned}:</td>
		<td>{psdate}</td>
		<td>{lang_date_due_planned}:</td>
		<td>{pedate}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_start_date}:</td>
		<td>{sdate}</td>
		<td>{lang_date_due}:</td>
		<td>{edate}</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_creator}:</td>
		<td>{owner}</td>
		<td>{lang_cdate}:</td>
		<td>{cdate}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td>{lang_processor}:</td>
		<td>{processor}</td>
		<td>{lang_last_update}:</td>
		<td>{udate}</td>
	</tr>

	<tr height="15">
		<td>&nbsp;</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_customer}:</td>
		<td>{customer}</td>
		<td>{lang_customer_nr}:</td>
		<td>{customer_nr}</td>
	</tr>


	<tr bgcolor="{row_off}">
		<td>{lang_coordinator}:</td>
		<td colspan="3">{coordinator}</td>
	</tr>

	<tr bgcolor="{row_on}">
		<td valign="top">{lang_employees}:</td>
		<td colspan="2">
			<table width="100%" border="0" cellspacing="2" cellpadding="2">

				<tr>
					<td width="30%">&nbsp;</td>
					<td width="30%"><u>{lang_role}</u></td>
					<td width="40%"><u>{lang_events}</u></td>
				</tr>
<!-- BEGIN emplist -->

				<tr>
					<td valign="top">{emp_name}</td>
					<td valign="top">{role_name}</td>
					<td>{events}</td>
				</tr>

<!-- END emplist -->

			</table>
		<td valign="top" align="right">{edit_roles_events_button}</td>
	</tr>
	</tr>
	<tr height="15">
		<td>&nbsp;</td>
	</tr>


<!-- BEGIN nonanonym -->

	<tr bgcolor="{row_off}">
		<td>{lang_ptime}:&nbsp;{lang_hours}</td>
		<td colspan="3">{ptime}</td>
		<!--<td>{lang_utime}&nbsp;{lang_jobs_included}:&nbsp;{lang_hours}</td>
		<td>{uhours_jobs}</td>-->
	</tr>

	<tr bgcolor="{row_on}">
		<td>{lang_budget}:&nbsp;{currency}</td>
		<td>{budget}</td>
		<td>{lang_extra_budget}:&nbsp;{currency}</td>
		<td>{ebudget}</td>
	</tr>

{accounting_settings}
{accounting_2settings}

<!-- END nonanonym -->

	<tr height="15">
		<td>&nbsp;</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_result}:</td>
		<td colspan="3">{result}</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td valign="top">{lang_test}:</td>
		<td colspan="3">{test}</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_quality}:</td>
		<td colspan="3">{quality}</td>
	</tr>

	<tr height="15">
		<td>&nbsp;</td>
	</tr>

<!-- BEGIN done -->
         
	<tr height="50" valign="bottom">
		<td colspan="2">{edit_button}</td>
		<td align="right"><input type="submit" name="back" value="{lang_back}"></td>
		<td align="right"><input type="submit" name="done" value="{lang_done}"></td>
	</tr>

</table>
</div>
<div id="tabcontent2" class="inactivetab" bgcolor="white">
{milestones_table}
<table width="100%" border="0" cellspacing="2" cellpadding="2">
	<tr height="15">
		<td>&nbsp;</td>
	</tr>
	<tr height="50" valign="bottom">
		<td colspan="2">{edit_button}</td>
		<td align="right"><input type="submit" name="back" value="{lang_back}"></td>
		<td align="right"><input type="submit" name="done" value="{lang_done}"></td>
	</tr>
</table>
</div>
<div id="tabcontent3" class="inactivetab" bgcolor="white">
{files_table}
</div>
<!-- END done -->


<!-- BEGIN accounting_act -->


	<tr bgcolor="{row_on}">
		<td valign="top">{lang_bookable_activities}:</td>
		<td colspan="3">{book_activities_list}&nbsp;</td>
	</tr>
	<tr bgcolor="{row_off}">
		<td valign="top">{lang_billable_activities}:</td>
		<td colspan="3">{bill_activities_list}&nbsp;</td>
	</tr>

<!-- END accounting_act -->

<!-- BEGIN accounting_own -->

	<tr bgcolor="{row_off}" valign="top">
		<td>{lang_accounting}:</td>
		<td>{accounting_factor}</td>
		<td>{lang_accounting_factor_for_project}:&nbsp;{currency}</td>
		<td>
			<table border="0" cellpadding="1" cellspacing="0">
				<tr>
					<td>{project_accounting_factor}&nbsp;{lang_per_hour}</td>
				</tr>
				<tr>
					<td>{project_accounting_factor_d}&nbsp;{lang_per_day}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor="{row_on}">
		<td>{lang_billable}:</td>
		<td colspan="3">{billable}</td>
	</tr>

<!-- END accounting_own -->

<!-- BEGIN accounting_both -->

	<tr bgcolor="{row_off}">
		<td>{lang_invoicing_method}:</td>
		<td>{inv_method}</td>
		<td valign="top">{lang_discount}:&nbsp;{discount_type}</td>
		<td>{discount}</td>
	</tr>

<!-- END accounting_both -->

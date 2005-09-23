<!-- $Id: addressbook.tpl,v 1.27 2004/06/15 08:43:37 ralfbecker Exp $ -->
<STYLE type="text/css">
	A {text-decoration:none;}
	<!--
	A:link {text-decoration:none;}
	A:visted {text-decoration:none;}
	A:active {text-decoration:none;}
	body {margin-top: 0px; margin-right: 0px; margin-left: 0px;}
	td {text-decoration:none;}
	tr {text-decoration:none;}
	table {text-decoration:none;}
	center {text-decoration:none;}
	-->
</STYLE>
<script LANGUAGE="JavaScript">
	function ExchangeCustomer(thisform)
	{
		opener.document.app_form.abid.value = thisform.abid.value;
		opener.document.app_form.name.value = thisform.name.value;
	}
</script>
<div id="divMain">
<center>
<table border="0" width="100%">
	<tr>
		<td colspan="4">
			<table border="0" width="100%">
				<tr>
				{left}
					<td align="center">{lang_showing}</td>
				{right}
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="33%" align="left">
			<form action="{cats_action}" name="form" method="POST">
			{lang_category}&nbsp;&nbsp;&nbsp;<select name="cat_id" onChange="this.form.submit();"><option value="">{lang_all}</option>{cats_list}</select>
			<noscript>&nbsp;<input type="submit" name="submit" value="{lang_submit}"></noscript></form></td>
		<td width="33%" align="center"><form method="POST" name="filter" action="{filter_action}">{filter_list}</form></td>
		<td width="33%" align="right">
			<form method="POST" action="{search_action}">
			<input type="text" name="query">&nbsp;<input type="submit" name="search" value="{lang_search}">
			</form></td>
	</tr>
</table>
<table border="0" width="100%" cellpadding="2" cellspacing="2">
	<tr bgcolor="{th_bg}">
		<td width="30%" bgcolor="{th_bg}" align="center"><font face="{font}">{sort_company}</font></td>
		<td width="20%" bgcolor="{th_bg}" align="center"><font face="{font}">{sort_firstname}</font></td>
		<td width="20%" bgcolor="{th_bg}" align="center"><font face="{font}">{sort_lastname}</font></td>
		<td width="10%" bgcolor="{th_bg}" align="center"><font face="{font}">{lang_select}</font></td>
	</tr>

<!-- BEGIN abook_list -->

	<tr bgcolor="{tr_color}">
		<td><font face="{font}">{company}</font></td>
		<td><font face="{font}">{firstname}</font></td>
		<td><font face="{font}">{lastname}</font></td>
		<form>
		<input type="hidden" size="25" name="abid" value="{abid}">
		<input type="hidden" size="25" name="name" value="{company} {firstname} {lastname}">
		<td align="center"><font face="{font}"><input type="button" value="{lang_select}" onClick="ExchangeCustomer(this.form);" name="button"></td>
		</font></form>    
	</tr>

<!-- END abook_list -->

</table>
<table cellpadding="2" cellspacing="2">
	<tr> 
		<form>  
		<td><font face="{font}"><input type="button" name="Done" value="{lang_done}" onClick="window.close()"></font>
		</form>
		</td>
	</tr>
</table>
</center>
</div>

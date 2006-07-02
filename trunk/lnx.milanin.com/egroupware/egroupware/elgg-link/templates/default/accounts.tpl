<!-- $Id: accounts.tpl,v 1.16 2004/07/04 17:33:13 ralfbecker Exp $ -->
<style type="text/css">
	.letter_box,.letter_box_active {
		background-color: #D3DCE3;
		width: 25px;
		border: 1px solid #D3DCE3;
		text-align: center;
		cursor: pointer;
		cusror: hand;
	}
	.letter_box_active {
		font-weight: bold;
		background-color: #E8F0F0;
	}
	.letter_box_active,.letter_box:hover {
		border: 1px solid black;
		background-color: #E8F0F0;
	}
</style>
<script type="text/javascript">
 function toggleLayer(whichLayer)
{
            if (document.getElementById)
{
          // this is the way the standards work
              var style2 = document.getElementById(whichLayer).style;
          style2.display = style2.display == "block" ? "none":"block";
}
            else if (document.all)
{
          // this is the way old msie versions work
              var style2 = document.all[whichLayer].style;
          style2.display = style2.display == "block" ? "none":"block";
}
            else if (document.layers)
{
          // this is the way nn4 works
              var style2 = document.layers[whichLayer].style;
          style2.display = style2.display == "block" ? "none":"block";
}
}
</script>
    
<div align="center">
<table border="0" width="80%">
	<tr>
		<td align="right" colspan="5">
			<form method="POST" action="{accounts_url}">
				<table width="100%"><tr>
					<td>{lang_group} {group}</td>
					<td align="right">
						{query_type}
						<input type="text" name="query">
						<input type="submit" name="search" value="{lang_search}">
					</td>
				</tr></table>
			</form>
		</td>
	</tr>
	<tr>
		<td colspan="5">
			<table width="100%"><tr>
<!-- BEGIN letter_search -->
				<td class="{class}" onclick="location.href='{link}';">{letter}</td>
<!-- END letter_search -->
			</tr></table>
		</td>
	</tr>
	<tr>
		{left_next_matchs}
		<td align="center">{lang_showing}</td>
		{right_next_matchs}
	</tr>
</table>
</div>
 <div align="center">
  <table border="0" width="80%">
   <tr class="th">
    <td width="20%">{lang_firstname}</td>
    <td width="20%">{lang_lastname}</td>
    <td width="20%">{lang_industry}</td>
    <td width="20%">{lang_occupation}</td>
    <td width="5%">{lang_membership_date}</td>
    <td width="5%">{lang_linkedin}</td>
    <td width="5%">{lang_view}</td>
   </tr>

 <!-- BEGIN row -->
   <tr class="{class}" style="{online}">
    <td>{account_firstname}</td>
    <td>{account_lastname}</td>
    <td>{account_industry}</td>
    <td>{account_occupation}</td>
    <td>{account_membership_date}</td>
    <td>{row_linkedin}</td>
    <td><span><a href="javascript:toggleLayer('LinksList_{account_id}')">{row_view}</a></td>
   </tr>
    <tr>
      <td colspan="7">
    <div class="infobox" id="LinksList_{account_id}" style="display : none">
    <table border="0" width="100%">
      <tr>
    <td><a href="{profile_link}">{lang_full_profile}</a></td>
    <td><a href="{weblog_link}">{lang_weblog}</a></td>
    <td><a href="{message_link}">{lang_send_message}</a></td>
      </tr>
    </table>
      </div>
      </td>
    </tr>
   
<!-- END row -->

  </table>
 </div>

  <div align="center">
   <table border="0" width="80%">
    <tr>
	 <td align="left">
	  <form method="POST" action="{new_action}">
	   {input_add}
	  </form>
	 </td>
    </tr>
   </table>
  </div>


<!-- BEGIN row_empty -->
   <tr>
    <td colspan="5" align="center">{message}</td>
   </tr>
<!-- END row_empty -->

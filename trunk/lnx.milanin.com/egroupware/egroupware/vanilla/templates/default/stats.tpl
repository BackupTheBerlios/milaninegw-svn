<!-- BEGIN stats -->
<div style="border: #9c9c9c 1px solid; margin:15px">
<table border="0" cellpadding="0" cellspacing="0" width="100%"> 
 <tr nowrap align="center">

  <td align="left">
	<div class="divSideboxHeader" style="height: 15px; padding-top: 0px">
	<table border="0" cellpadding="1" cellspacing="0" width="100%">
	  <tr>
		<td align="left">&nbsp;<strong>{lang_my_profile_stats}</strong></td>
	  </tr>
	</table>
	</div>
  </td>
 </tr>
 <tr>
 <td align="center">
<table align="center" border="0" width="100%">
 <tr>
 	<td align="left" width="50%" valign="top">
        	<table width="100%">
                <tr><th colspan="2">{lang_views_by_members} {lang_in_last_days}</th></tr>
                {members_views}
                </table>
        </td>
        <td align="right"  width="50%" valign="top">
                <table width="100%">
                <tr><th colspan="3">{lang_views_by_guests} {lang_in_last_days}</th></tr>
                <tr>
                  <th>{lang_guest_from}</th>
                  <th>{lang_guest_last_date}</th>
                  <th>{lang_guest_counter}</th>
                </tr>
                {guests_views}
                </table>
        </td>
 </tr>
</table>
</td>
</tr>
</table>
</div>
</p>
<!-- END stats -->

<!-- BEGIN member_view -->
<tr class="{row_class}">
<td>{member_icon}</td>
<td>
	<ul>
        <li>{member_name}</li>
        <li>{member_date}</li>
        </ul>
</td>
</tr>
<!-- END member_view -->
<!-- BEGIN guest_view -->
<tr class="{row_class}">
<td>{guest_from}</td>
<td>{guest_last_date}</td>
<td>{guest_counter}</td>
</tr>
<!-- END guest_view -->
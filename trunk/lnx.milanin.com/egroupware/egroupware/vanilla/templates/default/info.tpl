<!-- BEGIN info -->
<table align="center" border="0" width="40%">
 <tr><th colspan="2">{lang_my_profile}</tr>
 <tr>
 	<td align="center" colspan="2">{relative_percentage}</td>
 </tr>
 <tr>
  <td align="right">{edit_link}</td><td align="left">{show_link}</td>
 </tr>
</table>
<!-- END info -->

<!-- BEGIN stats -->
<table align="center" border="0" width="90%">
 <tr><th colspan="2">{lang_my_profile_stats}</tr>
 <tr>
 	<td align="center" valign="top">
        	<table>
                <tr><th colspan="2">{lang_views_by_members} {lang_in_last_days}</th></tr>
                {members_views}
                </table>
        </td>
        <td align="center"  valign="top">
                <table>
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
<!-- $Id: index.tpl,v 1.1.4.1 2004/08/06 10:00:54 ak703 Exp $ -->
{printer_friendly}
<table border="0" width="100%" cols="5" class="calDayViewShadowBox">
 <tr>
  <td align="left" valign="top" width="20%">
   {small_calendar_prev}</td>
  <td align="center" valign="middle" width="20%">
   <b>{prev_month_link}</b></td>
  <td align="center" width="20%">
   <font size="+2" color="#000000"><b>{month_identifier}</b></font>
   <font size="+1" color="#000000"><br>{username}</font></td>
  <td align="center" valign="middle" width="20%">
   {next_month_link}</td>
  <td align="right" valign="top" width="20%">
   {small_calendar_next}</td>
 </tr>
 <tr>
  <td colspan=5 width=100%>
	<table width=100% class="calDayViewSideBoxes">
		<tr>
			<td>{large_month}</td>
		</tr>
	</table><p style="font-size:10px; text-align:center">{print}</p>
</td>
</tr>
</table>



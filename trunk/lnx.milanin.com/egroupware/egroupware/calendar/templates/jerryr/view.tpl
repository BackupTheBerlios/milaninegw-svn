<!-- $Id: view.tpl,v 1.1.4.1 2004/08/06 10:00:54 ak703 Exp $ -->
<!-- BEGIN view_event -->
<center>
<table border="0" width="100%" class="calDayViewShadowBox">
<tr>
<td>
<table border="0" width="100%">
  {row}
  <tr>
   <td><table cellspacing="5"><tr>{button_left}</tr></table></td>
   <td align="center"><table cellspacing="5"><tr>{button_center}</tr></table></td>
   <td align="right"><table cellspacing="5"><tr>{button_right}</tr></table></td>
  </tr>
 </table>
</td>
</tr>
</table>
</center>
<!-- END view_event -->

<!-- BEGIN list -->
  <tr bgcolor="{tr_color}">
   <td valign="top" width="30%">&nbsp;<b>{field}:</b></td>
   <td colspan="2" valign="top" width="70%">{data}</td>
  </tr>
<!-- END list -->

<!-- BEGIN hr -->
 <tr>
  <td colspan="3" bgcolor="{th_bg}" align="center">
   <b>{hr_text}</b>
  </td>
 </tr>
<!-- END hr -->

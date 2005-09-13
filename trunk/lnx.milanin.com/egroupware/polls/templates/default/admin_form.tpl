<!-- BEGIN form -->
 <center>{message}</center>

 <form method="POST" action="{form_action}">
 <input type="hidden" name="poll_id" value="{poll_id}">
 {hidden}
  <table border="0" width="400" align="center">
   <tr bgcolor="{th_bg}">
    <td colspan="2">{td_message}</td>
   </tr>

   {rows}

   <tr>
    <td colspan="2" align="center">
	  <table border="0" align="center">
		<tr>
		  {buttons}
		</tr>
	  </table>
	</td>
   </tr>
  </table>
 </form>
<!-- END form -->

<!-- BEGIN row -->
   <tr bgcolor="{tr_color}">
    <td>{td_1}</td>
    <td>{td_2}</td>
   </tr>
<!-- END row -->

<!-- BEGIN messagebar -->
 <tr bgcolor="{th_bg}">
  <td colspan="2"><b>{mesg}</b></td>
 </tr>
<!-- END messagebar -->

<!-- BEGIN button -->
    <td valign="top" align="center"><input type="submit" name="{btn_name}" value="{btn_value}"></td>
<!-- END button -->

<!-- BEGIN input -->
  <input type="text" name="{input_name}" value="{input_value}" size="35">
<!-- END input -->

<!-- BEGIN results -->
<tr>
 <td colspan="2">
  {poll_results}
 </td>
</tr>
<!-- END results -->

<!-- BEGIN answers -->
<tr>
 <td colspan="2">
   <table border="0" width="100%">
     {poll_answers}
   </table>
 </td>
</tr>
<!-- END answers -->

<!-- BEGIN answer_row -->
  <tr bgcolor="{tr_color}">
   <td>{option_text}</td>
   <td width="50">{answer_actions}</td>
  </tr>
<!-- END answer_row -->


<!-- BEGIN form -->
<form action="{form_action}" method="post">
<input type="hidden" name="poll_id" value="{poll_id}">

<table border="0" align="center" width="50%">
 <tr>
  <td colspan="2" bgcolor="{title_bgcolor}" align="center">{poll_title}</td>
 </tr>

 {entries}

 <tr bgcolor="{bgcolor}">
  <td colspan="2">&nbsp;</td>
 </tr>

 <tr bgcolor="{bgcolor}">
  <td colspan="2" align="center"><input name="submit" type="submit" value="{lang_vote}"></td>
 </tr>

</table>

</form>
<!-- END form -->

<!-- BEGIN entry -->
 <tr bgcolor="{tr_color}">
  <td align="center"><input type="radio" name="poll_voteNr" value="{vote_id}"></td>
  <td>{option_text}</td>
 </tr>
<!-- END entry -->

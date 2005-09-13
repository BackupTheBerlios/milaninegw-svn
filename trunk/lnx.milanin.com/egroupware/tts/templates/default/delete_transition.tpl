<!-- $Id: delete_transition.tpl,v 1.2 2003/10/09 21:59:39 ralfbecker Exp $ -->
<!-- BEGIN delete_transition.tpl -->
<br><br>
<center><font color="red">{messages}</font></center>

<form method="POST" action="{delete_transition_link}">
<p><b>{lang_are_you_sure}</b></p>
<p>
<p align="center">
	<input type="submit" name="delete" value="{lang_delete}"> &nbsp;
	<input type="submit" name="cancel" value="{lang_cancel}">
</p>
</form>

<!-- END delete_transition.tpl -->


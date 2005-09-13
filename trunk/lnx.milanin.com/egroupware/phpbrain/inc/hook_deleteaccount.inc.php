<?php
	/* $Id: hook_deleteaccount.inc.php,v 1.1.2.1 2004/10/29 16:31:20 alpeb Exp $ */

	$bokb = CreateObject('phpbrain.bokb');

	if((int)$_POST['new_owner'] == 0)
	{
		$bokb->delete_owner_articles((int)$_POST['account_id']);
	}
	else
	{
		$bokb->change_articles_owner((int)$_POST['account_id'],(int)$_POST['new_owner']);
	}
?>

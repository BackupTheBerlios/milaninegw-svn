<?php
$inv_msg_lang=7;
if ($_REQUEST['inv_msg_lang'] != null) {
$inv_msg_lang=$_REQUEST['inv_msg_lang'];
}
$inv_idx_lang=0;
if ($_REQUEST['inv_idx_lang'] != null) {
$inv_idx_lang=$_REQUEST['inv_idx_lang'];
}
$invite_name="";
if ($_REQUEST['invite_name'] != null) {
$invite_name=$_REQUEST['invite_name'];
}
$invite_email="";
if ($_REQUEST['inv_idx_lang'] != null) {
$invite_email=$_REQUEST['invite_email'];
}
	// Ask for details to invite a friend
	
	
		$run_result .= <<< END
		
		<form name="invite_form" action="" method="post">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="inv_msg_lang" value="">
		<input type="hidden" name="inv_idx_lang" value="">
		
END;
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'Their name',
														'column1' => run("display:input_field",array("invite_name",$invite_name,"text"))
							)
							);
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'Their email address',
														'column1' => run("display:input_field",array("invite_email",$invite_email,"text"))
							)
							);

//reserve template ident 6-10 for multilang invitation 
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'Message language',
														'column1' => run("display:input_field",array(" name='invite_lang' onchange='getInvitationLang()' ",$_REQUEST['inv_msg_lang'],"selectbox", "SELECT * FROM `templates` where `ident` > 5 and `ident` < 11"))
							)
							);


//array("invite_title","","title_selectbox", "7") "7" is Italian lang title template identifier(might be different if any use). If the user lang is different, add templates into DB 
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'Message title',
														'column1' => run("display:input_field",array(" name='invite_title' onchange='getInvitationMsg()'","","selectbox", "SELECT * FROM `template_elements` where `template_id` = ".$inv_msg_lang." order by name asc"))
							)
							);

							
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'An optional message',
														'column1' => run("display:input_field",array("invite_text","","longtext"))
							)
							);

							
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => '&nbsp;',
														'column1' => '<input type="submit" value="Invite" onclick="return doInvite()"/>'
							)
							);
							
		$run_result .= <<< END
		
			
		
		</form>
<script language="JavaScript" type="text/javascript">
<!--

getInvitationMsg();	
document.invite_form.invite_lang[
END;
$run_result .=$inv_idx_lang;
$run_result .= <<< END
].selected = true;
-->
</script>		
END;

?>


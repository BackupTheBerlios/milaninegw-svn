<script language="JavaScript" type="text/javascript">
				<!--
				function getInvitationMsg()
				{
				  for (var iSelect = 0; iSelect < document.invite_form.inv_idx_title.length; iSelect++) {
				  if (document.invite_form.inv_idx_title[iSelect].selected == true)
				   break;
				  }
				  document.invite_form.invite_text.value=template_content[iSelect];
				  return true;
				}
				-->
			</script>
<?php

$inv_msg_lang=title1;
if ($_REQUEST['inv_msg_lang'] != null) {
$inv_msg_lang=$_REQUEST['inv_msg_lang'];
}
$inv_idx_lang=language1;
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

  $run_result .= run("templates:draw", array(
												'context' => 'databox1',
												'name' => "Message language",
												'column1' => run("invite:invite_lang_select",array("inv_idx_lang",$inv_idx_lang))
											)
											);

		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'Message title',
														'column1' => run("invite:invite_title_select",array("inv_idx_title",$inv_idx_lang, $inv_msg_lang))
							)
							);

							
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'An optional message',
														'column1' => run("display:input_field",array("invite_text","","longtext", "readonly=true"))
							)
							);

//$run_result .= run("invite:invite_text_select", array($inv_idx_lang, $inv_msg_lang));
$run_result .= run("invite:invite_body", array($inv_idx_lang));							
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
-->
</script>		
END;

?>


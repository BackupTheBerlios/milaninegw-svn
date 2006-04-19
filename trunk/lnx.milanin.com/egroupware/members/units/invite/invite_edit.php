<script language="JavaScript" type="text/javascript">
				<!--
				function getInvitationMsg()
				{
				  for (var iSelect = 0; iSelect < document.invite_form.inv_idx_title.length; iSelect++) {
				  if (document.invite_form.inv_idx_title[iSelect].selected == true)
				   break;
				  }
				  document.invite_form.invite_text.value=template_content[iSelect];
				  
				  if (document.invite_form.inv_idx_title.length > 1)
				   document.invite_form.invite_title.value=document.invite_form.inv_idx_title[iSelect].text;

				  for (var iSelect = 0; iSelect < document.invite_form.inv_idx_salut.length; iSelect++) {
				  if (document.invite_form.inv_idx_salut[iSelect].selected == true)
				   break;
				  }
				  if (document.invite_form.inv_idx_salut.length > 1)
				   document.invite_form.inv_salut.value=document.invite_form.inv_idx_salut[iSelect].text;
				  
				  //
				  return true;
				}
				-->
			</script>
<?php
	// Ask for details to invite a friend
	

		$run_result .= <<< END
		<form name="invite_form" action="" method="post">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="inv_msg_lang" value="">
		<input type="hidden" name="inv_idx_lang" value="">
		<input type="hidden" name="inv_salut" value="$inv_salut">
		<input type="hidden" name="invite_title" value="$invite_title">
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
												'name' => "Salutation",
												'column1' => run("invite:invite_salut_select",array("inv_idx_salut",$inv_idx_lang, $inv_idx_salut))
											)
											);
							
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'An optional message',
														'column1' => run("display:input_field",array("invite_text","","longtext", "readonly=true"))
							)
							);

$run_result .= run("invite:invite_body", array($inv_idx_lang));							
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => '&nbsp;',
														'column1' => '<input type="submit" value="Preview" onclick="return doInvite(1)"><input type="submit" value="Invite" onclick="return doInvite(2)"/>'
							)
							);
		$run_result .= "</form>"
?>
<script language="JavaScript" type="text/javascript">
				<!--
       function getInvitationMsg()
				{
				  return true;
				}
				function doPreview(i)
         {
				  if (i == 2)
				  document.invite_form.action.value='invite_invite';
				  
				  if (i == 1)
				  document.invite_form.action.value='invite_edit';
				  
				  //alert(document.invite_form.action);
				  return true;
				}
				-->
			</script>
			
<?php
	// Ask for details to invite a friend

$inv_idx_title = $_REQUEST['inv_idx_title'];
$invite_text = $_REQUEST['invite_text'];
	
  $From_str = "<p>From : ".$_SESSION['name'];
  $Subject_str = "<p>Subject :".$invite_title;
  $To_str = "<p>".$inv_salut." ".$invite_name.",";
  $Body_str = "<p>".$invite_text;
  $Footer_str = "<p>".$_SESSION['name']."<p>Milanin provides necessary joinUs information.";
	
	$run_result .= <<< END
		<form name="invite_form" action="" method="post">
		<input type="hidden" name="action" value="">
		<input type="hidden" name="inv_idx_title" value=$inv_idx_title>
		<input type="hidden" name="inv_idx_lang" value=$inv_idx_lang>
		
		<input type="hidden" name="invite_name" value="$invite_name">
		<input type="hidden" name="invite_email" value="$invite_email">
		<input type="hidden" name="inv_idx_title" value="$inv_msg_lang">
		<input type="hidden" name="invite_text" value="$invite_text">
		<input type="hidden" name="inv_idx_salut" value="$inv_idx_salut">
		<input type="hidden" name="inv_salut" value="$inv_salut">
		<input type="hidden" name="invite_title" value="$invite_title">
		
END;

  
  
  $run_result .= run("templates:draw", array(
												'context' => 'databox1',
												'name' => "Preview",
												'column1' => $From_str.$Subject_str.$To_str.$Body_str.$Footer_str
											)
											);

$run_result .= run("invite:invite_body", array($inv_idx_lang));							
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => '&nbsp;',
														'column1' => '<input type="submit" value="Back to edit" onclick="return doPreview(1)"><input type="submit" value="Send invitation" onclick="return doPreview(2)"/>'
							)
							);
		$run_result .= "</form>"
?>

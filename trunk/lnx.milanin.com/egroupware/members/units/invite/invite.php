<?php

	// Ask for details to invite a friend
	
		$run_result .= <<< END
		
		<form name="invite_form" action="" method="post">
		
END;
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'Their name',
														'column1' => run("display:input_field",array("invite_name","","text"))
							)
							);
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'Their email address',
														'column1' => run("display:input_field",array("invite_email","","text"))
							)
							);

//array("invite_title","","title_selectbox", "7") "7" is Italian lang title template identifier(might be different if any use). If the user lang is different, add templates into DB 
		$run_result .= run("templates:draw", array(
														'context' => 'databox1',
														'name' => 'Message title',
														'column1' => run("display:input_field",array("invite_title","","title_selectbox", "7"))
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
														'column1' => '<input type="submit" value="Invite" />'
							)
							);
							
		$run_result .= <<< END
		
			<input type="hidden" name="action" value="invite_invite" />
		
		</form>
<script language="JavaScript" type="text/javascript">
<!--
getInvitationMsg();	
-->
</script>		
END;

?>


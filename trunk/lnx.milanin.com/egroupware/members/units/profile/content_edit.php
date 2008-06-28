<?php
global $pVal;
	$run_result .= <<< END
<p>
	This screen allows you to edit your profile. Blank fields will not show up
	on your profile screen in any view; you can change the access level for each
	piece of information in order to prevent it from falling into the wrong hands.
	For example, we strongly recommend you keep your address to yourself or a
	few trusted parties.
</p>
<p>&nbsp;(<font color="red">*</font>) - Required fields</p>
END;
if(!$pVal->isValid)
{
	$run_result .= '<p style="color:red;">Please, complete all mandatory fields below in a correct way.</p>';
}
?>
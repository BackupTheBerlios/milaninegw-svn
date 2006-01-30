<?php

$invite_lang1 = run("invite:invite_lang");

	$run_result .= "<select name=\"". $parameter[0] . "\" onchange=\"document.invite_form.submit();\" >";

	if (sizeof($invite_lang1['lang']) > 0) {
		foreach($invite_lang1['lang'] as $lang) {
			if ($parameter[1] == $lang[1] && $parameter[1] != "") {
				$selected = "selected = \"selected\"";
			} else {
				$selected = "";
			}
			$run_result .= <<< END
	<option value="{$lang[1]}" {$selected}>
		{$lang[0]}
	</option>
END;
		}
	}

	$run_result .= "</select>";
	
?>
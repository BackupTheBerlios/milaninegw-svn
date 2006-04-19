<?php
$invite_lang1 = run("invite:invite_salut", array($parameter[1]));

	$run_result .= "<select name=\"". $parameter[0] . "\">";
//echo sizeof($invite_lang1['salut']);
	if (sizeof($invite_lang1['salut']) > 0) {
		foreach($invite_lang1['salut'] as $salut) {
		  if ($parameter[2] == $salut[1] && $parameter[2] != "") {
				$selected = "selected = \"selected\"";
			} else {
				$selected = "";
			}
			$run_result .= <<< END
	<option value="{$salut[1]}" {$selected}>
		{$salut[0]}
	</option>
END;
		}
	}
	$run_result .= "</select>";

?>
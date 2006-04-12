<?php

//$parameter[0] selectbox name
//$parameter[1] selected lang value
//$parameter[2] selected title value

$invite_lang1 = run("invite:invite_title", array($parameter[1]));

	$run_result .= "<select onchange=\"getInvitationMsg()\" name=\"". $parameter[0] . "\">";
//echo sizeof($invite_lang1['lang']);
	if (sizeof($invite_lang1['lang']) > 0) {
		foreach($invite_lang1['lang'] as $lang) {
		 // echo "??".$parameter[2]."--".$lang[1]."!!!";
			if ($parameter[2] == $lang[1] && $parameter[2] != "") {
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

//create javascript client array for fast text update


?>
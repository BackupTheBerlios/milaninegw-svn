<?php

	global $page_owner;
	$url = url;
		
	// If this is someone else's portfolio, display the user's icon
		$run_result .= "<div class=\"box_user\">";
		if ($page_owner != -1) {
			// $rsslink = "(<a href=\"". url . run("users:id_to_name",$page_owner) . "/rss/\">RSS</a>)";
			if ($page_owner != $_SESSION['userid']) {
				$run_result .= run("users:infobox", array("Profile Owner",array($page_owner)));
			} else {
				$run_result .= run("users:infobox", array("You",array($page_owner)));
			}
		}
		$run_result .= "</div>";

	if ((!defined("logged_on") || logged_on == 0) && $page_owner == -1) {

		$body = <<< END
		
		<a href="/egroupware/login.php" caption="Login">Login</a>
END;
		$run_result .= $body;
			
	}

?>

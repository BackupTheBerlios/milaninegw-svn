<?
global $page_owner;
		if ($parameter == "uploadvideos") {
			if ($page_owner == $_SESSION['userid']) {
				$run_result = true;
			}
			
		}
?>
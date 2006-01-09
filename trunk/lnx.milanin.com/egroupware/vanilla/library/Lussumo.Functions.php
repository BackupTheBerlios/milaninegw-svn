<?php
function TabClass($CurrentPage, $ComparisonPage) {
	return ($CurrentPage == $ComparisonPage)?"TabOn":"TabOff";
}

function WriteHead($PageLabel) {
	$SelfUrl = basename(ForceString(@$_SERVER['PHP_SELF'], "index.php"));

	echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>
	<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
	<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-ca\">

		<head>
			<title>".agAPPLICATIONTITLE." - ".$PageLabel."</title>
			<link rel=\"shortcut icon\" href=\"/favicon.ico\" /> 
			<link rel=\"stylesheet\" type=\"text/css\" href=\"./style/global.css\" media=\"screen\" />
			<link rel=\"stylesheet\" type=\"text/css\" href=\"./style/handheld.css\" media=\"handheld\" />
			<script type=\"text/javascript\" src=\"./js/global.js\"></script>
			<script type=\"text/javascript\" src=\"./js/data.js\"></script>
			<script type=\"text/javascript\" src=\"./js/lussumo.js\"></script>
		</head>
		<body>
			<div class=\"SiteContainer\">");
				
}

function WriteFoot() {
		echo("</div>
		</body>
	</html>");
}
?>
<?
function IsValidEmail($email) #return '' if not mail
{
	$email=trim($email);
	return (preg_replace("/(([\w\d-]+(\.[\w\d-]+)*)\@(([\w\d-]+(\.[\w\d-]+)*)\.([\w]{2,4})))/", "", $email) == "");
}

function str_is_int($str) {
	$var=intval($str);
	return ($str==$var."");
}

function setInteger($value)
{
	settype($value, "integer");
	return $value;
}

function ValidateLinkedin($value, $returnInt=false)
{
	if(str_is_int($value))
		return $returnInt ? $value : true;
	
	if(trim($value) == "")
		return true;
	
	$matches = array();
	preg_match("/^.*?linkedin\.com.*?[?&]+key\=(\d+).*?$/i", $value, $matches);
	if(str_is_int($matches[1]))
		return $returnInt ? $matches[1] : true;
	
	preg_match("/^.*?linkedin\.com.*?[?&]+id\=(\d+).*?$/i", $value, $matches);
	if(str_is_int($matches[1]))
		return $returnInt ? $matches[1] : true;
	
	return false;
}

function IsValidDate($date) #return '' if not mail
{
	if(trim($date) == "") return true;
	$date = split("/", $date);
	while( count($date) < 3 )
		array_push($date, "0");
	if( !(is_numeric($date[0]) && is_numeric($date[1]) && is_numeric($date[2])) )
		return false;

	$date = array_map("setInteger", $date);
	return checkdate($date[1], $date[0], $date[2]);
}

function CheckDateValue($dd, $mm, $yyyy)
{
	$dd = trim($dd); $mm = trim($mm); $yyyy = trim($yyyy);
	if( $dd == "" && $mm == "" && $yyyy == "")
		return true;
	else
	{
		if( !(is_numeric($dd) && is_numeric($mm) && is_numeric($yyyy)) )
			return false;
		else return checkdate($mm, $dd, $yyyy);
	}
}

function IsValidWebUrl($url)
{
	if(trim($url) == "") return true;
	$pattern = "/(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?/i";
	preg_match($pattern, $url, $matches);

	return (count($matches) > 0 && $matches[0] == $url);
}

function IsValidFriendlyUrl($url)
{
	if(trim($url) == "") return true;
	$pattern = "/^([A-Za-z0-9\.\_\-]+)$/i";
	preg_match($pattern, $url, $matches);
	
	return (count($matches) > 0 && $matches[0] == $url);
}
?>
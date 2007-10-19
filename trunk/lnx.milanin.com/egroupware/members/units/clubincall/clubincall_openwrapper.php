<?
// echo $_GET['wrapper'];
// require_once("../../includes.php");
setcookie("last_settings_wrapper",
          $_GET['wrapper'],
          time()+60*60*24*365,
          dirname($_SERVER['REQUEST_URI']));
echo $_GET['wrapper'];
?>
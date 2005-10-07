<?
require("includes.php");
//session_id($_COOKIE['sessionid']);
//session_start();

?>
<html>
<body>
<pre>
<?
$name = "/tmp/sess_".$_COOKIE['sessionid'];
$fp = fopen($name, 'rb');
fpassthru($fp);
echo session_id()."\n------------------------------------------\n";

echo session_id()."\n----------------------------------------0--\n";

print_r($_SESSION);
print_r($_COOKIE);

echo "\n----------------------------------------0--\n";
echo $_SESSION['user_info_cache'][$_SESSION['userid']]->name;
echo "\n----------------------------------------0--\n";
print_r($_SESSION['user_info_cache'][$_SESSION['userid']]);

?>

</pre>
</body>
</html>
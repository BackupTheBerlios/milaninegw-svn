<?
define('PHPGW_API_INC', path.'../egroupware/phpgwapi/inc');
require_once(PHPGW_API_INC.'/class.phpmailer.inc.php');
require_once(PHPGW_API_INC."/class.send.inc.php");

function IsValidEmail($email) #return '' if not mail
{
	$email=trim($email);
	return (preg_replace("/(([\w\d-]+(\.[\w\d-]+)*)\@(([\w\d-]+(\.[\w\d-]+)*)\.([\w]{2,4})))/", "", $email) == "" && $email != "") ;
}

function SendForm()
{
	$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$url = preg_replace ("/(([\&|\?]*)f=(\d*))/i", "", $url);
	$url = preg_replace ("/&$/", "", $url);
	
	
	$mailer = new PHPMailer();
	
	$values = $_POST;
	$mailer->Subject = "News from the web site of Business Club MilanIN";  // change it 
	$mailer->From = "messenger@milanin.com";  // change it
	$mailer->FromName = "Milan IN website";  // change it
	
	$body = sprintf("Hello %s\n\r", $_P["nameto"]);
	$body .= sprintf("This link %s was sent by your friend.\n\r", $url);
	$body .= sprintf("Name : %s\n\r", $values['name']);
	$body .= sprintf("-----------------\n\r");
	$body .= sprintf("His comment:\n\r%s",$values['comments']);
	$mailer->Body = $body;
	$mailer->AddAddress($values['emailto']);
	$mailer->AddReplyTo("no-reply@milanin.com", "no-reply@milanin.com");
	$mailer->Send();
}

if (isset($parameter)) 
{
	$post = $parameter;
	$_P = $_POST;
	$message = "";
	
	if($_P["sendto"] == "1" && !IsValidEmail($_POST['emailto']) )
		$message = "E-mail address is not valid";
	if($_P["sendto"] == "1" && IsValidEmail($_POST['emailto']) )
	{
		$message = "Your message was sent successfully.";
		SendForm();
	}
	
	if($message != "")
		$run_result .= <<< END
		<div align="center"><font color="#FF0000">$message</font></div>
END;
	$run_result .= <<< END

<script>
function ShowUrlForm(archor)
{
	var div = document.getElementById("newsFormUrl");
	if( div != null)
	{
		var isVisible = (div.style.display != 'none');
		div.style.display = isVisible ? 'none' : 'block';
		archor.innerHTML = isVisible ? 'Send to a Friend' : 'Hide Form';
		return false;
	}
	return true;
}
</script>

	<div class='newscontainer' id="newsFormUrl" style="display:{display};">
	<h2>Send this message to a friend</h2>
	<a name="form"></a>
	<form action="" method="post" name="milanin_add_comment_form" id="milanin_add_comment_form">
	<table width="95%" class="profiletable" align="center" style="margin-bottom: 3px">
		<tr> 
			<td colspan="2">The information provided will not be collected any used for any other purpose but just to send your message.</td>
		</tr>
		<tr>
			<td align="right">Name of your friend:&nbsp;</td>
			<td><input type="Text" name="nameto" value="" style="width:200px;"></td>
		</tr>
		<tr>
			<td align="right">E-mail of your friend: *</td>
			<td><input type="Text" name="emailto" value="" style="width:200px;"></td>
		</tr>
		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Your Name:&nbsp;</td>
			<td><input type="Text" name="name" value="" style="width:200px;"></td>
		</tr>
		<tr>
			<td align="right">Comments: &nbsp;</td>
			<td><textarea name="comments" rows="6" style="width:200px;"></textarea></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="Submit" name="submit" style="width:98px;" value="Send">&nbsp;<input type="reset" name="reset" style="width:98px;" value="Reset"></td>
		</tr>
	</table>
	</div>
	<input type="Hidden" name="sendto" value="1">
	</form>
END;

/*if( $_P["sendto"] == "1" && IsValidEmail($_POST['emailto']) )
	$run_result .= "<script>location.href = location.href+'#form';</script>";*/

}
?>
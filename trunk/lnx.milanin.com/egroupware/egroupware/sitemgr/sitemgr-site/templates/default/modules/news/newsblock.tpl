<!-- BEGIN ScriptBlock -->
<script>
function ShowUrlForm(archor)
{
	var div = document.getElementById("newsFormUrl");
	if( div != null)
	{
		var isVisible = (div.style.display != 'none');
		div.style.display = isVisible ? 'none' : 'block';
		archor.innerHTML = isVisible ? '{SendToFriendBottom}' : '{HideForm}';
		return false;
	}
	return true;
}
</script>
<!-- END ScriptBlock -->
<!-- BEGIN PagingTopBlock -->
<div align="right" style="padding:8px 0px 8px 0px;">{label}:&nbsp;{content}</div>
<!-- END PagingTopBlock -->
<!-- BEGIN NewsBlock -->
<div class='newscontainer'>
<table width="98%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td height="23" width="432">
		<b><a href="?news_id={news_id}" class="newsTitle">{news_title}</a></b>
	</td>
</tr>
<tr> 
	<td> 
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td colspan="2"> 
						<i>Submitted by '{news_submitter}' {news_date}</i>
				</td>
			</tr>
			<tr>
				<td style="padding:0px 10px 0px 10px;" colspan="2">
							{news_content}
				</td>
			</tr>
			<tr>
				<td style="padding-top:5px;"><a href="?news_id={news_id}&f=1#form" onclick="return ShowUrlForm(this);">{news_link_friend}</a></td>
				<td align="right" style="padding-right:10px;padding-top:5px;"><a href="{news_url}">{news_link_title}</a></td>
			</tr>
		</table>
	</td>
</tr>
</table>
</div>
<br/>
<!-- END NewsBlock -->

<!-- BEGIN formurl -->
	<div align="center"><font color="#FF0000">{message}</font></div>
	<div class='newscontainer' id="newsFormUrl" style="display:{display};">
	<a name="form"></a>
	<table width="98%" border="0" cellspacing="0" cellpadding="0">
	<form name="form" action="{form_url}" method="post">
		<tr>
			<td height="23" width="432" colspan="2"><b>{SendToFriend}</b></td>
		</tr>
		<tr> 
			<td colspan="2">{FormPreText}</td>
		</tr>
		<tr>
			<td align="right">{FormNameTo}&nbsp;</td>
			<td><input type="Text" name="nameto" value="{nameto}" style="width:200px;"></td>
		</tr>
		<tr>
			<td align="right">{FormHisEmail}&nbsp;</td>
			<td><input type="Text" name="emailto" value="{emailto}" style="width:200px;"></td>
		</tr>
		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">{FormYourName}&nbsp;</td>
			<td><input type="Text" name="name" value="{name}" style="width:200px;"></td>
		</tr>
		<tr>
			<td align="right">{FormComments}&nbsp;</td>
			<td><textarea name="comments" rows="6" style="width:200px;">{comments}</textarea></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="Submit" name="submit" style="width:98px;" value="{SubmitButton}">&nbsp;<input type="reset" name="reset" style="width:98px;" value="{CancelButton}"></td>
		</tr>
		</form>
	</table>
	
	</div>
<!-- END formurl -->

<!-- BEGIN PagingBottomBlock -->
<div align="right">{label}:&nbsp;{content}</div>
<!-- END PagingBottomBlock -->
<!-- BEGIN RssBlock -->
<a href="{rsslink}" target="_blank"><img src="images/M_images/rss.png" alt="RSS" /></a>
<!-- END RssBlock -->

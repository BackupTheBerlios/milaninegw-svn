<!-- BEGIN PagingTopBlock -->
<div align="right" style="padding:8px 0px 8px 0px;">{label}:&nbsp;{content}</div>
<!-- END PagingTopBlock -->
<!-- BEGIN NewsBlock -->
<div align="left" class='newscontainer'>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td height="23" width="432">
		<b><a href="?news_id={news_id}" class="newsTitle">{news_title}</a></b>
	</td>
</tr>
<tr> 
	<td > 
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td> 
						<i>Submitted by '{news_submitter}' {news_date}</i>
				</td>
			</tr>
			<tr>
				<td style="padding:0px 10px 0px 10px;">
							{news_content}
				</td>
			</tr>
			<tr>
				<td align="right" style="padding-right:10px;">
							<a href="?news_id={news_id}">{news_link_title}</a>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
</div>
<br/>
<!-- END NewsBlock -->
<!-- BEGIN PagingBottomBlock -->
<div align="right">{label}:&nbsp;{content}</div>
<!-- END PagingBottomBlock -->
<!-- BEGIN RssBlock -->
<a href="{rsslink}" target="_blank"><img src="images/M_images/rss.png" alt="RSS" /></a>
<!-- END RssBlock -->



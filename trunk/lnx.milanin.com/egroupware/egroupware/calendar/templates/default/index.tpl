<!-- $Id: index.tpl,v 1.8 2004/03/14 22:30:50 ak703 Exp $ -->
{printer_friendly}
<table id="calendar_index_table" border="0" width="100%" cols="5">
	<tr>
		<td align="left" valign="top" width="20%">
			{small_calendar_prev}
		</td>
		<td align="center" valign="middle" width="15%">
			<b>{prev_month_link}</b>
		</td>
		<td align="center" width="30%">
			<span class="calendar_month_identifier">
				{month_identifier}
			</span>
				<br />
			<span class="calendar_user_identifier">
				:&nbsp;{username}&nbsp;:
			</span>
		</td>
		<td align="center" valign="middle" width="15%">
			{next_month_link}
		</td>
		<td align="right" valign="top" width="20%">
			{small_calendar_next}
		</td>
	</tr>
</table>
{large_month}
<p>
<p>
<div class="calendar_link_print">
	{print}
</div>

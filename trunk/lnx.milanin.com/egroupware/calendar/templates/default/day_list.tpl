<!-- $Id: day_list.tpl,v 1.2 2003/12/23 05:28:36 shrykedude Exp $ -->
<!-- BEGIN day -->
<div class="event-off">
<table border="0" width="100%" cellpadding="0">
	<tr>
		<td class="event-on">
{row}
		</td>
	</tr>
</table>
</div>
<!-- END day -->
<!-- BEGIN day_row -->
    <font style="font-size: 8pt;">{event}</font>
<!-- END day_row -->
<!-- BEGIN day_event_on -->
	<font class="event-on">{event}</font>
<!-- END day_event_on -->
<!-- BEGIN day_event_off -->
	<font class="event-on">{event}</font>
<!-- END day_event_off -->
<!-- BEGIN day_event_holiday -->
	{event}
<!-- END day_event_holiday -->
<!-- BEGIN day_time -->
	{time}
     <!--<td class="time" nowrap>{open_link}{time}{close_link}</td>-->
<!-- END day_time -->


<!-- $Id: day_cal.tpl,v 1.21 2004/03/12 23:47:03 ak703 Exp $ -->
<!-- BEGIN day -->
<div class="th">
 <table class="calendar_dayview_maintable" width="100%" cellpadding="0">
{row}
</table>
 </div>
<!-- END day -->
<!-- BEGIN day_row -->
    <tr>{time}{event}
    </tr>
<!-- END day_row -->
<!-- BEGIN day_event_on -->
     <td class="event-on"{extras}>&nbsp;{event}</td>
<!-- END day_event_on -->
<!-- BEGIN day_event_off -->
     <td class="event-off"{extras}>&nbsp;{event}</td>
<!-- END day_event_off -->
<!-- BEGIN day_event_holiday -->
     <td class="event-holiday"{extras}>&nbsp;{event}</td>
<!-- END day_event_holiday -->
<!-- BEGIN day_time -->
     <td class="time" nowrap>{open_link}{time}{close_link}</td>
<!-- END day_time -->


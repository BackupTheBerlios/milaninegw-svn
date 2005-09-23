<!-- $Id: day.tpl,v 1.12 2004/03/14 22:30:50 ak703 Exp $ -->
<!-- BEGIN day -->
{printer_friendly}
<table id="calendar_dayview_table" class="calendar_dayview_table" border="0" width="100%">
	<tr>
		<td valign="top" width="70%">
			<table id="calendar_dayview1" border="0" width=100%>
				<tr>
					<td class="calendar_dayview_table_header">
      					{date}&nbsp;<span class="calendar_user_identifier">:&nbsp;{username}&nbsp;:</span>
						<br />
     				</td>
    			</tr>
				{day_events}
   			</table>
   			<p align="center">{print}</p>
  		</td>
 		 <td align="center" valign="top">
  			 <table id="calendar_dayview2" width="100%">
    			<tr>
     				<td align="center">
						{small_calendar}
     				</td>
    			</tr>
    			<tr>
     				<td align="center">
      					<div class="th">
       						<p class="calendar_dayview_todo_header">{lang_todos}</p>
								{todos}
      					</div>
     				</td>
    			</tr>
   			</table>
  		</td>
 	</tr>
</table>
<!-- END day -->

<!-- BEGIN day_event -->
	<tr>
		<td>

			{daily_events}

		</td>
	</tr>
<!-- END day_event -->

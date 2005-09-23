<!-- $Id: link_pict.tpl,v 1.14.4.6 2005/02/09 18:03:49 ralfbecker Exp $    link_pict -->

<!-- BEGIN link_pict -->
{picture}
<!-- END link_pict -->

<!-- BEGIN link_open -->
<div id="calendar_event_entry" {tooltip}>
<a class="event_entry" href="{link_link}" onMouseOver="window.status='{lang_view}'; return true;" onMouseOut="window.status=''; return true;"><br>
<!-- END link_open -->

<!-- BEGIN pict -->
 <img src="{pic_image}" width="{width}" height="{height}" title="{title}" border="0" />
<!-- END pict -->

<!-- BEGIN link_text_old -->
<nobr>&nbsp;{time}&nbsp;</nobr> {title}&nbsp;{users_status}: <i>{desc}</i><!--({location})-->
<!-- END link_text_old -->

<!-- BEGIN link_text -->
<nobr>&nbsp;<span style="color: black">{time}</span> {users_status}</nobr><br><b>{title}</b>
<!-- END link_text -->

<!-- BEGIN link_close -->
</a></div>
<!-- END link_close -->

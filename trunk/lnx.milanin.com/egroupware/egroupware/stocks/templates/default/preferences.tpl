<!-- $Id: preferences.tpl,v 1.9 2003/12/31 13:16:39 milosch Exp $ -->

<p><b>&nbsp;&nbsp;&nbsp;{lang_action}</b><br>
<hr noshade width="98%" align="center" size="1">

<center>
<table width="60%" border="0" cellspacing="2" cellpadding="2">
    <tr bgcolor="{th_bg}">
        <td width="30%">{lang_symbol}</td>
        <td width="40%">{lang_company}</td>
        <td width="10%" align="center">{h_lang_edit}</td>
        <td width="10%" align="center">{h_lang_delete}</td>
    </tr>

<!-- BEGIN stock_prefs -->
    <tr bgcolor="{tr_color}">
        <td>{dsymbol}</td>
        <td>{dname}</td>
        <td align="center"><a href="{edit}">{lang_edit}</a></td>
        <td align="center"><a href="{delete}">{lang_delete}</a></td>
    </tr>
<!-- END stock_prefs -->

</table>
<table width="60%" border="0" cellspacing="2" cellpadding="2">
    <tr bgcolor="{th_bg}">
        <td>{lang_display}</td>
        <td align="center"><a href="{newstatus}">{lang_newstatus}</a></td>
    </tr>
</table>

<!-- BEGIN add -->
<table width="40%" border="0" cellspacing="2" cellpadding="2">
<form method="POST" action="{add_action}">
{hidden_vars}
    <tr bgcolor="{th_bg}">
        <td colspan="2" align="center">{lang_add_stock}</td>
    </tr>
    <tr bgcolor="{tr_color1}">
        <td>{lang_symbol}:</td>
        <td align="center"><input type="text" name="symbol" value="{symbol}"></td>
    </tr>
    <tr bgcolor="{tr_color2}">
        <td>{lang_company}:</td>
        <td align="center"><input type="text" name="name" value="{name}"></td>
    </tr>
    <tr bgcolor="{tr_color1}" valign="bottom">
        <td colspan="2" align="center"><input type="submit" name="submit" value="{lang_add}"></form></td>
    </tr>
    <form method="POST" action="{done_action}">
    <tr>
        <td colspan="2" align="center"><input type="submit" name="submit" value="{lang_done}"></form></td>
    </tr>
</table>
</center>
<!-- END add -->

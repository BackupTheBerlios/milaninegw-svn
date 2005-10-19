<!-- BEGIN news_form -->
 {_category}
  <h2 align="center">{cat_name}
    </h2>
	<table align="center" border="0" width="90%" cellspacing="0" cellpadding="0">
   {rows}
    </table>
 <table width="100%"><tr><td align="left">{lesslink}</td><td align="right">{morelink}</td></tr></table>
<!-- END news_form -->

<!-- BEGIN row -->
   <tr bgcolor="#c7c3c7">
    <td width="13" valign="top" valign="top">
     <img src="{icon}" align="top">
    </td>
    <td align="left" width="99%">
     <b>{subject}</b>&nbsp;
    </td>
    <td align="left" width="1%" >
     &nbsp;
    </td>
   </tr>
   <tr>
    <td width="100%" colspan="3" bgcolor="#efefef">
     {submitedby}
     <p>{content}</p>
     
	 <p>{category}</p>
	 
    </td>
   </tr>
   <tr>
   	<td colspan="3" style="border-top:1px solid #c7c3c7;">&nbsp;
    </td>
   </tr>
<!-- END row -->

<!-- BEGIN row_empty -->
  <tr>
   <td align="center">{row_message}</td>
  </tr>
<!-- END row_empty -->


<!-- BEGIN category -->
<form method="POST">
 <select name="inputread" onChange="location.href=this.value">{readable}</select>
 {maintainlink}
</form>
<!-- END category -->

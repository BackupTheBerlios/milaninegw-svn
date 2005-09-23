<?php echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?> 
<?	/**
	 *  created by J.Wilson / mentalbloc
  	 *  http://mentalbloc.da.ru	
	 *
	 *	Mambo Site Server Open Source Edition Version 3.0.7
	 *	Dynamic portal server and Content managment engine
	 *	04-05-2001
 	 *
	 *	Copyright (C) 2000 - 2001 Miro Contruct Pty Ltd
	 *	Distributed under the terms of the GNU General Public License
	 *	This software may be used without warrany provided these statements are left 
	 *	intact and a "Powered By Mambo" appears at the bottom of each HTML page.
	 *	This code is Available at http://sourceforge.net/projects/mambo
	 *
	 *	Site Name: Mambo Site Server Open Source Edition Version 3.0.7
	 *	File Name: index.php
	 *	Developers: Danny Younes - danny@miro.com.au
	 *				Nicole Anderson - nicole@miro.com.au
	 *	Date: 17/07/2001
	 * 	Version #: 3.0.7
	 *	Comments:
	 *  Ported to Mambo 4.5.9.1 and above by:
	 *		Lawrence Meckan
	 *		Absalom Media
	 *		http://www.absalom.biz
	**/
?>
<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $mosConfig_sitename; ?></title>
<?php echo _ISO; ?>
<?php include ("includes/metadata.php"); ?>
<?php include ("editor/editor.php"); ?>
<script language="JavaScript" type="text/javascript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
<link href="templates/mentalbloc/css/mbloctec.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="templates/mentalbloc/images/favicon.ico" />

<?php initEditor(); ?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<script language="JavaScript" type="text/JavaScript">
<!--

function swapImgRestore() { 
  var i,x,a=document.sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function preloadImages() { 
  var d=document; if(d.images){ if(!d.p) d.p=new Array();
    var i,j=d.p.length,a=preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.p[j]=new Image; d.p[j++].src=a[i];}}
}

function findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function swapImage() { 
  var i,j=0,x,a=swapImage.arguments; document.sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=findObj(a[i]))!=null){document.sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_nbGroup(event, grpName) { //v3.0
  var i,img,nbArr,args=MM_nbGroup.arguments;
  if (event == "init" && args.length > 2) {
    if ((img = MM_findObj(args[2])) != null && !img.MM_init) {
      img.MM_init = true; img.MM_up = args[3]; img.MM_dn = img.src;
      if ((nbArr = document[grpName]) == null) nbArr = document[grpName] = new Array();
      nbArr[nbArr.length] = img;
      for (i=4; i < args.length-1; i+=2) if ((img = MM_findObj(args[i])) != null) {
        if (!img.MM_up) img.MM_up = img.src;
        img.src = img.MM_dn = args[i+1];
        nbArr[nbArr.length] = img;
    } }
  } else if (event == "over") {
    document.MM_nbOver = nbArr = new Array();
    for (i=1; i < args.length-1; i+=3) if ((img = MM_findObj(args[i])) != null) {
      if (!img.MM_up) img.MM_up = img.src;
      img.src = (img.MM_dn && args[i+2]) ? args[i+2] : args[i+1];
      nbArr[nbArr.length] = img;
    }
  } else if (event == "out" ) {
    for (i=0; i < document.MM_nbOver.length; i++) {
      img = document.MM_nbOver[i]; img.src = (img.MM_dn) ? img.MM_dn : img.MM_up; }
  } else if (event == "down") {
    if ((nbArr = document[grpName]) != null)
      for (i=0; i < nbArr.length; i++) { img=nbArr[i]; img.src = img.MM_up; img.MM_dn = 0; }
    document[grpName] = nbArr = new Array();
    for (i=2; i < args.length-1; i+=2) if ((img = MM_findObj(args[i])) != null) {
      if (!img.MM_up) img.MM_up = img.src;
      img.src = img.MM_dn = args[i+1];
      nbArr[nbArr.length] = img;
  } }
}
//-->
</script>
</HEAD>
<BODY leftMargin=0 topMargin=0 onLoad="MM_preloadImages('templates/mentalbloc/images/homeButton-over.gif','templates/mentalbloc/images/linksButton-over.gif')">
<table border="0" cellpadding="0" cellspacing="0" width="778">
  <tbody>
  <tr>
      <td><img height="84" src="templates/mentalbloc/images/headerTopLeft.gif" width="73" /></td>
      <td><img height="84" src="templates/mentalbloc/images/headerTopCenter.gif" width="288" /></td>
    <td background="templates/mentalbloc/images/headerTopRight.gif" width="417"><?php mosLoadComponent( "banners" ); ?></td></tr></tbody></table>
<table border="0" cellpadding="0" cellspacing="0" width="778">
  <tbody> 
  <tr> 
      <td height="10" width="74" ><img height="24" src="templates/mentalbloc/images/menubarLeftBorder.gif" width="74" /></td>
    <td width="117" align="center"><a href="index.php">Home</a></td>
    <td width="117" align="center"><a href="index.php?option=news&Itemid=2">News</a></td>
    <td width="117" align="center"><a href="index.php?option=faq&Itemid=5">FAQ'S</a></td>
    <td width="117" align="center"><a href="index.php?option=articles&Itemid=3">Articles</a></td>
    <td width="117" align="center"><a href="index.php?option=contact&Itemid=6">Contact</a></td>
    <td width="119" align="center"><a href="index.php?option=weblinks&Itemid=4p">Links</a></td>
  </tr>
  </tbody> 
</table>
<table border="0" cellpadding="0" cellspacing="0" width="778">
  <tbody>
  <tr>
      <td><img height="31" src="templates/mentalbloc/images/searchBarLeftBorder.gif" 
    width="74" /></td>
      <td><img height="31" src="templates/mentalbloc/images/searchBarLeft.gif" width="3" /></td>
    <td align="right" background="templates/mentalbloc/images/searchBarFill.gif" nowrap="nowrap" 
    valign="center" width="100%">
      <table border="0">
        <tbody>
        <tr>
          <td align="left" nowrap="nowrap" valign="center">&nbsp;&nbsp;</td>
          
          <td align="left" valign="center"></td>
          <td align="left" valign="center" width="100%">&nbsp;</td>
          <form action='index.php' method='post'>
          <td align="right" class="pn-normal" valign="center">&nbsp;Search&nbsp;</td>
          <td align="right" valign="center">
		  
		 <input class="inputbox" type="text" name="searchword" size="15" value="<?php echo _SEARCH_BOX; ?>"  onblur="if(this.value=='') this.value='<?php echo _SEARCH_BOX; ?>';" onfocus="if(this.value=='<?php echo _SEARCH_BOX; ?>') this.value='';" />
          </td>
          <td align="right" valign="center"> <input type="hidden" name="option" value="search" /> </td></form></tr></tbody></table></td>
      <td><img height="31" src="templates/mentalbloc/images/searchBar.gif" 
  width="2" /></td>
    </tr></tbody></table>
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="778">
  <tbody>
  <tr>
      <td align="left" background="templates/mentalbloc/images/borderLeftFill.gif" 
      valign="top"><img height="466" src="templates/mentalbloc/images/pageBorderLeft.gif" 
      width="74" /></td>
    <td valign="top">
      <table cellpadding="0" cellspacing="0" border="0">
        <tbody> 
        <tr>
          <td valign="top" height="164"> 
            <table cellpadding="0" cellspacing="0" border="0">
              <tbody> 
              <tr>
                <td valign="top">
                  <table border="0" cellpadding="0" cellspacing="0" width="140">
                    <tbody>
                    <tr>
                      <td>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_tl.png" width="10" /></td>
                            <td background="templates/mentalbloc/images/bloc_t_tfill.png" 
                            height="6" width="100%"></td>
                                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_tr.png" 
                          width="10" /></td>
                                    </tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                                      <td><img height="18" 
                              src="templates/mentalbloc/images/bloc_t_l.png" width="10" /></td>
                                      <td align="right" 
                            background="templates/mentalbloc/images/bloc_t_fill.png" height="18" 
                            nowrap="nowrap" valign="center" width="100%"><span 
                              class="pn-sub"><b>Menu<img alt="" border="0" 
                              src="templates/mentalbloc/images/upb.gif" /></b></span></td>
                                      <td><img height="18" 
                              src="templates/mentalbloc/images/bloc_t_r.png" 
                          width="10" /></td>
                                    </tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_bl.png" width="10" /></td>
                            <td background="templates/mentalbloc/images/bloc_t_b_fill.png" 
                            height="6" width="100%"></td>
                                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_br.png" 
                          width="10" /></td>
                                    </tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                            <td background="templates/mentalbloc/images/bloc_b_l_fill.png" 
                            width="10"></td>
                            <td><? include("mainmenu.php");?></td>
                            <td background="templates/mentalbloc/images/bloc_b_r_fill.png" 
                            width="10"></td></tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                                      <td><img height="13" 
                              src="templates/mentalbloc/images/bloc_b_l.png" width="10" /></td>
                            <td background="templates/mentalbloc/images/bloc_b_fill.png" 
                            height="13" width="100%"></td>
                                      <td><img height="13" 
                              src="templates/mentalbloc/images/bloc_b_r.png" 
                          width="10" /></td>
                                    </tr></tbody></table></td></tr></tbody></table>
                  <table border="0" cellpadding="0" cellspacing="0" width="140">
                    <tbody>
                    <tr>
                      <td>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_tl.png" width="10" /></td>
                            <td background="templates/mentalbloc/images/bloc_t_tfill.png" 
                            height="6" width="100%"></td>
                                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_tr.png" 
                          width="10" /></td>
                                    </tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                                      <td><img height="18" 
                              src="templates/mentalbloc/images/bloc_t_l.png" width="10" /></td>
                                      <td align="right" 
                            background="templates/mentalbloc/images/bloc_t_fill.png" height="18" 
                            nowrap="nowrap" valign="center" width="100%"><span 
                              class="pn-sub"><b>Left<img alt="" border="0" 
                              src="templates/mentalbloc/images/upb.gif" /></b></span></td>
                                      <td><img height="18" 
                              src="templates/mentalbloc/images/bloc_t_r.png" 
                          width="10" /></td>
                                    </tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_bl.png" width="10" /></td>
                            <td background="templates/mentalbloc/images/bloc_t_b_fill.png" 
                            height="6" width="100%"></td>
                                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_br.png" 
                          width="10" /></td>
                                    </tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                            <td background="templates/mentalbloc/images/bloc_b_l_fill.png" 
                            width="10"></td>
                            <td><?php mosLoadModules ( 'left' ); ?></td>
                            <td background="templates/mentalbloc/images/bloc_b_r_fill.png" 
                            width="10"></td></tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                                      <td><img height="13" 
                              src="templates/mentalbloc/images/bloc_b_l.png" width="10" /></td>
                            <td background="templates/mentalbloc/images/bloc_b_fill.png" 
                            height="13" width="100%"></td>
                                      <td><img height="13" 
                              src="templates/mentalbloc/images/bloc_b_r.png" 
                          width="10" /></td>
                                    </tr></tbody></table></td></tr></tbody></table>
                  
                </td>
                <td valign="top" width="500">
                  
                              <table border="0" width="100%">
                                <tbody>
                                <tr align="left" valign="center">
                                <td><? 
	  	switch ($option){
			case "news":
				include("news.php");
				break;
			case "articles":
				include("articles.php");
				break;
			case "weblinks":
				include("weblinks.php");
				break;
			case "faq":
				include("faq.php");
				break;
			case "surveyresult":
				
				include("pollBooth.php");
				break;
			case "search":
				include("search.php");
				break;
			case "contact":
				include("contact.php");
				break;
			case "user":
				//if ($uid==""){
					//$uid=$usercookie;
				//}
				include("userpage.php");
				break;
			case "displaypage":
				include("displaypage.php");
				break;
			case "registration":
				include("registration.php");
				break;
			case "archiveNews":
				include("pastarticles.php");
				break;
			default:
				$Itemid=1;
				include("mainbody.php");
				break;
			}
	   ?>
                                
                                  </td></tr></tbody></table></td></tr></tbody></table>
                        
          </td>
                    </tr></tbody></table>
                 
                </td>
                <td align="middle" valign="top" width="140">
                  <table border="0" cellpadding="0" cellspacing="0" width="140">
                    <tbody>
                    <tr>
                      <td>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                            
                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_tl.png" width="10" /></td>
                            <td background="templates/mentalbloc/images/bloc_t_tfill.png" 
                            height="6" width="100%"></td>
                            
                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_tr.png" 
                          width="10" /></td>
                    </tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                            
                      <td><img height="18" 
                              src="templates/mentalbloc/images/bloc_t_l.png" width="10" /></td>
                            
                      <td align="right" 
                            background="templates/mentalbloc/images/bloc_t_fill.png" height="18" 
                            nowrap="nowrap" valign="center" width="100%"><span 
                              class="pn-sub"><b>Right<img alt="" border="0" 
                              src="templates/mentalbloc/images/upb.gif" /></b></span></td>
                            
                      <td><img height="18" 
                              src="templates/mentalbloc/images/bloc_t_r.png" 
                          width="10" /></td>
                    </tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                            
                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_bl.png" width="10" /></td>
                            <td background="templates/mentalbloc/images/bloc_t_b_fill.png" 
                            height="6" width="100%"></td>
                            
                      <td><img height="6" 
                              src="templates/mentalbloc/images/bloc_t_br.png" 
                          width="10" /></td>
                    </tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                            <td background="templates/mentalbloc/images/bloc_b_l_fill.png" 
                            width="10"></td>
                            <td><?php mosLoadModules ( 'right' ); ?>
                             </td>
                            <td background="templates/mentalbloc/images/bloc_b_r_fill.png" 
                            width="10"></td></tr></tbody></table>
                        <table border="0" cellpadding="0" cellspacing="0" 
width="100%">
                          <tbody>
                          <tr>
                            
                      <td><img height="13" 
                              src="templates/mentalbloc/images/bloc_b_l.png" width="10" /></td>
                            <td background="templates/mentalbloc/images/bloc_b_fill.png" 
                            height="13" width="100%"></td>
                            
                      <td><img height="13" 
                              src="templates/mentalbloc/images/bloc_b_r.png" 
                          width="10" /></td>
                    </tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>
         
      <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" 
        width="100%"><tbody>
        <tr>
          <td></td></tr></tbody></table>
      
   </body></html>
<?include ("configuration.php");?>
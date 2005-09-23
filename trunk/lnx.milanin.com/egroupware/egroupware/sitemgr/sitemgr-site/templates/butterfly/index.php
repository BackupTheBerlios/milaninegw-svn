<?php echo "<?xml version=\"1.0\"?>"; ?>
<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $mosConfig_sitename; ?></title>
<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
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
<link href="templates/butterfly/css/template_css.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="images/favicon.ico" />
<?php initEditor(); ?>
</head>
<body bgcolor="#ffffff"> 
<div id="Layer3" style="position:absolute; width:174px; height:33px; z-index:2; left: 476px; top: 22px;">
  <?php mosLoadModules ( 'user1' ); ?>
</div>
<div id="Layer1" style="position:absolute; left:625px; top:4px; width:300px; height:21px; z-index:1">
  <div align="left"><span class="small"><img src="templates/butterfly/images/time.gif" width="13" height="12" align="absmiddle" /> <?php echo (strftime (_DATE_FORMAT_LC)); ?></span></div>
</div>
<div id="Layer4" style="position:absolute; width:400px; height:9px; z-index:3; left: 13px; top: 5px;"><span class="pathway">
  <img src="templates/butterfly/images/home.gif" width="15" height="14" align="absmiddle" /> <?php include "pathway.php";?>
</span></div>
<table  cellpadding="0" cellspacing="0" width="100%"> 
  <tr> 
    <td width="100%" background="templates/butterfly/images/index_1_bg.gif"><img name="index_1" src="templates/butterfly/images/index_1.gif" width="800" height="155"  alt=""></td> 
  </tr> 
</table> 
<table width="100%" border="0" cellpadding="0" cellspacing="0"> 
  <tr valign="top"> 
    <td valign="top">&nbsp;</td> 
    <td> 
      <table width="150" border="0" cellspacing="0" cellpadding="0"> 
        <tr> 
          <td height="24" class="boxheading"><img src="templates/butterfly/images/index_3.gif" width="175" height="32" /></td>
        </tr>
        <tr>
          <td valign="top" background="templates/butterfly/images/index_15.gif"> <table width="95%" border="0" align="center" cellpadding="3" cellspacing="0">
              <tr>
                <td> <form action='index.php' method='post'>
                  <div align="center">
                    <input class="headadbox" type="text" name="searchword" size="10" value="<?php echo _SEARCH_BOX; ?>"  onblur="if(this.value=='') this.value='<?php echo _SEARCH_BOX; ?>';" onfocus="if(this.value=='<?php echo _SEARCH_BOX; ?>') this.value='';" />
                    <input type="hidden" name="option" value="search" />
                    <img src="templates/butterfly/images/search.gif" width="17" height="17" /><?php mosLoadModules ( 'user1' ); ?> 
                    <?php mosLoadModules ( 'left' ); ?>
                    </div>
                </form>                  </td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td><img src="templates/butterfly/images/index_16.gif" width="175" height="24" /></td> 
        </tr>
      </table>
      <table width="150" border="0" cellspacing="0" cellpadding="0"> 
        <tr>
          <td height="24" class="boxheading"><img src="templates/butterfly/images/index_3.gif" width="175" height="32" /></td>
        </tr> 
        <tr> 
          <td valign="top" background="templates/butterfly/images/index_15.gif"> <table width="95%" border="0" align="center" cellpadding="6" cellspacing="0"> 
              <tr> 
                <td> <?php mosLoadComponent( "newsflash" ); ?> </td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td><img src="templates/butterfly/images/index_16.gif" width="175" height="24" /></td> 
        </tr> 
      </table></td> 
    <td valign="top">&nbsp;</td> 
    <td width="100%" valign="top"><table width="90%" border="0" align="center" cellpadding="0" cellspacing="0"> 
        <tr> 
          <td> <img src="templates/butterfly/images/index_5.gif" width="20" height="24" alt="" /></td>
          <td width="100%" background="templates/butterfly/images/index_6.gif">&nbsp; </td>
          <td> <img src="templates/butterfly/images/index_7.gif" width="20" height="24" alt="" /></td>
        </tr>
        <tr> 
          <td height="100%" background="templates/butterfly/images/index_12.gif">&nbsp; </td>
          <td width="100%" align="left"><?php mosLoadModules ( 'user2' ); ?>
          <?php include ("mainbody.php"); ?></td> 
          <td height="100%" background="templates/butterfly/images/index_14.gif">&nbsp; </td> 
        </tr>
        <tr> 
          <td> <img src="templates/butterfly/images/index_17.gif" width="20" height="20" alt="" /></td> 
          <td width="100%" background="templates/butterfly/images/index_18.gif">&nbsp; </td> 
          <td> <img src="templates/butterfly/images/index_19.gif" alt="" width="20" height="20" /></td> 
        </tr> 
      </table> 
      <br /> 
    </td> 
    <td width="100%" valign="top">&nbsp;</td> 
    <td width="163" align="right" valign="top"> </td> 
    <td colspan="2"  valign="top"> <table border="0" cellpadding="0" cellspacing="0" " width="100%"> 
        <tr> 
          <?php if (mosCountModules( "right" )) { ?> 
          <td width="150" valign="top"> <!-- //########## Right boxMdules ##########// --> 
            <table width="150" border="0" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td height="24" class="boxheading"><img src="templates/butterfly/images/index_3.gif" width="175" height="32" /></td>
              </tr> 
              <tr> 
                <td valign="top" background="templates/butterfly/images/index_15.gif"> <table width="95%" border="0" align="center" cellpadding="3" cellspacing="0"> 
                    <tr> 
                      <td> <?php $side = "right"; mosLoadModules ( 'right' );  ?> </td> 
                    </tr> 
                  </table></td> 
              </tr> 
              <tr> 
                <td><img src="templates/butterfly/images/index_16.gif" width="175" height="24" /></td> 
              </tr> 
            </table> 
            <?php } ?> </td> 
        </tr> 
      </table></td> 
  </tr> 
</table> 
<table width="100%" cellpadding="0" cellspacing="0"> 
  <tr> 
    <td width="100%" background="templates/butterfly/images/index_27_bg.gif"><img src="templates/butterfly/images/index_27.gif"  width="800" height="112" /></td> 
  </tr> 
</table> 
</body>
</html>

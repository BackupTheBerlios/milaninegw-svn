<?php echo "<?xml version=\"1.0\"?".">"; ?>
<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
?>
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
<link href="templates/bluedream/css/template_css.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="images/favicon.ico" />

<?php initEditor(); ?>

</head>
<BODY BGCOLOR=#FFFFFF LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0>
<CENTER><!-- ImageReady Slices (bluedream.psd) -->
<TABLE WIDTH=770 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD COLSPAN=10>
			<IMG SRC="templates/bluedream/images/bluedream_01.gif" WIDTH=770 HEIGHT=15 ALT=""></TD>
	</TR>
	<TR>
		<TD COLSPAN=2>
			<IMG SRC="templates/bluedream/images/bluedream_02.gif" WIDTH=75 HEIGHT=38 ALT=""></TD>
		<TD COLSPAN=8 background="templates/bluedream/images/bluedream_03.gif">
			<font face="Arial Black" size="4" color="#FFFFFF"><?php echo $mosConfig_sitename; ?></font></TD>
	</TR>
	<TR>
		<TD COLSPAN=10>
			<IMG SRC="templates/bluedream/images/bluedream_04.gif" WIDTH=770 HEIGHT=76 ALT=""></TD>
	</TR>
	<TR>
		<TD background="templates/bluedream/images/bluedream_05.gif">&nbsp;
	  </TD>
		<TD COLSPAN=3 valign="top" background="templates/bluedream/images/bluedream_06.gif"><table width="96%" border="0">
		  <tr>
		    <td><?php mosLoadModules ( 'left' ); ?>
		    </td>
	      </tr>
		  <tr>
		    <td><?php mosLoadComponent( "newsflash" ); ?>
		    </td>
	      </tr>
		  </table>
			</TD>
		<TD background="templates/bluedream/images/bluedream_07.gif">&nbsp;
	  </TD>
		<TD COLSPAN=4 valign="top" background="templates/bluedream/images/bluedream_08.gif"><table width="99%" border="0">
		  <tr>
		    <td valign="top"><span class="small"><?php echo (strftime (_DATE_FORMAT_LC)); ?></span>
			<BR><BR><?php include "pathway.php";?><br />
	        </td>
	      </tr>
		  <tr>
		    <td valign="top"><?php include_once("mainbody.php"); ?>
		    </td>
	      </tr>
	    </table></TD>
		<TD background="templates/bluedream/images/bluedream_09.gif">&nbsp;
	  </TD>
	</TR>
	<TR>
		<TD COLSPAN=10>
			<IMG SRC="templates/bluedream/images/bluedream_10.gif" WIDTH=770 HEIGHT=32 ALT=""></TD>
	</TR>
	<TR>
		<TD COLSPAN=3>
			<IMG SRC="templates/bluedream/images/bluedream_11.gif" WIDTH=174 HEIGHT=43 ALT=""></TD>
		<TD COLSPAN=3 valign="middle" background="templates/bluedream/images/bluedream_12.gif"><form action='index.php' method='post'>
                       
          <div align="left">
            <input class="inputbox" type="text" name="searchword" size="15" value="<?php echo _SEARCH_BOX; ?>"  onblur="if(this.value=='') this.value='<?php echo _SEARCH_BOX; ?>';" onfocus="if(this.value=='<?php echo _SEARCH_BOX; ?>') this.value='';" />
            <input type="hidden" name="option" value="search" />
              </div>
		</form>
	  </TD>
		<TD>
			<IMG SRC="templates/bluedream/images/bluedream_13.gif" WIDTH=49 HEIGHT=43 ALT=""></TD>
		<TD>
			<a href="#top">
			<IMG SRC="templates/bluedream/images/bluedream_14.gif" WIDTH=39 HEIGHT=43 border="0"></a></TD>
		<TD COLSPAN=2>
			<IMG SRC="templates/bluedream/images/bluedream_15.gif" WIDTH=335 HEIGHT=43 ALT=""></TD>
	</TR>
	<TR>
		<TD COLSPAN=10>
			<IMG SRC="templates/bluedream/images/bluedream_16.gif" WIDTH=770 HEIGHT=22 ALT=""></TD>
	</TR>
	<TR>
		<TD>
			<IMG SRC="templates/bluedream/images/spacer.gif" WIDTH=21 HEIGHT=1 ALT=""></TD>
		<TD>
			<IMG SRC="templates/bluedream/images/spacer.gif" WIDTH=54 HEIGHT=1 ALT=""></TD>
		<TD>
			<IMG SRC="templates/bluedream/images/spacer.gif" WIDTH=99 HEIGHT=1 ALT=""></TD>
		<TD>
			<IMG SRC="templates/bluedream/images/spacer.gif" WIDTH=36 HEIGHT=1 ALT=""></TD>
		<TD>
			<IMG SRC="templates/bluedream/images/spacer.gif" WIDTH=27 HEIGHT=1 ALT=""></TD>
		<TD>
			<IMG SRC="templates/bluedream/images/spacer.gif" WIDTH=110 HEIGHT=1 ALT=""></TD>
		<TD>
			<IMG SRC="templates/bluedream/images/spacer.gif" WIDTH=49 HEIGHT=1 ALT=""></TD>
		<TD>
			<IMG SRC="templates/bluedream/images/spacer.gif" WIDTH=39 HEIGHT=1 ALT=""></TD>
		<TD>
			<IMG SRC="templates/bluedream/images/spacer.gif" WIDTH=300 HEIGHT=1 ALT=""></TD>
		<TD>
			<IMG SRC="templates/bluedream/images/spacer.gif" WIDTH=35 HEIGHT=1 ALT=""></TD>
	</TR>
</TABLE>
<!-- End ImageReady Slices -->
<div align="center"> 
                <?php mosLoadComponent( "banners" ); ?>
                <p><?php include_once("includes/footer.php"); ?></p>
</div>
</CENTER>
</BODY>
</HTML>
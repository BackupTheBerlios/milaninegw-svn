<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: help_index.php.t,v 1.1.1.1 2003/10/17 21:11:28 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}

	$section = isset($_GET['section']) ? $_GET['section'] : '';
	switch ($section) {
		case 'usermaintance':
		case 'boardusage':
		case 'readingposting':
			$file = '/web/htdocs/www.milanin.com/home/egroupware/fudforum/3814588639/theme/default/help/' . $section . '.hlp';
			$return_top = '<div align="center" class="GenText">[ <a href="/egroupware/fudforum/3814588639/index.php?t=help_index&amp;'._rsid.'" class="GenLink">Return to Help Index</a> ]</div>';
			break;
		default:
			$file = '/web/htdocs/www.milanin.com/home/egroupware/fudforum/3814588639/theme/default/help/faq_index.hlp';
			$return_top = '';
	}

	ses_update_status($usr->sid, 'Reading the <a href="/egroupware/fudforum/3814588639/index.php?t=help_index" class="GenLink">F.A.Q.</a>');
	$TITLE_EXTRA = ': F.A.Q.';



	$str = file_get_contents($file);

	$tt_len = strlen('TOPIC_TITLE:');
	$th_len = strlen('TOPIC_HELP:');
	$help_section_data = '';
	while (($str = strstr($str, 'TOPIC_TITLE:')) !== false) {
		$end_of = strpos($str, "\n");
		$topic_title = substr($str, $tt_len, $end_of-$tt_len);
		$str = strstr($str, 'TOPIC_HELP:');
		$str = substr($str, $th_len);
		$end_of_str = strstr($str, 'TOPIC_TITLE:');
		$topic_help = substr($str, 0, strlen($str)-strlen($end_of_str));
		$str = $end_of_str;
		$rs = _rsid;
		$topic_help = str_replace('%_rsid%', $rs, $topic_help);

		$help_section_data .= '<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th>'.$topic_title.' <a href="#top" class="thLnk">back to top</a></th></tr>
<tr><td class="ForumBackground">
	<table cellspacing=2 cellpadding=2 width="100%" class="dashed"><tr><td class="GenText">
	'.$topic_help.'
	</td></tr></table>
</td></tr>
</table>
<br />';
	}

?>
<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<a name="top"></a>
<?php echo $return_top; ?>
<?php echo $help_section_data; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>
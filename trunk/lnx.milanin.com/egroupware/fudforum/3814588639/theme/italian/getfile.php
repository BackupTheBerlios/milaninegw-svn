<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: getfile.php.t,v 1.3 2004/03/06 20:26:44 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function safe_attachment_copy($source, $id, $ext)
{
	$loc = $GLOBALS['FILE_STORE'] . $id . '.atch';
	if (!$ext && !move_uploaded_file($source, $loc)) {
		std_out('unable to move uploaded file', 'ERR');
	} else if ($ext && !copy($source, $loc)) {
		std_out('unable to handle file attachment', 'ERR');
	}
	@unlink($source);

	@chmod($loc, ($GLOBALS['FUD_OPT_2'] & 8388608 ? 0600 : 0666));

	return $loc;
}

function attach_add($at, $owner, $attach_opt=0, $ext=0)
{
	$mime_type = (int) q_singleval("SELECT id FROM phpgw_fud_mime WHERE fl_ext='".addslashes(substr(strrchr($at['name'], '.'), 1))."'");

	$id = db_qid("INSERT INTO phpgw_fud_attach (location,message_id,original_name,owner,attach_opt,mime_type,fsize) VALUES('',0,'".addslashes($at['name'])."', ".$owner.", ".$attach_opt.", ".$mime_type.", ".$at['size'].")");

	safe_attachment_copy($at['tmp_name'], $id, $ext);

	return $id;
}

function attach_finalize($attach_list, $mid, $attach_opt=0)
{
	$id_list = '';
	$attach_count = 0;

	$tbl = !$attach_opt ? 'msg' : 'pmsg';

	foreach ($attach_list as $key => $val) {
		if (empty($val)) {
			$del[] = (int)$key;
			@unlink($GLOBALS['FILE_STORE'].(int)$key.'.atch');
		} else {
			$attach_count++;
			$id_list .= (int)$key.',';
		}
	}

	if (!empty($id_list)) {
		$id_list = substr($id_list, 0, -1);
		$cc = __FUD_SQL_CONCAT__.'('.__FUD_SQL_CONCAT__."('".$GLOBALS['FILE_STORE']."', id), '.atch')";
		q("UPDATE phpgw_fud_attach SET location=".$cc.", message_id=".$mid." WHERE id IN(".$id_list.") AND attach_opt=".$attach_opt);
		$id_list = ' AND id NOT IN('.$id_list.')';
	} else {
		$id_list = '';
	}

	/* delete any temp attachments created during message creation */
	if (isset($del)) {
		q('DELETE FROM phpgw_fud_attach WHERE id IN('.implode(',', $del).') AND message_id='.$mid.' AND attach_opt='.$attach_opt);
	}

	/* delete any prior (removed) attachments if there are any */
	q("DELETE FROM phpgw_fud_attach WHERE message_id=".$mid." AND attach_opt=".$attach_opt.$id_list);

	if (!$attach_opt && ($atl = attach_rebuild_cache($mid))) {
		q('UPDATE phpgw_fud_msg SET attach_cnt='.$attach_count.', attach_cache=\''.addslashes(@serialize($atl)).'\' WHERE id='.$mid);
	}
}

function attach_rebuild_cache($id)
{
	$c = uq('SELECT a.id, a.original_name, a.fsize, a.dlcount, CASE WHEN m.icon IS NULL THEN \'unknown.gif\' ELSE m.icon END FROM phpgw_fud_attach a LEFT JOIN phpgw_fud_mime m ON a.mime_type=m.id WHERE message_id='.$id.' AND attach_opt=0');
	while ($r = db_rowarr($c)) {
		$ret[] = $r;
	}

	return (isset($ret) ? $ret : null);
}

function attach_inc_dl_count($id, $mid)
{
	q('UPDATE phpgw_fud_attach SET dlcount=dlcount+1 WHERE id='.$id);
	if (($a = attach_rebuild_cache($mid))) {
		q('UPDATE phpgw_fud_msg SET attach_cache=\''.addslashes(@serialize($a)).'\' WHERE id='.$mid);
	}
}function reverse_fmt(&$data)
{
	$data = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $data);
}


	if (!isset($_GET['id']) || !($id = (int)$_GET['id'])) {
		invl_inp_err();
	}
	if (!isset($_GET['private'])) { /* non-private upload */
		$r = db_saq('SELECT mm.mime_hdr, a.original_name, a.location, m.id, mod.id,
			('.(_uid ? '(CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END)' : 'g1.group_cache_opt').' & 2) > 0
			FROM phpgw_fud_attach a
			INNER JOIN phpgw_fud_msg m ON a.message_id=m.id AND a.attach_opt=0
			INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
			INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id='.(_uid ? 2147483647 : 0).' AND g1.resource_id=t.forum_id
			LEFT JOIN phpgw_fud_mod mod ON mod.forum_id=t.forum_id AND mod.user_id='._uid.'
			LEFT JOIN phpgw_fud_mime mm ON mm.id=a.mime_type
			'.(_uid ? 'LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=t.forum_id' : '').'
			WHERE a.id='.$id);
		if (!$r) {
			invl_inp_err();
		}
		if (!($usr->users_opt & 1048576) && !$r[4] && !$r[5]) {
			std_error('access');
		}
	} else {
		$r = db_saq('SELECT mm.mime_hdr, a.original_name, a.location, pm.id, a.owner
			FROM phpgw_fud_attach a
			INNER JOIN phpgw_fud_pmsg pm ON a.message_id=pm.id AND a.attach_opt=1
			LEFT JOIN phpgw_fud_mime mm ON mm.id=a.mime_type
			WHERE a.attach_opt=1 AND a.id='.$id);
		if (!$r) {
			invl_inp_err();
		}
		if (!($usr->users_opt & 1048576) && $r[4] != _uid) {
			std_error('access');
		}
	}

	if ($FUD_OPT_2 & 4194304 && !empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $WWW_ROOT) === false) {
		header("HTTP/1.0 404 Not Found");
		exit;
	}

	reverse_fmt($r[1]);
	if (!$r[0]) {
		$r[0] = 'application/ocet-stream';
		$append = 'attachment; ';
	} else if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') && preg_match('!^(audio|video|image)/!i', $r[0])) {
		$append = 'inline; ';
	} else if (strncmp($r[0], "image/", 6)) {
		$append = 'attachment; ';
	} else {
		$append = '';
	}

	/* if we encounter a compressed file and PHP's output compression is enabled do not
	 * try to compress images & already compressed files */
	if ($FUD_OPT_2 & 16384 && $append) {
		$comp_ext = array('zip', 'gz', 'rar', 'tgz', 'bz2', 'tar');
		$ext = strtolower(substr(strrchr($r[1], '.'), 1));
		if (!in_array($ext, $comp_ext)) {
			ob_start(array('ob_gzhandler', (int)$PHP_COMPRESSION_LEVEL));
		}
	}

	/* this is a hack for IE browsers when working on HTTPs,
	 * the no-cache headers appear to cause problems as indicated by the following
	 * MS advisories:
	 *      http://support.microsoft.com/?kbid=812935
	 *      http://support.microsoft.com/default.aspx?scid=kb;en-us;316431
	 */

	if ($_SERVER["SERVER_PORT"] == "443" && (strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') !== false)) {
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0", 1);
	        header("Pragma: public", 1);
	}

	header('Content-type: '.$r[0]);
	header('Content-Disposition: '.$append.'filename="'.$r[1].'"');

	if (!$r[2]) {
		$r[2] = $GLOBALS['FILE_STORE'] . $id . '.atch';
	}

	attach_inc_dl_count($id, $r[3]);
	@fpassthru(fopen($r[2], 'rb'));
?>
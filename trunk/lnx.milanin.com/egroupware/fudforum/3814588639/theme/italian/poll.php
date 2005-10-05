<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: poll.php.t,v 1.1.1.1 2003/10/17 21:11:29 ralfbecker Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	define('plain_form', 1);

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function poll_delete($id)
{
	if (!$id) {
		return;
	}

	q('UPDATE phpgw_fud_msg SET poll_id=0 WHERE poll_id='.$id);
	q('DELETE FROM phpgw_fud_poll_opt WHERE poll_id='.$id);
	q('DELETE FROM phpgw_fud_poll_opt_track WHERE poll_id='.$id);
	q('DELETE FROM phpgw_fud_poll WHERE id='.$id);
}

function poll_fetch_opts($id)
{
	$c = uq('SELECT id,name FROM phpgw_fud_poll_opt WHERE poll_id='.$id);
	while ($r = db_rowarr($c)) {
		$a[$r[0]] = $r[1];
	}

	return (isset($a) ? $a : null);
}

function poll_del_opt($id, $poll_id)
{
	q('DELETE FROM phpgw_fud_poll_opt WHERE poll_id='.$poll_id.' AND id='.$id);
	q('DELETE FROM phpgw_fud_poll_opt_track WHERE poll_id='.$poll_id.' AND poll_opt='.$id);
	$ttl_votes = (int) q_singleval('SELECT SUM(count) FROM phpgw_fud_poll_opt WHERE id='.$id);
	q('UPDATE phpgw_fud_poll SET total_votes='.$ttl_votes.' WHERE id='.$poll_id);
}

function poll_activate($poll_id, $frm_id)
{
	q('UPDATE phpgw_fud_poll SET forum_id='.$frm_id.' WHERE id='.$poll_id);
}

function poll_sync($poll_id, $name, $max_votes, $expiry)
{
	q("UPDATE phpgw_fud_poll SET name='".addslashes(htmlspecialchars($name))."', expiry_date=".intzero($expiry).", max_votes=".intzero($max_votes)." WHERE id=".$poll_id);
}

function poll_add($name, $max_votes, $expiry)
{
	return db_qid("INSERT INTO phpgw_fud_poll (name, owner, creation_date, expiry_date, max_votes) VALUES ('".addslashes(htmlspecialchars($name))."', "._uid.", ".__request_timestamp__.", ".intzero($expiry).", ".intzero($max_votes).")");
}

function poll_opt_sync($id, $name)
{
	q("UPDATE phpgw_fud_poll_opt SET name='".addslashes($name)."' WHERE id=".$id);
}

function poll_opt_add($name, $poll_id)
{
	return db_qid("INSERT INTO phpgw_fud_poll_opt (poll_id,name) VALUES(".$poll_id.", '".addslashes($name)."')");
}

function poll_validate($poll_id, $msg_id)
{
	if (($mid = (int) q_singleval('SELECT id FROM phpgw_fud_msg WHERE poll_id='.$poll_id)) && $mid != $msg_id) {
		return 0;
	} else {
		return $poll_id;
	}
}$GLOBALS['seps'] = array(' '=>' ', "\n"=>"\n", "\r"=>"\r", "'"=>"'", '"'=>'"', '['=>'[', ']'=>']', '('=>'(', ';'=>';', ')'=>')', "\t"=>"\t", '='=>'=', '>'=>'>', '<'=>'<');

function fud_substr_replace($str, $newstr, $pos, $len)
{
        return substr($str, 0, $pos).$newstr.substr($str, $pos+$len);
}

function char_fix(&$str)
{
	$str = str_replace(
		array('&amp;#0', '&amp;#1', '&amp;#2', '&amp;#3', '&amp;#4', '&amp;#5', '&amp;#6', '&amp;#7','&amp;#8','&amp;#9'),
		array('&#0', '&#1', '&#2', '&#3', '&#4', '&#5', '&#6', '&#7', '&#8', '&#9'),
		$str);
}

function tags_to_html($str, $allow_img=1, $no_char=0)
{
	if (!$no_char) {
		$str = htmlspecialchars($str);
	}

	$str = nl2br($str);

	$ostr = '';
	$pos = $old_pos = 0;

	while (($pos = strpos($str, '[', $pos)) !== false) {
		if (isset($GLOBALS['seps'][$str[$pos + 1]])) {
			++$pos;
			continue;
		}

		if (($epos = strpos($str, ']', $pos)) === false) {
			break;
		}
		if (!($epos-$pos-1)) {
			$pos = $epos + 1;
			continue;
		}
		$tag = substr($str, $pos+1, $epos-$pos-1);
		if (($pparms = strpos($tag, '=')) !== false) {
			$parms = substr($tag, $pparms+1);
			if (!$pparms) { /*[= exception */
				$pos = $epos+1;
				continue;
			}
			$tag = substr($tag, 0, $pparms);
		} else {
			$parms = '';
		}

		$tag = strtolower($tag);

		switch ($tag) {
			case 'quote title':
				$tag = 'quote';
				break;
			case 'list type':
				$tag = 'list';
				break;
		}

		if ($tag[0] == '/') {
			if (isset($end_tag[$pos])) {
				if( ($pos-$old_pos) ) $ostr .= substr($str, $old_pos, $pos-$old_pos);
				$ostr .= $end_tag[$pos];
				$pos = $old_pos = $epos+1;
			} else {
				$pos = $epos+1;
			}

			continue;
		}

		$cpos = $epos;
		$ctag = '[/'.$tag.']';
		$ctag_l = strlen($ctag);
		$otag = '['.$tag;
		$otag_l = strlen($otag);
		$rf = 1;
		while (($cpos = strpos($str, '[', $cpos)) !== false) {
			if (isset($end_tag[$cpos]) || isset($GLOBALS['seps'][$str[$cpos + 1]])) {
				++$cpos;
				continue;
			}

			if (($cepos = strpos($str, ']', $cpos)) === false) {
				break 2;
			}

			if (strcasecmp(substr($str, $cpos, $ctag_l), $ctag) == 0) {
				--$rf;
			} else if (strcasecmp(substr($str, $cpos, $otag_l), $otag) == 0) {
				++$rf;
			} else {
				++$cpos;
				continue;
			}

			if (!$rf) {
				break;
			}
			$cpos = $cepos;
		}

		if (!$cpos || ($rf && $str[$cpos] == '<')) { /* left over [ handler */
			++$pos;
			continue;
		}

		if ($cpos !== false) {
			if (($pos-$old_pos)) {
				$ostr .= substr($str, $old_pos, $pos-$old_pos);
			}
			switch ($tag) {
				case 'notag':
					$ostr .= '<span name="notag">'.substr($str, $epos+1, $cpos-1-$epos).'</span>';
					$epos = $cepos;
					break;
				case 'url':
					if (!$parms) {
						$url = substr($str, $epos+1, ($cpos-$epos)-1);
					} else {
						$url = $parms;
					}

					if (!strncasecmp($url, 'www.', 4)) {
						$url = 'http&#58;&#47;&#47;'. $url;
					} else if (strpos(strtolower($url), 'javascript:') !== false) {
						$ostr .= substr($str, $pos, $cepos - $pos + 1);
						$epos = $cepos;
						$str[$cpos] = '<';
						break;
					} else {
						$url = str_replace('://', '&#58;&#47;&#47;', $url);
					}

					$end_tag[$cpos] = '</a>';
					$ostr .= '<a href="'.$url.'" target="_blank">';
					break;
				case 'i':
				case 'u':
				case 'b':
				case 's':
				case 'sub':
				case 'sup':
					$end_tag[$cpos] = '</'.$tag.'>';
					$ostr .= '<'.$tag.'>';
					break;
				case 'email':
					if (!$parms) {
						$parms = str_replace('@', '&#64;', substr($str, $epos+1, ($cpos-$epos)-1));
						$ostr .= '<a href="mailto:'.$parms.'" target="_blank">'.$parms.'</a>';
						$epos = $cepos;
						$str[$cpos] = '<';
					} else {
						$end_tag[$cpos] = '</a>';
						$ostr .= '<a href="mailto:'.str_replace('@', '&#64;', $parms).'" target="_blank">';
					}
					break;
				case 'color':
				case 'size':
				case 'font':
					if ($tag == 'font') {
						$tag = 'face';
					}
					$end_tag[$cpos] = '</font>';
					$ostr .= '<font '.$tag.'="'.$parms.'">';
					break;
				case 'code':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);
					reverse_nl2br($param);

					$ostr .= '<div class="pre"><pre>'.$param.'</pre></div>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'pre':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);
					reverse_nl2br($param);

					$ostr .= '<pre>'.$param.'</pre>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'php':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);
					reverse_nl2br($param);
					reverse_fmt($param);
					$param = trim($param);

					if (strncmp($param, '<?php', 5)) {
						if (strncmp($param, '<?', 2)) {
							$param = "<?php\n" . $param;
						} else {
							$param = "<?php\n" . substr($param, 3);
						}
					}
					if (substr($param, -2) != '?>') {
						$param .= "\n?>";
					}

					$ostr .= '<span name="php">'.trim(@highlight_string($param, true)).'</span>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'img':
					if (!$allow_img) {
						$ostr .= substr($str, $pos, ($cepos-$pos)+1);
					} else {
						if (!$parms) {
							$parms = substr($str, $epos+1, ($cpos-$epos)-1);
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img src="'.$parms.'" border=0 alt="'.$parms.'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						} else {
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img src="'.$parms.'" border=0 alt="'.substr($str, $epos+1, ($cpos-$epos)-1).'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						}
					}
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'quote':
					if (!$parms) {
						$parms = 'Quote:';
					}
					$ostr .= '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>'.$parms.'</b></td></tr><tr><td class="quote"><br>';
					$end_tag[$cpos] = '<br></td></tr></table>';
					break;
				case 'align':
					$end_tag[$cpos] = '</div>';
					$ostr .= '<div align="'.$parms.'">';
					break;
				case 'list':
					$tmp = substr($str, $epos, ($cpos-$epos));
					$tmp_l = strlen($tmp);
					$tmp2 = str_replace(array('[*]', '<br />'), array('<li>', ''), $tmp);
					$tmp2_l = strlen($tmp2);
					$str = str_replace($tmp, $tmp2, $str);

					$diff = $tmp2_l - $tmp_l;
					$cpos += $diff;

					if (isset($end_tag)) {
						foreach($end_tag as $key => $val) {
							if ($key < $epos) {
								continue;
							}

							$end_tag[$key+$diff] = $val;
						}
					}

					switch (strtolower($parms)) {
						case '1':
						case 'a':
							$end_tag[$cpos] = '</ol>';
							$ostr .= '<ol type="'.$parms.'">';
							break;
						case 'square':
						case 'circle':
						case 'disc':
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul type="'.$parms.'">';
							break;
						default:
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul>';
					}
					break;
				case 'spoiler':
					$rnd = get_random_value(64);
					$end_tag[$cpos] = '</div></div>';
					$ostr .= '<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis(\''.$rnd.'\', 1);">Mostra lo spoiler</a><div align="left" id="'.$rnd.'" style="visibility: hidden;">';
					break;
			}

			$str[$pos] = '<';
			$pos = $old_pos = $epos+1;
		} else {
			$pos = $epos+1;
		}
	}
	$ostr .= substr($str, $old_pos, strlen($str)-$old_pos);

	/* url paser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '://', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}
		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i > $ppos) {
			if ($ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if ($ostr[$i]=='<') {
			$pos+=3;
			continue;
		}

		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the span tag
		if (($ts = strpos($ostr, '<span>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</span>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		$us = $pos;
		$l = strlen($ostr);
		while (1) {
			--$us;
			if ($ppos > $us || $us >= $l || isset($GLOBALS['seps'][$ostr[$us]])) {
				break;
			}
		}

		unset($GLOBALS['seps']['=']);
		$ue = $pos;
		while (1) {
			++$ue;
			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}

			if ($ostr[$ue] == '&') {
				if ($ostr[$ue+4] == ';') {
					$ue += 4;
					continue;
				}
				if ($ostr[$ue+3] == ';' || $ostr[$ue+5] == ';') {
					break;
				}
			}

			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}
		}
		$GLOBALS['seps']['='] = '=';

		$url = substr($ostr, $us+1, $ue-$us-1);
		if (!strncasecmp($url, 'javascript', strlen('javascript'))) {
			$pos = $ue;
			continue;
		}
		$html_url = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
		$html_url_l = strlen($html_url);
		$ostr = fud_substr_replace($ostr, $html_url, $us+1, $ue-$us-1);
		$ppos = $pos;
		$pos = $us+$html_url_l;
	}

	/* email parser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '@', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}

		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i>$ppos) {
			if ( $ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if ($ostr[$i]=='<') {
			++$pos;
			continue;
		}


		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<div class="pre"><pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre></div>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		for ($es = ($pos - 1); $es > ($ppos - 1); $es--) {
			if (
				( ord($ostr[$es]) >= ord('A') && ord($ostr[$es]) <= ord('z') ) ||
				( ord($ostr[$es]) >= ord(0) && ord($ostr[$es]) <= ord(9) ) ||
				( $ostr[$es] == '.' || $ostr[$es] == '-' || $ostr[$es] == '\'')
			) { continue; }
			++$es;
			break;
		}
		if ($es == $pos) {
			$ppos = $pos += 1;
			continue;
		}
		if ($es < 0) {
			$es = 0;
		}

		for ($ee = ($pos + 1); @isset($ostr[$ee]); $ee++) {
			if (
				( ord($ostr[$ee]) >= ord('A') && ord($ostr[$ee]) <= ord('z') ) ||
				( ord($ostr[$ee]) >= ord(0) && ord($ostr[$ee]) <= ord(9) ) ||
				( $ostr[$ee] == '.' || $ostr[$ee] == '-' )
			) { continue; }
			break;
		}
		if ($ee == ($pos+1)) {
			$ppos = $pos += 1;
			continue;
		}

		$email = str_replace('@', '&#64;', substr($ostr, $es, $ee-$es));
		$email_url = '<a href="mailto:'.$email.'" target="_blank">'.$email.'</a>';
		$email_url_l = strlen($email_url);
		$ostr = fud_substr_replace($ostr, $email_url, $es, $ee-$es);
		$ppos =	$es+$email_url_l;
		$pos = $ppos;
	}

	return $ostr;
}

if (!function_exists('html_entity_decode')) {
	function html_entity_decode($s)
	{
		return strtr($s, array_flip(get_html_translation_table(HTML_ENTITIES)));
	}
}

function html_to_tags($fudml)
{
	while (preg_match('!<span name="php">(.*?)</span>!is', $fudml, $res)) {
		$tmp = trim(html_entity_decode(strip_tags(str_replace('<br />', "\n", $res[1]))));
		$m = md5($tmp);
		$php[$m] = $tmp;
		$fudml = str_replace($res[0], "[php]\n".$m."\n[/php]", $fudml);
	}

	if (strpos($fudml, '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>')  !== false) {
		$fudml = str_replace(array('<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>','</b></td></tr><tr><td class="quote"><br>','<br></td></tr></table>'), array('[quote title=', ']', '[/quote]'), $fudml);
	}

	if (preg_match('!<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis\(\'.*?\', 1\);">Mostra lo spoiler</a><div align="left" id=".*?" style="visibility: hidden;">!is', $fudml)) {
		$fudml = preg_replace('!\<div class\="dashed" style\="padding: 3px;" align\="center" width\="100%"\>\<a href\="javascript://" OnClick\="javascript: layerVis\(\'.*?\', 1\);">Mostra lo spoiler\</a\>\<div align\="left" id\=".*?" style\="visibility: hidden;"\>!is', '[spoiler]', $fudml);
		$fudml = str_replace('</div></div>', '[/spoiler]', $fudml);
	}

	while (preg_match('!<font (color|face|size)=".+?">.*?</font>!is', $fudml)) {
		$fudml = preg_replace('!<font (color|face|size)="(.+?)">(.*?)</font>!is', '[\1=\2]\3[/\1]', $fudml);
	}
	while (preg_match('!<(o|u)l type=".+?">.*?</\\1l>!is', $fudml)) {
		$fudml = preg_replace('!<(o|u)l type="(.+?)">(.*?)</\\1l>!is', '[list type=\2]\3[/list]', $fudml);
	}

	$fudml = str_replace(
	array(
		'<b>', '</b>', '<i>', '</i>', '<u>', '</u>', '<s>', '</s>', '<sub>', '</sub>', '<sup>', '</sup>',
		'<div class="pre"><pre>', '</pre></div>', '<div align="center">', '<div align="left">', '<div align="right">', '</div>',
		'<ul>', '</ul>', '<span name="notag">', '</span>', '<li>', '&#64;', '&#58;&#47;&#47;', '<br />', '<pre>', '</pre>'
	),
	array(
		'[b]', '[/b]', '[i]', '[/i]', '[/u]', '[/u]', '[s]', '[/s]', '[sub]', '[/sub]', '[sup]', '[/sup]', 
		'[code]', '[/code]', '[align=center]', '[align=left]', '[align=right]', '[/align]', '[list]', '[/list]',
		'[notag]', '[/notag]', '[*]', '@', '://', '', '[pre]', '[/pre]'
	), 
	$fudml);

	while (preg_match('!<img src="(.*?)" border=0 alt="\\1">!is', $fudml)) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="\\1">!is', '[img]\1[/img]', $fudml);
	}
	while (preg_match('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', '[email]\1[/email]', $fudml);
	}
	while (preg_match('!<a href="(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">\\1</a>!is', '[url]\1[/url]', $fudml);
	}

	if (strpos($fudml, '<img src="') !== false) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="(.*?)">!is', '[img=\1]\2[/img]', $fudml);
	}
	if (strpos($fudml, '<a href="mailto:') !== false) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">(.+?)</a>!is', '[email=\1]\2[/email]', $fudml);
	}
	if (strpos($fudml, '<a href="') !== false) { 
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">(.+?)</a>!is', '[url=\1]\2[/url]', $fudml);
	}

	if (isset($php)) {
		$fudml = str_replace(array_keys($php), array_values($php), $fudml);
	}

	/* unhtmlspecialchars */
	reverse_fmt($fudml);

	return $fudml;
}


function filter_ext($file_name)
{
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'file_filter_regexp';
	if (!count($GLOBALS['__FUD_EXT_FILER__'])) {
		return;
	}
	if (($p = strrpos($file_name, '.')) === false) {
		return 1;
	}
	return !in_array(strtolower(substr($file_name, ($p + 1))), $GLOBALS['__FUD_EXT_FILER__']);
}

function safe_tmp_copy($source, $del_source=0, $prefx='')
{
	if (!$prefx) {
		 $prefx = getmypid();
	}

	$umask = umask(($GLOBALS['FUD_OPT_2'] & 8388608 ? 0177 : 0111));
	if (!move_uploaded_file($source, ($name = tempnam($GLOBALS['TMP'], $prefx.'_')))) {
		return;
	}
	umask($umask);
	if ($del_source) {
		@unlink($source);
	}
	umask($umask);

	return basename($name);
}

function reverse_nl2br(&$data)
{
	$data = str_replace('<br />', '', $data);
}$GLOBALS['__SML_CHR_CHK__'] = array("\n"=>1, "\r"=>1, "\t"=>1, " "=>1, "]"=>1, "["=>1, "<"=>1, ">"=>1, "'"=>1, '"'=>1, "("=>1, ")"=>1, "."=>1, ","=>1, "!"=>1, "?"=>1);

function smiley_to_post($text)
{
	$text_l = strtolower($text);

        $c = uq('SELECT code, '.__FUD_SQL_CONCAT__.'(\'images/smiley_icons/\', img), descr FROM phpgw_fud_smiley');
        while ($r = db_rowarr($c)) {
        	$codes = (strpos($r[0], '~') !== false) ? explode('~', strtolower($r[0])) : array(strtolower($r[0]));

		foreach ($codes as $v) {
			$a = 0;
			$len = strlen($v);
			while (($a = strpos($text_l, $v, $a)) !== false) {
				if ((!$a || isset($GLOBALS['__SML_CHR_CHK__'][$text_l[$a - 1]])) && ((@$ch = $text_l[$a + $len]) == "" || isset($GLOBALS['__SML_CHR_CHK__'][$ch]))) {
					$rep = '<img src="'.$r[1].'" border=0 alt="'.$r[2].'">';
					$text = substr_replace($text, $rep, $a, $len);
					$text_l = substr_replace($text_l, $rep, $a, $len);
					$a += strlen($rep);
				} else {
					$a += $len;
				}
			}
		}
	}

	return $text;
}

function post_to_smiley($text)
{
	$c = uq('SELECT code, '.__FUD_SQL_CONCAT__.'(\'images/smiley_icons/\', img), descr FROM phpgw_fud_smiley');
	while ($r = db_rowarr($c)) {
		$im = '<img src="'.$r[1].'" border=0 alt="'.$r[2].'">';
		$re[$im] = (($p = strpos($r[0], '~')) !== false) ? substr($r[0], 0, $p) : $r[0];
	}

	return (isset($re) ? strtr($text, $re) : $text);
}function tmpl_draw_select_opt($values, $names, $selected, $normal_tmpl, $selected_tmpl)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (($a = count($vls)) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values inside a select<br>\n");
	}

	$options = '';
	for ($i = 0; $i < $a; $i++) {
		$options .= $vls[$i] != $selected ? '<option value="'.$vls[$i].'" '.$normal_tmpl.'>'.$nms[$i].'</option>' : '<option value="'.$vls[$i].'" selected '.$selected_tmpl.'>'.$nms[$i].'</option>';
	}

	return $options;
}function apply_custom_replace($text)
{
	if (!defined('__fud_replace_init')) {
		make_replace_array();
	}
	if (!isset($GLOBALS['__FUD_REPL__'])) {
		return $text;
	}

	return preg_replace($GLOBALS['__FUD_REPL__']['pattern'], $GLOBALS['__FUD_REPL__']['replace'], $text);
}

function make_replace_array()
{
	$c = uq('SELECT with_str, replace_str FROM phpgw_fud_replace WHERE replace_str IS NOT NULL AND with_str IS NOT NULL AND LENGTH(replace_str)>0');
	while ($r = db_rowarr($c)) {
		$GLOBALS['__FUD_REPL__']['pattern'][] = $r[1];
		$GLOBALS['__FUD_REPL__']['replace'][] = $r[0];
	}

	define('__fud_replace_init', 1);
}

function make_reverse_replace_array()
{
	$c = uq('SELECT replace_opt, with_str, replace_str, from_post, to_msg FROM phpgw_fud_replace');

	while ($r = db_rowarr($c)) {
		if (!$r[0]) {
			$GLOBALS['__FUD_REPLR__']['pattern'][] = $r[3];
			$GLOBALS['__FUD_REPLR__']['replace'][] = $r[4];
		} else if ($r[0] && strlen($r[1]) && strlen($r[2])) {
			$GLOBALS['__FUD_REPLR__']['pattern'][] = '/'.str_replace('/', '\\/', preg_quote(stripslashes($r[1]))).'/';
			preg_match('/\/(.+)\/(.*)/', $r[2], $regs);
			$GLOBALS['__FUD_REPLR__']['replace'][] = str_replace('\\/', '/', $regs[1]);
		}
	}

	define('__fud_replacer_init', 1);
}

function apply_reverse_replace($text)
{
	if (!defined('__fud_replacer_init')) {
		make_reverse_replace_array();
	}
	if (!isset($GLOBALS['__FUD_REPLR__'])) {
		return $text;
	}
	return preg_replace($GLOBALS['__FUD_REPLR__']['pattern'], $GLOBALS['__FUD_REPLR__']['replace'], $text);
}function &get_all_read_perms($uid, $mod)
{
	$limit = array(0);

	$r = uq('SELECT resource_id, group_cache_opt FROM phpgw_fud_group_cache WHERE user_id='._uid);
	while ($ent = db_rowarr($r)) {
		$limit[$ent[0]] = $ent[1] & 2;
	}

	if (_uid) {
		$r = uq("SELECT resource_id FROM phpgw_fud_group_cache WHERE resource_id NOT IN (".implode(',', array_keys($limit)).") AND user_id=2147483647 AND (group_cache_opt & 2) > 0");
		while ($ent = db_rowarr($r)) {
			if (!isset($limit[$ent[0]])) {
				$limit[$ent[0]] = 1;
			}
		}

		if ($mod) {
			$r = uq('SELECT forum_id FROM phpgw_fud_mod WHERE user_id='._uid);
			while ($ent = db_rowarr($r)) {
				$limit[$ent[0]] = 1;
			}
		}
	}

	return $limit;
}

function perms_from_obj($obj, $adm)
{
	$perms = 1|2|4|8|16|32|64|128|256|512|1024|2048|4096|8192|16384|32768;

	if ($adm || $obj->md) {
		return $perms;
	}

	return ($perms & $obj->group_cache_opt);
}

function make_perms_query(&$fields, &$join, $fid='')
{
	if (!$fid) {
		$fid = 'f.id';
	}

	if (_uid) {
		$join = ' INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id='.$fid.' LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id='.$fid.' ';
		$fields = ' (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS group_cache_opt ';
	} else {
		$join = ' INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=0 AND g1.resource_id='.$fid.' ';
		$fields = ' g1.group_cache_opt ';
	}
}function reverse_fmt(&$data)
{
	$data = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $data);
}

	if (isset($_GET['frm_id'])) {
		$frm_id = (int) $_GET['frm_id'];
	} else if (isset($_POST['frm_id'])) {
		$frm_id = (int) $_POST['frm_id'];
	} else {
		invl_inp_err();
	}

	if (isset($_GET['pl_id'])) {
		$pl_id = (int) $_GET['pl_id'];
	} else if (isset($_POST['pl_id'])) {
		$pl_id = (int) $_POST['pl_id'];
	} else {
		$pl_id = 0;
	}

	make_perms_query($fields, $join, $frm_id);

	/* fetch forum, poll & moderator data */
	if (!$pl_id) { /* new poll */
		$frm = db_sab('SELECT f.id, f.forum_opt, m.id AS md, '.$fields.'
			FROM phpgw_fud_forum f
			LEFT JOIN phpgw_fud_mod m ON m.user_id='._uid.' AND m.forum_id=f.id
			'.$join.'
			WHERE f.id='.$frm_id);
	} else { /* editing a poll */
		$frm = db_sab('SELECT f.id, f.forum_opt, m.id AS md, ms.id AS old_poll, p.id AS poll_id, p.*, '.$fields.'
			FROM phpgw_fud_forum f
			INNER JOIN phpgw_fud_poll p ON p.id='.$pl_id.'
			LEFT JOIN phpgw_fud_mod m ON m.user_id='._uid.' AND m.forum_id=f.id
			LEFT JOIN phpgw_fud_msg ms ON ms.poll_id=p.id
			'.$join.'
			WHERE f.id='.$frm_id);
	}

	$frm->group_cache_opt = (int) $frm->group_cache_opt;
	$frm->forum_opt = (int) $frm->forum_opt;

	if (!$frm || (!$frm->md && !($usr->users_opt & 1048576) && (!empty($frm->old_poll) && (!($frm->group_cache_opt & 4096) || (!($frm->group_cache_opt & 16) && $frm->owner != _uid))) && !($frm->group_cache_opt & 4))) {
		std_error('access');
	}

	if (isset($_POST['pl_submit'])) {
		if ($pl_id) { /* update a poll */
			poll_sync($pl_id, $_POST['pl_name'], $_POST['pl_max_votes'], $_POST['pl_expiry_date']);
		} else { /* adding a new poll */
			$pl_id = poll_add($_POST['pl_name'], $_POST['pl_max_votes'], $_POST['pl_expiry_date']);
		}
		$pl_name = $_POST['pl_name'];
		$pl_max_votes = $_POST['pl_max_votes'];
		$pl_expiry_date = $_POST['pl_expiry_date'];
	} else if (!empty($frm->poll_id)) {
		$pl_name = $frm->name;
		reverse_fmt($pl_name);
		$pl_max_votes = $frm->max_votes;
		$pl_expiry_date = $frm->expiry_date;
	} else {
		$pl_name = $pl_max_votes = $pl_expiry_date = '';
	}

	/* remove a poll option */
	if (isset($_GET['del_id'])) {
		poll_del_opt((int)$_GET['del_id'], $pl_id);
	}

	/* Adding or Updating poll options */
	if(!empty($_POST['pl_upd']) || !empty($_POST['pl_add'])) {
		$pl_option = apply_custom_replace($_POST['pl_option']);

		if ($frm->forum_opt & 16) {
			$pl_option = tags_to_html($pl_option, $frm->group_cache_opt & 32768);
		} else if ($frm->forum_opt & 8) {
			$pl_option = nl2br(htmlspecialchars($pl_option));
		}

		if ($frm->group_cache_opt & 16384 && !isset($_POST['pl_smiley_disabled'])) {
			$pl_option = smiley_to_post($pl_option);
		}

		if (isset($_POST['pl_upd'], $_POST['pl_option_id'])) {
			poll_opt_sync((int)$_POST['pl_option_id'], $pl_option);
		} else {
			poll_opt_add($pl_option, $pl_id);
		}
		$pl_option = '';
	}

	/* if we have a poll, fetch poll options */
	if ($pl_id) {
		$poll_opts = poll_fetch_opts($pl_id);
	}

	/* edit a poll option */
	if (isset($_GET['pl_optedit'])) {
		$pl_option = @$poll_opts[$_GET['pl_optedit']];
		$pl_option_id = $_GET['pl_optedit'];
	}

	$TITLE_EXTRA = ': Editor di sondaggi';



	$pl_expiry_date_data = tmpl_draw_select_opt("0\n3600\n21600\n43200\n86400\n259200\n604800\n2635200\n31536000", "Illimitato\n1 ora\n6 ore\n12 ore\n1 giorno\n3 giorni\n1 settimana\n1 mese\n1 anno", $pl_expiry_date, '', '');
	$pl_max_votes_data = tmpl_draw_select_opt("0\n10\n50\n100\n200\n500\n1000\n10000\n100000", "Illimitato\n10\n50\n100\n200\n500\n1000\n10000\n100000", $pl_max_votes, '', '');

	if ($frm->group_cache_opt & 16384) {
		$checked = isset($_POST['pl_smiley_disabled']) ? ' checked' : '';
		$pl_smiley_disabled_chk = '<tr><td align="right" valign="top" colspan=2 class="GenText"><input type="checkbox" name="pl_smiley_disabled" value="Y"'.$checked.'>Disabilita gli smiley</td></tr>';
	} else {
		$pl_smiley_disabled_chk = '';
	}

	$pl_submit = !$pl_id ? '<input type="submit" class="button" name="pl_submit" value="Crea sondaggio">' : '<input type="submit" class="button" name="pl_submit" value="Aggiorna sondaggio">';

	/* this is only available on a created poll */
	if ($pl_id) {
		if (isset($pl_option)) {
			$pl_option = post_to_smiley($pl_option);

			if ($frm->forum_opt & 16) {
				$pl_option = html_to_tags($pl_option);
			} else if ($frm->forum_opt & 8) {
				reverse_nl2br($pl_option);
			}

			$pl_option = apply_reverse_replace($pl_option);
		} else {
			$pl_option = '';
		}

		$pl_action = !isset($_GET['pl_optedit']) ? '<input type="submit" class="button" name="pl_add" onClick="javascript: return check_submission();" value="Aggiungi risposta">' : '<input type="hidden" name="pl_option_id" value="'.$pl_option_id.'">
<input type="submit" class="button" name="pl_upd" onClick="javascript: return check_submission();" value="Aggiorna risposta">';

		$poll_option_entry_data = '';
		if (!empty($poll_opts)) {
			foreach ($poll_opts as $k => $v) {
				$poll_option_entry_data .= '<tr><td class="GenText">'.$v.'</td><td nowrap>[<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=poll&amp;frm_id='.$frm_id.'&amp;'._rsid.'&amp;pl_id='.$pl_id.'&amp;pl_optedit='.$k.'">Modifica</a>] [<a href="/egroupware/fudforum/3814588639/index.php?t=poll&amp;frm_id='.$frm_id.'&amp;pl_id='.$pl_id.'&amp;del_id='.$k.'&amp;'._rsid.'">Cancella</a>]</td></tr>';
			}
		}

		$poll_editor = '<table width="99%" border=0 cellspacing=2 cellpadding=0 class="dashed">
<tr><td class="GenText">Aggiungi risposta:</td><td align="right"><input type="text" name="pl_option" value="'.htmlspecialchars($pl_option).'">
'.$pl_smiley_disabled_chk.'
<tr><td colspan=2 align=right>'.$pl_action.'</td></tr>
<tr><td colspan=2><table>
'.$poll_option_entry_data.'
</table>
</td></tr>
</table>';
	} else {
		$poll_editor = '';
	}

	$poll_submit_btn = !$pl_id ? '<input type="button" class="button" onClick="javascript: window.opener.document.post_form.pl_id.value=\'0\'; window.opener.document.post_form.submit(); window.close();" value="Crea">' : '<input type="button" class="button" onClick="javascript: window.opener.document.post_form.pl_id.value='.$pl_id.'; window.opener.document.post_form.submit(); window.close();" value="Aggiorna">';


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<title><?php echo $GLOBALS['FORUM_TITLE'].$TITLE_EXTRA; ?></title>
<script language="JavaScript" src="<?php echo $GLOBALS['WWW_ROOT']; ?>/lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="/egroupware/fudforum/3814588639/theme/italian/forum.css" type="text/css">
</head>
<body>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<script language="javascript" type="text/javascript">
function check_submission()
{
	var a;
	
	a = document.poll_submit.pl_option.value.replace('/[ \n\r]/g', '');

	if (!a.length) {
		if (document.poll_submit.pl_add && document.poll_submit.pl_add.value.length) {
			alert("Non puoi inserire una risposta vuota.");
			return false;
		} else if (document.poll_submit.pl_upd && document.poll_submit.pl_upd.value.length) {
			return confirm("Se vuoi cancellare questa risposta, premi OK");
		}
	}
	return true;	
}
</script>
<form action="/egroupware/fudforum/3814588639/index.php?t=poll" method="post" name="poll_submit">
<input type="hidden" name="frm_id" value="<?php echo $frm_id; ?>"><input type="hidden" name="pl_id" value="<?php echo $pl_id; ?>"><?php echo _hs; ?>
<table border=0 cellspacing=2 width="99%" cellpadding=0 class="dashed">
	<tr>
		<td class="GenText">Nome del sondaggio:</td>
		<td><input type="text" name="pl_name" value="<?php echo htmlspecialchars($pl_name); ?>"></td>
	</tr>
	<tr>
		<td class="GenText">Limite di tempo</td>
		<td><select name="pl_expiry_date"><?php echo $pl_expiry_date_data; ?></select></td>
	</tr>
	<tr>
		<td class="GenText">Voti massimi</td>
		<td><select name="pl_max_votes"><?php echo $pl_max_votes_data; ?></select></td>
	</tr>
	
	<tr><td colspan=2 align="right"><?php echo $pl_submit; ?></td></tr>
</table>
<p>
<?php echo $poll_editor; ?>
<p>
<div align=right>
<?php echo $poll_submit_btn; ?>
</div>
</form>
</td></tr></table></body></html>
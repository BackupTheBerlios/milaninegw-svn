<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: post.php.t,v 1.3 2003/12/18 18:20:49 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

function flood_check()
{
	$check_time = __request_timestamp__-$GLOBALS['FLOOD_CHECK_TIME'];

	if (($v = q_singleval("SELECT post_stamp FROM phpgw_fud_msg WHERE ip_addr='".get_ip()."' AND poster_id="._uid." AND post_stamp>".$check_time." ORDER BY post_stamp DESC LIMIT 1"))) {
		return (($v + $GLOBALS['FLOOD_CHECK_TIME']) - __request_timestamp__);
	}

	return;
}

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}function tmpl_draw_radio_opt($name, $values, $names, $selected, $normal_tmpl, $selected_tmpl, $sep)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (($a = count($vls)) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values<br>\n");
	}

	$checkboxes = '';
	for ($i = 0; $i < $a; $i++) {
		$checkboxes .= $vls[$i] != $selected ? '<input type="radio" name="'.$name.'" value="'.$vls[$i].'" '.$normal_tmpl.'>'.$nms[$i].$sep : '<input type="radio" name="'.$name.'" value="'.$vls[$i].'" checked '.$selected_tmpl.'>'.$nms[$i].$sep;
	}

	return $checkboxes;
}function reverse_fmt(&$data)
{
	$data = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $data);
}function tmpl_post_options($arg, $perms=0)
{
	$post_opt_html		= 'L&#39;<b>HTML</b> è <b>OFF</b>';
	$post_opt_fud		= '<b>FUDcode</b> è <b>OFF</b>';
	$post_opt_images 	= 'Le <b>immagini</b> sono <b>OFF</b>';
	$post_opt_smilies	= 'Gli <b>smiley</b> sono <b>OFF</b>';
	$edit_time_limit	= '';

	if (is_int($arg)) {
		if ($arg & 16) {
			$post_opt_fud = '<b><a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#style" target=_new>FUDcode</b> è <b>ON</a></b>';
		} else if (!($arg & 8)) {
			$post_opt_html = 'L&#39;<b>HTML</b> è <b>ON</b>';
		}
		if ($perms & 16384) {
			$post_opt_smilies = '<a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#sml" target=_new>Gli <b>smiley</b> sono <b>ON</b></a>';
		}
		if ($perms & 32768) {
			$post_opt_images = 'Le <b>immagini</b> sono <b>ON</b>';
		}
		$edit_time_limit = $GLOBALS['EDIT_TIME_LIMIT'] ? '<br><b>Tempo limite per la modifica</b>: <b>'.$GLOBALS['EDIT_TIME_LIMIT'].'</b> minuti' : '<br><b>Tempo limite per la modifica</b>: <b>illimitato</b>';
	} else if ($arg == 'private') {
		$o =& $GLOBALS['FUD_OPT_1'];

		if ($o & 4096) {
			$post_opt_fud = '<b><a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#style" target=_new>FUDcode</b> è <b>ON</a></b>';
		} else if (!($o & 2048)) {
			$post_opt_html = 'L&#39;<b>HTML</b> è <b>ON</b>';
		}
		if ($o & 16384) {
			$post_opt_images = 'Le <b>immagini</b> sono <b>ON</b>';
		}
		if ($o & 8192) {
			$post_opt_smilies = '<a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#sml" target=_new>Gli <b>smiley</b> sono <b>ON</b></a>';
		}
	} else if ($arg == 'sig') {
		$o =& $GLOBALS['FUD_OPT_1'];

		if ($o & 131072) {
			$post_opt_fud = '<b><a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#style" target=_new>FUDcode</b> è <b>ON</a></b>';
		} else if (!($o & 65536)) {
			$post_opt_html = 'L&#39;<b>HTML</b> è <b>ON</b>';
		}
		if ($o & 524288) {
			$post_opt_images = 'Le <b>immagini</b> sono <b>ON</b>';
		}
		if ($o & 262144) {
			$post_opt_smilies = '<a href="/egroupware/fudforum/3814588639/index.php?t=help_index&sect=readingposting&'._rsid.'#sml" target=_new>Gli <b>smiley</b> sono <b>ON</b></a>';
		}
	}

	return '<font class="SmallText"><b>Opzioni del forum</b><br />
'.$post_opt_html.'<br />
'.$post_opt_fud.'<br />
'.$post_opt_images.'<br />
'.$post_opt_smilies.$edit_time_limit.'</font><br />';
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
}function fud_wrap_tok($data)
{
	$wa = array();
	$len = strlen($data);

	$i=$j=$p=0;
	$str = '';
	while ($i < $len) {
		switch ($data{$i}) {
			case ' ':
			case "\n":
			case "\t":
				if ($j) {
					$wa[] = array('word'=>$str, 'len'=>($j+1));
					$j=0;
					$str ='';
				}

				$wa[] = array('word'=>$data[$i]);

				break;
			case '<':
				if (($p = strpos($data, '>', $i)) !== false) {
					if ($j) {
						$wa[] = array('word'=>$str, 'len'=>($j+1));
						$j=0;
						$str ='';
					}
					$s = substr($data, $i, ($p - $i) + 1);
					if ($s == '<pre>') {
						$s = substr($data, $i, ($p = (strpos($data, '</pre>', $p) + 6)) - $i);
						--$p;
					} else if ($s == '<span name="php">') {
						$s = substr($data, $i, ($p = (strpos($data, '</span>', $p) + 7)) - $i);
						--$p;
					}

					$wa[] = array('word' => $s);

					$i = $p;
					$j = 0;
				} else {
					$str .= $data[$i];
					$j++;
				}
				break;

			case '&':
				if (($e = strpos($data, ';', $i))) {
					$st = substr($data, $i, ($e - $i + 1));
					if (($st{1} == '#' && is_numeric(substr($st, 3, -1))) || !strcmp($st, '&nbsp;') || !strcmp($st, '&gt;') || !strcmp($st, '&lt;') || !strcmp($st, '&quot;')) {
						if ($j) {
							$wa[] = array('word'=>$str, 'len'=>($j+1));
							$j=0;
							$str ='';
						}

						$wa[] = array('word' => $st, 'sp' => 1);
						$i=$e;
						$j=0;
						break;
					}
				} /* fall through */
			default:
				$str .= $data[$i];
				$j++;
		}
		$i++;
	}

	if ($j) {
		$wa[] = array('word'=>$str, 'len'=>($j+1));
	}

	return $wa;
}

function fud_wordwrap(&$data)
{
	if (!$GLOBALS['WORD_WRAP'] || $GLOBALS['WORD_WRAP'] >= strlen($data)) {
		return;
	}

	$wa = fud_wrap_tok($data);
	$m = (int) $GLOBALS['WORD_WRAP'];
	$l = 0;
	$data = null;
	foreach($wa as $v) {
		if (isset($v['len']) && $v['len'] > $m) {
			if ($v['len'] + $l > $m) {
				$l = 0;
				$data .= ' ';
			}
			$data .= wordwrap($v['word'], $m, ' ', 1);
			$l += $v['len'];
		} else {
			if (isset($v['sp'])) {
				if ($l > $m) {
					$data .= ' ';
					$l = 0;
				}
				++$l;
			} else if (!isset($v['len'])) {
				$l = 0;
			} else {
				$l += $v['len'];
			}
			$data .= $v['word'];
		}
	}
}function get_spell_suggest($word)
{
	return pspell_suggest(__FUD_PSPELL_LINK__, $word);
}

function spell_check_w($word)
{
	return pspell_check(__FUD_PSPELL_LINK__, $word);
}

function init_spell($type, $dict)
{
	$pspell_config = pspell_config_create($dict);
	pspell_config_mode($pspell_config, $type);
	pspell_config_personal($pspell_config, $GLOBALS['FORUM_SETTINGS_PATH']."forum.pws");
	pspell_config_ignore($pspell_config, 2);
	define('__FUD_PSPELL_LINK__', pspell_new_config($pspell_config));

	return true;
}

function tokenize_string($data)
{
	if (!($len = strlen($data))) {
		return array();
	}
	$wa = array();

	$i=$p=0;
	while ($i < $len) {
		switch ($data{$i}) {
			case ' ':
			case '/':
			case '\\':
			case '.':
			case ',':
			case '!':
			case '>':
			case '?':
			case "\n":
			case "\r":
			case "\t":
			case ")":
			case "(":
			case "}":
			case "{":
			case "[":
			case "]":
			case "*":
			case ";":
			case '=':
			case ':':
				if (isset($str)) {
					$wa[] = array('token'=>$str, 'check'=>1);
					unset($str);
				}

				$wa[] = array('token'=>$data[$i], 'check'=>0);

				break;
			case '<':
				if (($p = strpos($data, '>', $i)) !== false) {
					if (isset($str)) {
						$wa[] = array('token'=>$str, 'check'=>1);
						unset($str);
					}

					$wrd = substr($data,$i,($p-$i)+1);
					$p3=$l=null;

					if ($wrd == '<pre>') {
						$l = 'pre';
					} else if ($wrd == '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1">') {
						$l = 1;
						$p3 = $p;

						while ($l > 0) {
							$p3 = strpos($data, 'table', $p3);

							if ($data[$p3-1] == '<') {
								$l++;
							} else if ($data[$p3-1] == '/' && $data[$p3-2] == '<') {
								$l--;
							}

							$p3 = strpos($data, '>', $p3);
						}
					}

					if ($p3) {
						$p = $p3;
						$wrd = substr($data, $i, ($p-$i)+1);
					} else if ($l && ($p2 = strpos($data, '</'.$l.'>', $p))) {
						$p = $p2+1+strlen($l)+1;
						$wrd = substr($data,$i,($p-$i)+1);
					}


					$wa[] = array('token'=>$wrd, 'check'=>0);

					$i=$p;
				} else {
					$str .= $data[$i];
				}
				break;
			case ':':
				if ($data[$i+1] == '/' && $data[$i+2] == '/') {
					$tmp_string = substr($data,$i+3);
					$regs = array();
					if (preg_match('!([A-Za-z0-9\-_\.\%\?\&=/]+)!is', $tmp_string, $regs)) {
						$wa[] = array('token'=>$str.'//'.$regs[1], 'check'=>0);
						unset($str);

						$i += 2+strlen($regs[1]);
						break;
					}
				} else if ($str == 'Re') {
					$wa[] = array('token'=>$str.':', 'check'=>0);
					unset($str);
					break;
				}

				if (isset($str)) {
					$wa[] = array('token'=>$str, 'check'=>1);
					unset($str);
				}
				$wa[] = array('token'=>$data[$i], 'check'=>0);

				break;
			case '&':
				if (isset($str)) {
					$wa[] = array('token'=>$str, 'check'=>1);
					unset($str);
				}

				$regs = array();
				if (preg_match('!(\&[A-Za-z]{2,5}\;)!', substr($data,$i,6), $regs)) {
					$wa[] = array('token'=>$regs[1], 'check'=>0);
					$i += strlen($regs[1])-1;
				} else {
					$wa[] = array('token'=>$data[$i], 'check'=>0);
				}
				break;
			default:
				if (isset($str)) {
					$str .= $data[$i];
				} else {
					$str = $data[$i];
				}
		}
		$i++;
	}

	if (isset($str)) {
		$wa[] = array('token'=>$str, 'check'=>1);
	}

	return $wa;
}

function draw_spell_sug_select($v,$k,$type)
{
	$sel_name = 'spell_chk_'.$type.'_'.$k;
	$data = '<select name="'.$sel_name.'">';
	$data .= '<option value="'.htmlspecialchars($v['token']).'">'.htmlspecialchars($v['token']).'</option>';
	$sug = get_spell_suggest($v['token']);
	$i=0;
	foreach($sug as $va) {
		$data .= '<option value="'.$va.'">'.++$i.') '.$va.'</option>';
	}

	if (!count($sug)) {
		$data .= '<option value="">no alternatives</option>';
	}

	$data .= '</select>';

	return $data;
}

function spell_replace($wa,$type)
{
	$data = '';

	foreach($wa as $k => $v) {
		if( $v['check']==1 && isset($_POST['spell_chk_'.$type.'_'.$k]) && strlen($_POST['spell_chk_'.$type.'_'.$k])) {
			$data .= $_POST['spell_chk_'.$type.'_'.$k];
		} else {
			$data .= $v['token'];
		}
	}

	return $data;
}

function spell_check_ar($wa,$type)
{
	foreach($wa as $k => $v) {
		if ($v['check'] > 0 && !spell_check_w($v['token'])) {
			$wa[$k]['token'] = draw_spell_sug_select($v, $k, $type);
		}
	}

	return $wa;
}

function reasemble_string($wa)
{
	$data = '';
	foreach($wa as $v) {
		$data .= $v['token'];
	}

	return $data;
}

function check_data_spell($data, $type, $dict)
{
	if (!$data) {
		return $data;
	}
	if (!defined('__FUD_PSPELL_LINK__') && !init_spell(PSPELL_FAST, $dict)) {
		return $data;
	}

	$wa = tokenize_string($data);
	$wa = spell_check_ar($wa, $type);
	$data = reasemble_string($wa);

	return $data;
}function is_notified($user_id, $thread_id)
{
	return q_singleval('SELECT * FROM phpgw_fud_thread_notify WHERE thread_id='.$thread_id.' AND user_id='.$user_id);
}

function thread_notify_add($user_id, $thread_id)
{
	if (!is_notified($user_id, $thread_id)) {
		q('INSERT INTO phpgw_fud_thread_notify(user_id, thread_id) VALUES ('.$user_id.', '.$thread_id.')');
	}
}

function thread_notify_del($user_id, $thread_id)
{
	q('DELETE FROM phpgw_fud_thread_notify WHERE thread_id='.$thread_id.' AND user_id='.$user_id);
}$GLOBALS['__error__'] = 0;

function set_err($err, $msg)
{
	$GLOBALS['__err_msg__'][$err] = $msg;
	$GLOBALS['__error__'] = 1;
}

function is_post_error()
{
	return $GLOBALS['__error__'];
}

function get_err($err, $br=0)
{
	if(isset($err) && isset($GLOBALS['__err_msg__'][$err])) {
		return ($br ? '<font class="ErrorText">'.$GLOBALS['__err_msg__'][$err].'</font><br />' : '<br /><font class="ErrorText">'.$GLOBALS['__err_msg__'][$err].'</font>');
	}
}

function post_check_images()
{
	if ($GLOBALS['MAX_IMAGE_COUNT'] && $GLOBALS['MAX_IMAGE_COUNT'] < count_images($_POST['msg_body'])) {
		return -1;
	}

	return 0;
}

function check_post_form()
{
	/* make sure we got a valid subject */
	if (!strlen(trim($_POST['msg_subject']))) {
		set_err('msg_subject', 'Oggetto necessario');
	}

	/* make sure the number of images [img] inside the body do not exceed the allowed limit */
	if (post_check_images()) {
		set_err('msg_body', 'Sono consentite un massimo di '.$GLOBALS['MAX_IMAGE_COUNT'].' immagini per messaggio; per cortesia, riduci il numero di immagini');
	}

	return $GLOBALS['__error__'];
}

function check_ppost_form($msg_subject)
{
	if (!strlen(trim($msg_subject))) {
		set_err('msg_subject', 'Oggetto necessario');
	}

	if (post_check_images()) {
		set_err('msg_body', 'Sono consentite un massimo di '.$GLOBALS['MAX_IMAGE_COUNT'].' immagini per messaggio; per cortesia, riduci il numero di immagini');
	}
	$list = explode(';', $_POST['msg_to_list']);
	foreach($list as $v) {
		$v = trim($v);
		if (strlen($v)) {
			if (!($obj = db_sab('SELECT u.users_opt, u.id, ui.ignore_id FROM phpgw_fud_users u LEFT JOIN phpgw_fud_user_ignore ui ON ui.user_id=u.id AND ui.ignore_id='._uid.' WHERE u.alias='.strnull(addslashes(htmlspecialchars($v)))))) {
				set_err('msg_to_list', 'Non c&#39;è alcun utente "'.htmlspecialchars($v).'" in questo forum');
				break;
			}
			if (!empty($obj->ignore_id)) {
				set_err('msg_to_list', 'Non puoi spedire un messaggio personale a "'.htmlspecialchars($v).'", perchè questo utente ha deciso di ignorarti.');
				break;
			} else if (!($obj->users_opt & 32) && !($GLOBALS['usr']->users_opt & 1048576)) {
				set_err('msg_to_list', 'Non puoi inviare un messaggio privato a "'.htmlspecialchars($v).'", perchè non accetta messaggi privati.');
				break;
			} else {
				$GLOBALS['recv_user_id'][] = $obj->id;
			}
		}
	}

	if (empty($_POST['msg_to_list'])) {
		set_err('msg_to_list', 'Non è possibile inviare il messaggio, manca il destinatario');
	}

	return $GLOBALS['__error__'];
}

function check_femail_form()
{
	if (empty($_POST['femail']) || validate_email($_POST['femail'])) {
		set_err('femail', 'Inserisci un indirizzo email valido.');
	}
	if (empty($_POST['subj'])) {
		set_err('subj', 'Non puoi spedire una email lasciando vuoto il campo Oggetto.');
	}
	if (empty($_POST['body'])) {
		set_err('body', 'Non puoi spedire una email lasciando vuoto il campo Testo.');
	}

	return $GLOBALS['__error__'];
}

function count_images($text)
{
	$text = strtolower($text);
	$a = substr_count($text, '[img]');
	$b = substr_count($text, '[/img]');

	return (($a > $b) ? $b : $a);
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
}function is_moderator($frm_id, $user_id)
{
	return q_singleval('SELECT id FROM phpgw_fud_mod WHERE user_id='.$user_id.' AND forum_id='.$frm_id);
}

function frm_updt_counts($frm_id, $replies, $threads, $last_post_id)
{
	$threads	= !$threads ? '' : ', thread_count=thread_count+'.$threads;
	$last_post_id	= !$last_post_id ? '' : ', last_post_id='.$last_post_id;

	q('UPDATE phpgw_fud_forum SET post_count=post_count+'.$replies.$threads.$last_post_id.' WHERE id='.$frm_id);
}function msg_get($id)
{
	if (($r = db_sab('SELECT * FROM phpgw_fud_msg WHERE id='.$id))) {
		$r->body = read_msg_body($r->foff, $r->length, $r->file_id);
		un_register_fps();
		return $r;
	}
	error_dialog('Messaggio non valido', 'Il messaggio che stai cercando di visualizzare non esiste.');
}

function poll_cache_rebuild($poll_id, &$data)
{
	if (!$poll_id) {
		$data = null;
		return;
	}

	if (!$data) { /* rebuild from cratch */
		$c = uq('SELECT id, name, count FROM phpgw_fud_poll_opt WHERE poll_id='.$poll_id);
		while ($r = db_rowarr($c)) {
			$data[$r[0]] = array($r[1], $r[2]);
		}
		if (!$data) {
			$data = null;
		}
	} else { /* register single vote */
		$data[$poll_id][1] += 1;
	}
}class fud_msg
{
	var $id, $thread_id, $poster_id, $reply_to, $ip_addr, $host_name, $post_stamp, $subject, $attach_cnt, $poll_id,
	    $update_stamp, $icon, $apr, $updated_by, $login, $length, $foff, $file_id, $msg_opt,
	    $file_id_preview, $length_preview, $offset_preview, $body, $mlist_msg_id;
}

class fud_msg_edit extends fud_msg
{
	function add_reply($reply_to, $th_id=null, $perm, $autoapprove=true)
	{
		if ($reply_to) {
			$this->reply_to = $reply_to;
			$fd = db_saq('SELECT t.forum_id, f.message_threshold, f.forum_opt FROM phpgw_fud_msg m INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id INNER JOIN phpgw_fud_forum f ON f.id=t.forum_id WHERE m.id='.$reply_to);
		} else {
			$fd = db_saq('SELECT t.forum_id, f.message_threshold, f.forum_opt FROM phpgw_fud_thread t INNER JOIN phpgw_fud_forum f ON f.id=t.forum_id WHERE t.id='.$th_id);
		}

		return $this->add($fd[0], $fd[1], $fd[2], $perm, $autoapprove);
	}

	function add($forum_id, $message_threshold, $forum_opt, $perm, $autoapprove=true)
	{
		if (!$this->post_stamp) {
			$this->post_stamp = __request_timestamp__;
		}

		if (!isset($this->ip_addr)) {
			$this->ip_addr = get_ip();
		}
		$this->host_name = $GLOBALS['FUD_OPT_1'] & 268435456 ? "'".addslashes(get_host($this->ip_addr))."'" : 'NULL';
		$this->thread_id = isset($this->thread_id) ? $this->thread_id : 0;
		$this->reply_to = isset($this->reply_to) ? $this->reply_to : 0;

		$file_id = write_body($this->body, $length, $offset);

		/* determine if preview needs building */
		if ($message_threshold && $message_threshold < strlen($this->body)) {
			$thres_body = trim_html($this->body, $message_threshold);
			$file_id_preview = write_body($thres_body, $length_preview, $offset_preview);
		} else {
			$file_id_preview = $offset_preview = $length_preview = 0;
		}

		poll_cache_rebuild($this->poll_id, $poll_cache);
		$poll_cache = ($poll_cache ? @serialize($poll_cache) : null);

		$this->id = db_qid("INSERT INTO phpgw_fud_msg (
			thread_id,
			poster_id,
			reply_to,
			ip_addr,
			host_name,
			post_stamp,
			subject,
			attach_cnt,
			poll_id,
			icon,
			msg_opt,
			file_id,
			foff,
			length,
			file_id_preview,
			offset_preview,
			length_preview,
			mlist_msg_id,
			poll_cache
		) VALUES(
			".$this->thread_id.",
			".$this->poster_id.",
			".(int)$this->reply_to.",
			'".$this->ip_addr."',
			".$this->host_name.",
			".$this->post_stamp.",
			".strnull(addslashes($this->subject)).",
			".(int)$this->attach_cnt.",
			".(int)$this->poll_id.",
			".strnull(addslashes($this->icon)).",
			".$this->msg_opt.",
			".$file_id.",
			".(int)$offset.",
			".(int)$length.",
			".$file_id_preview.",
			".$offset_preview.",
			".$length_preview.",
			".strnull($this->mlist_msg_id).",
			".strnull(addslashes($poll_cache))."
		)");

		$thread_opt = (int) ($perm & 4096 && isset($_POST['thr_locked']));

		if (!$this->thread_id) { /* new thread */
			if ($perm & 64 && isset($_POST['thr_ordertype'], $_POST['thr_orderexpiry'])) {
				if ((int)$_POST['thr_ordertype']) {
					$thread_opt |= (int) $_POST['thr_ordertype'];
					$thr_orderexpiry = (int) $_POST['thr_orderexpiry'];
				}
			}

			$this->thread_id = th_add($this->id, $forum_id, $this->post_stamp, $thread_opt, (isset($thr_orderexpiry) ? $thr_orderexpiry : 0));

			q('UPDATE phpgw_fud_msg SET thread_id='.$this->thread_id.' WHERE id='.$this->id);
		} else {
			th_lock($this->thread_id, $thread_opt & 1);
		}

		if ($autoapprove && $forum_opt & 2) {
			$this->approve($this->id, true);
		}

		return $this->id;
	}

	function sync($id, $frm_id, $message_threshold, $perm)
	{
		if (!db_locked()) {
			db_lock('phpgw_fud_poll_opt WRITE, phpgw_fud_forum WRITE, phpgw_fud_msg WRITE, phpgw_fud_thread WRITE, phpgw_fud_thread_view WRITE');
			$ll=1;
		}
		$file_id = write_body($this->body, $length, $offset);

		/* determine if preview needs building */
		if ($message_threshold && $message_threshold < strlen($this->body)) {
			$thres_body = trim_html($this->body, $message_threshold);
			$file_id_preview = write_body($thres_body, $length_preview, $offset_preview);
		} else {
			$file_id_preview = $offset_preview = $length_preview = 0;
		}

		poll_cache_rebuild($this->poll_id, $poll_cache);
		$poll_cache = ($poll_cache ? @serialize($poll_cache) : null);

		q("UPDATE phpgw_fud_msg SET
			file_id=".$file_id.",
			foff=".(int)$offset.",
			length=".(int)$length.",
			mlist_msg_id=".strnull(addslashes($this->mlist_msg_id)).",
			file_id_preview=".$file_id_preview.",
			offset_preview=".$offset_preview.",
			length_preview=".$length_preview.",
			updated_by=".$id.",
			msg_opt=".$this->msg_opt.",
			attach_cnt=".(int)$this->attach_cnt.",
			poll_id=".(int)$this->poll_id.",
			update_stamp=".__request_timestamp__.",
			icon=".strnull(addslashes($this->icon))." ,
			poll_cache=".strnull(addslashes($poll_cache)).",
			subject=".strnull(addslashes($this->subject))."
		WHERE id=".$this->id);

		/* determine wether or not we should deal with locked & sticky stuff
		 * current approach may seem a little redundant, but for (most) users who
		 * do not have access to locking & sticky this eliminated a query.
		 */
		$th_data = db_saq('SELECT orderexpiry, thread_opt, root_msg_id FROM phpgw_fud_thread WHERE id='.$this->thread_id);
		$locked = (int) isset($_POST['thr_locked']);
		if (isset($_POST['thr_ordertype'], $_POST['thr_orderexpiry']) || (($th_data[1] ^ $locked) & 1)) {
			$thread_opt = (int) $th_data[1];
			$orderexpiry = isset($_POST['thr_orderexpiry']) ? (int) $_POST['thr_orderexpiry'] : 0;

			/* confirm that user has ability to change lock status of the thread */
			if ($perm & 4096) {
				if ($locked && !($thread_opt & $locked)) {
					$thread_opt |= 1;
				} else if (!$locked && $thread_opt & 1) {
					$thread_opt &= ~1;
				}
			}

			/* confirm that user has ability to change sticky status of the thread */
			if ($th_data[2] == $this->id && isset($_POST['thr_ordertype'], $_POST['thr_orderexpiry']) && $perm & 64) {
				if (!$_POST['thr_ordertype'] && $thread_opt>1) {
					$orderexpiry = 0;
					$thread_opt &= ~6;
				} else if ($thread_opt < 2 && (int) $_POST['thr_ordertype']) {
					$thread_opt |= $_POST['thr_ordertype'];
				} else if (!($thread_opt & (int) $_POST['thr_ordertype'])) {
					$thread_opt = $_POST['thr_ordertype'] | ($thread_opt & 1);
				}
			}

			/* Determine if any work needs to be done */
			if ($thread_opt != $th_data[1] || $orderexpiry != $th_data[0]) {
				q("UPDATE phpgw_fud_thread SET thread_opt=".$thread_opt.", orderexpiry=".$orderexpiry." WHERE id=".$this->thread_id);
				/* Avoid rebuilding the forum view whenever possible, since it's a rather slow process
				 * Only rebuild if expiry time has changed or message gained/lost sticky status
				 */
				$diff = $thread_opt ^ $th_data[1];
				if (($diff > 1 && !($diff & 6)) || $orderexpiry != $th_data[0]) {
					rebuild_forum_view($frm_id);
				}
			}
		}

		if (isset($ll)) {
			db_unlock();
		}

		if ($GLOBALS['FUD_OPT_1'] & 16777216) {
			delete_msg_index($this->id);
			index_text((preg_match('!^Re: !i', $this->subject) ? '': $this->subject), $this->body, $this->id);
		}
	}

	function delete($rebuild_view=true, $mid=0, $th_rm=0)
	{
		if (!$mid) {
			$mid = $this->id;
		}

		if (!db_locked()) {
			db_lock('phpgw_fud_thr_exchange WRITE, phpgw_fud_thread_view WRITE, phpgw_fud_level WRITE, phpgw_fud_forum WRITE, phpgw_fud_forum_read WRITE, phpgw_fud_thread WRITE, phpgw_fud_msg WRITE, phpgw_fud_attach WRITE, phpgw_fud_poll WRITE, phpgw_fud_poll_opt WRITE, phpgw_fud_poll_opt_track WRITE, phpgw_fud_users WRITE, phpgw_fud_thread_notify WRITE, phpgw_fud_msg_report WRITE, phpgw_fud_thread_rate_track WRITE');
			$ll = 1;
		}

		if (!($del = db_sab('SELECT
				phpgw_fud_msg.id, phpgw_fud_msg.attach_cnt, phpgw_fud_msg.poll_id, phpgw_fud_msg.thread_id, phpgw_fud_msg.reply_to, phpgw_fud_msg.apr, phpgw_fud_msg.poster_id,
				phpgw_fud_thread.replies, phpgw_fud_thread.root_msg_id AS root_msg_id, phpgw_fud_thread.last_post_id AS thread_lip, phpgw_fud_thread.forum_id,
				phpgw_fud_forum.last_post_id AS forum_lip FROM phpgw_fud_msg LEFT JOIN phpgw_fud_thread ON phpgw_fud_msg.thread_id=phpgw_fud_thread.id LEFT JOIN phpgw_fud_forum ON phpgw_fud_thread.forum_id=phpgw_fud_forum.id WHERE phpgw_fud_msg.id='.$mid))) {
			if (isset($ll)) {
				db_unlock();
			}
			return;
		}

		/* attachments */
		if ($del->attach_cnt) {
			$res = q('SELECT location FROM phpgw_fud_attach WHERE message_id='.$mid." AND attach_opt=0");
			while ($loc = db_rowarr($res)) {
				@unlink($loc[0]);
			}
			unset($res);
			q('DELETE FROM phpgw_fud_attach WHERE message_id='.$mid." AND attach_opt=0");
		}

		q('DELETE FROM phpgw_fud_msg_report WHERE msg_id='.$mid);

		if ($del->poll_id) {
			poll_delete($del->poll_id);
		}

		/* check if thread */
		if ($del->root_msg_id == $del->id) {
			$th_rm = 1;
			/* delete all messages in the thread if there is more then 1 message */
			if ($del->replies) {
				$rmsg = q('SELECT id FROM phpgw_fud_msg WHERE thread_id='.$del->thread_id.' AND id != '.$del->id);
				while ($dim = db_rowarr($rmsg)) {
					fud_msg_edit::delete(false, $dim[0], 1);
				}
				unset($rmsg);
			}

			q('DELETE FROM phpgw_fud_thread_notify WHERE thread_id='.$del->thread_id);
			q('DELETE FROM phpgw_fud_thread WHERE id='.$del->thread_id);
			q('DELETE FROM phpgw_fud_thread_rate_track WHERE thread_id='.$del->thread_id);
			q('DELETE FROM phpgw_fud_thr_exchange WHERE th='.$del->thread_id);

			if ($del->apr) {
				/* we need to determine the last post id for the forum, it can be null */
				$lpi = (int) q_singleval('SELECT phpgw_fud_thread.last_post_id FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.last_post_id=phpgw_fud_msg.id AND phpgw_fud_msg.apr=1 WHERE forum_id='.$del->forum_id.' AND moved_to=0 ORDER BY phpgw_fud_msg.post_stamp DESC LIMIT 1');
				q('UPDATE phpgw_fud_forum SET last_post_id='.$lpi.', thread_count=thread_count-1, post_count=post_count-'.$del->replies.'-1 WHERE id='.$del->forum_id);
			}
		} else if (!$th_rm  && $del->apr) {
			q('UPDATE phpgw_fud_msg SET reply_to='.$del->reply_to.' WHERE thread_id='.$del->thread_id.' AND reply_to='.$mid);

			/* check if the message is the last in thread */
			if ($del->thread_lip == $del->id) {
				list($lpi, $lpd) = db_saq('SELECT id, post_stamp FROM phpgw_fud_msg WHERE thread_id='.$del->thread_id.' AND apr=1 AND id!='.$del->id.' ORDER BY post_stamp DESC LIMIT 1');
				q('UPDATE phpgw_fud_thread SET last_post_id='.$lpi.', last_post_date='.$lpd.', replies=replies-1 WHERE id='.$del->thread_id);
			} else {
				q('UPDATE phpgw_fud_thread SET replies=replies-1 WHERE id='.$del->thread_id);
			}

			/* check if the message is the last in the forum */
			if ($del->forum_lip == $del->id) {
				$lp = db_saq('SELECT phpgw_fud_thread.last_post_id, phpgw_fud_thread.last_post_date FROM phpgw_fud_thread_view INNER JOIN phpgw_fud_thread ON phpgw_fud_thread_view.forum_id=phpgw_fud_thread.forum_id AND phpgw_fud_thread_view.thread_id=phpgw_fud_thread.id WHERE phpgw_fud_thread_view.forum_id='.$del->forum_id.' AND phpgw_fud_thread_view.page=1 AND phpgw_fud_thread.moved_to=0 ORDER BY phpgw_fud_thread.last_post_date DESC LIMIT 1');
				if (!isset($lpd) || $lp[1] > $lpd) {
					$lpi = $lp[0];
				}
				q('UPDATE phpgw_fud_forum SET post_count=post_count-1, last_post_id='.$lpi.' WHERE id='.$del->forum_id);
			} else {
				q('UPDATE phpgw_fud_forum SET post_count=post_count-1 WHERE id='.$del->forum_id);
			}
		}

		q('DELETE FROM phpgw_fud_msg WHERE id='.$mid);

		if ($del->apr) {
			if ($del->poster_id) {
				user_set_post_count($del->poster_id);
			}

			if ($rebuild_view) {
				rebuild_forum_view($del->forum_id);

				/* needed for moved thread pointers */
				$r = q('SELECT forum_id, id FROM phpgw_fud_thread WHERE root_msg_id='.$del->root_msg_id);
				while (($res = db_rowarr($r))) {
					if ($th_rm) {
						q('DELETE FROM phpgw_fud_thread WHERE id='.$res[1]);
					}
					rebuild_forum_view($res[0]);
				}
				unset($r);
			}
		}

		if (isset($ll)) {
			db_unlock();
		}
	}

	function approve($id, $unlock_safe=false)
	{
		/* fetch info about the message, poll (if one exists), thread & forum */
		$mtf = db_sab('SELECT
					m.id, m.poster_id, m.apr, m.subject, m.foff, m.length, m.file_id, m.thread_id, m.poll_id, m.attach_cnt,
					m.post_stamp, m.reply_to, m.mlist_msg_id,
					t.forum_id, t.last_post_id, t.root_msg_id, t.last_post_date,
					m2.post_stamp AS frm_last_post_date,
					f.name AS frm_name,
					u.alias, u.email, u.sig,
					n.id AS nntp_id, ml.id AS mlist_id
				FROM phpgw_fud_msg m
				INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
				INNER JOIN phpgw_fud_forum f ON t.forum_id=f.id
				LEFT JOIN phpgw_fud_msg m2 ON f.last_post_id=m2.id
				LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
				LEFT JOIN phpgw_fud_mlist ml ON ml.forum_id=f.id
				LEFT JOIN phpgw_fud_nntp n ON n.forum_id=f.id
				WHERE m.id='.$id.' AND m.apr=0');

		/* nothing to do or bad message id */
		if (!$mtf) {
			return;
		}

		if ($mtf->alias) {
			reverse_fmt($mtf->alias);
		} else {
			$mtf->alias = $GLOBALS['ANON_NICK'];
		}

		if (!db_locked()) {
			db_lock('phpgw_fud_thread_view WRITE, phpgw_fud_level WRITE, phpgw_fud_users WRITE, phpgw_fud_forum WRITE, phpgw_fud_thread WRITE, phpgw_fud_msg WRITE');
			$ll = 1;
		}

		q("UPDATE phpgw_fud_msg SET apr=1 WHERE id=".$mtf->id);

		if ($mtf->poster_id) {
			user_set_post_count($mtf->poster_id);
		}

		$last_post_id = $mtf->post_stamp > $mtf->frm_last_post_date ? $mtf->id : 0;

		if ($mtf->root_msg_id == $mtf->id) {	/* new thread */
			rebuild_forum_view($mtf->forum_id);
			$threads = 1;
		} else {				/* reply to thread */
			if ($mtf->post_stamp > $mtf->last_post_date) {
				th_inc_post_count($mtf->thread_id, 1, $mtf->id, $mtf->post_stamp);
			} else {
				th_inc_post_count($mtf->thread_id, 1);
			}
			rebuild_forum_view($mtf->forum_id, q_singleval('SELECT page FROM phpgw_fud_thread_view WHERE forum_id='.$mtf->forum_id.' AND thread_id='.$mtf->thread_id));
			$threads = 0;
		}

		/* update forum thread & post count as well as last_post_id field */
		frm_updt_counts($mtf->forum_id, 1, $threads, $last_post_id);

		if ($unlock_safe || isset($ll)) {
			db_unlock();
		}

		if ($mtf->poll_id) {
			poll_activate($mtf->poll_id, $mtf->forum_id);
		}

		$mtf->body = read_msg_body($mtf->foff, $mtf->length, $mtf->file_id);

		if ($GLOBALS['FUD_OPT_1'] & 16777216) {
			index_text((preg_match('!Re: !i', $mtf->subject) ? '': $mtf->subject), $mtf->body, $mtf->id);
		}

		/* handle notifications */
		if ($mtf->root_msg_id == $mtf->id) {
			if (empty($mtf->frm_last_post_date)) {
				$mtf->frm_last_post_date = 0;
			}

			/* send new thread notifications to forum subscribers */
			$c = uq('SELECT u.email, u.icq, u.users_opt
					FROM phpgw_fud_forum_notify fn
					INNER JOIN phpgw_fud_users u ON fn.user_id=u.id
					LEFT JOIN phpgw_fud_forum_read r ON r.forum_id=fn.forum_id AND r.user_id=fn.user_id
					INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id='.$mtf->forum_id.'
					LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id=fn.user_id AND g2.resource_id='.$mtf->forum_id.'
				WHERE
					fn.forum_id='.$mtf->forum_id.' AND fn.user_id!='.(int)$mtf->poster_id.'
					AND (CASE WHEN (r.last_view IS NULL AND (u.last_read=0 OR u.last_read >= '.$mtf->frm_last_post_date.')) OR r.last_view > '.$mtf->frm_last_post_date.' THEN 1 ELSE 0 END)=1
					AND ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0');
			$notify_type = 'frm';
		} else {
			/* send new reply notifications to thread subscribers */
			$c = uq('SELECT u.email, u.icq, u.users_opt, r.msg_id, u.id
					FROM phpgw_fud_thread_notify tn
					INNER JOIN phpgw_fud_users u ON tn.user_id=u.id
					LEFT JOIN phpgw_fud_read r ON r.thread_id=tn.thread_id AND r.user_id=tn.user_id
					INNER JOIN phpgw_fud_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id='.$mtf->forum_id.'
					LEFT JOIN phpgw_fud_group_cache g2 ON g2.user_id=tn.user_id AND g2.resource_id='.$mtf->forum_id.'
				WHERE
					tn.thread_id='.$mtf->thread_id.' AND tn.user_id!='.(int)$mtf->poster_id.'
					AND (r.msg_id='.$mtf->last_post_id.' OR (r.msg_id IS NULL AND '.$mtf->post_stamp.' > u.last_read))
					AND ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0');
			$notify_type = 'thr';
		}
		while ($r = db_rowarr($c)) {
			if ($r[2] & 16) {
				$to['EMAIL'] = $r[0];
			} else {
				$to['ICQ'] = $r[1].'@pager.icq.com';
			}
			if (isset($r[4]) && is_null($r[3])) {
				$tl[] = $r[4];
			}
		}
		unset($c);
		if (isset($tl)) {
			/* this allows us to mark the message we are sending notification about as read, so that we do not re-notify the user
			 * until this message is read.
			 */
			q('INSERT INTO phpgw_fud_read (thread_id, msg_id, last_view, user_id) SELECT '.$mtf->thread_id.', 0, 0, id FROM phpgw_fud_users WHERE id IN('.implode(',', $tl).')');
		}
		if (isset($to)) {
			send_notifications($to, $mtf->id, $mtf->subject, $mtf->alias, $notify_type, ($notify_type == 'thr' ? $mtf->thread_id : $mtf->forum_id), $mtf->frm_name, $mtf->forum_id);
		}

		// Handle Mailing List and/or Newsgroup syncronization.
		if (($mtf->nntp_id || $mtf->mlist_id) && !$mtf->mlist_msg_id) {
			fud_use('email_msg_format.inc', true);

			reverse_fmt($mtf->alias);
			$from = $mtf->poster_id ? $mtf->alias.' <'.$mtf->email.'>' : $GLOBALS['ANON_NICK'].' <'.$GLOBALS['NOTIFY_FROM'].'>';
			$body = $mtf->body . (($mtf->msg_opt & 1 && $mtf->sig) ? "\n--\n" . $mtf->sig : '');
			plain_text($body);
			plain_text($subject);

			if ($mtf->reply_to) {
				$replyto_id = q_singleval('SELECT mlist_msg_id FROM phpgw_fud_msg WHERE id='.$mtf->reply_to);
			} else {
				$replyto_id = 0;
			}

			if ($mtf->attach_cnt) {
				$r = uq("SELECT a.id, a.original_name,
						CASE WHEN m.mime_hdr IS NULL THEN 'application/octet-stream' ELSE m.mime_hdr END
						FROM phpgw_fud_attach a
						LEFT JOIN phpgw_fud_mime m ON a.mime_type=m.id
						WHERE a.message_id=".$mtf->id." AND a.attach_opt=0");
				while ($ent = db_rowarr($r)) {
					$attach[$ent[1]] = file_get_contents($GLOBALS['FILE_STORE'].$ent[0].'.atch');
					if ($mtf->mlist_id) {
						$attach_mime[$ent[1]] = $ent[2];
					}
				}
			} else {
				$attach_mime = $attach = null;
			}

			if ($mtf->nntp_id) {
				fud_use('nntp.inc', true);

				$nntp_adm = db_sab('SELECT * FROM phpgw_fud_nntp WHERE id='.$mtf->nntp_id);
				$nntp = new fud_nntp;

				$nntp->server = $nntp_adm->server;
				$nntp->newsgroup = $nntp_adm->newsgroup;
				$nntp->port = $nntp_adm->port;
				$nntp->timeout = $nntp_adm->timeout;
				$nntp->nntp_opt = $nntp_adm->nntp_opt;
				$nntp->login = $nntp_adm->login;
				$nntp->pass = $nntp_adm->pass;

				define('sql_p', 'phpgw_fud_');

				$lock = $nntp->get_lock();
				$nntp->post_message($mtf->subject, $body, $from, $mtf->id, $replyto_id, $attach);
				$nntp->close_connection();
				$nntp->release_lock($lock);
			} else {
				fud_use('mlist_post.inc', true);
				$GLOBALS['CHARSET'] = 'ISO-8859-15';
				$r = db_saq('SELECT name, additional_headers FROM phpgw_fud_mlist WHERE id='.$mtf->mlist_id);
				mail_list_post($r[0], $from, $mtf->subject, $body, $mtf->id, $replyto_id, $attach, $attach_mime, $r[1]);
			}
		}
	}
}

function write_body($data, &$len, &$offset)
{
	$MAX_FILE_SIZE = 2147483647;

	$len = strlen($data);
	$i = 1;
	while ($i < 100) {
		$fp = fopen($GLOBALS['MSG_STORE_DIR'].'msg_'.$i, 'ab');
		fseek($fp, 0, SEEK_END);
		if (!($off = ftell($fp))) {
			$off = __ffilesize($fp);
		}
		if (!$off || ($off + $len) < $MAX_FILE_SIZE) {
			break;
		}
		fclose($fp);
		$i++;
	}

	if (fwrite($fp, $data) !== $len) {
		exit("FATAL ERROR: system has ran out of disk space<br>\n");
	}
	fclose($fp);

	if (!$off) {
		@chmod('msg_'.$i, ($GLOBALS['FUD_OPT_2'] & 8388608 ? 0600 : 0666));
	}
	$offset = $off;

	return $i;
}

function trim_html($str, $maxlen)
{
	$n = strlen($str);
	$ln = 0;
	for ($i = 0; $i < $n; $i++) {
		if ($str[$i] != '<') {
			$ln++;
			if ($ln > $maxlen) {
				break;
			}
			continue;
		}

		if (($p = strpos($str, '>', $i)) === false) {
			break;
		}

		for ($k = $i; $k < $p; $k++) {
			switch ($str[$k]) {
				case ' ':
				case "\r":
				case "\n":
				case "\t":
				case ">":
					break 2;
			}
		}

		if ($str[$i+1] == '/') {
			$tagname = strtolower(substr($str, $i+2, $k-$i-2));
			if (@end($tagindex[$tagname])) {
				$k = key($tagindex[$tagname]);
				unset($tagindex[$tagname][$k], $tree[$k]);
			}
		} else {
			$tagname = strtolower(substr($str, $i+1, $k-$i-1));
			switch ($tagname) {
				case 'br':
				case 'img':
				case 'meta':
					break;
				default:
					$tree[] = $tagname;
					end($tree);
					$tagindex[$tagname][key($tree)] = 1;
			}
		}
		$i = $p;
	}

	$data = substr($str, 0, $i);
	if (isset($tree) && is_array($tree)) {
		$tree = array_reverse($tree);
		foreach ($tree as $v) {
			$data .= '</'.$v.'>';
		}
	}

	return $data;
}

function make_email_message(&$body, &$obj, $iemail_unsub)
{
	$TITLE_EXTRA = $iemail_poll = $iemail_attach = '';
	if ($obj->poll_cache) {
		$pl = @unserialize($obj->poll_cache);
		if (is_array($pl) && count($pl)) {
			foreach ($pl as $k => $v) {
				$length = ($v[1] && $obj->total_votes) ? round($v[1] / $obj->total_votes * 100) : 0;
				$iemail_poll .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td>'.$v[0].'</td><td><img src="/egroupware/fudforum/3814588639/theme/italian/images/poll_pix.gif" alt="" height="10" width="'.$length.'" /> '.$v[1].' / '.$length.'%</td></tr>';
			}
			$iemail_poll = '<table border=0 cellspacing=1 cellpadding=2 class="PollTable">
<tr><th nowrap colspan=3>'.$obj->poll_name.'<img src="blank.gif" alt="" height=1 width=10 /><font size="-1">[ '.$obj->total_votes.' voti ]</font></th></tr>
'.$iemail_poll.'
</table><p>';
		}
	}
	if ($obj->attach_cnt && $obj->attach_cache) {
		$atch = @unserialize($obj->attach_cache);
		if (is_array($atch) && count($atch)) {
			foreach ($atch as $v) {
				$sz = $v[2] / 1024;
				$sz = $sz < 1000 ? number_format($sz, 2).'KB' : number_format($sz/1024, 2).'MB';
				$iemail_attach .= '<tr>
<td valign="middle"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=getfile&amp;id='.$v[0].'&amp;'._rsid.'"><img alt="" src="'.$GLOBALS['WWW_ROOT'].'images/mime/'.$v[4].'" /></a></td>
<td><font class="GenText"><b>Attachment:</b></font> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=getfile&amp;id='.$v[0].'&amp;'._rsid.'">'.$v[1].'</a><br />
<font class="SmallText">(Dimensione: '.$sz.', Scaricato '.$v[3].' volte)</font></td></tr>';
			}
			$iemail_attach = '<p>
<table border=0 cellspacing=0 cellpadding=2>
'.$iemail_attach.'
</table>';
		}
	}

	return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<title>'.$GLOBALS['FORUM_TITLE'].$TITLE_EXTRA.'</title>
<script language="JavaScript" src="'.$GLOBALS['WWW_ROOT'].'/lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="/egroupware/fudforum/3814588639/theme/italian/forum.css" type="text/css">
</head>
<body>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr class="RowStyleB">
	<td width="33%"><b>Subject:</b> '.$obj->subject.'</td>
	<td width="33%"><b>Author:</b> '.$obj->alias.'</td>
	<td width="33%"><b>Date:</b> '.strftime("%a, %d %B %Y %H:%M", $obj->post_stamp).'</td>
</tr>
<tr class="RowStyleA">
	<td colspan="3">
	'.$iemail_poll.'
	'.$body.'
	'.$iemail_attach.'
	</td>
</tr>
<tr class="RowStyleB">
	<td colspan="3">
	[ <a href="/egroupware/fudforum/3814588639/index.php?t=post&reply_to='.$obj->id.'">Reply</a> ][ <a href="/egroupware/fudforum/3814588639/index.php?t=post&reply_to='.$obj->id.'&quote=true">Quote</a> ][ <a href="/egroupware/fudforum/3814588639/index.php?t=rview&goto='.$obj->id.'">View Topic/Message</a> ]'.$iemail_unsub.'
	</td>
</tr>
</table>
</td></tr></table></body></html>';
}

function send_notifications($to, $msg_id, $thr_subject, $poster_login, $id_type, $id, $frm_name, $frm_id)
{
	if (isset($to['EMAIL']) && (is_string($to['EMAIL']) || (is_array($to['EMAIL']) && count($to['EMAIL'])))) {
		$do_email = 1;
		$goto_url['email'] = 'http://'.$_SERVER['SERVER_NAME'].'/egroupware/fudforum/3814588639/index.php?t=rview&goto='.$msg_id;
		if ($GLOBALS['FUD_OPT_2'] & 64) {

			$obj = db_sab("SELECT p.total_votes, p.name AS poll_name, m.reply_to, m.subject, m.id, m.post_stamp, m.poster_id, m.foff, m.length, m.file_id, u.alias, m.attach_cnt, m.attach_cache, m.poll_cache FROM phpgw_fud_msg m LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id LEFT JOIN phpgw_fud_poll p ON m.poll_id=p.id WHERE m.id=".$msg_id." AND m.apr=1");

			$headers  = "MIME-Version: 1.0\r\n";
			if ($obj->reply_to) {
				$headers .= "In-Reply-To: ".$obj->reply_to."\r\n";
			}
			$headers .= "List-Id: ".$frm_id.".".$_SERVER['SERVER_NAME']."\r\n";
			$split = get_random_value(128)                                                                            ;
			$headers .= "Content-Type: multipart/alternative; boundary=\"------------" . $split . "\"\r\n";
			$boundry = "\r\n--------------" . $split . "\r\n";

			$CHARSET = 'ISO-8859-15';

			$pfx = '';

			$plain_text = read_msg_body($obj->foff, $obj->length, $obj->file_id);
			$iemail_unsub = $id_type == 'thr' ? '[ <a href="http://'.$_SERVER['SERVER_NAME'].'/egroupware/fudforum/3814588639/index.php?t=rview&th='.$id.'&notify=1&opt=off">Unsubscribe from this thread</a> ]' : '[ <a href="http://'.$_SERVER['SERVER_NAME'].'/egroupware/fudforum/3814588639/index.php?t=rview&frm_id='.$id.'&unsub=1">Unsubscribe from this forum</a> ]';

			$body_email = $boundry . "Content-Type: text/plain; charset=" . $CHARSET . "; format=flowed\r\nContent-Transfer-Encoding: 7bit\r\n\r\n" . strip_tags($plain_text) . "\r\n\r\n" . 'To participate in the discussion, go here:' . ' ' . 'http://'.$_SERVER['SERVER_NAME'].'/egroupware/fudforum/3814588639/index.php?t=rview&th=' . $id . "&notify=1&opt=off\r\n" .
			$boundry . "Content-Type: text/html; charset=" . $CHARSET . "\r\nContent-Transfer-Encoding: 7bit\r\n\r\n" . make_email_message($plain_text, $obj, $iemail_unsub) . "\r\n" . substr($boundry, 0, -2) . "--\r\n";
		}
	}
	if (isset($to['ICQ']) && (is_string($to['ICQ']) || (is_array($to['ICQ']) && count($to['ICQ'])))) {
		$do_icq = 1;
		$icq = str_replace('http://', "http'+'://'+'", $GLOBALS['WWW_ROOT']);
		$icq = str_replace('www.', "www'+'.", $icq);
		$goto_url['icq'] = "javascript:window.location='".$icq."/egroupware/fudforum/3814588639/index.php?t=rview&goto=".$msg_id."';";
	} else if (!isset($do_email)) {
		/* nothing to do */
		return;
	}

	reverse_fmt($thr_subject);
	reverse_fmt($poster_login);

	if ($id_type == 'thr') {
		$subj = 'Nuova risposta a '.$thr_subject.' da parte di '.$poster_login;

		if (!isset($body_email) && isset($do_email)) {
			$unsub_url['email'] = 'http://'.$_SERVER['SERVER_NAME'].'/egroupware/fudforum/3814588639/index.php?t=rview&th='.$id.'&notify=1&opt=off';
			$body_email = 'Per visualizzare le reply non lette vai a '.$goto_url['email'].'\n\nSe non vuoi ricevere ulteriori notifiche circa le risposte inviate in questo topic, clicca qui: '.$unsub_url['email'];
		}

		if (isset($do_icq)) {
			$unsub_url['icq'] = "javascript:window.location='".$icq."/egroupware/fudforum/3814588639/index.php?t=rview&th=".$id."&notify=1&opt=off';";
			$body_icq = 'Per visualizzare le reply non lette vai a\n'.$goto_url['icq'].'\n\nSe non vuoi ricevere ulteriori notifiche circa le risposte inviate in questo topic, clicca qui:\n'.$unsub_url['icq'];
		}
	} else if ($id_type == 'frm') {
		reverse_fmt($frm_name);

		$subj = 'Nuovo topic nel forum '.$frm_name.' intitolato '.$thr_subject.' di '.$poster_login;

		if (isset($do_icq)) {
			$unsub_url['icq'] = "javascript:window.location='".$icq."/egroupware/fudforum/3814588639/index.php?t=rview&unsub=1&frm_id=".$id."';";
			$body_icq = 'Per vedere il topic vai a:\n'.$goto_url['icq'].'\n\nPer non ricevere più notifiche circa nuovi post in questo forum, clicca qui:\n'.$unsub_url['icq'];
		}
		if (!isset($body_email) && isset($do_email)) {
			$unsub_url['email'] = 'http://'.$_SERVER['SERVER_NAME'].'/egroupware/fudforum/3814588639/index.php?t=rview&unsub=1&frm_id='.$id;
			$body_email = 'Per vedere il topic vai a:\n'.$goto_url['email'].'\n\nPer non ricevere più notifiche circa nuovi post in questo forum, clicca qui: '.$unsub_url['email'];
		}
	}

	if (isset($do_email)) {
		send_email($GLOBALS['NOTIFY_FROM'], $to['EMAIL'], $subj, $body_email, (isset($headers) ? $headers : ''));
	}
	if (isset($do_icq)) {
		send_email($GLOBALS['NOTIFY_FROM'], $to['ICQ'], $subj, $body_icq);
	}
}function check_return($returnto)
{
	if (!$returnto || !strncmp($returnto, 't=error', 7)) {
		header('Location: /egroupware/fudforum/3814588639/index.php?t=index&'._rsidl);
	} else {
		if (strpos($returnto, 'S=') === false && $GLOBALS['FUD_OPT_1'] & 128) {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto.'&S='.s);
		} else {
			header('Location: /egroupware/fudforum/3814588639/index.php?'.$returnto);
		}
	}
	exit;
}include $GLOBALS['FORUM_SETTINGS_PATH'] . 'ip_filter_cache';
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'login_filter_cache';
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'email_filter_cache';

function is_ip_blocked($ip)
{
	if (!count($GLOBALS['__FUD_IP_FILTER__'])) {
		return;
	}
	$block =& $GLOBALS['__FUD_IP_FILTER__'];
	list($a,$b,$c,$d) = explode('.', $ip);

	if (!isset($block[$a])) {
		return;
	}
	if (isset($block[$a][$b][$c][$d])) {
		return 1;
	}

	if (isset($block[$a][256])) {
		$t = $block[$a][256];
	} else if (isset($block[$a][$b])) {
		$t = $block[$a][$b];
	} else {
		return;
	}

	if (isset($t[$c])) {
		$t = $t[$c];
	} else if (isset($t[256])) {
		$t = $t[256];
	} else {
		return;
	}

	return (isset($t[$d]) || isset($t[256])) ? 1 : null;
}

function is_login_blocked($l)
{
	foreach ($GLOBALS['__FUD_LGN_FILTER__'] as $v) {
		if (preg_match($v, $l)) {
			return 1;
		}
	}
	return;
}

function is_email_blocked($addr)
{
	if (!count($GLOBALS['__FUD_EMAIL_FILTER__'])) {
		return;
	}
	$addr = strtolower($addr);
	foreach ($GLOBALS['__FUD_EMAIL_FILTER__'] as $k => $v) {
		if (($v && (strpos($addr, $k) !== false)) || (!$v && preg_match($k, $addr))) {
			return 1;
		}
	}
	return;
}

function is_allowed_user(&$usr)
{
	if ($GLOBALS['FUD_OPT_2'] & 1024 && $usr->users_opt & 2097152) {
		error_dialog('Unverified Account', 'The administrator had chosen to review all accounts manually prior to activation. Until your account is validated by the administrator you will not be able to utilize the full capabilities of your account.');
	}

	if ($usr->users_opt & 65536 || is_email_blocked($usr->email) || is_login_blocked($usr->login) || is_ip_blocked(get_ip())) {
		error_dialog('ERRORE: non sei autorizzato a postare messaggi', 'A questo account è stata impedita la possibilità di scrivere messaggi');
	}
}function logaction($user_id, $res, $res_id=0, $action=null)
{
	q('INSERT INTO phpgw_fud_action_log (logtime, logaction, user_id, a_res, a_res_id)
		VALUES('.__request_timestamp__.', '.strnull($action).', '.$user_id.', '.strnull($res).', '.(int)$res_id.')');
}

function clear_action_log()
{
	q('DELETE FROM phpgw_fud_action_log');
}function draw_post_smiley_cntrl()
{
	$c = uq('SELECT code, descr, img FROM phpgw_fud_smiley ORDER BY vieworder LIMIT '.$GLOBALS['MAX_SMILIES_SHOWN']);
	$data = '';
	while ($r = db_rowarr($c)) {
		$r[0] = ($a = strpos($r[0], '~')) ? substr($r[0], 0, $a) : $r[0];
		$data .= '<a href="javascript: insertTag(document.post_form.msg_body, \'\', \' '.$r[0].' \');"><img title="'.$r[1].'" alt="'.$r[1].'" src="images/smiley_icons/'.$r[2].'" /></a>&nbsp;';
	}

	return ($data ? '<tr class="RowStyleA"><td nowrap valign=top class="GenText">Shortcut per le faccine:
	<br /><font size="-1">[<a href="javascript://" onClick="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=smladd\', \'sml_list\', 220, 200);">mostra tutti gli smiley</a>]</font>
</td>
<td valign=top><table border=0 cellspacing=5 cellpadding=0><tr valign="bottom"><td>'.$data.'</td></tr></table></td></tr>' : '');
}

function draw_post_icons($msg_icon)
{
	$tmp = $data = '';
	$allowed_ext = array('.jpg' => 1, '.png' => 1, '.jpeg' => 1, '.gif' => 1);
	$p = -1;
	$rl = (int) $GLOBALS['POST_ICONS_PER_ROW'];

	$none_checked = !$msg_icon ? ' checked' : '';

	if ($d = opendir($GLOBALS['WWW_ROOT_DISK'] . 'images/message_icons')) {
		while ($f = readdir($d)) {
			if ($f == '.' || $f == '..') continue;
			if (strlen($f) < 4 || !isset($allowed_ext[strtolower(strrchr($f, '.'))])) {
				continue;
			}
			if (++$p > $rl) {
				$data .= '<tr>'.$tmp.'</tr>';
				$tmp = ''; $p = 0;
			}
			$checked = $f == $msg_icon ? ' checked' : '';
			$tmp .= '<td nowrap valign="middle"><input type="radio" name="msg_icon" value="'.$f.'"'.$checked.'><img src="images/message_icons/'.$f.'" alt="" /></td>';
		}
		closedir($d);
		if ($tmp) {
			$data .= '<tr>'.$tmp.'</tr>';
		}
	}

	return ($data ? '<tr class="RowStyleA"><td valign=top class="GenText">Icona del messaggio:</td><td>
<table border=0 cellspacing=0 cellpadding=2>
<tr><td class="GenText" colspan='.$GLOBALS['POST_ICONS_PER_ROW'].'><input type="radio" name="msg_icon" value=""'.$none_checked.'>Nessuna icona</td></tr>
'.$data.'
</table>
</td></tr>' : '');
}

function draw_post_attachments($al, $max_as, $max_a, $attach_control_error, $private='', $msg_id)
{
	$attached_files = '';
	$i = 0;

	if (!empty($al) && count($al)) {
		$enc = base64_encode(@serialize($al));
		$c = uq('SELECT a.id,a.fsize,a.original_name,m.mime_hdr
		FROM phpgw_fud_attach a
		LEFT JOIN phpgw_fud_mime m ON a.mime_type=m.id
		WHERE a.id IN('.implode(',', $al).') AND message_id IN(0, '.$msg_id.') AND attach_opt='.($private ? 1 : 0));
		while ($r = db_rowarr($c)) {
			$sz = ( $r[1] < 100000 ) ? number_format($r[1]/1024,2).'KB' : number_format($r[1]/1048576,2).'MB';
			$insert_uploaded_image = strncasecmp('image/', $r[3], 6) ? '' : '&nbsp;|&nbsp;<a href="javascript: insertTag(document.post_form.msg_body, \'[img]/egroupware/fudforum/3814588639/index.php?t=getfile&id='.$r[0].$private.'\', \'[/img]\');">Insert image into message body</a>';
			$attached_files .= '<tr>
	<td class="RowStyleB">'.$r[2].'</td>
	<td class="RowStyleB">'.$sz.'</td>
	<td class="RowStyleB"><a class="GenLink" href="javascript: document.post_form.file_del_opt.value=\''.$r[0].'\'; document.post_form.submit();">Cancella</a>'.$insert_uploaded_image.'</td>
</tr>';
			$i++;
		}
	}

	if ($i) {
		$attachment_list = '<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr>
	<th>Nome</th>
	<th>Dimensione</th>
	<th>Azione</th>
</tr>
'.$attached_files.'
</table>
<input type="hidden" name="file_del_opt" value="">
<input type="hidden" name="file_array" value="'.$enc.'">';
		$attached_status = '<font class="SmallText"> allegati '.$i.' file';
	} else {
		$attached_status = $attachment_list = '';
	}

	$upload_file = (($i + 1) <= $max_a) ? '<input type="file" name="attach_control"> <input type="submit" class="button" name="attach_control_add" value="Upload file">' : '';

	if (!$private && $GLOBALS['MOD'] && $GLOBALS['frm']->forum_opt & 32) {
		$allowed_extensions = '(unrestricted)';
	} else {
		include $GLOBALS['FORUM_SETTINGS_PATH'] . 'file_filter_regexp';
		if (!count($GLOBALS['__FUD_EXT_FILER__'])) {
			$allowed_extensions = '(unrestricted)';
		} else {
			$allowed_extensions = implode(' ', $GLOBALS['__FUD_EXT_FILER__']);
		}
	}
	return '<tr class="RowStyleB"><td nowrap valign=top class="GenText">Allegati:</td><td>
'.$attachment_list.'
'.$attach_control_error.'
<font class="SmallText"><b>Estensioni consentite:</b> '.$allowed_extensions.'<br /><b>Dimensione massima del file:</b> '.$max_as.'Kb<br /><b>Numero massimo di file per messaggio:</b> '.$max_a.'
'.$attached_status.'
</font><p>
'.$upload_file.'
</td></tr>';
}function th_lock($id, $lck)
{
	q("UPDATE phpgw_fud_thread SET thread_opt=(thread_opt|1)".(!$lck ? '& ~ 1' : '')." WHERE id=".$id);
}

function th_inc_view_count($id)
{
	q('UPDATE phpgw_fud_thread SET views=views+1 WHERE id='.$id);
}

function th_inc_post_count($id, $r, $lpi=0, $lpd=0)
{
	if ($lpi && $lpd) {
		q('UPDATE phpgw_fud_thread SET replies=replies+'.$r.', last_post_id='.$lpi.', last_post_date='.$lpd.' WHERE id='.$id);
	} else {
		q('UPDATE phpgw_fud_thread SET replies=replies+'.$r.' WHERE id='.$id);
	}
}

function th_frm_last_post_id($id, $th)
{
	return (int) q_singleval('SELECT phpgw_fud_thread.last_post_id FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE phpgw_fud_thread.forum_id='.$id.' AND phpgw_fud_thread.id!='.$th.' AND phpgw_fud_thread.moved_to=0 AND phpgw_fud_msg.apr=1 ORDER BY phpgw_fud_thread.last_post_date DESC LIMIT 1');
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
}function register_fp($id)
{
	if (!isset($GLOBALS['__MSG_FP__'][$id])) {
		$GLOBALS['__MSG_FP__'][$id] = fopen($GLOBALS['MSG_STORE_DIR'].'msg_'.$id, 'rb');
	}

	return $GLOBALS['__MSG_FP__'][$id];
}

function un_register_fps()
{
	if (!isset($GLOBALS['__MSG_FP__'])) {
		return;
	}
	unset($GLOBALS['__MSG_FP__']);
}

function read_msg_body($off, $len, $file_id)
{
	$fp = register_fp($file_id);
	fseek($fp, $off);
	return fread($fp, $len);
}function validate_email($email)
{
        return !preg_match('!([-_A-Za-z0-9\.]+)\@([-_A-Za-z0-9\.]+)\.([A-Za-z0-9]{2,4})$!', $email);
}

function send_email($from, $to, $subj, $body, $header='')
{
	if (empty($to) || !count($to)) {
		return;
	}
	$body = str_replace('\n', "\n", $body);

	if ($GLOBALS['FUD_OPT_1'] & 512) {
		if (!class_exists('fud_smtp')) {
			fud_use('smtp.inc');
		}
		$smtp = new fud_smtp;
		$smtp->msg = str_replace("\n.", "\n..", $body);
		$smtp->subject = $subj;
		$smtp->to = $to;
		$smtp->from = $from;
		$smtp->headers = $header;
		$smtp->send_smtp_email();
	} else {
		$bcc = '';

		if (is_array($to)) {
			$to = $to[0];
			if (count($to) > 1) {
				unset($to[0]);
				$bcc = 'Bcc: ' . implode(', ', $to);
			}
		}
		if ($header) {
			$header = "\n" . str_replace("\r", "", $header);
		} else if ($bcc) {
			$bcc = "\n" . $bcc;
		}

		if (version_compare("4.3.3RC2", phpversion(), ">")) {
			$body = str_replace("\n.", "\n..", $body);
		}

		mail($to, $subj, str_replace("\r", "", $body), "From: ".$from."\nErrors-To: ".$from."\nReturn-Path: ".$from."\nX-Mailer: FUDforum v".$GLOBALS['FORUM_VERSION'].$header.$bcc);
	}
}class fud_smtp
{
	var $fs, $last_ret, $msg, $subject, $to, $from, $headers;

	function get_return_code($cmp_code='250')
	{
		if (!($this->last_ret = fgets($this->fs, 1024))) {
			return;
		}
		if (substr($this->last_ret, 0, 3) == $cmp_code) {
			return 1;
		}

		return;
	}

	function wts($string)
	{
		fwrite($this->fs, $string . "\r\n");
	}

	function open_smtp_connex()
	{
		if( !($this->fs = fsockopen($GLOBALS['FUD_SMTP_SERVER'], 25, $errno, $errstr, $GLOBALS['FUD_SMTP_TIMEOUT'])) ) {
			exit("ERROR: stmp server at ".$GLOBALS['FUD_SMTP_SERVER']." is not avaliable<br>\nAdditional Problem Info: $errno -> $errstr <br>\n");
		}
		if (!$this->get_return_code(220)) {
			return;
		}
		$this->wts("HELO ".$GLOBALS['FUD_SMTP_SERVER']);
		if (!$this->get_return_code()) {
			return;
		}

		/* Do SMTP Auth if needed */
		if ($GLOBALS['FUD_SMTP_LOGIN']) {
			$this->wts('AUTH LOGIN');
			if (!$this->get_return_code(334)) {
				return;
			}
			$this->wts(base64_encode($GLOBALS['FUD_SMTP_LOGIN']));
			if (!$this->get_return_code(334)) {
				return;
			}
			$this->wts(base64_encode($GLOBALS['FUD_SMTP_PASS']));
			if (!$this->get_return_code(235)) {
				return;
			}
		}

		return 1;
	}

	function send_from_hdr()
	{
		$this->wts('MAIL FROM: <'.$GLOBALS['NOTIFY_FROM'].'>');
		return $this->get_return_code();
	}

	function send_to_hdr()
	{
		if (!@is_array($this->to)) {
			$this->to = array($this->to);
		}

		foreach ($this->to as $to_addr) {
			$this->wts('RCPT TO: <'.$to_addr.'>');
			if (!$this->get_return_code()) {
				return;
			}
		}
		return 1;
	}

	function send_data()
	{
		$this->wts('DATA');
		if (!$this->get_return_code(354)) {
			return;
		}

		/* This is done to ensure what we comply with RFC requiring each line to end with \r\n */
		$this->msg = preg_replace("!(\r)?\n!si", "\r\n", $this->msg);

		if( empty($this->from) ) $this->from = $GLOBALS['NOTIFY_FROM'];

		$this->wts('Subject: '.$this->subject);
		$this->wts('Date: '.date("r"));
		$this->wts('To: '.(count($this->to) == 1 ? $this->to[0] : $GLOBALS['NOTIFY_FROM']));
		$this->wts('From: '.$this->from);
		$this->wts('X-Mailer: FUDforum v'.$GLOBALS['FORUM_VERSION']);
		$this->wts($this->headers."\r\n");
		$this->wts($this->msg);
		$this->wts('.');

		return $this->get_return_code();
	}

	function close_connex()
	{
		$this->wts('quit');
		fclose($this->fs);
	}

	function send_smtp_email()
	{
		if (!$this->open_smtp_connex()) {
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_from_hdr()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_to_hdr()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_data()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}

		$this->close_connex();
	}
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
}function get_host($ip)
{
	if (!$ip || $ip == '0.0.0.0') {
		return;
	}

	$name = gethostbyaddr($ip);

	if ($name == $ip) {
		$name = substr($name, 0, strrpos($name, '.')) . '*';
	} else if (substr_count($name, '.') > 2) {
		$name = '*' . substr($name, strpos($name, '.')+1);
	}

	return $name;
}function delete_msg_index($msg_id)
{
	q('DELETE FROM phpgw_fud_index WHERE msg_id='.$msg_id);
	q('DELETE FROM phpgw_fud_title_index WHERE msg_id='.$msg_id);
}

function mb_word_split($str)
{
	$m = array();
	$lang = $GLOBALS['usr']->lang == 'chinese' ? 'EUC-CN' : 'BIG-5';

	if (function_exists('iconv')) {
		preg_match_all('!(\W)!u', @iconv($lang, 'UTF-8', $str), $m);
	} else if (function_exists('mb_convert_encoding')) {
		preg_match_all('!(\W)!u', @mb_convert_encoding($str, 'UTF-8', $lang), $m);
	}

	if (!$m) {
		return array();
	}

	$m = array_unique($m[0]);
	foreach ($m as $v) {
		if (isset($v[1])) {
			$m2[] = "'".addslashes($v)."'";
		}
	}

	return isset($m2) ? $m2 : array();
}

function index_text($subj, $body, $msg_id)
{
	/* Remove Stuff In Quotes */
	while (preg_match('!<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>(.*?)</b></td></tr><tr><td class="quote"><br>(.*?)<br></td></tr></table>!is', $body)) {
		$body = preg_replace('!<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>(.*?)</b></td></tr><tr><td class="quote"><br>(.*?)<br></td></tr></table>!is', '', $body);
	}

	/* this is mostly a hack for php verison < 4.3 because isset(string[bad offset]) returns a warning */
	error_reporting(0);

	if (strncmp($GLOBALS['usr']->lang, 'chinese', 7)) {
		$cs = array('!\W!', '!\s+!');
		$cd = array(' ', ' ');

		reverse_fmt($subj);
		$subj = trim(preg_replace($cs, $cd, strip_tags(strtolower($subj))));
		reverse_fmt($body);
		$body = trim(preg_replace($cs, $cd, strip_tags(strtolower($body))));

		/* build full text index */
		$t1 = array_unique(explode(' ', $subj));
		$t2 = array_unique(explode(' ', $body));

		foreach ($t1 as $v) {
			if (isset($v[51]) || !isset($v[3])) continue;
			$w1[] = "'".addslashes($v)."'";
		}

		if (isset($w1)) {
			$w2 = $w1;
		}

		foreach ($t2 as $v) {
			if (isset($v[51]) || !isset($v[3])) continue;
			$w2[] = "'".addslashes($v)."'";
		}
	} else { /* handling for multibyte languages */
		$w1 = mb_word_split($subj);
		if ($w1) {
			$w2 = array_merge($w1, mb_word_split($body));
		} else {
			unset($w1);
		}
	}

	if (!$w2) {
		return;
	}

	$w2 = array_unique($w2);
	if (__dbtype__ == 'mysql') {
		ins_m('phpgw_fud_search', 'word', $w2);
	} else {
		ins_m('phpgw_fud_search', 'word', $w2, 'text');
	}

	/* This allows us to return right away, meaning we don't need to wait
	 * for any locks to be released etc... */
	if (__dbtype__ == 'mysql') {
		$del = 'DELAYED';
	} else {
		$del = '';
	}

	if (isset($w1)) {
		db_li('INSERT '.$del.' INTO phpgw_fud_title_index (word_id, msg_id) SELECT id, '.$msg_id.' FROM phpgw_fud_search WHERE word IN('.implode(',', $w1).')', $ef);
	}
	db_li('INSERT '.$del.' INTO phpgw_fud_index (word_id, msg_id) SELECT id, '.$msg_id.' FROM phpgw_fud_search WHERE word IN('.implode(',', $w2).')', $ef);
}function th_add($root, $forum_id, $last_post_date, $thread_opt, $orderexpiry, $replies=0, $lpi=0)
{
	if (!$lpi) {
		$lpi = $root;
	}

	return db_qid("INSERT INTO
		phpgw_fud_thread
			(forum_id, root_msg_id, last_post_date, replies, views, rating, last_post_id, thread_opt, orderexpiry)
		VALUES
			(".$forum_id.", ".$root.", ".$last_post_date.", ".$replies.", 0, 0, ".$lpi.", ".$thread_opt.", ".$orderexpiry.")");
}

function th_move($id, $to_forum, $root_msg_id, $forum_id, $last_post_date, $last_post_id)
{
	if (!db_locked()) {
		db_lock('phpgw_fud_poll WRITE, phpgw_fud_thread_view WRITE, phpgw_fud_thread WRITE, phpgw_fud_forum WRITE, phpgw_fud_msg WRITE');
		$ll = 1;
	}
	$msg_count = q_singleval("SELECT count(*) FROM phpgw_fud_thread LEFT JOIN phpgw_fud_msg ON phpgw_fud_msg.thread_id=phpgw_fud_thread.id WHERE phpgw_fud_msg.apr=1 AND phpgw_fud_thread.id=".$id);

	q('UPDATE phpgw_fud_thread SET forum_id='.$to_forum.' WHERE id='.$id);
	q('UPDATE phpgw_fud_forum SET post_count=post_count-'.$msg_count.' WHERE id='.$forum_id);
	q('UPDATE phpgw_fud_forum SET thread_count=thread_count+1,post_count=post_count+'.$msg_count.' WHERE id='.$to_forum);
	q('DELETE FROM phpgw_fud_thread WHERE forum_id='.$to_forum.' AND root_msg_id='.$root_msg_id.' AND moved_to='.$forum_id);
	if (($aff_rows = db_affected())) {
		q('UPDATE phpgw_fud_forum SET thread_count=thread_count-'.$aff_rows.' WHERE id='.$to_forum);
	}
	q('UPDATE phpgw_fud_thread SET moved_to='.$to_forum.' WHERE id!='.$id.' AND root_msg_id='.$root_msg_id);

	q('INSERT INTO phpgw_fud_thread
		(forum_id, root_msg_id, last_post_date, last_post_id, moved_to)
	VALUES
		('.$forum_id.', '.$root_msg_id.', '.$last_post_date.', '.$last_post_id.', '.$to_forum.')');

	rebuild_forum_view($forum_id);
	rebuild_forum_view($to_forum);

	$c = q('SELECT poll_id FROM phpgw_fud_msg WHERE thread_id='.$id.' AND apr=1 AND poll_id>0');
	while ($r = db_rowarr($c)) {
		$p[] = $r[0];
	}
	unset($c);
	if (isset($p)) {
		q('UPDATE phpgw_fud_poll SET forum_id='.$to_forum.' WHERE id IN('.implode(',', $p).')');
	}

	if (isset($ll)) {
		db_unlock();
	}
}

function rebuild_forum_view($forum_id, $page=0)
{
	if (!db_locked()) {
		$ll = 1;
	        db_lock('phpgw_fud_thread_view WRITE, phpgw_fud_thread WRITE, phpgw_fud_msg WRITE, phpgw_fud_forum WRITE');
	}

	$tm = __request_timestamp__;

	/* Remove expired moved thread pointers */
	q('DELETE FROM phpgw_fud_thread WHERE forum_id='.$forum_id.' AND last_post_date<'.($tm-86400*$GLOBALS['MOVED_THR_PTR_EXPIRY']).' AND moved_to!=0');
	if (($aff_rows = db_affected())) {
		q('UPDATE phpgw_fud_forum SET thread_count=thread_count-'.$aff_rows.' WHERE id='.$forum_id);
		$page = 0;
	}

	/* De-announce expired announcments and sticky messages */
	$r = q("SELECT phpgw_fud_thread.id FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE phpgw_fud_thread.forum_id=".$forum_id." AND thread_opt>=2 AND (phpgw_fud_msg.post_stamp+phpgw_fud_thread.orderexpiry)<=".$tm);
	while ($tid = db_rowarr($r)) {
		q("UPDATE phpgw_fud_thread SET orderexpiry=0, thread_opt=thread_opt & ~ (2|4) WHERE id=".$tid[0]);
	}
	unset($r);

	if (__dbtype__ == 'pgsql') {
		$tmp_tbl_name = "phpgw_fud_ftvt_".get_random_value();
		q("CREATE TEMP TABLE ".$tmp_tbl_name." ( forum_id INT NOT NULL, page INT NOT NULL, thread_id INT NOT NULL, pos SERIAL, tmp INT )");

		if ($page) {
			q("DELETE FROM phpgw_fud_thread_view WHERE forum_id=".$forum_id." AND page<".($page+1));
			q("INSERT INTO ".$tmp_tbl_name." (thread_id,forum_id,page,tmp) SELECT phpgw_fud_thread.id, phpgw_fud_thread.forum_id, 2147483647, CASE WHEN thread_opt>=2 AND (phpgw_fud_msg.post_stamp+phpgw_fud_thread.orderexpiry>".$tm." OR phpgw_fud_thread.orderexpiry=0) THEN 2147483647 ELSE phpgw_fud_thread.last_post_date END AS sort_order_fld  FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE forum_id=".$forum_id." AND phpgw_fud_msg.apr=1 ORDER BY sort_order_fld DESC, phpgw_fud_thread.last_post_id DESC LIMIT ".($GLOBALS['THREADS_PER_PAGE']*$page));
		} else {
			q("DELETE FROM phpgw_fud_thread_view WHERE forum_id=".$forum_id);
			q("INSERT INTO ".$tmp_tbl_name." (thread_id,forum_id,page,tmp) SELECT phpgw_fud_thread.id, phpgw_fud_thread.forum_id, 2147483647, CASE WHEN thread_opt>=2 AND (phpgw_fud_msg.post_stamp+phpgw_fud_thread.orderexpiry>".$tm." OR phpgw_fud_thread.orderexpiry=0) THEN 2147483647 ELSE phpgw_fud_thread.last_post_date END AS sort_order_fld  FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE forum_id=".$forum_id." AND phpgw_fud_msg.apr=1 ORDER BY sort_order_fld DESC, phpgw_fud_thread.last_post_id DESC");
		}

		q("INSERT INTO phpgw_fud_thread_view (thread_id,forum_id,page,pos) SELECT thread_id,forum_id,CEIL(pos/".$GLOBALS['THREADS_PER_PAGE'].".0),(pos-(CEIL(pos/".$GLOBALS['THREADS_PER_PAGE'].".0)-1)*".$GLOBALS['THREADS_PER_PAGE'].") FROM ".$tmp_tbl_name);
		q("DROP TABLE ".$tmp_tbl_name);
		return;
	} else if (__dbtype__ == 'mysql') {
		if ($page) {
			q('DELETE FROM phpgw_fud_thread_view WHERE forum_id='.$forum_id.' AND page<'.($page+1));
			q("INSERT INTO phpgw_fud_thread_view (thread_id,forum_id,page,tmp) SELECT phpgw_fud_thread.id, phpgw_fud_thread.forum_id, 2147483645, CASE WHEN thread_opt>=2 AND (phpgw_fud_msg.post_stamp+phpgw_fud_thread.orderexpiry>".$tm." OR phpgw_fud_thread.orderexpiry=0) THEN 4294967294 ELSE phpgw_fud_thread.last_post_date END AS sort_order_fld  FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE forum_id=".$forum_id." AND phpgw_fud_msg.apr=1 ORDER BY sort_order_fld DESC, phpgw_fud_thread.last_post_id DESC LIMIT 0, ".($GLOBALS['THREADS_PER_PAGE']*$page));
			q('UPDATE phpgw_fud_thread_view SET page=CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].'), pos=pos-(CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].')-1)*'.$GLOBALS['THREADS_PER_PAGE'].' WHERE forum_id='.$forum_id.' AND page=2147483645');
		} else {
			q('DELETE FROM phpgw_fud_thread_view WHERE forum_id='.$forum_id);
			q("INSERT INTO phpgw_fud_thread_view (thread_id,forum_id,page,tmp) SELECT phpgw_fud_thread.id, phpgw_fud_thread.forum_id, 2147483645, CASE WHEN thread_opt>=2 AND (phpgw_fud_msg.post_stamp+phpgw_fud_thread.orderexpiry>".$tm." OR phpgw_fud_thread.orderexpiry=0) THEN 4294967294 ELSE phpgw_fud_thread.last_post_date END AS sort_order_fld  FROM phpgw_fud_thread INNER JOIN phpgw_fud_msg ON phpgw_fud_thread.root_msg_id=phpgw_fud_msg.id WHERE forum_id=".$forum_id." AND phpgw_fud_msg.apr=1 ORDER BY sort_order_fld DESC, phpgw_fud_thread.last_post_id DESC");
			q('UPDATE phpgw_fud_thread_view SET page=CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].'), pos=pos-(CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].')-1)*'.$GLOBALS['THREADS_PER_PAGE'].' WHERE forum_id='.$forum_id);
		}
	}

	if (isset($ll)) {
		db_unlock();
	}
}function pager_replace(&$str, $s, $c)
{
	$str = str_replace('%s', $s, $str);
	$str = str_replace('%c', $c, $str);
}

function tmpl_create_pager($start, $count, $total, $arg, $suf='', $append=1, $js_pager=false)
{
	if (!$count) {
		$count =& $GLOBALS['POSTS_PER_PAGE'];
	}
	if ($total <= $count) {
		return;
	}

	$cur_pg = ceil($start / $count);
	$ttl_pg = ceil($total / $count);

	$page_pager_data = '';

	if (($page_start = $start - $count) > -1) {
		if ($append) {
			$page_first_url = $arg.'&amp;start=0'.$suf;
			$page_prev_url = $arg.'&amp;start='.$page_start.$suf;
		} else {
			$page_first_url = $page_prev_url = $arg;
			pager_replace($page_first_url, 0, $count);
			pager_replace($page_prev_url, $page_start, $count);
		}

		$page_pager_data .= !$js_pager ? '&nbsp;<a href="'.$page_first_url.'" class="PagerLink">&laquo;</a>&nbsp;&nbsp;<a href="'.$page_prev_url.'" class="PagerLink">&lt;</a>&nbsp;&nbsp;' : '&nbsp;<a href="javascript://" onClick="'.$page_first_url.'" class="PagerLink">&laquo;</a>&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_prev_url.'" class="PagerLink">&lt;</a>&nbsp;&nbsp;';
	}

	$mid = ceil($GLOBALS['GENERAL_PAGER_COUNT'] / 2);

	if ($ttl_pg > $GLOBALS['GENERAL_PAGER_COUNT']) {
		if (($mid + $cur_pg) >= $ttl_pg) {
			$end = $ttl_pg;
			$mid += $mid + $cur_pg - $ttl_pg;
			$st = $cur_pg - $mid;
		} else if (($cur_pg - $mid) <= 0) {
			$st = 0;
			$mid += $mid - $cur_pg;
			$end = $mid + $cur_pg;
		} else {
			$st = $cur_pg - $mid;
			$end = $mid + $cur_pg;
		}

		if ($st < 0) {
			$start = 0;
		}
		if ($end > $ttl_pg) {
			$end = $ttl_pg;
		}
	} else {
		$end = $ttl_pg;
		$st = 0;
	}

	while ($st < $end) {
		if ($st != $cur_pg) {
			$page_start = $st * $count;
			if ($append) {
				$page_page_url = $arg.'&amp;start='.$page_start.$suf;
			} else {
				$page_page_url = $arg;
				pager_replace($page_page_url, $page_start, $count);
			}
			$st++;
			$page_pager_data .= !$js_pager ? '<a href="'.$page_page_url.'" class="PagerLink">'.$st.'</a>&nbsp;&nbsp;' : '<a href="javascript://" onClick="'.$page_page_url.'" class="PagerLink">'.$st.'</a>&nbsp;&nbsp;';
		} else {
			$st++;
			$page_pager_data .= !$js_pager ? $st.'&nbsp;&nbsp;' : $st.'&nbsp;&nbsp;';
		}
	}

	$page_pager_data = substr($page_pager_data, 0 , strlen((!$js_pager ? '&nbsp;&nbsp;' : '&nbsp;&nbsp;')) * -1);

	if (($page_start = $start + $count) < $total) {
		$page_start_2 = ($st - 1) * $count;
		if ($append) {
			$page_next_url = $arg.'&amp;start='.$page_start.$suf;
			$page_last_url = $arg.'&amp;start='.$page_start_2.$suf;
		} else {
			$page_next_url = $page_last_url = $arg;
			pager_replace($page_next_url, $page_start, $count);
			pager_replace($page_last_url, $page_start_2, $count);
		}
		$page_pager_data .= !$js_pager ? '&nbsp;&nbsp;<a href="'.$page_next_url.'" class="PagerLink">&gt;</a>&nbsp;&nbsp;<a href="'.$page_last_url.'" class="PagerLink">&raquo;</a>' : '&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_next_url.'" class="PagerLink">&gt;</a>&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_last_url.'" class="PagerLink">&raquo;</a>';
	}

	return !$js_pager ? '<font class="SmallText"><b>Pagine ('.$ttl_pg.'): 
['.$page_pager_data.']
</b></font>' : '<font class="SmallText"><b>Pagine ('.$ttl_pg.'): 
['.$page_pager_data.']
</b></font>';
}/* Handle poll votes if any are present */
function register_vote(&$options, $poll_id, $opt_id, $mid)
{
	/* invalid option or previously voted */
	if (!isset($options[$opt_id]) || q_singleval('SELECT id FROM phpgw_fud_poll_opt_track WHERE poll_id='.$poll_id.' AND user_id='._uid)) {
		return;
	}

	if (db_li('INSERT INTO phpgw_fud_poll_opt_track(poll_id, user_id, poll_opt) VALUES('.$poll_id.', '._uid.', '.$opt_id.')', $a)) {
		q('UPDATE phpgw_fud_poll_opt SET count=count+1 WHERE id='.$opt_id);
		q('UPDATE phpgw_fud_poll SET total_votes=total_votes+1 WHERE id='.$poll_id);
		poll_cache_rebuild($opt_id, $options);
		q('UPDATE phpgw_fud_msg SET poll_cache='.strnull(addslashes(@serialize($options))).' WHERE id='.$mid);
	}

	return 1;
}

$query_type = (empty($_POST['poll_opt']) || !($_POST['poll_opt'] = (int)$_POST['poll_opt']) ? 'uq' : 'q');

/* needed for message threshold & reveling messages */
if (isset($_GET['rev'])) {
	$tmp = explode(':', $_GET['rev']);
	foreach ($tmp as $v) {
		$GLOBALS['__FMDSP__'][$v] = 1;
	}
	define('reveal_lnk', '&amp;rev=' . $_GET['rev']);
} else {
	define('reveal_lnk', '');
}

/* initialize buddy & ignore list for registered users */
if (_uid) {
	if ($usr->buddy_list) {
		$usr->buddy_list = @unserialize($usr->buddy_list);
	}
	if ($usr->ignore_list) {
		$usr->ignore_list = @unserialize($usr->ignore_list);
	}

	/* handle temporarily un-hidden users */
	if (isset($_GET['reveal'])) {
		$tmp = explode(':', $_GET['reveal']);
		foreach($tmp as $v) {
			if (isset($usr->ignore_list[$v])) {
				$usr->ignore_list[$v] = 0;
			}
		}
		define('unignore_tmp', '&amp;reveal='.$_GET['reveal']);
	} else {
		define('unignore_tmp', '');
	}
} else {
	define('unignore_tmp', '');
}

if ($GLOBALS['FUD_OPT_2'] & 2048) {
	$GLOBALS['affero_domain'] = parse_url($WWW_ROOT);
	$GLOBALS['affero_domain'] = $GLOBALS['affero_domain']['host'];
}

$_SERVER['QUERY_STRING_ENC'] = str_replace('&', '&amp;', $_SERVER['QUERY_STRING']);

function make_tmp_unignore_lnk($id)
{
	if (!isset($_GET['reveal'])) {
		return $_SERVER['QUERY_STRING_ENC'] . '&amp;reveal='.$id;
	} else {
		return str_replace('&amp;reveal='.$_GET['reveal'], unignore_tmp . ':' . $id, $_SERVER['QUERY_STRING_ENC']);
	}
}

function make_reveal_link($id)
{
	if (!isset($GLOBALS['__FMDSP__'])) {
		return $_SERVER['QUERY_STRING_ENC'] . '&amp;rev='.$id;
	} else {
		return str_replace('&amp;rev='.$_GET['rev'], reveal_lnk . ':' . $id, $_SERVER['QUERY_STRING_ENC']);
	}
}

/* Draws a message, needs a message object, user object, permissions array,
 * flag indicating wether or not to show controls and a variable indicating
 * the number of the current message (needed for cross message pager)
 * last argument can be anything, allowing forms to specify various vars they
 * need to.
 */
function tmpl_drawmsg($obj, $usr, $perms, $hide_controls, &$m_num, $misc)
{
	$o1 =& $GLOBALS['FUD_OPT_1'];
	$o2 =& $GLOBALS['FUD_OPT_2'];
	$a =& $obj->users_opt;
	$b =& $usr->users_opt;
	$c =& $obj->level_opt;

	/* draw next/prev message controls */
	if (!$hide_controls && $misc) {
		/* tree view is a special condition, we only show 1 message per page */
		if ($_GET['t'] == 'tree') {
			$prev_message = $misc[0] ? '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;mid='.$misc[0].'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/italian/images/up.png" title="Messaggio precedente" alt="Messaggio precedente" width=16 height=11 /></a>' : '';
			$next_message = $misc[1] ? '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;mid='.$misc[1].'" class="GenLink"><img alt="Messaggio precedente" title="Messaggio successivo" src="/egroupware/fudforum/3814588639/theme/italian/images/down.png" width=16 height=11 /></a>' : '';
			$next_page = '';
		} else {
			/* handle previous link */
			if (!$m_num && $obj->id > $obj->root_msg_id) { /* prev link on different page */
				$msg_start = $misc[0] - $misc[1];
				$prev_message = '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.$msg_start.reveal_lnk.unignore_tmp.'" class="GenLink"><img src="/egroupware/fudforum/3814588639/theme/italian/images/up.png" title="Messaggio precedente" alt="Messaggio precedente" width=16 height=11 /></a>';
			} else if ($m_num) { /* inline link, same page */
				$msg_num = $m_num;
				$prev_message = '<a href="#msg_num_'.$msg_num.'" class="GenLink"><img alt="Messaggio precedente" title="Messaggio precedente" src="/egroupware/fudforum/3814588639/theme/italian/images/up.png" width=16 height=11 /></a>';
			} else {
				$prev_message = '';
			}

			/* handle next link */
			if ($obj->id < $obj->last_post_id) {
				if ($m_num && !($misc[1] - $m_num - 1)) { /* next page link */
					$msg_start = $misc[0] + $misc[1];
					$next_message = '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.$msg_start.reveal_lnk.unignore_tmp.'" class="GenLink"><img alt="Messaggio precedente" title="Messaggio successivo" src="/egroupware/fudforum/3814588639/theme/italian/images/down.png" width=16 height=11 /></a>';
					$next_page = '<a href="/egroupware/fudforum/3814588639/index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.$msg_start.reveal_lnk.unignore_tmp.'" class="GenLink">Pagina successiva <img src="/egroupware/fudforum/3814588639/theme/italian/images/goto.gif" alt="" /></a>';
				} else {
					$msg_num = $m_num + 2;
					$next_message = '<a href="#msg_num_'.$msg_num.'" class="GenLink"><img alt="Messaggio successivo" title="Messaggio successivo" src="/egroupware/fudforum/3814588639/theme/italian/images/down.png" width=16 height=11 /></a>';
					$next_page = '';
				}
			} else {
				$next_page = $next_message = '';
			}
		}
		$m_num++;
	} else {
		$next_page = $next_message = $prev_message = '';
	}

	if (!$obj->user_id) {
		$user_login =& $GLOBALS['ANON_NICK'];
		$user_login_td = $GLOBALS['ANON_NICK'].' è ignorato&nbsp;';
	} else {
		$user_login =& $obj->login;
		$user_login_td = 'Il messaggio di <a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&'._rsid.'&id='.$obj->user_id.'" class="GenLink">'.$obj->login.'</a> viene ignorato&nbsp;';
	}

	/* check if the message should be ignored and it is not temporarily revelead */
	if ($usr->ignore_list && !empty($usr->ignore_list[$obj->poster_id]) && !isset($GLOBALS['__FMDSP__'][$obj->id])) {
		$rev_url = make_reveal_link($obj->id);
		$un_ignore_url = make_tmp_unignore_lnk($obj->poster_id);
		return !$hide_controls ? '<tr><td>
<table border=0 cellspacing=0 cellpadding=0 class="MsgTable">
<tr>
<td align="left" class="MsgIg">
<a name="msg_num_'.$m_num.'"></a>
<a name="msg_'.$obj->id.'"></a>
'.$user_login_td.'
[<a href="/egroupware/fudforum/3814588639/index.php?'.$rev_url.'" class="GenLink">mostra messaggio</a>]&nbsp;
[<a href="/egroupware/fudforum/3814588639/index.php?'.$un_ignore_url.'" class="GenLink">mostra tutti i messaggi di '.$user_login.'</a>]&nbsp;
[<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;del='.$obj->poster_id.'&amp;redr=1&amp;'._rsid.'" class="GenLink">smetti di ignorare questo utente</a>]</td>
<td class="MsgIg" align="right">'.$prev_message.$next_message.'</td></tr>
</table></td></tr>' : '<tr class="MsgR1">
<td class="GenText"><a name="msg_num_'.$m_num.'"></a> <a name="msg_'.$obj->id.'"></a>Post by '.$user_login.' is ignored&nbsp;</td>
</tr>';
	}

	if ($obj->user_id) {
		if (!$hide_controls) {
			$custom_tag = $obj->custom_status ? '<br />'.$obj->custom_status : '';

			if ($obj->avatar_loc && $a & 8388608 && $b & 8192 && $o1 & 28 && !($c & 2)) {
				if (!($c & 1)) {
					$level_name =& $obj->level_name;
					$level_image = $obj->level_img ? '&nbsp;<img src="images/'.$obj->level_img.'" alt="" />' : '';
				} else {
					$level_name = $level_image = '';
				}
			} else {
				$level_image = $obj->level_img ? '&nbsp;<img src="images/'.$obj->level_img.'" alt="" />' : '';
				$obj->avatar_loc = '';
				$level_name =& $obj->level_name;
			}
			$avatar = ($obj->avatar_loc || $level_image) ? '<td class="avatarPad" width="1">'.$obj->avatar_loc.$level_image.'</td>' : '';
			$dmsg_tags = ($custom_tag || $level_name) ? '<div class="ctags">'.$level_name.$custom_tag.'</div>' : '';

			if (($o2 & 32 && !($a & 32768)) || $b & 1048576) {
				$online_indicator = (($obj->time_sec + $GLOBALS['LOGEDIN_TIMEOUT'] * 60) > __request_timestamp__) ? '<img src="/egroupware/fudforum/3814588639/theme/italian/images/online.gif" alt="'.$user_login.' è attualmente online" title="'.$user_login.' è attualmente online" />&nbsp;' : '<img src="/egroupware/fudforum/3814588639/theme/italian/images/offline.gif" alt="'.$user_login.'  è attualmente offline" title="'.$user_login.'  è attualmente offline" />&nbsp;';
			} else {
				$online_indicator = '';
			}

			$user_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'">'.$user_login.'</a>';

			if ($obj->location) {
				if (strlen($obj->location) > $GLOBALS['MAX_LOCATION_SHOW']) {
					$location = substr($obj->location, 0, $GLOBALS['MAX_LOCATION_SHOW']) . '...';
				} else {
					$location =& $obj->location;
				}
				$location = '<br /><b>Località: </b>'.$location;
			} else {
				$location = '';
			}

			if (_uid && _uid != $obj->user_id) {
				$buddy_link	= !isset($usr->buddy_list[$obj->user_id]) ? '<a href="/egroupware/fudforum/3814588639/index.php?t=buddy_list&amp;add='.$obj->user_id.'&amp;'._rsid.'" class="GenLink">aggiungi alla buddy list</a><br />' : '<a href="/egroupware/fudforum/3814588639/index.php?t=buddy_list&amp;del='.$obj->user_id.'&amp;redr=1&amp;'._rsid.'" class="GenLink">remove from buddy list</a><br />';
				$ignore_link	= !isset($usr->ignore_list[$obj->user_id]) ? '<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;add='.$obj->user_id.'&amp;'._rsid.'" class="GenLink">ignora tutti i messaggi<br>di questo utente</a>' : '<a href="/egroupware/fudforum/3814588639/index.php?t=ignore_list&amp;del='.$obj->user_id.'&amp;redr=1&amp;'._rsid.'" class="GenLink">smetti di ignorare i messaggi di questo utente</a>';
				$dmsg_bd_il	= $buddy_link.$ignore_link.'<br />';
			} else {
				$dmsg_bd_il = '';
			}

			/* show im buttons if need be */
			if ($b & 16384) {
				$im_icq		= $obj->icq ? '<a href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->poster_id.'&amp;'._rsid.'#icq_msg"><img title="'.$obj->icq.'" src="/egroupware/fudforum/3814588639/theme/italian/images/icq.gif" alt="" /></a>' : '';
				$im_aim		= $obj->aim ? '<a href="aim:goim?screenname='.$obj->aim.'&amp;message=Hi.+Are+you+there?" target="_blank"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/aim.gif" title="'.$obj->aim.'" /></a>' : '';
				$im_yahoo	= $obj->yahoo ? '<a target="_blank" href="http://edit.yahoo.com/config/send_webmesg?.target='.$obj->yahoo.'&amp;.src=pg"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/yahoo.gif" title="'.$obj->yahoo.'" /></a>' : '';
				$im_msnm	= $obj->msnm ? '<a href="mailto: '.$obj->msnm.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msnm.gif" title="'.$obj->msnm.'" /></a>' : '';
				$im_jabber	= $obj->jabber ? '<img src="/egroupware/fudforum/3814588639/theme/italian/images/jabber.gif" title="'.$obj->jabber.'" alt="" />' : '';
				if ($o2 & 2048) {
					$im_affero = $obj->affero ? '<a href="http://svcs.affero.net/rm.php?r='.$obj->affero.'&amp;ll='.$obj->forum_id.'.'.$GLOBALS['affero_domain'].'&amp;lp='.$obj->forum_id.'.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/affero_reg.gif" /></a>' : '<a href="http://svcs.affero.net/rm.php?m='.urlencode($obj->email).'&amp;ll='.$obj->forum_id.'.'.$GLOBALS['affero_domain'].'&amp;lp='.$obj->forum_id.'.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/affero_noreg.gif" /></a>';
				} else {
					$im_affero = '';
				}
				$dmsg_im_row = ($im_icq || $im_aim || $im_yahoo || $im_msnm || $im_jabber || $im_affero) ? $im_icq.' '.$im_aim.' '.$im_yahoo.' '.$im_msnm.' '.$im_jabber.' '.$im_affero.'<br />' : '';
			} else {
				$dmsg_im_row = '';
			}
		 } else {
		 	$user_link = $user_login;
		 	$dmsg_tags = $dmsg_im_row = $dmsg_bd_il = $location = $online_indicator = $avatar = '';
		 }
	} else {
		$user_link = $user_login;
		$dmsg_tags = $dmsg_im_row = $dmsg_bd_il = $location = $online_indicator = $avatar = '';
	}

	/* Display message body
	 * If we have message threshold & the entirity of the post has been revelead show a preview
	 * otherwise if the message body exists show an actual body
	 * if there is no body show a 'no-body' message
	 */
	if (!$hide_controls && $obj->message_threshold && $obj->length_preview && $obj->length > $obj->message_threshold && !isset($GLOBALS['__FMDSP__'][$obj->id])) {
		$rev_url = make_reveal_link($obj->id);
		$msg_body = read_msg_body($obj->offset_preview, $obj->length_preview, $obj->file_id_preview);
		$msg_body = '<font class="MsgBodyText">'.$msg_body.'</font>
<br /><div align="center">[<a href="/egroupware/fudforum/3814588639/index.php?'.$rev_url.'" class="GenLink">Mostra il resto del messaggio</a>]</div>';
	} else if ($obj->length) {
		$msg_body = read_msg_body($obj->foff, $obj->length, $obj->file_id);
		$msg_body = '<font class="MsgBodyText">'.$msg_body.'</font>';
	} else {
		$msg_body = 'Non c&#39;è il corpo del messaggio!';
	}

	if ($obj->poll_cache) {
		$obj->poll_cache = @unserialize($obj->poll_cache);
	}

	/* handle poll votes */
	if (!empty($_POST['poll_opt']) && ($_POST['poll_opt'] = (int)$_POST['poll_opt']) && !($obj->thread_opt & 1) && $perms & 512) {
		if (register_vote($obj->poll_cache, $obj->poll_id, $_POST['poll_opt'], $obj->id)) {
			$obj->total_votes += 1;
			$obj->cant_vote = 1;
		}
		unset($_GET['poll_opt']);
	}

	/* display poll if there is one */
	if ($obj->poll_id && $obj->poll_cache) {
		/* we need to determine if we allow the user to vote or see poll results */
		$show_res = 1;

		if (isset($_GET['pl_view']) && !isset($_POST['pl_view'])) {
			$_POST['pl_view'] = $_GET['pl_view'];
		}

		/* various conditions that may prevent poll voting */
		if (!$hide_controls && !$obj->cant_vote && (!isset($_POST['pl_view']) || $_POST['pl_view'] != $obj->poll_id)) {
			if ($perms & 512 && (!($obj->thread_opt & 1) || $perms & 4096)) {
				if (!$obj->expiry_date || ($obj->creation_date + $obj->expiry_date) > __request_timestamp__) {
					/* check if the max # of poll votes was reached */
					if (!$obj->max_votes || $obj->total_votes < $obj->max_votes) {
						$show_res = 0;
					}
				}
			}
		}

		$i = 0;

		$poll_data = '';
		foreach ($obj->poll_cache as $k => $v) {
			$i++;
			if ($show_res) {
				$length = ($v[1] && $obj->total_votes) ? round($v[1] / $obj->total_votes * 100) : 0;
				$poll_data .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td>'.$v[0].'</td><td><img src="/egroupware/fudforum/3814588639/theme/italian/images/poll_pix.gif" alt="" height="10" width="'.$length.'" /> '.$v[1].' / '.$length.'%</td></tr>';
			} else {
				$poll_data .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td colspan=2><input type="radio" name="poll_opt" value="'.$k.'">&nbsp;&nbsp;'.$v[0].'</td></tr>';
			}
		}

		if (!$show_res) {
			$view_poll_results_button = $obj->total_votes ? '<input type="submit" class="button" name="pl_res" value="Visualizza risultati">' : '';
			$poll_buttons = '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td colspan=3 align="right"><input type="submit" class="button" name="pl_vote" value="Vota">&nbsp;'.$view_poll_results_button.'</td></tr>';
			$poll = '<p>
<form action="/egroupware/fudforum/3814588639/index.php?'.$_SERVER['QUERY_STRING'].'#msg_'.$obj->id.'" method="post">'._hs.'
<table border=0 cellspacing=1 cellpadding=2 class="PollTable">
<tr><th nowrap colspan=3>'.$obj->poll_name.'<img src="blank.gif" alt="" height=1 width=10 /><font size="-1">[ '.$obj->total_votes.' voti ]</font></th></tr>
'.$poll_data.'
'.$poll_buttons.'
</table><input type="hidden" name="pl_view" value="'.$obj->poll_id.'"></form><p>';
		} else {
			$poll = '<p><table border=0 cellspacing=1 cellpadding=2 class="PollTable">
<tr><th nowrap colspan=3>'.$obj->poll_name.'<img src="blank.gif" alt="" height=1 width=10 /><font size="-1">[ '.$obj->total_votes.' voti ]</font></th></tr>
'.$poll_data.'
</table><p>';
		}
	} else {
		$poll = '';
	}

	/* draw file attachments if there are any */
	$drawmsg_file_attachments = '';
	if ($obj->attach_cnt && !empty($obj->attach_cache)) {
		$atch = @unserialize($obj->attach_cache);
		if (is_array($atch) && count($atch)) {
			foreach ($atch as $v) {
				$sz = $v[2] / 1024;
				$sz = $sz < 1000 ? number_format($sz, 2).'KB' : number_format($sz/1024, 2).'MB';
				$drawmsg_file_attachments .= '<tr>
<td valign="middle"><a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=getfile&amp;id='.$v[0].'&amp;'._rsid.'"><img alt="" src="images/mime/'.$v[4].'" /></a></td>
<td><font class="GenText"><b>Attachment:</b></font> <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=getfile&amp;id='.$v[0].'&amp;'._rsid.'">'.$v[1].'</a><br />
<font class="SmallText">(Dimensione: '.$sz.', Scaricato '.$v[3].' volte)</font></td></tr>';
			}
			$drawmsg_file_attachments = '<p>
<table border=0 cellspacing=0 cellpadding=2>
'.$drawmsg_file_attachments.'
</table>';
		}
	}

	/* Determine if the message was updated and if this needs to be shown */
	if ($obj->update_stamp) {
		if ($obj->updated_by != $obj->poster_id && $o1 & 67108864) {
			$modified_message = '<p>[Aggiornato il : '.strftime("%a, %d %B %Y %H:%M", $obj->update_stamp).'] dal moderatore';
		} else if ($obj->updated_by == $obj->poster_id && $o1 & 33554432) {
			$modified_message = '<p>[Aggiornato il : '.strftime("%a, %d %B %Y %H:%M", $obj->update_stamp).']';
		} else {
			$modified_message = '';
		}
	} else {
		$modified_message = '';
	}

	$rpl = '';
	if (!$hide_controls) {
		$ip_address = ($b & 1048576 || $usr->md || $o1 & 134217728) ? '<b>IP:</b> <a href="http://www.nic.com/cgi-bin/whois.cgi?query='.$obj->ip_addr.'" target="_blank">'.$obj->ip_addr.'</a>' : '';
		$host_name = ($obj->host_name && $o1 & 268435456) ? '<b>Da:</b> '.$obj->host_name.'<br />' : '';
		$msg_icon = !$obj->icon ? '' : '<img src="images/message_icons/'.$obj->icon.'" alt="'.$obj->icon.'" />&nbsp;&nbsp;';
		$signature = ($obj->sig && $o1 & 32768 && $obj->msg_opt & 1 && $b & 4096) ? '<p><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />'.$obj->sig : '';

		$report_to_mod_link = '<div align="right"><font class="SmallText">[<a class="SmallText" href="/egroupware/fudforum/3814588639/index.php?t=report&amp;msg_id='.$obj->id.'&amp;'._rsid.'">Segnala il messaggio al moderatore</a>]</font></div>';

		if ($obj->reply_to && $obj->reply_to != $obj->id && $o2 & 536870912) {
			if ($_GET['t'] != 'tree' && $_GET['t'] != 'msg') {
				$lnk = d_thread_view;
			} else {
				$lnk =& $_GET['t'];
			}
			$rpl = '<font class="small"> [ <a href="/egroupware/fudforum/3814588639/index.php?t='.$lnk.'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;goto='.$obj->reply_to.'" class="small">is a reply to message #'.$obj->reply_to.'</a> ]</font>';
		}

		if ($obj->user_id) {
			$user_profile = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_about.gif" /></a>';
			$email_link = ($o1 & 4194304 && $a & 16) ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=email&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_email.gif" /></a>' : '';
			$private_msg_link = $o1 & 1024 ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=ppost&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img alt="Invia un messaggi privato a questo utente" title="Invia un messaggi privato a questo utente" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_pm.gif" /></a>' : '';
			$dmsg_user_info = '<br /><b>Messaggi:</b> '.$obj->posted_msg_count.'<br />
<b>Registrato:</b> '.strftime("%B %Y", $obj->join_date).'
'.$location;
		} else {
			$user_profile = $email_link = $private_msg_link = '';
			$dmsg_user_info = ($host_name || $ip_address) ? '' : '';
		}

		/* little trick, this variable will only be avaliable if we have a next link leading to another page */
		if (isset($next_page)) {
			$next_page = '&nbsp;';
		}

		$delete_link = $perms & 32 ? '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=mmod&amp;del='.$obj->id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_delete.gif" /></a>&nbsp;' : '';

		if ($perms & 16 || (_uid == $obj->poster_id && (!$GLOBALS['EDIT_TIME_LIMIT'] || __request_timestamp__ - $obj->post_stamp < $GLOBALS['EDIT_TIME_LIMIT'] * 60))) {
			$edit_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;msg_id='.$obj->id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_edit.gif" /></a>&nbsp;&nbsp;&nbsp;&nbsp;';
		} else {
			$edit_link = '';
		}

		if (!($obj->thread_opt & 1) || $perms & 4096) {
			$reply_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;reply_to='.$obj->id.'&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_reply.gif" /></a>&nbsp;';
			$quote_link = '<a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=post&amp;reply_to='.$obj->id.'&amp;quote=true&amp;'._rsid.'"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/msg_quote.gif" /></a>';
		} else {
			$reply_link = $quote_link = '';
		}

		$message_toolbar = '<tr><td colspan="2" class="MsgToolBar"><table border=0 cellspacing=0 cellpadding=0 width="100%"><tr>
<td nowrap align="left">'.$user_profile.'&nbsp;'.$email_link.'&nbsp;'.$private_msg_link.'</td>
<td width="100%" align="center" class="GenText">'.$next_page.'</td>
<td nowrap align="right">'.$delete_link.$edit_link.$reply_link.$quote_link.'</td>
</tr></table></td></tr>';
	} else {
		$host_name = $ip_address = $dmsg_user_info = $msg_icon = $signature = $report_to_mod_link = $message_toolbar = '';
	}

	return '<tr><td class="MsgSpacer"><table cellspacing=0 cellpadding=0 class="MsgTable">
<tr>
<td valign="top" align="left" class="MsgR1"><font class="MsgSubText"><a name="msg_num_'.$m_num.'"></a><a name="msg_'.$obj->id.'"></a>'.$msg_icon.$obj->subject.$rpl.'</font></td>
<td valign="top" align="right" class="MsgR1"><font class="DateText">'.strftime("%a, %d %B %Y %H:%M", $obj->post_stamp).'</font> '.$prev_message.$next_message.'</td>
</tr>
<tr class="MsgR2"><td class="MsgR2" colspan=2><table border="0" cellspacing="0" cellpadding="0" class="ContentTable">
<tr class="MsgR2">

'.$avatar.'
<td class="msgud">'.$online_indicator.$user_link.$dmsg_user_info.'</td>
<td class="msgud">'.$dmsg_tags.'</td>
<td class="msgot">'.$dmsg_bd_il.$dmsg_im_row.$host_name.$ip_address.'</td>
</tr></table></td>
</tr>
<tr><td colspan="2" class="MsgR3">
'.$poll.$msg_body.$drawmsg_file_attachments.'
'.$modified_message.$signature.$report_to_mod_link.'
</td></tr>
'.$message_toolbar.'
</table></td></tr>';
}function alt_var($key)
{
	if (!isset($GLOBALS['_ALTERNATOR_'][$key])) {
		$args = func_get_args(); array_shift($args);
		$GLOBALS['_ALTERNATOR_'][$key] = array('p' => 1, 't' => count($args), 'v' => $args);
		return $args[0];
	}
	$k =& $GLOBALS['_ALTERNATOR_'][$key];
	if ($k['p'] == $k['t']) {
		$k['p'] = 0;
	}
	return $k['v'][$k['p']++];
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
}

	$pl_id = 0;
	$old_subject = $attach_control_error = '';

	/* redirect user where need be in moderated forums after they've seen the moderation message. */
	if (isset($_POST['moderated_redr'])) {
		check_return($usr->returnto);
	}

	/* we do this because we don't want to take a chance that data is passed via cookies */
	if (isset($_GET['reply_to']) || isset($_POST['reply_to'])) {
		$reply_to = (int) (isset($_GET['reply_to']) ? $_GET['reply_to'] : $_POST['reply_to']);
	} else {
		$reply_to = 0;
	}
	if (isset($_GET['msg_id']) || isset($_POST['msg_id'])) {
		$msg_id = (int) (isset($_GET['msg_id']) ? $_GET['msg_id'] : $_POST['msg_id']);
	} else {
		$msg_id = 0;
	}
	if (isset($_GET['th_id']) || isset($_POST['th_id'])) {
		$th_id = (int) (isset($_GET['th_id']) ? $_GET['th_id'] : $_POST['th_id']);
	} else {
		$th_id = 0;
	}
	if (isset($_GET['frm_id']) || isset($_POST['frm_id'])) {
		$frm_id = (int) (isset($_GET['frm_id']) ? $_GET['frm_id'] : $_POST['frm_id']);
	} else {
		$frm_id = 0;
	}

	/* replying or editing a message */
	if ($reply_to || $msg_id) {
		$msg = msg_get(($reply_to ? $reply_to : $msg_id));
	 	$th_id = $msg->thread_id;
	 	$msg->login = q_singleval('SELECT alias FROM phpgw_fud_users WHERE id='.$msg->poster_id);
	}

	if ($th_id) {
		$thr = db_sab('SELECT t.forum_id, t.replies, t.thread_opt, t.root_msg_id, t.orderexpiry, m.subject FROM phpgw_fud_thread t INNER JOIN phpgw_fud_msg m ON t.root_msg_id=m.id WHERE t.id='.$th_id);
		if (!$thr) {
			invl_inp_err();
		}
		$frm_id = $thr->forum_id;
	} else if ($frm_id) {
		$th_id = null;
	} else {
		std_error('systemerr');
	}
	$frm = db_sab('SELECT id, name, max_attach_size, forum_opt, max_file_attachments, post_passwd, message_threshold FROM phpgw_fud_forum WHERE id='.$frm_id);
	$frm->forum_opt = (int) $frm->forum_opt;

	/* fetch permissions & moderation status */
	$MOD = (int) ($usr->users_opt & 1048576 || ($usr->users_opt & 524288 && is_moderator($frm->id, _uid)));
	$perms = perms_from_obj(db_sab('SELECT group_cache_opt, '.$MOD.' as md FROM phpgw_fud_group_cache WHERE user_id IN('._uid.',2147483647) AND resource_id='.$frm->id.' ORDER BY user_id ASC LIMIT 1'), ($usr->users_opt & 1048576));

	/* this is a hack, it essentially disables file attachment code when file_uploads are off */
	if (ini_get("file_uploads") != 1 || !($perms & 256)) {
		$post_enctype = '';
		$perms = $perms &~ 256;
	} else {
		$post_enctype = 'enctype="multipart/form-data"';
	}

	/* More Security */
	if (isset($thr) && !($perms & 4096) && $thr->thread_opt & 1) {
		error_dialog('ERRORE: Topic chiuso', 'Questo topic è chiuso, non è più possibile inserire messaggi');
	}

	if (_uid) {
		/* all sorts of user blocking filters */
		is_allowed_user($usr);

		/* if not moderator, validate user permissions */
		if (!$reply_to && !$msg_id && !($perms & 4)) {
			std_error('perms');
		} else if (!$msg_id && ($th_id || $reply_to) && !($perms & 8)) {
			std_error('perms');
		} else if ($msg_id && $msg->poster_id != $usr->id && !($perms & 16)) {
			std_error('perms');
		} else if ($msg_id && $EDIT_TIME_LIMIT && !$MOD && ($msg->post_stamp + $EDIT_TIME_LIMIT * 60 <__request_timestamp__)) {
			error_dialog('ERRORE', 'Non puoi più modificare questo messaggio');
		}
	} else {
		if (!$th_id && !($perms & 4)) {
			error_dialog('ERRORE: non si disponde dei privilegi necessari', 'Gli utenti anonimi non possono creare nuovi topic.<br><br>Se vuoi registrarti, vai alla <a href="/egroupware/fudforum/3814588639/index.php?t=register&'._rsid.'">pagina di registrazione</a>.<br>Se vuoi collegarti, vai alla <a href="/egroupware/fudforum/3814588639/index.php?t=login&'._rsid.'&returnto='.urlencode($returnto_d).'">schermata di login</a>');
		} else if ($reply_to && !($perms & 8)) {
			error_dialog('ERRORE: non si disponde dei privilegi necessari', 'Gli utenti anonimi non possono rispondere a messaggi.<br><br>Se vuoi registrarti, vai alla <a href="/egroupware/fudforum/3814588639/index.php?t=register&'._rsid.'">pagina di registrazione</a>.<br>Se vuoi collegarti, vai alla <a href="/egroupware/fudforum/3814588639/index.php?t=login&'._rsid.'&returnto='.urlencode($returnto_d).'">schermata di login</a>');
		} else if (($msg_id && !($perms & 16)) || is_ip_blocked(get_ip())) {
			invl_inp_err();
		}
	}

	if (isset($_GET['prev_loaded'])) {
		$_POST['prev_loaded'] = $_GET['prev_loaded'];
	}

	/* Retrieve Message */
	if (!isset($_POST['prev_loaded'])) {
		if (_uid) {
			$msg_show_sig = !$msg_id ? ($usr->users_opt & 2048) : ($msg->msg_opt & 1);

			if ($msg_id || $reply_to || $th_id) {
				$msg_poster_notif = (($usr->users_opt & 2) && !q_singleval("SELECT id FROM phpgw_fud_msg WHERE thread_id=".$msg->thread_id." AND poster_id="._uid)) || is_notified(_uid, $msg->thread_id);
			} else {
				$msg_poster_notif = ($usr->users_opt & 2);
			}
		}

		if ($msg_id) {
			$msg_subject = $msg->subject;
			reverse_fmt($msg_subject);
			$msg_subject = apply_reverse_replace($msg_subject);

			$msg_body = post_to_smiley($msg->body);
	 		if ($frm->forum_opt & 16) {
	 			$msg_body = html_to_tags($msg_body);
	 		} else if ($frm->forum_opt & 8) {
	 			reverse_fmt($msg_body);
	 			reverse_nl2br($msg_body);
	 		}
	 		$msg_body = apply_reverse_replace($msg_body);

	 		$msg_smiley_disabled = ($msg->msg_opt & 2);
			$msg_icon = $msg->icon;

	 		if ($msg->attach_cnt) {
	 			$r = q("SELECT id FROM phpgw_fud_attach WHERE message_id=".$msg->id." AND attach_opt=0");
	 			while ($fa_id = db_rowarr($r)) {
	 				$attach_list[$fa_id[0]] = $fa_id[0];
	 			}
	 			unset($r);
	 			$attach_count = count($attach_list);
		 	}
		 	$pl_id = (int) $msg->poll_id;
		} else if ($reply_to || $th_id) {
			$subj = $reply_to ? $msg->subject : $thr->subject;
			reverse_fmt($subj);

			$msg_subject = strncmp('Re:', $subj, strlen('Re:')) ? 'Re:' . ' ' . $subj : $subj;
			$old_subject = $msg_subject;

			if (isset($_GET['quote'])) {
				$msg_body = post_to_smiley(str_replace("\r", '', $msg->body));

				if (!strlen($msg->login)) {
					$msg->login =& $ANON_NICK;
				}
				reverse_fmt($msg->login);

				if ($frm->forum_opt & 16) {
					$msg_body = html_to_tags($msg_body);
					reverse_fmt($msg_body);
				 	$msg_body = '[quote title='.htmlspecialchars($msg->login).' ha scritto '.strftime("%a, %d %B %Y %H:%M", $msg->post_stamp).']'.$msg_body.'[/quote]';
				} else if ($frm->forum_opt & 8) {
					reverse_fmt($msg_body);
					reverse_nl2br($msg_body);
					$msg_body = str_replace('<br>', "\n", 'Quota: '.htmlspecialchars($msg->login).' ha scritto '.strftime("%a, %d %B %Y %H:%M", $msg->post_stamp).'<br />----------------------------------------------------<br />'.$msg_body.'<br />----------------------------------------------------<br />');
				} else {
					$msg_body = '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>'.htmlspecialchars($msg->login).' ha scritto '.strftime("%a, %d %B %Y %H:%M", $msg->post_stamp).'</b></td></tr><tr><td class="quote"><br />'.$msg_body.'<br /></td></tr></table>';
				}
				$msg_body .= "\n";
			}
		}
	} else { /* $_POST['prev_loaded'] */
		if ($FLOOD_CHECK_TIME && !$MOD && !$msg_id && ($tm = flood_check())) {
			error_dialog('ERRORE: è attivo il controllo sui messaggi.', 'Per cortesia, riprova tra '.$tm.' secondi');
		}

		/* import message options */
		$msg_show_sig		= isset($_POST['msg_show_sig']) ? $_POST['msg_show_sig'] : '';
		$msg_smiley_disabled	= isset($_POST['msg_smiley_disabled']) ? $_POST['msg_smiley_disabled'] : '';
		$msg_poster_notif	= isset($_POST['msg_poster_notif']) ? $_POST['msg_poster_notif'] : '';
		$pl_id			= !empty($_POST['pl_id']) ? poll_validate((int)$_POST['pl_id'], $msg_id) : 0;
		$msg_body		= $_POST['msg_body'];
		$msg_subject		= $_POST['msg_subject'];

		if ($perms & 256) {
			$attach_count = 0;

			/* restore the attachment array */
			if (!empty($_POST['file_array']) ) {
				$attach_list = @unserialize(base64_decode($_POST['file_array']));
				if (($attach_count = count($attach_list))) {
					foreach ($attach_list as $v) {
						if (!$v) {
							--$attach_count;
						}
					}
				}
			}

			/* remove file attachment */
			if (!empty($_POST['file_del_opt']) && isset($attach_list[$_POST['file_del_opt']])) {
				$attach_list[$_POST['file_del_opt']] = 0;
				/* Remove any reference to the image from the body to prevent broken images */
				if (strpos($msg_body, '[img]/egroupware/fudforum/3814588639/index.php?t=getfile&id='.$_POST['file_del_opt'].'[/img]') !== false) {
					$msg_body = str_replace('[img]/egroupware/fudforum/3814588639/index.php?t=getfile&id='.$_POST['file_del_opt'].'[/img]', '', $msg_body);
				}

				$attach_count--;
			}

			if ($frm->forum_opt & 32 && $MOD) {
				$frm->max_attach_size = (int) ini_get('upload_max_filesize');
				$t = str_replace($frm->max_attach_size, '', ini_get('upload_max_filesize'));
				if ($t == 'M' || $t == 'm') {
					$frm->max_attach_size *= 1024;
				}
				$frm->max_file_attachments = 100;
			}
			$MAX_F_SIZE = $frm->max_attach_size * 1024;

			/* newly uploaded files */
			if (isset($_FILES['attach_control']) && $_FILES['attach_control']['size']) {
				if ($_FILES['attach_control']['size'] > $MAX_F_SIZE) {
					$attach_control_error = '<font class="ErrorText">Il file allegato è troppo grande (supera il limite consentito di '.$MAX_F_SIZE.' byte)</font><br>';
				} else {
					if (!($MOD && $frm->forum_opt & 32) && filter_ext($_FILES['attach_control']['name'])) {
						$attach_control_error = '<font class="ErrorText">Il file che stai cercando di spedire non appartiene ai tipi di file consentiti.</font><br>';
					} else {
						if (($attach_count+1) <= $frm->max_file_attachments) {
							$val = attach_add($_FILES['attach_control'], _uid);
							$attach_list[$val] = $val;
							$attach_count++;
						} else {
							$attach_control_error = '<font class="ErrorText">Stai cercando di spedire più file di quanti sia consentito spedire.</font><br>';
						}
					}
				}
			}
			$attach_cnt = $attach_count;
		} else {
			$attach_cnt = 0;
		}

		/* removal of a poll */
		if (!empty($_POST['pl_del']) && $pl_id && $perms & 128) {
			poll_delete($pl_id);
			$pl_id = 0;
		}

		if ($reply_to && $old_subject == $msg_subject) {
			$no_spell_subject = 1;
		}

		if (isset($_POST['btn_spell'])) {
			$GLOBALS['MINIMSG_OPT']['DISABLED'] = 1;
			$text = apply_custom_replace($msg_body);
			$text_s = apply_custom_replace($msg_subject);

			if ($frm->forum_opt & 16) {
				$text = tags_to_html($text, $perms & 32768);
			} else if ($frm->forum_opt & 8) {
				$text = htmlspecialchars($text);
			}

			if ($frm->forum_opt & 24) {
				char_fix($text);
			}

			if ($perms & 16384 && !$msg_smiley_disabled) {
				$text = smiley_to_post($text);
			}

	 		if (strlen($text)) {
				$wa = tokenize_string($text);
				$msg_body = spell_replace($wa, 'body');

				if ($perms & 16384 && !$msg_smiley_disabled) {
					$msg_body = post_to_smiley($msg_body);
				}
				if ($frm->forum_opt & 16) {
					$msg_body = html_to_tags($msg_body);
				} else if ($frm->forum_opt & 8) {
					reverse_fmt($msg_body);
				}

				$msg_body = apply_reverse_replace($msg_body);
			}
			$wa = '';

			if (strlen($_POST['msg_subject']) && empty($no_spell_subject)) {
				$text_s = htmlspecialchars($text_s);
				char_fix($text_s);
				$wa = tokenize_string($text_s);
				$text_s = spell_replace($wa, 'subject');
				reverse_fmt($text_s);
				$msg_subject = apply_reverse_replace($text_s);
			}
		}

		if (!empty($_POST['submitted']) && !isset($_POST['spell']) && !isset($_POST['preview'])) {
			$_POST['btn_submit'] = 1;
		}

		if (!($usr->users_opt & 1048576) && isset($_POST['btn_submit']) && $frm->forum_opt & 4 && (!isset($_POST['frm_passwd']) || $frm->post_passwd != $_POST['frm_passwd'])) {
			set_err('password', 'Incorrect password.');
		}

		/* submit processing */
		if (isset($_POST['btn_submit']) && !check_post_form()) {
			$msg_post = new fud_msg_edit;

			/* Process Message Data */
			$msg_post->poster_id = _uid;
			$msg_post->poll_id = $pl_id;
			$msg_post->subject = $msg_subject;
			$msg_post->body = $msg_body;
			$msg_post->icon = (isset($_POST['msg_icon']) && basename($_POST['msg_icon']) == $_POST['msg_icon'] && @file_exists($WWW_ROOT_DISK.'images/message_icons/'.$_POST['msg_icon'])) ? $_POST['msg_icon'] : '';
		 	$msg_post->msg_opt =  $msg_smiley_disabled ? 2 : 0;
		 	$msg_post->msg_opt |= $msg_show_sig ? 1 : 0;
		 	$msg_post->attach_cnt = (int) $attach_cnt;
			$msg_post->body = apply_custom_replace($msg_post->body);

			if ($frm->forum_opt & 16) {
				$msg_post->body = tags_to_html($msg_post->body, $perms & 32768);
			} else if ($frm->forum_opt & 8) {
				$msg_post->body = nl2br(htmlspecialchars($msg_post->body));
			}

			if ($frm->forum_opt & 24) {
				char_fix($msg_post->body);
			}

	 		if ($perms & 16384 && !($msg_post->msg_opt & 2)) {
	 			$msg_post->body = smiley_to_post($msg_post->body);
	 		}

			fud_wordwrap($msg_post->body);

			$msg_post->subject = htmlspecialchars(apply_custom_replace($msg_post->subject));
			char_fix($msg_post->subject);

		 	/* chose to create thread OR add message OR update message */

		 	if (!$th_id) {
		 		$create_thread = 1;
		 		$msg_post->add($frm->id, $frm->message_threshold, $frm->forum_opt, ($perms & (64|4096)), false);
		 	} else if ($th_id && !$msg_id) {
				$msg_post->thread_id = $th_id;
		 		$msg_post->add_reply($reply_to, $th_id, ($perms & (64|4096)), false);
			} else if ($msg_id) {
				$msg_post->id = $msg_id;
				$msg_post->thread_id = $th_id;
				$msg_post->post_stamp = $msg->post_stamp;
				$msg_post->sync(_uid, $frm->id, $frm->message_threshold, ($perms & (64|4096)));
				/* log moderator edit */
			 	if (_uid && _uid != $msg->poster_id) {
			 		logaction($usr->id, 'MSGEDIT', $msg_post->id);
			 	}
			} else {
				std_error('systemerr');
			}

			/* write file attachments */
			if ($perms & 256 && isset($attach_list)) {
				attach_finalize($attach_list, $msg_post->id);
			}

			if (!$msg_id && (!($frm->forum_opt & 2) || $MOD)) {
				$msg_post->approve($msg_post->id, true);
			}

			if (_uid && !$msg_id) {
				/* deal with notifications */
	 			if (isset($_POST['msg_poster_notif'])) {
	 				thread_notify_add(_uid, $msg_post->thread_id);
	 			} else {
	 				thread_notify_del(_uid, $msg_post->thread_id);
	 			}

				/* register a view, so the forum marked as read */
				if (isset($frm)) {
					user_register_forum_view($frm->id);
				}
			}

			/* where to redirect, to the treeview or the flat view and consider what to do for a moderated forum */
			if ($frm->forum_opt & 2 && !$MOD) {
				if ($FUD_OPT_2 & 262144) {
					$modl = array();
					$c = uq('SELECT u.email FROM phpgw_fud_mod mm INNER JOIN phpgw_fud_users u ON u.id=mm.user_id WHERE mm.forum_id='.$frm->id);
					while ($r = db_rowarr($c)) {
						$modl[] = $r[0];
					}
					if ($modl) {
						send_email($NOTIFY_FROM, $modl, 'New message in forum "'.$frm->name.'" pending approval', 'A new message titled "'.$msg_post->subject.'" was just posted in a forum that you moderate. To review this message go to: /egroupware/fudforum/3814588639/index.php?t=modque\n\nThis is an automated process, do not reply to this message.\n', '');
					}
				}
				$data = file_get_contents($INCLUDE.'theme/'.$usr->theme_name.'/usercp.inc');
				$s = strpos($data, '<?php') + 5;
				eval(substr($data, $s, (strrpos($data, '?>') - $s)));
				?>
				<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<div align="center">
<table border="0" cellspacing="1" cellpadding="2" class="DialogTable">
<tr><th>Avviso: forum moderato</th></tr>
<tr class="RowStyleA" align="center">
	<td class="GenText">
		Hai scritto un messaggio in un forum moderato, il che vuol dire che il tuo messaggio non sarà reso pubblico fino a quando uno dei moderatori non l&#39;avrà approvato.
		<br /><br /><form action="/egroupware/fudforum/3814588639/index.php?t=post" method="post"><?php echo _hs; ?>
		<input type="submit" class="button" name="proceed" value="Procedi">
		<input type="hidden" name="moderated_redr" value="1">
		</form>
	</td>
</tr>
</table>
</div>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>
				<?php
				exit;
			} else {
				$t = d_thread_view;

				if ($usr->returnto) {
					if (!strncmp('t=selmsg', $usr->returnto, 8) || !strncmp('/sel/', $usr->returnto, 5)) {
						check_return($usr->returnto);
					}
					if (preg_match('!t=(tree|msg)!', $usr->returnto, $tmp)) {
						$t = $tmp[1];
					}
				}
				/* redirect the user to their message */
				header('Location: /egroupware/fudforum/3814588639/index.php?t='.$t.'&goto='.$msg_post->id.'&'._rsidl);
				exit;
			}
		} /* Form submitted and user redirected to own message */
	} /* $prevloaded is SET, this form has been submitted */

	if ($reply_to || $th_id && !$msg_id) {
		ses_update_status($usr->sid, 'Risponde a <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=msg&goto='.$thr->root_msg_id.'">'.$thr->subject.'</a> in '.$frm->name.'', $frm->id, 0);
	} else if ($msg_id) {
		ses_update_status($usr->sid, 'Risponde a <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=msg&goto='.$thr->root_msg_id.'">'.$thr->subject.'</a> in '.$frm->name.'', $frm->id, 0);
	} else  {
		ses_update_status($usr->sid, 'Scrive un nuovo topic in <a class="GenLink" href="/egroupware/fudforum/3814588639/index.php?t=thread&frm_id='.$frm->id.'">'.$frm->name.'</a>', $frm->id, 0);
	}

	if (isset($_POST['spell'])) {
		$GLOBALS['MINIMSG_OPT']['DISABLED'] = true;
	}

$start = '';
if ($th_id && empty($GLOBALS['MINIMSG_OPT']['DISABLED'])) {
	$GLOBALS['DRAWMSG_OPTS']['NO_MSG_CONTROLS'] = 1;

	$count = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;
	$start = isset($_GET['start']) ? (int)$_GET['start'] : (isset($_POST['minimsg_pager_switch']) ? (int)$_POST['minimsg_pager_switch'] : 0);
	$total = $thr->replies + 1;

	if ($reply_to && !isset($_POST['minimsg_pager_switch']) && $total > $count) {
		$start = ($total - q_singleval("SELECT count(*) FROM phpgw_fud_msg WHERE thread_id=".$th_id." AND apr=1 AND id>=".$reply_to));
		$msg_order_by = 'ASC';
	} else {
		$msg_order_by = 'DESC';
	}

	$c = uq('SELECT m.*, t.thread_opt, t.root_msg_id, t.last_post_id, t.forum_id,
			u.id AS user_id, u.alias AS login, u.users_opt, u.last_visit AS time_sec,
			p.max_votes, p.expiry_date, p.creation_date, p.name AS poll_name,  p.total_votes
		FROM
			phpgw_fud_msg m
			INNER JOIN phpgw_fud_thread t ON m.thread_id=t.id
			LEFT JOIN phpgw_fud_users u ON m.poster_id=u.id
			LEFT JOIN phpgw_fud_poll p ON m.poll_id=p.id
		WHERE
			m.thread_id='.$th_id.' AND m.apr=1
		ORDER BY id '.$msg_order_by.' LIMIT '.qry_limit($count, $start));

	$message_data='';
	$m_count = 0;
	while ($obj = db_rowobj($c)) {
		$message_data .= tmpl_drawmsg($obj, $usr, $perms, true, $m_count, '');
		$mid = $obj->id;
	}

	un_register_fps();

	$minimsg_pager = tmpl_create_pager($start, $count, $total, "javascript: document.post_form.minimsg_pager_switch.value='%s'; document.post_form.submit();", null, false, false);
	$minimsg = '<br /><br />
<table border=0 width="100%" cellspacing=0 cellpadding=3 class="dashed">
<tr><td class="miniMH">Thread View</td></tr>
<tr><td>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
'.$message_data.'
</table>
</td></tr>
<tr><td>'.$minimsg_pager.'</td></tr>
</table>
<input type="hidden" name="minimsg_pager_switch" value="'.$start.'">';

	unset($GLOBALS['DRAWMSG_OPTS']['NO_MSG_CONTROLS']);
} else if ($th_id) {
	$start = isset($_GET['start']) ? (int)$_GET['start'] : (isset($_POST['minimsg_pager_switch']) ? (int)$_POST['minimsg_pager_switch'] : 0);
	$minimsg = '<br /><br />
<table border=0 width="100%" cellspacing=0 cellpadding=3 class="dashed">
<tr><td align=middle>[<a href="javascript: document.post_form.submit();">Reveal Thread</a>]</td></tr>
</table>
<input type="hidden" name="minimsg_pager_switch" value="'.$start.'">';
} else {
	$minimsg = '';
}

	if (!$th_id) {
		$label = 'Crea Topic';
	} else if ($msg_id) {
		$label = 'Applica le modifiche al messaggio';
	} else {
		$label = 'Rispondi';
	}

	$spell_check_button = ($FUD_OPT_1 & 2097152 && extension_loaded('pspell') && $usr->pspell_lang) ? '<input type="submit" class="button" value="Controllo ortografico del messaggio" name="spell">&nbsp;' : '';

	if (isset($_POST['preview']) || isset($_POST['spell'])) {
		$text = apply_custom_replace($msg_body);
		$text_s = apply_custom_replace($msg_subject);

		if ($frm->forum_opt & 16) {
			$text = tags_to_html($text, $perms & 32768);
		} else if ($frm->forum_opt & 8) {
			$text = nl2br(htmlspecialchars($text));
		}

		if ($frm->forum_opt & 24) {
			char_fix($text);
		}

		if ($perms & 16384 && !$msg_smiley_disabled) {
			$text = smiley_to_post($text);
		}

		$text_s = htmlspecialchars($text_s);
		char_fix($text_s);

		$spell = $spell_check_button && isset($_POST['spell']);

		if ($spell && $text) {
			$text = check_data_spell($text, 'body', $usr->pspell_lang);
		}
		fud_wordwrap($text);

		if ($spell && empty($no_spell_subject) && $text_s) {
			$subj = check_data_spell($text_s, 'subject', $usr->pspell_lang);
		} else {
			$subj = $text_s;
		}

		if ($FUD_OPT_1 & 32768 && $msg_show_sig) {
			if ($msg_id && $msg->poster_id && $msg->poster_id != _uid && !$reply_to) {
				$sig = q_singleval('SELECT sig FROM phpgw_fud_users WHERE id='.$msg->poster_id);
			} else {
				$sig = $usr->sig;
			}

			$signature = $sig ? '<p><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />'.$sig.'' : '';
		} else {
			$signature = '';
		}

		$apply_spell_changes = $spell ? '<input type="submit" class="button" name="btn_spell" value="Applica le modifiche">&nbsp;' : '';

		$preview_message = '<div align="center">
<table border="0" cellspacing="1" cellpadding="2" class="PreviewTable">
<tr><th colspan=2>Anteprima del messaggio</th></tr>
<tr><td class="RowStyleA"><font class="MsgSubText">'.$subj.'</font></td></tr>
<tr><td class="RowStyleA"><font class="MsgBodyText">'.$text.$signature.'</font></td></tr>
<tr><td align="left" class="RowStyleB">'.$apply_spell_changes.'<input type="submit" class="button" value="Anteprima del messaggio" tabindex="4" name="preview">&nbsp;'.$spell_check_button.'<input type="submit" class="button" tabindex="5" name="btn_submit" value="'.$label.'" onClick="javascript: document.post_form.submitted.value=1;"></td></tr>
</table><br /></div>';
	} else {
		$preview_message = '';
	}

	$post_error = is_post_error() ? '<h4 align="center"><font class="ErrorText">Si è verificato un errore</font></h4>' : '';
	$loged_in_user = _uid ? '<tr class="RowStyleB"><td nowrap class="GenText">Utente collegato:</td><td class="GenText" width="100%">'.htmlspecialchars($usr->login).'</td></tr>' : '';

	/* handle password protected forums */
	if ($frm->forum_opt & 4 && !$MOD) {
		$pass_err = get_err('password');
		$post_password = '<tr class="RowStyleB"><td class="GenText">Password:</td><td><input type="password" name="frm_passwd" value="" tabindex="1">'.$pass_err.'</td></tr>';
	} else {
		$post_password = '';
	}

	$msg_subect_err = get_err('msg_subject');
	if (!isset($msg_subject)) {
		$msg_subject = '';
	}

	/* handle polls */
	$poll = '';
	if ($perms & 128) {
		if (!$pl_id) {
			$poll = '<tr class="RowStyleB"><td class="GenText">Sondaggio:</td><td class="GenText"><a class="GenLink" href="javascript://" onClick="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=poll&amp;'._rsid.'&amp;frm_id='.$frm->id.'\', \'poll_creator\', 400, 300);">[CREA SONDAGGIO]</a></td></tr>';
		} else if (($poll = db_saq('SELECT id, name FROM phpgw_fud_poll WHERE id='.$pl_id))) {
			$poll = '<tr class="RowStyleB"><td class="GenText">Sondaggio:</td><td class="GenText">'.$poll[1].' [<a class="GenLink" href="javascript://" onClick="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=poll&amp;'._rsid.'&amp;pl_id='.$poll[0].'&amp;frm_id='.$frm->id.'\', \'poll\', 400, 300);">MODIFICA</a>] <input type="hidden" name="pl_del" value="">[<a class="GenLink" href="javascript: document.post_form.pl_del.value=\'1\'; document.post_form.submit();">CANCELLA</a>]</td></tr>';
		}
	}

	/* sticky/announcment controls */
	if ($perms & 64 && (!isset($thr) || ($thr->root_msg_id == $msg->id && !$reply_to))) {
		if (!isset($_POST['prev_loaded'])) {
			if (!isset($thr)) {
				$thr_ordertype = $thr_orderexpiry = '';
			} else {
				$thr_ordertype = ($thr->thread_opt|1) ^ 1;
				$thr_orderexpiry = $thr->orderexpiry;
			}
		} else {
			$thr_ordertype = isset($_POST['thr_ordertype']) ? (int) $_POST['thr_ordertype'] : '';
			$thr_orderexpiry = isset($_POST['thr_orderexpiry']) ? (int) $_POST['thr_orderexpiry'] : '';
		}

		$thread_type_select = tmpl_draw_select_opt("0\n4\n2", "Normale\nTopped\nAnnuncio", $thr_ordertype, '', '');
		$thread_expiry_select = tmpl_draw_select_opt("1000000000\n3600\n7200\n14400\n28800\n57600\n86400\n172800\n345600\n604800\n1209600\n2635200\n5270400\n10540800\n938131200", "Mai\n1 Ora\n3 Ore\n4 Ore\n8 Ore\n16 Ore\n1 Giorno\n2 Giorni\n4 Giorni\n1 Settimana\n2 Settimane\n1 Mese\n2 Mesi\n4 Mesi\n1 Anno", $thr_orderexpiry, '', '');

		$admin_options = '<tr class="RowStyleB"><td class="GenText" nowrap>Opzioni per il moderatore:</td>
<td>
Tipo di topic: <select name="thr_ordertype">'.$thread_type_select.'</select>
Topic che scade: <select name="thr_orderexpiry">'.$thread_expiry_select.'</select>
</td>
</tr>';
	} else {
		$admin_options = '';
	}

	/* thread locking controls */
	if ($perms & 4096) {
		if (!isset($_POST['prev_loaded']) && isset($thr)) {
			$thr_locked_checked = $thr->thread_opt & 1 ? ' checked' : '';
		} else if (isset($_POST['prev_loaded'])) {
			$thr_locked_checked = isset($_POST['thr_locked']) ? ' checked' : '';
		} else {
			$thr_locked_checked = '';
		}
		$mod_post_opts = '<tr><td><input type="checkbox" name="thr_locked" value="Y"'.$thr_locked_checked.'></td><td class="GenText"><b>Topic chiuso</b></td></tr>';
	} else {
		$mod_post_opts = '';
	}

	/* message icon selection */
	$post_icons = draw_post_icons((isset($_POST['msg_icon']) ? $_POST['msg_icon'] : ''));

	/* tool bar icons */
	$fud_code_icons = $frm->forum_opt & 16 ? '<tr class="RowStyleA"><td nowrap class="GenText">Strumenti di formattazione:</td><td>
<table border=0 cellspacing=0 cellpadding=0>
<tr><td>
<table border=0 cellspacing=1 cellpadding=2 class="FormattingToolsBG">
<tr>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[B]\', \'[/B]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_bold.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[I]\', \'[/I]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_italic.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[U]\', \'[/U]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_underline.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[ALIGN=left]\', \'[/ALIGN]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_aleft.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[ALIGN=center]\', \'[/ALIGN]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_acenter.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[ALIGN=right]\', \'[/ALIGN]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_aright.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: url_insert();"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_url.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: email_insert();"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_email.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: image_insert();"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_image.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=mklist&amp;'._rsid.'&amp;tp=OL:1\', \'listmaker\', 350, 350);"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_numlist.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: window_open(\'/egroupware/fudforum/3814588639/index.php?t=mklist&amp;'._rsid.'&amp;tp=UL:square\', \'listmaker\', 350, 350);"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_bulletlist.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[QUOTE]\', \'[/QUOTE]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_quote.gif" /></a></td>
<td class="FormattingToolsCLR"><a href="javascript: insertTag(document.post_form.msg_body, \'[CODE]\', \'[/CODE]\');"><img alt="" src="/egroupware/fudforum/3814588639/theme/italian/images/b_code.gif" /></a></td>
</tr>
</table>
</td>
<td>&nbsp;&nbsp;
<select name="fnt_size" onChange="javascript:insertTag(document.post_form.msg_body, \'[SIZE=\'+document.post_form.fnt_size.options[this.selectedIndex].value+\']\', \'[/SIZE]\'); document.post_form.fnt_size.options[0].selected=true">
<option value="" selected>Dimensione</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
</select>
<select name="fnt_color" onChange="javascript:insertTag(document.post_form.msg_body, \'[COLOR=\'+document.post_form.fnt_color.options[this.selectedIndex].value+\']\', \'[/COLOR]\'); document.post_form.fnt_color.options[0].selected=true">
<option value="">Colore</option>
<option value="skyblue" style="color:skyblue">Sky Blue</option>
<option value="royalblue" style="color:royalblue">Royal Blue</option>
<option value="blue" style="color:blue">Blue</option>
<option value="darkblue" style="color:darkblue">Dark Blue</option>
<option value="orange" style="color:orange">Orange</option>
<option value="orangered" style="color:orangered">Orange Red</option>
<option value="crimson" style="color:crimson">Crimson</option>
<option value="red" style="color:red">Red</option>
<option value="firebrick" style="color:firebrick">Firebrick</option>
<option value="darkred" style="color:darkred">Dark Red</option>
<option value="green" style="color:green">Green</option>
<option value="limegreen" style="color:limegreen">Lime Green</option>
<option value="seagreen" style="color:seagreen">Sea Green</option>
<option value="deeppink" style="color:deeppink">Deep Pink</option>
<option value="tomato" style="color:tomato">Tomato</option>
<option value="coral" style="color:coral">Coral</option>
<option value="purple" style="color:purple">Purple</option>
<option value="indigo" style="color:indigo">Indigo</option>
<option value="burlywood" style="color:burlywood">Burly Wood</option>
<option value="sandybrown" style="color:sandybrown">Sandy Brown</option>
<option value="sienna" style="color:sienna">Sienna</option>
<option value="chocolate" style="color:chocolate">Chocolate</option>
<option value="teal" style="color:teal">Teal</option>
<option value="silver" style="color:silver">Silver</option>
</select>
<select name="fnt_face" onChange="javascript:insertTag(document.post_form.msg_body, \'[FONT=\'+document.post_form.fnt_face.options[this.selectedIndex].value+\']\', \'[/FONT]\'); document.post_form.fnt_face.options[0].selected=true">
<option value="">Font</option>
<option value="Arial" style="font-family:Arial">Arial</option>
<option value="Times" style="font-family:Times">Times</option>
<option value="Courier" style="font-family:Courier">Courier</option>
<option value="Century" style="font-family:Century">Century</option>
</select>
</td></tr></table></td></tr>' : '';

	$post_options = tmpl_post_options($frm->forum_opt, $perms);
	$message_err = get_err('msg_body', 1);
	$msg_body = isset($msg_body) ? htmlspecialchars(str_replace("\r", '', $msg_body)) : '';
	if (!empty($msg_subject)) {
		$msg_subject = htmlspecialchars($msg_subject);
		char_fix($msg_subject);
	}
	char_fix($msg_body);

	$pivate = '';
	/* handle file attachments */
	if ($perms & 256) {
		if ($frm->forum_opt & 32 && $MOD) {
			$frm->max_attach_size = (int) ini_get('upload_max_filesize');
			$t = str_replace($frm->max_attach_size, '', ini_get('upload_max_filesize'));
			if ($t == 'M' || $t == 'm') {
				$frm->max_attach_size *= 1024;
			}
			$frm->max_file_attachments = 100;
		}
		$file_attachments = draw_post_attachments((isset($attach_list) ? $attach_list : ''), $frm->max_attach_size, $frm->max_file_attachments, $attach_control_error, '', $msg_id);
	} else {
		$file_attachments = '';
	}

	if (_uid) {
		$msg_poster_notif_check = $msg_poster_notif ? ' checked' : '';
		$msg_show_sig_check = $msg_show_sig ? ' checked' : '';
		$reg_user_options = '<tr><td><input type="checkbox" name="msg_poster_notif" value="Y"'.$msg_poster_notif_check.'></td><td class="GenText"><b>Notifica del messaggio</b></td></tr>
<tr><td>&nbsp;</td><td><font class="SmallText">Segnala quando qualcuno risponde a questo messaggio.</font></td></tr>
<tr><td><input type="checkbox" name="msg_show_sig" value="Y"'.$msg_show_sig_check.'></td><td class="GenText"><b>Inserisci signature</b></td></tr>
<tr><td>&nbsp;</td><td><font class="SmallText">Inserisci la signature del mio profilo.</font></td></tr>
'.$mod_post_opts;
	} else {
		$reg_user_options = '';
	}

	/* handle smilies */
	if ($perms & 16384) {
		$msg_smiley_disabled_check = !empty($msg_smiley_disabled) ? ' checked' : '';
		$disable_smileys = '<tr><td><input type="checkbox" name="msg_smiley_disabled" value="Y"'.$msg_smiley_disabled_check.'></td><td class="GenText"><b>Disabilita le faccine in questo messaggio</b></td></tr>';
		$post_smilies = draw_post_smiley_cntrl();
	} else {
		$post_smilies = $disable_smileys = '';
	}
if ($FUD_OPT_2 & 2) {
	$page_gen_end = gettimeofday();
	$page_gen_time = sprintf('%.5f', ($page_gen_end['sec'] - $PAGE_TIME['sec'] + (($page_gen_end['usec'] - $PAGE_TIME['usec'])/1000000)));
	$page_stats = '<br /><div align="left" class="SmallText">Tempo totale richiesto per generare la pagina: '.$page_gen_time.' secondi</div>';
} else {
	$page_stats = '';
}
?>
<?php echo $GLOBALS['fud_egw_hdr']; ?>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<form action="/egroupware/fudforum/3814588639/index.php?t=post" method="post" name="post_form" <?php echo $post_enctype; ?> onSubmit="javascript: document.post_form.btn_submit.disabled = true;">
<?php echo $post_error; ?>
<?php echo $preview_message; ?>
<table border="0" cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=2><a name="ptop"> </a>Crea nuovo topic</th></tr>
<?php echo $loged_in_user; ?>
<?php echo $post_password; ?>
<tr class="RowStyleB"><td class="GenText">Forum:</td><td class="GenText"><?php echo $frm->name; ?></td></tr>
<tr class="RowStyleB"><td class="GenText">Titolo:</td><td class="GenText"><input type="text" maxLength=100 name="msg_subject" value="<?php echo $msg_subject; ?>" size=50 tabindex="2"> <?php echo $msg_subect_err; ?></td></tr>
<?php echo $poll; ?>
<?php echo $admin_options; ?>
<?php echo $post_icons; ?>
<?php echo $post_smilies; ?>
<?php echo $fud_code_icons; ?>

<tr class="RowStyleA"><td nowrap valign=top class="GenText">Testo:<br /><br /><?php echo $post_options; ?></td><td><?php echo $message_err; ?><textarea rows="20" cols="65" tabindex="3" wrap="virtual" id="txtb" name="msg_body" onKeyUp="storeCaret(this);" onClick="storeCaret(this);" onSelect="storeCaret(this);"><?php echo $msg_body; ?></textarea></td></tr>

<?php echo $file_attachments; ?>
<tr class="RowStyleB" valign="top">
<td class="GenText">Opzioni:</td>
<td>
<table border=0 cellspacing=0 cellpadding=1>
<?php echo $reg_user_options; ?>
<?php echo $disable_smileys; ?>
</table>
</td></tr>
<tr class="RowStyleA"><td class="GenText" align="right" colspan=2>
<input type="submit" class="button" value="Anteprima del messaggio" tabindex="4" name="preview">&nbsp;<?php echo $spell_check_button; ?><input type="submit" class="button" tabindex="5" name="btn_submit" value="<?php echo $label; ?>" onClick="javascript: document.post_form.submitted.value=1;"></td></tr>
</table>
<?php echo $minimsg; ?>
<?php echo _hs; ?>
<input type="hidden" name="submitted" value="">
<input type="hidden" name="reply_to" value="<?php echo $reply_to; ?>">
<input type="hidden" name="th_id" value="<?php echo $th_id; ?>">
<input type="hidden" name="frm_id" value="<?php echo $frm_id; ?>">
<input type="hidden" name="start" value="<?php echo $start; ?>">
<input type="hidden" name="msg_id" value="<?php echo $msg_id; ?>">
<input type="hidden" name="pl_id" value="<?php echo $pl_id; ?>">
<input type="hidden" name="old_subject" value="<?php echo $old_subject; ?>">
<input type="hidden" name="prev_loaded" value="1">
</form>
<?php echo $page_stats; ?>
</td></tr></table>
<table width="100%" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground" align="center">
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?><br />Copyright &copy;2001-2003 <a href="http://fud.prohost.org/" class="GenLink">Advanced Internet Designs Inc.</a></span>
</td></tr></table>
<?php echo $GLOBALS['phpgw']->common->phpgw_footer(); ?>
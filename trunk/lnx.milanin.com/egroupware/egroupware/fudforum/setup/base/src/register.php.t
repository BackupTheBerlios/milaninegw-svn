<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: register.php.t,v 1.6 2003/12/18 18:22:09 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

/*{PRE_HTML_PHP}*/

/* Create a list of avaliable themes */
function create_theme_select($name, $def=null)
{
	$theme_select_values = '';
	$r = uq("SELECT id, name FROM {SQL_TABLE_PREFIX}themes WHERE theme_opt>=1 AND (theme_opt & 1) > 0 ORDER BY ((theme_opt & 2) > 0) DESC");
	while ($t = db_rowarr($r)) {
		$selected = $t[0] == $def ? ' selected' : '';
		$theme_select_values .= '{TEMPLATE: theme_select_value}';
	}

	return '{TEMPLATE: theme_select}';
}

if (!function_exists('array_fill')) {
function array_fill($s, $e, $t)
{
	++$e;
	do {
		$arr[$s] = $t;
	} while (++$s < $e);

	return $arr;
}
}

function fetch_img($url, $user_id)
{
	$ext = array(1=>'gif', 2=>'jpg', 3=>'png', 4=>'swf');
	list($max_w, $max_y) = explode('x', $GLOBALS['CUSTOM_AVATAR_MAX_DIM']);
	if (!($img_info = @getimagesize($url)) || $img_info[0] > $max_w || $img_info[1] > $max_y || $img_info[2] > ($GLOBALS['FUD_OPT_1'] & 64 ? 4 : 3)) {
		return;
	}
	if (!($img_data = file_get_contents($url))) {
		return;
	}
	$name = $user_id . '.' . $ext[$img_info[2]]. '_';

	while (($fp = fopen(($path = tempnam($GLOBALS['TMP'], $name)), 'ab'))) {
		if (!ftell($fp)) { /* make sure that the temporary file picked, did not exist before, yes, this is paranoid. */
			break;
		}
	}
	fwrite($fp, $img_data);
	fclose($fp);

	return $path;
}
	/* intialize error status */
	$GLOBALS['error'] = 0;

function check_passwd($id, $passwd)
{
	return q_singleval("SELECT login FROM {SQL_TABLE_PREFIX}users WHERE id=".$id." AND passwd='".md5($passwd)."'");
}

function sanitize_url($url)
{
	if (!$url) {
		return '';
	}

	if (strncasecmp($url, 'http://', strlen('http://')) && strncasecmp($url, 'https://', strlen('https://')) && strncasecmp($url, 'ftp://', strlen('ftp://'))) {
		if (stristr($url, 'javascript:')) {
			return '';
		} else {
			return 'http://' . $url;
		}
	}
	return $url;
}

function sanitize_login($login)
{
	for ($i = 0; $i < 32; $i++) $list[] = chr($i);
	for ($i = 127; $i < 160; $i++) $list[] = chr($i);

	return str_replace($list, array_fill(0, count($list), ''), $login);
}

function register_form_check($user_id)
{
	$_POST['reg_home_page'] = sanitize_url(trim($_POST['reg_home_page']));
	$_POST['reg_user_image'] = !empty($_POST['reg_user_image']) ? sanitize_url(trim($_POST['reg_user_image'])) : '';

	if (!empty($_POST['reg_icq']) && !(int)$_POST['reg_icq']) { /* ICQ # can only be an integer */
		$_POST['reg_icq'] = '';
	}

	/* Image count check */
	if ($GLOBALS['FORUM_IMG_CNT_SIG'] && $GLOBALS['FORUM_IMG_CNT_SIG'] < substr_count(strtolower($_POST['reg_sig']), '[img]') ) {
		set_err('reg_sig', '{TEMPLATE: register_err_toomanyimages}');
	}

	/* Url Avatar check */
	if (!empty($_POST['reg_avatar_loc']) && !($GLOBALS['reg_avatar_loc_file'] = fetch_img($_POST['reg_avatar_loc'], $user_id))) {
		set_err('avatar', '{TEMPLATE: register_err_not_valid_img}');
	}

	/* Alias Check */
	if ($GLOBALS['FUD_OPT_2'] & 128 && isset($_POST['reg_alias'])) {
		if (($_POST['reg_alias'] = trim(sanitize_login($_POST['reg_alias'])))) {
			if (strlen($_POST['reg_alias']) > $GLOBALS['MAX_LOGIN_SHOW']) {
				$_POST['reg_alias'] = substr($_POST['reg_alias'], 0, $GLOBALS['MAX_LOGIN_SHOW']);
			}
			if (q_singleval("SELECT id FROM {SQL_TABLE_PREFIX}users WHERE alias='".addslashes(htmlspecialchars($_POST['reg_alias']))."' AND id!=".$user_id)) {
				set_err('reg_alias', '{TEMPLATE: register_err_taken_alias}');
			}
		}
	}

	if ($GLOBALS['FORUM_SIG_ML'] && strlen($_POST['reg_sig']) > $GLOBALS['FORUM_SIG_ML']) {
		set_err('reg_sig', '{TEMPLATE: register_err_sig_too_long}');
	}

	return $GLOBALS['error'];
}

function fmt_year($val)
{
	if (!($val = (int)$val)) {
		return;
	}
	if ($val > 1000) {
		return $val;
	} else if ($val < 100 && $val > 10) {
		return (1900 + $val);
	} else if ($val < 10) {
		return (2000 + $val);
	}
}

function set_err($err_name, $err_msg)
{
	$GLOBALS['error'] = 1;
	if (isset($GLOBALS['err_msg'])) {
		array_push($GLOBALS['err_msg'], array($err_name => $err_msg));
	} else {
		$GLOBALS['err_msg'] = array($err_name => $err_msg);
	}
}

function draw_err($err_name)
{
	if (!isset($GLOBALS['err_msg'][$err_name])) {
		return;
	}
	return '{TEMPLATE: register_error}';
}

function make_avatar_loc($path, $disk, $web)
{
	$img_info = @getimagesize($disk . $path);

	if ($img_info[2] < 4 && $img_info[2] > 0) {
		return '<img src="'.$web . $path.'" '.$img_info[3].' />';
	} else if ($img_info[2] == 4) {
		return '<embed src="'.$web . $path.'" '.$img_info[3].' />';
	} else {
		return '';
	}
}

function remove_old_avatar($avatar_str)
{
	if (preg_match('!images/custom_avatars/(([0-9]+)\.([A-Za-z]+))" width=!', $avatar_str, $tmp)) {
		@unlink($GLOBALS['WWW_ROOT_DISK'] . 'images/custom_avatars/' . basename($tmp[1]));
	}
}

function decode_uent(&$uent)
{
	reverse_fmt($uent->home_page);
	reverse_fmt($uent->bio);
	reverse_fmt($uent->interests);
	reverse_fmt($uent->occupation);
	reverse_fmt($uent->location);
	reverse_fmt($uent->user_image);
	$uent->aim = urldecode($uent->aim);
	$uent->yahoo = urldecode($uent->yahoo);
	$uent->msnm = urldecode($uent->msnm);
	$uent->jabber = urldecode($uent->jabber);
	$uent->affero = urldecode($uent->affero);
}

	if (isset($_GET['mod_id'])) {
		$mod_id = (int)$_GET['mod_id'];
	} else if (isset($_POST['mod_id'])) {
		$mod_id = (int)$_POST['mod_id'];
	} else {
		$mod_id = '';
	}

	/* allow the root to modify settings other lusers */
	if (_uid && $usr->users_opt & 1048576 && $mod_id) {
		if (!($uent =& usr_reg_get_full($mod_id))) {
			exit('Invalid User Id');
		}
		decode_uent($uent);
	} else {
		$uent =& usr_reg_get_full($usr->id);
		decode_uent($uent);
	}

	/* this is a hack, it essentially disables uploading of avatars when file_uploads are off */
	if (ini_get("file_uploads") != 1 || !($FUD_OPT_1 & 8)) {
		$register_enctype = '';
		$FUD_OPT_1 = $FUD_OPT_1 &~ 8;
	} else {
		$register_enctype = '{TEMPLATE: register_enctype}';
	}

	$avatar_tmp = $avatar_arr = null;
	/* deal with avatars, only done for regged users */
	if (!empty($_POST['avatar_tmp'])) {
		list($avatar_arr['file'], $avatar_arr['del'], $avatar_arr['leave']) = explode("\n", base64_decode($_POST['avatar_tmp']));
	}
	if (isset($_POST['btn_detach']) && isset($avatar_arr)) {
		$avatar_arr['del'] = 1;
	}
	if (!($FUD_OPT_1 & 8) && (!@file_exists($avatar_arr['file']) || empty($avatar_arr['leave']))) {
		/* hack attempt for URL avatar */
		$avatar_arr = null;
	} else if (($FUD_OPT_1 & 8) && isset($_FILES['avatar_upload']) && $_FILES['avatar_upload']['size'] > 0) { /* new upload */
		if ($_FILES['avatar_upload']['size'] >= $CUSTOM_AVATAR_MAX_SIZE) {
			set_err('avatar', '{TEMPLATE: register_err_avatartobig}');
		} else {
			/* [user_id].[file_extension]_'random data' */
			$file_name = $uent->id . strrchr($_FILES['avatar_upload']['name'], '.') . '_';
			$tmp_name = safe_tmp_copy($_FILES['avatar_upload']['tmp_name'], 0, $file_name);

			if (!($img_info = @getimagesize($TMP . $tmp_name))) {
				set_err('avatar', '{TEMPLATE: register_err_not_valid_img}');
				unlink($TMP . $tmp_name);
			}

			list($max_w, $max_y) = explode('x', $CUSTOM_AVATAR_MAX_DIM);
			if ($img_info[2] > ($FUD_OPT_1 & 64 ? 4 : 3)) {
				set_err('avatar', '{TEMPLATE: register_err_avatarnotallowed}');
				unlink($TMP . $tmp_name);
			} else if ($img_info[0] >$max_w || $img_info[1] >$max_y) {
				set_err('avatar', '{TEMPLATE: register_err_avatardimtobig}');
				unlink($TMP . $tmp_name);
			} else {
				/* remove old uploaded file, if one exists & is not in DB */
				if (empty($avatar_arr['leave']) && @file_exists($avatar_arr['file'])) {
					@unlink($TMP . $avatar_arr['file']);
				}

				$avatar_arr['file'] = $tmp_name;
				$avatar_arr['del'] = 0;
				$avatar_arr['leave'] = 0;
			}
		}
	}

	if (count($_POST)) {
		$new_users_opt = 0;
		foreach (array('display_email', 'notify', 'notify_method', 'ignore_admin', 'email_messages', 'pm_messages', 'pm_notify', 'default_view', 'gender', 'append_sig', 'show_sigs', 'show_avatars', 'show_im', 'invisible_mode') as $v) {
			if (!empty($_POST['reg_'.$v])) {
				$new_users_opt |= (int) $_POST['reg_'.$v];
			}
		}
		/* security check, prevent haxors from passing values that shouldn't */
		if (!($new_users_opt & (131072|65536|262144|524288|1048576|2097152|4194304|8388608|16777216))) {
			$uent->users_opt = ($uent->users_opt & (131072|65536|262144|524288|1048576|2097152|4194304|8388608|16777216)) | $new_users_opt;
		}
	}

	/* SUBMITTION CODE */
	if (isset($_POST['fud_submit']) && !isset($_POST['btn_detach']) && !isset($_POST['btn_upload']) && !register_form_check($uent->id)) {
		$old_email = $uent->email;
		$old_avatar_loc = $uent->avatar_loc;
		$old_avatar = $uent->avatar;

		/* import data from _POST into $uent object */
		$vars = array_keys(get_object_vars($uent));
		foreach ($vars as $v) {
			if (isset($_POST['reg_'.$v])) {
				$uent->{$v} = $_POST['reg_'.$v];
			}
		}

		$uent->bday = fmt_year($_POST['b_year']) . str_pad((int)$_POST['b_month'], 2, '0', STR_PAD_LEFT) . str_pad((int)$_POST['b_day'], 2, '0', STR_PAD_LEFT);
		$uent->sig = apply_custom_replace($uent->sig);
		if ($FUD_OPT_1 & 131072) {
			$uent->sig = tags_to_html($uent->sig, $FUD_OPT_1 & 524288);
		} else if ($FUD_OPT_1 & 65536) {
			$uent->sig = nl2br(htmlspecialchars($uent->sig));
		}

		if ($FUD_OPT_1 & 196608) {
			char_fix($uent->sig);
		}

		if ($FUD_OPT_1 & 262144) {
			$uent->sig = smiley_to_post($uent->sig);
		}
		fud_wordwrap($uent->sig);

		if ($uent->bio) {
			$uent->bio = htmlspecialchars($uent->bio);
			char_fix($uent->bio);
		}

		if (!$uent->icq && !($uent->users_opt & 4)) {
			$uent->users_opt |= 4;
		}

		/* Restore avatar values to their previous values */
		$uent->avatar = $old_avatar;
		$uent->avatar_loc = $old_avatar_loc;
		$old_opt = $uent->users_opt & (4194304|16777216|8388608);
		$uent->users_opt |= 4194304|16777216|8388608;

		/* prevent non-confirmed users from playing with avatars, yes we are that cruel */
		if ($FUD_OPT_1 & 28 && _uid) {
			if ($_POST['avatar_type'] == 'b') { /* built-in avatar */
				if (!$old_avatar && $old_avatar_loc) {
					remove_old_avatar($old_avatar_loc);
					$uent->avatar_loc = '';
				} else if (isset($avatar_arr['file'])) {
					@unlink($TMP . basename($avatar_arr['file']));
				}
				if ($_POST['reg_avatar'] == '0') {
					$uent->avatar_loc = '';
					$uent->avatar = 0;
				} else if ($uent->avatar != $_POST['reg_avatar'] && ($img = q_singleval('SELECT img FROM {SQL_TABLE_PREFIX}avatar WHERE id='.(int)$_POST['reg_avatar']))) {
					/* verify that the avatar exists and it is different from the one in DB */
					$uent->avatar_loc = make_avatar_loc('images/avatars/' . $img, $WWW_ROOT_DISK, $WWW_ROOT);
					$uent->avatar = $_POST['reg_avatar'];
				}
				if ($uent->avatar && $uent->avatar_loc) {
					$uent->users_opt ^= 4194304|16777216;
				}
			} else {
				if ($_POST['avatar_type'] == 'c' && isset($reg_avatar_loc_file)) { /* New URL avatar */
					$common_av_name = $reg_avatar_loc_file;

					if (!empty($avatar_arr['file'])) {
						$avatar_arr['del'] = 1;
					}
				} else if ($_POST['avatar_type'] == 'u' && empty($avatar_arr['del']) && empty($avatar_arr['leave'])) { /* uploaded file */
					$common_av_name = $avatar_arr['file'];
				}

				/* remove old avatar if need be */
				if (!empty($avatar_arr['del'])) {
					if (empty($avatar_arr['leave'])) {
						@unlink($TMP . basename($avatar_arr['file']));
					} else {
						remove_old_avatar($old_avatar_loc);
					}
				}

				/* add new avatar if needed */
				if (isset($common_av_name)) {
					$common_av_name = basename($common_av_name);
					$av_path = 'images/custom_avatars/' . substr($common_av_name, 0, strpos($common_av_name, '_'));
					copy($TMP . basename($common_av_name), $WWW_ROOT_DISK . $av_path);
					@unlink($TMP . basename($common_av_name));
					if (($uent->avatar_loc = make_avatar_loc($av_path, $WWW_ROOT_DISK, $WWW_ROOT))) {
						if (!($FUD_OPT_1 & 32) || $uent->users_opt & 1048576) {
							$uent->users_opt ^= 16777216|4194304;
						} else {
							$uent->users_opt ^= 8388608|4194304;
						}
					}
				} else if (empty($avatar_arr['leave']) || !empty($avatar_arr['del'])) {
					$uent->avatar_loc = '';
				} else if (!empty($avatar_arr['leave'])) {
					$uent->users_opt ^= (8388608|16777216|4194304) ^ $old_opt;
				}
				$uent->avatar = 0;
			}
			if (empty($uent->avatar_loc)) {
				$uent->users_opt ^= 8388608|16777216;
			}
		} else {
			$uent->users_opt ^= (8388608|16777216|4194304) ^ $old_opt;
		}

		$uent->sync_user();

		if (!$mod_id) {
			check_return($usr->returnto);
		} else {
			header('Location: adm/admuser.php?usr_id='.$uent->id.'&'._rsidl.'&act=nada');
			exit;
		}
	}

	/* populate form variables based on user's profile */
	if (!isset($_POST['prev_loaded'])) {
		foreach ($uent as $k => $v) {
			${'reg_'.$k} = htmlspecialchars($v);
		}
		reverse_fmt($reg_sig);
		$reg_sig = apply_reverse_replace($reg_sig);

		if ($FUD_OPT_1 & 262144) {
			$reg_sig = post_to_smiley($reg_sig);
		}

		if ($FUD_OPT_1 & 131072) {
			$reg_sig = html_to_tags($reg_sig);
		} else if ($FUD_OPT_1 & 65536) {
			reverse_nl2br($reg_sig);
		}

		if ($FUD_OPT_1 & 196608) {
			char_fix($reg_sig);
		}
		char_fix($reg_bio);

		if ($uent->bday) {
			$b_year = substr($uent->bday, 0, 4);
			$b_month = substr($uent->bday, 4, 2);
			$b_day = substr($uent->bday, 6, 8);
		} else {
			$b_year = $b_month = $b_day = '';
		}
		if (!$reg_avatar && $reg_avatar_loc) { /* custom avatar */
			reverse_fmt($reg_avatar_loc);
			if (preg_match('!src="([^"]+)" width="!', $reg_avatar_loc, $tmp)) {
				$avatar_arr['file'] = $tmp[1];
				$avatar_arr['del'] = 0;
				$avatar_arr['leave'] = 1;
				$avatar_type = 'u';
			}
		}
		reverse_fmt($reg_alias);
	} else if (isset($_POST['prev_loaded'])) { /* import data from POST data */
		foreach ($_POST as $k => $v) {
			if (!strncmp($k, 'reg_', 4)) {
				${$k} = htmlspecialchars($v);
			}
		}
		char_fix($reg_bio);
		char_fix($reg_sig);

		$b_year = $_POST['b_year'];
		$b_month = $_POST['b_month'];
		$b_day = $_POST['b_day'];
		if (isset($_POST['avatar_type'])) {
			$avatar_type = $_POST['avatar_type'];
		}
	}

	if (empty($reg_time_zone)) {
		$reg_time_zone = $SERVER_TZ;
	}

	if (!$mod_id) {
		ses_update_status($usr->sid, '{TEMPLATE: register_profile_update}', 0, 0);
	}

	$TITLE_EXTRA = ': {TEMPLATE: register_title}';

/*{POST_HTML_PHP}*/

	$reg_sig_err	= draw_err('reg_sig');

	if ($FUD_OPT_2 & 2048) {
		$affero_domain = parse_url($WWW_ROOT);
		$register_affero = '{TEMPLATE: register_affero}';
	} else {
		$register_affero = '';
	}

	/* Initialize avatar options */
	$avatar = $avatar_type_sel = '';

	$reg_time_limit_err = '';

	$avatar_err = draw_err('avatar');

	$submit_button = '{TEMPLATE: update_button}';

	if ($FUD_OPT_1 & 28 && _uid) {
		if ($FUD_OPT_1 == 28) {
			/* if there are no built-in avatars, don't show them */
			if (q_singleval('SELECT count(*) FROM {SQL_TABLE_PREFIX}avatar')) {
				$sel_opt = "{TEMPLATE: register_builtin}\n{TEMPLATE: register_specify_url}\n{TEMPLATE: register_uploaded}";
				$a_type='b';
				$sel_val = "b\nc\nu";
			} else {
				$sel_opt = "{TEMPLATE: register_specify_url}\n{TEMPLATE: register_uploaded}";
				$a_type='u';
				$sel_val = "c\nu";
			}
		} else {
			$sel_opt = $sel_val = '';

			if (q_singleval('SELECT count(*) FROM {SQL_TABLE_PREFIX}avatar') && $FUD_OPT_1 & 16) {
				$sel_opt .= "{TEMPLATE: register_builtin}\n";
				$a_type = 'b';
				$sel_val .= "b\n";
			}
			if ($FUD_OPT_1 & 8) {
				$sel_opt .= "{TEMPLATE: register_uploaded}\n";
				if (!isset($a_type)) {
					$a_type = 'u';
				}
				$sel_val .= "u\n";
			}
			if ($FUD_OPT_1 & 4) {
				$sel_opt .= "{TEMPLATE: register_specify_url}\n";
				if (!isset($a_type)) {
					$a_type = 'c';
				}
				$sel_val .= "c\n";
			}
			$sel_opt = trim($sel_opt);
			$sel_val = trim($sel_val);
		}
		if (isset($a_type)) { /* rare condition, no built-in avatars & no other avatars are allowed */
			if (!isset($avatar_type)) {
				$avatar_type = $a_type;
			}
			$avatar_type_sel_options = tmpl_draw_select_opt($sel_val, $sel_opt, $avatar_type, '{TEMPLATE: sel_opt}', '{TEMPLATE: sel_opt_selected}');
			$avatar_type_sel = '{TEMPLATE: avatar_type_sel}';

			/* preview image */
			if (isset($_POST['prev_loaded'])) {
				if ((!empty($_POST['reg_avatar']) && $_POST['reg_avatar'] == $uent->avatar) || (!empty($avatar_arr['file']) && empty($avatar_arr['del']) && $avatar_arr['leave'])) {
					$custom_avatar_preview = $uent->avatar_loc;
				} else if (!empty($_POST['reg_avatar']) && ($im = q_singleval('SELECT img FROM {SQL_TABLE_PREFIX}avatar WHERE id='.(int)$_POST['reg_avatar']))) {
					$custom_avatar_preview = make_avatar_loc('images/avatars/' . $im, $WWW_ROOT_DISK, $WWW_ROOT);
				} else {
					if (isset($reg_avatar_loc_file)) {
						$common_name = $reg_avatar_loc_file;
					} else if (!empty($avatar_arr['file']) && empty($avatar_arr['del'])) {
						$common_name = $avatar_arr['file'];
					}
					if (isset($common_name)) {
						$custom_avatar_preview = make_avatar_loc(basename($common_name), $TMP, '{ROOT}?t=tmp_view&img=');
					}
				}
			} else if ($uent->avatar_loc) {
				$custom_avatar_preview = $uent->avatar_loc;
			}

			if (!isset($custom_avatar_preview)) {
				$custom_avatar_preview = '<img src="blank.gif" />';
			}

			/* determine the avatar specification field to show */
			if ($avatar_type == 'b') {
				if (empty($reg_avatar)) {
					$reg_avatar = '0';
					$reg_avatar_img = 'blank.gif';
				} else if (!empty($reg_avatar_loc)) {
					reverse_fmt($reg_avatar_loc);
					preg_match('!images/avatars/([^"]+)"!', $reg_avatar_loc, $tmp);
					$reg_avatar_img = 'images/avatars/' . $tmp[1];
				} else {
					$reg_avatar_img = 'images/avatars/' . q_singleval('SELECT img FROM {SQL_TABLE_PREFIX}avatar WHERE id='.(int)$reg_avatar);
				}
				$del_built_in_avatar = $reg_avatar ? '{TEMPLATE: del_built_in_avatar}' : '';
				$avatar = '{TEMPLATE: built_in_avatar}';
			} else if ($avatar_type == 'c') {
				if (!isset($reg_avatar_loc)) {
					$reg_avatar_loc = '';
				}
				$avatar = '{TEMPLATE: custom_url_avatar}';
			} else if ($avatar_type == 'u') {
				$avatar_tmp = $avatar_arr ? base64_encode($avatar_arr['file'] . "\n" . $avatar_arr['del'] . "\n" . $avatar_arr['leave']) : '';
				$buttons = (!empty($avatar_arr['file']) && empty($avatar_arr['del'])) ? '{TEMPLATE: delete_uploaded_avatar}' : '{TEMPLATE: upload_avatar}';
				$avatar = '{TEMPLATE: custom_upload_avatar}';
			}
		}
	}

	$post_options = tmpl_post_options('sig');

	$theme_select = create_theme_select('reg_theme', $reg_theme);

	$day_select		= tmpl_draw_select_opt("\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12\n13\n14\n15\n16\n17\n18\n19\n20\n21\n22\n23\n24\n25\n26\n27\n28\n29\n30\n31", "\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12\n13\n14\n15\n16\n17\n18\n19\n20\n21\n22\n23\n24\n25\n26\n27\n28\n29\n30\n31", $b_day, '{TEMPLATE: sel_opt}', '{TEMPLATE: sel_opt_selected}');
	$month_select		= tmpl_draw_select_opt("\n1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12", "\n{TEMPLATE: month_1}\n{TEMPLATE: month_2}\n{TEMPLATE: month_3}\n{TEMPLATE: month_4}\n{TEMPLATE: month_5}\n{TEMPLATE: month_6}\n{TEMPLATE: month_7}\n{TEMPLATE: month_8}\n{TEMPLATE: month_9}\n{TEMPLATE: month_10}\n{TEMPLATE: month_11}\n{TEMPLATE: month_12}", $b_month, '{TEMPLATE: sel_opt}', '{TEMPLATE: sel_opt_selected}');
	$gender_select		= tmpl_draw_select_opt("512\n1024\n0","{TEMPLATE: unspecified}\n{TEMPLATE: male}\n{TEMPLATE: female}", ($uent->users_opt & 512 ? 512 : ($uent->users_opt & 1024)), '{TEMPLATE: sel_opt}', '{TEMPLATE: sel_opt_selected}');
	$mppg_select		= tmpl_draw_select_opt("0\n5\n10\n20\n30\n40", "{TEMPLATE: use_forum_default}\n5\n10\n20\n30\n40", $reg_posts_ppg, '{TEMPLATE: sel_opt}', '{TEMPLATE: sel_opt_selected}');
	$view_select		= tmpl_draw_select_opt("384\n128".($FUD_OPT_2 & 512 ?"\n256\n0":''), "{TEMPLATE: register_flat_view}\n{TEMPLATE: register_msg_tree_view}".($FUD_OPT_2 & 512 ? "\n{TEMPLATE: register_tree_msg_view}\n{TEMPLATE: register_tree_view}":''), ($uent->users_opt & (128|256)), '{TEMPLATE: sel_opt}', '{TEMPLATE: sel_opt_selected}');
	$timezone_select	= tmpl_draw_select_opt($tz_values, $tz_names, $reg_time_zone, '{TEMPLATE: sel_opt}', '{TEMPLATE: sel_opt_selected}');
	$notification_select	= tmpl_draw_select_opt("4\n0", "{TEMPLATE: register_email}\n{TEMPLATE: register_icq}", ($uent->users_opt & 4), '{TEMPLATE: sel_opt}', '{TEMPLATE: sel_opt_selected}');

	$ignore_admin_radio	= tmpl_draw_radio_opt('reg_ignore_admin', "8\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 8), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$invisible_mode_radio	= tmpl_draw_radio_opt('reg_invisible_mode', "32768\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 32768), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$show_email_radio	= tmpl_draw_radio_opt('reg_display_email', "1\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 1), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$notify_default_radio	= tmpl_draw_radio_opt('reg_notify', "2\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 2), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$pm_notify_default_radio= tmpl_draw_radio_opt('reg_pm_notify', "64\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 64), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$accept_user_email	= tmpl_draw_radio_opt('reg_email_messages', "16\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 16), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$accept_pm		= tmpl_draw_radio_opt('reg_pm_messages', "32\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 32), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$show_sig_radio		= tmpl_draw_radio_opt('reg_show_sigs', "4096\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 4096), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$show_avatar_radio	= tmpl_draw_radio_opt('reg_show_avatars', "8192\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 8192), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$show_im_radio		= tmpl_draw_radio_opt('reg_show_im', "16384\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 16384), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');
	$append_sig_radio	= tmpl_draw_radio_opt('reg_append_sig', "2048\n0", "{TEMPLATE: yes}\n{TEMPLATE: no}", ($uent->users_opt & 2048), '{TEMPLATE: radio_button}', '{TEMPLATE: radio_button_selected}', '{TEMPLATE: radio_button_separator}');

	$reg_user_image_field = $FUD_OPT_2 & 65536 ? '{TEMPLATE: reg_user_image}' : '';
	$sig_len_limit = $FORUM_SIG_ML ? '{TEMPLATE: register_sig_limit}' : '';

/*{POST_PAGE_PHP_CODE}*/
?>
{TEMPLATE: REGISTER_PAGE}
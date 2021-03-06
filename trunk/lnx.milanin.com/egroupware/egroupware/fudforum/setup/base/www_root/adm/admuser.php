<?php
/***************************************************************************
* copyright            : (C) 2001-2003 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: admuser.php,v 1.3 2004/01/29 16:34:55 iliaa Exp $
*
* This program is free software; you can redistribute it and/or modify it 
* under the terms of the GNU General Public License as published by the 
* Free Software Foundation; either version 2 of the License, or 
* (at your option) any later version.
***************************************************************************/

	require('./GLOBALS.php'); fud_egw();
	fud_use('adm.inc', true);
	fud_use('customtags.inc', true);
	fud_use('users_reg.inc');
	fud_use('users_adm.inc', true);
	fud_use('logaction.inc');
	fud_use('iemail.inc');
	fud_use('private.inc');

	if (isset($_GET['act'], $_GET['usr_id'])) {
		$act = $_GET['act'];
		$usr_id = (int)$_GET['usr_id'];
	} else if (isset($_POST['act'], $_POST['usr_id'])) {
		$act = $_POST['act'];
		$usr_id = (int)$_POST['usr_id'];
	} else {
		$usr_id = $act = '';
	}
	if ($act && $usr_id && !($u = db_sab('SELECT * FROM '.$DBHOST_TBL_PREFIX.'users WHERE id='.$usr_id))) {
		$usr_id = $act = '';
	}

	switch ($act) {
		case 'block':
			if ($u->users_opt & 65536) {
				q('UPDATE '.$DBHOST_TBL_PREFIX.'users SET users_opt=users_opt & ~ 65536 WHERE id='.$usr_id);
				$u->users_opt ^= 65536;
			} else {
				q('UPDATE '.$DBHOST_TBL_PREFIX.'users SET users_opt=users_opt|65536 WHERE id='.$usr_id);
				$u->users_opt |= 65536;
			}

			if (isset($_GET['f'])) {
				header('Location: '.$WWW_ROOT.__fud_index_name__.$usr->returnto);
				exit;
			}
			break;
		case 'color':
			$u->custom_color = trim($_POST['custom_color']);
			q('UPDATE '.$DBHOST_TBL_PREFIX.'users SET custom_color='.strnull(addslashes($u->custom_color)).' WHERE id='.$usr_id);
			break;
		case 'admin':
			if ($u->users_opt & 1048576) {
				if (!isset($_POST['adm_confirm'])) {
?>
<html>
<title>Adminstrator confirmation</title>
<body color="white">
<form method="post" action="admuser.php"><?php echo _hs; ?>
<input type="hidden" name="act" value="admin">
<input type="hidden" name="usr_id" value="<?php echo $usr_id; ?>">
<input type="hidden" name="adm_confirm" value="1">
<div align="center">You are taking away administration privileges from <font color="red"><b><?php echo $u->alias; ?></b></font>!<br><br>
Are you sure you want to do this?<br>
<input type="submit" value="Yes" name="btn_yes"> <input type="submit" value="No" name="btn_no">
</div>
</form>
</body></html>
<?php
					exit;
				} else if (isset($_POST['btn_yes'])) {
					if (q_singleval('SELECT count(*) FROM '.$DBHOST_TBL_PREFIX.'mod WHERE user_id='.$u->id)) {
						q('UPDATE '.$DBHOST_TBL_PREFIX.'users SET users_opt=(users_opt & ~ 1048576) |524288 WHERE id='.$usr_id);
						$u->users_opt ^= 1048576;
					} else {
						q('UPDATE '.$DBHOST_TBL_PREFIX.'users SET users_opt=users_opt & ~ (524288|1048576) WHERE id='.$usr_id);
						$u->users_opt = $u->users_opt & ~ (1048576|524288);
					}
				}
			} else {
				if (!isset($_POST['adm_confirm'])) {
?>
<html>
<title>Adminstrator confirmation</title>
<body color="white">
<form method="post" action="admuser.php"><?php echo _hs; ?>
<input type="hidden" name="act" value="admin">
<input type="hidden" name="adm_confirm" value="1">
<input type="hidden" name="usr_id" value="<?php echo $usr_id; ?>">
<div align="center">WARNING: Making <font color="red"><b><?php echo $u->alias; ?></b></font> an <font color="red"><b>administrator</b></font> will give this person full
administration permissions to the forum. This individual will be able to do anything with the forum, including taking away your own administration permissions.
<br><br>Are you sure you want to do this?<br>
<input type="submit" value="Yes" name="btn_yes"> <input type="submit" value="No" name="btn_no">
</div>
</form>
</body></html>
<?php
					exit;
				} else if (isset($_POST['btn_yes'])) {
					q('UPDATE '.$DBHOST_TBL_PREFIX.'users SET users_opt=(users_opt & ~ 524288) | 1048576 WHERE id='.$usr_id);
					$u->users_opt |= 1048576;
				}
			}
			break;
	}

	$search_error = $login_error = '';
	if ($usr_id) {
		/* deal with custom tags */
		if (!empty($_POST['c_tag'])) {
			q('INSERT INTO '.$DBHOST_TBL_PREFIX.'custom_tags (name, user_id) VALUES('.strnull(addslashes($_POST['c_tag'])).', '.$usr_id.')');
		} else if (!empty($_GET['deltag'])) {
			q('DELETE FROM '.$DBHOST_TBL_PREFIX.'custom_tags WHERE id='.(int)$_GET['deltag']);
		} else {
			$nada = 1;
		}
		if (!isset($nada) && db_affected()) {
			ctag_rebuild_cache($usr_id);
		}
	} else if (!empty($_POST['usr_email']) || !empty($_POST['usr_login'])) {
		/* user searching logic */
		$item = !empty($_POST['usr_email']) ? $_POST['usr_email'] : $_POST['usr_login'];
		$field = !empty($_POST['usr_email']) ? 'email' : ($FUD_OPT_2 & 128 ? 'alias' : 'login');
		if (strpos($item, '*') !== false) {
			$like = 1;
			$item = str_replace('*', '%', $item);
			$item_s = str_replace('\\', '\\\\', $item);
			if ($FUD_OPT_2 & 128) {
				$item_s = htmlspecialchars($item_s);
			}
		} else {
			$like = 0;
			$item_s = $item;
		}
		$item_s = "'" . addslashes($item_s) . "'";

		$c = q('SELECT id, alias, email FROM '.$DBHOST_TBL_PREFIX.'users WHERE ' . $field . ($like ? ' LIKE ' : '=') . $item_s .' LIMIT 50');
		switch (($cnt = db_count($c))) {
			case 0:
				$search_error = errorify('There are no users matching the specified '.$field.' mask.');
				unset($c);
				break;
			case 1:
				list($usr_id) = db_rowarr($c);
				$u = db_sab('SELECT * FROM '.$DBHOST_TBL_PREFIX.'users WHERE id='.$usr_id);
				unset($c);
				break;
			default:
				echo 'There are '.$cnt.' users that match this '.$field.' mask:<br>';
				while ($r = db_rowarr($c)) {
					echo '<a href="admuser.php?usr_id='.$r[0].'&act=m&'._rsidl.'">Pick user</a> <b>'.$r[1].' / '.htmlspecialchars($r[2]).'</b><br>';
				}
				unset($c);
				exit;
				break;
		}
	}

	require($WWW_ROOT_DISK . 'adm/admpanel.php');
?>
<h2>User Adminstration System</h2>
<form name="frm_usr" method="post" action="admuser.php">
<?php echo _hs . $search_error; ?>
<table class="datatable solidtable">
	<tr class="field">
		<td colspan=2>Search for User</td>
	</tr>

	<tr class="field">
		<td>By <?php echo ($FUD_OPT_2 & 128 ? 'Alias' : 'Login'); ?>:</td>
		<td><input type="text" name="usr_login"></td>
	</tr>

	<tr class="field">
		<td>By Email:</td>
		<td><input type="text" name="usr_email"></td>
	</tr>

	<tr class="fieldaction">
		<td colspan=2 align=right><input type="submit" value="Search" name="usr_search"></td>
	</tr>
</table>
</form>
<?php if ($usr_id) { ?>
<table class="datatable solidtable">
	<tr class="field"><td>Loing:</td><td><?php echo $u->alias; ?></td></tr>
	<tr class="field"><td>Email:</td><td><?php echo $u->email; ?></td></tr>
	<tr class="field"><td>Name:</td><td><?php echo $u->name; ?></td></tr>
<?php
	if ($u->bday) {
		echo '<tr class="field"><td>Birthday:</td><td>' . strftime('%B, %d, %Y', strtotime($u->bday)) . '</td></tr>';
	}

	echo '<tr class="field"><td align=middle colspan=2><font size="+1">&gt;&gt; <a href="../'.__fud_index_name__.'?t=register&mod_id='.$usr_id.'&'._rsidl.'">Change User\'s Profile</a> &lt;&lt;</font></td></tr>';
	echo '<tr class="field"><td nowrap><font size="+1"><b>Forum Administrator:</b></td><td>'.($u->users_opt & 1048576 ? '<b><font size="+2" color="red">Y</font>' : 'N').' [<a href="admuser.php?act=admin&usr_id='.$usr_id . '&' . _rsidl.'">Toggle</a>]</td></tr>';
	echo '<tr class="field"><td>Blocked (banned):</td><td>'.($u->users_opt & 65536 ? 'Yes' : 'No').' [<a href="admuser.php?act=block&usr_id=' . $usr_id . '&' . _rsidl.'">Toggle</a>]</td></tr>';
	echo '<tr class="field"><td>Email Confirmation:</td><td>'.($u->users_opt & 131072 ? 'Yes' : 'No').' [<a href="admuser.php?act=econf&usr_id=' . $usr_id . '&' . _rsidl .'">Toggle</a>]</td></tr>';

	if ($FUD_OPT_1 & 1048576) {
		echo '<tr class="field"><td>COPPA:</td><td>'.($u->users_opt & 262144 ? 'Yes' : 'No').' [<a href="admuser.php?act=coppa&usr_id=' . $usr_id . '&' . _rsidl .'">Toggle</a>]</td></tr>';
	}

	echo '<tr class="field"><td nowrap valign="top">Moderating Forums:</td><td valign="top">';
	$c = q('SELECT f.name FROM '.$DBHOST_TBL_PREFIX.'mod mm INNER JOIN '.$DBHOST_TBL_PREFIX.'forum f ON mm.forum_id=f.id WHERE mm.user_id='.$usr_id);
	if (db_count($c)) {
		echo '<table border=0 cellspacing=1 cellpadding=3>';
		while ($r = db_rowarr($c)) {
			echo '<tr><td>'.$r[0].'</td></tr>';
		}
		echo '</table>';
	} else {
		echo 'None<br>';
	}
	unset($c);
?>
	<a name="mod_here"> </a>
	<a href="#mod_here" onClick="javascript: window.open('admmodfrm.php?usr_id=<?php echo $usr_id . '&' . _rsidl; ?>', 'frm_mod', 'menubar=false,width=200,height=400,screenX=100,screenY=100,scrollbars=yes');">Modify Moderation Permissions</a>
	<tr class="field"><td valign=top>Custom Tags:</td><td valign="top">
<?php
	$c = uq('SELECT name, id FROM '.$DBHOST_TBL_PREFIX.'custom_tags WHERE user_id='.$usr_id);
	while ($r = db_rowarr($c)) {
		echo $r[0] . ' [<a href="admuser.php?act=nada&usr_id='.$usr_id.'&deltag=' . $r[1] . '&' . _rsidl . '">Delete</a>]<br>';
	}
?>
	<form name="extra_tags" action="admuser.php" method="post">
	<?php echo _hs; ?>
	<input type="text" name="c_tag">
	<input type="submit" value="Add">
	<input type="hidden" name="usr_id" value="<?php echo $usr_id; ?>">
	<input type="hidden" name="act" value="nada">
	</form>
	</td></tr>

	<tr class="field"><td valign=top>Profile Link Color:</td>
		<td valign=top>
		<form name="extra_tags" method="post" action="admuser.php">
		<?php echo _hs; ?>
		<input type="text" name="custom_color" maxLength="255" value="<?php echo $u->custom_color; ?>">
		<input type="hidden" name="usr_id" value="<?php echo $usr_id; ?>">
		<input type="hidden" name="act" value="color"><input type="submit" value="Change">
		</form>
		</td>
	</tr>
	<tr class="field">
		<td colspan=2><br><br><b>Actions:</b></td>
	</tr>

	<tr class="field">
	<td colspan=2>
<?php
	if ($FUD_OPT_1 & 1024) {
		echo '<a href="../'.__fud_index_name__.'?t=ppost&'._rsidl.'&toi='.$usr_id.'">Send Private Message</a> | ';
	}
	if ($FUD_OPT_1 & 4194304) {
		echo '<a href="../'.__fud_index_name__.'?t=email&toi='.$usr_id.'&'._rsidl.'">Send Email</a> | ';
	} else {
		echo '<a href="mailto:'.$u->email.'">Send Email</a> | ';
	}

	echo '	<a href="../'.__fud_index_name__.'?t=showposts&id='.$usr_id.'&'._rsid.'">See Posts</a></td></tr>';
}
?>
</table>
<?php require($WWW_ROOT_DISK . 'adm/admclose.php'); ?>
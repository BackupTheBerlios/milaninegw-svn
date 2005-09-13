<?php
	/****************************************************************************\
	* phpGroupWare - FUDforum 2.6.0 equivalent                                   *
	* http://fud.prohost.org/                                                    *
	* Written by Ilia Alshanetsky <ilia@prohost.org>                             *
	* -------------------------------------------                                *
	*  This program is free software; you can redistribute it and/or modify it   *
	*  under the terms of the GNU General Public License as published by the     *
	*  Free Software Foundation; either version 2 of the License, or (at your    *
	*  option) any later version.                                                *
	\****************************************************************************/

	class ufud
	{
		/* array(
			'account_id'  => // numerical user id
		        'account_lid' => // account-name
		        'account_status' => // 'A' on active, else empty
		        'account_firstname' => // guess what ;-)
		        'account_lastname' => //
		        'new_passwd' => //
		        'location' => 'addaccount')
		*/
		function __get_email($id)
		{
			$preferences = CreateObject('phpgwapi.preferences', $id);
			$preferences->read_repository();
			return $preferences->email_address($id);
		}

		function add_account($row)
		{
			$GLOBALS['phpgw']->db->query("SELECT id FROM phpgw_fud_themes WHERE (theme_opt & 2) > 0 LIMIT 1");
			$theme = $GLOBALS['phpgw']->db->row(true);
			$theme = $theme['id'] ? (int) $theme['id'] : 1;
			$email = addslashes($this->__get_email($row['account_id']));
			$name = addslashes($row['account_firstname'] . ' ' . $row['account_lastname']);
			$egw_id = $row['account_id'];
			$alias = addslashes(htmlspecialchars($row['account_lid']));
			$login = addslashes($row['account_lid']);
			$users_opt = 2|4|16|32|64|128|256|512|2048|4096|8192|16384|131072|4194304;
			if ($row['account_status'] != 'A') {
				$user_opts |= 2097152;
			}
			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_fud_users (last_visit, join_date, theme, alias, login, email, passwd, name, users_opt, egw_id) VALUES(".time().", ".time().", {$theme}, '{$alias}', '{$login}', '{$email}', '{$row['account_pwd']}', '{$name}', {$users_opt}, {$egw_id})");
		}

		/* array(
			'account_id'  => // numerical user id
		        'account_lid' => // account-name
		        'account_status' => // 'A' on active, else empty
		        'account_firstname' => // guess what ;-)
		        'account_lastname' => //
		        'location' => 'changepassword'
		) */
		function chg_settings($row)
		{
			if (isset($_GET, $_POST, $_GET['account_id']) && count($_POST) && ($ac_id = (int) $_GET['account_id'])) {
				$acc = new accounts();
				$acc->account_id = $ac_id;
				$acc->read_repository();
			
				if ($acc->data['account_id']) {
					$email = addslashes($this->__get_email($ac_id));
					$name = addslashes($$acc->data['firstname'] . ' ' . $acc->data['lastname']);
					$login = addslashes($acc->data['account_lid']);
					$alias = addslashes(htmlspecialchars($acc->data['account_lid']));
					$status = ($acc->data['status'] != 'A') ? 'users_opt & ~ 2097152' : 'users_opt|2097152';
					$GLOBALS['phpgw']->db->query("UPDATE phpgw_fud_users SET name='{$name}', email='{$email}', login='{$login}', alias='{$alias}', users_opt={$status} WHERE egw_id=".$ac_id);
				}
			}
		}

		/* array(
			'account_id'  => // numerical user id
		        'account_lid' => // account-name
		) */

		function del_account($row)
		{
			$ac_id = (int) $row['account_id'];
			if (!$ac_id) {
				return;
			}

			define('plain_page', 1);
			$db =& $GLOBALS['phpgw']->db;
			$server =& $GLOBALS['phpgw_info']['server'];

			require($server['files_dir'] . "/fudforum/".sprintf("%u", crc32($GLOBALS['phpgw_info']['user']['domain']))."/include/GLOBALS.php");

			if (!empty($server['use_adodb']) || empty($db->Link_ID) || !is_resource($db->Link_ID)) {
				// open your own connection, as ADOdb does not export the use Link_ID
				switch ($server['db_type']) {
					case 'mysql':
						$func = $server['db_persistent'] ? 'mysql_pconnect' : 'mysql_connect';
						define('fud_sql_lnk',$func($db->Host, $db->User, $db->Password));
						mysql_select_db($db->Database,fud_sql_lnk);
						break;

					case 'pgsql':
						$func = $server['db_persistent'] ? 'pg_pconnect' : 'pg_connect';
						define('fud_sql_lnk',$func('dbname='.$db->Database.' host='.$db->Host.' user='.$db->User.' password='.$db->Password));
						break;

					default:
						die('FUDforum only supports mysql or pgsql !!!');
				}
				unset($func);
			} else {
				define('fud_sql_lnk', $db->Link_ID);
			}

			fud_use('db.inc');
			fud_use('private.inc');
			fud_use('users_reg.inc');
			fud_use('users_adm.inc', true);
			$GLOBALS['DBHOST_TBL_PREFIX'] = 'phpgw_fud_';
			$id = q_singleval("SELECT id FROM phpgw_fud_users WHERE egw_id=".$ac_id);
			if ($id) {
				usr_delete($id);
			}
		}
	}
?>

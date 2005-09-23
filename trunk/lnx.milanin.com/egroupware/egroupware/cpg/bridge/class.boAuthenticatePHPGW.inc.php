<?php
/*
 * $Id: class.boAuthenticatePHPGW.inc.php,v 1.1.2.1 2004/01/01 18:15:31 mdean Exp $
 *
 * Double Choco Latte - Source Configuration Management System
 * Copyright (C) 1999  Michael L. Dean & Tim R. Norman
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Select License Info from the Help menu to view the terms and conditions of this license.
 */

import('boAuthenticate');
class boAuthenticatePHPGW extends boAuthenticate
{
	var $_sqlFallback;

	function boAuthenticatePHPGW()
	{
		parent::boAuthenticate();
	}

	function _SetCredentials()
	{
		$this->_uid = $GLOBALS['phpgw_info']['user']['account_lid'];
		$this->_pwd = '';
	}

	function _SetQuery()
	{
		$this->_sql = sprintf("SELECT id, security, short, email FROM personnel WHERE short='%s' AND active='Y'", $this->_uid);
		$this->_sqlFallback = "select id, short, security, email from personnel where short='sa'";
	}

	function IsValidLogin(&$aAuthInfo)
	{
		global $phpgw_info;

		// phpGroupWare authentication - just lookup by active login
		$this->_oDB->Query($this->_sql);
		if (!$this->_oDB->next_record())
		{
			$this->_oDB->FreeResult();
			if (isset($phpgw_info['user']['apps']['admin']) && is_array($phpgw_info['user']['apps']['admin']))
			{
				// Not in user table, but is phpgw admin, so load sa account
				$this->_oDB->Query($this->_sqlFallback);
				$this->_oDB->next_record();
			}
		}

		if (is_array($this->_oDB->Record))
		{
			$aAuthInfo = array(
					'id' => $this->_oDB->f(0),
					'security' => $this->_oDB->f(2),
					'short' => $this->_oDB->f(1),
					'email' => $this->_oDB->f(3)
				);

			$this->_oDB->FreeResult();

			return true;
		}

		$this->_oDB->FreeResult();

		return false;
	}
}
?>

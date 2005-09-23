<?php
  /**************************************************************************\
  * eGroupWare - Addressbook                                                 *
  * http://www.egroupware.org                                                *
  * Written by Joseph Engo <jengo@phpgroupware.org                           *
  *  and Miles Lott <milos@groupwhere.org>                                   *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: class.soaddressbook.inc.php,v 1.23 2004/02/15 18:21:35 milosch Exp $ */

	class soaddressbook
	{
		var $contacts;
		var $rights;
		var $grants;
		var $owner;

		function soaddressbook()
		{
			if(!is_object($GLOBALS['phpgw']->contacts))
			{
				$GLOBALS['phpgw']->contacts = CreateObject('phpgwapi.contacts');
			}
			$this->contacts = &$GLOBALS['phpgw']->contacts;
			$this->grants = &$this->contacts->grants;

			/* _debug_array($GLOBALS['phpgw_info']); */
			/* _debug_array($grants); */
		}

		function read_entries($data)
		{
//			echo 'OK!';
//			_debug_array($data);exit;
			return $this->contacts->read(
				$data['start'],
				$data['limit'],
				$data['fields'],
				$data['query'],
				$data['filter'],
				$data['sort'],
				$data['order'],
				-1,
				$data['cquery']
			);
		}

		function read_entry($id,$fields)
		{
			return $this->contacts->read_single_entry($id,$fields);
		}

		function read_last_entry($fields)
		{
			return $this->contacts->read_last_entry($fields);
		}

		function add_entry($fields)
		{
			$owner  = $fields['owner'];
			$access = $fields['access'];
			$cat_id = $fields['cat_id'];
			$tid    = $fields['tid'];
			unset($fields['owner']);
			unset($fields['access']);
			unset($fields['cat_id']);
			unset($fields['ab_id']);
			unset($fields['tid']);

			return $this->contacts->add($owner,$fields,$access,$cat_id,$tid);
		}

		function get_lastid()
		{
			$entry = $this->contacts->read_last_entry();
			return $entry[0]['id'];
		}

		function update_entry($fields)
		{
			$ab_id  = isset($fields['ab_id']) ? $fields['ab_id'] : $fields['id'];
			$owner  = $fields['owner'];
			unset($fields['owner']);
			unset($fields['ab_id']);
			unset($fields['id']);

			return $this->contacts->update($ab_id,$owner,$fields);
		}

		function delete_entry($id)
		{
			return $this->contacts->delete($id);
		}
	}
?>

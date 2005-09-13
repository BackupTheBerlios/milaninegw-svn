<?php
  /**************************************************************************\
  * eGroupWare - Preferences                                                 *
  * http://www.egroupware.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_settings.inc.php,v 1.3.2.1 2004/08/21 19:22:16 dragob Exp $ */

  $yes_and_no = array(
    'True'  => lang('Yes'),
    'False' => lang('No')
  );
  create_select_box('show new/updated tickets on main screen','mainscreen_show_new_updated',$yes_and_no);

  $acc = CreateObject('phpgwapi.accounts');
  $group_list = $acc->get_list('groups');
  while (list($key,$entry) = each($group_list))
  {
    $_groups[$entry['account_id']] = $entry['account_lid'];
  }
  create_select_box('Default group','groupdefault',$_groups);

  $account_list = $acc->get_list('accounts');
  while (list($key,$entry) = each($account_list))
  {
    $_accounts[$entry['account_id']] = $entry['account_lid'];
  }
  create_select_box('Default assign to','assigntodefault',$_accounts);

  // Choose the correct priority to display
  $priority_comment[1]  = ' - ' . lang('Lowest'); 
  $priority_comment[5]  = ' - ' . lang('Medium'); 
  $priority_comment[10] = ' - ' . lang('Highest'); 
  for ($i=1; $i<=10; $i++)
  {
    $priority[$i] = $i . $priority_comment[$i];
  }
  create_select_box('Default Priority','prioritydefault',$priority);

  create_input_box('Refresh every (seconds)','refreshinterval');

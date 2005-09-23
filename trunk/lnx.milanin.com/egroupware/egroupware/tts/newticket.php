<?php
  /**************************************************************************\
  * eGroupWare - Trouble Ticket System                                       *
  * http://www.egroupware.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  // $Id: newticket.php,v 1.40.2.1 2004/07/22 20:12:11 shrykedude Exp $
  // $Source: /cvsroot/egroupware/tts/newticket.php,v $

  $GLOBALS['phpgw_info']['flags'] = array(
    'noheader'             => True,
    'nonavbar'             => True,
    'currentapp'           => 'tts',
    'enable_send_class'    => True,
    'enable_config_class'  => True,
    'enable_categories_class' => True
  );

  include('../header.inc.php');

  $GLOBALS['phpgw']->config->read_repository();

  if($_POST['cancel'])
  {
    $GLOBALS['phpgw']->redirect_link('/tts/index.php');
  }

  if($_POST['submit'] && !empty($_POST['ticket_subject']))
  {
  
    if (get_magic_quotes_gpc())
    {
      foreach(array('subject','details') as $name)
      {
        $_POST['ticket_'.$name] = stripslashes($_POST['ticket_'.$name]);
      }
    }
    $_POST['ticket_details'] = html_activate_urls($_POST['ticket_details']);

    $ticket_billable_hours = str_replace(',','.',$_POST['ticket_billable_hours']);
    $ticket_billable_rate = str_replace(',','.',$_POST['ticket_billable_rate']);

    $GLOBALS['phpgw']->db->query("insert into phpgw_tts_tickets (ticket_state,ticket_group,ticket_priority,ticket_owner,"
      . "ticket_assignedto,ticket_subject,ticket_category,ticket_billable_hours,"
      . "ticket_billable_rate,ticket_status,ticket_details) values ('"
      . intval($_POST['ticket_state']) . "','"
      . intval($_POST['ticket_group']) . "','"
      . intval($_POST['ticket_priority']) . "','"
      . $GLOBALS['phpgw_info']['user']['account_id'] . "','"
      . intval($_POST['ticket_assignedto']) . "','"
      . $GLOBALS['phpgw']->db->db_addslashes($_POST['ticket_subject']) . "','"
      . intval($_POST['ticket_category']) . "','"
      . $GLOBALS['phpgw']->db->db_addslashes($ticket_billable_hours) . "','"
      . $GLOBALS['phpgw']->db->db_addslashes($ticket_billable_rate) . "','O','"
      . $GLOBALS['phpgw']->db->db_addslashes($_POST['ticket_details']) . "')",__LINE__,__FILE__);

    $ticket_id = $GLOBALS['phpgw']->db->get_last_insert_id('phpgw_tts_tickets','ticket_id');

    $historylog = createobject('phpgwapi.historylog','tts');
    $historylog->add('O',$ticket_id,' ','');

    if($GLOBALS['phpgw']->config->config_data['mailnotification'])
    {
      mail_ticket($ticket_id);
    }

    $GLOBALS['phpgw']->redirect_link('/tts/viewticket_details.php','&ticket_id=' . $ticket_id);
  }
  else if ($_POST['submit']) {
    //there is an error:
    $GLOBALS['phpgw']->template->set_var('messages',lang('ERROR: The subject of the ticket is not specified.'));
  }
  else { //the form was not yet submitted, grab the defaults
    $GLOBALS['phpgw']->preferences->read_repository();
    if($GLOBALS['phpgw_info']['user']['preferences']['tts']['groupdefault']) 
      $_POST['ticket_group']=$GLOBALS['phpgw_info']['user']['preferences']['tts']['groupdefault'];
    if($GLOBALS['phpgw_info']['user']['preferences']['tts']['assigntodefault'])
      $_POST['ticket_assignedto']=$GLOBALS['phpgw_info']['user']['preferences']['tts']['assigntodefault'];
    if($GLOBALS['phpgw_info']['user']['preferences']['tts']['prioritydefault'])
      $_POST['ticket_priority']=$GLOBALS['phpgw_info']['user']['preferences']['tts']['prioritydefault'];
  }

  $GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['tts']['title'] . ' - ' . lang('Create new ticket');
  $GLOBALS['phpgw']->common->phpgw_header();
  echo parse_navbar();

  $GLOBALS['phpgw']->template->set_file(array(
    'newticket'   => 'newticket.tpl'
  ));
  $GLOBALS['phpgw']->template->set_block('newticket','options_select');
  $GLOBALS['phpgw']->template->set_block('newticket','form');

  $GLOBALS['phpgw']->template->set_var('form_action', $GLOBALS['phpgw']->link('/tts/newticket.php'));

  $GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
  $GLOBALS['phpgw']->template->set_var('lang_group', lang('Group'));
  $GLOBALS['phpgw']->template->set_var('lang_subject', lang('Subject') );
  $GLOBALS['phpgw']->template->set_var('lang_nosubject', lang('No subject'));
  $GLOBALS['phpgw']->template->set_var('lang_details', lang('Details'));
  $GLOBALS['phpgw']->template->set_var('lang_priority', lang('Priority'));
  $GLOBALS['phpgw']->template->set_var('lang_lowest', lang('Lowest'));
  $GLOBALS['phpgw']->template->set_var('lang_medium', lang('Medium'));
  $GLOBALS['phpgw']->template->set_var('lang_highest', lang('Highest'));
  $GLOBALS['phpgw']->template->set_var('lang_addticket', lang('Add Ticket'));
  $GLOBALS['phpgw']->template->set_var('lang_clearform', lang('Clear Form'));
  $GLOBALS['phpgw']->template->set_var('lang_initialstate', lang('Initial State'));
  $GLOBALS['phpgw']->template->set_var('lang_assignedto',lang('Assign to'));
  $GLOBALS['phpgw']->template->set_var('lang_submit',lang('Save'));
  $GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));
  $GLOBALS['phpgw']->template->set_var('lang_no_subject',lang('Please enter the subject of the ticket, otherwise the ticket cannot be stored.'));

  $GLOBALS['phpgw']->template->set_var('lang_billable_hours',lang('Billable hours'));
  $GLOBALS['phpgw']->template->set_var('lang_billable_hours_rate',lang('Billable hours rate'));

  $GLOBALS['phpgw']->template->set_var('row_off', $GLOBALS['phpgw_info']['theme']['row_off']);
  $GLOBALS['phpgw']->template->set_var('row_on', $GLOBALS['phpgw_info']['theme']['row_on']);
  $GLOBALS['phpgw']->template->set_var('th_bg', $GLOBALS['phpgw_info']['theme']['th_bg']);

  $GLOBALS['phpgw']->template->set_var('value_details',$_POST['ticket_details']);
  $GLOBALS['phpgw']->template->set_var('value_subject',$_POST['ticket_subject']);
  $GLOBALS['phpgw']->template->set_var('value_billable_hours',($_POST['ticket_billable_hours']?$_POST['ticket_billable_hours']:'0.00'));
  $GLOBALS['phpgw']->template->set_var('value_billable_hours_rate',($_POST['ticket_billable_rate']?$_POST['ticket_billable_rate']:'0.00'));

  
  //produce the list of groups
  unset($s);
  $groups = CreateObject('phpgwapi.accounts');
  $group_list = array();
  $group_list = $GLOBALS['phpgw']->accounts->membership($GLOBALS['phpgw_info']['user']['account_id']);

  while(list($key,$entry) = each($group_list))
  {
    $GLOBALS['phpgw']->template->set_var('optionname', $entry['account_name']);
    $GLOBALS['phpgw']->template->set_var('optionvalue', $entry['account_id']);
    $GLOBALS['phpgw']->template->set_var('optionselected', $entry['account_id']==$_POST['ticket_group']?' SELECTED ':'');
    $GLOBALS['phpgw']->template->parse('options_group','options_select',true);
  }


  //produce the list of categories
  $s = '<select name="ticket_category">' . $GLOBALS['phpgw']->categories->formated_list('select','all',$_POST['ticket_category'],True) . '</select>';
  $GLOBALS['phpgw']->template->set_var('value_category',$s);

  
  
  //produce the list of accounts for assigned to
  $s = '<option value="0">' . lang('None') . '</option>';
  $accounts = $groups;
  $accounts->account_id = $group_id;
  $account_list = $accounts->get_list('accounts');
  while(list($key,$entry) = each($account_list))
  {
    $s .= '<option value="' . $entry['account_id'] . '" ' 
      . ($entry['account_id']==$_POST['ticket_assignedto']?' SELECTED ':'')
      . '>' . $entry['account_lid'] . '</option>';
  }
  $GLOBALS['phpgw']->template->set_var('value_assignedto','<select name="ticket_assignedto">' . $s . '</select>');

  // Choose the correct priority to display
  $prority_selected[$ticket_priority] = ' selected';
  $priority_comment[1]  = ' - '.lang('Lowest');
  $priority_comment[5]  = ' - '.lang('Medium');
  $priority_comment[10] = ' - '.lang('Highest');
  for($i=1; $i<=10; $i++)
  {
    $priority_select .= '<option value="' . $i . '"' 
      . ($i==$_POST['ticket_priority']?' SELECTED ':'') 
      . '>' . $i . $priority_comment[$i] . '</option>';
  }
  $GLOBALS['phpgw']->template->set_var('value_priority','<select name="ticket_priority">' . $priority_select . '</select>');

  // Choose the initial state to display
  $GLOBALS['phpgw']->template->set_var('options_state',
    listid_field('phpgw_tts_states','state_name','state_id',$_POST['ticket_state'], "state_initial=1"));

  $GLOBALS['phpgw']->template->set_var('tts_select_options','');
  $GLOBALS['phpgw']->template->set_var('tts_new_lstcategory','');
  $GLOBALS['phpgw']->template->set_var('tts_new_lstassignto','');

  $GLOBALS['phpgw']->template->pfp('out','form');
  $GLOBALS['phpgw']->common->phpgw_footer();
?>

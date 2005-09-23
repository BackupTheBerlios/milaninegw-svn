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

  // $Id: viewticket_details.php,v 1.61.2.3 2005/02/12 16:04:21 ralfbecker Exp $
  // $Source: /cvsroot/egroupware/tts/viewticket_details.php,v $


  $GLOBALS['phpgw_info']['flags'] = array(
    'enable_nextmatchs_class' => True,
    'enable_categories_class' => True,
    'enable_config_class'     => True,
    'currentapp'              => 'tts',
    'noheader'                => True,
    'nonavbar'                => True,
    'enable_config_class'     => !@$_POST['submit'] && !@$_POST['cancel']
  );

  include('../header.inc.php');
  
  $filter = reg_var('filter','GET');
  $start  = reg_var('start','GET','numeric',0);
  $sort   = reg_var('sort','GET');
  $order  = reg_var('order','GET');
  $searchfilter = reg_var('searchfilter','POST');

  if($_POST['cancel'])
  {
    $GLOBALS['phpgw']->redirect_link('/tts/index.php',array('filter'=>$filter,'order'=>$order,'sort'=>$sort));
  }
  $ticket_id = intval(get_var('ticket_id',array('POST','GET')));

  $GLOBALS['phpgw']->config->read_repository();

  $GLOBALS['phpgw']->historylog = createobject('phpgwapi.historylog','tts');

  $GLOBALS['phpgw']->historylog->types = array(
    'R' => 'Re-opened',
    'X' => 'Closed',
    'O' => 'Opened',
    'A' => 'Re-assigned',
    'P' => 'Priority changed',
    'T' => 'Category changed',
    'S' => 'Subject changed',
    'B' => 'Billing rate',
    'H' => 'Billing hours',
    'G' => 'Group ownership changed',
    'N' => 'State changed'
  );

  if(!$_POST['save'] && !$_POST['apply'])
  {
    // load the necessary css for the tabs
    function css()
    {
      $appCSS =
      'th.activetab
      {
        color:#000000;
        background-color:#D3DCE3;
        border-top-width : 2px;
        border-top-style : solid;
        border-top-color : Black;
        border-left-width : 2px;
        border-left-style : solid;
        border-left-color : Black;
        border-right-width : 2px;
        border-right-style : solid;
        border-right-color : Black;
      }

      th.inactivetab
      {
        color:#000000;
        background-color:#E8F0F0;
        border-width : 1px;
        border-style : solid;
        border-color : Black;
        border-bottom-width : 2px;
        border-bottom-style : solid;
        border-bottom-color : Black;
      }

      table.tabcontent
      {
        border-bottom-width : 2px;
        border-bottom-style : solid;
        border-bottom-color : Black;
        border-left-width : 2px;
        border-left-style : solid;
        border-left-color : Black;
        border-right-width : 2px;
        border-right-style : solid;
        border-right-color : Black;
      }

      .td_left { border-left : 1px solid Gray; border-top : 1px solid Gray; }
      .td_right { border-right : 1px solid Gray; border-top : 1px solid Gray; }

      div.activetab{ display:inline; }
      div.inactivetab{ display:none; }';

      return $appCSS;
    }
    $GLOBALS['phpgw_info']['flags']['css'] = css();

    // load the necessary javascript for the tabs
    if(!@is_object($GLOBALS['phpgw']->js))
    {
      $GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
    }
    $GLOBALS['phpgw']->js->validate_file('tabs','tabs');
    $GLOBALS['phpgw']->js->set_onload('tab.init();');

    $GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['tts']['title'] . ' - ' . lang('View Job Detail');
    $GLOBALS['phpgw']->common->phpgw_header();
    echo parse_navbar();

    // Have they viewed this ticket before ?
    $GLOBALS['phpgw']->db->query("select count(*) from phpgw_tts_views where view_id='$ticket_id' "
      . "and view_account_id='" . $GLOBALS['phpgw_info']['user']['account_id'] . "'",__LINE__,__FILE__);
    $GLOBALS['phpgw']->db->next_record();

    if(!$GLOBALS['phpgw']->db->f(0))
    {
      $GLOBALS['phpgw']->db->query("insert into phpgw_tts_views values ('$ticket_id','"
        . $GLOBALS['phpgw_info']['user']['account_id'] . "','" . time() . "')",__LINE__,__FILE__);
    }

    // select the ticket that you selected
    $GLOBALS['phpgw']->db->query("select * from phpgw_tts_tickets where ticket_id='$ticket_id'",__LINE__,__FILE__);
    $GLOBALS['phpgw']->db->next_record();

    $ticket['billable_hours'] = $GLOBALS['phpgw']->db->f('ticket_billable_hours');
    $ticket['billable_rate']  = $GLOBALS['phpgw']->db->f('ticket_billable_rate');
    $ticket['assignedto']     = $GLOBALS['phpgw']->db->f('ticket_assignedto');
    $ticket['category']       = $GLOBALS['phpgw']->db->f('ticket_category');
    $ticket['details']        = $GLOBALS['phpgw']->db->f('ticket_details');
    $ticket['subject']        = $GLOBALS['phpgw']->db->f('ticket_subject');
    $ticket['priority']       = $GLOBALS['phpgw']->db->f('ticket_priority');
    $ticket['owner']          = $GLOBALS['phpgw']->db->f('ticket_owner');
    $ticket['group']          = $GLOBALS['phpgw']->db->f('ticket_group');
    $ticket['groupnotification']          = $GLOBALS['phpgw']->db->f('ticket_groupnotification');
    $ticket['status']         = $GLOBALS['phpgw']->db->f('ticket_status');
    $ticket['state']          = $GLOBALS['phpgw']->db->f('ticket_state');

    $GLOBALS['phpgw']->template->set_file('viewticket','viewticket_details.tpl');
    $GLOBALS['phpgw']->template->set_block('viewticket','options_select');
    $GLOBALS['phpgw']->template->set_block('viewticket','additional_notes_row');
    $GLOBALS['phpgw']->template->set_block('viewticket','additional_notes_row_empty');
    $GLOBALS['phpgw']->template->set_block('viewticket','row_history');
    $GLOBALS['phpgw']->template->set_block('viewticket','row_history_empty');
    $GLOBALS['phpgw']->template->set_block('viewticket','form');
    $GLOBALS['phpgw']->template->set_block('form','update_state_items','update_state_group');

    $messages .= rtrim($GLOBALS['phpgw']->session->appsession('messages','tts'),"\0");
    if($messages)
    {
      $GLOBALS['phpgw']->template->set_var('messages',$messages);
      $GLOBALS['phpgw']->session->appsession('messages','tts','');
    }

    if($GLOBALS['phpgw']->db->f('ticket_status') == 'C')
    {
      $GLOBALS['phpgw']->template->set_var('t_status','FIX ME! time closed ' . __LINE__); // $GLOBALS['phpgw']->common->show_date($GLOBALS['phpgw']->db->f('t_timestamp_closed')));
    }
    else
    {
      $GLOBALS['phpgw']->template->set_var('t_status', lang('In progress'));
    }

    // Choose the correct priority to display
    $priority_selected[$ticket['priority']] = ' selected';
    $priority_comment[1]  = ' - '.lang('Lowest');
    $priority_comment[5]  = ' - '.lang('Medium');
    $priority_comment[10] = ' - '.lang('Highest');

    for($i=1; $i<=10; $i++)
    {
      $GLOBALS['phpgw']->template->set_var('optionname', $i.$priority_comment[$i]);
      $GLOBALS['phpgw']->template->set_var('optionvalue', $i);
      $GLOBALS['phpgw']->template->set_var('optionselected', $priority_selected[$i]);
      $GLOBALS['phpgw']->template->parse('options_priority','options_select',true);
    }

    // assigned to
    $accounts = CreateObject('phpgwapi.accounts');
    $account_list = $accounts->get_list('accounts');
    $GLOBALS['phpgw']->template->set_var('optionname',lang('None'));
    $GLOBALS['phpgw']->template->set_var('optionvalue','0');
    $GLOBALS['phpgw']->template->set_var('optionselected','');
    $GLOBALS['phpgw']->template->parse('options_assignedto','options_select',true);
    while(list($key,$entry) = each($account_list))
    {
      $tag = '';
      if($entry['account_id'] == $ticket['assignedto'])
      {
        $tag = 'selected';
      }
      $GLOBALS['phpgw']->template->set_var('optionname', $entry['account_lid']);
      $GLOBALS['phpgw']->template->set_var('optionvalue', $entry['account_id']);
      $GLOBALS['phpgw']->template->set_var('optionselected', $tag);
      $GLOBALS['phpgw']->template->parse('options_assignedto','options_select',True);
    }

    // Figure out when it was opened and last closed
    $history_array = $GLOBALS['phpgw']->historylog->return_array(array(),array('X','O'),'','',$ticket_id);

    while(is_array($history_array) && list(,$value) = each($history_array))
    {
      if($value['status'] == 'O')
      {
        $ticket['opened'] = $GLOBALS['phpgw']->common->show_date($value['datetime'] - ((60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset']));
      }

      if($value['status'] == 'X')
      {
        $ticket['closed'] = $GLOBALS['phpgw']->common->show_date($value['datetime'] - ((60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset']));
      }
    }

    // group

    $group_list = array();
    $group_list = $GLOBALS['phpgw']->accounts->membership($GLOBALS['phpgw_info']['user']['account_id']);

    while(list($key,$entry) = each($group_list))
    {
      $tag = '';
      if($entry['account_id'] == $ticket['group'])
      {
        $tag = 'selected';
      }
      $GLOBALS['phpgw']->template->set_var('optionname', $entry['account_name']);
      $GLOBALS['phpgw']->template->set_var('optionvalue', $entry['account_id']);
      $GLOBALS['phpgw']->template->set_var('optionselected', $tag);
      $GLOBALS['phpgw']->template->parse('options_group','options_select',true);
    }
    
    // groupnotification
    if ($ticket['groupnotification']){
    
      $GLOBALS['phpgw']->template->set_var('options_groupnotification',"CHECKED");
    }else{
      $GLOBALS['phpgw']->template->set_var('options_groupnotification',"");
    }
    $GLOBALS['phpgw']->template->set_var('lang_groupnotification',lang('notify changes to ticket group by e-mail'));
    $GLOBALS['phpgw']->template->set_var('options_category',$GLOBALS['phpgw']->categories->formated_list('select','all',$ticket['category'],$ticket['category'],True));

    $ticket_status[$ticket['status']] = ' selected';

    $s = '<option value="O"' . $ticket_status['O'] . '>' . lang('Open') . '</option>';
    $s .= '<option value="X"' . $ticket_status['X'] . '>' . lang('Closed') . '</option>';

    $GLOBALS['phpgw']->template->set_var('options_status',$s);
    $GLOBALS['phpgw']->template->set_var('lang_status',lang('Open / Closed'));

    /**************************************************************\
    * Display additional notes                                     *
    \**************************************************************/
    $history_array = $GLOBALS['phpgw']->historylog->return_array(array(),array('C'),'','',$ticket_id);
    $i = 0;
    while(is_array($history_array) && list(,$value) = each($history_array))
    {
      $GLOBALS['phpgw']->template->set_var('row_class',++$i & 1 ? 'row_off' : 'row_on');

      $GLOBALS['phpgw']->template->set_var('lang_date',lang('Date'));
      $GLOBALS['phpgw']->template->set_var('lang_user',lang('User'));

      $GLOBALS['phpgw']->template->set_var('value_date',$GLOBALS['phpgw']->common->show_date($value['datetime'] - ((60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'])));
      $GLOBALS['phpgw']->template->set_var('value_user',$value['owner']);

      $GLOBALS['phpgw']->template->set_var('value_note',nl2br(stripslashes($value['new_value'])));
      $GLOBALS['phpgw']->template->fp('rows_notes','additional_notes_row',True);
    }

    if(!count($history_array))
    {
      $GLOBALS['phpgw']->template->set_var('lang_no_additional_notes',lang('No additional notes'));
      $GLOBALS['phpgw']->template->fp('rows_notes','additional_notes_row_empty',True);
    }

    /**************************************************************\
    * Display record history                                       *
    \**************************************************************/
    $GLOBALS['phpgw']->template->set_var('lang_history',lang('History'));
    $GLOBALS['phpgw']->template->set_var('lang_user',lang('User'));
    $GLOBALS['phpgw']->template->set_var('lang_date',lang('Date'));
    $GLOBALS['phpgw']->template->set_var('lang_action',lang('Action'));
    $GLOBALS['phpgw']->template->set_var('lang_new_value',lang('New Value'));
    $GLOBALS['phpgw']->template->set_var('lang_old_value',lang('Old Value'));

    $i=0;
    $history_array = $GLOBALS['phpgw']->historylog->return_array(array('C'),array(),'','',$ticket_id);
    while(is_array($history_array) && list(,$value) = each($history_array))
    {
      $GLOBALS['phpgw']->template->set_var('row_class',++$i & 1 ? 'row_off' : 'row_on');

      $GLOBALS['phpgw']->template->set_var('value_date',$GLOBALS['phpgw']->common->show_date($value['datetime'] - ((60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'])));
      $GLOBALS['phpgw']->template->set_var('value_user',$value['owner']);

      switch($value['status'])
      {
        case 'R': $type = lang('Re-opened'); break;
        case 'X': $type = lang('Closed');    break;
        case 'O': $type = lang('Opened');    break;
        case 'A': $type = lang('Re-assigned'); break;
        case 'P': $type = lang('Priority changed'); break;
        case 'T': $type = lang('Category changed'); break;
        case 'S': $type = lang('Subject changed'); break;
        case 'H': $type = lang('Billable hours changed'); break;
        case 'B': $type = lang('Billable rate changed'); break;
        case 'G': $type = lang('Group ownership changed'); break;
        case 'F': $type = lang('notify changes to ticket group by e-mail'); break;
        case 'N': $type = lang('State changed'); break;
        default: break;
      }

      $GLOBALS['phpgw']->template->set_var('value_action',($type?$type:'&nbsp;'));
      unset($type);

      if($value['status'] == 'A')
      {
        if(!$value['new_value'])
        {
          $GLOBALS['phpgw']->template->set_var('value_new_value',lang('None'));
        }
        else
        {
          $GLOBALS['phpgw']->template->set_var('value_new_value',$GLOBALS['phpgw']->accounts->id2name($value['new_value']));
        }

        if(!$value['old_value'])
        {
          $GLOBALS['phpgw']->template->set_var('value_old_value',lang('None'));
        }
        else
        {
          $GLOBALS['phpgw']->template->set_var('value_old_value',$GLOBALS['phpgw']->accounts->id2name($value['old_value']));
        }
      }
      elseif($value['status'] == 'T')
      {
        $GLOBALS['phpgw']->template->set_var('value_new_value',$GLOBALS['phpgw']->categories->id2name($value['new_value']));
        $GLOBALS['phpgw']->template->set_var('value_old_value',$GLOBALS['phpgw']->categories->id2name($value['old_value']));
      }
      elseif($value['status'] == 'G')
      {
        $s = $GLOBALS['phpgw']->accounts->id2name($value['new_value']);
        $s = ($s ? $s : '--');
        $GLOBALS['phpgw']->template->set_var('value_new_value',$s);

        $s = $GLOBALS['phpgw']->accounts->id2name($value['old_value']);
        $s = ($s ? $s : '--');
        $GLOBALS['phpgw']->template->set_var('value_old_value',$s);
      }
      elseif($value['status'] == 'N')
      {
        $s = id2field('phpgw_tts_states','state_name','state_id',$value['new_value']);
        $s = ($s ? $s : '--');
        $GLOBALS['phpgw']->template->set_var('value_new_value',$s);

        $s = id2field('phpgw_tts_states','state_name','state_id',$value['old_value']);
        $s = ($s ? $s : '--');
        $GLOBALS['phpgw']->template->set_var('value_old_value',$s);
      }
      elseif($value['status'] != 'O' && $value['new_value'])
      {
        $GLOBALS['phpgw']->template->set_var('value_new_value',$value['new_value']);
        $GLOBALS['phpgw']->template->set_var('value_old_value',$value['old_value']);
      }
      else
      {
        $GLOBALS['phpgw']->template->set_var('value_new_value','&nbsp;');
        $GLOBALS['phpgw']->template->set_var('value_old_value','&nbsp;');
      }

      $GLOBALS['phpgw']->template->fp('rows_history','row_history',True);
    }

    if(!count($history_array))
    {
      $GLOBALS['phpgw']->template->set_var('lang_no_history',lang('No history for this record'));
      $GLOBALS['phpgw']->template->fp('rows_history','row_history_empty',True);
    }

    $GLOBALS['phpgw']->template->set_var('lang_update',lang('Update'));

//    $phpgw->template->set_var('additonal_details_rows',$s);

    $GLOBALS['phpgw']->template->set_var('viewticketdetails_link', $GLOBALS['phpgw']->link('/tts/viewticket_details.php',array('filter'=>$filter,'order'=>$order,'sort'=>$sort)));
    $GLOBALS['phpgw']->template->set_var('ticket_id', $ticket_id);

    $GLOBALS['phpgw']->template->set_var('lang_assignedfrom', lang('Assigned from'));
    $GLOBALS['phpgw']->template->set_var('value_owner',$GLOBALS['phpgw']->accounts->id2name($ticket['owner']));

    $GLOBALS['phpgw']->template->set_var('lang_opendate', lang('Open Date'));
    $GLOBALS['phpgw']->template->set_var('value_opendate',$ticket['opened']);

    $GLOBALS['phpgw']->template->set_var('lang_priority', lang('Priority'));
    $GLOBALS['phpgw']->template->set_var('value_priority',$ticket['priority']);

    $GLOBALS['phpgw']->template->set_var('lang_group', lang('Group'));
    $s = $GLOBALS['phpgw']->accounts->id2name($ticket['group']);
    $s = ($s ? $s : '--');
    $GLOBALS['phpgw']->template->set_var('value_group',$s);

    $GLOBALS['phpgw']->template->set_var('lang_state', lang('State'));
    $s = id2field('phpgw_tts_states','state_name','state_id',$ticket['state']);
    $GLOBALS['phpgw']->template->set_var('value_state',$s ? $s : '--');
    $t = id2field('phpgw_tts_states','state_description','state_id',$ticket['state'],False);
    $GLOBALS['phpgw']->template->set_var('value_state_description',
      $t ? $t : '-- '.lang('Missing description').' --');

    $GLOBALS['phpgw']->template->set_var('lang_billable_hours',lang('Billable hours'));
    $GLOBALS['phpgw']->template->set_var('value_billable_hours',$ticket['billable_hours']);

    $GLOBALS['phpgw']->template->set_var('lang_billable_hours_rate',lang('Billable rate'));
    $GLOBALS['phpgw']->template->set_var('value_billable_hours_rate',$ticket['billable_rate']);

    $GLOBALS['phpgw']->template->set_var('lang_billable_hours_total',lang('Total billable'));
    $GLOBALS['phpgw']->template->set_var('value_billable_hours_total',sprintf('%01.2f',($ticket['billable_hours'] * $ticket['billable_rate'])));

    $GLOBALS['phpgw']->template->set_var('lang_assignedto',lang('Assigned to'));
    if($ticket['assignedto'])
    {
      $assignedto = $GLOBALS['phpgw']->accounts->id2name($ticket['assignedto']);
    }
    else
    {
      $assignedto = lang('None');
    }
    $GLOBALS['phpgw']->template->set_var('value_assignedto',$assignedto);

    $GLOBALS['phpgw']->template->set_var('lang_subject', lang('Subject'));

    $GLOBALS['phpgw']->template->set_var('lang_details', lang('Details'));

    // cope with old, wrongly saved entries, stripslashes would remove single backslashes too
    foreach(array('subject','details') as $name)
    {
      $ticket[$name] = str_replace(array('\\\'','\\"','\\\\'),array("'",'"','\\'),$ticket[$name]);
    }
    $GLOBALS['phpgw']->template->set_var('value_details', nl2br($ticket['details']));

    $GLOBALS['phpgw']->template->set_var('value_subject', $ticket['subject']);

    $GLOBALS['phpgw']->template->set_var('lang_additional_notes',lang('Additional notes'));
    $GLOBALS['phpgw']->template->set_var('lang_save', lang('Save'));
    $GLOBALS['phpgw']->template->set_var('lang_apply', lang('Apply'));
    $GLOBALS['phpgw']->template->set_var('lang_cancel', lang('Cancel'));

    $GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
    $GLOBALS['phpgw']->template->set_var('value_category',$GLOBALS['phpgw']->categories->id2name($ticket['category']));

    $GLOBALS['phpgw']->template->set_var('options_select','');

    $GLOBALS['phpgw']->template->set_var('lang_update_state',lang('Update ticket state'));
    $GLOBALS['phpgw']->template->set_var('lang_keep_present_state',
      lang('Keep the present state [%1].',id2field('phpgw_tts_states','state_name','state_id',$ticket['state'])));

    $db = clone($GLOBALS['phpgw']->db);
    $db->query("select * from phpgw_tts_transitions where transition_source_state=".$ticket['state'],__LINE__,__FILE__);

    while($db->next_record())
    {
      $GLOBALS['phpgw']->template->set_var('update_state_value',$db->f('transition_target_state'));
      $GLOBALS['phpgw']->template->set_var('update_state_text',
        try_lang($db->f('transition_description'),
        id2field('phpgw_tts_states','state_name','state_id',$db->f('transition_target_state'))));
      $GLOBALS['phpgw']->template->parse('update_state_group', 'update_state_items', True);
    }


    $GLOBALS['phpgw']->template->pfp('out','form');
    $GLOBALS['phpgw']->common->phpgw_footer();

  }
  else // save or apply
  {
    $ticket = $_POST['ticket'];

    // DB Content is fresher than http posted value.
    $GLOBALS['phpgw']->db->query("select * from phpgw_tts_tickets where ticket_id='$ticket_id'",__LINE__,__FILE__);
    $GLOBALS['phpgw']->db->next_record();

    $oldassigned = $GLOBALS['phpgw']->db->f('ticket_assignedto');
    $oldpriority = $GLOBALS['phpgw']->db->f('ticket_priority');
    $oldcategory = $GLOBALS['phpgw']->db->f('ticket_category');
    $old_status  = $GLOBALS['phpgw']->db->f('ticket_status');
    $old_billable_hours = $GLOBALS['phpgw']->db->f('ticket_billable_hours');
    $old_billable_rate = $GLOBALS['phpgw']->db->f('ticket_billable_rate');
    $old_group   = $GLOBALS['phpgw']->db->f('ticket_group');
    $oldgroupnotification = $GLOBALS['phpgw']->db->f('ticket_groupnotification');
    $old_state   = $GLOBALS['phpgw']->db->f('ticket_state');

    $GLOBALS['phpgw']->db->transaction_begin();

    /*
    **  phpgw_tts_append.append_type - Defs
    **  R - Reopen ticket
    ** X - Ticket closed
    ** O - Ticket opened
    ** C - Comment appended
    ** A - Ticket assignment
    ** P - Priority change
    ** T - Category change
    ** S - Subject change
    ** B - Billing rate
    ** H - Billing hours
    ** G - Group
    ** N - Petri Net State change
    ** F - Group notification
    */
  
    $no_error=True;
    if($old_status != $ticket['status'])
    {
      //only allow assigned-to or admin members to close tickets
      if(($GLOBALS['phpgw_info']['user']['account_id'] == $oldassigned) ||
        ($GLOBALS['phpgw']->acl->get_specific_rights('Admins','phpgw_group')))
      {
        $fields_updated = True;
        $GLOBALS['phpgw']->historylog->add($ticket['status'],$ticket_id,$ticket['status'],$old_status);

        $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_status='"
          . $ticket['status'] . "' where ticket_id='$ticket_id'",__LINE__,__FILE__);
      }
      else
      {
        $messages .= '<br>'.lang('You can only close a ticket if it is assigned to you.');
        $GLOBALS['phpgw']->session->appsession('messages','tts',$messages);
        $no_error=False;
      }
    }

    if($old_group != $ticket['group'])
    {
      $fields_updated = True;
      $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_group='" . $ticket['group']
        . "' where ticket_id='$ticket_id'",__LINE__,__FILE__);
      $GLOBALS['phpgw']->historylog->add('G',$ticket_id,$ticket['group'],$old_group);
    }

    if($oldassigned != $ticket['assignedto'])
    {
      $fields_updated = True;
      $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_assignedto='" . $ticket['assignedto']
        . "' where ticket_id='$ticket_id'",__LINE__,__FILE__);
      $GLOBALS['phpgw']->historylog->add('A',$ticket_id,$ticket['assignedto'],$oldassigned);
    }

    if($oldpriority != $ticket['priority'])
    {
      $fields_updated = True;
      $ticket['priority']=intval($ticket['priority']);
      $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_priority='" . $ticket['priority']
        . "' where ticket_id='$ticket_id'",__LINE__,__FILE__);
      $GLOBALS['phpgw']->historylog->add('P',$ticket_id,$ticket['priority'],$oldpriority);
    }
    if ($oldgroupnotification != $ticket['groupnotification'])
    {
      $fields_updated = True;
      $GLOBALS['phpgw']->historylog->add('F',$ticket_id,$ticket['groupnotification']);
      $ticket['groupnotification'] = ($ticket['groupnotification'] == "on") ? 1 : 0;
      $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_groupnotification=" . $ticket['groupnotification']
      ." where ticket_id='$ticket_id'",__LINE__,__FILE__);
      
    }
    if($oldcategory != $ticket['category'])
    {
      $fields_updated = True;
      $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_category='" . $ticket['category']
        . "' where ticket_id='$ticket_id'",__LINE__,__FILE__);
      $GLOBALS['phpgw']->historylog->add('T',$ticket_id,$ticket['category'],$oldcategory);
    }

    if($old_billable_hours != $ticket['billable_hours'])
    {
      $fields_updated = True;
      $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_billable_hours='" . $ticket['billable_hours']
        . "' where ticket_id='$ticket_id'",__LINE__,__FILE__);
      $GLOBALS['phpgw']->historylog->add('H',$ticket_id,$ticket['billable_hours'],$old_billable_hours);
    }

    if($old_billable_rate != $ticket['billable_rate'])
    {
      $fields_updated = True;
      $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_billable_rate='" . $ticket['billable_rate']
        . "' where ticket_id='$ticket_id'",__LINE__,__FILE__);
      $GLOBALS['phpgw']->historylog->add('B',$ticket_id,$ticket['billable_rate'],$old_billable_rate);
    }

    if($ticket['state'] && $old_state != $ticket['state'])
    {
      $fields_updated = True;
      $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_state='" . $ticket['state']
        . "' where ticket_id='$ticket_id'",__LINE__,__FILE__);
      $GLOBALS['phpgw']->historylog->add('N',$ticket_id,$ticket['state'],$old_state);
    }


    if($ticket['note'])
    {
      $fields_updated = True;
      $ticket['note'] = html_activate_urls($ticket['note']);

      $GLOBALS['phpgw']->historylog->add('C',$ticket_id,$ticket['note'],'');

      // Do this before we go into mail_ticket()
      $GLOBALS['phpgw']->db->transaction_commit();
    }
    else
    {
      // Only do our commit once
      $GLOBALS['phpgw']->db->transaction_commit();
    }

    if($fields_updated)
    {
      $GLOBALS['phpgw']->session->appsession('messages','tts',lang('Ticket has been updated').'<br/>'.$messages);

      if($GLOBALS['phpgw']->config->config_data['mailnotification'])
      {
        mail_ticket($ticket_id);
      }
    }

    if ($_POST['save'] && $no_error)
    {
      $GLOBALS['phpgw']->redirect_link('/tts/index.php',array('filter'=>$filter,'order'=>$order,'sort'=>$sort));
    }
    else  // apply
    {
      $GLOBALS['phpgw']->redirect_link('/tts/viewticket_details.php',array('ticket_id'=>$ticket_id,'filter'=>$filter,'order'=>$order,'sort'=>$sort));
    }
  }
?>

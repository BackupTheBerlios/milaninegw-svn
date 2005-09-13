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

  /* $Id: delete_state.php,v 1.4.2.1 2005/02/12 16:04:21 ralfbecker Exp $ */

  /* Note to self:
  ** Self ... heres the query to use when limiting access to entrys within a group
  ** The acl class *might* handle this instead .... not sure
  ** select distinct group_ticket_id, phpgw_tts_groups.group_ticket_id, phpgw_tts_tickets.*
  ** from phpgw_tts_tickets, phpgw_tts_groups where ticket_id = group_ticket_id and group_id in (14,15);
  */

  /* ACL levels
  ** 1 - Read ticket within your group only
  ** 2 - Close ticket
  ** 4 - Allow to make changes to priority, billing hours, billing rate, category, and assigned to
  */

  $GLOBALS['phpgw_info']['flags']['currentapp'] = 'tts';
  $GLOBALS['phpgw_info']['flags']['enable_contacts_class'] = True;
  $GLOBALS['phpgw_info']['flags']['enable_categories_class'] = True;
  $GLOBALS['phpgw_info']['flags']['enable_nextmatchs_class'] = True;
  $GLOBALS['phpgw_info']['flags']['noheader'] = True;
  include('../header.inc.php');

  // select what tickets to view
  $filter = reg_var('filter','GET');
  $start  = reg_var('start','GET','numeric',0);
  $sort   = reg_var('sort','GET');
  $order  = reg_var('order','GET');

  $state_id  = intval(get_var('state_id',array('POST','GET')));

  $ticket['state']    = intval(get_var('ticket_state',array('POST','GET')));
  $ticket['newstate'] = intval(get_var('ticket_newstate',array('POST','GET')));

  if($_POST['cancel'] || !$state_id)
  {
    $GLOBALS['phpgw']->redirect_link('/tts/states.php');
  }

  if($_POST['delete'])
  {
    if ($ticket['state']==-100) //delete the tickets
    {
      $GLOBALS['phpgw']->db->query("select ticket_id from phpgw_tts_tickets where ticket_state=$state_id");

      if ($GLOBALS['phpgw']->db->next_record())
        $ids='('.$GLOBALS['phpgw']->db->f('ticket_id');

      while($GLOBALS['phpgw']->db->next_record())
        $ids.=','.$GLOBALS['phpgw']->db->f('ticket_id');

      $ids.=')';

//remove ticket's history
      $GLOBALS['phpgw']->db->query("delete from phpgw_history_log where history_appname='tts' and history_record_id in $ids",__LINE__,__FILE__);
      $GLOBALS['phpgw']->db->query("delete from phpgw_tts_tickets where ticket_state=$state_id",__LINE__,__FILE__);
    }
    else if ($ticket['state']==-200) //change the state
    {
      $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_state=".intval($ticket['newstate']).
        " where ticket_state=$state_id",__LINE__,__FILE__);
    }
    else
    {
      $GLOBALS['phpgw']->db->query("update phpgw_tts_tickets set ticket_state=".intval($ticket['state']).
        " where ticket_state=$state_id",__LINE__,__FILE__);
    }
    $GLOBALS['phpgw']->db->query("delete from phpgw_tts_transitions where transition_source_state=$state_id or transition_target_state=$state_id",__LINE__,__FILE__);
    $GLOBALS['phpgw']->db->query("delete from phpgw_tts_states where state_id=$state_id",__LINE__,__FILE__);
    $GLOBALS['phpgw']->redirect_link('/tts/states.php');
  }

  $GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['tts']['title'].
    ' - '.lang('Deleting the state');
  $GLOBALS['phpgw']->common->phpgw_header();

  $GLOBALS['phpgw']->historylog = createobject('phpgwapi.historylog','tts');

  $GLOBALS['phpgw']->template->set_file('delete_state','delete_state.tpl');
  $GLOBALS['phpgw']->template->set_block('delete_state', 'tts_title', 'tts_title');
  $GLOBALS['phpgw']->template->set_block('delete_state', 'tts_search', 'tts_search');
  $GLOBALS['phpgw']->template->set_block('delete_state', 'tts_list', 'tts_list');
  $GLOBALS['phpgw']->template->set_block('delete_state', 'form', 'form');
  $GLOBALS['phpgw']->template->set_block('delete_state', 'tts_row', 'tts_row');
  $GLOBALS['phpgw']->template->set_block('delete_state', 'tts_col_ifviewall', 'tts_col_ifviewall');
  $GLOBALS['phpgw']->template->set_block('delete_state', 'tts_head_ifviewall', 'tts_head_ifviewall');
  $GLOBALS['phpgw']->template->set_block('form','update_state_items','update_state_group');

  $s=id2field('phpgw_tts_states','state_name','state_id',$state_id);
  $GLOBALS['phpgw']->template->set_var('lang_are_you_sure',lang('You want to delete the state %1 and associated transitions. Are you sure?',$s));
  $GLOBALS['phpgw']->template->set_var('lang_tickets_in_state',lang('The tickets in the following list are in the state %1. Please, decide what should be done with them.',$s));
  $GLOBALS['phpgw']->template->set_var('lang_preferences', lang('Preferences'));
  $GLOBALS['phpgw']->template->set_var('lang_search', lang('search'));
  $GLOBALS['phpgw']->template->set_var('tts_newticket', lang('New ticket'));
  $GLOBALS['phpgw']->template->set_var('tts_head_status',lang('Status'));
  $GLOBALS['phpgw']->template->set_var('tts_notickets','');
  $GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));

  $GLOBALS['phpgw']->template->set_var('delete_state_link',
    $GLOBALS['phpgw']->link('/tts/delete_state.php','state_id='.$state_id));
  $GLOBALS['phpgw']->template->set_var('lang_delete_the_tickets',lang('Delete the listed tickets.'));
  $GLOBALS['phpgw']->template->set_var('lang_irregular_move_into_state',lang('Perform irregular transition into the following state'));
  $GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));
  $GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));

  $GLOBALS['phpgw']->db->query("select * from phpgw_tts_transitions where transition_source_state=".$state_id,__LINE__,__FILE__);

  while($GLOBALS['phpgw']->db->next_record())
  {
    $GLOBALS['phpgw']->template->set_var('update_state_value',try_lang($GLOBALS['phpgw']->db->f('transition_target_state')));
    $GLOBALS['phpgw']->template->set_var('update_state_text',try_lang($GLOBALS['phpgw']->db->f('transition_description')));
    $GLOBALS['phpgw']->template->parse('update_state_group', 'update_state_items', True);
  }

  // Choose the initial state to display
  $GLOBALS['phpgw']->template->set_var('options_state',
    listid_field('phpgw_tts_states','state_name','state_id','', "state_id<>".$state_id));

  if (!$sort)
  {
    $sortmethod = 'order by ticket_priority desc';
  }
  else
  {
    $sortmethod = "order by $order $sort";
  }

  $GLOBALS['phpgw']->db->query("select count(*) from phpgw_tts_tickets where ticket_state=".$state_id,__LINE__,__FILE__);
  $GLOBALS['phpgw']->db->next_record();
  $numtotal = $GLOBALS['phpgw']->db->f('0') ;

  $GLOBALS['phpgw']->db->query("select count(*) from phpgw_tts_tickets where ticket_status='O' and ticket_state=".$state_id,__LINE__,__FILE__);
  $GLOBALS['phpgw']->db->next_record();
  $numopen = $GLOBALS['phpgw']->db->f('0') ;

  $GLOBALS['phpgw']->template->set_var('tts_numtotal',lang('Tickets total %1',$numtotal));
  $GLOBALS['phpgw']->template->set_var('tts_numopen',lang('Tickets open %1',$numopen));


  $db2 = clone($GLOBALS['phpgw']->db);
  $GLOBALS['phpgw']->db->query("select * from phpgw_tts_tickets where ticket_state=".$state_id." ".$sortmethod,__LINE__,__FILE__);
  $numfound = $GLOBALS['phpgw']->db->num_rows();

  $GLOBALS['phpgw']->template->set_var('tts_ticketstotal', lang('Tickets total %1',$numtotal));
  $GLOBALS['phpgw']->template->set_var('tts_ticketsopen', lang('Tickets open %1',$numopen));

  // fill header
  $GLOBALS['phpgw']->template->set_var('tts_head_bgcolor',$GLOBALS['phpgw_info']['theme']['th_bg'] );
  $GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg'] );
  $GLOBALS['phpgw']->template->set_var('tts_head_ticket', $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'ticket_id',$order,'/tts/delete_state.php?state_id=$state_id',lang('Ticket').' #'));
  $GLOBALS['phpgw']->template->set_var('tts_head_prio', $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'ticket_priority',$order,'/tts/delete_state.php?state_id=$state_id',lang('Prio')));
  $GLOBALS['phpgw']->template->set_var('tts_head_group',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'ticket_group',$order,'/tts/delete_state.php?state_id=$state_id',lang('Group')));
  $GLOBALS['phpgw']->template->set_var('tts_head_category',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'ticket_category',$order,'/tts/delete_state.php?state_id=$state_id',lang('Category')));
  $GLOBALS['phpgw']->template->set_var('tts_head_assignedto', $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'ticket_assignedto',$order,'/tts/delete_state.php?state_id=$state_id',lang('Assigned to')));
  $GLOBALS['phpgw']->template->set_var('tts_head_openedby', $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'ticket_owner',$order,'/tts/delete_state.php?state_id=$state_id',lang('Opened by')));

  $GLOBALS['phpgw']->template->set_var('tts_head_dateopened',lang('Date opened'));

  $GLOBALS['phpgw']->template->set_var('tts_head_subject', $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'ticket_subject',$order,'/tts/delete_state.php?state_id=$state_id',lang('Subject')));
  $GLOBALS['phpgw']->template->set_var('tts_head_state', $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'ticket_state',$order,'/tts/delete_state.php?state_id=$state_id',lang('State')));

  if ($GLOBALS['phpgw']->db->num_rows() == 0)
  {
    $GLOBALS['phpgw']->template->set_var('rows', '<p><center>'.lang('No tickets found').'</center>');
  }
  else
  {
    while ($GLOBALS['phpgw']->db->next_record())
    {
      $GLOBALS['phpgw']->template->set_var('tts_col_status','');
      $priority = $GLOBALS['phpgw']->db->f('ticket_priority');
      switch ($priority)
      {
        case 1:  $tr_color = $GLOBALS['phpgw_info']['theme']['bg01']; break;
        case 2:  $tr_color = $GLOBALS['phpgw_info']['theme']['bg02']; break;
        case 3:  $tr_color = $GLOBALS['phpgw_info']['theme']['bg03']; break;
        case 4:  $tr_color = $GLOBALS['phpgw_info']['theme']['bg04']; break;
        case 5:  $tr_color = $GLOBALS['phpgw_info']['theme']['bg05']; break;
        case 6:  $tr_color = $GLOBALS['phpgw_info']['theme']['bg06']; break;
        case 7:  $tr_color = $GLOBALS['phpgw_info']['theme']['bg07']; break;
        case 8:  $tr_color = $GLOBALS['phpgw_info']['theme']['bg08']; break;
        case 9:  $tr_color = $GLOBALS['phpgw_info']['theme']['bg09']; break;
        case 10: $tr_color = $GLOBALS['phpgw_info']['theme']['bg10']; break;
        default: $tr_color = $GLOBALS['phpgw_info']['theme']['bg_color'];
      }

      if ($filter!="viewopen" && $GLOBALS['phpgw']->db->f('t_timestamp_closed'))
      {
        $tr_color = $GLOBALS['phpgw_info']['theme']['th_bg']; /*"#CCCCCC";*/
      }

      $db2->query("select count(*) from phpgw_tts_views where view_id='" . $GLOBALS['phpgw']->db->f('ticket_id')
        . "' and view_account_id='" . $GLOBALS['phpgw_info']['user']['account_id'] . "'",__LINE__,__FILE__);
      $db2->next_record();

      if ($db2->f(0))
      {
        $ticket_read = True;
      }
      else
      {
        $ticket_read = False;
      }

      $GLOBALS['phpgw']->template->set_var('tts_row_color', $tr_color );
      $GLOBALS['phpgw']->template->set_var('tts_ticketdetails_link', $GLOBALS['phpgw']->link('/tts/viewticket_details.php','ticket_id=' . $GLOBALS['phpgw']->db->f('ticket_id')));

      $GLOBALS['phpgw']->template->set_var('row_ticket_id','<a href="' . $GLOBALS['phpgw']->link('/tts/viewticket_details.php','ticket_id=' . $GLOBALS['phpgw']->db->f('ticket_id')) . '">' . $GLOBALS['phpgw']->db->f('ticket_id') . '</a>');

      if (! $ticket_read)
      {
        $GLOBALS['phpgw']->template->set_var('row_status','<img src="templates/default/images/updated.gif">');
      }
      else
      {
        $GLOBALS['phpgw']->template->set_var('row_status','&nbsp;');
      }

      $priostr = '';
      while ($priority > 0)
      {
        $priostr = $priostr . "||";
        $priority--;
      }
      $GLOBALS['phpgw']->template->set_var('tts_t_priostr',$priostr );

      $cat_name   = $GLOBALS['phpgw']->categories->id2name($GLOBALS['phpgw']->db->f('ticket_category'));
      $GLOBALS['phpgw']->template->set_var('row_category',$cat_name);

      $group_name = $GLOBALS['phpgw']->accounts->id2name($GLOBALS['phpgw']->db->f('ticket_group'));
      $group_name = ($group_name ? $group_name : '--');
      $GLOBALS['phpgw']->template->set_var('row_group',$group_name);

      $GLOBALS['phpgw']->template->set_var('tts_t_assignedto', $GLOBALS['phpgw']->db->f('ticket_assignedto')?$GLOBALS['phpgw']->accounts->id2name($GLOBALS['phpgw']->db->f('ticket_assignedto')):lang('None'));
      $GLOBALS['phpgw']->template->set_var('tts_t_user',$GLOBALS['phpgw']->accounts->id2name($GLOBALS['phpgw']->db->f('ticket_owner')));

      $history_values = $GLOBALS['phpgw']->historylog->return_array(array(),array('O'),'history_timestamp','ASC',$GLOBALS['phpgw']->db->f('ticket_id'));
      $GLOBALS['phpgw']->template->set_var('tts_t_timestampopened',$GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'] - ((60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'])));

      if ($GLOBALS['phpgw']->db->f('ticket_status') == 'X')
      {
        $history_values = $GLOBALS['phpgw']->historylog->return_array(array(),array('X'),'history_timestamp','DESC',$GLOBALS['phpgw']->db->f('ticket_id'));
        $GLOBALS['phpgw']->template->set_var('tts_t_timestampclosed',$GLOBALS['phpgw']->common->show_date($history_values[0]['datetime'] - ((60*60) * $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'])));
        $GLOBALS['phpgw']->template->parse('tts_col_status','tts_col_ifviewall',False);
      }
      else {
//        if ($GLOBALS['phpgw']->db->f('ticket_assignedto') != -1)
//        {
//          $assigned_to = lang('Not assigned');
//        }
//        else
//        {
//          $assigned_to = $GLOBALS['phpgw']->accounts->id2name($GLOBALS['phpgw']->db->f('ticket_assignedto'));
//        }
//        $GLOBALS['phpgw']->template->set_var('tts_t_timestampclosed',$assigned_to);
        $GLOBALS['phpgw']->template->set_var('tts_t_timestampclosed',lang('Open'));
        $GLOBALS['phpgw']->template->parse('tts_col_status','tts_col_ifviewall',False);
      }
      $GLOBALS['phpgw']->template->set_var('tts_t_subject', $GLOBALS['phpgw']->db->f('ticket_subject'));
      $GLOBALS['phpgw']->template->set_var('tts_t_state',
        id2field('phpgw_tts_states','state_name','state_id',$GLOBALS['phpgw']->db->f('ticket_state')));

      $GLOBALS['phpgw']->template->parse('rows','tts_row',True);
    }
  }

  // this is a workaround to clear the subblocks autogenerated vars
  $GLOBALS['phpgw']->template->set_var('tts_row','');
  $GLOBALS['phpgw']->template->set_var('tts_col_ifviewall','');
  $GLOBALS['phpgw']->template->set_var('tts_head_ifviewall','');
  $GLOBALS['phpgw']->template->set_var('tts_ticket_id_read','');
  $GLOBALS['phpgw']->template->set_var('tts_ticket_id_unread','');

  $GLOBALS['phpgw']->template->pfp('out','delete_state');

  $GLOBALS['phpgw']->common->phpgw_footer();
?>

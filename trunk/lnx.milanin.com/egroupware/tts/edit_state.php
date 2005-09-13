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

  // $Id: edit_state.php,v 1.5 2004/06/10 14:46:08 dragob Exp $
  // $Source: /cvsroot/egroupware/tts/edit_state.php,v $

  $GLOBALS['phpgw_info']['flags']['currentapp']          = 'tts';
  $GLOBALS['phpgw_info']['flags']['enable_send_class']   = True;
  $GLOBALS['phpgw_info']['flags']['enable_config_class'] = True;
  $GLOBALS['phpgw_info']['flags']['enable_categories_class'] = True;
  $GLOBALS['phpgw_info']['flags']['noheader']            = True;
  include('../header.inc.php');

  $state_id = intval(get_var('state_id',array('POST','GET')));

  if($_POST['cancel'])
  {
    $GLOBALS['phpgw']->redirect_link('/tts/states.php');
  }

  $GLOBALS['phpgw']->config->read_repository();

  if($_POST['save'])
  {
    $state = $_POST['state'];
    if (get_magic_quotes_gpc())
    {
      foreach(array('id','name','description') as $name)
      {
        $state[$name] = stripslashes($state[$name]);
      }
    }

    if (!$state_id)
    {
      $auto=($state['autoid']=='on');
      $GLOBALS['phpgw']->db->query("insert into phpgw_tts_states (".($auto?'':'state_id,')."state_name,state_description,state_initial) "
        . " values ('"
        . ($auto?'':(addslashes($state['id']) . "','"))
        . addslashes($state['name']) . "','"
        . addslashes($state['description']) . "','"
        . ($state['initial']=='on'?1:0). "')",__LINE__,__FILE__);
    }
    else
    {
      $GLOBALS['phpgw']->db->query("update phpgw_tts_states "
        . " set state_id='". addslashes($state['id']) . "', "
        . " state_name='". addslashes($state['name']) . "', "
        . " state_description='". addslashes($state['description']) . "', "
        . " state_initial=". ($state['initial']=='on'?1:0) 
        . " WHERE state_id=".intval($state_id),__LINE__,__FILE__);
  
    }
  
    $GLOBALS['phpgw']->redirect_link('/tts/states.php');
  }
  else
  {
    // select the ticket that you selected
    $GLOBALS['phpgw']->db->query("select * from phpgw_tts_states where state_id='$state_id'",__LINE__,__FILE__);
    $GLOBALS['phpgw']->db->next_record();

    $state['name']      = $GLOBALS['phpgw']->db->f('state_name');
    $state['description']   = try_lang($GLOBALS['phpgw']->db->f('state_description'));
    $state['initial']       = $GLOBALS['phpgw']->db->f('state_initial');
  
    $GLOBALS['phpgw']->template->set_file(array(
      'edit_state'   => 'edit_state.tpl'
    ));
    $GLOBALS['phpgw']->template->set_block('edit_state','form');

    $GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['tts']['title'].
      ' - '.(!$state_id ? lang('Create new state') : lang('Edit the state'));
    $GLOBALS['phpgw']->common->phpgw_header();

    $GLOBALS['phpgw']->template->set_var('form_action',
      $GLOBALS['phpgw']->link('/tts/edit_state.php',
      array(state_id => $state_id)));

    if (!$state_id)
    {
      $GLOBALS['phpgw']->template->set_block('form','autoid','aid');
      $GLOBALS['phpgw']->template->set_var('lang_auto_id',lang("Check here to generate the state's ID automatically or enter a particular ID below."));
      $GLOBALS['phpgw']->template->parse('aid','autoid',True);
    }
    else
    {
      $GLOBALS['phpgw']->template->set_block('form','autoid','aid');
      $GLOBALS['phpgw']->template->set_var('aid',''); //clear the section
    }

    $GLOBALS['phpgw']->template->set_var('lang_state_id',lang('State ID'));
    $GLOBALS['phpgw']->template->set_var('lang_state_name',lang('State Name'));
    $GLOBALS['phpgw']->template->set_var('lang_state_description', lang('Description'));
    $GLOBALS['phpgw']->template->set_var('lang_new_ticket_into_state', lang('New tickets can be put into this state.') );
    $GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
    $GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));

    $GLOBALS['phpgw']->template->set_var('value_description',$state['description']);
    $GLOBALS['phpgw']->template->set_var('value_id',$state_id>0?intval($state_id):'');
    $GLOBALS['phpgw']->template->set_var('value_name',try_lang($state['name']));
    $GLOBALS['phpgw']->template->set_var('value_initial',($state['initial']?'CHECKED':''));

    $GLOBALS['phpgw']->template->pfp('out','form');
    $GLOBALS['phpgw']->common->phpgw_footer();
  }
?>

<?php
    /**************************************************************************\
    * eGroupWare - Daily Comic Admin Link Data                                 *
    * http://www.egroupware.org                                                *
    * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
    * --------------------------------------------                             *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

    /* $Id: admin_comics.php,v 1.7 2004/01/27 15:12:50 reinerj Exp $ */

	$phpgw_info['flags'] = Array(
		'currentapp' => 'comic',
		'enable_nextmatchs_class' => True,
		'admin_header' => True
	);

	include('../header.inc.php');
	include('inc/comic_data.inc.php');

	$title             = lang('Daily Comics Data');

	$done_label        = lang('Done');
	$doneurl           = $GLOBALS['phpgw']->link('/admin/index.php');

	$message           = '';
    
	$act = get_var('act', array('GET','POST'));
	$data_id = intval(get_var('data_id', array('GET','POST')));
	$order = get_var('order', array('GET','POST'));
	$sort = get_var('sort', array('GET','POST'));
	$filter = get_var('filter', array('GET','POST'));
	$qfield = get_var('qfield', array('GET','POST'));
	$query = get_var('query', array('GET','POST'));
	$start = get_var('start', array('GET','POST'));

	if ( empty($qfield) ) { $qfield = "data_title"; }

	if ($_POST['submit'])
	{
		switch($act)
		{
			case 'edit':
				$message = 'modification';
				break;
			case 'delete':
				$message = 'deletion';
				break;
			case 'add':
				$message = 'addition';
				break;
		}
		$message = lang('Performed %1 of element', $message);
	}

	$other_c           = '';
	$body_c            = '';

	$data_title = htmlspecialchars($_POST['data_title']);
	$data_name = @ucwords($_POST['data_title']);
	$data_name = preg_replace("/\s+/", "", $data_name);
	$data_name = substr($data_name, 0, 24);
	$data_name = htmlspecialchars($_POST['data_name']);

	switch($act)
	{
		case 'edit':
			if ($_POST['submit'])
			{
				$GLOBALS['phpgw']->db->lock('phpgw_comic_data');
				$GLOBALS['phpgw']->db->query('update phpgw_comic_data set '
					."data_title='".$data_title."',data_name='".$dataname."' "
					."where data_id='".$data_id."'",__LINE__,__FILE__);
				$GLOBALS['phpgw']->db->unlock();

				comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
				comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
			}
			else
			{
				comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
				comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
				comic_entry($data_id,$act,$order,$sort,$filter,$start,$query,$qfield,$other_c);
			}
			break;
		case 'delete':

			if ($_GET['confirm'])
			{
				$GLOBALS['phpgw']->db->lock('phpgw_comic_data');
				$GLOBALS['phpgw']->db->query('delete from phpgw_comic_data '
					."where data_id='".$data_id."'",__LINE__,__FILE__);
				$GLOBALS['phpgw']->db->unlock();

				comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
				comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
			}
			else
			{

				$urlback = "/comic/admin_comics.php?start=$start&order=$order"
							. "&filter=$filter&sort=$sort&query=$query&qfield=$qfield";

				$GLOBALS['phpgw']->db->query("select data_title from phpgw_comic_data where data_id=$data_id",
											__LINE__,__FILE__);
				$GLOBALS['phpgw']->db->next_record();

				
                $delete_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('comic'));
                $delete_tpl->set_unknowns('remove');
                $delete_tpl->set_file('confirm', 'admin.confirm.tpl');
    
				$delete_tpl->set_var(
					Array(
						'data_title' 	=> $GLOBALS['phpgw']->db->f("data_title"),
						'no_url' 		=> $GLOBALS['phpgw']->link("${urlback}"),
						'yes_url'		=> $GLOBALS['phpgw']->link("${urlback}&data_id=${data_id}&act=delete&confirm=true"),
						'no'			=> lang("No"),
						'yes'			=> lang("Yes")
					)
				);
    
				$message = lang("are you sure you want to delete this entry ?");
                $delete_tpl->parse('confirm_part', 'confirm');
                $body_c = $delete_tpl->get('confirm_part');

				$table_c = '';
				$add_c = '';
			}
	
			break;
		case 'add':
			if ($_POST['submit'])
			{
				$comic_data = array(
								'data_name' => $data_name,
								'data_title' => $data_title,
								'data_author' => '', 'data_prefix' => '',
								'data_date' => '0', 'data_comicid' => '0', 'data_linkurl' => '',
								'data_baseurl' => '', 'data_parseurl' => '', 'data_parsexpr' => '',
								'data_imageurl' => '', 'data_censorlvl' => '0', 'data_daysold' => '0',
								'data_width' => '0', 'data_swidth' => '0'
								);
				$GLOBALS['phpgw']->db->lock('phpgw_comic_data');
				$GLOBALS['phpgw']->db->query('INSERT INTO phpgw_comic_data ('
											. implode(',',array_keys($comic_data)).') VALUES ('
											. $GLOBALS['phpgw']->db->column_data_implode(',',$comic_data,False)
											. ')', __LINE__,__FILE__);
				$GLOBALS['phpgw']->db->unlock();
			}
			comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
			comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
			break;
		default:
			comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
			comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
			break;
	}
    
	$comics_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('comic'));
	$comics_tpl->set_unknowns('remove');
	$comics_tpl->set_file(
		Array(
			'message'   => 'message.common.tpl',
			'comics'    => 'admin.datalist.tpl'
		)
	);
	$comics_tpl->set_var(
		Array(
			'messagename'      => $message,
			'title'            => $title,
			'done_url'         => $doneurl,
			'done_label'       => $done_label,
			'data_table'       => $table_c,
			'add_form'         => $add_c,
			'other_form'       => $other_c
		)
	);

	$comics_tpl->parse('message_part','message');
	$message_c = $comics_tpl->get('message_part');

	if ( empty($body_c) ) 
	{
		$comics_tpl->parse('body_part','comics');
		$body_c = $comics_tpl->get('body_part');
	}
    
    /**************************************************************************
     * pull it all together
     *************************************************************************/
	$body_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('comic'));
	$body_tpl->set_unknowns('remove');
	$body_tpl->set_file('body','admin.common.tpl');
	$body_tpl->set_var(
		Array(
			'admin_message' => $message_c,
			'admin_body'    => $body_c
		)
	);
	$body_tpl->parse('BODY','body');
	$body_tpl->p('BODY');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>

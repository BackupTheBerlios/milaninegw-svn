<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
   Copyright (C)2002 - 2004 Pim Snel <pim@lingewoud.nl>

   eGroupWare - http://www.egroupware.org

   This file is part of JiNN

   JiNN is free software; you can redistribute it and/or modify it under
   the terms of the GNU General Public License as published by the Free
   Software Foundation; version 2.

   JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
   WARRANTY; without even the implied warranty of MERCHANTABILITY or 
   FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
   for more details.

   You should have received a copy of the GNU General Public License 
   along with JiNN; if not, write to the Free Software Foundation, Inc.,
   59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
   */

   class uiadminacl extends uiadmin
   {

	  var $nextmatch;
	  var $common;

	  function uiadminacl($bo)
	  {

		 if(!$GLOBALS['phpgw_info']['user']['apps']['admin'])
		 {
			Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiuser.index'));
			$GLOBALS['phpgw']->common->phpgw_exit();
		 }

		 $this->bo=$bo;
		 $this->ui = CreateObject('jinn.uicommon');
		 $this->nextmatchs=CreateObject('phpgwapi.nextmatchs');
		 $this->template = $GLOBALS['phpgw']->template;
		 $this->common = CreateObject('jinn.bocommon');
	  }

	  /*************************************************************************\
	  * accessrights mainscreen                                                 *
	  \*************************************************************************/

	  function main_screen()
	  {
		 $this->template->set_file(array(
			'access_rights_main' => 'access_rights.tpl'
		 ));
		 // get all sites
		 $sites=$this->bo->common->get_sites_allowed($GLOBALS['phpgw_info']['user']['account_id']);
		 if (count($sites)>0)
		 {
			foreach($sites as $site_id)
			{
			   unset($object_rows);

			   $objects=$this->bo->common->get_objects_allowed($site_id,$GLOBALS['phpgw_info']['user']['account_id']);
			   if (count($objects)>0)
			   {
				  foreach($objects as $object_id)
				  {
					 if ($row_color==$GLOBALS['phpgw_info']['theme']['row_on'])
					 {
						$row_color=$GLOBALS['phpgw_info']['theme']['row_off'];
					 }
					 else
					 {
						$row_color=$GLOBALS['phpgw_info']['theme']['row_on'];
					 }

					 $object_rows.= '<tr bgcolor="'.$row_color.'"><td>'.$this->bo->so->get_object_name($object_id).'</td><td><a href="';
							  $object_rows.= $GLOBALS[phpgw]->link('/index.php',"menuaction=jinn.uiadmin.set_access_rights_site_objects&object_id=$object_id&site_id=$site_id");
							  $object_rows.= '">'.lang('set object moderator').'</a></td></tr>';
				  }
			   }

			   $this->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			   $this->template->set_var('lang_site_admin',lang('set site admin'));
			   $this->template->set_var('site_name',$this->bo->so->get_site_name($site_id));
			   $this->template->set_var('link_site_admin',$GLOBALS[phpgw]->link('/index.php',"menuaction=jinn.uiadmin.set_access_rights_sites&site_id=$site_id"));
			   $this->template->set_var('object_rows',$object_rows);

			   $this->template->pparse('out','access_rights_main');
			}
		 }

	  }



	  /*************************************************************************\
	  * adding and removing users to an object                                  *
	  \*************************************************************************/

	  function set_site_objects()
	  {

		 $this->template->set_file(array(
			'accounts' => 'accounts.tpl'
		 ));

		 list($site_id,$object_id,$sort, $order,$total,$start,$query)=$this->common->get_global_vars(array('site_id','object_id','sort','order','total','start','query'));

		 $object_name =$this->bo->so->get_object_name($object_id);
		 $site_name = $this->bo->so->get_site_name($site_id);

		 $this->template->set_block('accounts','list','list');
		 $this->template->set_block('accounts','row','row');
		 $this->template->set_block('accounts','row_empty','row_empty');

		 $account_hidden_info = $GLOBALS['phpgw']->accounts->get_list('accounts','','','','',$total); // all the accounts
		 $account_info = $GLOBALS['phpgw']->accounts->get_list('accounts',$start,$sort,$order,$query,$total); // the accounts in the current screen
		 $total = $GLOBALS['phpgw']->accounts->total;

		 $url = $GLOBALS['phpgw']->link('/index.php',"menuaction=jinn.uiadmin.set_access_rights_site_objects&object_id=$object_id&site_id=$site_id");

		 //	FIXME clean this up
		 $var = Array(
			'bg_color' => $GLOBALS['phpgw_info']['theme']['bg_color'],
			'th_bg'    => $GLOBALS['phpgw_info']['theme']['th_bg'],
			'left_next_matchs'   => $this->nextmatchs->left($url,$start,$total,"menuaction=jinn.uiadmin.set_access_rights_site_objects&object_id=$object_id&site_id=$site_id"),
			'lang_user_accounts' => lang("editors for object $object_name in site $site_name"),
			'right_next_matchs'  => $this->nextmatchs->right($url,$start,$total,"menuaction=jinn.uiadmin.set_access_rights_site_objects&object_id=$object_id&site_id=$site_id"),
			'lang_loginid'       => $this->nextmatchs->show_sort_order($sort,'account_lid',$order,$url,lang('LoginID')),
			'lang_lastname'      => $this->nextmatchs->show_sort_order($sort,'account_lastname',$order,$url,lang('last name')),
			'lang_firstname'     => $this->nextmatchs->show_sort_order($sort,'account_firstname',$order,$url,lang('first name')),
			'lang_edit'    => lang('edit'),
			'actionurl'    => $GLOBALS['phpgw']->link('/index.php','menuaction=jinn.boadmin.save_access_rights_object'),
			'accounts_url' => $url,
			'lang_search'  => lang('search'),
			'site_id'  => $site_id,
			'object_id'  => $object_id
		 );

		 $this->template->set_var($var);

		 if (! $GLOBALS['phpgw']->acl->check('account_access',4,'admin'))
		 {
			$this->template->set_var('input_add','<input type="submit" value="' . lang('save') . '">');
		 }

		 if (! $GLOBALS['phpgw']->acl->check('account_access',2,'admin'))
		 {
			$this->template->set_var('input_search',lang('Search') . '&nbsp;<input type="text" name="query">');
		 }


		 if (!count($account_info) || !$total)
		 {
			$this->template->set_var('message',lang('No matchs found'));
			$this->template->parse('rows','row_empty',True);
		 }
		 else
		 {
			// temporary bugfix
			foreach($account_info as $this_accounts)
			{
			   $this_accounts_id[]=$this_accounts['account_id'];
			}
			reset($account_info);
			//end fix

			foreach($account_hidden_info as $hidden_accounts)
			{
			   // kijk of account_id in acl voorkomt
			   $account_objects=$this->bo->so->get_objects_for_user($hidden_accounts['account_id']);

			   if ( $account_objects && in_array($object_id, $account_objects)  && !in_array($hidden_accounts['account_id'], $this_accounts_id) )
			   {
				  $hidden_editors.='<input type="hidden" name="editor'.$i++.'" value="'.$hidden_accounts['account_id'].'">';
			   }

			}
			$this->template->set_var('hidden_editors',$hidden_editors);

			foreach($account_info as $account)
			{

			   unset($checked);
			   $this->nextmatchs->template_alternate_row_color($this->template);

			   $account_objects=$this->bo->so->get_objects_for_user($account['account_id']);

			   if ($account_objects){
				  if (in_array($object_id, $account_objects))
				  {
					 $checked='CHECKED';
				  }
			   }

			   $var = array(
				  'row_loginid'   => $account['account_lid'],
				  'row_firstname' => (!$account['account_firstname']?'&nbsp':$account['account_firstname']),
				  'row_lastname'  => (!$account['account_lastname']?'&nbsp':$account['account_lastname'])
			   );
			   $this->template->set_var($var);

			   $this->template->set_var('row_editor','<input type="checkbox" name="editor'.$i++.'"'. $checked .' value="'.$account['account_id'].'">');

			   $this->template->parse('rows','row',True);
			}


		 }

		 $this->template->pfp('out','list');

	  }

	  /*************************************************************************\
	  * adding and removing users to an site                                    *
	  \*************************************************************************/

	  function set_sites()
	  {
		 list($site_id,$total,$start,$sort,$order,$query)=$this->common->get_global_vars(array('site_id','total','start','sort','order','query'));

		 $this->template->set_file(array
		 (
			'accounts' => 'accounts.tpl'
		 ));

		 $site_name = $this->bo->so->get_site_name($site_id);

		 $action=lang('Set administrators for %1',$site_name);//." $site_name";

		 $this->template->set_block('accounts','list','list');
		 $this->template->set_block('accounts','row','row');
		 $this->template->set_block('accounts','row_empty','row_empty');


		 $account_hidden_info = $GLOBALS['phpgw']->accounts->get_list('accounts','','','','',$total);
		 $account_info = $GLOBALS['phpgw']->accounts->get_list('accounts',$start,$sort,$order,$query,$total);
		 $total = $GLOBALS['phpgw']->accounts->total; 

		 $url = $GLOBALS['phpgw']->link('/index.php',"menuaction=jinn.uiadmin.set_access_rights_sites&site_id=$site_id");

		 // FIXME clean this up
		 $var = Array(
			'bg_color' => $GLOBALS['phpgw_info']['theme']['bg_color'],
			'th_bg'    => $GLOBALS['phpgw_info']['theme']['th_bg'],
			'left_next_matchs'   => $this->nextmatchs->left($url,$start,$total,"menuaction=jinn.uiadmin.set_access_rights_sites&site_id=$site_id"),
			'lang_user_accounts' => lang("administrators for site $site_name"),
			'right_next_matchs'  => $this->nextmatchs->right($url,$start,$total,"menuaction=jinn.uiadmin.set_access_rights_sites&site_id=$site_id"),
			'lang_loginid'       => $this->nextmatchs->show_sort_order($sort,'account_lid',$order,$url,lang('LoginID')),
			'lang_lastname'      => $this->nextmatchs->show_sort_order($sort,'account_lastname',$order,$url,lang('last name')),
			'lang_firstname'     => $this->nextmatchs->show_sort_order($sort,'account_firstname',$order,$url,lang('first name')),
			'lang_edit'    => lang('edit'),
			'actionurl'    => $GLOBALS['phpgw']->link('/index.php','menuaction=jinn.boadmin.save_access_rights_site'),
			'accounts_url' => $url,
			'lang_search'  => lang('search'),
			'site_id'  => $site_id,
		 );

		 $this->template->set_var($var);

		 if (! $GLOBALS['phpgw']->acl->check('account_access',4,'admin'))
		 {
			$this->template->set_var('input_add','<input type="submit" value="' . lang('save') . '">');
		 }

		 if (! $GLOBALS['phpgw']->acl->check('account_access',2,'admin'))
		 {
			$this->template->set_var('input_search',lang('Search') . '&nbsp;<input type="text" name="query">');
		 }


		 if (!count($account_info) || !$total)
		 {
			$this->template->set_var('message',lang('No matchs found'));
			$this->template->parse('rows','row_empty',True);
		 }
		 else
		 {

			// temporary bugfix
			foreach($account_info as $this_accounts)
			{
			   $this_accounts_id[]=$this_accounts['account_id'];
			}
			reset($account_info);
			//end fix


			foreach($account_hidden_info as $hidden_account)
			{
			   $account_sites=$this->bo->so->get_sites_for_user2($hidden_account['account_id']);

			   if ($account_sites && in_array($site_id, $account_sites) && !in_array($hidden_account['account_id'],$this_accounts_id ) ) 
			   {
				  $hidden_editors.='<input type="hidden" name="editor'.$i++.'" value="'.$hidden_account['account_id'].'">';

			   }
			}
			$this->template->set_var('hidden_editors',$hidden_editors);

			foreach($account_info as $account)
			{

			   unset($checked);
			   $this->nextmatchs->template_alternate_row_color($this->template);

			   // kijk of account_id in acl voorkomt
			   $account_sites=$this->bo->so->get_sites_for_user2($account['account_id']);

			   if ($account_sites){
				  if (in_array($site_id, $account_sites))
				  {
					 $checked='CHECKED';
				  }
			   }

			   $var = array(
				  'row_loginid'   => $account['account_lid'],
				  'row_firstname' => (!$account['account_firstname']?'&nbsp':$account['account_firstname']),
				  'row_lastname'  => (!$account['account_lastname']?'&nbsp':$account['account_lastname'])
			   );
			   $this->template->set_var($var);

			   $this->template->set_var('row_editor','<input type="checkbox" name="editor'.$i++.'"'. $checked .' value="'.$account['account_id'].'">');

			   $this->template->parse('rows','row',True);
			}
		 }

		 $this->template->pfp('out','list');

	  }

   }

?>

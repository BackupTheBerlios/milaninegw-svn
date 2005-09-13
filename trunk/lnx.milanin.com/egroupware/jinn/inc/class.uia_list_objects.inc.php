<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
   Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

   eGroupWare - http://www.egroupware.org

   This file is part of JiNN

   JiNN is free software; you can redistribute it and/or modify it under
   the terms of the GNU General Public License as published by the Free
   Software Foundation; either version 2 of the License, or (at your 
   boption) any later version.

   JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
   WARRANTY; without even the implied warranty of MERCHANTABILITY or 
   FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
   for more details.

   You should have received a copy of the GNU General Public License 
   along with JiNN; if not, write to the Free Software Foundation, Inc.,
   59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
   */

   // FIXME do we need to extend uiadmin?	
   class uia_list_objects extends uiadmin
   {
	  //FIXME do we need to have the bo
	  function uia_list_objects($bo)
	  {

		 if(!$GLOBALS['phpgw_info']['user']['apps']['admin'])
		 {
			Header('Location: '.$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiuser.index'));
			$GLOBALS['phpgw']->common->phpgw_exit();
		 }

		 $this->bo = $bo; 
		 $this->template = $GLOBALS['phpgw']->template;
	  }

	  function render_list($where_key,$where_value)
	  {
		 $this->template->set_file(array(

			'list_objects' => 'list_objects.tpl'
		 ));

		 $this->template->set_block('list_objects','listheader','listheader');
		 $this->template->set_block('list_objects','rows','rows');
		 $this->template->set_block('list_objects','listfooter','listfooter');

		 $table_title=lang('Site-objects');
		 $this->template->set_var('table_title',$table_title);

		 $lang_add_object=lang('add object');
		 $link_add_object=$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.add_edit_object&parent_site_id='.$where_value);

		 $lang_auto_add_object=lang('automaticly add objects for all tables');
		 $link_auto_add_object=$GLOBALS['phpgw']->link('/index.php','menuaction=jinn.uiadmin.add_edit_object&parent_site_id='.$where_value);

		 $this->template->set_var('lang_add_object',$lang_add_object);
		 $this->template->set_var('link_add_object',$link_add_object);
		 $this->template->set_var('lang_auto_add_object',$lang_auto_add_object);
		 $this->template->set_var('link_auto_add_object',$link_auto_add_object);

		 $fieldnames = $this->bo->so->get_phpgw_fieldnames('phpgw_jinn_site_objects');

		 $col_list=array_slice($fieldnames,2,2);

		 foreach ( $col_list as $field ) 
		 {
				
			
			$display_name = ucfirst(strtolower(ereg_replace("_", " ", $field)));
			$column_header.='<td bgcolor="'.$GLOBALS['phpgw_info']['theme']['th_bg'].'" valign="top"><font color="'.$GLOBALS['phpgw_info']['theme']['th_text'] .'">'.lang($display_name).'</font></td>';
		 }

		 $records=$this->bo->get_phpgw_records('phpgw_jinn_site_objects',$where_key,$where_value,$limit[start],$limit[stop],'num');

		 $this->template->set_var('bgclr',$GLOBALS['phpgw_info']['theme']['th_bg']);
		 $this->template->set_var('fieldnames',$column_header);
		 $this->template->parse('listheader','listheader');
		 $this->template->pparse('out','listheader');

		 if ($records)
		 {

			foreach($records as $recordvalues)
			{
			   $where_key=$fieldnames[0];
			   $where_value=$recordvalues[0];

			   if ($bgclr==$GLOBALS['phpgw_info']['theme']['row_off'])
			   {
				  $bgclr=$GLOBALS['phpgw_info']['theme']['row_on'];
			   }
			   else
			   {
				  $bgclr=$GLOBALS['phpgw_info']['theme']['row_off'];
			   }

			   $this->template->set_var('bgclr',$bgclr);
			   $this->template->set_var('link_edit',$GLOBALS[phpgw]->link("/index.php","menuaction=jinn.uiadmin.add_edit_object&where_key=$where_key&where_value=$where_value"));
			   $this->template->set_var('lang_edit',lang('edit'));

			   $this->template->set_var('link_del',$GLOBALS[phpgw]->link("/index.php","menuaction=jinn.boadmin.del_phpgw_jinn_site_objects&where_key=$where_key&where_value=$where_value"));
			   $this->template->set_var('lang_del',lang('delete'));
			   $this->template->set_var('confirm_del',lang('Are you sure?'));

			   if(count($recordvalues)>0)
			   {
				  $record_list=array_slice($recordvalues,2,2);

				  unset($table_row);
				  foreach($record_list as $recordvalue)
				  {

					 if (empty($recordvalue))
					 {
						$table_row.='<td style="background-color:'.$bgclr.'">&nbsp;</td>';
					 }
					 else
					 {
						$table_row.='<td style="background-color:'.$bgclr.'" valign="top">'.$recordvalue.'</td>';
					 }
				  }
			   }

			   $this->template->set_var('row',$table_row);
			   $this->template->parse('rowbuffer','rows',true);
			}

			$this->template->pparse('out','rowbuffer');

			$this->template->set_var('msg','');
		 }
		 else
		 {
			$this->template->set_var('msg',lang('No objects found for this site.'));
		 }

		 $this->template->pparse('out','listfooter');
	  }
   }
?>

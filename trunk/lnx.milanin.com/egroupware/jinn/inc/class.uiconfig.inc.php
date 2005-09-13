<?php
	/*
	JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
	Copyright (C)2002, 2004 Pim Snel <pim@lingewoud.nl>

	eGroupWare - http://www.egroupware.org

	This file is part of JiNN

	JiNN is free software; you can redistribute it and/or modify it under
	the terms of the GNU General Public License as published by the Free
	Software Foundation; either version 2 of the License, or (at your 
	option) any later version.

	JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
	WARRANTY; without even the implied warranty of MERCHANTABILITY or 
	FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License 
	along with JiNN; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
	*/



	class uiconfig extends uiuser
	{
		function uiconfig($bo)
		{
			$this->bo = $bo; 
			$this->template = $GLOBALS['phpgw']->template;
		}

		function show_fields()
		{
			$this->template->set_file(array(
				'config' => 'config_browse_view.tpl'
			));

			$columns_data=$this->bo->so->site_table_metadata($this->bo->site_id, $this->bo->site_object['table_name']);

			if(is_array($columns_data));
			{
				foreach($columns_data as $col_data)
				{
					$columns[]=$col_data[name];

				}

			}

			if (count($columns)>0)
			{
				// get the prefered columns, if they exist
				$prefs_show_hide=$this->bo->read_preferences('show_fields'); 

				$default_order=$this->bo->read_preferences('default_order');


				$prefs_show_hide=explode('|',$prefs_show_hide);
				if(is_array($prefs_show_hide))
				{
					foreach($prefs_show_hide as $pref_s_h)
					{
						$pref_array=explode(',',$pref_s_h);
						if($pref_array[0]==$this->bo->site_object_id)
						{
							$pref_columns=array_slice($pref_array,1);
						}
					}
				}

				// which/how many column to show, all, the prefered, or the default thirst 4
				if($pref_columns)
				{
					$show_cols=$pref_columns;
				}
				else
				{
					$show_cols=array_slice($columns,0,4);
				}

				foreach ($columns as $col)
				{
					unset($checked);
					unset($checked2);
					unset($checked3);

					if($default_order=="$col ASC") $checked2='CHECKED';
					if($default_order=="$col DESC") $checked3='CHECKED';
					if(in_array($col,$show_cols)) $checked='CHECKED';
					if ($bgclr==$GLOBALS['phpgw_info']['theme']['row_off'])
					{
						$bgclr=$GLOBALS['phpgw_info']['theme']['row_on'];
					}
					else
					{
						$bgclr=$GLOBALS['phpgw_info']['theme']['row_off'];
					}

					//FIXME move to template (gabriel help??)
					$rows.='<tr>';				
					$rows.='<td bgcolor='.$bgclr.' align="left">'.$col.'</td>';
					$rows.='<td bgcolor='.$bgclr.' align="left"><input name="SHOW'.$col.'" type=checkbox '.$checked.'></td>';
					$rows.='<td bgcolor='.$bgclr.' align="left"><input name="ORDER" type=radio value="'.$col.' ASC" '.$checked2.'>'.lang('ascending').'</td>';
					$rows.='<td bgcolor='.$bgclr.' align="left"><input name="ORDER" type=radio value="'.$col.' DESC" '.$checked3.'>'.lang('descending'). '</td>';
					$rows.='</tr>';
				}

				$form_action=$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.bouser.save_object_config');
				$button_save='<td><input type="submit" name="action" value="'.lang('save').'"></td>';

				$button_cancel='<td><input type="button" onClick="location=\''.
				$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiuser.browse_objects') .
				'\'" name="action" value="'.lang('cancel').'"></td>';

				$this->template->set_var('form_action',$form_action);
				$this->template->set_var('button_save',$button_save);
				$this->template->set_var('button_cancel',$button_cancel);
				$this->template->set_var('lang_config_table',lang('Configure view of').' '.$this->bo->site_object[name]);
				$this->template->set_var('rows',$rows);
				$this->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
				$this->template->set_var('lang_column_name',lang('column name'));
				$this->template->set_var('lang_show_column',lang('show colomn'));
				$this->template->set_var('lang_default_order',lang('default order'));

				$this->template->pparse('out','config');

				unset($this->message);
			}

		}
	}
	?>

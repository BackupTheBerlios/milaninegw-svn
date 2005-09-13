<?php
	/**************************************************************************\
	* eGroupWare - eTemplate Extension - Tab Widget                            *
	* http://www.egroupware.org                                                *
	* Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.tab_widget.inc.php,v 1.14 2004/01/27 16:58:17 reinerj Exp $ */

	/*!
	@class tab_widget
	@author ralfbecker
	@abstract widget that shows one row of tabs and an other row with the eTemplate of the selected tab
	@discussion see the example in 'etemplate.tab_widget.test' (use show to view it)
	@discussion This widget is independent of the UI as it only uses etemplate-widgets and has therefor no render-function
	*/
	class tab_widget
	{
		var $public_functions = array(
			'pre_process' => True,
			'post_process' => True
		);
		var $human_name = 'Tabs';	// this is the name for the editor

		function tab_widget($ui)
		{
		}

		function pre_process($form_name,&$value,&$cell,&$readonlys,&$extension_data,&$tmpl)
		{
			$dom_enabled = 0;//$GLOBALS['phpgw_info']['etemplate']['dom_enabled'];
			$labels = explode('|',$cell['label']);
			$helps = explode('|',$cell['help']);
			$names = explode('|',$cell['name']);

			$tab = new etemplate('etemplate.tab_widget.tab'.($dom_enabled ? '_dom' : ''));
			$tab_active = new etemplate('etemplate.tab_widget.tab_active');
			$tabs = new etemplate();
			$tabs->init('*** generated tabs','','',0,'',0,0);	// make an empty template

			foreach($names as $k => $name)
			{
				if (!strstr($name,'.'))
				{
					$name = $names[$k] = $tmpl->name . '.' . $name;
				}
				if ($extension_data == $name)
				{
					$selected_tab = $name;
				}
			}
			if (empty($selected_tab))
			{
				$extension_data = $selected_tab = $names[0];
			}
			$tab_row = array();	// generate the tab row
			while (list($k,$name) = each($names))
			{
				if (!strstr($name,'.'))
				{
					$name = $names[$k] = $tmpl->name . '.' . $name;
				}
				$tcell = $tabs->empty_cell();
				if ($extension_data == $name)
				{
					// save selected tab in persistent extension_data to use it in post_process
					$selected_tab = $name;
					$tcell['obj'] = $tab_active;
					$tcell['name'] = $tab_active->name;
				}
				else
				{
					$tcell['obj'] = $tab;
					$tcell['name'] = $tab->name;
				}
				if ($dom_enabled)
				{
					$tcell['obj']->set_cell_attribute('tab','onclick',"activate_tab('$name','$cell[name]');");
					$tcell['obj']->set_cell_attribute('tab','id',$name.'-tab');
				}
				$tcell['type'] = 'template';
				$tcell['size'] = $cell['name'].'['.$name.']';
				$value[$name] = array(
					'name'  => $name,
					'label' => $labels[$k],
					'help'  => $helps[$k]
				);
				$tab_row[$tabs->num2chrs($k)] = $tcell;
			}
			// add one empty cell to take all the space of the row
			$tab_row[$k = $tabs->num2chrs(sizeof($tab_row))] = $tabs->empty_cell();
			$tabs->data[0][$k] = '99%'; // width
			$tabs->data[0]['c1'] = ',bottom';

			$tabs->data[1] = $tab_row;
			$tabs->set_rows_cols();
			$tabs->size = "$cell[width],,,0,0";

			$tab_widget = new etemplate('etemplate.tab_widget');
			$tab_widget->set_cell_attribute('@tabs','obj',$tabs);
			
			if ($dom_enabled)
			{
				$tab_widget->set_cell_attribute('@body','type','deck');
				$tab_widget->set_cell_attribute('@body','width',$cell['width']);
				$tab_widget->set_cell_attribute('@body','height',$cell['height']);
				$tab_widget->set_cell_attribute('@body','size',count($names));
				$tab_widget->set_cell_attribute('@body','class',$cell['class']);
				foreach($names as $n => $name)
				{
					$bcell = $tab_widget->empty_cell();
					$bcell['type'] = 'template';
					$bcell['obj'] = new etemplate($name,$tmpl->as_array());
					$bcell['name'] = $name;
					$tab_widget->set_cell_attribute('@body',$n+1,$bcell);
				}
				$tab_widget->set_cell_attribute('@body','name',$cell['name']);
			}
			else
			{
				$stab = new etemplate($selected_tab,$tmpl->as_array());
				$options = array_pad(explode(',',$stab->size),3,'');
				$options[3] = ($options[3]!= '' ? $options[3].' ':'') . 'tab_body';
				$stab->size = implode(',',$options);
				$tab_widget->set_cell_attribute('@body','obj',$stab);
			}
			$tab_widget->set_cell_attribute('@body','name',$selected_tab);

			$cell['type'] = 'template';
			$cell['obj'] = &$tab_widget;
			$cell['label'] = $cell['help'] = '';

			return False;	// NO extra Label
		}

		function post_process($name,&$value,&$extension_data,&$loop,&$tmpl)
		{
			//echo "<p>tab_widget::post_process($name): value = "; _debug_array($value);
			if (is_array($value))
			{
				reset($value);
				list($tab,$button) = each($value);
				list(,$button) = each($button);
				if ($button)
				{
					$extension_data = $tab;
					$loop = True;
				}
			}
			return True;
		}
	}

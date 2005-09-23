<?php
	/**************************************************************************\
	* eGroupWare - eTemplates - Editor                                         *
	* http://www.eGroupWare.org                                                *
	* Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.editor.inc.php,v 1.39 2004/03/09 21:46:25 ralfbecker Exp $ */

	class editor
	{
		var $debug;
		var $etemplate; // eTemplate we edit
		var $editor;	// editor eTemplate
		var $aligns = array(
			'' => 'Left',
			'right' => 'Right',
			'center' => 'Center'
		);
		var $options = array(
			'width',
			'height',
			'border',
			'class',
			'spacing',
			'padding',
			'overflow'
		);
		var $overflows = array(
			'' => 'visible',
			'hidden' => 'hidden',
			'scroll' => 'scroll',
			'auto' => 'auto'
		);
		var $extensions = '';

		var $public_functions = array
		(
			'edit'         => True,
			'process_edit' => True,
			'delete'       => True,
			'show'         => True,
			//'admin'       => True,
			//'preferences' => True
		);

		function editor()
		{
			$this->etemplate = CreateObject('etemplate.etemplate');
			//echo '$HTTP_POST_VARS='; _debug_array($HTTP_POST_VARS);

			$this->editor = new etemplate('etemplate.editor');
		}

		function edit($msg = '')
		{
			if (isset($_GET['name']) && !$this->etemplate->read($_GET))
			{
				$msg .= lang('Error: Template not found !!!');
			}
			if (!is_array($this->extensions))
			{
				$this->extensions = $this->scan_for_extensions();
				if (count($this->extensions))
				{
					$msg .= lang('Extensions loaded:') . ' ' . implode(', ',$this->extensions);
					$msg_ext_loaded = True;
				}
			}
			list($app) = explode('.',$this->etemplate->name);
			if ($app && $app != 'etemplate')
			{
				$GLOBALS['phpgw']->translation->add_app($app);	// load translations for app
			}
			if ($app && $app != 'etemplate' && is_array($this->extensions) &&
			    (!is_array($this->extensions['**loaded**']) || !$this->extensions['**loaded**'][$app]))
			{
				$extensions = $this->scan_for_extensions($app);
				if (count($extensions))
				{
					$msg .= (!$msg_ext_loaded?lang('Extensions loaded:').' ':', ') . implode(', ',$extensions);
					$this->extensions += $extensions;
				}
				$this->extensions['**loaded**'][$app] = True;
			}
			$content = $this->etemplate->as_array() + array(
				'cols' => $this->etemplate->cols,
				'msg' => $msg
			);
			$options = explode(',',$this->etemplate->size);
			reset($this->options);
			while (list($n,$opt) = each($this->options))
			{
				$content['options'][$opt] = $options[$n];
			}
			$cols_spanned = array();
			reset($this->etemplate->data);
			if (isset($this->etemplate->data[0]))
			{
				each($this->etemplate->data);
			}
			$no_button = array();
			while (list($row,$cols) = each($this->etemplate->data))
			{
				if ($this->etemplate->rows <= 1)
				{
					$no_button["Row$row"]['delete_row[1]'] = True;
				}
				if ($row > 1)
				{
					$no_button["Row$row"]['insert_row[0]'] = True;
				}
				$content["Row$row"] = array(
					'height' => array("h$row" => $this->etemplate->data[0]["h$row"]),
					'class'  => array("c$row" => $this->etemplate->data[0]["c$row"])
				);
				for ($spanned = $c = 0; $c < $this->etemplate->cols; ++$c)
				{
					if (!(list($col,$cell) = each($cols)))
					{
						$cell = $this->etemplate->empty_cell();	// if cell gots lost, create it empty
						$col = $this->etemplate->num2chrs($c);
					}
					if (--$spanned > 0)	// preserv spanned cells
					{
						while(list($k,$v) = each($cell))		// so spanned (not shown) cells got
						{                                   // reported back like regular one
							$cols_spanned[$col.$row][$k] = $v;
						}
					}
					else
					{
						$spanned = $cell['span'] == 'all' ? $this->etemplate->cols-$c : 0+$cell['span'];
						
						switch($cell['type'])	// load a cell-type-specific tpl
						{
							case 'vbox':
							case 'hbox':
							case 'deck':
								$cell['cell_tpl'] = '.vbox';
								break;
						}
						$content[$col.$row] = $cell;
					}
					if ($row == 1)
					{
						$content["Col$col"] = array('width' => array($col => $this->etemplate->data[0][$col]));
						if ($this->etemplate->cols <= 1)
						{
							$no_button["Col$col"]['delete_col[1]'] = True;
						}
						if ($c > 0)
						{
							$no_button["Col$col"]['insert_col[0]'] = True;
						}
					}
				}
			}
			$no_button['ColA']['exchange_col[1]'] = $no_button['Row1']['exchange_row[1]'] = True;

			if ($this->debug)
			{
				echo 'editor.edit: content ='; _debug_array($content);
			}
			$types = array_merge($this->etemplate->types,$this->extensions);
			unset($types['**loaded**']);
			$this->editor->exec('etemplate.editor.process_edit',$content,
				array(
					'type' => $types,
					'align' => $this->aligns,
					'overflow' => $this->overflows
				),
				$no_button,$cols_spanned + array('**extensions**' => $this->extensions));
		}

		function swap(&$a,&$b)
		{
			$t = $a; $a = $b; $b = $t;
		}

		function process_edit($content)
		{
			if ($this->debug)
			{
				echo "editor.process_edit: content ="; _debug_array($content);
			}
			$this->extensions = $content['**extensions**']; unset($content['**extensions**']);
			$this->etemplate->init($content);

			$opts = array();
			reset($this->options);
			while (list(,$opt) = each($this->options))
			{
				$opts[$opt] = $content['options'][$opt];
			}
			$this->etemplate->size = ereg_replace(',*$','',implode(',',$opts));
			$this->etemplate->style = $content['style'];

			$names = array('width','height','class');
			$opts = array();
			while (list(,$opt) = each($names))
			{
				if (is_array($content[$opt]))
				{
					$opts += $content[$opt];
				}
			}
			$this->etemplate->data = array($opts);
			$row = 1; $col = 0;
			while (isset($content[$name = $this->etemplate->num2chrs($col) . $row]))
			{
				$cell = &$content[$name];
				switch ($cell['type'])
				{
					case 'vbox':
					case 'hbox':
					case 'deck':
						if ($cell['size'] < 2)
						{
							$cell['size'] = 2;
						}
						for ($n = 1; $n <= $cell['size']; ++$n)	// create new rows
						{
							if (!isset($cell[$n]) || !is_array($cell[$n]))
							{
								$cell[$n] = $this->etemplate->empty_cell();
							}
						}
						while (isset($cell[$n]))	// unset not longer used rows
						{
							unset($cell[$n++]);
						}
						break;
				}
				$row_data[$this->etemplate->num2chrs($col++)] = $cell;

				if (!isset($content[$name = $this->etemplate->num2chrs($col) . $row]))	// try new row
				{
					if ($col > $cols)
					{
						$cols = $col;
					}
					$this->etemplate->data[$row] = $row_data;
					++$row; $col = 0; $row_data = array();
				}
			}
			$this->etemplate->rows = $row - 1;
			$this->etemplate->cols = $cols;

			if (isset($content['insert_row']))
			{
				list($row) = each($content['insert_row']);
				$opts = $this->etemplate->data[0];		// move height + class options of rows
				for ($r = $this->etemplate->rows; $r > $row; --$r)
				{
					$opts['c'.(1+$r)] = $opts["c$r"]; unset($opts["c$r"]);
					$opts['h'.(1+$r)] = $opts["h$r"]; unset($opts["h$r"]);
				}
				$this->etemplate->data[0] = $opts;
				$old = $this->etemplate->data;	// move rows itself
				$row_data = array();
				for ($col = 0; $col < $this->etemplate->cols; ++$col)
				{
					$row_data[$this->etemplate->num2chrs($col)] = $this->etemplate->empty_cell();
				}
				$this->etemplate->data[++$row] = $row_data;
				for (; $row <= $this->etemplate->rows; ++$row)
				{
					$this->etemplate->data[1+$row] = $old[$row];
				}
				++$this->etemplate->rows;
			}
			elseif (isset($content['insert_col']))
			{
				list($insert_col) = each($content['insert_col']);
				for ($row = 1; $row <= $this->etemplate->rows; ++$row)
				{
					$old = $row_data = $this->etemplate->data[$row];
					$row_data[$this->etemplate->num2chrs($insert_col)] = $this->etemplate->empty_cell();
					for ($col = $insert_col; $col < $this->etemplate->cols; ++$col)
					{
						$row_data[$this->etemplate->num2chrs(1+$col)] = $old[$this->etemplate->num2chrs($col)];
					}
					$this->etemplate->data[$row] = $row_data;
				}
				$width = $this->etemplate->data[0];
				for ($col = $this->etemplate->cols; $col > $insert_col; --$col)
				{
					$width[$this->etemplate->num2chrs($col)] = $width[$this->etemplate->num2chrs($col-1)];
				}
				unset($width[$this->etemplate->num2chrs($insert_col)]);
				$this->etemplate->data[0] = $width;

				++$this->etemplate->cols;
			}
			elseif (isset($content['exchange_col']))
			{
				list($exchange_col) = each($content['exchange_col']);
				$right = $this->etemplate->num2chrs($exchange_col-1);
				$left  = $this->etemplate->num2chrs($exchange_col-2);

				for ($row = 1; $row <= $this->etemplate->rows; ++$row)
				{
					$this->swap($this->etemplate->data[$row][$left],$this->etemplate->data[$row][$right]);
				}
				$this->swap($this->etemplate->data[0][$left],$this->etemplate->data[0][$right]);
			}
			elseif (isset($content['exchange_row']))
			{
				list($er2) = each($content['exchange_row']); $er1 = $er2-1;
				$this->swap($this->etemplate->data[$er1],$this->etemplate->data[$er2]);
				$this->swap($this->etemplate->data[0]["c$er1"],$this->etemplate->data[0]["c$er2"]);
				$this->swap($this->etemplate->data[0]["h$er1"],$this->etemplate->data[0]["h$er2"]);
			}
			elseif (isset($content['delete_row']))
			{
				list($delete_row) = each($content['delete_row']);
				$opts = $this->etemplate->data[0];
				for ($row = $delete_row; $row < $this->etemplate->rows; ++$row)
				{
					$this->etemplate->data[$row] = $this->etemplate->data[1+$row];
					$opts["c$row"] = $opts['c'.(1+$row)];
					$opts["h$row"] = $opts['h'.(1+$row)];
				}
				unset($this->etemplate->data[$this->etemplate->rows--]);
				$this->etemplate->data[0] = $opts;
			}
			elseif (isset($content['delete_col']))
			{
				list($delete_col) = each($content['delete_col']);
				for ($row = 1; $row <= $this->etemplate->rows; ++$row)
				{
					$row_data = $this->etemplate->data[$row];
					for ($col = $delete_col; $col < $this->etemplate->cols; ++$col)
					{
						$row_data[$this->etemplate->num2chrs($col-1)] = $row_data[$this->etemplate->num2chrs($col)];
					}
					unset($row_data[$this->etemplate->num2chrs($this->etemplate->cols-1)]);
					$this->etemplate->data[$row] = $row_data;
				}
				$width = $this->etemplate->data[0];
				for ($col = $delete_col; $col < $this->etemplate->cols; ++$col)
				{
					$width[$this->etemplate->num2chrs($col-1)] = $width[$this->etemplate->num2chrs($col)];
				}
				$this->etemplate->data[0] = $width;
				--$this->etemplate->cols;
			}
			if ($this->debug)
			{
				echo 'editor.process_edit: rows='.$this->etemplate->rows.', cols='.
					$this->etemplate->cols.', data ='; _debug_array($this->etemplate->data);
			}
			// Execute the action resulting from the submit-button
			if ($content['read'])
			{
				if ($content['version'] != '')
				{
					$save_version = $content['version'];
					unset($content['version']);
					$this->etemplate->read($content);
					$newest_version = $this->etemplate->version;
					$content['version'] = $save_version;
				}
				if (!$this->etemplate->read($content))
				{
					$content['version'] = '';	// trying it without version
					if ($this->etemplate->read($content))
					{
						$msg = lang('only an other Version found !!!');
					}
					else
					{
						$result = $this->etemplate->search($content);
						if (count($result) > 1)
						{
							return $this->list_result(array('result' => $result));
						}
						elseif (!count($result) || !$this->etemplate->read($result[0]))
						{
							$msg = lang('Error: Template not found !!!');
						}
						elseif ($content['name'] == $result[0]['name'])
						{
							$msg = lang('only an other Version found !!!');
						}
					}
				}
				elseif ($newest_version != '' && $this->etemplate->version != $newest_version)
				{
					$msg = lang("newer version '%1' exists !!!",$newest_version);
				}
			}
			elseif ($content['delete'])
			{
				$this->delete();
				return;
			}
			elseif ($content['dump'])
			{
				list($name) = explode('.',$content['name']);
				if (empty($name) || !@is_dir(PHPGW_SERVER_ROOT.'/'.$name))
				{
					$msg = lang('Application name needed to write a langfile or dump the eTemplates !!!');
				}
				else
				{
					$msg = $this->etemplate->dump2setup($content['name']);
				}
			}
			elseif ($content['save'])
			{
				if (!$this->etemplate->modified_set || !$this->etemplate->modified)
				{
					$this->etemplate->modified = time();
				}
				$ok = $this->etemplate->save(trim($content['name']),trim($content['template']),trim($content['lang']),intval($content['group']),trim($content['version']));
				$msg = $ok ? lang('Template saved') : lang('Error: while saveing !!!');
			}
			elseif ($content['show'])
			{
				$this->show();
				return;
			}
			elseif ($content['langfile'])
			{
				list($name) = explode('.',$content['name']);
				if (empty($name) || !@is_dir(PHPGW_SERVER_ROOT.'/'.$name))
				{
					$msg = lang('Application name needed to write a langfile or dump the eTemplates !!!');
				}
				else
				{
					$additional = array();
					if ($name == 'etemplate')
					{
						$additional = $this->etemplate->types + $this->extensions + $this->aligns;
					}
					else	// try to call the writeLangFile function of the app's ui-layer
					{
						$ui = @CreateObject($name.'.'.($class = 'ui'.$name));
						if (!is_object($ui))
						{
							$ui = @CreateObject($name.'.'.($class = 'ui'));
						}
						if (!is_object($ui))
						{
							$ui = @CreateObject($name.'.'.($class = $name));
						}
						if (is_object($ui) && @$ui->public_functions['writeLangFile'])
						{
							$msg = "$class::writeLangFile: ".$ui->writeLangFile();
						}
						unset($ui);
					}
					if (empty($msg))
					{
						$msg = $this->etemplate->writeLangFile($name,'en',$additional);
					}
				}
			}
			elseif ($content['export_xml'])
			{
				$msg = $this->export_xml();
			}
			elseif ($content['import_xml'])
			{
				$msg = $this->import_xml($content['file']['tmp_name']);
			}
			elseif ($content['db_tools'])
			{
				ExecMethod('etemplate.db_tools.edit');
				return;
			}
			$this->edit($msg);
		}

		function export_xml()
		{
			$name = $this->etemplate->name;
			$template = $this->etemplate->template != '' ? $this->etemplate->template : 'default';

			list($app) = explode('.',$name);

			$dir = PHPGW_SERVER_ROOT . "/$app/templates/$template";
			if ($create_it = !is_dir($dir))
			{
				$dir = PHPGW_SERVER_ROOT . "/$app/templates";
			}
			if (!is_writeable($dir))
			{
				return lang("Error: webserver is not allowed to write into '%1' !!!",$dir);
			}
			if ($create)
			{
				mkdir($dir .= "/$template");
			}
			$file = $dir . '/' . substr($name,strlen($app)+1);
			if ($this->etemplate->lang)
			{
				$file .= '.' . $this->etemplate->lang;
			}
			$old_file = $file . '.old.xet';
			$file .= '.xet';
			if (file_exists($file))
			{
				if (file_exists($old_file))
				{
					unlink($old_file);
				}
				rename($file,$old_file);
			}

			if (!($f = fopen($file,'w')))
			{
				return 0;
			}
			if (!is_object($this->etemplate->xul_io))
			{
				$this->etemplate->xul_io = CreateObject('etemplate.xul_io');
			}
			$xul = $this->etemplate->xul_io->export($this->etemplate);

			fwrite($f,$xul);
			fclose($f);

			return lang("eTemplate '%1' written to '%2'",$name,$file);
		}

		function import_xml($file)
		{
			if ($file == 'none' || $file == '' || !($f = fopen($file,'r')))
			{
				return lang('no filename given or selected via Browse...');
			}
			$xul = fread ($f, filesize ($file));
			fclose($f);

			if (!is_object($this->etemplate->xul_io))
			{
				$this->etemplate->xul_io = CreateObject('etemplate.xul_io');
			}
			$imported = $this->etemplate->xul_io->import($this->etemplate,$xul);
			$this->etemplate->modified = @filemtime($f);
			$this->etemplate->modified_set = 'xul-import';

			if (is_array($imported))
			{
				if (count($imported) == 1)
				{
					$imported = lang("eTemplate '%1' imported, use Save to put it in the database",$this->etemplate->name);
				}
				else
				{
					$imported = lang('File contains more than one eTemplate, last one is shown !!!');
				}
			}
			return $imported;
		}

		function delete($content='',$back = 'edit')
		{
			if ($this->debug)
			{
				echo "delete(back='$back') content = "; _debug_array($content);
			}
			if (!is_array($content))
			{
				$content = array();
			}
			if (!is_array($this->extensions) && isset($content['**extensions**']))
			{
				$this->extensions = $content['**extensions**']; unset($content['**extensions**']);
			}
			if (isset($content['name']))
			{
				$read_ok = $this->etemplate->read($content);
			}
			if (isset($content['yes']))	// Delete
			{
				if ($read_ok)
				{
					$read_ok = $this->etemplate->delete();
				}
				$msg = $read_ok ? lang('Template deleted') : lang('Error: Template not found !!!');

				if ($content['back'] == 'list_result')
				{
					$this->list_result($content['preserv'],$msg);
				}
				else
				{
					$this->edit($msg);
				}
				return;
			}
			if (isset($content['no']))	// Back to ...
			{
				switch ($back = $content['back'])
				{
					case 'list_result':
						$this->$back($content['preserv']);
						return;
					case 'show':
						break;
					default:
						$back = 'edit';
				}
				$this->$back();
				return;
			}
			if (isset($_GET['name']) && !$this->etemplate->read($_GET))
			{
				$this->edit(lang('Error: Template not found !!!'));
				return;
			}
			$preserv = array(
				'preserv' => $content['preserv'],
				'back'    => $back
			);
			$content = $this->etemplate->as_array();

			$delete = new etemplate('etemplate.editor.delete');
			$delete->exec('etemplate.editor.delete',$content,array(),array(),
				$content+$preserv+array(
					'**extensions**' => $this->extensions
				),'');
		}

		function list_result($cont='',$msg='')
		{
			if ($this->debug)
			{
				echo "<p>etemplate.editor.list_result: cont="; _debug_array($cont);
			}
			if (!is_array($this->extensions) && is_array($cont) && isset($cont['**extensions**']))
			{
				$this->extensions = $cont['**extensions**']; unset($cont['**extensions**']);
			}
			if (!$cont || !is_array($cont))
			{
				return $this->edit('error');
			}
			if (!isset($cont['result']) || isset($cont['search']))
			{
				$cont['result'] = $this->etemplate->search($cont);
			}
			$result = $cont['result'];

			if (isset($cont['delete']))
			{
				list($delete) = each($cont['delete']);
				$this->etemplate->read($result[$delete-1]);
				unset($cont['delete']);
				unset($cont['result']);
				$this->delete(array('preserv' => $cont),'list_result');
				return;
			}
			if (isset($cont['delete_selected']))
			{
				while (list($row,$sel) = each($cont['selected']))
				{
					if ($sel)
					{
						$this->etemplate->read($result[$row-1]);
						$this->etemplate->delete();
						++$n;
					}
				}
				if ($n)
				{
					$msg = lang('%1 eTemplates deleted',$n);
				}
				unset($cont['selected']);
				unset($cont['delete_selected']);
				$result = $this->etemplate->search($cont);
			}
			if (isset($cont['read']))
			{
				list($read) = each($cont['read']);
				$this->etemplate->read($result[$read-1]);
				$this->edit();
				return;
			}
			if (!$msg)
			{
				$msg = lang('%1 eTemplates found',count($result));
			}
			unset($cont['result']);
			if (!isset($cont['name']))
			{
				$cont += $this->etemplate->as_array();
			}
			$content = $cont + array('msg' => $msg);

			reset($result);
			for ($row=1; list(,$param) = each($result); ++$row)
			{
				$content[$row] = $param;
			}
			$list_result = new etemplate('etemplate.editor.list_result');
			//$list_result->debug=1;
			$list_result->exec('etemplate.editor.list_result',$content,'','',array(
				'result' => $result,
				'**extensions**' => $this->extensions
			),'');
		}

		function show($post_vars='')
		{
			if ($this->debug)
			{
				echo "<p>etemplate.editor.show: content="; _debug_array($post_vars);
			}
			if (!is_array($post_vars))
			{
				$post_vars = array();
			}
			if (!is_array($this->extensions) && isset($post_vars['**extensions**']))
			{
				$this->extensions = $post_vars['**extensions**']; unset($post_vars['**extensions**']);
			}
			if (isset($_GET['name']) && !$this->etemplate->read($_GET) ||
			    isset($post_vars['name']) && !$this->etemplate->read($post_vars))
			{
				$msg = lang('Error: Template not found !!!');

				if (isset($post_vars['name']))
				{
					$post_vars['version'] = '';	// trying it without version
					if ($this->etemplate->read($post_vars))
					{
						$msg = lang('only an other Version found !!!');
					}
				}
			}
			if (!$msg && isset($post_vars['delete']))
			{
				$this->delete(array(),'show');
				return;
			}
			if (isset($post_vars['edit']))
			{
				$this->edit();
				return;
			}
			list($app) = explode('.',$this->etemplate->name);
			if ($app && $app != 'etemplate')
			{
				$GLOBALS['phpgw']->translation->add_app($app);	// load translations for app
			}
			$content = $this->etemplate->as_array() + array('msg' => $msg);

			$show = new etemplate('etemplate.editor.show');
			if (!$msg && isset($post_vars['values']) && !isset($post_vars['vals']))
			{
				$cont = $post_vars['cont'];
				for ($r = 1; list($key,$val) = @each($cont); ++$r)
				{
					$vals["@$r"] = $key;
					$vals["A$r"] = is_array($val) ? htmlspecialchars(serialize($val)).'#SeR#' : $val;
				}
				$show->data[$show->rows]['A']['name'] = 'etemplate.editor.values';
				$show->data[$show->rows]['A']['size'] = 'vals';
				$content['vals'] = $vals;
			}
			else
			{
				$show->data[$show->rows]['A']['obj'] = &$this->etemplate;
				$vals = $post_vars['vals'];
				$olds = $post_vars['olds'];

				for ($r = 1; isset($vals["A$r"]); ++$r)
				{
					$content['cont'][$olds["@$r"]] = substr($vals["A$r"],-5)=='#SeR#' ?
						unserialize(substr($vals["A$r"],0,-5)) : $vals["A$r"];
				}
			}
			$show->exec('etemplate.editor.show',$content,array(),'',array(
				'olds' => $vals,
				'**extensions**' => $this->extensions
			),'');
		}

		/*!
		@function scan_for_extensions
		@syntax scan_for_extensions( $app )
		@author ralfbecker
		@abstract search the inc-dirs of etemplate and the app whichs template is edited for extensions / custom widgets
		@discussion extensions are class-files in $app/inc/class.${name}_widget.inc.php
		@result array with name => human_name of the extensions found
		*/
		function scan_for_extensions($app='etemplate')
		{
			$extensions = array();

			$dir = @opendir(PHPGW_SERVER_ROOT.'/'.$app.'/inc');

			while ($dir && ($file = readdir($dir)))
			{
				if (ereg('class\\.([a-zA-Z0-9_]*)_widget.inc.php',$file,$regs) &&
					 ($ext = $this->etemplate->loadExtension($regs[1].'.'.$app,$this->etemplate)))
				{
					if (is_array($ext))
					{
						if (!is_array($extensions))
						{
							$extensions = $ext;
						}
						else
						{
							$extensions += $ext;
						}
					}
					else
					{
						$extensions[$regs[1]] = $ext;
					}
				}
			}
			return $extensions;
		}
	};




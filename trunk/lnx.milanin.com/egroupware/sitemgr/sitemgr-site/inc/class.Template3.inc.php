<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.Template3.inc.php,v 1.20 2004/05/06 16:01:32 alpeb Exp $ */

require_once(PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'inc' . SEP . 'class.module.inc.php');

	class Template3
	{
		/* 'yes' => halt, 'report' => report error, continue, 'no' => ignore error quietly */
		var $bo;
		var $halt_on_error = 'yes';
		var $file;
		var $template;
		var $modules;
		var $permitted_modules;
		var $sitename;
		var $draft_transformer, $edit_transformer;

		function Template3($root)
		{
			$this->set_root($root);
			$file = $this->root . SEP . 'main.tpl';
			if (file_exists($file)) $this->loadfile($file);
			$this->bo = &$GLOBALS['Common_BO']->content;
			$this->modulebo = &$GLOBALS['Common_BO']->modules;
			$this->modules = array();
		}

		/* public: setroot(pathname $root)
		 * root:   new template directory.
		 */
		function set_root($root)
		{
			if (!is_dir($root))
			{
				$this->halt("set_root: $root is not a directory.");
				return false;
			}
			$this->transformer_root = $this->root = $root;
			return true;
		}

		function loadfile($file)
		{
			$this->file = $file;
			$str = implode('', @file($this->file));
			if (empty($str))
			{
				$this->halt("loadfile: $this->file does not exist or is empty.");
				return false;
			}
			else
			{
				$this->template = $str;
			}
		}


		/***************************************************************************/
		/* public: halt(string $msg)
		 * msg:    error message to show.
		 */
		function halt($msg)
		{
			$this->last_error = $msg;

			if ($this->halt_on_error != 'no')
			{
				$this->haltmsg($msg);
			}

			if ($this->halt_on_error == 'yes')
			{
				echo('<b>Halted.</b>');
			}

			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}

		/* public, override: haltmsg($msg)
		 * msg: error message to show.
		 */
		function haltmsg($msg)
		{
			printf("<b>Template Error:</b> %s<br>\n", $msg);
		}

		function parse()
		{
			if ($GLOBALS['sitemgr_info']['mode'] == 'Draft')
			{
				$transformerfile = $this->root . SEP . 'draft_transform.inc.php';
				if (!file_exists($transformerfile))
				{
					$transformerfile = $this->root . '/../default/draft_transform.inc.php';
				}
				if (file_exists($transformerfile))
				{
					include_once($transformerfile);
					if (class_exists('draft_transform'))
					{
						$this->draft_transformer = new draft_transform();
					}
				}
			}
			elseif ($GLOBALS['sitemgr_info']['mode'] == 'Edit')
			{
				$transformerfile = $this->root . SEP . 'edit_transform.inc.php';
				if (!file_exists($transformerfile))
				{
					$transformerfile = $this->root . '/../default/edit_transform.inc.php';
				}
				if (file_exists($transformerfile))
				{
					include_once($transformerfile);
					if (class_exists('edit_transform'))
					{
						$this->edit_transformer = new edit_transform();
					}
				}
			}
			//get block content for contentareas
			$str = preg_replace_callback(
				"/\{contentarea:([^{ ]+)\}/",
				array($this,'process_blocks'),
				$this->template);
			$this->permitted_modules = array_keys($this->modulebo->getcascadingmodulepermissions('__PAGE__',$page->cat_id));
			//process module calls hardcoded into template of form {modulename?arguments}
			$str = preg_replace_callback(
				"/\{([[:alnum:]_-]+)\?([^{ ]+)?\}/",
				array($this,'exec_module'),
				$str);
			//{?page_id=4} is a shortcut for calling the link functions
			$str = preg_replace_callback(
				"/\{\?((sitemgr|phpgw):)?([^{ ]*)\}/",
				array($this,'make_link'),
				$str);
			$str = preg_replace_callback(
				"/\{lang_([^{]+)\}/",
				array($this,'lang'),
				$str);
			//all template variables that survive look for metainformation
			return preg_replace_callback(
				"/\{([^{ ]+)\}/",
				array($this,'get_meta'),
				$str);
		}

		function process_blocks($vars)
		{
			global $page;
			global $objbo;

			$areaname = is_array($vars) ? $vars[1] : $vars;
			$this->permitted_modules = array_keys($this->modulebo->getcascadingmodulepermissions($areaname,$page->cat_id));

			$transformername = $areaname . '_bt';

			$transformerfile = $this->transformer_root . SEP . $transformername . '.inc.php';
			if (file_exists($transformerfile))
			{
				include_once($transformerfile);
				if (class_exists($transformername))
				{
					$transformer = new $transformername;
				}
			}
			//compatibility with former sideblocks template
			elseif (($areaname == "left" || $areaname == "right") && file_exists($this->root . SEP . 'sideblock.tpl'))
			{
				$t = Createobject('phpgwapi.Template');
				$t->set_root($this->root);
				$t->set_file('SideBlock','sideblock.tpl');
				$transformer = new sideblock_transform($t);
			}
			$content = '';

			$blocks = $this->bo->getvisibleblockdefsforarea($areaname,$page->cat_id,$page->id,$objbo->is_admin(),$objbo->isuser);
			// if we are in the center area, we append special blocks
			if ($areaname == "center" && $page->block)
			{
				array_unshift($blocks,$page->block);
			}
			if ($blocks)
			{
				while (list(,$block) = each($blocks))
				{
					if (in_array($block->module_id,$this->permitted_modules))
					{
						//we maintain an array of modules we have already used, so we do not 
						//have to create them anew. Since they are copied, before the transformer
						//is added, we do not have to worry about transformers staying around 
						//on the transformer chain
						$moduleobject = $this->getmodule($block->module_name);

						if ($block->id)
						{
							$block->title = $this->getblocktitlewrapper($block->id);
							$block->arguments = $moduleobject->i18n ? 
								$this->getversionwrapper($block->version) : $this->bo->getversion($block->version);
						}
						
						$moduleobject->set_block($block,True);

						if (($block->state == SITEMGR_STATE_PREPUBLISH) && is_object($this->draft_transformer))
						{

							$moduleobject->add_transformer($this->draft_transformer);
						}
						if (isset($transformer))
						{
							$moduleobject->add_transformer($transformer);
						}
						if ($GLOBALS['sitemgr_info']['mode'] == 'Edit' &&
							$block->id &&
							$GLOBALS['Common_BO']->acl->can_write_category($block->cat_id) &&
							is_object($this->edit_transformer))
						{
							$moduleobject->add_transformer($this->edit_transformer);
						}

						$output = $moduleobject->get_output();
						//process module calls embedded into output
						$content .= preg_replace_callback(
							"/\{([[:alnum:]_-]*)\.([[:alnum:]_-]*)(\?([^{ ]+))?\}/",
							array($this,'exec_module'),
							$output);
					}
					else
					{
						$content .= lang('Module %1 is not permitted in this context!',$block->module_name);
					}
				}
			}
			if ($GLOBALS['sitemgr_info']['mode'] == 'Edit' &&
				is_object($this->edit_transformer) &&
				method_exists($this->edit_transformer,'area_transform'))
			{
				return $this->edit_transformer->area_transform($areaname,$content,$page);
			}
			return $content;
		}

		function exec_module($vars)
		{
			global $page;
			list(,$modulename,$query)= $vars;
			$moduleid = $this->modulebo->getmoduleid($modulename);
			if (in_array($moduleid,$this->permitted_modules))
			{
				$moduleobject = $this->getmodule($modulename);
				if ($moduleobject)
				{
					parse_str($query,$arguments);
					//we set up a block object so that the module object can retrieve the right arguments and properties
					$block = CreateObject('sitemgr.Block_SO',True);
					$block->module_id = 0;
					$block->area = '__PAGE__';
					$block->cat_id = $page->cat_id;
					$block->module_name = $modulename;
					$block->arguments = $arguments;
					$moduleobject->set_block($block,True);
					return $moduleobject->get_output();
				}
			}
			else
			{
				return lang('Module %1 is not permitted in this context!',$modulename);
			}
		}

		function make_link($vars)
		{
			switch($vars[2])
			{
				case 'phpgw':
					$params=explode(',',$vars[3]);
					switch(count($params))
					{
						case 0:
							return '';
						case 1:
							return phpgw_link($params[0]);
						case 2:
							return phpgw_link($params[0],$params[1]);
						default:
							return $vars[0];
					}
				//sitemgr link
				default:
						return sitemgr_link($vars[3]);
			}
		}

		function lang($vars)
		{
			return lang(str_replace('_',' ',$vars[1]));
		}

		function get_meta($vars)
		{
			global $page,$objbo;

			switch ($var = is_array($vars) ? $vars[1] : $vars)
			{
				case 'title':
				case 'page_title':
					return $page->title;
				case 'editicons':
					// add icons to edit the page/cat, if we are in edit-mode
					if ($GLOBALS['sitemgr_info']['mode'] == 'Edit')
					{
						if ($page->id)
						{
							return $objbo->getEditIconsPage($page->id,$page->cat_id).
								// if the page is an index-page for a cat, add the cat-icons too
								($GLOBALS['category_id'] && is_object($GLOBALS['cat']) ?
								' - '.lang('Category').' '.$GLOBALS['cat']->name.' '.$objbo->getEditIconsCat($page->cat_id) : '');
						}
						elseif ($page->cat_id && $page->cat_id != CURRENT_SITE_ID)
						{
							return $objbo->getEditIconsCat($page->cat_id);
						}
						else
						{
							return $objbo->getEditIconsTop();
						}
					}
					return '';
				case 'subtitle':
				case 'page_subtitle':
					return $page->subtitle;
				case 'sitename':
				case 'site_name':
					return $GLOBALS['sitemgr_info']['site_name_' . $GLOBALS['phpgw_info']['user']['preferences']['common']['lang']];
				case 'sitedesc':
				case 'site_desc':
					return $GLOBALS['sitemgr_info']['site_desc_' . $GLOBALS['phpgw_info']['user']['preferences']['common']['lang']];
// 				case 'footer':
// 				case 'site_footer':
// 					return $GLOBALS['Common_BO']->headerfooter->getsitefooter($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']);
// 				case 'header':
// 				case 'site_header':
// 					return $GLOBALS['Common_BO']->headerfooter->getsiteheader($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']);
				case 'user':
					return $GLOBALS['phpgw_info']['user']['account_lid'];
				case 'charset':
					return is_object($GLOBALS['phpgw']->translation) ? $GLOBALS['phpgw']->translation->charset() : 'iso-8859-1';
				case 'lang':
					return $GLOBALS['sitemgr_info']['userlang'];
				case 'year':
					return date('Y');	// nice to keep all copyrights up to date
				case 'editmode_styles':
					return $GLOBALS['sitemgr_info']['mode'] == 'Edit' ?
						'<link href="templates/default/style/editmode.css" type="text/css" rel="StyleSheet" />' : '';
				default:
					return '{'.$var.'}';	// leave it unchanged, happens eg. with js-code
			}
		}

		function getmodule($modulename)
		{
			if (!in_array($modulename,array_keys($this->modules)))
			{
				$moduleobject = $this->modulebo->createmodule($modulename);
				$this->modules[$modulename] = $moduleobject;
			}
			else
			{
				$moduleobject = $this->modules[$modulename];
			}
			return $moduleobject;
		}

		function getblocktitlewrapper($block_id)
		{
			$availablelangsforblocktitle = $this->bo->getlangarrayforblocktitle($block_id);
			if (in_array($GLOBALS['sitemgr_info']['userlang'],$availablelangsforblocktitle))
			{
				return $this->bo->getlangblocktitle($block_id,$GLOBALS['sitemgr_info']['userlang']);
			}
			else
			{
				// try useing the availible translation, if we have an english title
				if (in_array('en',$availablelangsforblocktitle))
				{
					$en_title = $this->bo->getlangblocktitle($block_id,'en');
					$title = lang($en_title);
					if ($title != $en_title.'*')
					{
						return $title;
					}
				}
				foreach ($GLOBALS['sitemgr_info']['sitelanguages'] as $lang)
				{
					if (in_array($lang,$availablelangsforblocktitle))
					{
						return $this->bo->getlangblocktitle($block_id,$lang);
					}
				}
			}
		}

		function getversionwrapper($version_id)
		{
			$availablelangsforversion = $this->bo->getlangarrayforversion($version_id);
			if (in_array($GLOBALS['sitemgr_info']['userlang'],$availablelangsforversion))
			{
				return $this->bo->getversion($version_id,$GLOBALS['sitemgr_info']['userlang']);
			}
			else
			{
				foreach ($GLOBALS['sitemgr_info']['sitelanguages'] as $lang)
				{
					if (in_array($lang,$availablelangsforversion))
					{
						return $this->bo->getversion($version_id,$lang);
					}
				}
			}
		}
	}

	class sideblock_transform
	{
		function sideblock_transform(&$template)
		{
			$this->template = $template;
		}


		function apply_transform($title,$content)
		{
			$this->template->set_var(array(
				'block_title' => $title,
				'block_content' => $content
			));
			return $this->template->parse('out','SideBlock');
		}
	}

<?php
	/**************************************************************************\
	* eGroupWare Wiki - UserInterface                                       *
	* http://www.egroupware.org                                                *
	* -------------------------------------------------                        *
	* Copyright (C) 2004 RalfBecker@outdoor-training.de                        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.uiwiki.inc.php,v 1.4.2.2 2004/09/19 21:25:50 ralfbecker Exp $ */

	require_once(PHPGW_INCLUDE_ROOT.'/wiki/inc/class.bowiki.inc.php');

	class uiwiki extends bowiki
	{
		var $public_functions = array(
			'edit' => True,
		);

		function uiwiki()
		{
			$this->bowiki($_GET['wiki_id']);

			$this->tpl = CreateObject('etemplate.etemplate');

			// should pages with wiki-syntax be converted to html automaticaly
			switch($this->AutoconvertPages)
			{
				case 'always':
				case 'never':
				case 'onrequest':
					$this->auto_convert = $this->AutoconvertPages == 'always';
					break;
				case 'auto':
				default:
					$this->auto_convert = $this->tpl->html->htmlarea_availible();
			}
			if (get_magic_quotes_gpc())
			{
				foreach($_GET as $name => $val)
				{
					$_GET[$name] = stripslashes($val);
				}
			}
		}

		function edit($content='')
		{
			//echo "<p>uiwiki::edit() content=<pre>".print_r($content,True)."</pre>\n";
			if (!is_array($content))
			{
				$content['name'] = $content ? $content : $_GET['page'];
				$content['lang'] = $_GET['lang'];
				$content['version'] = $_GET['version'];
				$start = True;
			}
			list($action) = @each($content['action']);
			if (empty($content['name']))
			{
				$this->tpl->location('/wiki/');
			}
			$pg = $this->page($content['name'],$content['lang']);
			if ($content['version'] && $action != 'load')
			{
				$pg->version = $content['version'];
			}
			if ($pg->read() === False)	// new entry
			{
				$pg->lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}

			// acl checks
			if (!$pg->acl_check())	// no edit-rights
			{
				$GLOBALS['phpgw']->redirect($this->ViewURL($content));
			}
			elseif (!$pg->acl_check(True))	// no read-rights
			{
				$this->tpl->location('/wiki/');
			}
			if ($start || $action == 'load')
			{
				$content = $pg->as_array();
				$content['is_html'] = substr($content['text'],0,7) == "<html>\n" && substr($content['text'],-8) == "</html>\n";
			}
			if ($start || $action == 'load' || $action == 'convert')
			{
				if ($content['is_html'])
				{
					$content['text'] = substr($content['text'],7,-8);
				}
				elseif ($this->auto_convert || $action == 'convert')
				{
					$content['text'] = $this->parse($pg,'Convert');
					$content['is_html'] = True;
				}
			}

			if ($content['is_html'])
			{
				// some tavi stuff need to be at the line-end
				$content['text'] = preg_replace(array('/(.+)(<br \\/>)/i',"/(<br \\/>\n?)+$/i"),array("\\1\n\\2",''),$content['text']);

				$content['preview'] = $this->parse("<html>\n".$content['text']."\n</html>\n",'Parse',$content['name']);
			}
			else
			{
				$content['preview'] = $this->parse($content['text'],'Parse',$content['name']);
			}
			if (empty($content['title'])) $content['title'] = $content['name'];
			//echo "<p>uiwiki::edit() action='$action', content=<pre>".print_r($content,True)."</pre>\n";

			if ($action)
			{
				switch($action)
				{
					case 'delete':
						$content['text'] = '';
						$content['is_html'] = False;	// else page is not realy empty
					case 'rename':
					case 'save':
					case 'apply':
						// do save
						if ($content['is_html'])
						{
							$content['text'] = "<html>\n".$content['text']."\n</html>\n";
						}
						if ($action == 'rename')
						{
							$this->rename($content,$content['old_name'],$content['old_lang']);
						}
						else
						{
							$this->write($content);
						}
						if ($content['is_html'])
						{
							$content['text'] = substr($content['text'],7,-8);
						}
				}
				switch($action)
				{
					case 'delete':
						$content = '';	// load the Homepage
					case 'save':
					case 'cancel':
						// return to view
						$GLOBALS['phpgw']->redirect($this->ViewURL($content));
						break;
				}
			}
			$acl_values = array(
				WIKI_ACL_ALL =>   lang('everyone'),
				WIKI_ACL_USER =>  lang('users'),
				WIKI_ACL_ADMIN => lang('admins'),
			);
			$this->tpl->read('wiki.edit');

			if ($content['is_html'] || $this->AutoconvertPages == 'never' || !$this->tpl->html->htmlarea_availible())
			{
				$this->tpl->disable_cells('action[convert]');
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['wiki']['title'] . ' - ' .
				lang('edit') . ' ' . $content['name'] .
				($content['lang'] && $content['lang'] != $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] ?
					':' . $content['lang'] : '').
				($content['name'] != $content['title'] ? ' - ' . $content['title'] : '');
			$this->tpl->exec('wiki.uiwiki.edit',$content,array(
				'lang'     => array('' => lang('not set')) + $GLOBALS['phpgw']->translation->get_installed_langs(),
				'readable' => $acl_values,
				'writable' => $acl_values,
			),False,array(
				'wiki_id'  => $content['wiki_id'],
				'old_name' => isset($content['old_name']) ? $content['old_name'] : $content['name'],
				'old_lang' => isset($content['old_lang']) ? $content['old_lang'] : $content['lang'],
				'version'  => $content['version'],
				'is_html'  => $content['is_html'],
			));
		}
	}

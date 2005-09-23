<?php
	/**************************************************************************\
	* eGroupWare - Online User manual                                          *
	* http://www.eGroupWare.org                                                *
	* Written and (c) by RalfBecker@outdoor-training.de                        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.uimanualadmin.inc.php,v 1.1 2004/06/29 17:44:41 ralfbecker Exp $ */

	include_once(PHPGW_INCLUDE_ROOT.'/wiki/inc/class.xmlwiki.inc.php');

	class uimanualadmin extends xmlwiki
	{
		var $public_functions = array(
			'import' =>True,
		);

		function uimanualadmin()
		{
			$this->config = CreateObject('phpgwapi.config','manual');
			$this->config->read_repository();
			
			foreach(array(
				'manual_wiki_id' => 1,
				'manual_update_url' => 'http://egroupware.org/egroupware/wiki/index.php?page=Manual&action=xml',
			) as $name => $default)
			{
				if (!isset($this->config->config_data[$name]))
				{
					$this->config->config_data[$name] = $default;
					$need_save = True;
				}
			}
			if ($need_save)
			{
				$this->config->save_repository();
			}			
			$this->wiki_id = (int) $this->config->config_data['manual_wiki_id'];
			$this->xmlwiki($this->wiki_id);	// call the constructor of the class we extend
		}

		function import()
		{
			$url = $this->config->config_data['manual_update_url'];
			$from = explode('/',$url);
			$from = count($from) > 2 ? $from[2] : $url;

			$langs = implode(',',array_keys($GLOBALS['phpgw']->translation->get_installed_langs()));
			if ($langs)
			{
				$url .= (strstr($url,'?') === False ? '?' : '&').'lang='.$langs;
			}
			// only do an incremental update if the langs are unchanged and we already did an update
			if ($langs == $this->config->config_data['manual_langs'] && $this->config->config_data['manual_updated'])
			{
				$url .= (strstr($url,'?') === False ? '?' : '&').'modified='.(int) $this->config->config_data['manual_updated'];
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('manual').' - '.lang('download');
			$GLOBALS['phpgw']->common->phpgw_header();
			parse_navbar();
			echo str_pad('<h3>'.lang('Starting import from %1, this might take several minutes (specialy if you start it the first time) ...',
				'<a href="'.$url.'" target="_blank">'.$from.'</a>')."</h3>\n",4096);	// dirty hack to flushes the buffer;
			@set_time_limit(0);

			$status = xmlwiki::import($url,True);
			
			$this->config->config_data['manual_updated'] = $status['meta']['exported'];
			$this->config->config_data['manual_langs'] = $langs;
			$this->config->save_repository();

			echo '<h3>'.lang('%1 manual page(s) added or updated',count($status['imported']))."</h3>\n";

			$GLOBALS['phpgw']->common->phpgw_footer();
		}

		function menu($args)
		{
			display_section('manual','manual',array(
//				'Site Configuration' => $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiconfig.index&appname=manual'),
				'install or update the manual-pages' => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'manual.uimanualadmin.import')),
			));
		}
	}

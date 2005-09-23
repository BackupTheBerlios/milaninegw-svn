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

	/* $Id: class.uimanual.inc.php,v 1.5.2.3 2004/11/19 12:58:26 ralfbecker Exp $ */

	include_once(PHPGW_INCLUDE_ROOT.'/wiki/inc/class.bowiki.inc.php');

	class uimanual extends bowiki
	{
		var $public_functions = array(
			'view' => True,
		);

		function uimanual()
		{
			$this->config = CreateObject('phpgwapi.config','manual');
			$this->config->read_repository();
			$this->wiki_id = (int) (isset($this->config->config_data['manual_wiki_id']) ? $this->config->config_data['manual_wiki_id'] : 1);

			$this->bowiki($this->wiki_id);

			$GLOBALS['phpgw']->common->phpgw_header();
		}

		function viewURL($page, $lang='', $version='', $full = '')
		{
			$args = array(
				'menuaction' => 'manual.uimanual.view',
			);
			if ($lang || @$page['lang'])
			{
				$args['lang'] = $lang ? $lang : @$page['lang'];
				if ($args['lang'] == $GLOBALS['phpgw_info']['user']['prefereces']['common']['lang']) unset($args['lang']);
			}
			if ($version)
			{
				$args['version'] = $version;
			}
			if ($full)
			{
				$args['full'] = 1;
			}
			// the page-parameter has to be the last one, as the old wiki code only calls it once with empty page and appends the pages later
			return $GLOBALS['phpgw']->link('/index.php',$args).'&page='.urlencode(is_array($page) ? $page['name'] : $page);
		}
		
		function editURL()
		{
			return False;
		}

		function view()
		{
			// let the (existing) window pop up
			echo "<script language=\"JavaScript\">\n\twindow.focus();\n</script>\n";
			if (!isset($_GET['page']))
			{
				// use the referer
				$referer = !isset($_SERVER['HTTP_REFERER']) ? $_GET['referer'] : $_SERVER['HTTP_REFERER'];
				list($referer,$query) = explode('?',$referer);
				parse_str($query,$query);
				// echo "<p>_GET[referer]='$_GET[referer]', referer='$referer', query=".print_r($query,True)."</p>\n";
				
				if (isset($query['menuaction']) && $query['menuaction'])
				{
					list($app,$class,$function) = explode('.',$query['menuaction']);
					// for acl-preferences use the app-name from the query and acl as function
					if ($app == 'preferences' && $class == 'uiaclprefs')
					{
						$app = $query['acl_app'] ? $query['acl_app'] : $_GET['acl_app'];
						$function = 'acl';
					}
					elseif ($app == 'admin' && $class == 'uiconfig')
					{
						$app = $query['appname'] ? $query['appname'] : $_GET['appname'];
					}
					$pages[] = 'Manual'.ucfirst($app).ucfirst($class).ucfirst($function);
					$pages[] = 'Manual'.ucfirst($app).ucfirst($function);
					$pages[] = 'Manual'.ucfirst($app).ucfirst($class);
				}
				else
				{
					if ($GLOBALS['phpgw_info']['server']['webserver_url'] != '/')
					{
						list(,$referer) = explode($GLOBALS['phpgw_info']['server']['webserver_url'],$referer,2);
					}
					else
					{
						$referer = parse_url($referer);	// remove the protocol + domain
						$referer = $referer['path'];
					}
					$parts = explode('/',$referer);
					$file = str_replace('.php','',array_pop($parts));
					if (empty($file)) $file = 'index';
					$app  = array_pop($parts);
					if (is_numeric($app)) $app  = array_pop($parts);	// for fudforum
					// for preferences use the app-name from the query
					if ($app == 'preferences' && $file == 'preferences')
					{
						$app = $query['appname'] ? $query['appname'] : $_GET['appname'];
					}
					$pages[] = 'Manual'.ucfirst($app).ucfirst($file);
				}
				$pages[] = 'Manual'.ucfirst($app);
			}
			else
			{
				$pages[] = $_GET['page'];
			}
			echo '<div id="divMain">'."\n";

			// show the first page-hit
			$found = False;
			foreach($pages as $name)
			{
				$page = $this->page($name);
				if ($found = $page->read() !== False)
				{
					break;
				}
			}
			if (!$found)
			{
				echo '<h3>'.lang("Page(s) %1 not found !!!",implode(', ',$pages))."</h3>\n";
				// show the Manual startpage
				$page = 'Manual';
			}
			echo $this->get($page,'',$this->wiki_id,$this->viewURL(''));

			echo "\n</div>\n";
		}
	}
?>

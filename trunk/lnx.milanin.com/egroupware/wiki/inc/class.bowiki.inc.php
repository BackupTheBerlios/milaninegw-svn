<?php
	/**************************************************************************\
	* eGroupWare Wiki - Business Objects                                       *
	* http://www.egroupware.org                                                *
	* -------------------------------------------------                        *
	* Copyright (C) 2004 RalfBecker@outdoor-training.de                        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.bowiki.inc.php,v 1.11.2.1 2004/07/19 13:15:39 ralfbecker Exp $ */

	// old global stuff, is still need for now, but hopefully will go away
	global $ParseEngine,$DiffEngine,$DisplayEngine,$ConvertEngine,$SaveMacroEngine;;
	global $UpperPtn,$LowerPtn,$AlphaPtn,$LinkPtn,$UrlPtn,$InterwikiPtn,$MaxNesting,$MaxHeading;
	global $EditBase,$ViewBase;

	require_once(PHPGW_INCLUDE_ROOT.'/wiki/lib/defaults.php');
	if ($GLOBALS['phpgw']->translation->charset() == 'iso-8859-1')	// allow all iso-8859-1 extra-chars
	{
		$UpperPtn = "[A-Z\xc0-\xde]";
		$LowerPtn = "[a-z\xdf-\xff]";
		$AlphaPtn = "[A-Za-z\xc0-\xff]";
		$LinkPtn = $UpperPtn . $AlphaPtn . '*' . $LowerPtn . '+' .
			$UpperPtn . $AlphaPtn . '*(\\/' . $UpperPtn . $AlphaPtn . '*)?';
	}

	require_once(PHPGW_INCLUDE_ROOT.'/wiki/lib/url.php');
	require_once(PHPGW_INCLUDE_ROOT.'/wiki/lib/messages.php');

	global $pagestore,$FlgChr,$Entity;
	$FlgChr = chr(255);                     // Flag character for parse engine.
	$Entity = array();                      // Global parser entity list.

	require_once(PHPGW_INCLUDE_ROOT.'/wiki/parse/transforms.php');
	require_once(PHPGW_INCLUDE_ROOT.'/wiki/parse/main.php');
	require_once(PHPGW_INCLUDE_ROOT.'/wiki/parse/macros.php');
	require_once(PHPGW_INCLUDE_ROOT.'/wiki/parse/html.php');
	require_once(PHPGW_INCLUDE_ROOT.'/wiki/parse/save.php');

	require_once(PHPGW_INCLUDE_ROOT.'/wiki/lib/category.php');

	require_once(PHPGW_INCLUDE_ROOT.'/wiki/inc/class.sowiki.inc.php');

	class bowiki extends sowiki
	{
		function bowiki($wiki_id=0)
		{
			$this->sowiki($wiki_id);

			global $pagestore;
			if (!is_object($pagestore))
			{
				$pagestore = new sowiki($wiki_id);
			}
			global $Admin,$HomePage,$InterWikiPrefix,$EnableFreeLinks,$EnableWikiLinks;
			$c = CreateObject('phpgwapi.config','wiki');
			$c->read_repository();
			$config = $c->config_data;
			$Admin = $config['emailadmin'];
			$HomePage = (isset($config['wikihome'])?$config['wikihome']:'eGroupWare');
			$InterWikiPrefix = (isset($config['InterWikiPrefix'])?$config['InterWikiPrefix']:'EGroupWare');
			$EnableFreeLinks = (isset($config['Enable_Free_Links'])?$config['Enable_Free_Links']:1);
			$EnableWikiLinks = (isset($config['Enable_Wiki_Links'])?$config['Enable_Wiki_Links']:1);

			global $Charset,$UserName;
			$Charset = $GLOBALS['phpgw']->translation->charset();
			$UserName = $GLOBALS['phpgw_info']['user']['account_lid'];

			$this->AutoconvertPages = $config['AutoconvertPages'];
		}

		function get($page,$lang='',$wiki_id=0,$view_base='')
		{
			if (!is_object($page))
			{
				$page = $this->page($page,$lang,$wiki_id);
				$page->read();
			}
			global $ViewBase;
			if ($view_base) $ViewBase = $view_base;

			return $this->parse($page);
		}

		function parse($page,$engine='Parse',$name='')
		{
			if (is_object($page))
			{
				$text = $page->text;
				$name = $name ? $name : $page->name;
			}
			elseif (is_array($page))
			{
				$text = $page['text'];
				$name = $name ? $name : $page['name'];
			}
			else
			{
				$text = $page;
			}
			switch($engine)
			{
				case 'Convert': case 'convert':
					$engine = $GLOBALS['ConvertEngine'];
					break;
				case 'Parse': case 'parse':
				default:
					$engine = $GLOBALS['ParseEngine'];
					break;
				case 'Diff': case 'diff':
					$engine = $GLOBALS['DiffEngine'];
					break;
				case 'Save': case 'save':
					$engine = $GLOBALS['SaveMacroEngine'];
					break;
			}
			//echo "<p>parseText(\$text,\$engine,'$name'); \$engine=<pre>\n".print_r($engine,True)."</pre>";
			return parseText($text,$engine,$name);
		}

		function write($values,$set_host_user=True)
		{
			//echo "<p>bowiki::write(".print_r($values,True).")</p>";
			$page = $this->page($values['name'],$values['lang']);

			if ($page->read() !== False)	// !== as an empty page would return '' == False
			{
				$page->version++;
			}
			else
			{
				$page->version = 1;
			}
			$needs_write = False;
			foreach(array('text','title','comment','readable','writable') as $name)
			{
				$needs_write = $needs_write || $page->$name != $values[$name];
				$page->$name = $values[$name];
			}
			if (!$needs_write) return False;	// no change => dont write it back

			$page->hostname = $set_host_user ? gethostbyaddr($_SERVER['REMOTE_ADDR']) : $values['hostname'];
			$page->username = $set_host_user ? $GLOBALS['phpgw_info']['user']['account_lid'] : $values['username'];

			$page->write();
			$GLOBALS['page'] = $page->as_array();	// we need this to track lang for new_link, sister_wiki, ...

			if(!empty($values['category']))		// Editor asked page to be added to a category or categories.
			{
				add_to_category($page, $values['category']);
			}
			// delete the links of the page
			$this->clear_link($values);
			// Process save macros (eg. store the links or define interwiki entries).
			$this->parse($page,'Save');

			return True;
		}

		function rename_links($old_name,$name,$title,$text)
		{
			global $LinkPtn;
			//echo "<p>rename_links('$old_name','$name','$title'), preg_match('/$LinkPtn/',\$name)=".(preg_match('/'.$LinkPtn.'/',$name)?'True':'False')."</p>";

			$is_wiki_link = preg_match('/'.$LinkPtn.'/',$name);

			// construct the new link
			$new_link = $name != $title ? '(('.$name.'|'.$title.'))' : ($is_wiki_link ? $name : '(('.$name.'))');

			$to_replace = array(
				'/\(\('.preg_quote($old_name).'\ ?\| ?[^)]+\)\)/i',	// free link with given appearence
				'/\(\('.preg_quote($old_name).'\)\)/i',				// free link
			);
			if (preg_match('/'.$LinkPtn.'/',$old_name))		// only replace the plain old_name, if it is a wiki link
			{
				$to_replace[] = '/(?=\b)'.preg_quote($old_name).'(?=\b )/i';	// wiki link
			}
			return preg_replace($to_replace,$new_link,$text);
		}

		function rename(&$values,$old_name,$old_lang)
		{
			@set_time_limit(0);
			//echo "<p>bowiki::rename '$old_name:$old_lang' to '$values[name]:$values[lang]'</p>";
			$page = $this->page($old_name,$old_lang);

			if ($page->read() === False || !$page->rename($values['name'],$values['lang']))
			{
				//echo "<p>\$page->rename('$values[name]','$values[lang]') == False</p>";
				return False;
			}
			// change all links to old_name with the new link
			foreach($this->get_links($old_name) as $page => $langs)
			{
				foreach($langs as $lang => $link)
				{
					$to_replace = $this->page($page,$lang);
					if ($to_replace->read() !== False)
					{
						$to_replace = $to_replace->as_array();
						$to_replace['text'] = $this->rename_links($old_name,$values['name'],$values['title'],$was=$to_replace['text']);
						$to_replace['comment'] = $old_name . ($old_lang && $old_lang != $values['lang'] ? ':'.$old_lang : '') . ' --> ' .
							$values['name'] . ($values['lang']  && $old_lang != $values['lang'] ? ':'.$values['lang'] : '');
						//echo "<p><b>$to_replace[name]</b>: $to_replace[comment]<br>\n<b>From:</b><br>\n$was<br>\n<b>To</b><br>\n$to_replace[text]</p>\n";
						$this->write($to_replace);
					}
				}
			}
			// also rename links in our own content
			$values['text'] = $this->rename_links($old_name,$values['name'],$values['title'],$values['text']);

			foreach(array('text','title','comment','readable','writable') as $name)
			{
				if (isset($values[$name]) && $values[$name] != $page->$name)
				{
					// other changes, write them
					return $this->write($values);
				}
			}
			// delete the links of the old page
			$this->clear_link(array('name' => $old_name,'lang' => $old_lang));

			$GLOBALS['page'] = $page->as_array();	// we need this to track lang for new_link, sister_wiki, ...
			if(!empty($values['category']))		// Editor asked page to be added to a category or categories.
			{
				add_to_category($page, $values['category']);
			}
			// Process save macros (eg. store the links or define interwiki entries).
			$this->parse($page,'Save');
		}

		function editURL($page, $lang='',$version = '')
		{
			$args = array(
				'menuaction' => 'wiki.uiwiki.edit',
				'page' => is_array($page) ? $page['name'] : $page
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
			return $GLOBALS['phpgw']->link('/index.php',$args);
		}

		function viewURL($page, $lang='', $version='', $full = '')
		{
			$args = array(
				//'menuaction' => 'wiki.uiwiki.view',
				'page' => is_array($page) ? $page['name'] : $page
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
			return $GLOBALS['phpgw']->link('/wiki/index.php',$args);
		}
	}

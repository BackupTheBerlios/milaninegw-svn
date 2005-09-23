<?php
	/**************************************************************************\
	* eGroupWare - wiki - XML Import & Export                                  *
	* http://www.egroupware.org                                                *
	* Written and (c) by Ralf Becker <RalfBecker@outdoor-training.de>          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.xmlwiki.inc.php,v 1.3.2.2 2004/08/17 18:09:38 ralfbecker Exp $ */

	if (!function_exists('var2xml'))
	{
		if (file_exists(PHPGW_API_INC.'class.xmltool.inc.php'))
		{
			include_once(PHPGW_API_INC.'class.xmltool.inc.php');
		}
		else
		{
			include_once(PHPGW_INCLUDE_ROOT.'/etemplate/inc/class.xmltool.inc.php');
		}
	}

	include_once(PHPGW_INCLUDE_ROOT.'/wiki/inc/class.bowiki.inc.php');

	class xmlwiki extends bowiki
	{
		var $public_functions = array(
			'export' => True,
		);

		function xmlwiki($wiki_id=0)
		{
			$this->bowiki($wiki_id);	// call the constructor of the extended class
		}

		function export($name='',$lang='',$modified=0)
		{
			if (!$name) $name = $_GET['page'];
			if (!$lang) $lang = $_GET['lang'];
			if (!is_array($lang))
			{
				$lang = $lang ? explode(',',$lang) : False;
			}
			if (!$modified) $modified = (int) $_GET['modified'];

			header('Content-Type: text/xml; charset=utf-8');

			$xml_doc = new xmldoc();
			$xml_doc->add_comment('$'.'Id$');	// to be able to comit the file
			$xml_doc->add_comment("eGroupWare wiki-pages matching '$name%'".
				($lang ? " and lang in(".implode(',',$lang).')':'').
				($modified ? " modified since ".date('Y-m-d H:m:i',$modified):'').
				", exported ".date('Y-m-d H:m:i',$exported=time())." from $_SERVER[HTTP_HOST]");

			$xml_wiki = new xmlnode('wiki');

			foreach($this->find($name.'%','name') as $page)
			{
				if ($lang && !in_array($page['lang'],$lang)) 
				{
					//echo "continue as page[lang]=$page[lang] not in ".print_r($lang,True)."<br>\n";
					continue;	// wrong language or not modified since $modified
				}
				$page = $this->page($page);	// read the complete page
				$page->read();
				$page = $page->as_array();
				unset($page['wiki_id']);		// we dont export the wiki-id

				if ($modified && $modified > $page['time']) 
				{
					//echo "continue as page[time]=$page[time] < $modified<br>\n";
					continue;	// not modified since $modified
				}

				$page = $GLOBALS['phpgw']->translation->convert($page,$GLOBALS['phpgw']->translation->charset(),'utf-8');

				$xml_page = new xmlnode('page');
				foreach($page as $attr => $val)
				{
					if ($attr != 'text')
					{
						$xml_page->set_attribute($attr,$val);
					}
					else
					{
						$xml_page->set_value($val);
					}
				}
				$xml_wiki->add_node($xml_page);
			}
			$xml_wiki->set_attribute('exported',$exported);
			if ($lang)
			{
				$xml_wiki->set_attribute('languages',implode(',',$lang));
			}
			if ($name)
			{
				$xml_wiki->set_attribute('matching',$name.'%');
			}
			if ($modified)
			{
				$xml_wiki->set_attribute('modified',$modified);
			}
			$xml_doc->add_root($xml_wiki);
			$xml = $xml_doc->export_xml();

			//if ($this->debug)
			{
				//echo "<pre>\n" . htmlentities($xml) . "\n</pre>\n";
				echo $xml;
			}
			return $xml;
		}

		function import($url,$debug_messages=False)
		{
			if (substr($url,0,4) == 'http')
			{
				// use our network class, as it deals with proxies and the proxy-config in admin->configuration
				$network = CreateObject('phpgwapi.network');
				$xmldata = $network->gethttpsocketfile($url);
				$xmldata = strstr(implode('',$xmldata),'<?xml');	// discard everything before the start of the xml-file
			}
			else
			{
				if (function_exists('file_get_contents'))
				{
					$xmldata = file_get_contents($url);
				}
				elseif (($xmldata = file($url)) !== False)
				{
					$xmldata = implode('',$xmldata);
				}
			}
			//echo '<pre>'.htmlspecialchars($xmldata)."</pre>\n";
			if (!$xmldata)
			{
				return False;
			}
			$parser = xml_parser_create();
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE,   0);	// need to be off, else it eats our newlines
			xml_parse_into_struct($parser, $xmldata, $vals);
			xml_parser_free($parser);

			$imported = array();
			foreach($vals as $val)
			{
				if ($val['tag'] == 'wiki')	// wiki meta-data: eg. exported=Y-m-d h:m:i or export data
				{
					if ($val['type'] == 'open' || $val['type'] == 'complete')
					{
						$meta = $val['attributes'];
					} 
					continue;
				}
				switch ($val['type'])
				{
					case 'open':
						$wiki_page = $val['attributes'];
						break;
					case 'complete':
						$wiki_page = $val['attributes'];
						// fall through
					case 'cdata':
						$wiki_page['text'] = trim($val['value']);
						$wiki_page = $GLOBALS['phpgw']->translation->convert($wiki_page,'utf-8');
						if ($this->write($wiki_page,False))
						{
							if ($debug_messages) 
							{
								echo str_pad("<b>$wiki_page[name]:$wiki_page[lang]: $wiki_page[title]</b><pre>$wiki_page[text]</pre>\n",4096);
							}
							$imported[] = $wiki_page['name'].':'.$wiki_page['lang'];
						}
						break;
					case 'closed':
						break;
				}
			}
			return array(
				'meta' => $meta,
				'imported' => $imported,
			);
		}
	}

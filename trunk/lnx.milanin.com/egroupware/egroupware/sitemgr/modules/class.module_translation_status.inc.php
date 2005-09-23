<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* -------------------------------------------------                        *
	* Copyright (C) 2004 RalfBecker@outdoor-training.de                        *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.module_translation_status.inc.php,v 1.2 2004/04/09 19:25:01 ralfbecker Exp $ */

	class module_translation_status extends Module
	{
		function module_translation_status()
		{
			$this->arguments = array(
				'colors' => array(
					'type' => 'textfield',
					'label' => 'Colors to use from which percentage on (eg. "green: 80, yellow: 40, red")',
					'default' => 'green: 80, yellow: 40, red',
					'params' => array('size' => 50),
				),
			);
			$this->get = array('details');
			$this->properties = array();
			$this->title = lang('Translation Status');
			$this->description = lang('This module show the status / percentage of the translation of eGW');

			if (!is_object($GLOBALS['phpgw']->html))
			{
				$GLOBALS['phpgw']->html = CreateObject('phpgwapi.html');
			}
			$this->html = &$GLOBALS['phpgw']->html;
			$this->db = $GLOBALS['phpgw']->db;
		}

		function try_lang($message_id,$args='')
		{
			return $GLOBALS['phpgw']->translation->translate($message_id,$args,'');
		}
	
		function get_content(&$arguments,$properties)
		{
			$details = $arguments['details'];

			$colors = array();
			foreach(split(', ?',$arguments['colors']) as $value)
			{
				list($color,$minimum) = split(': ?',$value);
				$colors[$minimum] = $color;
			}
			krsort($colors);

			if (empty($details))
			{
				$table[] = array(
					'lang' => lang('Language'),
					'percent' => lang('Percentage'),
					'total'   => lang('Phrases in total'),
					'.total'  => 'colspan="2"',
				);
				$this->db->query('SELECT lang,lang_name,count( message_id ) AS count FROM phpgw_lang LEFT JOIN phpgw_languages ON lang=lang_id GROUP BY lang,lang_name ORDER BY count DESC,lang');
				while($row = $this->db->row(True))
				{
					if (empty($row['lang']) || empty($row['lang_name']))
					{
						continue;
					}
					if (!isset($max)) $max = $row['count'];
					$percent = sprintf('%0.1lf',100.0 * $row['count'] / $max);
					foreach($colors as $minimum => $color)
					{
						if ($percent >= $minimum)
						{
							break;
						}
					}
					$table[] = array(
						'lang' => $this->try_lang($row['lang_name']).' ('.$row['lang'].')',
						'percent' => $this->html->progressbar($percent,$percent.'%','','50px',$color,'8px'),
						'total'   => $row[count],
						'details' => '<a href="'.$this->link(array('details'=>$row['lang'])).'" title="'.lang('Show details for the applications').'">('.lang('details').')</a>'
					);
				}
				return $this->html->table($table);
			}
			$table[] = array(
				'app'     => lang('Application'),
				'percent' => lang('Percentage'),
				'total'   => lang('Phrases in total')
			);
			$this->db->query("SELECT app_name,lang,count( message_id ) AS count,lang,CASE WHEN lang='en' THEN 1 ELSE 0 END AS is_en FROM phpgw_lang WHERE lang IN (".$this->db->quote($details).",'en') GROUP BY app_name,lang,is_en ORDER BY is_en DESC,count DESC,app_name");

			while($row = $this->db->row(True))
			{
				if (empty($row['app_name'])) continue;

				if ($row['lang'] != $details)
				{
					$max[$row['app_name']] = $row['count'];
					continue;
				}
				$percent = sprintf('%0.1lf',100.0 * ($max[$row['app_name']] ? $row['count'] / $max[$row['app_name']] : 1));
				foreach($colors as $minimum => $color)
				{
					if ($percent >= $minimum)
					{
						break;
					}
				}
				$table[] = array(
					'app' => ($row['app_name'] == 'common' ? 'API' : $this->try_lang($row['app_name'])).' ('.$row['app_name'].')',
					'percent' => $this->html->progressbar($percent,$percent.'%','','50px',$color,'8px'),
					'total'   => $row[count]
				);
			}
			$this->db->query('SELECT lang_name FROM phpgw_languages WHERE lang_id='.$this->db->quote($details),__FILE__,__LINE__);
			$row = $this->db->row(True);
			return '<h3>'.lang('Details for language %1 (%2)',$this->try_lang($row['lang_name']),$details)."</h3>\n".
				$this->html->table($table).
				'<a href="'.$this->link().'">('.lang('Back to the list of languages').')</a>';
		}
	}

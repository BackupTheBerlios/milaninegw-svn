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

	/* $Id: class.module_news.inc.php,v 1.10 2004/05/24 12:20:52 ralfbecker Exp $ */

	class module_news extends Module
	{
		function module_news()
		{
			//specification of options is postponed into the get_user_interface function
			$this->arguments = array(
				'category' => array('type' => 'select', 'label' => lang('Choose a category'), 'options' => array()),
				'rsslink' => array('type' => 'checkbox', 'label' => lang('Do you want to publish a RSS feed for this news category')),
				'limit' => array(
					'type' => 'textfield', 
					'label' => lang('Number of news items to be displayed on page'),
					'params' => array('size' => 3)
				)
			);
			$this->get = array('item','start');
			$this->session = array('item','start');
			$this->properties = array();
			$this->title = lang('News module');
			$this->description = lang('This module publishes news from the news_admin application on your website. Be aware of news_admin\'s ACL restrictions.');
			$this->template;
		}

		function get_user_interface()
		{
			if (!is_dir(PHPGW_SERVER_ROOT.'/news_admin') || !isset($GLOBALS['phpgw_info']['apps']['news_admin']))
			{
				return lang("Application '%1' is not installed !!!<br>Please install it, to be able to use the block.",'news_admin');
			}
			//we could put this into the module's constructor, but by putting it here, we make it execute only when the block is edited,
			//and not when it is generated for the web site, thus speeding the latter up slightly
			$cat = createobject('phpgwapi.categories','','news_admin');
			$cats = $cat->return_array('all',0,False,'','','cat_name',True);
			if ($cats)
			{
				$cat_ids['all'] = lang('All categories');
				while (list(,$category) = each($cats))
				{
					$cat_ids[$category['id']] = $category['name'];
				}
			}
			$this->arguments['category']['options'] = $cat_ids;
			return parent::get_user_interface();
		}

		function get_content(&$arguments,$properties)
		{
			if (!is_dir(PHPGW_SERVER_ROOT.'/news_admin') || !isset($GLOBALS['phpgw_info']['apps']['news_admin']))
			{
				return lang("Application '%1' is not installed !!!<br>Please install it, to be able to use the block.",'news_admin');
			}
			$bonews = CreateObject('news_admin.bonews');
			$cat = createobject('phpgwapi.categories','','news_admin');
			$my_cat = $cat->return_single($arguments['category']);
			$this->template = Createobject('phpgwapi.Template',$this->find_template_dir());
			$this->template->set_file('news','newsblock.tpl');
			$this->template->set_block('news','NewsBlock','newsitem');
			$this->template->set_block('news','RssBlock','rsshandle');

			$limit = $arguments['limit'] ? $arguments['limit'] : 5;

			if ($arguments['rsslink'])
			{
				$this->template->set_var('rsslink',
					$GLOBALS['phpgw_info']['server']['webserver_url'] . '/news_admin/website/export.php?cat_id=' . $arguments['category']);
				$this->template->parse('rsshandle','RssBlock');
			}
			else
			{
				$this->template->set_var('rsshandle','');
			}

			// somehow $arguments['item'] is set to some whitespace
			// i have no idea why :( 
			// so i added trim
			// lkneschke 2004-02-24
			$item = trim($arguments['item']);
			if ($item)
			{
				$newsitem = $bonews->get_news($item);
				if ($newsitem && ($newsitem['category'] == $arguments['category']))
				{
					$this->render($newsitem);
					$link_data['item'] = 0;
					$this->template->set_var('morelink',
						'<a href="' . $this->link($link_data) . '">' . lang('More news') . '</a>'
					);
					return $this->template->parse('out','news');
//					return $this->template->get_var('newsitem');
				}
				else
				{
					return lang('No matching news item');
				}
			}

			$newslist = $bonews->get_newslist($arguments['category'],$arguments['start'],'','',$limit,True);

			while (list(,$newsitem) = @each($newslist))
			{
				$this->render($newsitem);
			}
			if ($arguments['start'])
			{
				$link_data['start'] = $arguments['start'] - $limit;
				$this->template->set_var('lesslink',
					'<a href="' . $this->link($link_data) . '">&lt;&lt;&lt;</a>'
				);
			}
			if ($bonews->total > $arguments['start'] + $limit)
			{
				$link_data['start'] = $arguments['start'] + $limit;
				$this->template->set_var('morelink',
					'<a href="' . $this->link($link_data) . '">' . lang('More news') . '</a>'
				);
			}
			return '<div class="contentheading" width="100%">'
                                .$my_cat[0]['name']
                                .'</div>'
                                .$this->template->parse('out','news');
		}

		function render($newsitem)
		{
			$this->template->set_var(array(
				'news_title' => stripslashes($newsitem['subject']),
				'news_submitter' => $GLOBALS['phpgw']->common->grab_owner_name($newsitem['submittedby']),
				'news_date' => $GLOBALS['phpgw']->common->show_date($newsitem['date']),
				'news_content' => stripslashes($newsitem['content'])
			));
			$this->template->parse('newsitem','NewsBlock',True);
		}
	}

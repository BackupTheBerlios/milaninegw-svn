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

	function DebugLog($object, $responseEnd = false)
	{
		if(DEBUG == false) return;
		print "<pre>";
		print_r($object);
		print "</pre>";
		if($responseEnd) exit;
	}
	
	class cPageSplitter
{
	// Draw the line like in the example below
	// Example: << 8-15 16-23 >>
	
	var $leftArrow, $rightArrow; // 	<< and >> - link to the prev next tab.
	var $rowPerPage; // count of the items on the current page
	var $activePageLinkClass, $passivePageLinkClass;//css classes
	var $countPages; // count of the links to pages - caclulated all
	var $url, $params;			//		index.php and &param1=value1&paaram2=value2... - first param is p=pageIndex for page
	var $currentPageIndex; //current page 
	var $rowCount; //Count of rows
	
	var $currentTabIndex;
	var $countTabs;
	var $countPagesPerTab; // 0 all links are visible. show tab with appropriate link

	/********Cunstructor*********/
	
	/*function SetRowCount($rowCount=0, $rowPerPage = 0)
	{
		$this->rowCount = ($rowCount > 0) ? $rowCount : 0;
		$this->rowPerPage = ($rowPerPage > 0) ? $rowPerPage : 25;
		$this->countPagesPerTab = ($this->countPagesPerTab > 0) ? $this->countPagesPerTab : 100;
	}
	function SetCounts($rowPerPage, $countPagesPerTab, $currentPageIndex=-1)
	{
		$this->rowPerPage = ($this->rowPerPage>0) ? $this->rowPerPage : 10;
		$this->countPagesPerTab = ($this->countPagesPerTab>0) ? $this->countPagesPerTab : 5;
		$this->currentPageIndex = ($this->currentPageIndex<0) ? $currentPageIndex : $this->currentPageIndex;
	}*/
	
	function cPageSplitter($currentPageIndex = 0, $rowPerPage=10, $rowCount = 0)
	{
		$this->SetInt($currentPageIndex);
		$this->currentPageIndex = $currentPageIndex < 0 ? 0 : $currentPageIndex;
		$this->SetDefaults($rowPerPage, $rowCount);
	}
	
	function Prepare()
	{
		if ($this->rowCount == 0)
			return 2;
		
		if ($this->rowCount < $this->currentPageIndex * $this->rowPerPage) 
			return 1;

		$this->countPages = $this->rowCount / (($this->countPagesPerTab == 0 ? 1 : $this->countPagesPerTab) * $this->rowPerPage);
		$this->SetInt($this->countPages);
		
		$this->currentTabIndex = $this->countPagesPerTab == 0 ? 0 : $this->currentPageIndex / $this->countPagesPerTab;
		$this->SetInt($this->currentTabIndex);

		return 0;
	}
	
	function WriteOut($lineType="")
	{
		switch ($this->Prepare())
		{
		   case 0:
		   	   //it is ok - render it
			   {
			   		switch ($lineType)
					{
						case ""		: return $this->Standard(); break;
						case "form" : return $this->FormStandard(); break;
					}
			   }
		       break;
		   case 1: 
		       return '<b>- - -</b>';
		       break;
		   case 2: 
		       return '<b>- - -</b>'; 
		       break;
		}
	}
	
	function SetInt(&$value)
	{
		settype($value, "integer");
	}
	
	function SetDefaults($rowPerPage=10, $rowCount = 0)
	{
		$this->SetInt($rowPerPage); $this->SetInt($rowCount);
		$this->rowPerPage = $rowPerPage <= 0 ? 10 : $rowPerPage;
		$this->rowCount = $rowCount < 0 ? 0 : $rowCount;
		$this->SetCountPagePerTab();
		$this->SetArrows();
	}
	
	function SetCountPagePerTab($countPagesPerTab = 0)
	{
		$this->SetInt($countPagesPerTab);
		$this->countPagesPerTab = $countPagesPerTab < 0 ? 0 : $countPagesPerTab;
	}
	
	function SetArrows($left="&lt;&lt;", $right="&gt;&gt;")
	{
		$this->leftArrow = $left;
		$this->rightArrow = $right;
	}
	
	function SetLinkStyles($passive, $active)
	{
		$this->passivePageLinkClass = $passive;
		$this->activePageLinkClass = $active;
	}
	
	function SetUrl($url)
	{
		$this->url = $url;
	}
	
	function SetParams($params)
	{
		$params = preg_replace ("/(([\&|\?]*)p=(\d*))/i", "", $params);
		$params = preg_replace ("/^&/", "", $params);
		$params = "&".$params;
		$this->params = $params;
	}

	function Standard()
	{
		$result = "";
		
		if($this->currentTabIndex > 0)
			$result.="<a href=\"".$this->url."?p=".($this->currentPageIndex-1).$this->params."\">".$this->leftArrow."</a>&nbsp;";

		for($i = $this->currentTabIndex*$this->countPagesPerTab;  $i < ($this->currentTabIndex + 1) * $this->countPagesPerTab;  $i++)
		{
			$l = ($i+1) * $this->rowPerPage;
			if ($l > $this->rowCount)
				$l = $this->rowCount;
			
			$cssClass = ($i==$this->currentPageIndex) ? ' class="'.$this->activePageLinkClass.'"' : ' class="'.$this->passivePageLinkClass.'"';
			if(trim($cssClass) == 'class=""') $cssClass ="";
			
			$result.="<a href=\"".$this->url."?p=".($i).$this->params."\"".$cssClass.">".($i*$this->rowPerPage+1)."-".($l)."</a> &nbsp;";
			
			if ($l>=$this->rowCount) 
				break;
		}

		if ( ($this->currentTabIndex + 1) * $this->countPagesPerTab * $this->rowPerPage + 1 < $this->rowCount ) 
			$result.="<a href=\"".$this->url."?p=".$i.$this->params."\">".$this->rightArrow."</a>&nbsp;";

		return $result;
	}

	function FormStandard()
	{
		$result = "";
		
		if($this->currentTabIndex > 0)
			$result.='<a href="#" class="'.$this->passivePageLinkClass.'" onclick="DoPaging(this, '.($this->currentPageIndex-1).')">'.$this->leftArrow.'</a>&nbsp;';
		
		for($i = $this->currentTabIndex*$this->countPagesPerTab;  $i < ($this->currentTabIndex + 1) * $this->countPagesPerTab;  $i++)
		{
			$l = ($i+1) * $this->rowPerPage;
			if ($l > $this->rowCount)
				$l = $this->rowCount;
			
			$cssClass = ($i==$this->currentPageIndex) ? ' class="'.$this->activePageLinkClass.'"' : ' class="'.$this->passivePageLinkClass.'"';
			if(trim($cssClass) == 'class=""') $cssClass ="";
			
			$result.="<a href=\"#\"".$cssClass." onclick=\"DoPaging(this, $i)\">".($i+1)."</a> ";
			
			if ($l>=$this->rowCount) 
				break;
		}

		if ( ($this->currentTabIndex + 1) * $this->countPagesPerTab * $this->rowPerPage + 1 <= $this->rowCount ) 
			$result.='<a href="#" class="'.$this->passivePageLinkClass.'" onclick="DoPaging(this, '.$i.')">'.$this->rightArrow.'</a>';

		return $result;
	}

}

	/***OLD CODE REFACTOR***/
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

		//veb: added begin.
		function get_curent_pageIndex()
		{
			$p = $_GET["p"].$_POST["p"];;
			if(!is_numeric($p)) $p = 0;
			settype($p, "integer");
			if($p<0) $p=0;
			
			return $p;
		}
		//veb: added end.
		
		function InitPaging($p, $perPage, $count)
		{
			$t = new cPageSplitter($p, $perPage, $count);
			$t->SetLinkStyles("blogsection", "");
			$t->SetArrows($left="&lt;&lt;", $right="&gt;&gt;");
			$t->SetCountPagePerTab(10);
			return $t->WriteOut();
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

			//veb: added begin.
			$arguments['start'] = $this->get_curent_pageIndex();
			$newslist = $bonews->get_newslist($arguments['category'], $arguments['start']*$limit,'','',$limit,True);
			//fix problem if user try to hack the page.
			if($bonews->total < $arguments['start']*$limit)
			{
				$arguments['start'] = 0;
				$newslist = $bonews->get_newslist($arguments['category'], $arguments['start']*$limit,'','',$limit,True);
			}
			$this->template->set_block('news','NewsPaging','pageitem');
			$this->template->set_var('content', $this->InitPaging($arguments['start'], $limit, $bonews->total));
			$this->template->parse('pageitem', 'NewsPaging');
			//veb: added end.
			
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

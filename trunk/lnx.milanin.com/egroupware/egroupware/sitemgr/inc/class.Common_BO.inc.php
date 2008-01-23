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

	/* $Id: class.Common_BO.inc.php,v 1.12.2.1 2004/08/27 18:24:41 ralfbecker Exp $ */

	class Common_BO
	{
		var $sites,$acl,$theme,$pages,$cats,$content,$modules;
		var $state,$visiblestates;
		var $sitemenu,$othermenu;
		function Common_BO()
		{
			$this->sites = CreateObject('sitemgr.Sites_BO',True);
			$this->acl = CreateObject('sitemgr.ACL_BO',True);
			$this->theme = CreateObject('sitemgr.Theme_BO',True);
			$this->pages = CreateObject('sitemgr.Pages_BO',True);
			$this->cats = CreateObject('sitemgr.Categories_BO',True);
			$this->content = CreateObject('sitemgr.Content_BO',True);
			$this->modules = CreateObject('sitemgr.Modules_BO',True);
			$this->state = array(
				SITEMGR_STATE_DRAFT => lang('draft'),
				SITEMGR_STATE_PREPUBLISH => lang('prepublished'),
				SITEMGR_STATE_PUBLISH => lang('published'),
				SITEMGR_STATE_PREUNPUBLISH => lang('preunpublished'),
				SITEMGR_STATE_ARCHIVE => lang('archived'),
			);
			$this->viewable = array(
				SITEMGR_VIEWABLE_EVERBODY => lang('everybody'),
				SITEMGR_VIEWABLE_USER => lang('phpgw users'),
				SITEMGR_VIEWABLE_ADMIN => lang('administrators'),
				SITEMGR_VIEWABLE_ANONYMOUS => lang('anonymous')
			);
		}

		function setvisiblestates($mode)
		{
			$this->visiblestates = $this->getstates($mode);
		}

		function getstates($mode)
		{
			switch ($mode)
			{
				case 'Administration' :
					return array(SITEMGR_STATE_DRAFT,SITEMGR_STATE_PREPUBLISH,SITEMGR_STATE_PUBLISH,SITEMGR_STATE_PREUNPUBLISH);
				case 'Draft' :
					return array(SITEMGR_STATE_PREPUBLISH,SITEMGR_STATE_PUBLISH);
				case 'Edit' :
					return array(SITEMGR_STATE_DRAFT,SITEMGR_STATE_PREPUBLISH,SITEMGR_STATE_PUBLISH,SITEMGR_STATE_PREUNPUBLISH);
				case 'Commit' :
					return array(SITEMGR_STATE_PREPUBLISH,SITEMGR_STATE_PREUNPUBLISH);
				case 'Archive' :
					return array(SITEMGR_STATE_ARCHIVE);
				case 'Production' :
				default:
					return array(SITEMGR_STATE_PUBLISH,SITEMGR_STATE_PREUNPUBLISH);
			}
		}

		function globalize($varname)
		{
			if (is_array($varname))
			{
				foreach($varname as $var)
				{
					$GLOBALS[$var] = $_POST[$var];
				}
			}
			else
			{
				$GLOBALS[$varname] = $_POST[$varname];
			}
		}

		function getlangname($lang)
		{
			$GLOBALS['phpgw']->db->query("select lang_name from phpgw_languages where lang_id = '$lang'",__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			return $GLOBALS['phpgw']->db->f('lang_name');
		}

		function inputstateselect($default)
		{
			$returnValue = '';
			foreach($this->state as $value => $display)
			{
				$selected = ($default == $value) ? $selected = 'selected="selected" ' : '';
				$returnValue.='<option '.$selected.'value="'.$value.'">'.
					$display.'</option>'."\n";
			}
			return $returnValue;
		}

		function set_menus()
		{
			$this->sitemenu = $this->get_sitemenu();
			$this->othermenu = $this->get_othermenu();
		}

		function get_sitemenu()
		{
			if ($GLOBALS['Common_BO']->acl->is_admin())
			{
				$file['Configure Website'] = $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Common_UI.DisplayPrefs');
				$link_data['cat_id'] = CURRENT_SITE_ID;
				$link_data['menuaction'] = "sitemgr.Modules_UI.manage";
				$file['Manage site-wide module properties'] = $GLOBALS['phpgw']->link('/index.php',$link_data);
/* not longer show, as it can be done via Edit-mode now
				$link_data['page_id'] = 0;
				$link_data['menuaction'] = "sitemgr.Content_UI.manage";
				$file['Manage site-wide content'] = $GLOBALS['phpgw']->link('/index.php',$link_data);
*/
			}
//			$file['Manage Categories and pages'] = $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Outline_UI.manage');
			$file['Manage Translations'] = $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Translations_UI.manage');
			$file['Commit Changes'] = $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Content_UI.commit');
			$file['Manage archived content'] = $GLOBALS['phpgw']->link('/index.php', 'menuaction=sitemgr.Content_UI.archive');

			if (($site = $this->sites->read(CURRENT_SITE_ID)) && $site['site_url'])
			{
				$file[] = '_NewLine_';
				$file['View generated Site'] = $site['site_url'].'?mode=Production'.
 					'&sessionid='.@$GLOBALS['phpgw_info']['user']['sessionid'] .
					'&kp3=' . @$GLOBALS['phpgw_info']['user']['kp3'] .
					'&domain=' . @$GLOBALS['phpgw_info']['user']['domain'];

				$file['Edit Site'] = $GLOBALS['phpgw']->link('/sitemgr/');
			}
			return $file;
		}

		function get_othermenu()
		{
			$numberofsites = $this->sites->getnumberofsites();
			$isadmin = $GLOBALS['phpgw']->acl->check('run',1,'admin');
			if ($numberofsites < 2 && !$isadmin)
			{
				return false;
			}
			$menu_title = lang('Other websites');
			if ($numberofsites > 1)
			{
				$link_data['menuaction'] = 'sitemgr.Common_UI.DisplayIFrame';
				$sites = $GLOBALS['Common_BO']->sites->list_sites(False);
				while(list($site_id,$site) = @each($sites))
				{
					if ($site_id != CURRENT_SITE_ID)
					{
						$link_data['siteswitch'] = $site_id;
						$file[] = array(
							'text' => $site['site_name'],
							'no_lang' => True,
							'link' => $GLOBALS['phpgw']->link('/index.php',$link_data)
						);
					}
				}
			}
			if ($numberofsites > 1 && $isadmin)
			{
				$file['_NewLine_'] ='';
			}
			if ($isadmin)
			{
				$file['Define websites'] = $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Sites_UI.list_sites');
			}
			return $file;
		}			
	}
?>

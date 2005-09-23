<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* Rewritten with the new db-functions by RalfBecker-AT-outdoor-training.de *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.Sites_SO.inc.php,v 1.6.2.1 2004/08/06 18:31:04 ralfbecker Exp $ */

	class Sites_SO
	{
		var $db;
		var $sites_table = 'phpgw_sitemgr_sites';	// only reference to the db-prefix
		
		function Sites_SO()
		{
			$this->db = $GLOBALS['phpgw']->db;
			$this->db->set_app('sitemgr');
		}

		function list_siteids()
		{
			$this->db->select($this->sites_table,'site_id',False,__LINE__,__FILE__);

			$result = array();
			while ($this->db->next_record())
			{
				$result[] = $this->db->f('site_id');
			}
			return $result;
		}

		function getWebsites($limit,$start,$sort,$order,$query,&$total)
		{
			if ($limit)
			{
				if ($query)
				{
					$query = $this->db->quote('%'.$query.'%');
					$whereclause = "site_name LIKE $query OR site_url LIKE $query OR site_dir LIKE $query";
				}
				if (preg_match('/^[a-z_0-9]+$/i',$order) && preg_match('/^(asc|desc)*$/i',$sort))
				{
					$orderclause = "ORDER BY $order " . ($sort ? $sort : 'DESC');
				}
				else
				{
					$orderclause = 'ORDER BY site_name ASC';
				}
				$this->db->select($this->sites_table,'COUNT(*)',$whereclause,__LINE__,__FILE__);
				$total = $this->db->next_record() ? $this->db->f(0) : 0;

				$this->db->select($this->sites_table,'site_id,site_name,site_url',$whereclause,__LINE__,__FILE__,$start,$orderclause);
			}
			else
			{
				$this->db->select($this->sites_table,'site_id,site_name,site_url',False,__LINE__,__FILE__);
			}
			while ($this->db->next_record())
			{
				foreach(array('site_id', 'site_name', 'site_url') as $col)
				{
					$site[$col] = $this->db->f($col);
				}
				$result[$site['site_id']] = $site;
			}
			return $result;
		}

		function getnumberofsites()
		{
			$this->db->select($this->sites_table,'COUNT(*)',False,__LINE__,__FILE__);

			return $this->db->next_record() ? $this->db->f(0) : 0;
		}

		function urltoid($url)
		{
			$this->db->select($this->sites_table,'site_id',array(
					'site_url' => $url,
				),__LINE__,__FILE__);

			return $this->db->next_record() ? $this->db->f('site_id') : False;
		}

		function read($site_id)
		{
			$this->db->select($this->sites_table,'*',array(
					'site_id' => $site_id,
				),__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				foreach(
					array(
						'site_id', 'site_name', 'site_url', 'site_dir', 'themesel', 
						'site_languages', 'home_page_id', 'anonymous_user','anonymous_passwd'
					) as $col
				)
				{
					$site[$col] = $this->db->f($col);
				}
				return $site;
			}
			return false;
		}

		function read2($site_id)
		{
			$this->db->select($this->sites_table,'site_url,site_dir',array(
					'site_id' => $site_id,
				),__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				foreach(
					array(
						'site_url', 'site_dir'
					) as $col
				)
				{
					$site[$col] = $this->db->f($col);
				}
				return $site;
			}
			return false;
		}

		function add($site)
		{
			$cats = CreateObject('phpgwapi.categories',-1,'sitemgr');
			$site_id =  $cats->add(array(
				'name'		=> $site['name'],
				'descr'		=> '',
				'access'	=> 'public',
				'parent'	=> 0,
				'old_parent' => 0
			));
			$this->db->insert($this->sites_table,array(
					'site_id'   => $site_id,
					'site_name' => $site['name'],
					'site_url'  => $site['url'],
					'site_dir'  => $site['dir'],
					'anonymous_user' => $site['anonuser'],
					'anonymous_passwd' => $site['anonpasswd'],
				),False,__LINE__,__FILE__);

			return $site_id;
		}

		function update($site_id,$site)
		{
			return $this->db->update($this->sites_table,array(
					'site_name' => $site['name'],
					'site_url'  => $site['url'],
					'site_dir'  => $site['dir'],
					'anonymous_user' => $site['anonuser'],
					'anonymous_passwd' => $site['anonpasswd'],
				),array(
					'site_id' => $site_id
				),__LINE__,__FILE__);
		}

		function delete($site_id)
		{
			return $this->db->delete($this->sites_table,array(
					'site_id' => $site_id
				),__LINE__,__FILE__);
		}

		function saveprefs($prefs,$site_id=CURRENT_SITE_ID)
		{
			return $this->db->update($this->sites_table,array(
					'themesel' => $prefs['themesel'],
					'site_languages' => $prefs['site_languages'],
					'home_page_id' => $prefs['home_page_id'],
				),array(
					'site_id' => $site_id
				),__LINE__,__FILE__);
		}
	}

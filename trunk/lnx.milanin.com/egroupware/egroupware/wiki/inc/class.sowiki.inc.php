<?php
/**************************************************************************\
* eGroupWare - Wiki DB-Layer                                               *
* http://www.egroupware.org                                                *
* Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
* originaly based on WikkiTikkiTavi tavi.sf.net and www.axisgroupware.org: *
* former files lib/pagestore.php + lib/page.php                            *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id: class.sowiki.inc.php,v 1.19.2.3 2004/09/01 10:40:12 ralfbecker Exp $ */

define ('WIKI_ACL_ALL',0);		// everyone incl. anonymous
define ('WIKI_ACL_USER',-1);	// everyone BUT anonymous
define ('WIKI_ACL_ADMIN',-2);	// only admins (access to the admin app !)

/*!
@class sowiki
@author ralfbecker
*/
class soWikiPage
{
	var $name = '';                       // Name of page.
	var $title = '';                      // Title of page.
	var $text = '';                       // Page's text in wiki markup form.
	var $time = '';                       // Page's modification time.
	var $hostname = '';                   // Hostname of last editor.
	var $username = '';                   // Username of last editor.
	var $comment  = '';                   // Description of last edit.
	var $version = -1;                    // Version number of page.
	var $mutable = 1;                     // Whether page may be edited (depricated !)
	var $readable = WIKI_ACL_ALL;         // who can read the page
	var $writable = WIKI_ACL_ALL;         // who can write the page
	var $exists = 0;                      // Whether page already exists.
	var $db;                              // Database object.
	var $colNames = array(                // column-name - class-var-name pairs
		'wiki_id'   => 'wiki_id',
		'name'      => 'name',
		'lang'      => 'lang',
		'version'   => 'version',
		'time'      => 'time',
		'supercede' => 'supercede',
		'readable'  => 'readable',
		'writable'  => 'writable',
		'hostname'  => 'hostname',
		'username'  => 'username',
		'comment'   => 'comment',
		'title'     => 'title',
		'body'      => 'text',
	);

	/*
	@function soWikiPage
	@abstract Constructor of the soWikiPage class
	@syntax soWikiPage(&$pagestore,$name = '')
	@param $store Referenz to a pagestore for phpgw db-object and the db-names
	@param $name Name of page to load
	*/
	function soWikiPage($db,$PgTbl,$table_def,$name = '',$lang=False,$wiki_id=0)
	{
		$this->db = $db;		// to have an independent result-pointer
		$this->PgTbl = $PgTbl;
		$this->db->set_column_definitions($table_def['fd']);
		$this->name = $name;
		$this->lang = $lang;
		$this->wiki_id = (int) $wiki_id;
		$this->memberships = $GLOBALS['phpgw']->accounts->membership();
		foreach($this->memberships as $n => $data)
		{
			$this->memberships[$n] = (int) $data['account_id'];
		}
		$this->user_lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
		$this->use_langs = array($this->user_lang,'');
		// english as fallback, should be configurable or a pref
		if ($this->user_lang != 'en') $this->use_langs[] = 'en';
//		$this->lang_priority_sql  = "IF(body='',".(count($this->use_langs)+1).',CASE lang';
		$this->lang_priority_sql  = "CASE WHEN body LIKE '' THEN ".(count($this->use_langs)+1).' ELSE (CASE lang';

		foreach($this->use_langs as $order => $lang)
		{
			$this->lang_priority_sql .= ' WHEN '.$this->db->quote($lang)." THEN $order";
		}
		$this->lang_priority_sql  .= ' ELSE '.count($this->use_langs).' END) END AS lang_priority';

		// $GLOBALS['config'] is set by lib/init
		if (!is_array($GLOBALS['config']))
		{
			$c = CreateObject('phpgwapi.config','wiki');
			$c->read_repository();
			$GLOBALS['config'] = $c->config_data;
			unset($c);
		}
		$this->config = &$GLOBALS['config'];
	}

	/*!
	@function acl_filter()
	@abstracts filter to and into query to get only readable / writeable page of current user
	@param bool $readable generate SQL for readable or writable filter, default True == readable
	@param bool $add_wiki_id add code to filter only the actual wiki
	@returns SQL to AND into the query
	*/
	function acl_filter($readable = True,$add_wiki_id=True)
	{
		static $filters = array();

		$filter_id = "$readable-$add_wiki_id";
		if (isset($filters[$filter_id]))
		{
			return $filters[$filter_id];
		}
		$user = $GLOBALS['phpgw_info']['user']['account_id'];

		$filters[] = WIKI_ACL_ALL;

		if ($GLOBALS['phpgw_info']['user']['account_lid'] !=  $GLOBALS['config']['AnonymousUser'])
		{
			$filters[] = WIKI_ACL_USER;
		}
		if (@$GLOBALS['phpgw_info']['user']['apps']['admin'])
		{
			$filters[] = WIKI_ACL_ADMIN;
		}
		$filters = array_merge($filters,$this->memberships);

		$sql = '('.($add_wiki_id ? " wiki_id=$this->wiki_id AND " : '').
			($readable ? 'readable' : 'writable').' IN ('.implode(',',$filters).'))';
		//echo "<p>sowiki::acl_filter($readable,$add_wiki_id) = '$sql'</p>\n";

		return $filters[$filter_id] = $sql;
	}

	/*!
	@function acl_check()
	@abstracts check if page is readable or writeable by the current user
	@param bool $readable check if page is readable or writable, default False == writeable
	@returns True if check was successful, or false
	@note If we have an anonymous session and the anonymous session-type is NOT editable,
		all pages are readonly (even if their own setting is editable by all) !!!
	*/
	function acl_check($readable = False)
	{
		if (!$readable && $this->config['Anonymous_Session_Type'] != 'editable' &&
			$GLOBALS['phpgw_info']['user']['account_lid'] == $this->config['anonymous_username'])
		{
			return False;	// Global config overrides page-specific setting
		}
		switch ($acl = $readable ? $this->readable : $this->writable)
		{
			case WIKI_ACL_ALL:
				return True;

			case WIKI_ACL_USER:
				return $GLOBALS['phpgw_info']['user']['account_lid'] !=  $this->config['anonymous_username'];

			case WIKI_ACL_ADMIN:
				return  isset($GLOBALS['phpgw_info']['user']['apps']['admin']);

			default:
				return in_array($acl,$this->memberships);
		}
		return False;
	}

	function as_array()
	{
		$arr = array();
		foreach($this->colNames as $name)
		{
			$arr[$name] = $this->$name;
		}
		return $arr;
	}

	/*!
	@function exists 
	@abstract Check whether a page exists.
	@syntax exists()
	@note the name of the page has to be set by the constructor
	@returns true if page exists in database.
	*/
	function exists()
	{
		$this->db->query("SELECT lang FROM $this->PgTbl WHERE name=".$this->db->quote($this->name).' AND lang IN ('.
			$this->db->column_data_implode(',',$this->use_langs,False).') AND '.$this->acl_filter(),__LINE__,__FILE__);
		
		return $this->db->next_record() ? ($this->db->f(0) ? $this->db->f(0) : 'default')  : False;
	}

	/*!
	@function read
	@abstract Read in a page's contents.
	@syntax read()
	@note the name of the page has to be set by the constructor
	@returns contents of the page or False.
	*/
	function read()
	{
		$query = "SELECT *,$this->lang_priority_sql FROM $this->PgTbl WHERE name=".$this->db->quote($this->name)." AND ".$this->acl_filter();
		if (!empty($this->lang))
		{
			$query .= " AND lang=".$this->db->quote($this->lang);
		}
		else
		{
			$query .= ' AND lang IN ('.$this->db->column_data_implode(',',$this->use_langs,False).')';
		}
		if($this->version != -1)
		{
			$query .= " AND version=".$this->db->quote($this->version,'int');
		}
		else
		{
			$query .= ' AND supercede=time';	// gives the up-to-date version only
		}
		$query .= ' ORDER BY lang_priority,version DESC';

		$this->db->query($query,__LINE__,__FILE__);

		if (!$this->db->next_record())
		{
			return False;
		}
		foreach($this->colNames as $dbname => $name)
		{
			$this->$name     = $this->db->f($dbname);
		}
		$this->exists   = 1;
		$this->mutable  = $this->acl_check();

		return $this->text;
	}

	/*!
	@function write
	@abstract Write the a page's contents to the db and sets the supercede-time of the prev. version
	@syntax write()
	@note The caller is responsible for performing locking.
	*/
	function write()
	{
		$this->time = $this->supercede = time();
		foreach($this->colNames as $dbname => $name)
		{
			$arr[$dbname] = $this->$name;
		}
		$this->db->query($sql="INSERT INTO $this->PgTbl ".
			$this->db->column_data_implode(',',$arr,'VALUES'),__LINE__,__FILE__);

		//echo "<p>soWikiPage::write() sql='$sql'</p>";
		if($this->version > 1)	// set supercede-time of prev. version
		{
			$this->db->query("UPDATE $this->PgTbl SET supercede=$this->supercede WHERE ".
				$this->db->column_data_implode(' AND ',array(
					'wiki_id' => $this->wiki_id,
					'name' => $this->name,
					'lang' => $this->lang,
					'version' => $this->version-1
			)),__LINE__,__FILE__);
		}
	}

	/*!
	@function rename
	@abstract Renames a page to a new name and/or lang
	@syntax rename($new_name,$new_lang='')
	@note The caller is responsible for performing locking.
	*/
	function rename($new_name=False,$new_lang=False)
	{
		if ($new_name === False && $new_lang === False || !$this->acl_check())
		{
			//echo "soWikiPage::rename('$new_name','$new_lang') returning False this=<pre>".print_r($this->as_array(),True)."</pre>";
			return False;	// nothing to do or no permission
		}
		$new = array(
			'wiki_id' => $this->wiki_id,
			'name'    => $new_name === False ? $this->name : $new_name,
			'lang'    => $new_lang === False ? $this->lang : $new_lang,
		);
		// delete (evtl.) existing target
		$this->db->query($sql="DELETE FROM $this->PgTbl WHERE ".
			$this->db->column_data_implode(' AND ',$new),__LINE__,__FILE__);

		$this->db->query($sql2="UPDATE $this->PgTbl SET ".$this->db->column_data_implode(',',$new)." WHERE ".
			$this->db->column_data_implode(' AND ',array(
				'wiki_id' => $this->wiki_id,
				'name' => $this->name,
				'lang' => $this->lang,
			)),__LINE__,__FILE__);

		//echo "<p>soWikiPage::rename('$new_name','$new_lang') old='$this->name:$this->lang', sql='$sql', sql2='$sql2'</p>";
		if ($new_name !== False) $this->name = $new_name;
		if ($new_lang !== False) $this->lang = $new_lang;

		return $this->db->affected_rows();
	}
}

/*!
@class sowiki
@author ralfbecker
@note was former called pageStore
*/
class sowiki	// DB-Layer
{
	var $db;
	var $LkTbl = 'phpgw_wiki_links';
	var $PgTbl = 'phpgw_wiki_pages';
	var $RtTbl = 'phpgw_wiki_rate';
	var $IwTbl = 'phpgw_wiki_interwiki';
	var $SwTbl = 'phpgw_wiki_sisterwiki';
	var $RemTbl= 'phpgw_wiki_remote_pages';
	var $ExpireLen,$Admin;
	var $RatePeriod,$RateView,$RateSearch,$RateEdit;
	var $wiki_id = 0;

	/*!
	@function sowiki
	@abstract Constructor of the PageStrore class sowiki
	@syntax sowiki()
	*/
	function sowiki($wiki_id=0)
	{
		$this->wiki_id = (int) $wiki_id;
		$this->user_lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];

		$this->db = $GLOBALS['phpgw']->db;
		
		global $ExpireLen,$Admin;		// this should come from the app-config later
		global $RatePeriod, $RateView, $RateSearch, $RateEdit;
		$this->ExpireLen  = $ExpireLen;
		$this->Admin      = $Admin;
		$this->RatePeriod = $RatePeriod;
		$this->RateView   = $RateView;
		$this->RateSearch = $RateSearch;
		$this->RateEdit   = $RateEdit;

		$this->table_defs = $this->db->get_table_definitions('wiki');
	}

	/*!
	@function page
	@abstract Create a page object.
	@syntax page($name = '')
	@param $name Name of the page
	@returns the page
	*/
	function page($name = '',$lang=False)
	{
		//echo "<p>sowiki::page(".print_r($name,True).",'$lang')</p>";
		if (is_array($name))
		{
			$lang = $lang ? $lang : @$name['lang'];
			$name = @$name['name'] ? $name['name'] : @$name['title'];
		}
		return new soWikiPage($this->db,$this->PgTbl,$this->table_defs[$this->PgTbl],$name,$lang,$this->wiki_id);
	}

	/*!
	@function find
	@abstract Find $text in the database, searches title and body.
	@syntax find($text)
	@param $text string pattern to search
	@param $search_in mixed comma-separated string or array with columns to search (name,title,body) or
		False to search all three for "%text%" (!)
	@returns an array of wiki-pages (array with column-name / value pairs)
	*/
	function find($text,$search_in=False)
	{
		$sql="SELECT t1.name,t1.lang,t1.version,MAX(t2.version) as max,t1.title,t1.body".
			" FROM $this->PgTbl AS t1,$this->PgTbl AS t2".
			" WHERE t1.name=t2.name AND t1.lang=t2.lang AND t1.wiki_id=$this->wiki_id AND t2.wiki_id=$this->wiki_id".
			" GROUP BY t1.name,t1.lang,t1.version,t1.title,t1.body".
			" HAVING t1.version=MAX(t2.version) AND (";

		// fix for case-insensitiv search on pgsql for lowercase searchwords
		$op_text = $this->db->type == 'pgsql' && !preg_match('/[A-Z]/') ? 'ILIKE' : 'LIKE';
		$op_text .= ' '.$this->db->quote($search_in ? $text : "%$text%");

		$search_in = $search_in ? explode(',',$search_in) : array('name','title','body');
		foreach($search_in as $n => $name)
		{
			$sql .= ($n ? ' OR ' : '') . "t1.$name $op_text";
		}
		$sql .= ')';

		$this->db->query($sql,__LINE__,__FILE__);
		$list = array();
		while($row = $this->db->row(True))
		{
			unset($row['max']);
			$list[] = $row;
		}
		return $list;
	}

	/*!
	@function history
	@abstract Retrieve a page's edit history.
	@syntax history($page,$lang=False)
	@param $page name of the page
	@returns an array of the different versions 
	*/
	function history($page,$lang=False)
	{
		$name = $this->db->db_addslashes(is_array($page) ? $page['name'] : $page);
		$lang = $this->db->db_addslashes(is_array($page) && !$lang ? $page['lang'] : $lang);

		$this->db->query($sql = "SELECT time,hostname,version,username,comment " .
		                 " FROM $this->PgTbl WHERE name='$name' AND lang='$lang' AND wiki_id=$this->wiki_id ORDER BY version DESC");
		$list = array();
		while($this->db->next_record())
		{
			$list[] = $this->db->Record;	// that allows num. indexes as well as strings
		}
		//echo "<p>sowiki::history(".print_r($page,True).",'$lang') sql='$sql'<pre>".print_r($list,True)."</pre>\n";
		return $list;
	}

	/*!
	@function interwiki
	@abstract Look up an interwiki prefix
	@syntax interwiki($name)
	@param $name name-prefix of an interwiki
	@returns the url of False 
	*/
	function interwiki($name)
	{
		$values = array(
			'wiki_id' => $this->wiki_id,
			'prefix'  => $name,
		);
		$name = $this->db->db_addslashes($name);
		$this->db->query("SELECT url FROM $this->IwTbl WHERE ".
			$this->db->column_data_implode(' AND ',$values,True,False,$this->table_defs[$this->IwTbl]['fd']),
			__LINE__,__FILE__);
		
		return $this->db->next_record() ? $this->db->f('url') : False;
	}

	/*!
	@function clear_link
	@abstract Clear all the links cached for a particular page.
	@syntax clear_link($page)
	@param $page page-name
	*/
	function clear_link($page)
	{
		$values = array(
			'wiki_id' => is_array($page) && isset($page['wiki_id']) ? $page['wiki_id'] : $this->wiki_id,
			'page'    => is_array($page) ? $page['name'] : $page,
			'lang'    => $page['lang'],
		);
		//echo "<p>sowiki::clear_link('$values[wiki_id]:$values[page]:$values[lang]')</p>";

		$this->db->query("DELETE FROM $this->LkTbl WHERE ".
			$this->db->column_data_implode(' AND ',$values,True,False,$this->table_defs[$this->LkTbl]['fd']),
			__LINE__,__FILE__);
	}

	/*!
	@function clear_interwiki
	@abstract Clear all the interwiki definitions for a particular page.
	@syntax clear_interwiki($page)
	@param $page page-name
	*/
	function clear_interwiki($page)
	{
		$values = array(
			'wiki_id'            => is_array($page) && isset($page['wiki_id']) ? $page['wiki_id'] : $this->wiki_id,
			'where_defined_page' => is_array($page) ? $page['name'] : $page,
			'where_defined_lang' => $page['lang'],
		);

		$this->db->query("DELETE FROM $this->IwTbl WHERE ".
			$this->db->column_data_implode(' AND ',$values,True,False,$this->table_defs[$this->IwTbl]['fd']),
			__LINE__,__FILE__);
	}

	/*!
	@function clear_sisterwiki
	@abstract Clear all the sisterwiki definitions for a particular page.
	@syntax clear_sisterwiki($page)
	@param $page page-name
	*/
	function clear_sisterwiki($page)
	{
		$values = array(
			'wiki_id'            => is_array($page) && isset($page['wiki_id']) ? $page['wiki_id'] : $this->wiki_id,
			'where_defined_page' => is_array($page) ? $page['name'] : $page,
			'where_defined_lang' => $page['lang'],
		);

		$this->db->query("DELETE FROM $this->SwTbl WHERE ".
			$this->db->column_data_implode(' AND ',$values,True,False,$this->table_defs[$this->SwTbl]['fd']),
			__LINE__,__FILE__);
	}

	/*!
	@function new_link
	@abstract Add a link for a given page to the link table.
	@syntax new_link($page, $link)
	@param $page
	@param $link
	*/
	function new_link($page, $link)
	{
		static $links = array();

		$values = array(
			'wiki_id' => is_array($page) && isset($page['wiki_id']) ? $page['wiki_id'] : $this->wiki_id,
			'page'    => trim(is_array($page) ? $page['name'] : $page),
			'lang'    => $page['lang'],
			'link'    => trim($link),
		);
		// $links need to be 2-dimensional as rename, can cause new_link to be called for different pages
		$page_uid = strtolower($values['wiki_id'].':'.$values['page'].':'.$values['lang']);
		$link = strtolower(trim($link));
		$values['count'] = ++$links[$page_uid][$link];

		//echo "<p>sowiki::new_link('$values[wiki_id]:$values[page]:$values[lang]','$link') = $values[count]</p>";

		if ($values['count'] == 1)
		{
			$this->db->query($sql="INSERT INTO $this->LkTbl ".
				$this->db->column_data_implode(',',$values,'VALUES',False,$this->table_defs[$this->LkTbl]['fd'])
				,__LINE__,__FILE__);
		}
		else
		{
			unset($values['count']);
			$this->db->query($sql="UPDATE $this->LkTbl SET count=".$this->db->quote($links[$page_uid][$link],'int').' WHERE '.
				$this->db->column_data_implode(' AND ',$values,True,False,$this->table_defs[$this->LkTbl]['fd']),
				__LINE__,__FILE__);
		}
		//echo "<p>sowiki::new_link('$page','$link') sql='$sql'</p>\n";
	}

	function get_links($link='')
	{
		$result = array();
		$values = array('wiki_id' => $this->wiki_id);
		if ($link)
		{
			$values['link'] = $link;
		}
		$this->db->query("SELECT page,lang,link FROM $this->LkTbl WHERE ".
			$this->db->column_data_implode(' AND ',$values,True,False,$this->table_defs[$this->LkTbl]['fd']),
			" ORDER BY page,lang",__LINE__,__FILE__);

		while ($row = $this->db->row(True))
		{
			$result[$row['page']][$row['lang']][] = $row['link'];
		}
		return $result;
	}

	/*!
	@function new_interwiki
	@abstract Add an interwiki definition for a particular page.
	@syntax new_interwiki($where_defined, $prefix, $url)
	@param $where_defined
	@param $prefix Prefix of the new interwiki
	@param $url URL of the new interwiki
	*/
	function new_interwiki($page, $prefix, $url)
	{
		$values = array(
			'wiki_id'               => is_array($page) && isset($page['wiki_id']) ? $page['wiki_id'] : $this->wiki_id,
			'prefix'                => $prefix,
			'where_defined_page'    => is_array($page) ? $page['name'] : $page,
			'where_defined_lang'    => $page['lang'],
			'url'                   => str_replace('&amp;','&',$url),
		);
		$this->db->query("SELECT where_defined FROM $this->IwTbl WHERE ".
			$this->db->column_data_explode(' AND ',$values,True,array('wiki_id','prefix')),__LINE__,__FILE__);

		if($this->db->next_record())
		{
			$this->db->query("UPDATE $this->IwTbl SET ".
				$this->db->column_data_explode(',',$values,True),__LINE__,__FILE__);
		}
		else
		{
			$this->db->query("INSERT INTO $this->IwTbl " .
				$this->db->column_data_explode(',',$values,'VALUES'),__LINE__,__FILE__);
		}
	}

	/*!
	@function new_sisterwiki
	@abstract Add an sisterwiki definition for a particular page.
	@syntax new_sisterwiki($where_defined, $prefix, $url)
	@param $where_defined
	@param $prefix Prefix of the new sisterwiki
	@param $url URL of the new sisterwiki
	*/
	function new_sisterwiki($where_defined, $prefix, $url)
	{
		$values = array(
			'wiki_id'               => is_array($page) && isset($page['wiki_id']) ? $page['wiki_id'] : $this->wiki_id,
			'prefix'                => $prefix,
			'where_defined_page'    => is_array($page) ? $page['name'] : $page,
			'where_defined_lang'    => $page['lang'],
			'url'                   => str_replace('&amp;','&',$url),
		);

		$this->db->query("SELECT where_defined FROM $this->SwTbl WHERE ".
			$this->db->column_data_explode(' AND ',$values,True,array('wiki_id','prefix')),__LINE__,__FILE__);

		if($this->db->next_record())
		{
			$this->db->query("UPDATE $this->SwTbl SET ".
				$this->db->column_data_explode(',',$values,True),__LINE__,__FILE__);
		}
		else
		{
			$this->db->query("INSERT INTO $this->SwTbl " .
				$this->db->column_data_explode(',',$values,'VALUES'),__LINE__,__FILE__);
		}
	}

	/*!
	@function twinpages
	@abstract Find all twins of a page at sisterwiki sites.
	@syntax twinpages($page)
	@param $page page-name
	@returns a list of array(site,page)
	*/
	function twinpages($page)
	{
		$this->db->query("SELECT site, page FROM $this->RemTbl WHERE page=".
			$this->db->quote(is_array($page) ? $page['name'] : $page),__LINE__,__FILE__);

		$list = array();
		while($this->db->next_record())
		{ 
			$list[] = $this->db->Record;
		}
		return $list;
	}

	/*
	@function lock
	@abstract Lock the database tables.
	@syntax lock()
	*/
	function lock()
	{
		$this->db->lock(array($this->PgTbl,$this->IwTbl,$this->SwTbl,$this->LkTbl),'write');
	}

	/*
	@function unlock
	@abstract Unlock the database tables.
	@syntax unlock()
	*/
	function unlock()
	{
		$this->db->unlock();
	}

	/*
	@function allpages
	@abstract Retrieve a list of all of the pages in the wiki.
	@syntax allpages()
	@returns array of all pages
	*/
	function allpages()
	{
		$qid = $this->db->query("SELECT t1.time,t1.name,t1.lang,t1.hostname,t1.username,t1.title,".
		                        " LENGTH(t1.body) AS length,t1.comment,t1.version,MAX(t2.version)" .
		                        " FROM $this->PgTbl AS t1, $this->PgTbl AS t2" .
		                        " WHERE t1.name = t2.name AND t1.lang=t2.lang".
		                        " GROUP BY t1.name,t1.lang,t1.version,t1.time,t1.hostname,t1.username,t1.body,t1.comment,t1.title" .
		                        " HAVING t1.version = MAX(t2.version)",__LINE__,__FILE__);
		$list = array();
		while($this->db->next_record())
		{
			$list[] = $this->db->Record;
		}

		return $list;
	}

	/*
	@function newpages
	@abstract Retrieve a list of the new pages in the wiki.
	@syntax newpages()
	@returns array of pages
	*/
	function newpages()
	{
		$this->db->query("SELECT time,name,lang,hostname,username,LENGTH(body) AS length,comment,title" .
		                 " FROM $this->PgTbl WHERE version=1",__LINE__,__FILE__);

		$list = array();
		while($this->db->next_record())
		{
			$list[] = $this->db->Record;
		}
		return $list;
	}

	/*
	@function emptypages
	@abstract Retrieve a list of all empty (deleted) pages in the wiki.
	@syntax emptypages()
	@returns array of page-infos
	*/
	function emptypages()
	{
		$this->db->query("SELECT t1.time,t1.name,t1.lang,t1.hostname,t1.username,0,t1.comment,t1.version,MAX(t2.version),t1.title " .
		                 " FROM $this->PgTbl AS t1,$this->PgTbl AS t2" .
		                 " WHERE t1.name=t2.name AND t1.lang=t2.lang".
		                 " GROUP BY t1.name,t1.lang,t1.version,t1.time,t1.hostname,t1.username,t1.comment".
		                 " HAVING t1.version = MAX(t2.version) AND t1.body LIKE ''",__LINE__,__FILE__);
		$list = array();
		while($this->db->next_record())
		{
			$list[] = $this->db->Record;
		}
		return $list;
	}

	/*
	@function givenpages
	@abstract Retrieve a list of information about a particular set of pages
	@syntax givenpages()
	@returns array of page-infos
	*/
	function givenpages($names)
	{
		$list = array();
		foreach($names as $page)
		{
			$esc_page = $this->db->db_addslashes($page);
			$this->db->query("SELECT time,name,hostname,username,LENGTH(body) AS length,comment,title".
			                 " FROM $this->PgTbl WHERE name='$esc_page'" .
			                 " ORDER BY version DESC",__LINE__,__FILE__);

			if($this->db->next_record())
			{ 
				$list[] = $this->db->Record;
			}
		}
		return $list;
	}

	/*!
	@function maintain
	@abstract Expire old versions of pages.
	@syntax maintain()
	*/
	function maintain()
	{
/* this code created one query for every page in the wiki on each page-request !!!
		$db2 = $this->db;	// we need a new/second result-pointer
		
		$this->db->query("SELECT name,lang,MAX(version) AS version".
		                 " FROM $this->PgTbl GROUP BY name,lang",__LINE__,__FILE__);

		while($this->db->next_record())
		{
			$name = $this->db->db_addslashes($this->db->f('name'));
			$alng = $this->db->db_addslashes($this->db->f('lang'));
			$version = $this->db->f('version');
			$db2->query("DELETE FROM $this->PgTbl WHERE name='$name' AND lang='$lang' AND" .
			            " (version < $version OR body='') AND ".
			            intval(time()/86400-$this->ExpireLen).">supercede/86400",__LINE__,__FILE__);
			            //was "TO_DAYS(NOW()) - TO_DAYS(supercede) > $ExpireLen";
		}
the new code generates only one query, by using the fact the time == supercede for the up-to-date version */
		$this->db->query($sql="DELETE FROM $this->PgTbl WHERE (time != supercede OR body LIKE '') AND ".
			"supercede<".(time()-86400*$this->ExpireLen),__LINE__,__FILE__);

		if($this->RatePeriod)
		{
			$this->db->query("DELETE FROM $this->RtTbl WHERE ip NOT LIKE '%.*' AND " .
			                 intval(time()/86400)." > time/86400",__LINE__,__FILE__);
			                 //was "TO_DAYS(NOW()) > TO_DAYS(time)"
		}
	}

	/*!
	@function rateCheck
	@abstract Perform a lookup on an IP addresses edit-rate.
	@syntax rateCheck($type,$remote_addr)
	@param $type 'view',' search' or 'edit'
	@param $remote_addr eg. $_SERVER['REMOTE_ADDR']
	*/
	function rateCheck($type,$remote_addr)
	{
		if(!$this->RatePeriod)
		{ 
			return;
		}

		$this->db->lock($this->RtTbl,'WRITE');

		// Make sure this IP address hasn't been excluded.

		$fields = explode(".", $remote_addr);
		$this->db->query("SELECT * FROM $this->RtTbl WHERE ip='$fields[0].*'".
		                 " OR ip='$fields[0].$fields[1].*'".
		                 " OR ip='$fields[0].$fields[1].$fields[2].*'",__LINE__,__FILE__);
		
		if ($this->db->next_record())
		{
			global $ErrorDeniedAccess;
			die($ErrorDeniedAccess);
		}

		// Now check how many more actions we can perform.

		$this->db->query("SELECT time,". //was "TIME_TO_SEC(NOW()) - TIME_TO_SEC(time),"
		                 "viewLimit,searchLimit,editLimit FROM $this->RtTbl " .
		                 "WHERE ip='$remote_addr'",__LINE__,__FILE__);

		if(!$this->db->next_record())
		{ 
			$result = array(-1, $this->RateView, $this->RateSearch, $this->RateEdit); 
		}
		else
		{
			$result = $this->db->Record;

			$result[0] = time()-$result[0];
			if ($result[0]  < 0)
			{ 
				$result[0] = $this->RatePeriod; 
			}
			$result[1] = min($result[1] + $result[0] * $this->RateView / $this->RatePeriod,$this->RateView);
			$result[2] = min($result[2] + $result[0] * $this->RateSearch / $this->RatePeriod,$this->RateSearch);
			$result[3] = min($result[3] + $result[0] * $this->RateEdit / $this->RatePeriod,$this->RateEdit);
		}

		switch($type)
		{
			case 'view':	$result[1]--; break;
			case 'search':	$result[2]--; break;
			case 'edit':	$result[3]--; break;
		}
		if($result[1] < 0 || $result[2] < 0 || $result[3] < 0)
		{
			global $ErrorRateExceeded;
			die($ErrorRateExceeded);
		}

		// Record this action.

		if($result[0] == -1)
		{
			$this->db->query("INSERT INTO $this->RtTbl VALUES('$remote_addr',".time().	//was "NULL"
			                 ",$result[1],$result[2],$result[3])",__LINE__,__FILE__);
		}
		else
		{
			$this->db->query("UPDATE $this->RtTbl SET viewLimit=$result[1],searchLimit=$result[2],".
			                 " editLimit=$result[3],time=".time().
			                 " WHERE ip='$remote_addr'",__LINE__,__FILE__);
		}
		$this->db->unlock();
	}

	/*!
	@function rateBlockList
	@abstract Return a list of blocked address ranges.
	@syntax rateBlockList()
	*/
	function rateBlockList()
	{
		$list = array();

		if(!$this->RatePeriod)
		{ 
			return $list; 
		}
		$this->db->query("SELECT ip FROM $this->RtTbl",__LINE__,__FILE__);
		
		while($this->db->next_record())
		{
			if(preg_match('/^\\d+\\.(\\d+\\.(\\d+\\.)?)?\\*$/',$this->db->f('ip')))
			{ 
				$list[] = $this->db->f('ip');
			}
		}
		return $list;
	}

	/*!
	@function rateBlockAdd
	@abstract Block an address range.
	@syntax rateBlockAdd($address)
	@param $address ip-addr. or addr-range
	*/
	function rateBlockAdd($address)
	{
		if(preg_match('/^\\d+\\.(\\d+\\.(\\d+\\.)?)?\\*$/', $address))
		{
			$this->db->query("SELECT * FROM $this->RtTbl WHERE ip='$address'",__LINE__,__FILE__);

			if(!$this->db->next_record())
			{
				$this->db->query("INSERT INTO $this->RtTbl (ip,time) VALUES('$address',".time().")",__LINE__,__FILE__);
			}
		}
	}

	/*!
	@function rateBlockRemove
	@abstract Remove an address-range block.
	@syntax rateBlockRemove($address)
	@param $address ip-addr. or addr-range
	*/
	function rateBlockRemove($address)
	{
		$this->db->query("DELETE FROM $this->RtTbl WHERE ip='$address'",__LINE__,__FILE__);
	}
}
?>

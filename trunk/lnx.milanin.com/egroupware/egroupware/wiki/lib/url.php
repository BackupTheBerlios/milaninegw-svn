<?php
/* $Id: url.php,v 1.5 2004/04/12 13:02:07 ralfbecker Exp $ */

// Under phpgw these URL's are NOT configurable, you can set the phpgw install-path in setup

$ScriptBase = $GLOBALS['phpgw']->link('/wiki/index.php');
$ScriptBase .= strstr($ScriptBase,'?') ? '&' : '?';

$AdminScript = $ScriptBase . 'action=admin';

//if(!isset($ViewBase))
  { $ViewBase    = $ScriptBase . 'page='; }
//if(!isset($EditBase))
  { $EditBase    = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'wiki.uiwiki.edit')).'&page='; }
//if(!isset($HistoryBase))
  { $HistoryBase = $ScriptBase . 'action=history&page='; }
//if(!isset($FindScript))
  { $FindScript  = $ScriptBase . 'action=find'; }
//if(!isset($FindBase))
  { $FindBase    = $FindScript . '&find='; }
//if(!isset($SaveBase))
  { $SaveBase    = $ScriptBase . 'action=save&page='; }
//if(!isset($DiffScript))
  { $DiffScript  = $ScriptBase . 'action=diff'; }
//if(!isset($PrefsScript))
  { $PrefsScript = $ScriptBase . 'action=prefs'; }
//if(!isset($StyleScript))
  { $StyleScript = $ScriptBase . 'action=style'; }

if(!function_exists('viewURL'))
{
	function viewURL($page, $version = '', $full = '')
	{
		global $ViewBase;

		if (is_array($page))
		{
			$lang = @$page['lang'] && $page['lang'] != $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] ? '&lang='.$page['lang'] : '';
			$page = $page['name'];
		}
		return $ViewBase . urlencode($page) . @$lang .
				($version == '' ? '' : "&version=$version") .
				($full == '' ? '' : '&full=1');
	}
}

if(!function_exists('editURL'))
{
	function editURL($page, $version = '')
	{
		global $EditBase;

		if (is_array($page))
		{
			$lang = @$page['lang'] && $page['lang'] != $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] ? '&lang='.$page['lang'] : '';
			$page = $page['name'];
		}
		return $EditBase . urlencode($page) . @$lang .
				($version == '' ? '' : "&version=$version");
	}
}

if(!function_exists('historyURL'))
{
	function historyURL($page, $full = '',$lang='')
	{
		global $HistoryBase;

		if ($lang || (is_array($page) && isset($page['lang'])))
		{
			$lang = '&lang=' . ($lang ? $lang : $page['lang']);
		}
		return $HistoryBase . urlencode(is_array($page) ? $page['name'] : $page) . $lang;
				($full == '' ? '' : '&full=1');
	}
}

if(!function_exists('findURL'))
{
	function findURL($page,$lang='')
	{
		global $FindBase;

		if ($lang || (is_array($page) && isset($page['lang'])))
		{
			$lang = '&lang=' . ($lang ? $lang : $page['lang']);
		}
		return $FindBase . urlencode(is_array($page) ? $page['name'] : $page) . $lang;
	}
}

if(!function_exists('saveURL'))
{
	function saveURL($page)
	{
		global $SaveBase;

		return $SaveBase . urlencode($page);
	}
}

?>

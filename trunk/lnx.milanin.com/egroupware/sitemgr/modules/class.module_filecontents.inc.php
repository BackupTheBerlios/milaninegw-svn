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

	/* $Id: class.module_filecontents.inc.php,v 1.6.2.1 2005/02/27 08:20:35 ralfbecker Exp $ */

class module_filecontents extends Module 
{
	function module_filecontents()
	{
		$this->arguments = array(
			'filepath' => array(
				'type' => 'textfield', 
				'label' => lang('The complete URL or path to a file to be included'),
				'params' => array('size' => 50),
			)
		);
		$this->title = lang('File contents');
		$this->description = lang('This module includes the contents of an URL or file (readable by the webserver and in its docroot !)');
	}

	function get_content(&$arguments,$properties)
	{
		$url = parse_url($path = $arguments['filepath']);

		if (empty($path))
		{
			return '';
		}
		if (!$this->validate($arguments))
		{
			return $this->validation_error;
		}
		$is_html = preg_match('/\.html?$/i',$path);

		if ($this->is_script($path) || @$url['scheme'])
		{
			if (!@$url['scheme'])
			{
				$path = ($_SERVER['HTTPS'] ? 'https://' : 'http://') .
					($url['hostname'] ? $url['hostname'] : $_SERVER['HTTP_HOST']) .
					str_replace($_SERVER['DOCUMENT_ROOT'],'',$path);
			}
			if ($fp = fopen($path,'rb'))
			{
				$ret = '';
				while (!feof($fp))
				{
					$ret .= fread($fp,1024);
				}
				fclose ($fp);
				$is_html = True;
			}
			else
			{
				$ret = lang('File %1 is not readable by the webserver !!!',$path);
			}
		}
		else
		{
			$ret = implode('', file($path));
		}
		if ($is_html)
		{
			$one_line = str_replace("\n",'\\n',$ret);
			// only use what's between the body tags
			if (preg_match('/<body[^>]*>(.*)<\/body>/i',$one_line,$parts))
			{
				$ret = str_replace('\\n',"\n",$parts[1]);
			}
			if (preg_match('/<meta http-equiv="content-type" content="text\/html; ?charset=([^"]+)"/i',$one_line,$parts))
			{
				$ret = $GLOBALS['phpgw']->translation->convert($ret,$parts[1]);
			}
		}
		return $ret;
	}

	// test if $path lies within the webservers document-root
	//
	function in_docroot($path)
	{
		$docroots = array(PHPGW_SERVER_ROOT,$_SERVER['DOCUMENT_ROOT']);
		$path = realpath($path);

		foreach ($docroots as $docroot)
		{
			$len = strlen($docroot);

			if ($docroot == substr($path,0,$len))
			{
				$rest = substr($path,$len);

				if (!strlen($rest) || $rest[0] == DIRECTORY_SEPARATOR)
				{
					return True;
				}
			}
		}
		return False;
	}

	function is_script($url)
	{
		$url = parse_url($url);

		return preg_match('/\.(php.?|pl|py)$/i',$url['path']);
	}

	function validate(&$data)
	{
		$url = parse_url($data['filepath']);
		$allow_url_fopen = ini_get('allow_url_fopen');

		if ($url['scheme'] || $this->is_script($data['filepath']) && !$allow_url_fopen)
		{
			if (!$allow_url_fopen)
			{
				$this->validation_error = lang("Can't open an URL or execute a script, because allow_url_fopen is not set in your php.ini !!!");
				return false;
			}
			return True;
		}
		if (!is_readable($url['path']))
		{
			$this->validation_error = lang('File %1 is not readable by the webserver !!!',$data['filepath']);
			return false;
		}
		if (!$this->in_docroot($data['filepath']))
		{
			$this->validation_error = lang('File %1 is outside the docroot of the webserver !!!<br>This module does NOT allow - for security reasons - to open files outside the docroot.',$data['filepath']);
			return false;
		}
		return true;
	}
}

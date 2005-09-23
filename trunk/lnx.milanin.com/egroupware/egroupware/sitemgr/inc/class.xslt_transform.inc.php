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

	/* $Id: class.xslt_transform.inc.php,v 1.4 2004/05/02 18:13:30 ralfbecker Exp $ */

// some constanst for pre php4.3
if (!defined('PHP_SHLIB_SUFFIX'))
{
	define('PHP_SHLIB_SUFFIX',strtoupper(substr(PHP_OS, 0,3)) == 'WIN' ? 'dll' : 'so');
}
if (!defined('PHP_SHLIB_PREFIX'))
{
	define('PHP_SHLIB_PREFIX',PHP_SHLIB_SUFFIX == 'dll' ? 'php_' : '');
}

class xslt_transform
{
	var $arguments;

	function xslt_transform($xsltfile,$xsltparameters=NULL)
	{
		$this->xsltfile = $xsltfile;
		$this->xsltparameters = $xsltparameters;

		$this->xslt_extension_availible = extension_loaded('xslt') || @dl(PHP_SHLIB_PREFIX.'xslt.'.PHP_SHLIB_SUFFIX);
	}

	function apply_transform($title,$content)
	{
		if (!$this->xslt_extension_availible)
		{
			return 'The xslt_transformation used, needs the "xslt" extension of php !!!';
		}
		$xh = xslt_create();
		$xsltarguments = array('/_xml' => $content);
		$result = xslt_process($xh, 'arg:/_xml', $this->xsltfile, NULL, $xsltarguments,$this->xsltparameters);
		xslt_free($xh);
		return $result;
	}
}

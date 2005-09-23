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

	/* $Id: class.ui.inc.php,v 1.15 2004/06/15 08:09:38 ralfbecker Exp $ */

	class ui
	{
		var $t;
		

		function ui()
		{
			$themesel = $GLOBALS['sitemgr_info']['themesel'];
			$templateroot = $GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates' . SEP . $themesel;
			$this->t = new Template3($templateroot);
		}

		function displayPageByName($page_name)
		{
			global $objbo;
			global $page;
			$objbo->loadPage($GLOBALS['Common_BO']->pages->so->PageToID($page_name));
			$this->generatePage();
		}

		function displayPage($page_id)
		{
			global $objbo;
			$objbo->loadPage($page_id);
			$this->generatePage();
		}

		function displayIndex()
		{
			global $objbo;
			$objbo->loadIndex();
			$this->generatePage();
		}

		function displayTOC($categoryid=false)
		{
			global $objbo;
			$objbo->loadTOC($categoryid);
			$this->generatePage();
		}

		function generatePage()
		{
			// add a content-type header to overwrite an existing default charset in apache (AddDefaultCharset directiv)
			header('Content-type: text/html; charset='.$GLOBALS['phpgw']->translation->charset());

			echo $this->t->parse();
		}

	}
?>

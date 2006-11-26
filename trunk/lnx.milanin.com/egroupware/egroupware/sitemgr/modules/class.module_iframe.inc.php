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

	/* $Id: class.module_hello.inc.php,v 1.3 2004/02/10 14:56:33 ralfbecker Exp $ */

class module_iframe extends Module 
{
	function module_iframe()
	{
		$this->arguments = array(
                        'title' => array(
				'type' => 'textfield', 
				'label' => lang('IFRAME title')
    			),
			'source' => array(
				'type' => 'textfield', 
				'label' => lang('IFRAME source')
    			),
                        'class' => array(
				'type' => 'textfield', 
				'label' => lang('IFRAME class')
    			),
                        'frameborder' => array(
				'type' => 'textfield', 
				'label' => lang('IFRAME border')
    			),
                        'scrolling' => array(
				'type' => 'textfield', 
				'label' => lang('IFRAME scrolling')
    			),
                        'align' => array(
				'type' => 'textfield', 
				'label' => lang('IFRAME align')
    			),
                        'width' => array(
				'type' => 'textfield', 
				'label' => lang('IFRAME width')
    			),
                        'height' => array(
				'type' => 'textfield', 
				'label' => lang('IFRAME height')
			),
                        'center' => array(
				'type' => 'checkbox', 
				'label' => lang('Wrap in "center" tag')
			)
		);
                $this->properties = array();
		$this->title = lang('IFRAME module');
		$this->description = lang('This is a simple IFRAME module');
	}

	function get_content(&$arguments,$properties) 
	{
                if ($arguments['center']){
                  $iframe='<center>';
                }
		$iframe.='<iframe src="'.
                            $arguments['source'].
                            '" title="'.
                            lang($arguments['title']).
                            '" width="'.
                            $arguments['width'].
                            '" height="'.
                            $arguments['height'].
                            '" class="'.
                            $arguments['class'].
                            '" align="'.
                            $arguments['align'].
                            '" frameborder="'.
                            $arguments['frameborder'].
                            '" scrolling="'.
                            $arguments['scrolling'].
                            '"><!-- Alternate content for non-supporting browsers --><h2>'.
                            lang("Your browser can't show IFRAME").'</h2></iframe>';
                if ($arguments['center']){
                  $iframe.='</center>';
                }
                return $iframe;
	}
}

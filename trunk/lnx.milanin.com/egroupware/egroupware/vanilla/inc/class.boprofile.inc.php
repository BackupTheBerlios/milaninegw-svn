<?php
	/**************************************************************************\
	* eGroupWare - Messenger                                                   *
	* http://www.egroupware.org                                                *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.bomessenger.inc.php,v 1.12.2.2 2004/08/18 11:56:44 reinerj Exp $ */

	class boprofile
	{
		var $so;
		var $public_functions = array(
			'get_relative_percentage'          => True,
		);
		function boprofile()
		{
			$this->so = CreateObject('profile.soprofile');
		}
                function get_relative_percentage()
                {
                	$percentage=$this->so->get_relative_percentage();
                        if ($percentage<70 && $percentage >30){
                        	$color='yellow';
                        }elseif ($percentage>=70){
                        	$color='green';
                        }else{
                        	$color='red';
                        }
                	return '<div style="text-align:left;width:100px;height:10px;border:solid 1px">
        			  <div style="height:10px;width:'.
                                  $percentage.
                                  'px;background:'.$color.'"></div></div><span>'.
                                  lang('your profile is').' <span style="color:'.$color.'">'.
                                  $percentage."%</span> ".lang('complete')."</span>";
                }
	}

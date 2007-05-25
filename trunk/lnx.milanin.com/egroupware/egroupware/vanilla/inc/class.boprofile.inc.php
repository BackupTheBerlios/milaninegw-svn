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
                var $members_views;
                var $guests_views;
                var $members_url;
                var $relative_percentage;
		var $public_functions = array(
			'get_relative_percentage'          => True,
		);
		function boprofile()
		{
			$this->so = CreateObject('profile.soprofile');
                        $this->members_url=((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? "https" : "http").'://www.milanin.com/members/';
          		$this->get_members_views();
          		$this->get_guests_views();
          		$this->relative_percentage=$this->so->get_relative_percentage();
		}
                function get_relative_percentage()
                {
                	
                        if ($this->relative_percentage<70 && $this->relative_percentage >30){
                        	$color='yellow';
                        }elseif ($this->relative_percentage>=70){
                        	$color='green';
                        }else{
                        	$color='red';
                        }
                	return '<div style="text-align:left;width:100px;height:10px;border:solid 1px">
        			  <div style="height:10px;width:'.
                                  $this->relative_percentage.
                                  'px;background:'.$color.'"></div></div><span>'.
                                  lang('your profile is').' <span style="color:'.$color.'">'.
                                  $this->relative_percentage."%</span> ".lang('complete')."</span>";
                }
                function get_members_views()
                {
                	foreach ($this->so->get_members_views() as $v){
                          $v['icon']='<a href="'.$this->members_url.$v['user'].
                          '" title="'.lang("view profile of").' '.$v['name'].'">'.
                          '<img src="'.$this->members_url.
                          ((isset($v['icon']) && $v['icon']>0) 
                            ? '_icons/data/'.$v['icon'] 
                            : '_icons/data/default.png'
                          ).
                          '" alt="'.lang('icon of').' '.$v['name'].'"/></a>';
                          $v['name']='<a href="'.$this->members_url.$v['user'].'" title="'.lang("view profile of").' '.$v['name'].'">'.$v['name'].'</a>';
                          
                          $this->members_views[]=$v;
                        }
                }
                function get_guests_views()
                {
                	foreach ($this->so->get_guests_views() as $v){
                          if (preg_match('/:\/\/([A-Za-z0-9-.]*)\//',$v['referral'],$v['name'])){
                            $v['name']=$v['name'][1];
                            $v['referral']='<a href="'.$v['referral'].
                                          '" title="'.$v['name'].'">'.
                                          $v['name'].'</a>';
                            $this->guests_views[]=$v;
                        }
                       }
                }
	}

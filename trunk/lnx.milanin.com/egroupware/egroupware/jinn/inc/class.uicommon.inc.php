<?php
	/*
	JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
	Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

	phpGroupWare - http://www.phpgroupware.org

	This file is part of JiNN

	JiNN is free software; you can redistribute it and/or modify it under
	the terms of the GNU General Public License as published by the Free
	Software Foundation; either version 2 of the License, or (at your 
	option) any later version.

	JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
	WARRANTY; without even the implied warranty of MERCHANTABILITY or 
	FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License 
	along with JiNN; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
	*/

	/* main ui class for user as administrator */


	class uicommon
	{

		var $app_title;
		var $template;
		var $message;

		function uicommon()
		{
			$this->template = $GLOBALS['phpgw']->template;
		}

		/**
		* header renders the app & screen title,
		* 
		* @param string $screen_title 
		*/
		function header($screen_title,$phpgw_header=true)
		{
			unset($GLOBALS['phpgw_info']['flags']['noheader']);
			unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
			unset($GLOBALS['phpgw_info']['flags']['noappheader']);
			unset($GLOBALS['phpgw_info']['flags']['noappfooter']);
			if($this->app_title) $extra_title =' ('.$this->app_title.')';
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps']['jinn']['title']. ' - '.$screen_title . $extra_title;

			if($phpgw_header)$GLOBALS['phpgw']->common->phpgw_header();
		}



		/**
		* format a standard msg_box with 
		* 
		* print errors in a red font and info messages in green
		*
		* @param array $msg_arr ['info'] contains the info msg and ['error'] the error msg
		*/
		function msg_box($msg_arr)
		{
			if ($msg_arr['info']) $info='<span style="color:green">'.$msg_arr['info'].'</span><br/>';
			if ($msg_arr['help']) $help='<span style="color:blue">'.$msg_arr['help'].'</span><br/>';
			if ($msg_arr['error']) $error='<span style="color:red">'.$msg_arr['error'].'</span><br/>';

			if($info || $error || $help)
			{
				$this->template->set_file(array(
					'msg_box' => 'msg_box.tpl'
				));

				$this->template->set_var('help',$help);
				$this->template->set_var('error',$error);
				$this->template->set_var('info',$info);
				$this->template->pparse('out','msg_box');
			}
		}


		/**
		* returns the options of a selectbox
		* 
		* @return string html formatted options 
		* @param array $list_array array with values and names for the options 
		* @param mixed $selected_value value that must be selected
		* @param boolean $allow_empty allow emty options
		*/
		function select_options($list_array,$selected_value,$allow_empty=false)
		{
			if($allow_empty) $options.="<option value=\"\">------------------</option>\n";

			if(is_array($list_array))
			{
				foreach ( $list_array as $array ) {

					unset($SELECTED);
					if ($array[value]==$selected_value)
					{
						$SELECTED='SELECTED';
					}				
					if ($array[name]) $name = $array[name];
					else $name = $array[value];


					$options.="<option value=\"".$array[value]."\" $SELECTED>".stripslashes($name)."</option>\n";
				}

			}
			return $options;
		}

	}		

	?>

<?php
	/*
	JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for phpGroupWare
	Copyright (C)2002, 2003 Pim Snel <pim@lingewoud.nl>

	eGroupWare - http://www.egroupware.org

	This file is part of JiNN

	JiNN is free software; you can redistribute it and/or modify it under
	the terms of the GNU General Public License as published by the Free
	Software Foundation; version 2 of the License.

	JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
	WARRANTY; without even the implied warranty of MERCHANTABILITY or 
	FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
	for more details.

	You should have received a copy of the GNU General Public License 
	along with JiNN; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
	*/

	class uiumedia 
	{
		var $public_functions = Array
		(
			'index'				=> True,
			''		=> True,
			''		=> True,
			''		=> True,
			''	=> True
		);

		var $bo;
		var $ui;
		var $template;
		var $test_dirs=array('/normal_size','/attachments');	
		var $dir_arr = array();

		function uiumedia()
		{
			$this->bo = CreateObject('jinn.bouser');

			$this->template = $GLOBALS['phpgw']->template;

			$this->ui = CreateObject('jinn.uicommon');
			if($this->bo->so->config[server_type]=='dev')
			{
				$dev_title_string='<font color="red">'.lang('Development Server').'</font> ';
			}
			$this->ui->app_title=$dev_title_string.lang('Moderator Mode');
		}

		/********************************
		*  create the default index page                                                          
		*/
		function index()
		{
			// mag gebruiker media beheren ja/nee
			if($not_allowed)
			{
				die(lang('You\'re not allowed to administrate media files'));		
			}

			if ($this->bo->site_id )
			{
				$this->bo->save_sessiondata();

				$this->create_dir_array();

				if(count($this->dir_arr)==0)
				{
					die(lang('There are no directories in this site you have access to.'));
				}

				$this->filelisting();

				//$this->bo->common->exit_and_open_screen('jinn.uiumedia.filelisting');
			}
			else
			{

				if (!$this->bo->site_id)
				{
					$this->bo->message['info']=lang('Select site to moderate');
				}
				/*				else //if(!$this->bo->site_object_id)
				{
					$this->bo->message['info']=lang('Select site-object to moderate');
				}
				*/
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				unset($GLOBALS['phpgw_info']['flags']['noappheader']);
				unset($GLOBALS['phpgw_info']['flags']['noappfooter']);

				$this->ui->header('Index');
				$this->ui->msg_box($this->bo->message);

				$this->main_menu();
				$this->bo->save_sessiondata();
			}
		}

		/****************************************************************************\
		* create main menu                                                           *
		\****************************************************************************/

		function main_menu()
		{
			$this->template->set_file(array(
				'main_menu' => 'main_menu.tpl'));

				// get sites for user and group and make options
				$sites=$this->bo->common->get_sites_allowed($GLOBALS['phpgw_info']['user']['account_id']);

				if(is_array($sites))
				{
					foreach($sites as $site_id)
					{
						$site_arr[]=array(
							'value'=>$site_id,
							'name'=>$this->bo->so->get_site_name($site_id)
						);
					}
				}

				$site_options=$this->ui->select_options($site_arr,$this->bo->site_id,true);

				if ($this->bo->site_id)
				{
					$objects=$this->bo->common->get_objects_allowed($this->bo->site_id, $GLOBALS['phpgw_info']['user']['account_id']);

					if (is_array($objects))
					{
						foreach ( $objects as $object_id) 
						{
							$objects_arr[]=array(
								'value'=>$object_id,
								'name'=>$this->bo->so->get_object_name($object_id)
							);
						}
					}

					$object_options=$this->ui->select_options($objects_arr,$this->bo->site_object_id,true);

				}
				else
				{
					unset($this->bo->site_object_id);
				}

				// set theme_colors
				$this->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
				$this->template->set_var('th_text',$GLOBALS['phpgw_info']['theme']['th_text']);
				$this->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
				$this->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);

				// set menu
				$this->template->set_var('site_objects',$object_options);
				$this->template->set_var('site_options',$site_options);

				$this->template->set_var('main_form_action',$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiuser.index'));
				$this->template->set_var('select_site',lang('select site'));
				//$this->template->set_var('select_object',lang('select_object'));
				$this->template->set_var('go',lang('go'));

				/* set admin shortcuts */
				// if site if site admin
				if($this->bo->site_id && $userisadmin)
				{
					$admin_site_link='<br><a href="'.$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiadminaddedit.').'">'.
					lang('admin:: edit site').'</a>';
				}
				$this->template->set_var('admin_site_link',$admin_site_link);
				$this->template->set_var('admin_object_link',$admin_object_link);

				$this->template->pparse('out','main_menu');

			}


			function create_dir_array()
			{
				$id=0;
				$objects=$this->bo->common->get_objects_allowed($this->bo->site_id, $GLOBALS['phpgw_info']['user']['account_id']);


				if(is_dir($this->bo->site[cur_upload_path].$test_dir) && $this->bo->site[cur_upload_url])
				{
					foreach($this->test_dirs as $test_dir)
					{

						$this->dir_arr[]=array(
							'id'=>$id++,
							'name'=>$this->bo->site[site_name],
							'type'=>'site',
							'ftype'=>$test_dir,
							'path'=>$this->bo->site[cur_upload_path].$test_dir,
							'url'=>$this->bo->site[cur_upload_url].$test_dir

						);
					}

				}				


				foreach($objects as $obj)
				{
					$obj_vals=$this->bo->so->get_object_values($obj);

					foreach($this->test_dirs as $test_dir)
					{
						if($obj_vals[cur_upload_path] && is_dir($obj_vals[cur_upload_path].$test_dir)	&& $obj_vals[cur_upload_url])
						{
							$this->dir_arr[]=array(
								'id'=>$id++,
								'name'=>$obj_vals[name],
								'type'=>'object',
								'ftype'=>$test_dir,
								'path'=>$obj_vals[cur_upload_path].$test_dir,
								'url'=>$obj_vals[cur_upload_url].$test_dir
							);
						}				
					}
				}


			}	



			//twee secties: images/thumbs en attachments files

			// controleer of gebruiker media mag beheren
			// geef per sectie all directories

			// klik directory geef filelisting weer
			// button check unused files en geef deze rood weer met de optie ze te verwijderen.
			// per file thumb aanwezig? zoja link edit thumbnail
			// per fie link delete file maar dat kan alleen als blijkt dat dit bestand ongebruikt blijkt

			function filelisting()
			{
				$this->ui->header('Media And Documents Adminstration');
				$this->ui->msg_box($this->bo->message);
				$this->main_menu();	

				//  if directory show directory else show sections with all directories
				// FIXME register globals off
				if(!isset($GLOBALS['dir']))
				{

					foreach($this->dir_arr as $dir)
					{
						if(is_dir($dir[path]))
						{
							$link.='<li>';	
							$link .='<a href="'.$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.uiumedia.index&dir='.$dir[id]).'">'.$dir[type].' > '.$dir[name].'>'.$dir[ftype].'</a>';
						}
					}

					echo $link;
					//laat dirs zien als link 
				}
				else
				{
				// FIXME register globals off
					echo $GLOBALS[dir];

				// FIXME register globals off
					if($this->dir_arr[$GLOBALS[dir]][ftype]=='/normal_size')
					{
				// FIXME register globals off
						$list=$this->getImgFileList($this->dir_arr[$GLOBALS[dir]][path]);

						$this->show_image_list($list[0],$list[1]);

					}
					else
					{
				// FIXME register globals off
						$list=$this->getFileList($this->dir_arr[$GLOBALS[dir]][path]);

					}
					//					_debug_array($list);
					// check dir type (image or attachtments)
					// list dir
				}


				$this->bo->save_sessiondata();
			}


			// Open images directory and splitting the
			// thumbnails from the original in 2 arrays.
			function getImgFileList($cur_dir) {
								// FIXME register globals off
				global $Cfg, $fileArray, $thumbArray;

				$fileArray = Array();
				$thumbArray = Array();

				$uploadPath = $cur_dir.'/';//sprintf('../%s',$Cfg['upload_path']);
				if (is_dir($uploadPath.'../thumb')) $thumb = true;
				else $thumbs = false;

				$d = opendir($uploadPath) or die(lang('error path_open %1',$uploadPath));
				while(false !== ($f = readdir($d))) {
					if(is_file($uploadPath.$f)) {
						$fileSize = round(filesize($uploadPath.$f)/1024);
						$fileDate = filemtime($uploadPath.$f);
						$fileDate = $this->format_date(date('Y-m-d-H-i', $fileDate),'%day%-%month%-%ye% %hour24%:%minute%');

						// thumbs
						if($thumb && is_file($uploadPath.'../thumb/'.$f))
						{
							$tmp = getImageSize($uploadPath.'../thumb/'.$f);
							$tmp = Array('name'=>$f,'width'=>$tmp[0],'height'=>$tmp[1],'date'=>$fileDate,'size'=>$fileSize);
							array_push($thumbArray,$tmp);
							$has_thumb=true;
						}
						else
						{
							$has_thumb=false;
						}

						// normal_size
						$tmp = getImageSize($uploadPath.$f);
						$tmp = Array('name'=>urlencode($f), 'width'=>$tmp[0], 'height'=>$tmp[1], 'date'=>$fileDate, 'size'=>$fileSize,'thumb'=>$has_thumb);
						array_push($fileArray,$tmp);


					}
				}
				closedir($d);

				usort($fileArray, 'filearray_sort');

				return array($fileArray, $thumbArray);
			}


			function show_image_list($fileArray,$thumbArray)
			{
				// FIXME register globals off
				global $Cfg, $Pivot_Vars, $base_url;


				printf('<script>function pop(a){window.open("modules/module_image.php?image="+a,"%s","toolbar=no,resizable=yes,scrollbars=yes,width=520,height=550");}</script>',$file['name']);

				$myurl =sprintf("index.php?session=%s&menu=files&doaction=1", $Pivot_Vars['session']);
				printf("<form name='form1' method='post' action='%s'>", $myurl);

				echo '<table cellspacing="0" class="tabular_border">';

				printf('<tr class="tabular_header"><td>&nbsp;</td><td width="200">%s</td><td>%s</td><td width="100">%s</td><td>%s</td><td>%s</td></tr>',lang('filename'),lang('thumbnail'),lang('date'),lang('filesize'),lang('dimensions'));


				foreach($fileArray as $key => $file) {

					$fullentry = urlencode($this->fixpath(sprintf('%s../%s%s',$base_url ,$Cfg['upload_path'],$file['name'])));
//					$thumb = $this->check_for_common($file['name']);

					if (!isset($linecount)) {
						$linecount=1;
					} else {
						$linecount++;
					}

					if (($linecount % 2)==0) {
						$bg_color="tabular_line_even";
					} else {
						$bg_color="tabular_line_odd";
					}

					$url=sprintf($base_url."/includes/photo.php?img=%s&w=%s&h=%s&t=%s",$fullentry, $file['width'], $file['height'], $file['name']);
					$view_html = sprintf("<a href='%s' onclick=\"window.open('%s', 'imagewindow', 'width=%s, height=%s, directories=no, location=no, menubar=no, scrollbars=no, status=no, toolbar=no, resizable=no');return false\" target='_self' title='%s'>", urldecode($fullentry), $url, $file['width'], $file['height'], $file['name'] );

					printf('<tr class="%s">',$bg_color);
					printf("<td><input type='checkbox' name='check[%s]'></td>",$file['name']);
					printf('<td>%s%s</a></td>',$view_html, $this->trimtext(strtolower(urldecode($file['name'])), 40, TRUE));

					if($file['thumb']) {
						printf('<td><a href=javascript:pop("%s");>' . lang('edit') . '</a></td>',$file['name']);
					} else {
						printf('<td><a href=javascript:pop("%s");>' . lang('create') . '</a></td>',$file['name']);
					}

					printf('<td>%s</td><td>%d KB</td><td>%d x %d</td></tr>',$file['date'],$file['size'],$file['width'],$file['height']);
				}

				echo '<tr class="tabular_nav"><td colspan="7"><img src="pics/arrow_ltr.gif" width="29" height="14" border="0" alt="">';
				echo '<a href="#" onclick=\'setCheckboxes("form1", true); return false;\'>'. lang('c_all') .'</a> / ';
				echo '<a href="#" onclick=\'setCheckboxes("form1", false); return false;\'>'. lang('c_none') .'</a>';
				echo '&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;'. lang('with_checked_files');
				echo '<select name="action" class="input"><option value="" selected>'. lang('choose') .'</option><option value="delete">'. lang( 'delete') .'</option></select>';
				echo '&nbsp;&nbsp;<input type="submit" value="'. lang('go') .'" class="button">';
				printf("</table></form>");

				$this->GenSetting('',lang('thisfile'),'',8,'',6);
				$this->StartForm('file_upload', 0, 'enctype="multipart/form-data"');
				printf('<input name="%s" type="file"  class="input"><br />',$Cfg['upload_file_name']);
				printf('<input type="submit" value="%s" class="button"></form>',lang('go'));

				//																																																																																																																													PageFooter();
			}


				//used before forms.
					function StartForm($whereto='', $admin='0', $special='') {
								global $Pivot_Vars;
										if($admin==1 || ( isset($Pivot_Vars['func']) && $Pivot_Vars['func']=='admin')){
														$whereto = 'admin&do='.$whereto;
																}
																		echo '<form method="post" '.$special.' action="index.php?menu='.$Pivot_Vars['menu'].'&func='.$whereto.'" class="nopadding">'."\n";
																				if(strlen($Pivot_Vars['session']) == 12) {
																								echo '<input type="hidden" name="session" value="'.$Pivot_Vars['session'].'">'."\n";
																										}
																											}


			function GenSetting($fieldname='none', $dispname='none given', $description='', $type='0', $defvalue='', $length='30', $additional='', $cfgvar='Cfg') {
				global $$cfgvar, $lang;
				if($type < 7) {
					$settingdisp = '<tr><td width="25%"  valign="top"><b class="tabular" style="padding-right: 7px;">'.$dispname.':</b></td>
					<td width="75%">';
				}	else {
					$settingdisp = "";
				}
				// we do other stuff in the case's themselves because it's just easier.
				switch($type) {
					case 0:
						$settingdisp .= GenTextfield($fieldname, $length, $additional, $defvalue, $cfgvar);
						break;
					case 1:
						$settingdisp .= GenPassfield($fieldname, $length, $additional, $cfgvar);
						break;
					case 2:
						$settingdisp .= GenCheckbox($fieldname, $additional, $defvalue, $cfgvar);
						break;
					case 3:
						$settingdisp .= GenDropdown($fieldname, $additional, $defvalue, $cfgvar);
						break;
					case 4:
						$settingdisp .= GenSelectbox($fieldname, $length, $defvalue, $cfgvar);
						break;
					case 5:
						$settingdisp .= GenTextarea($fieldname, $length, $additional, $defvalue, $cfgvar);
						break;
					case 6:
						$settingdisp .= GenRadios($fieldname, $defvalue, $cfgvar);
						break;
					case 7:
						$settingdisp .= GenHidden($fieldname, $additional, $defvalue, $cfgvar);
						break;
					case 8:
						$settingdisp = '<tr><td class="sHeading" colspan="'.$length.'"><hr size=1 noshade><h2 style="margin-left:0px;">'.$dispname.'</h2></td></tr>';
						break;

					}
					if($type < 7) {
						$settingdisp .= '</td></tr>';
						if(strlen($description) > strlen($dispname)){
							$settingdisp .= '<tr><td width="85%" colspan="2" style="padding-left: 18%; padding-bottom: 7px;">'.str_replace("&nbsp;", " ", $description) .'</td></tr>'."\n";
						}
					}
					echo $settingdisp;
				}

				// fixPath fixes a relative path eg. '/site/pivot/../index.php' becomes '/site/index.php';
				function fixPath($path) {
					$path      = ereg_replace('/+', '/', $path);
					$patharray = explode('/', $path);
					foreach ($patharray as $item) {
						if ($item == "..") {
							// remove the previous element
							@array_pop($new_path);
						} else if ( ($item != ".") ) {
							$new_path[]=$item;
						}
					}
					return implode("/", $new_path);
				}


				function format_date( $date="", $format="") {
					global $Cfg, $current_date, $db;

					if ($format=="") { $format="%day% %monname% '%ye%"; }
					if ($date=="") {$date= date("Y-m-d-H-i", get_current_date()); }
					list($yr,$mo,$da,$ho,$mi)=split("-",$date);

					$mktime = mktime(1,1,1,$mo,$da,$yr);

					$ho12 = ($ho>11) ? $ho - 12 : $ho;
					$ampm= ($ho12==$ho) ? "am" : "pm";
					if ($ho12==0) { $ho12=12; }

					$format=str_replace("%minute%", $mi, $format);
					$format=str_replace("%hour12%", $ho12, $format);
					$format=str_replace("%ampm%", $ampm, $format);
					$format=str_replace("%hour24%", $ho, $format);
					$format=str_replace("%day%", $da, $format);
					$format=str_replace("%daynum%", @date("w",$mktime), $format);
					$format=str_replace("%dayname%", lang ("days" , @date("w",$mktime)), $format);
					$format=str_replace("%weekday%", lang ("days" , @date("w",$mktime)), $format);
					$format=str_replace("%weeknum%", @date("W",$mktime), $format);
					$format=str_replace("%month%", $mo, $format);
					$format=str_replace("%monthname%", lang('months', -1+$mo), $format);
					$format=str_replace("%monname%", lang('months_abbr', -1+$mo), $format);
					$format=str_replace("%year%", $yr, $format);
					$format=str_replace("%ye%", substr($yr,2), $format);
					//debug("format: $date, $format");

					//while not part of 'dates', we also replace %title% with the
					//entry's, suitable for use in filenames
					//				$format=@str_replace("%title%", safe_string($db->entry['title'],TRUE) , $format);

					return $format;
				}



/*
				function check_for_common($str)
				{
					global $thumbArray;

					// first we split up the extension from filename
					// with a simple regexp... god i love regexp :-)
					preg_match('/^(.*)\.(.*)$/i',$str,$match);
					$compare = strtolower($match[1]);

					foreach($thumbArray as $val) {

						$thumb = strtolower(substr($val['name'],0,strlen($compare)));

						if($compare == $thumb) {
							// MATCH!!!
							$str = $val['name'];
						}
					}
					return $str;
				}
*/
				// Trim a text to a given length, taking html entities into account.
				function trimtext($str, $length, $nbsp=FALSE) {

					$str = strip_tags($str);

					if (strlen($str)>$length) {
						$str = unentify($str);
						$str=substr($str,0,$length+1);
						$str = entify($str)."&hellip;";
					}

					if ($nbsp==TRUE) {
						$str=str_replace(" ", "&nbsp;", $str);
					}

					$str=str_replace("http://", "", $str);

					return $str;

				}




			}

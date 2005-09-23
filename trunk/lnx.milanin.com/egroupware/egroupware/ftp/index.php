<?php
	/**************************************************************************\
	* eGroupWare - Ftp Module                                                  *  
	* http://www.egroupware.org                                                *
	* Written by Scott Moser <smoser@brickies.net>                             *
	* --------------------------------------------                             *
	* modified by IFOM-IEO Campus DentroWeb Team:                              *
	*          pinolallo <silvestro.dipietro@ifom-ieo-campus.it>               *
	*          kahuna    <carlo.comolli@ifom-ieo-campus.it>                    *
	*          lobosky   <mauro.donadello@ifom-ieo-campus.it>                  *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: index.php,v 1.21.2.2 2005/02/12 19:52:25 ralfbecker Exp $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'              => 'ftp',
		'enable_nextmatchs_class' => True
	);

	if(isset($_GET['action']) && ($_GET['action'] == 'get' || $_GET['action'] == 'view'))
	{
		$GLOBALS['phpgw_info']['flags']['nonavbar'] = True;
		$GLOBALS['phpgw_info']['flags']['noheader'] = True;
	}
	include('../header.inc.php');

	$action = get_var('action',array('GET','POST'));
	$start  = (int)get_var('start',array('GET','POST'));
	$username  = get_var('username','POST');
	$password  = get_var('password','POST');
	$ftpserver = get_var('ftpserver','POST');
	$file   = urldecode(get_var('file','GET'));
	$newdir = urldecode(get_var('newdir','GET'));
	$olddir = urldecode(get_var('olddir','GET'));
	$newdirname = urldecode(get_var('newdirname','GET'));

	$remoteuploaddir = urldecode(get_var('olddir','POST'));
	$remotenewdirname = urldecode(get_var('newdirname','POST'));

	$default_login  = $GLOBALS['phpgw_info']['user']['account_lid'];
	$default_pass   = $GLOBALS['phpgw']->session->appsession('password','phpgwapi');
	$default_server = $GLOBALS['phpgw_info']['server']['default_ftp_server'];

//	_debug_array($_POST);exit;
	$sessionUpdated = False;

	$em_bg      = $GLOBALS['phpgw_info']['theme']['table_bg'];
	$em_bg_text = $GLOBALS['phpgw_info']['theme']['table_text'];
	$bgcolor[0] = $GLOBALS['phpgw_info']['theme']['row_on'];
	$bgcolor[1] = $GLOBALS['phpgw_info']['theme']['row_off'];
	$tempdir    = $GLOBALS['phpgw_info']['server']['temp_dir'];

	$GLOBALS['target'] = '/' . $GLOBALS['phpgw_info']['flags']['currentapp'] . '/index.php';

	$GLOBALS['phpgw']->template->set_file(array(
		'main_'  => 'main.tpl',
		'login'  => 'login.tpl',
		'rename' => 'rename.tpl',
		'confirm_delete' => 'confirm_delete.tpl',
		'bad_connect' => 'bad_connection.tpl'
	));
	$GLOBALS['phpgw']->template->set_var(array(
		'em_bgcolor' => $em_bg,
		'em_text_color' => $em_bg_text,
		'bgcolor' => $bgcolor[0]
	));

	$GLOBALS['phpgw']->template->set_block('main_','main');
	$GLOBALS['phpgw']->template->set_block('main_','row');
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
	$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);

	$GLOBALS['phpgw']->template->set_var('module_name',lang('Ftp Client'));

	if($action == '' || $action == 'login')
	{
		// if theres no action, try to login to default host with user and pass
		if($action == 'login') 
		{
			// username, ftpserver and password should have been passed in
			// via POST
			$connInfo['username']  = $username;
			$connInfo['password']  = $password;
			$connInfo['ftpserver'] = $ftpserver;
		}
		else
		{
			// try to default with session id and passwd
			if(!($connInfo = getConnectionInfo()))
			{
				$connInfo['username']  = $default_login;
				$connInfo['password']  = $default_pass;
				$connInfo['ftpserver'] = $default_server;

				$tried_default = True;
			}
		}
		updateSession($connInfo);
		$sessionUpdated = True;
	}

	if($action != 'newlogin')
	{
		if(empty($connInfo))
		{
			$connInfo = getConnectionInfo();
		}
		$ftp = @phpftp_connect($connInfo['ftpserver'],$connInfo['username'],$connInfo['password']);
		if($ftp)
		{
			$homedir = ftp_pwd($ftp);
			$retval  = ftp_pasv($ftp,1);
			
			if($action == 'delete' || $action == 'rmdir')
			{				
				$remotedir = $_POST['olddir'];
				$remotefile = $_POST['file'];
				if($_POST['confirm'])
				{
				//$retval = ftp_delete($ftp,$olddir . '/' . $file);
					if($action == 'delete')
						$retval = ftp_delete($ftp,$remotedir . '/' . $remotefile);
					else
						$retval = ftp_rmdir($ftp,$remotedir . '/' . $remotefile);
	
					if($retval)
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('successfully deleted %1',"$remotedir/$remotefile"), True);
					else
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('failed to delete %1', "$remotedir/$remotefile"), True);
					
					$olddir = $remotedir;
				}
				elseif(!$_POST['cancel'])
				{
				  $GLOBALS['phpgw']->template->set_var('misc_data',confirmDeleteForm($action,$file,$olddir,$action),true);
	       // $olddir = $remotedir;				  
				}
			}

			if($action == 'rename')
			{
				if($_POST['confirm'])
				{
					$olddir = $_POST['olddir'];
					$filename = $olddir . '/' . $_POST['filename'];
					$newfilename = $olddir . '/' . $_POST['newfilename'];
				
					if(@ftp_rename($ftp,$filename, $newfilename))
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('renamed %1 to %2',$_POST['filename'], $_POST['newfilename']), True);
					else
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('failed to rename %1 to %2', $_POST['filename'],$_POST['newfilename'] ), True);
				}
				else
					$GLOBALS['phpgw']->template->set_var('misc_data', renameForm($action,$file,$olddir), true);
			}

			if($action == 'get')
			{
				phpftp_get($ftp,$tempdir,$olddir,$file);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			if($action == 'view')
			{
				phpftp_view($ftp,$tempdir,$olddir,$file);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if($action == 'upload')
			{
				$newfile = $_FILES['uploadfile']['name'];
				$uploadfile = $_FILES['uploadfile']['tmp_name'];
				
				$newfile = $remoteuploaddir . '/' . $newfile;
				//$newfile = $olddir . '/' . $uploadfile_name;

				if($_FILES['uploadfile']['name'] != '')
				{
					if(ftp_size($ftp,$newfile)==-1)
					{
						if(@ftp_put($ftp,$newfile, $uploadfile, FTP_BINARY))
							$GLOBALS['phpgw']->template->set_var('misc_data',lang('successfully uploaded %1',$newfile), True);
						else
							$GLOBALS['phpgw']->template->set_var('misc_data',lang('failed to upload %1',$newfile), True);
					}
					else
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('file %1 already exists!',$newfile), True);          
					unlink($uploadfile);
					$olddir = $remoteuploaddir;
				}
				else
					$GLOBALS['phpgw']->template->set_var('misc_data',lang('attempt to upload a file with empty name'),True);
			}

			if($action == 'mkdir')
			{
				if($remotenewdirname != '')
				{
					if(ftp_size($ftp,$newfile)==-1)
					{
						if(ftp_mkdir($ftp,$remoteuploaddir . '/' . $remotenewdirname)) 
						{
							$olddir = $remoteuploaddir;
							$newdir = $remotenewdirname;
							$action = 'cwd';
							$GLOBALS['phpgw']->template->set_var('misc_data',lang('successfully created directory %1',"$remoteuploaddir/$remotenewdirname"), True);
						}
						else
							$GLOBALS['phpgw']->template->set_var('misc_data',lang('failed to create directory %1',"$remoteuploaddir/$remotenewdirname"), True);
					}
					else
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('file %1 already exists!',$newfile), True);          
					$olddir = $remoteuploaddir;
				}
				else
					$GLOBALS['phpgw']->template->set_var('misc_data',lang('attempt to create a directory with empty name'),True);
			}

			/* here's where most of the work takes place */
			if($action == 'cwd')
			{
				if($olddir != $newdir)
				{
					if ($newdir == '..')
					{
						$parts = explode('/',$olddir);
						array_pop($parts);
						$olddir = implode('/',$parts);
						if ($olddir[0] != '/') $olddir = '/'.$olddir;
					}
					else
					{
						$olddir .= ($olddir != '/' ? '/' : '') . $newdir;
					}
				}
				ftp_chdir($ftp,$olddir);
			}
			elseif($action == '' && $connInfo['cwd'] != '')
			{
				/* this must have come back from another module, try to
				 * get into the old directory
				 */
				ftp_chdir($ftp,$connInfo['cwd']);
			}
			elseif($olddir)
			{
				ftp_chdir($ftp,$olddir);
			}

			if(!$olddir)
			{
				$olddir = ftp_pwd($ftp);
			}
			$cwd = ftp_pwd($ftp);
			$connInfo['cwd'] = $cwd;

			// set up the upload form
			$ul_form_open='<form name="upload" action="'.createLink($GLOBALS['target'])
				. '" enctype="multipart/form-data" method="post">' . "\n"
				. '<input type="hidden" name="olddir" value="' . $cwd . '">' . "\n"
				. '<input type="hidden" name="action" value="upload">' . "\n";
			$ul_select = '<input type="file" name="uploadfile" size="30">' . "\n" ;
			$ul_submit = '<input type="submit" name="upload" value="'.lang('Upload').'">' . "\n";
			$ul_form_close = '</form>' . "\n";

			// set up the create directory
			$crdir_form_open='<form name="mkdir" action="' . createLink($GLOBALS['target']) . '" method="post" >' . "\n"
				. "\t" . '<input type="hidden" name="olddir" value="' . $cwd . '">' . "\n"
				. "\t" . '<input type="hidden" name="action" value="mkdir">' . "\n";

			$crdir_form_close = '</form>' . "\n";
			$crdir_textfield = "\t" . '<input type="text" size="30" name="newdirname" value="">' . "\n";
			$crdir_submit = "\t" . '<input type="submit" name="submit" value="'.lang('Create New Directory').'">' . "\n";
			$ftp_location = 'ftp://' . $connInfo['username'] . '@' . $connInfo['ftpserver'] . $cwd;

			$newdir = ''; $temp = $olddir; $olddir = $homedir; 
			$home_link = macro_get_Link('cwd','<img border="0" src="' . $GLOBALS['phpgw']->common->image('ftp','home.gif') . '">') . "\n";
			$olddir = $temp;

			// set up all the global variables for the template
			$GLOBALS['phpgw']->template->set_var(array(
				'ftp_location' => $ftp_location,
				'relogin_link'=> macro_get_Link('newlogin',lang('logout/relogin')),
				'home_link' => $home_link,
				'ul_select' => $ul_select, 
				'ul_submit' => $ul_submit,
				'ul_form_open' => $ul_form_open,
				'ul_form_close' => $ul_form_close,
				'crdir_form_open' => $crdir_form_open,
				'crdir_form_close' => $crdir_form_close,
				'crdir_textfield' => $crdir_textfield,
				'crdir_submit' => $crdir_submit
			));

			$total = count(ftp_rawlist($ftp,''));
			$GLOBALS['phpgw']->template->set_var('nextmatchs_left',$GLOBALS['phpgw']->nextmatchs->left('/ftp/index.php',$start,$total));
			$GLOBALS['phpgw']->template->set_var('nextmatchs_right',$GLOBALS['phpgw']->nextmatchs->right('/ftp/index.php',$start,$total));

			$contents = phpftp_getList($ftp,'.',$start);

			$GLOBALS['phpgw']->template->set_var('lang_name',lang('Name'));
			$GLOBALS['phpgw']->template->set_var('lang_owner',lang('Owner'));
			$GLOBALS['phpgw']->template->set_var('lang_group',lang('Group'));
			$GLOBALS['phpgw']->template->set_var('lang_permissions',lang('permissions'));
			$GLOBALS['phpgw']->template->set_var('lang_size',lang('size'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('delete'));
			$GLOBALS['phpgw']->template->set_var('lang_rename',lang('rename'));

			$newdir = $olddir;
			$GLOBALS['phpgw']->template->set_var('name',macro_get_link('cwd','..'));
			$GLOBALS['phpgw']->template->set_var('del_link','&nbsp;');
			$GLOBALS['phpgw']->template->set_var('rename_link','&nbsp;');
			$GLOBALS['phpgw']->template->set_var('permissions','');
			$GLOBALS['phpgw']->template->fp('rowlist_dir','row',True);

			if(is_array($contents))
			{
				//echo '<pre>'; print_r($contents); echo '</pre>';
				foreach($contents as $fileinfo)
				{
					$newdir = $fileinfo['name'];

					if($fileinfo['size'] < 999999)
					{
						$fileinfo['size'] = round(10 * ($fileinfo['size'] / 1024)) / 10 . ' k';
					}
					else
					{
						//  round to W.XYZ megs by rounding WX.YZ
						$fileinfo['size'] = round($fileinfo['size'] / (1024 * 100));
						// then bring it back one digit and add the MB string
						$fileinfo['size'] = ($fileinfo['size']/10) .' M';
					}
					$GLOBALS['phpgw']->template->set_var('permissions',$fileinfo['permissions']);					
					$GLOBALS['phpgw']->template->set_var('owner',$fileinfo['owner']);					
					$GLOBALS['phpgw']->template->set_var('group',$fileinfo['group']);					
					if(substr($fileinfo['permissions'],0,1) == 'd')
					{
						$file = $fileinfo['name'];
						$GLOBALS['phpgw']->template->set_var('name',macro_get_link('cwd',$fileinfo['name']));
						$GLOBALS['phpgw']->template->set_var('del_link',macro_get_link('rmdir',lang('delete')));
						$GLOBALS['phpgw']->template->set_var('size','<image src="'.$GLOBALS['phpgw']->common->image('ftp','folder').'">');
					}
					else
					{
						$file = $fileinfo['name'];
						$GLOBALS['phpgw']->template->set_var('del_link',macro_get_link('delete',lang('delete')));
						$GLOBALS['phpgw']->template->set_var('name',macro_get_link('get',$fileinfo['name']));
						$GLOBALS['phpgw']->template->set_var('size',$fileinfo['size']);
					}
					$GLOBALS['phpgw']->template->set_var('rename_link',macro_get_link('rename',lang('rename')));
					$GLOBALS['phpgw']->template->fp('rowlist_dir','row',True);
				}
			}
			ftp_quit($ftp);
			$GLOBALS['phpgw']->template->pfp('out','main');
		}
		else
		{
			updateSession();
			$sessionUpdated = True;
			if(!$tried_default)
			{
				// don't put out an error on the default login
				$GLOBALS['phpgw']->template->set_var(
					'error_message',
					lang(
						'Failed to connect to %1 with user %2 and password %3',
						$connInfo['ftpserver'],
						$connInfo['username'],
						'**********'
					),
					True
				);
				$GLOBALS['phpgw']->template->parse('out','bad_connect',false);
				$GLOBALS['phpgw']->template->p('out');
			}
			newLogin($connInfo['ftpserver'],$connInfo['username'],'');
		}
	}
	else
	{
		// set the login and such to ''
		updateSession('');
		$sessionUpdated = True;
		// $GLOBALS['phpgw']->modsession(
		newLogin($default_server,$default_login,'');
	}
	if(!$sessionUpdated || $action == 'cwd')
	{
		// echo "updating session with new cwd<BR>\n";
		updateSession($connInfo);
	}

	$GLOBALS['phpgw']->common->phpgw_footer();
?>

<?php
  /**************************************************************************\
  * eGroupWare - Translation Editor                                          *
  * http://www.egroupware.org                                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: class.uilangfile.inc.php,v 1.28.2.1 2004/09/08 13:04:25 ralfbecker Exp $ */

	class uilangfile
	{
		var $helpme;
		var $public_functions = array(
			'index'     => True,
			'edit'      => True,
			'create'    => True,
			'addphrase' => True,
			'missingphrase'=> True,
			'missingphrase2'=> True,
			'download'  => True
		);
		var $bo;
		var $template;
		var $nextmatchs;

		function uilangfile()
		{
			$this->template = $GLOBALS['phpgw']->template;
			$this->template->egroupware_hack = False;	// else the phrases got translated
			$this->bo = CreateObject('developer_tools.bolangfile');
			$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$GLOBALS['phpgw']->translation->add_app('developer_tools');
			$GLOBALS['phpgw']->translation->add_app('common');
			if (!is_object($GLOBALS['phpgw']->html))
			{
				$GLOBALS['phpgw']->html = CreateObject('phpgwapi.html');
			}
			$this->html = $GLOBALS['phpgw']->html;
		}

		function addphrase()
		{
			$app_name   = get_var('app_name',array('POST','GET'));
			$sourcelang = get_var('sourcelang',array('POST','GET'));
			$targetlang = get_var('targetlang',array('POST','GET'));
			$entry      = $_POST['entry'];

			$this->bo->read_sessiondata();
			if($_POST['add'] || $_POST['cancel'] || $_POST['more'])
			{
				if($_POST['add'] || $_POST['more'])
				{
					if (get_magic_quotes_gpc())
					{
						foreach(array('message_id','content','target') as $name)
						{
							$entry[$name] = stripslashes($entry[$name]);
						}
					}
					$this->bo->addphrase($entry);
					if ($sourcelang == $targetlang)
					{
						$this->bo->target_langarray = $this->bo->source_langarray;
					}
					$this->bo->save_sessiondata();
				}
				if (!$_POST['more'])
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array(
						'menuaction' => 'developer_tools.uilangfile.edit',
						'app_name'   => $app_name,
						'sourcelang' => $sourcelang,
						'targetlang' => $targetlang
					));
				}
			}
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps'][$GLOBALS['phpgw_info']['flags']['currentapp']]['title'].
				' - '.lang('Add new phrase');

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->template->set_file(array('form' => 'addphrase.tpl'));
			$this->template->set_var('message_id_field','<input size ="40" name="entry[message_id]">');
			$this->template->set_var('translation_field','<input size ="40" name="entry[content]">');
			$this->template->set_var('target_field','<input size ="40" name="entry[target]">');
			$this->template->set_var('app_name','<input type="hidden" name="entry[app_name]" value="'.$app_name.'">');

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php','menuaction=developer_tools.uilangfile.addphrase'));
			$this->template->set_var('sourcelang',$sourcelang);
			$this->template->set_var('targetlang',$targetlang);
			$this->template->set_var('app_name',$app_name);

			$this->template->set_var('lang_message_id',lang('message_id in English'));
			$this->template->set_var('lang_translation',lang('Phrase in English (or empty if identical)'));
			$this->template->set_var('lang_target',lang('Translation of phrase'));
			$this->template->set_var('lang_add',lang('Add'));
			$this->template->set_var('lang_more',lang('Add more'));
			$this->template->set_var('lang_cancel',lang('Cancel'));

			$this->template->pfp('phpgw_body','form');
		}

		function missingphrase()
		{
			$app_name    = get_var('app_name',array('POST','GET'));
			$sourcelang  = get_var('sourcelang',array('POST','GET'));
			$targetlang  = get_var('targetlang',array('POST','GET'));

			$this->bo->read_sessiondata();
			$this->bo->missing_app($app_name,$sourcelang);
			$this->bo->save_sessiondata();

			// we have to redirect here, as solangfile defines function sidebox_menu, which clashes with the iDots func.
			//
			$GLOBALS['phpgw']->redirect_link('/index.php',array(
				'menuaction' => 'developer_tools.uilangfile.missingphrase2',
				'app_name'   => $app_name,
				'sourcelang' => $sourcelang,
				'targetlang' => $targetlang
			));
		}
			
		function missingphrase2()
		{
			$app_name    = get_var('app_name',array('POST','GET'));
			$sourcelang  = get_var('sourcelang',array('POST','GET'));
			$targetlang  = get_var('targetlang',array('POST','GET'));
			$newlang     = $_POST['newlang'];
			$dlsource    = $_POST['dlsource'];
			$writesource = $_POST['writesource'];
			$dltarget    = $_POST['dltarget'];
			$writetarget = $_POST['writetarget'];
			$update      = $_POST['update'];
			$entry       = $_POST['entry'];
			$submit      = $_POST['submit'];
			$this->bo->read_sessiondata();

			$this->template->set_file(array('langfile' => 'langmissing.tpl'));
			$this->template->set_block('langfile','header','header');
			$this->template->set_block('langfile','postheader','postheader');
			$this->template->set_block('langfile','detail','detail');
			$this->template->set_block('langfile','prefooter','prefooter');
			$this->template->set_block('langfile','footer','footer');
			if(!$sourcelang)
			{
				$sourcelang = 'en';
			}
			if(!$targetlang)
			{
				$targetlang = 'en';
			}
			$missingarray = $this->bo->missing_langarray;
			//echo "missingarray=<pre>"; print_r($this->bo->missing_langarray); echo "</pre>\n";
			if ($update)
			{
				$deleteme     = $_POST['delete'];
				//echo "deleteme=<pre>"; print_r($deleteme); echo "</pre>\n";

				while (list($_mess,$_checked) = @each($deleteme))
				{
					if($_checked == 'on')
					{
						$_mess = $this->recode_id($_mess);
						$this->bo->movephrase($_mess);
						/* _debug_array($missingarray[$_mess]); */
						unset($missingarray[$_mess]);
						/* _debug_array($missingarray[$_mess]); */
					}
				}
				unset($deleteme);

				$this->bo->save_sessiondata();
				$GLOBALS['phpgw']->redirect_link('/index.php',array(
					'menuaction' => 'developer_tools.uilangfile.edit',
					'app_name'   => $app_name,
					'sourcelang' => $sourcelang,
					'targetlang' => $targetlang
				));
			}
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->template->set_var('lang_remove',lang('Add phrase'));
			$this->template->set_var('lang_application',lang('Application'));
			$this->template->set_var('lang_update',lang('Add'));
			$this->template->set_var('lang_view',lang('Cancel'));
			
			$this->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php','menuaction=developer_tools.uilangfile.missingphrase2'));
			$this->template->set_var('sourcelang',$sourcelang);
			$this->template->set_var('targetlang',$targetlang);
			$this->template->set_var('app_name',$app_name);
			$this->template->set_var('app_title',$GLOBALS['phpgw_info']['apps'][$app_name]['title']);
			$this->template->pfp('out','header');
			if($sourcelang && $targetlang)
			{
				$this->template->set_var('lang_appname',lang('Application'));
				$this->template->set_var('lang_message',lang('Message'));
				$this->template->set_var('lang_original',lang('Original'));
				$this->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
				$this->template->set_var('view_link',
					$GLOBALS['phpgw']->link(
						'/index.php',
						'menuaction=developer_tools.uilangfile.edit&app_name='.$app_name.'&sourcelang=' . $sourcelang . '&targetlang=' . $targetlang
					)
				);
				$this->template->pfp('out','postheader');
				while(list($key,$data) = @each($missingarray))
				{
					$mess_id  = $this->encode_id($key);
					$this->template->set_var('mess_id',$mess_id);
					$this->template->set_var('source_content',$this->html->htmlspecialchars($data['content']));
					$this->template->set_var('transapp',$this->lang_option($app_name,$data['app_name'],$mess_id));
					$this->template->set_var('tr_color',$this->nextmatchs->alternate_row_color());
					$this->template->pfp('out','detail');
				}
				$this->template->pfp('out','prefooter');
				$this->template->pfp('out','footer');
			}
			/* _debug_array($this->bo->loaded_apps); */
			$this->bo->save_sessiondata();
		}

		function edit()
		{
			if ($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=developer_tools.uilangfile.index');
			}

			$app_name   = get_var('app_name',array('POST','GET'));
			$sourcelang = get_var('sourcelang',array('POST','GET'));
			$targetlang = get_var('targetlang',array('POST','GET'));
			$entry       = $_POST['entry'];

			if($_POST['addphrase'] || $_POST['missingphrase'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array(
					'menuaction' => 'developer_tools.uilangfile.'.($_POST['addphrase']?'addphrase':'missingphrase'),
					'app_name'   => $app_name,
					'sourcelang' => $sourcelang,
					'targetlang' => $targetlang
				));
			}
			if ($_POST['revert'])
			{
				$this->bo->clear_sessiondata();
			}
			$this->bo->read_sessiondata();

			if($_POST['dlsource'])
			{
				$this->download('source',$sourcelang);
			}
			if($_POST['dltarget'])
			{
				$this->download('target',$targetlang);
			}

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->template->set_file(array('langfile' => 'langfile.tpl'));
			$this->template->set_block('langfile','header','header');
			$this->template->set_block('langfile','postheader','postheader');
			$this->template->set_block('langfile','detail','detail');
			$this->template->set_block('langfile','detail_long','detail_long');
			$this->template->set_block('langfile','footer','footer');

			$this->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php','menuaction=developer_tools.uilangfile.edit'));
			$this->template->set_var('lang_remove',lang('Remove'));
			$this->template->set_var('lang_loaddb',lang('Update Database'));
			$this->template->set_var('lang_application',lang('Application'));
			$this->template->set_var('lang_source',lang('Source Language'));
			$this->template->set_var('lang_target',lang('Target Language'));
			$this->template->set_var('lang_submit',lang('Load'));
			$this->template->set_var('lang_update',lang('Save'));
			$this->template->set_var('lang_revert',lang('Revert'));
			$this->template->set_var('lang_cancel',lang('Cancel'));
			$this->template->set_var('lang_step',lang('Step'));
			$help = 'onMouseOver="self.status=\'%s\'; return true;" onMouseOut="self.status=\'\'; return true;"';
			$this->template->set_var('cancel_help',sprintf($help,str_replace("'","\\'",lang('Returns to the application list, not saved changes get lost !!!'))));
			$this->template->set_var('load_help',sprintf($help,str_replace("'","\\'",lang('Loads the selected lang-files, to be modified in the next steps'))));
			$this->template->set_var('update_help',sprintf($help,str_replace("'","\\'",lang('Saves the added/changed translations to an internal buffer, to be used in further steps'))));
			$this->template->set_var('search_help',sprintf($help,str_replace("'","\\'",lang('Searches the source-code for phrases not in the actual source-lang-file'))));
			$this->template->set_var('add_help',sprintf($help,str_replace("'","\\'",lang('Allows you to add a single phrase'))));
			$this->template->set_var('revert_help',sprintf($help,str_replace("'","\\'",lang('Clears the internal buffer, all changes made sofar are lost'))));
			$this->template->set_var('download_help',sprintf($help,str_replace("'","\\'",lang('Download the lang-file to be saved in the apps setup-dir'))));
			$this->template->set_var('write_help',sprintf($help,str_replace("'","\\'",lang('Write the lang-file to the apps setup-dir'))));
			$this->template->set_var('loaddb_help',sprintf($help,str_replace("'","\\'",lang('Updates the translations of both lang-files in your database, so you can verify your work immediately'))));

			$languages = $this->bo->list_langs();

			if(!$sourcelang)
			{
				$sourcelang = 'en';
			}
			if(!$targetlang)
			{
				$targetlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			}

			while (list($x,$_lang) = @each($languages))
			{
				$sourcelangs .= '      <option value="' . $_lang['lang_id'] . '"';
				if ($sourcelang)
				{
					if ($_lang['lang_id'] == $sourcelang)
					{
						$sourcelangs .= ' selected';
					}
				}
				elseif ($_lang['lang_id'] == 'EN')
				{
					$sourcelangs .= ' selected';
				}
				$sourcelangs .= '>' . $_lang['lang_name'] . '</option>' . "\n";
			}
			@reset($languages);

			while (list($x,$_lang) = @each($languages))
			{
				$targetlangs .= '      <option value="' . $_lang['lang_id'] . '"';
				if ($targetlang)
				{
					if ($_lang['lang_id'] == $targetlang)
					{
						$targetlangs .= ' selected';
					}
				}
				elseif ($_lang['lang_id'] == 'EN')
				{
					$targetlangs .= ' selected';
				}
				$targetlangs .= '>' . $_lang['lang_name'] . '</option>' . "\n";
			}
			$this->template->set_var('sourcelangs',$sourcelangs);
			$this->template->set_var('targetlangs',$targetlangs);
			$this->template->set_var('app_name',$app_name);
			$this->template->set_var('app_title',$GLOBALS['phpgw_info']['apps'][$app_name]['title']);
			$this->template->pfp('out','header');

			$db_perms = $GLOBALS['phpgw']->acl->get_user_applications($GLOBALS['phpgw_info']['user']['account_id']);
			@ksort($db_perms);
			@reset($db_perms);
			while (list($userapp) = each($db_perms))
			{
				if ($GLOBALS['phpgw_info']['apps'][$userapp]['enabled'] || $userapp == 'setup')
				{
					$userapps .= '<option value="' . $userapp . '"';
					if ($application_name == $userapp)
					{
						$userapps .= ' selected';
					}
					elseif ($GLOBALS['phpgw_info']['user']['preferences']['default_app'] == $userapp)
					{
						$userapps .= ' selected';
					}
					$userapps .= '>' . (isset($GLOBALS['phpgw_info']['apps'][$userapp]['title']) ? $GLOBALS['phpgw_info']['apps'][$userapp]['title'] : lang($userapp)) . '</option>' . "\n";
				}
			}
			$this->template->set_var('userapps',$userapps);

			if ($_POST['update'] || $_POST['update_too'])
			{
				$transapp     = $_POST['transapp'];
				$translations = $_POST['translations'];
				$deleteme     = $_POST['delete'];
				while (list($_mess,$_app) = each($transapp))
				{
					if($_mess)
					{
						$_mess = strtolower(trim($this->recode_id($_mess)));
						$this->bo->source_langarray[$_mess]['app_name'] = $_app;
						$this->bo->target_langarray[$_mess]['app_name'] = $_app;
					}
				}
				if (!is_array($this->bo->target_langarray))
				{
					$this->bo->target_langarray = array();
				}
				while (list($_mess,$_cont) = each($translations))
				{
					if($_mess && $_cont)
					{
						$_mess = strtolower(trim($this->recode_id($_mess)));
						$this->bo->target_langarray[$_mess]['message_id'] = $_mess;
						//POST method adds slashes if magic_quotes_gpc is set !!!
						if (get_magic_quotes_gpc())
						{
							$_cont = stripslashes($_cont);
						}
						$this->bo->target_langarray[$_mess]['content'] = $_cont;
						if($sourcelang == $targetlang)
						{
							$this->bo->source_langarray[$_mess]['content'] = $_cont;
						}
					}
				}
				while (list($_mess,$_checked) = @each($deleteme))
				{
					if($_checked == 'on')
					{
						$_mess = strtolower(trim($this->recode_id($_mess)));
						unset($this->bo->source_langarray[$_mess]);
						unset($this->bo->target_langarray[$_mess]);
					}
				}
				@ksort($this->bo->source_langarray);
				@ksort($this->bo->target_langarray);
				/* $this->bo->save_sessiondata($this->bo->source_langarray,$this->bo->target_langarray); */
				unset($transapp);
				unset($translations);
				if($deleteme)
				{
					$this->bo->save_sessiondata();
				}
				unset($deleteme);
			}
			if($_POST['writesource'] || $_POST['writesource_too'])
			{
				echo '<br>'.lang("Writing langfile for '%1' ...",$sourcelang);
				$this->bo->write_file('source',$app_name,$sourcelang);
			}
			if($_POST['writetarget'] || $_POST['writetarget_too'])
			{
				echo '<br>'.lang("Writing langfile for '%1' ...",$targetlang);
				$this->bo->write_file('target',$app_name,$targetlang);
			}
			if ($_POST['loaddb'] || $_POST['loaddb_too'])
			{
				echo '<br>' . lang('Loading source langfile') . ': ' . $sourcelang . '... ';
				if ($sourcelang != $targetlang)
				{
					echo '<br>' . lang('Loading target langfile') . ': ' . $targetlang . '... ';
				}
				$langs[$sourcelang] = $sourcelang;
				$langs[$targetlang] = $targetlang;
				echo $this->bo->loaddb($app_name,$langs);
			}

			if($sourcelang && $targetlang)
			{
				$this->template->set_var('lang_appname',lang('Application'));
				$this->template->set_var('lang_message',lang('Message'));
				$this->template->set_var('lang_original',lang('Original'));
				$this->template->set_var('lang_translation',lang('Translation'));
				$this->template->set_var('lang_missingphrase',lang('Search new phrases'));
				$this->template->set_var('lang_addphrase',lang('Add new phrase'));
				$this->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
				$this->template->set_var('sourcelang',$sourcelang);
				$this->template->set_var('targetlang',$targetlang);
				$this->template->pfp('out','postheader');

				$langarray = $this->bo->add_app($app_name,$sourcelang);
				$translation = $this->bo->load_app($app_name,$targetlang);

				while(list($key,$data) = @each($langarray))
				{
					$mess_id  = $this->encode_id($key);
					$content  = $this->html->htmlspecialchars($mess_id == 'charset' ? $mess_id : $data['content']);
					$transy   = $this->html->htmlspecialchars($translation[$key]['content']);
					$this->template->set_var('mess_id',$mess_id);
					$this->template->set_var('source_content',$content);
					$this->template->set_var('content',$transy);
					$this->template->set_var('transapp',$this->lang_option($app_name,$data['app_name'],$mess_id));
					$this->template->set_var('tr_color',empty($transy) ? $GLOBALS['phpgw_info']['theme']['bg06'] : $this->nextmatchs->alternate_row_color());
					if (($len = max(strlen($key),strlen($content))) > 50)
					{
						$this->template->set_var('rows',min(intval($len/80+0.5),10));
						$this->template->pfp('out','detail_long');
					}
					else
					{
						$this->template->pfp('out','detail');
					}
				}
				$this->template->set_var('sourcelang',$sourcelang);
				$this->template->set_var('targetlang',$targetlang);
				$this->template->set_var('lang_write',lang('Write'));
				$this->template->set_var('lang_download',lang('Download'));
				$this->template->set_var('src_file',$this->bo->src_file);
				if(!$this->bo->loaded_apps[$sourcelang]['writeable'])
				{
					$this->template->set_block('footer','srcwrite','srcwrite');
					$this->template->set_var('srcwrite','');
				}
				$this->template->set_var('tgt_file',$this->bo->tgt_file);
				$this->template->set_var('targetlang',$targetlang);
				if(!$this->bo->loaded_apps[$targetlang]['writeable'])
				{
					$this->template->set_block('footer','tgtwrite','tgtwrite');
					$this->template->set_var('tgtwrite','');
				}
				
				$this->template->set_var('helpmsg',lang('you have to [Save] every manual change in the above fields, before you can go to the next step !!!'));
				$this->template->pfp('out','footer');
			}
			/* _debug_array($this->bo->loaded_apps); */
			$this->bo->save_sessiondata();
		}

		function encode_id($id)
		{
			return str_replace(array('[',']','&','"'),array('%5B','%5D','&amp;','&quot;'),$id);
		}

		function recode_id($id)
		{
			if (get_magic_quotes_gpc())
			{
				$id = stripslashes($id);
			}
			return str_replace(array('%5B','%5D'),array('[',']'),$id);	// &amp; + &quot; are recode by php
		}

		function download($which,$userlang)
		{
			switch ($which)
			{
				case 'source':
					$langarray = $this->bo->source_langarray;
					break;
				case 'target':
					$langarray = $this->bo->target_langarray;
					break;
				default:
					break;
			}
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header('phpgw_' . $userlang . '.lang');
			$to = $GLOBALS['phpgw']->translation->charset($userlang);
			$from = $GLOBALS['phpgw']->translation->charset();
			while(list($mess_id,$data) = @each($langarray))
			{
				$content = $GLOBALS['phpgw']->translation->convert(trim($data['content']),$from,$to);
				if (!empty($content))
				{
					echo $mess_id . "\t" . $data['app_name'] . "\t" . $userlang . "\t" . $content . "\n";
				}
			}
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		function index()
		{
			$start = $_POST['start'];
			$sort  = $_POST['sort'];
			$order = $_POST['order'];
			$query = $_POST['query'];

			$this->bo->save_sessiondata('','');
			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw_info']['apps'][$GLOBALS['phpgw_info']['flags']['currentapp']]['title'].
				' - '.lang('Installed applications');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->template->set_file(array('applications' => 'applications.tpl'));
			$this->template->set_block('applications','list','list');
			$this->template->set_block('applications','row','row');

			$offset = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			$apps = array(
				strtolower(lang('Setup')) => array(
					'name'  => 'setup',
					'title' => lang('Setup')
				)
			);
			foreach($GLOBALS['phpgw_info']['apps'] as $app => $data)
			{
				$apps[strtolower($data['title'])] = $data;
			}
			$total = count($apps);

			if(!$sort)
			{
				$sort = 'ASC';
			}

			if($sort == 'ASC')
			{
				ksort($apps);
			}
			else
			{
				krsort($apps);
			}

			if ($start && $offset)
			{
				$limit = $start + $offset;
			}
			elseif ($start && !$offset)
			{
				$limit = $start;
			}
			elseif(!$start && !$offset)
			{
				$limit = $total;
			}
			else
			{
				$start = 0;
				$limit = $offset;
			}

			if ($limit > $total)
			{
				$limit = $total;
			}

			$this->template->set_var('bg_color',$GLOBALS['phpgw_info']['theme']['bg_color']);
			$this->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);

			$this->template->set_var('sort_title',$this->nextmatchs->show_sort_order($sort,'title','title','/index.php',lang('Title'),'&menuaction=developer_tools.uilangfile.index'));
			$this->template->set_var('lang_showing',$this->nextmatchs->show_hits($total,$start));
			$this->template->set_var('left',$this->nextmatchs->left('/index.php',$start,$total,'&menuaction=developer_tools.uilangfile.index'));
			$this->template->set_var('right',$this->nextmatchs->right('/index.php',$start,$total,'&menuaction=developer_tools.uilangfile.index'));

			$this->template->set_var('lang_edit',lang('Edit'));
			//$this->template->set_var('lang_translate',lang('Translate'));
			$this->template->set_var('new_action',$GLOBALS['phpgw']->link('/index.php','menuaction=developer_tools.uilangfile.create'));
			$this->template->set_var('create_new',lang('Create New Language File'));

			$i = 0;
			foreach($apps as $data)
			{
				if($start <= $i && $i < $limit)
				{
					$tr_color = $this->nextmatchs->alternate_row_color($tr_color);

					$this->template->set_var('tr_color',$tr_color);
					$this->template->set_var('name',$data['title']);

					$this->template->set_var('edit','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=developer_tools.uilangfile.edit&app_name=' . urlencode($data['name'])) . '"> ' . lang('Edit') . ' </a>');
				//	$this->template->set_var('translate','<a href="' . $GLOBALS['phpgw']->link('/index.php','menuaction=developer_tools.uilangfile.translate&app_name=' . urlencode($app['name'])) . '"> ' . lang('Translate') . ' </a>');

					$this->template->set_var('status',$status);

					$this->template->parse('rows','row',True);
				}
				++$i;
			}

			$this->template->pparse('phpgw_body','list');
		}

		function lang_option($app_name,$current,$name)
		{
			$list = (is_array($this->bo->src_apps) ? $this->bo->src_apps : array()) + array(
				$app_name     => $app_name,
				'common'      => 'common',
				'login'       => 'login',
				'admin'       => 'admin',
				'preferences' => 'preferences'
			);

			$select  = "\n" .'<select name="transapp[' . $name . ']">' . "\n";
			while (list($key,$val) = each($list))
			{
				$select .= '<option value="' . $key . '"';
				if ($key == $current && $current != '')
				{
					$select .= ' selected';
				}
				$select .= '>' . $val . '</option>'."\n";
			}

			$select .= '</select>'."\n";

			return $select;
		}
	}
?>

<?php
  /**************************************************************************\
  * eGroupWare - Polls                                                       *
  * http://www.egroupware.org                                                *
  * Copyright (c) 1999 Till Gerken (tig@skv.org)                             *
  * Modified by Greg Haygood (shrykedude@bellsouth.net)                      *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: class.ui.inc.php,v 1.5.2.2 2005/03/30 15:53:08 ralfbecker Exp $ */

	class ui
	{
		var $t;
		var $bo;
		var $prefs;
		var $nextmatchs;

		var $debug = False;

		var $public_functions = array
			(
				'index' => True,
				'admin' => True,
				'vote'  => True,
				'view'  => True,
			);

		function ui()
		{
			$this->t = $GLOBALS["phpgw"]->template;
			$this->bo = createobject('polls.bo',true);
			$this->nextmatchs = createobject('phpgwapi.nextmatchs');
		}

		function index()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$currentpoll = $GLOBALS['poll_settings']['currentpoll'];
			if(!$this->bo->user_can_vote($currentpoll))
			{
				$this->view_results($currentpoll,true,true,false);
			}
			else
			{
				$this->show_ballot($currentpoll);
			}
		}

		function view()
		{
			$currentpoll = $GLOBALS['poll_settings']['currentpoll'];
			$this->view_results($currentpoll,true,true,false);
		}

		function vote()
		{
			if($_POST['submit'] && isset($_POST['poll_id']) && isset($_POST['poll_voteNr']))
			{
				$poll_id = (int)$_POST['poll_id'];
				$vote_id = (int)$_POST['poll_voteNr'];
				if($this->bo->user_can_vote($poll_id))
				{
					$this->bo->add_vote($poll_id,$vote_id,$GLOBALS['phpgw_info']['user']['account_id']);
				}
				$GLOBALS['phpgw']->redirect_link(
							'/index.php',array('menuaction'=>'polls.ui.vote','show_results'=>$poll_id));
				$GLOBALS['phpgw']->common->phpgw_exit();
				return 0;
			}
			$showpoll = $_GET['show_results'];
			if(empty($showpoll))
			{
				$showpoll = $GLOBALS['poll_settings']['currentpoll'];
			}
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$this->view_results($showpoll);
			$GLOBALS['phpgw']->common->phpgw_footer();
		}

		function admin()
		{
			if(!$GLOBALS['phpgw_info']['user']['apps']['admin'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}
			$action = get_var('action',array('GET','POST'));
			$type 	= get_var('type',array('GET','POST'));
			if($_POST['cancel'])
			{
				if(!empty($type))
				{
					header('Location: '.$this->adminlink('show',$type));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=>'polls.ui.vote'));
				}
				$GLOBALS['phpgw']->common->phpgw_exit();
				return 0;
			}
			if(isset($_POST['delete']) && $action == 'edit')
			{
				$action = 'delete';
			}
			$func = $action.$type;
			if(method_exists($this,$func))
			{
				call_user_method($func,$this);
			}
			elseif(method_exists($this,$action))
			{
				call_user_method($action,$this);
			}
		}

		function button_bar($buttons)
		{
			if(isset($buttons) && is_array($buttons))
			{
				$this->t->set_var('buttons','');
				foreach($buttons as $name => $value)
				{
					$this->t->set_var('btn_name',$name);
					$this->t->set_var('btn_value',$value);
					$this->t->parse('buttons','button',True);
				}
			}
		}

		function action_button($action,$options)
		{
			$button = '';

			$img = '<img src="'
				. $GLOBALS['phpgw']->common->image('addressbook',$action)
				. '" border="0" title="'.lang($action).'">';
			if(empty($options) || !is_array($options))
			{
				$options = array();
			}
			if(!isset($options['action']))
			{
				$options['action'] = $action;
			}
			$button = '<a href="'.$GLOBALS['phpgw']->link('/index.php',$options).'">'.$img.'</a>';

			return $button;
		}

		function add_template_row($label,$value)
		{
			$this->nextmatchs->template_alternate_row_color($this->t);
			$this->t->set_var('td_1',$label);
			$this->t->set_var('td_2',$value);
			$this->t->parse('rows','row',True);
		}

		function adminlink($action = 'show',$type = 'question',$extra = '')
		{
			$options = array('menuaction'=>'polls.ui.admin','action'=>$action,'type'=>$type);
			if(isset($extra) && is_array($extra))
			{
				$options += $extra;
			}
			return $GLOBALS['phpgw']->link('/index.php',$options);
		}

		function addanswer()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Polls').' - '.lang('Add Answer to poll');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->t->set_file(array('admin' => 'admin_form.tpl'));
			$this->t->set_block('admin','form','form');
			$this->t->set_block('admin','row','row');
			$this->t->set_block('admin','button','button');
			$this->t->set_block('admin','input','input');
			$this->t->set_var('hidden','');

			if($_POST['submit'])
			{
				$poll_id = $_POST['poll_id'];
				$answer  = $_POST['answer'];

				$this->bo->add_answer($poll_id,$answer);
				$this->t->set_var('message',lang('Answer has been added to poll'));
			}

			$this->t->set_var('header_message',lang('Add answer to poll'));
			$this->t->set_var('td_message','&nbsp;');
			$this->t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->t->set_var('form_action',$this->adminlink('add','answer'));
			$this->button_bar(array(
					'submit' => lang('Add'),
					'cancel' => lang('Cancel')
				));

			$poll_select = $this->select_poll($poll_id);

			$this->add_template_row(lang('Which poll'),$poll_select);
			$this->t->set_var('input_name','answer');
			$this->t->set_var('input_value','');
			$this->add_template_row(lang('Answer'),$this->t->parse('td_2','input',True));

			$this->t->pfp('out','form');
		}

		function select_poll($preselected_poll, $show_select_tag=true)
		{
			if ($show_select_tag)
			{
				$poll_select = '<select name="poll_id">';
			}
			$questions = $this->bo->get_list('question',true);

			foreach($questions as $key => $array)
			{
				$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
				$this->t->set_var('tr_color',$tr_color);

				$_poll_id = $array['poll_id'];
				$_vote_id = $array['vote_id'];

				$_poll_title = $array['poll_title'];
				$_option_text = $array['option_text'];

				$poll_select .= '<option value="'.$_poll_id.'"';
				if($preselected_poll == $_poll_id)
				{
					$poll_select .= ' selected';
				}
				$poll_select .= '>'.trim(stripslashes($_poll_title)).'</option>';
			}
			if ($show_select_tag)
			{
				$poll_select .= '</select>';
			}

			return $poll_select;
		}

		function addquestion()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Polls').' - '.lang('Add new poll question');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->t->set_file(array('admin' => 'admin_form.tpl'));
			$this->t->set_block('admin','form','form');
			$this->t->set_block('admin','row','row');
			$this->t->set_block('admin','button','button');
			$this->t->set_block('admin','input','input');
			$this->t->set_var('hidden','');

			if ($_POST['submit'])
			{
				$newid = $this->bo->add_question();
				$newlink = $this->adminlink('add','answer',array('poll_id'=>$newid));
 				$this->t->set_var("message",'<a href="'.$newlink.'">'.lang("New poll has been added.  You should now add some answers for this poll").'</a>');
			}
			else
			{
				$this->t->set_var('message','');
			}

			$this->t->set_var('header_message',lang('Add new poll question'));
			$this->t->set_var('td_message',"&nbsp;");
			$this->t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->t->set_var('form_action', $this->adminlink('add','question'));
			$this->button_bar(array(
					'submit' => lang('Add'),
					'cancel' => lang('Cancel')
				));

			$this->t->set_var('input_name','question');
			$this->t->set_var('input_value','');
			$this->add_template_row(lang("Enter poll question"),$this->t->parse('td_2','input',True));

			$this->t->pparse('out','form');
		}

		function deleteanswer()
		{
			$poll_id = (int)get_var('poll_id',array('GET','POST'));
			$vote_id = (int)get_var('vote_id',array('GET','POST'));
			$confirm = get_var('confirm',array('GET','POST'));
			if (!empty($confirm))
			{
				$this->bo->delete_answer($poll_id,$vote_id);
				header('Location: ' . $this->adminlink('show','answer'));
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Polls').' - '.lang('delete') . ' ' . lang('answer');
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();

				$poll_data = $this->bo->get_poll_data($poll_id,$vote_id);
				$poll_info = $poll_data[0]['text'] . ' ('.lang('total votes').' = '.$poll_data[0]['votes'].')';

				$this->t->set_file(array('admin' => 'admin_form.tpl'));
				$this->t->set_block('admin','form','form');
				$this->t->set_block('admin','row','row');
				$this->t->set_block('admin','button','button');
				$this->t->set_var('rows','');

				$this->t->set_var('hidden','<input type="hidden" name="vote_id" value="'.$vote_id.'">');
				$this->t->set_var('poll_id',$poll_id);
				$this->t->set_var('vote_id',$vote_id);
				$this->t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
				$this->t->set_var('message',lang('Are you sure want to delete this answer ?'));
				$this->t->set_var('td_message', $this->bo->get_poll_title($poll_id) . ': ' . $poll_info);
				$this->t->set_var('form_action',$this->adminlink('delete','answer'));
				$this->button_bar(array(
						'cancel' => lang('No'),
						'confirm' => lang('Yes')
					));

				$this->t->pparse('out','form');
			}
		}

		function deletequestion()
		{
			$poll_id = (int)get_var('poll_id',array('GET','POST'));
			$confirm = get_var('confirm',array('GET','POST'));
			if (!empty($confirm))
			{
				$this->bo->delete_question($poll_id);
				header('Location: ' . $this->adminlink('show','question'));
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Polls').' - '.lang('delete') . ' ' . lang('Poll Question');
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();

				$this->t->set_file(array('admin' => 'admin_form.tpl'));
				$this->t->set_block('admin','form','form');
				$this->t->set_block('admin','row','row');
				$this->t->set_block('admin','button','button');
				$this->t->set_var('rows','');
				$this->t->set_var('hidden','');

				$this->t->set_var('poll_id',$poll_id);

				$this->t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
				$this->t->set_var('message',lang('Are you sure want to delete this question ?'));
				$this->t->set_var('td_message', $this->bo->get_poll_title($poll_id));
				$this->t->set_var('form_action',$this->adminlink('delete','question'));
				$this->button_bar(array(
						'cancel' => lang('No'),
						'confirm' => lang('Yes')
					));

				$this->t->pparse('out','form');
			}
		}

		function editanswer()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Polls').' - '.lang('Edit answer');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->t->set_file(array('admin' => 'admin_form.tpl'));
			$this->t->set_block('admin','form','form');
			$this->t->set_block('admin','row','row');
			$this->t->set_block('admin','button','button');
			$this->t->set_var('hidden','');

			$poll_id = (int)(get_var('poll_id',array('POST','GET')));
			$vote_id = (int)(get_var('vote_id',array('POST','GET')));
			$this->t->set_var('poll_id',$poll_id);

			if ($_POST['submit'])
			{
				$this->bo->update_answer($poll_id,$vote_id,$_POST['answer']);
				$this->t->set_var('message',lang('Answer has been updated'));
			}
			else
			{
				$this->t->set_var('message','');
			}

			$poll_data = $this->bo->get_poll_data($poll_id,$vote_id);
			$answer_value = trim($poll_data[0]['text']);
			//$poll_id = $GLOBALS['phpgw']->db->f('poll_id');

			$this->t->set_var('header_message',lang('Edit answer'));
			$this->t->set_var('td_message','&nbsp;');
			$this->t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->t->set_var('form_action',$this->adminlink('edit','answer',array('vote_id'=>$vote_id)));
			$this->button_bar(array(
					'submit' => lang('Edit'),
					'cancel' => lang('Cancel')
				));

			$poll_select = $this->select_poll($poll_id);

			$this->add_template_row(lang('Which poll'),$poll_select);
			$this->add_template_row(lang('Answer'),'<input name="answer" value="' . $answer_value . '">');

			$this->t->pparse('out','form');
			$GLOBALS['phpgw']->common->phpgw_footer();
		}

		function editquestion()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Polls').' - '.lang('Edit poll question');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->t->set_file(array('admin' => 'admin_form.tpl'));
			$this->t->set_block('admin','form','form');
			$this->t->set_block('admin','row','row');
			$this->t->set_block('admin','button','button');
			$this->t->set_block('admin','input','input');
			$this->t->set_block('admin','results','results');
			$this->t->set_block('admin','answers','answers');
			$this->t->set_block('admin','answer_row','answer_row');
			$this->t->set_block('admin','messagebar','messagebar');
			$this->t->set_var('hidden','');

			$poll_id = get_var('poll_id',array('GET','POST'));
			$this->t->set_var('poll_id',$poll_id);

			if ($_POST['edit'])
			{
				$this->bo->update_question($poll_id,$_POST['question']);
				$this->t->set_var('message',lang('Question has been updated'));
			}
			else
			{
				$this->t->set_var('message','');
			}

			$poll_title = $this->bo->get_poll_title($poll_id);
			$answers = $this->bo->get_poll_data($poll_id);

			$this->t->set_var('header_message',lang('Edit poll question'));
			$this->t->set_var('td_message','&nbsp;');
			$this->t->set_var('tr_color',$GLOBALS['phpgw_info']['theme']['bgcolor']);
			$this->t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->t->set_var('poll_id',$poll_id);
			$this->t->set_var('form_action',$this->adminlink('edit','question',array('poll_id'=>$poll_id)));
			$this->button_bar(array(
					'edit' => lang('Edit'),
					'cancel' => lang('Cancel')
				));

			$this->t->set_var('td_1',lang('Poll question'));
			$this->t->set_var('input_name','question');
			$this->t->set_var('input_value',stripslashes($poll_title));
			$this->t->set_var('td_2',$this->t->parse('td_2','input',True));
			$this->t->parse('rows','row',True);

			$this->t->set_var('mesg',lang('Answers'));
			$this->t->parse('rows','messagebar',True);

			//$this->t->set_var('poll_results', $this->view_results($poll_id,false,true,true));

			foreach($answers as $answer)
			{
				$option_text  = $answer['text'];
				$option_count = $answer['votes'];
				$vote_id = $answer['vote_id'];

				$actions = '';
				$_options = array(
						'menuaction' => 'polls.ui.admin',
						'type'	 	 => 'answer',
						'poll_id'    => $poll_id,
						'vote_id'    => $vote_id
				);
				foreach(array('edit','delete') as $_action)
				{
					$_options['action'] = $_action;
					$actions .= $this->action_button($_action,$_options);
				}
				$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
				$this->t->set_var('tr_color',$tr_color);
				$this->t->set_var('answer_actions',$actions);
				$this->t->set_var('option_text',$option_text);

				$this->t->parse('poll_answers','answer_row',True);
			}
			$this->t->parse('rows','answers',True);

			$this->t->pparse('out','form');
		}

		function settings()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Polls').' - '.lang('Settings');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->t->set_file(array('admin' => 'admin_settings.tpl'));
			$this->t->set_block('admin','settings','settings');
			//$this->t->set_block('admin','row','row');

			if($_POST['submit'])
			{
				$this->bo->save_settings($_POST['settings']);
				echo '<center>' . lang('Settings updated') . '</center>';
			}
			// load after a save, so this page displays correctly
			$settings = $this->bo->load_settings();

			$var = array(
				'form_action'	=> $this->adminlink('settings',''),
				'lang_allowmultiple' => lang('Allow users to vote more then once'),
				'check_allow_multiple_votes' => $GLOBALS['poll_settings']['allow_multiple_votes']?' checked':'',
				'lang_selectpoll' => lang('Select current poll'),
				'lang_submit' => lang('Submit'),
				'lang_cancel' => lang('Cancel'),
			);
			$this->t->set_var($var);

			$poll_questions = $this->select_poll($GLOBALS['poll_settings']['currentpoll'],false);
			$this->t->set_var('poll_questions', $poll_questions);

			$this->t->pparse('out','settings');
		}

		function viewquestion()
		{
			$poll_id = (int)$_GET['poll_id'];

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Polls').' - '.lang('View poll');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->t->set_file(array('admin' => 'admin_form.tpl'));
			$this->t->set_block('admin','form','form');
			$this->t->set_block('admin','row','row');
			$this->t->set_block('admin','button','button');
			$this->t->set_var('hidden','');

			$poll_title = $this->bo->get_poll_title($poll_id);

			$this->t->set_var('message','');
			$this->t->set_var('header_message',lang('View poll'));
			$this->t->set_var('td_message',$GLOBALS['phpgw']->strip_html($poll_title));
			$this->t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->t->set_var('form_action',$this->adminlink('edit','question'));
			$this->t->set_var('poll_id',$poll_id);

			$this->button_bar(array(
					'submit' => lang('Edit'),
					'delete' => lang('Delete'),
					'cancel' => lang('Cancel')
				));

			$this->t->set_var('rows', '<tr><td colspan="2" width="100%">'
					. $this->view_results($poll_id,false,true,true) . '</td></tr>');

			$this->t->pparse('out','form');
		}

		function show()
		{
			$type  = get_var('type',array('GET','POST'));
			if($type == 'question')
			{
				$pagetitle = lang('Show questions');
				$allowed_actions = array('view','edit','delete');
				$this->t->set_file(array('admin' => 'admin_list_questions.tpl'));
			}
			elseif($type == 'answer')
			{
				$pagetitle = lang('Show answers');
				$allowed_actions = array('edit','delete');
				$this->t->set_file(array('admin' => 'admin_list_answers.tpl'));
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/polls/index.php');
				$GLOBALS['phpgw']->common->phpgw_exit(True);
				return 0;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Polls').' - '.$pagetitle;
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->bo->sort  = $_GET['sort'] ? $_GET['sort'] : 'ASC';
			$this->bo->order = isset($_GET['order']) ? $_GET['order'] : 'poll_title';
			if(!$this->bo->start)
			{
				$this->bo->start = 0;
			}
			$this->bo->save_sessiondata();

			$this->t->set_block('admin','form','form');
			$this->t->set_block('admin','row','row');

			$this->t->set_unknowns('remove');

			$thelist = $this->bo->get_list($type);

			$this->t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->t->set_var('sort_title',$this->nextmatchs->show_sort_order($this->bo->sort,'poll_title',$this->bo->order,'index.php',lang('Title'),'&menuaction=polls.ui.admin&action=show&type='.$type));
			if($type == 'answer')
			{
				$this->t->set_var('sort_answer',$this->nextmatchs->show_sort_order($this->bo->sort,'option_text',$this->bo->order,'index.php',lang('Answer'),'&menuaction=polls.ui.admin&action=show&type='.$type));
			}

			$left  = $this->nextmatchs->left('/index.php',$this->bo->start,$this->bo->total,'menuaction=polls.ui.admin&action=show&type='.$type);
			$right = $this->nextmatchs->right('/index.php',$this->bo->start,$this->bo->total,'menuaction=polls.ui.admin&action=show&type='.$type);
			$this->t->set_var('match_left',$left);
			$this->t->set_var('match_right',$right);

			$this->t->set_var('lang_showing',$this->nextmatchs->show_hits($this->bo->total,$this->bo->start));

			$this->t->set_var('lang_actions',lang('actions'));
			$this->t->set_var('lang_view',lang('view'));
			$this->t->set_var('lang_edit',lang('edit'));
			$this->t->set_var('lang_delete',lang('delete'));

			$this->t->set_var('rows','');
			foreach($thelist as $key => $array)
			{
				$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
				$this->t->set_var('tr_color',$tr_color);

				$poll_id = $array['poll_id'];
				$vote_id = $array['vote_id'];

				$poll_title = $array['poll_title'];
				$option_text = $array['option_text'];

				$actions = '';
				$_options = array(
						'menuaction' => 'polls.ui.admin',
						'type'	 	 => $type,
						'poll_id'    => $poll_id
				);
				foreach($allowed_actions as $_action)
				{
					$_options['action'] = $_action;
					if($type == 'answer')
					{
						$_options['vote_id'] = $vote_id;
					}
					$actions .= $this->action_button($_action,$_options);
				}
				$this->t->set_var('row_actions',$actions);

				if($type == 'question')
				{
					$this->t->set_var('row_title',stripslashes($poll_title));
				}
				else
				{
					$this->t->set_var('row_answer',stripslashes($option_text));
					$this->t->set_var('row_title',stripslashes($poll_title));
					$this->t->set_var('row_edit','<a href="' . $this->adminlink('edit','answer',
									array ('vote_id' => $vote_id,
									       'poll_id' => $poll_id
									  ) ) .'">' . lang('Edit') . '</a>');
					$this->t->set_var('row_delete','<a href="' . $this->adminlink('delete','answer',
									array ('vote_id' => $vote_id,
									       'poll_id' => $poll_id
									  ) ) .'">' . lang('Delete') . '</a>');
				}
				$this->t->parse('rows','row',True);
			}

			$this->t->set_var('add_action',$this->adminlink('add',$type));
			$this->t->set_var('lang_add',lang('add'));

			$this->t->pparse('out','form');

			$GLOBALS['phpgw']->common->phpgw_footer();
		}

		function view_results($poll_id,$showtitle=true,$showtotal=true,$returnstring=false)
		{
			$poll_id = (int)$poll_id;

			$title = $this->bo->get_poll_title($poll_id);
			$sum = $this->bo->get_poll_total($poll_id);
			$results = $this->bo->get_poll_data($poll_id);

			$this->t->set_file(array('viewpoll' => 'view_poll.tpl'));
			$this->t->set_block('viewpoll','title','title');
			$this->t->set_block('viewpoll','poll','poll');
			$this->t->set_block('viewpoll','vote','vote');
			$this->t->set_block('viewpoll','image','image');
			$this->t->set_block('viewpoll','total','total');

			$this->t->set_var('titlebar', '');
			if($showtitle)
			{
				$this->t->set_var('poll_title', $title);
				$this->t->set_var('td_color', $GLOBALS['phpgw_info']['theme']['th_bg']);
				$this->t->parse('titlebar','title');
			}

			$this->t->set_var('votes', '');
			$this->t->set_var('server_url',$GLOBALS['phpgw_info']['server']['webserver_url']);
			foreach($results as $result)
			{
				$option_text  = $result['text'];
				$option_count = $result['votes'];

				$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
				$this->t->set_var('vote_color', $tr_color);

				if ($option_text != '')
				{
					if ($sum)
					{
						$poll_percent = 100 * $option_count / $sum;
					}
					else
					{
						$poll_percent = 0;
					}
					$poll_percent = sprintf("%.2f",$poll_percent);

					$this->t->set_var('poll_bar','');
					if ($poll_percent > 0)
					{
						$poll_percentScale = (int)($poll_percent * 1);
						$this->t->set_var('scale',$poll_percentScale);
						$this->t->parse('poll_bar','image');
					}
					else
					{
						$this->t->set_var('poll_bar','&nbsp;');
					}

					$this->t->set_var('option_text',$option_text);
					$this->t->set_var('option_count',$option_count);
					$this->t->set_var('percent',$poll_percent);
					$this->t->set_var('sum',$sum);

					$this->t->parse('votes','vote',True);
				}
			}

			if($showtotal)
			{
				$this->t->set_var('sum',$sum);
				$this->t->set_var('lang_total',lang('Total votes'));
				$this->t->set_var('tr_color', $GLOBALS['phpgw_info']['theme']['th_bg'] /*bgcolor*/);
				$this->t->parse('show_total','total');
			}

			if($returnstring)
			{
				return $this->t->parse('out','poll');
			}
			$this->t->pparse('out','poll');
			return 0;
		}

		function show_ballot($poll_id = '')
		{
			if(empty($poll_id))
			{
				$poll_id = $this->bo->get_latest_poll();
			}
			$poll_id = (int)$poll_id;

			if(!$this->bo->user_can_vote($poll_id))
			{
				return False;
			}

			$poll_title = $this->bo->get_poll_title($poll_id);
			$poll_sum = $this->bo->get_poll_total($poll_id);
			$results = $this->bo->get_poll_data($poll_id);

			$this->t->set_file(array('ballot' => 'ballot.tpl'));
			$this->t->set_block('ballot','form','form');
			$this->t->set_block('ballot','entry','entry');

			$this->t->set_var('form_action',
							$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'polls.ui.vote')));
			$this->t->set_var('poll_id',$poll_id);
			$this->t->set_var('poll_title',$poll_title);
			$this->t->set_var('title_bgcolor', $GLOBALS['phpgw_info']['theme']['th_bg']);
			$this->t->set_var('bgcolor', $GLOBALS['phpgw_info']['theme']['bgcolor']);

			$this->t->set_var('entries', '');
			foreach($results as $result)
			{
				$vote_id = $result['vote_id'];
				$option_text  = $result['text'];
				$option_count = $result['votes'];

				$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
				$this->t->set_var('tr_color', $tr_color);
				$this->t->set_var('vote_id', $vote_id);
				$this->t->set_var('option_text', $option_text);

				$this->t->parse('entries','entry',True);
			}

			$this->t->set_var('lang_vote', lang('Vote'));

			$this->t->pparse('out','form');
		}

	}
?>

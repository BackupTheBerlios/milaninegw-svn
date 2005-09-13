<?php
  /**************************************************************************\
  * eGroupWare - Addressbook                                                 *
  * http://www.egroupware.org                                                *
  * Written by Joseph Engo <jengo@phpgroupware.org> and                      *
  * Miles Lott <milos@groupwhere.org>                                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: class.uiaddressbook.inc.php,v 1.89.4.1 2005/03/15 14:34:22 ralfbecker Exp $ */

	class uiaddressbook
	{
		var $contacts;
		var $bo;
		var $cat;
		var $company;
		var $prefs;

		var $debug = False;

		var $start;
		var $limit;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $cat_id;

		var $public_functions = array(
			'index' => True,
			'view' => True,
			'add'  => True,
			'add_email' => True,
			'copy' => True,
			'edit' => True,
			'delete' => True,
			'preferences' => True
		);

		var $extrafields = array(
			'ophone'   => 'ophone',
			'address2' => 'address2',
			'address3' => 'address3'
		);

		var $contact_types = array(
			'n' => 'People',
			'c' => 'Companies'
		);
		var $contact_type = array(
			'n' => 'Person',
			'c' => 'Company'
		);

		function uiaddressbook()
		{
			$GLOBALS['phpgw']->country    = CreateObject('phpgwapi.country');
			$GLOBALS['phpgw']->browser    = CreateObject('phpgwapi.browser');
			$GLOBALS['phpgw']->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$this->fields = CreateObject('addressbook.uifields');

			$this->bo       = CreateObject('addressbook.boaddressbook',True);
			$this->cat      = CreateObject('phpgwapi.categories');
//			$this->company  = CreateObject('phpgwapi.categories','addressbook_company');
			$this->prefs    = $GLOBALS['phpgw_info']['user']['preferences']['addressbook'];

			$this->_set_sessiondata();
		}

		function _set_sessiondata()
		{
			$this->start  = $this->bo->start;
			$this->limit  = $this->bo->limit;
			$this->query  = $this->bo->query;
			$this->cquery = $this->bo->cquery;
			$this->sort   = $this->bo->sort;
			$this->order  = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->cat_id = $this->bo->cat_id;
			$this->typeid = $this->bo->typeid;
			if($this->debug) { $this->_debug_sqsof(); }
		}

		function _debug_sqsof()
		{
			$data = array(
				'start'  => $this->start,
				'limit'  => $this->limit,
				'query'  => $this->query,
				'cquery' => $this->cquery,
				'sort'   => $this->sort,
				'order'  => $this->order,
				'filter' => $this->filter,
				'cat_id' => $this->cat_id,
				'typeid' => $this->typeid
			);
			echo '<br>UI:';
			_debug_array($data);
		}

		/* Called only by index(), just prior to page footer. */
		function save_sessiondata()
		{
			$data = array(
				'start'  => $this->start,
				'limit'  => $this->limit,
				'query'  => $this->query,
				'cquery' => $this->cquery,
				'sort'   => $this->sort,
				'order'  => $this->order,
				'filter' => $this->filter,
				'cat_id' => $this->cat_id,
				'typeid' => $this->typeid
			);
			$this->bo->save_sessiondata($data);
		}

		function formatted_list($name,$list,$id='',$default=False,$java=False)
		{
			if($java)
			{
				$jselect = ' onChange="this.form.submit();"';
			}

			$select  = "\n" .'<select name="' . $name . '"' . $jselect . ">\n";
			if($default)
			{
				$select .= '<option value="">' . lang('Please Select') . '</option>'."\n";
			}
			settype($list,'array');
			foreach($list as $key => $val)
			{
				$select .= '<option value="' . $key . '"';
				if($key == $id && $id != '')
				{
					$select .= ' selected';
				}
				$select .= '>' . $val . '</option>'."\n";
			}

			$select .= '</select>'."\n";
			$select .= '<noscript><input type="submit" name="' . $name . '_select" value="'
				. lang('Submit') . '"></noscript>' . "\n";

			return $select;
		}

		/* Return a select form element with the categories option dialog in it */
		function cat_option($cat_id='',$notall=False,$java=True,$multiple=False)
		{
			if($java)
			{
				$jselect = ' onChange="this.form.submit();"';
			}
			/* Setup all and none first */
			$cats_link  = "\n" .'<select name="fcat_id'.($multiple?'[]':'').'"' .$jselect . ($multiple ? 'multiple ' : '') . ">\n";
			if(!$notall)
			{
				$cats_link .= '<option value=""';
				if($cat_id == 'all')
				{
					$cats_link .= ' selected';
				}
				$cats_link .= '>'.lang("all").'</option>'."\n";
			}

			/* Get global and app-specific category listings */
			$cats_link .= $this->cat->formated_list('select','all',$cat_id,True);
			$cats_link .= '</select>'."\n";
			return $cats_link;
		}

		/* this cleans up the fieldnames for display */
		function display_name($column)
		{
			$abc = array(
				'fn'                  => 'full name',
				'sound'               => 'Sound',
				'org_name'            => 'company name',
				'org_unit'            => 'department',
				'title'               => 'title',
				'n_prefix'            => 'prefix',
				'n_given'             => 'first name',
				'n_middle'            => 'middle name',
				'n_family'            => 'last name',
				'n_suffix'            => 'suffix',
				'label'               => 'label',
				'adr_one_street'      => 'business street',
				'adr_one_locality'    => 'business city',
				'adr_one_region'      => 'business state',
				'adr_one_postalcode'  => 'business zip code',
				'adr_one_countryname' => 'business country',
				'adr_one_type'        => 'business address type',
				'adr_two_street'      => 'home street',
				'adr_two_locality'    => 'home city',
				'adr_two_region'      => 'home state',
				'adr_two_postalcode'  => 'home zip code',
				'adr_two_countryname' => 'home country',
				'adr_two_type'        => 'home address type',
				'tz'                  => 'time zone',
				'geo'                 => 'geo',
				'tel_work'            => 'business phone',
				'tel_home'            => 'home phone',
				'tel_voice'           => 'voice phone',
				'tel_msg'             => 'message phone',
				'tel_fax'             => 'fax',
				'tel_pager'           => 'pager',
				'tel_cell'            => 'mobile phone',
				'tel_bbs'             => 'bbs phone',
				'tel_modem'           => 'modem phone',
				'tel_isdn'            => 'isdn phone',
				'tel_car'             => 'car phone',
				'tel_video'           => 'video phone',
				'tel_prefer'          => 'preferred phone',
				'email'               => 'business email',
				'email_type'          => 'business email type',
				'email_home'          => 'home email',
				'email_home_type'     => 'home email type',
				'address2'            => 'address line 2',
				'address3'            => 'address line 3',
				'ophone'              => 'Other Phone',
				'bday'                => 'birthday',
				'url'                 => 'url',
				'pubkey'              => 'public key',
				'note'                => 'notes'
			);

			if($abc[$column])
			{
				return lang($abc[$column]);
			}
			return;
		}

		function index()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$GLOBALS['phpgw']->template->set_file(array('addressbook_list_t' => 'index.tpl'));
			$GLOBALS['phpgw']->template->set_block('addressbook_list_t','addressbook_header','addressbook_header');
			$GLOBALS['phpgw']->template->set_block('addressbook_list_t','column','column');
			$GLOBALS['phpgw']->template->set_block('addressbook_list_t','row','row');
			$GLOBALS['phpgw']->template->set_block('addressbook_list_t','delete_block','delete_block');
			$GLOBALS['phpgw']->template->set_block('addressbook_list_t','addressbook_footer','addressbook_footer');
			$GLOBALS['phpgw']->template->set_block('addressbook_list_t','addressbook_alpha','addressbook_alpha');

			/* Setup query for 1st char of fullname, company, lastname using user lang */
			$chars = lang('alphabet');
			if($chars == 'alphabet*')
			{
				$chars = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
			}
			$aar = explode(',', $chars);
			unset($chars);
			$aar[] = 'all';
			foreach($aar as $char)
			{
				$GLOBALS['phpgw']->template->set_var('charclass',$this->cquery == $char ||
					$char == 'all' && !$this->cquery ? 'letter_box_active' : 'letter_box');

				if($char == 'all')
				{
					$GLOBALS['phpgw']->template->set_var('charlink',
						$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index&cquery=')
					);
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('charlink',
						$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index&cquery=' . $char)
					);
				}
				$GLOBALS['phpgw']->template->set_var('char',$char != 'all' ? strtoupper($char) : lang('all'));
				$GLOBALS['phpgw']->template->fp('alphalinks','addressbook_alpha',True);
			}
			unset($aar);
			unset($char);

			$custom = $this->fields->read_custom_fields();
			$customfields = array();
//			while(list($x,$y) = @each($custom))
			foreach($custom as $x => $y)
			{
				$customfields[$y['name']] = $y['name'];
				$namedfields[$y['name']] = $y['title'];
			}

			if($this->cat_id == -1)
			{
				$this->cat_id = $this->prefs['default_category'];
			}

			if($this->prefs['autosave_category'])
			{
				$GLOBALS['phpgw']->preferences->read_repository();
				$GLOBALS['phpgw']->preferences->delete('addressbook','default_category');
				$GLOBALS['phpgw']->preferences->add('addressbook','default_category',$this->cat_id);
				$GLOBALS['phpgw']->preferences->save_repository();
			}

			/* $qfields = $contacts->stock_contact_fields + $extrafields + $customfields; */
			/* create column list and the top row of the table based on user prefs */
			foreach($this->bo->stock_contact_fields as $column => $db_name)
			{
				$test = strtolower($column);
				if(isset($this->prefs[$test]) && $this->prefs[$test])
				{
					$showcol = $this->display_name($column);
					$cols .= '  <td height="21">' . "\n";
					$cols .= '    <font size="-1" face="Arial, Helvetica, sans-serif">';
					$cols .= $GLOBALS['phpgw']->nextmatchs->show_sort_order($this->sort,
						$column,$this->order,'/index.php',$showcol,'&menuaction=addressbook.uiaddressbook.index'
					);
					$cols .= "</font>\n  </td>";
					$cols .= "\n";

					/* To be used when displaying the rows */
					$columns_to_display[$column] = True;
				}
			}
			/* Setup the columns for non-standard fields, since we don't allow sorting */
			$nonstd = $this->extrafields + $customfields;
			foreach($nonstd as $column)
			{
				$test = strtolower($column);
				if(isset($this->prefs[$test]) && $this->prefs[$test])
				{
					$showcol = $this->display_name($column[0]);
					/* This must be a custom field */
					if(!$showcol)
					{
//						$showcol = $column;
						$showcol = $namedfields[$column];
					}
					$cols .= '  <td height="21">' . "\n";
					$cols .= '    <font size="-1" face="Arial, Helvetica, sans-serif">';
					$cols .= $showcol;
					$cols .= "</font>\n  </td>";
					$cols .= "\n";

					/* To be used when displaying the rows */
					$columns_to_display[$column] = True;
				}
			}

			/* Check if prefs were set, if not, create some defaults */
			if(!$columns_to_display)
			{
				/* No prefs,. so cols above may have been set to '' or a bunch of <td></td> */
				$cols='';
				$columns_to_display = array(
					'fn'        => True,
					'org_name'  => True,
					'adr_one_locality' => True,
					'tel_work'  => True,
					'tel_cell'  => True,
					'email'     => True
				);
				foreach($columns_to_display as $col => $nul)
				{
					$showcol = $this->display_name($col);
					$cols .= '  <td height="21">' . "\n";
					$cols .= '    <font size="-1" face="Arial, Helvetica, sans-serif">';
					$cols .= $GLOBALS['phpgw']->nextmatchs->show_sort_order(
						$this->sort,$col,$this->order,
						"/index.php",$showcol,
						'&menuaction=addressbook.uiaddressbook.index&cat_id=' . $this->cat_id . '&cquery=' . $this->cquery
					);
					$cols .= "</font>\n  </td>";
					$cols .= "\n";

					$prefs[$col] = 'on';
				}
				$this->bo->save_preferences($prefs,'',$columns_to_display,'');
			}

			if(!$this->start)
			{
				$this->start = 0;
			}

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] &&
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$this->limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$this->limit = 15;
			}

			/*global $filter; */
			if(empty($this->filter) || !isset($this->filter))
			{
				if($this->prefs['default_filter'])
				{
					$this->filter = $this->prefs['default_filter'];
					$this->query = '';
				}
				else
				{
					$this->filter = 'none';
				}
			}

			/*
			Set qfilter to display entries where tid=n (normal contact entry),
			else they may be accounts, etc.
			*/
			$qfilter = 'tid=' . (string)$this->typeid;
			switch($this->filter)
			{
				case 'blank':
					$nosearch = True;
					break;
				case 'none':
					break;
				case 'private':
					$qfilter .= ',access=private'; /* fall through */
				case 'yours':
					$qfilter .= ',owner=' . $GLOBALS['phpgw_info']['user']['account_id'];
					break;
				default:
					$qfilter .= ',owner=' . $this->filter;
			}
			if($this->cat_id)
			{
				$qfilter .= ',cat_id='.$this->cat_id;
			}

			if(!$userid)
			{
				$userid = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			if($nosearch && !$this->query)
			{
				$entries = array();
				$total_records = 0;
			}
			else
			{
				/* read the entry list */
				$entries = $this->bo->read_entries(array(
					'start'  => $this->start,
					'limit'  => $this->limit,
					'fields' => $columns_to_display,
					'filter' => $qfilter,
					'query'  => $this->query,
					'cquery' => $this->cquery,
					'sort'   => $this->sort,
					'order'  => $this->order
				));
				$total_records = $this->bo->total;
			}

			/* global here so nextmatchs accepts our setting of $query and $filter */
			$GLOBALS['query']  = $this->query;
			$GLOBALS['filter'] = $this->filter;

			$search_filter = $GLOBALS['phpgw']->nextmatchs->show_tpl('/index.php',
				$this->start, $total_records,
				'&menuaction=addressbook.uiaddressbook.index&fcat_id=' . $this->cat_id . '&cquery=' . $this->cquery,'95%',
				$GLOBALS['phpgw_info']['theme']['th_bg'],1,1,1,array('filter' => $this->filter,'yours' => 1),
				$this->cat_id
			);
			$query = $filter = '';

			$lang_showing = $GLOBALS['phpgw']->nextmatchs->show_hits($total_records,$this->start);

			/* set basic vars and parse the header */
			$GLOBALS['phpgw']->template->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);
			$GLOBALS['phpgw']->template->set_var('lang_actions',lang('Actions'));
			$GLOBALS['phpgw']->template->set_var('check',$GLOBALS['phpgw']->common->image('phpgwapi','transparent'));
			$GLOBALS['phpgw']->template->set_var('select_all','');
			if(count($entries))
			{
				$GLOBALS['phpgw']->template->set_var('check', $GLOBALS['phpgw']->common->image('addressbook','check'));
				$GLOBALS['phpgw']->template->set_var('select_all',lang('Select all'));
			}

			$GLOBALS['phpgw']->template->set_var('searchreturn',$noprefs . ' ' . $searchreturn);
			$GLOBALS['phpgw']->template->set_var('lang_showing',$lang_showing);
			$GLOBALS['phpgw']->template->set_var('search_filter',$search_filter);
			/*
			$GLOBALS['phpgw']->template->set_var('lang_show',lang('Show') . ':');
			$GLOBALS['phpgw']->template->set_var('contact_type_list',$this->formatted_list('typeid',$this->contact_types,$this->typeid,False,True));
			$GLOBALS['phpgw']->template->set_var('self_url',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index'));
			*/
			$GLOBALS['phpgw']->template->set_var('lang_show','');
			$GLOBALS['phpgw']->template->set_var('contact_type_list','');
			$GLOBALS['phpgw']->template->set_var('self_url','');

			$GLOBALS['phpgw']->template->set_var('cats',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('cats_url',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index'));
			/* $GLOBALS['phpgw']->template->set_var('cats_link',$this->cat_option($this->cat_id)); */
			$GLOBALS['phpgw']->template->set_var('lang_cats',lang('Select'));
			//			$GLOBALS['phpgw']->template->set_var('lang_addressbook',lang('Address book'));
			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('th_font',$GLOBALS['phpgw_info']['theme']['font']);
			$GLOBALS['phpgw']->template->set_var('th_text',$GLOBALS['phpgw_info']['theme']['th_text']);
			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.delete'));
			$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));
			$GLOBALS['phpgw']->template->set_var('add_url',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.add'));
			$GLOBALS['phpgw']->template->set_var('lang_addvcard',lang('AddVCard'));
			$GLOBALS['phpgw']->template->set_var('vcard_url',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uivcard.in'));
			$GLOBALS['phpgw']->template->set_var('lang_import',lang('Import Contacts'));
			$GLOBALS['phpgw']->template->set_var('import_url',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiXport.import'));
			$GLOBALS['phpgw']->template->set_var('lang_import_alt',lang('Alt. CSV Import'));
			$GLOBALS['phpgw']->template->set_var('import_alt_url',$GLOBALS['phpgw']->link('/addressbook/csv_import.php'));
			$GLOBALS['phpgw']->template->set_var('lang_export',lang('Export Contacts'));
			$GLOBALS['phpgw']->template->set_var('export_url',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiXport.export'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));
			$GLOBALS['phpgw']->template->set_var('column_count',count($columns_to_display));

			$GLOBALS['phpgw']->template->set_var('start',$this->start);
			$GLOBALS['phpgw']->template->set_var('sort',$this->sort);
			$GLOBALS['phpgw']->template->set_var('order',$this->order);
			$GLOBALS['phpgw']->template->set_var('filter',$this->filter);
			$GLOBALS['phpgw']->template->set_var('query',$this->query);
			$GLOBALS['phpgw']->template->set_var('cat_id',$this->cat_id);

			$GLOBALS['phpgw']->template->set_var('qfield',$qfield);
			$GLOBALS['phpgw']->template->set_var('cols',$cols);

			$GLOBALS['phpgw']->template->pparse('out','addressbook_header');

			/* Show the entries */
			/* each entry */
			for($i=0;$i<count($entries);$i++)
			{
				$GLOBALS['phpgw']->template->set_var('columns','');
				$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
				$GLOBALS['phpgw']->template->set_var('row_tr_color',$tr_color);
				$myid    = $entries[$i]['id'];
				$myowner = $entries[$i]['owner'];

				/* each entry column */
//				@reset($columns_to_display);
//				while($column = @each($columns_to_display))
				foreach($columns_to_display as $column => $nul)
				{
					$ref = $data='';
					$coldata = $entries[$i][$column];
					/* echo '<br>coldata="' . $coldata . '"'; */
					/* Some fields require special formatting. */
					//$coldata .= " Column: [$column] ";
					if($column == 'url')
					{
						if(!empty($coldata) && (substr($coldata,0,7) != 'http://'))
						{
							$coldata = 'http://' . $coldata;
						}
						$ref='<a href="'.$coldata.'" target="_new">';
						$data=$coldata.'</a>';
					}
					elseif($column == 'linkedin_profile')
					{
						if(!empty($coldata) && (substr($coldata,0,7) != 'http://'))
						{
							$coldata = 'http://' . $coldata;
						}
						$ref='<a href="'.$coldata.'" target="_new">';
						$data=$coldata.'</a>';
					}
					elseif(($column == 'email') || ($column == 'email_home'))
					{
						if($GLOBALS['phpgw_info']['user']['apps']['email'])
						{
							$ref = '<a href="'
								. $GLOBALS['phpgw']->link('/email/compose.php','to=' . urlencode($coldata))
								. '" target="_new">';
						}
						elseif($GLOBALS['phpgw_info']['user']['apps']['felamimail'])
						{
							$link_data = array(
								'menuaction' => 'felamimail.uicompose.compose',
								'send_to'    => base64_encode($coldata)
							);
							$ref = '<a href="'
								. $GLOBALS['phpgw']->link('/index.php',$link_data)
								. '" target="_new">';
						}
						else
						{
							$ref = '<a href="mailto:' . $coldata . '">';
						}
						$data = $coldata . '</a>';
					}
					else /* But these do not */
					{
						$ref = ''; $data = $coldata;
					}
					$GLOBALS['phpgw']->template->set_var('col_data',$ref.$data);
					$GLOBALS['phpgw']->template->parse('columns','column',True);
				}

				$actions = '<a href="'
					. $GLOBALS['phpgw']->link('/index.php',array(
						'menuaction' => 'addressbook.uiaddressbook.view',
						'ab_id'      => $entries[$i]['id']
					))
					. '"><img src="'
					. $GLOBALS['phpgw']->common->image('addressbook','view')
					. '" border="0" title="'.lang('View').'"></a> ';

				if($this->bo->check_perms($entries[$i],PHPGW_ACL_EDIT))
				{
					$actions .= '<a href="'
						. $GLOBALS['phpgw']->link('/index.php',array(
							'menuaction' => 'addressbook.uiaddressbook.edit',
							'ab_id'      => $entries[$i]['id']
						))
						. '"><img src="'
						. $GLOBALS['phpgw']->common->image('addressbook','edit')
						. '" border="0" title="' . lang('Edit') . '"></a> ';
				}

				if($this->bo->check_perms($entries[$i],PHPGW_ACL_DELETE))
				{
					$actions .= '<a href="'
						. $GLOBALS['phpgw']->link('/index.php',array(
							'menuaction' => 'addressbook.uiaddressbook.delete',
							'ab_id'      => $entries[$i]['id']
						))
						. '"><img src="'
						. $GLOBALS['phpgw']->common->image('addressbook','delete')
						. '" border="0" title="'.lang('Delete').'"></a>';
				}
				$actions .= '<input type="checkbox" name="select[' . $entries[$i]['id'] . ']">';
				$GLOBALS['phpgw']->template->set_var('actions',$actions);

				$GLOBALS['phpgw']->template->parse('rows','row',True);
				$GLOBALS['phpgw']->template->pparse('out','row');
				reset($columns_to_display);
			}

			$GLOBALS['phpgw']->template->set_var('delete_button','');
			if(count($entries))
			{
				$GLOBALS['phpgw']->template->fp('delete_button','delete_block');
			}
			$GLOBALS['phpgw']->template->pfp('out','addressbook_footer');
			$this->save_sessiondata();
			/* $GLOBALS['phpgw']->common->phpgw_footer(); */
		}

		function add_email()
		{
			$name      = $_POST['name'] ? $_POST['name'] : $_GET['name'];
			$referer   = $_POST['referer'] ? $_POST['referer'] : $_GET['referer'];
			$add_email = $_POST['add_email'] ? $_POST['add_email'] : $_GET['add_email'];

			$named = explode(' ', $name);
			for($i=count($named);$i>=0;$i--)
			{
				$names[$i] = $named[$i];
			}
			if($names[2])
			{
				$fields['n_given']  = $names[0];
				$fields['n_middle'] = $names[1];
				$fields['n_family'] = $names[2];
			}
			else
			{
				$fields['n_given']  = $names[0];
				$fields['n_family'] = $names[1];
			}
			$fields['n_given']  = $fields['n_given'] ? $fields['n_given'] : ' ';
			$fields['n_family'] = $fields['n_family'] ? $fields['n_family'] : ' ';
			$fields['fn']       = $fields['n_given'] . ' '  . $fields['n_family'];
			$fields['email']    = $add_email;
			$fields['access']   = 'private';
			$fields['tid']      = 'n';
			$referer = urlencode($referer);

			$fields['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
//			_debug_array($fields);exit;
			$this->bo->add_entry($fields);
			$ab_id = $this->bo->get_lastid();

			$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=addressbook.uiaddressbook.view&ab_id=' . $ab_id . '&referer=' . $referer);
		}

		function copy()
		{
			$custom = $this->fields->read_custom_fields();
			$customfields = array();
//			while(list($x,$y) = @each($custom))
			foreach($custom as $x => $y)
			{
				$customfields[$y['name']] = $y['title'];
			}

			list($addnew) = $this->bo->read_entry(array(
				'id' => (int) $_GET['ab_id'],
				'fields' => $this->bo->stock_contact_fields + $this->extrafields + $customfields
			));

			$addnew['note'] .= "\n".lang("Copied by %1, from record #%2.",$GLOBALS['phpgw']->accounts->id2name($addnew['owner']),$addnew['id']);
			$addnew['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
			unset($addnew['rights']);
			unset($addnew['id']);

			$ab_id = $this->bo->add_entry($addnew);

			$GLOBALS['phpgw']->redirect_link('/index.php','menuaction=addressbook.uiaddressbook.edit&ab_id=' . $ab_id);
		}

		function add()
		{
			if($_POST['submit'])
			{
				$fields = $this->get_form();

				$referer = urlencode($fields['referer']);
				unset($fields['referer']);
				$fields['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];

				$ab_id = $this->bo->add_entry($fields);
				if(@is_array($ab_id) || !$ab_id)
				{
					/* Errors encountered during validation */
					$errors = $ab_id;
				}
//				$ab_id = $this->bo->get_lastid();

				if(!$errors)
				{
					Header('Location: '
						. $GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.view&ab_id=' . $ab_id . '&referer=' . $referer));
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}

			$GLOBALS['phpgw']->template->set_file(array('add' => 'add.tpl'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Addressbook').' - '.lang('Add');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$custom = $this->fields->read_custom_fields();
			foreach($custom as $x => $y)
			{
				$customfields[$y['name']] = $y['title'];
			}

			$this->addressbook_form('','menuaction=addressbook.uiaddressbook.add','Add','',$customfields,$this->cat_id);

			$GLOBALS['phpgw']->template->set_var('errors','');
			if(@is_array($errors))
			{
				$GLOBALS['phpgw']->template->set_var('errors',implode(',',$errors));
			}
			$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));
			$GLOBALS['phpgw']->template->set_var('cancel_url',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index'));
			$GLOBALS['phpgw']->template->parse('out','add');
			$GLOBALS['phpgw']->template->pparse('out','add');
		}

		function edit()
		{
			if($_POST['submit'])
			{
				$_fields = $this->get_form();
				/* _debug_array($_fields);exit; */
				$check = $this->bo->read_entry(array('id' => $_fields['ab_id'], 'fields' => array('owner' => 'owner','tid' => 'tid')));

				if($this->bo->check_perms($check[0],PHPGW_ACL_EDIT))
				{
					$userid = $check[0]['owner'];
				}
				else
				{
					$userid = $GLOBALS['phpgw_info']['user']['account_id'];
				}
				$_fields['owner'] = $userid;
				$referer = urlencode($_fields['referer']);
				unset($_fields['referer']);

				$this->bo->update_entry($_fields);

				Header('Location: '
					. $GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.view&ab_id=' . $_fields['ab_id'] . '&referer=' . $referer)
				);

				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			/* First, make sure they have permission to this entry */
			$check = $this->bo->read_entry(array('id' => (int) $_GET['ab_id'], 'fields' => array('owner' => 'owner','tid' => 'tid')));

			if(!$this->bo->check_perms($check[0],PHPGW_ACL_EDIT))
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index'));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Addressbook').' - '.lang('Edit');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			/* Read in user custom fields, if any */
			$custom = $this->fields->read_custom_fields();
			$customfields = array();
//			while(list($x,$y) = @each($custom))
			foreach($custom as $x => $y)
			{
				$customfields[$y['name']] = $y['title'];
			}

			/* merge in extra fields */
			$qfields = $this->bo->stock_contact_fields + $this->extrafields + $customfields;
			$fields = $this->bo->read_entry(array('id' => (int) $_GET['ab_id'], 'fields' => $qfields));

			$this->addressbook_form('edit','menuaction=addressbook.uiaddressbook.edit',lang('Edit'),$fields[0],$customfields);

			$GLOBALS['phpgw']->template->set_file(array('edit' => 'edit.tpl'));

			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('ab_id',(int) $_GET['ab_id']);
			$GLOBALS['phpgw']->template->set_var('tid',$check[0]['tid']);
			$GLOBALS['phpgw']->template->set_var('referer',$referer);
			$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));
			$GLOBALS['phpgw']->template->set_var('cancel_link','<form method="POST" action="'
				. $GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index') . '">');

			if(($this->bo->grants[$check[0]['owner']] & PHPGW_ACL_DELETE) || $check[0]['owner'] == $GLOBALS['phpgw_info']['user']['account_id'])
			{
				$GLOBALS['phpgw']->template->set_var('delete_link','<form method="POST" action="'.$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.delete') . '">');
				$GLOBALS['phpgw']->template->set_var('delete_button','<input type="submit" name="delete" value="' . lang('Delete') . '">');
			}

			$GLOBALS['phpgw']->template->pfp('out','edit');
		}

		function delete()
		{
			$ab_id = $_POST['entry']['ab_id'] ? $_POST['entry']['ab_id'] : $_POST['ab_id'];
			$confirm = $_GET['confirm'] ? $_GET['confirm'] :$_POST['confirm'];
			$select = $_POST['select'];
			if(@is_array($select))
			{
				/* The original values are sent as select[number] = on */
				$select = array_keys($select);
			}
			elseif(is_string($select))
			{
				/* This is imploded below and sent along with the form if the answer is yes */
				$select = explode(',',$select);
			}

			if(!$ab_id)
			{
				$ab_id = (int) $_GET['ab_id'];		// else plain Link in delete does not work
			}
			if((!$ab_id && !$select) || $_POST['no'])
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index'));
			}

			if(!@is_array($select))
			{
				$select[] = $ab_id;
			}
			foreach($select as $null => $_id)
			{
				if(!(int)$_id)
				{
					continue;
				}
				$check = $this->bo->read_entry(array('id' => $_id, 'fields' => array('owner' => 'owner','tid' => 'tid')));

				if(!(($this->bo->grants[$check[0]['owner']] & PHPGW_ACL_DELETE) || $check[0]['owner'] == $GLOBALS['phpgw_info']['user']['account_id']))
				{
					Header('Location: ' . $GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index'));
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}

			$GLOBALS['phpgw']->template->set_file(array('delete' => 'delete.tpl'));

			if(!$_POST['yes'])
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Addressbook').' - '.lang('Delete');
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();

				if(count($select) == 1)
				{
					$GLOBALS['phpgw']->template->set_var('lang_sure',lang('Are you sure you want to delete this entry ?'));
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('lang_sure',lang('Are you sure you want to delete these entries ?'));
				}
				$GLOBALS['phpgw']->template->set_var('no_link',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index'));
				$GLOBALS['phpgw']->template->set_var('lang_no',lang('NO'));
				$GLOBALS['phpgw']->template->set_var('yes_link',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.delete&ab_id=' . $ab_id . '&confirm=true'));
				$GLOBALS['phpgw']->template->set_var('select',implode(',',$select));
				$GLOBALS['phpgw']->template->set_var('lang_yes',lang('YES'));
				$GLOBALS['phpgw']->template->pparse('out','delete');
			}
			else
			{
				if(!@is_array($select))
				{
					$select[] = $ab_id;
				}
				foreach($select as $null => $_id)
				{
					$this->bo->delete_entry(array('id' => $_id));
				}
				@Header('Location: ' . $GLOBALS['phpgw']->link('/addressbook/index.php','menuaction=addressbook.uiaddressbook.index'));
			}
		}

		function rebuild_referer($val)
		{
			$val = urldecode($val);
			$vars = split('&',$val);
			$i = 0;
			foreach($vars as $key => $var)
			{
				$pair = split('=',$var);
				if($pair[0] == 'sq')
				{
					$pair[1] = $GLOBALS['phpgw']->session->sq;
				}
				$vars[$i] = implode('=',$pair);
				$i++;
			}
			$val = implode('&',$vars);
			return $val;
		}

		function view()
		{
			$ab_id   = (int) $_GET['ab_id'];
			$submit  = $_POST['submit'];
			$referer = $this->rebuild_referer($_GET['referer']);

			/* First, make sure they have permission to this entry */
			if(!$ab_id || !$this->bo->check_perms($ab_id,PHPGW_ACL_READ))
			{
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index'));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			elseif(!$submit && $ab_id)
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Address book - view');
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
			}

			$GLOBALS['phpgw']->template->set_file(array('view_t' => 'view.tpl'));
			$GLOBALS['phpgw']->template->set_block('view_t','view_header','view_header');
			$GLOBALS['phpgw']->template->set_block('view_t','view_row','view_row');
			$GLOBALS['phpgw']->template->set_block('view_t','view_footer','view_footer');
			$GLOBALS['phpgw']->template->set_block('view_t','view_buttons','view_buttons');

			$custom = $this->fields->read_custom_fields();
			$customfields = array();
//			while(list($x,$y) = @each($custom))
			foreach($custom as $x => $y)
			{
				$customfields[$y['name']] = $y['title'];
			}

			/* _debug_array($this->prefs); */
//			while(list($column,$x) = each($this->bo->stock_contact_fields))
			foreach($this->bo->stock_contact_fields as $column => $x)
			{
				if(isset($this->prefs[$column]) && $this->prefs[$column])
				{
					$columns_to_display[$column] = True;
					$colname[$column] = $column;
				}
			}

			/* merge in extra fields */
			$qfields = $this->bo->stock_contact_fields + $this->extrafields + $customfields;

			$fields = $this->bo->read_entry(array('id' => $ab_id, 'fields' => $qfields));

			$record_owner = $fields[0]['owner'];

			if($fields[0]['access'] == 'private')
			{
				$access_check = lang('private');
			}
			else
			{
				$access_check = lang('public');
			}

			unset($qfields['email_type']);		// noone is useing that any more
			unset($qfields['email_home_type']);

//			@reset($qfields);
//			while(list($column,$null) = @each($qfields))
			foreach($qfields as $column => $nul)
			{
				if($this->display_name($colname[$column]))
				{
					$GLOBALS['phpgw']->template->set_var('display_col',$this->display_name($colname[$column]));
				}
				elseif($this->display_name($column))
				{
					$GLOBALS['phpgw']->template->set_var('display_col',$this->display_name($column));
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('display_col',ucfirst($column));
				}
				$ref = $data = '';
				if($fields[0][$column])
				{
					$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
					$GLOBALS['phpgw']->template->set_var('th_bg',$tr_color);
					$coldata = $fields[0][$column];
					/* Some fields require special formatting. */
					//$coldata.= " Column: $column ";
					if(($column == 'note' || $column == 'pubkey') && $coldata)
					{
						$datarray = explode("\n",$coldata);
						if($datarray[1])
						{
//							while(list($key,$info) = each($datarray))
							foreach($datarray as $key => $info)
							{
								if($key)
								{
									$data .= '</td></tr><tr bgcolor="'.$tr_color.'"><td width="30%">&nbsp;</td><td width="70%">' .$info;
								}
								else
								{
									/* First row, don't close td/tr */
									$data .= $info;
								}
							}
							$data .= '</tr>';
						}
						else
						{
							$data = $coldata;
						}
					}
					elseif($column == 'linkedin_profile' && $coldata)
					{
						$ref = '<a href="' . $coldata . '" target="_new">';
						$data = 'Link</a>';
					}
					elseif($column == 'url' && $coldata)
					{
						$ref = '<a href="' . $coldata . '" target="_new">';
						$data = $coldata . '</a>';
					}
					elseif((($column == 'email') || ($column == 'email_home')) && $coldata)
					{
						if($GLOBALS['phpgw_info']['user']['apps']['email'])
						{
							$ref='<a href="' . $GLOBALS['phpgw']->link('/email/compose.php','to='
								. urlencode($coldata)) . '" target="_new">';
						}
						else
						{
							$ref = '<a href="mailto:'.$coldata.'">';
						}
						$data = $coldata.'</a>';
					}
					elseif($column == 'bday')
					{
						list($month,$day,$year) = explode('/',$coldata);
						$data = $GLOBALS['phpgw']->common->dateformatorder($year,$month,$day,True);
					}
					else
					{
						/* But these do not */
						$ref = ''; $data = $coldata;
					}

					if(!$data)
					{
						$GLOBALS['phpgw']->template->set_var('ref_data','&nbsp;');
					}
					else
					{
						$GLOBALS['phpgw']->template->set_var('ref_data',$ref . $data);
					}
					$GLOBALS['phpgw']->template->parse('cols','view_row',True);
				}
			}
			/* Following cleans up view_row, since we were only using it to fill {cols} */
			//$GLOBALS['phpgw']->template->set_var('view_row','');

			$fields['cat_id'] = is_array($this->cat_id) ? implode(',',$this->cat_id) : $this->cat_id;

			$cats = explode(',',$fields[0]['cat_id']);
			$catnames = array();
			foreach($cats as $cat)
			{
				if ($cat)
				{
					$cat = $this->cat->return_single((int)$cat);
					$catnames[] = stripslashes($cat[0]['name']);
				}
			}
			$catname = implode('; ',$catnames);
			if (!$this->cat_id)
			{
				$this->cat_id = count($cats) > 1 ? $cats[1] : $cats[0];
			}

			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
			$GLOBALS['phpgw']->template->set_var(array(
				'ref_data' => $GLOBALS['phpgw']->common->grab_owner_name($record_owner),
				'display_col' => lang('Record owner'),
				'th_bg' => $tr_color
			));
			$GLOBALS['phpgw']->template->parse('cols','view_row',True);

			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
			$GLOBALS['phpgw']->template->set_var(array(
				'ref_data' => $access_check,
				'display_col' => lang('Record access'),
				'th_bg' => $tr_color
			));
			$GLOBALS['phpgw']->template->parse('cols','view_row',True);

			if($catname)
			{
				$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
				$GLOBALS['phpgw']->template->set_var(array(
					'ref_data' => $catname,
					'display_col' => lang('Category'),
					'th_bg' => $tr_color
				));
				$GLOBALS['phpgw']->template->parse('cols','view_row',True);
			}

			if(($this->bo->grants[$record_owner] & PHPGW_ACL_EDIT) || ($record_owner == $GLOBALS['phpgw_info']['user']['account_id']))
			{
				$extra_vars = array('cd' => 16,'query' => $this->query,'cat_id' => $this->cat_id);

				if($referer)
				{
					$extra_vars += array('referer' => urlencode($referer));
				}

				$GLOBALS['phpgw']->template->set_var('edit_button',$this->html_1button_form('edit','Edit',
					$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.edit&ab_id=' .$ab_id)));
			}
			$GLOBALS['phpgw']->template->set_var('copy_button',$this->html_1button_form('submit','copy',
				$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.copy&ab_id=' . $fields[0]['id'])));

			if($fields[0]['n_family'] && $fields[0]['n_given'])
			{
				$GLOBALS['phpgw']->template->set_var('vcard_button',$this->html_1button_form('VCardForm','VCard',
					$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uivcard.out&ab_id=' .$ab_id)));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('vcard_button',lang('no vcard'));
			}

			$GLOBALS['phpgw']->template->set_var('done_button',$this->html_1button_form('DoneForm','Done',
				$referer ? $referer : $GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.index')));
			$GLOBALS['phpgw']->template->set_var('access_link',$access_link);

			$GLOBALS['phpgw']->template->set_var('view_row','');  // cleanup to avoid showing categories twice
			$GLOBALS['phpgw']->template->pfp('phpgw_body','view_t');

			$GLOBALS['phpgw']->hooks->process(array(
				'location' => 'addressbook_view',
				'ab_id'    => $ab_id
			));
		}

		function html_1button_form($name,$lang,$link)
		{
			$html  = '<form method="POST" action="' . $link . '">' . "\n";
			$html .= '<input type="submit" name="' . $name .'" value="' . lang($lang) . '">' . "\n";
			$html .= '</form>' . "\n";
			return $html;
		}

		function preferences()
		{
			$prefs   = $_POST['prefs'];
			$other   = $_POST['other'];
			$fcat_id = (int)$_POST['fcat_id'];

			$custom = $this->fields->read_custom_fields();
			$customfields = array();
//			while(list($x,$y) = @each($custom))
			foreach($custom as $x => $y)
			{
				$customfields[$y['name']] = $y['name'];
			}

			$qfields = $this->bo->stock_contact_fields + $this->extrafields + $customfields;

			if($_POST['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			if($_POST['save'])
			{
				$totalerrors = 0;
				if(!count($prefs))
				{
					$errors[$totalerrors++] = lang('You must select at least 1 column to display');
				}
				if(!$totalerrors)
				{
					@reset($qfields);
					$this->bo->save_preferences($prefs,$other,$qfields,$fcat_id);
					$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
				}
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Addressbook').' '.lang('Preferences');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			if($totalerrors)
			{
				echo '<p><center>' . $GLOBALS['phpgw']->common->error_list($errors) . '</center>';
			}

			$GLOBALS['phpgw']->template->set_file(array('preferences' => 'preferences.tpl'));

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php','menuaction=addressbook.uiaddressbook.preferences'));

			$i = 0; $j = 0;
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

//			while(list($col, $descr) = each($qfields))
			foreach($qfields as $col => $descr)
			{
				/* echo '<br>test: $col - $i $j - ' . count($abc); */
				$i++; $j++;
				$showcol = $this->display_name($col);
				if(!$showcol) { $showcol = $col; }
				/* yank the *'s prior to testing for a valid column description */
				$coltest = ereg_replace('\*','',$showcol);
				if($coltest)
				{
					$GLOBALS['phpgw']->template->set_var($col,$showcol);
					if($GLOBALS['phpgw_info']['user']['preferences']['addressbook'][$col])
					{
						$GLOBALS['phpgw']->template->set_var($col.'_checked',' checked');
					}
					else
					{
						$GLOBALS['phpgw']->template->set_var($col.'_checked','');
					}
				}
			}

			if($customfields)
			{
				$custom_var = '
  <tr>
    <td bgcolor="'.$tr_color.'" colspan="6"><font color="#000000" face="">'.lang('Custom Fields').':</font></td>
  </tr>
';
				$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
				$i = 0;
				while(list($cf) = each($customfields))
				{
					if(!($i % 6))
					{
						$custom_var .= "\n  <tr bgcolor='$tr_color'>\n";
					}
					$custom_var .= '    <td><input type="checkbox" name="prefs['
						. strtolower($cf) . ']"'
						. ($this->prefs[$cf] ? ' checked' : '')
						. '>' . str_replace('_',' ',$cf) . '</option></td>' . "\n";

					if(!(++$i % 6))
					{
						echo "</tr>\n";
					}
				}
				if($i = 6 - ($i % 6))
				{
					$custom_var .= "    <td colspan=$i>&nbsp;</td>\n  </tr>\n";
				}
				$GLOBALS['phpgw']->template->set_var('custom_fields',$custom_var);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('custom_fields','');
			}

			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
			$GLOBALS['phpgw']->template->set_var(tr_color,$tr_color);
			$GLOBALS['phpgw']->template->set_var('lang_showbirthday',lang('show birthday reminders on main screen'));

			if($this->prefs['mainscreen_showbirthdays'])
			{
				$GLOBALS['phpgw']->template->set_var('show_birthday',' checked');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('show_birthday','');
			}

			$list = array(
				'none'    => lang('Show all'),
				'yours'   => lang('Only yours'),
				'private' => lang('Private') /*,
				'blank'   => lang('Blank') */
			);
			$GLOBALS['phpgw']->template->set_var('lang_default_filter',lang('Default Filter'));
			$GLOBALS['phpgw']->template->set_var('filter_select',$this->formatted_list('other[default_filter]',$list,$this->prefs['default_filter']));

			$GLOBALS['phpgw']->template->set_var('lang_autosave',lang('Autosave default category'));
			if($this->prefs['autosave_category'])
			{
				$GLOBALS['phpgw']->template->set_var('autosave',' checked');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('autosave','');
			}
			$GLOBALS['phpgw']->template->set_var('lang_defaultcat',lang('Default Category'));
			$GLOBALS['phpgw']->template->set_var('cat_select',$this->cat_option($this->prefs['default_category'], false, false));
			$GLOBALS['phpgw']->template->set_var('lang_fields',lang('Fields to show in address list'));
			$GLOBALS['phpgw']->template->set_var('lang_personal',lang('Personal'));
			$GLOBALS['phpgw']->template->set_var('lang_business',lang('Business'));
			$GLOBALS['phpgw']->template->set_var('lang_home',lang('Home'));
			$GLOBALS['phpgw']->template->set_var('lang_phones',lang('Extra').' '.lang('Phone Numbers'));
			$GLOBALS['phpgw']->template->set_var('lang_other',lang('Other').' '.lang('Fields'));
			$GLOBALS['phpgw']->template->set_var('lang_otherprefs',lang('Other').' '.lang('Preferences'));
			$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('Cancel'));
			$GLOBALS['phpgw']->template->set_var('th_bg',  $GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('th_text',$GLOBALS['phpgw_info']['theme']['th_text']);
			$GLOBALS['phpgw']->template->set_var('row_on', $GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);

			$GLOBALS['phpgw']->template->pparse('out','preferences');
		}

		function get_form()
		{
			$entry   = $_POST['entry'];
			$fcat_id = $_POST['fcat_id'];
			$referer = $entry['referer'] ? $entry['referer'] : $_GET['referer'];
			$referer = $referer ? $referer : $_POST['referer'];

			$test = @unserialize(rawurldecode($entry));
			if($test && ($test != $entry))
			{
				$entry = $test;
			}
			/* _debug_array($entry); exit; */

			if(!$entry['bday_month'] && !$entry['bday_day'] && !$entry['bday_year'])
			{
				$fields['bday'] = '';
			}
			else
			{
				$bday_day = $entry['bday_day'];
				if(strlen($bday_day) == 1)
				{
					$bday_day = '0' . $entry['bday_day'];
				}
				$fields['bday'] = $entry['bday_month'] . '/' . $bday_day . '/' . $entry['bday_year'];
			}

			$fields['url'] = $entry['url'] == 'http://' ? '' : $entry['url'];


			$fields['lid']				= $entry['lid'];
			$fields['org_name']				= $entry['company'];
			$fields['org_unit']				= $entry['department'];
			$fields['n_given']				= $entry['firstname'];
			$fields['n_family']				= $entry['lastname'];
			$fields['n_middle']				= $entry['middle'];
			$fields['n_prefix']				= $entry['prefix'];
			$fields['n_suffix']				= $entry['suffix'];
			if($entry['prefix']) { $pspc = ' '; }
			if($entry['middle']) { $mspc = ' '; } else { $nspc = ' '; }
			if($entry['suffix']) { $sspc = ' '; }
			$fields['fn']					= $entry['prefix'].$pspc.$entry['firstname'].$nspc.$mspc.$entry['middle'].$mspc.$entry['lastname'].$sspc.$entry['suffix'];
			$fields['email']				= $entry['email'];
			$fields['email_type']			= $entry['email_type'];
			$fields['email_home']			= $entry['hemail'];
			$fields['email_home_type']		= $entry['hemail_type'];
			$fields['title']				= $entry['title'];
			$fields['tel_work']				= $entry['wphone'];
			$fields['tel_home']				= $entry['hphone'];
			$fields['tel_fax']				= $entry['fax'];
			$fields['tel_pager']			= $entry['pager'];
			$fields['tel_cell']				= $entry['mphone'];
			$fields['tel_msg']				= $entry['msgphone'];
			$fields['tel_car'] 				= $entry['carphone'];
			$fields['tel_video']			= $entry['vidphone'];
			$fields['tel_isdn']				= $entry['isdnphone'];
			$fields['adr_one_street']		= $entry['bstreet'];
			$fields['adr_one_locality']		= $entry['bcity'];
			$fields['adr_one_region']		= $entry['bstate'];
			$fields['adr_one_postalcode']	= $entry['bzip'];
			$fields['adr_one_countryname']	= $entry['bcountry'];

			if($entry['one_dom'])
			{
				$typea .= 'dom;';
			}
			if($entry['one_intl'])
			{
				$typea .= 'intl;';
			}
			if($entry['one_parcel'])
			{
				$typea .= 'parcel;';
			}
			if($entry['one_postal'])
			{
				$typea .= 'postal;';
			}
			$fields['adr_one_type'] = substr($typea,0,-1);

			$fields['address2']				= $entry['address2'];
			$fields['address3']				= $entry['address3'];

			$fields['adr_two_street']		= $entry['hstreet'];
			$fields['adr_two_locality']		= $entry['hcity'];
			$fields['adr_two_region']		= $entry['hstate'];
			$fields['adr_two_postalcode']	= $entry['hzip'];
			$fields['adr_two_countryname']	= $entry['hcountry'];

			if($entry['two_dom'])
			{
				$typeb .= 'dom;';
			}
			if($entry['two_intl'])
			{
				$typeb .= 'intl;';
			}
			if($entry['two_parcel'])
			{
				$typeb .= 'parcel;';
			}
			if($entry['two_postal'])
			{
				$typeb .= 'postal;';
			}
			$fields['adr_two_type'] = substr($typeb,0,-1);

			$custom = $this->fields->read_custom_fields();
			foreach($custom as $name => $val)
			{
				$fields[$val['name']] = $entry[$val['name']];
			}

			$fields['ophone']	= $entry['ophone'];
			$fields['tz']		= $entry['timezone'];
			$fields['pubkey']	= $entry['pubkey'];
			$fields['note']		= $entry['notes'];
			$fields['label']	= $entry['label'];

			if($entry['access'] == 'True')
			{
				$fields['access'] = 'private';
			}
			else
			{
				$fields['access'] = 'public';
			}

			if(is_array($fcat_id))
			{
				$fields['cat_id'] = count($fcat_id) > 1 ? ','.implode(',',$fcat_id).',' : (int)$fcat_id[0];
			}
			else
			{
				$fields['cat_id'] = (int)$fcat_id;
			}

			$fields['ab_id']   = $entry['ab_id'];
			$fields['tid']     = $entry['tid'];
			if(!$fields['tid'])
			{
				$fields['tid'] = 'n';
			}

			$fields['referer'] = $referer;
			/* _debug_array($fields);exit; */
			return $fields;
		} /* end get_form() */

		/* Following used for add/edit */
		function addressbook_form($format,$action,$title='',$fields='',$customfields='',$cat_id='')
		{
			$referer = $_GET['referer'] ? $_GET['referer'] : $_POST['referer'];

			$GLOBALS['phpgw']->template->set_file(array('form' => 'form.tpl'));

			if(($GLOBALS['phpgw_info']['server']['countrylist'] == 'user_choice' &&
				$GLOBALS['phpgw_info']['user']['preferences']['common']['countrylist'] == 'use_select') ||
				($GLOBALS['phpgw_info']['server']['countrylist'] == 'force_select'))
			{
				$countrylist  = True;
			}

			$email      = $fields['email'];
			$emailtype  = $fields['email_type'];
			$hemail     = $fields['email_home'];
			$hemailtype = $fields['email_home_type'];
			$firstname  = $fields['n_given'];
			$middle     = $fields['n_middle'];
			$prefix     = $fields['n_prefix'];
			$suffix     = $fields['n_suffix'];
			$lastname   = $fields['n_family'];
			$title      = $fields['title'];
			$wphone     = $fields['tel_work'];
			$hphone     = $fields['tel_home'];
			$fax        = $fields['tel_fax'];
			$pager      = $fields['tel_pager'];
			$mphone     = $fields['tel_cell'];
			$ophone     = $fields['ophone'];
			$msgphone   = $fields['tel_msg'];
			$isdnphone  = $fields['tel_isdn'];
			$carphone   = $fields['tel_car'];
			$vidphone   = $fields['tel_video'];
			$preferred  = $fields['tel_prefer'];

			$bstreet    = $fields['adr_one_street'];
			$address2   = $fields['address2'];
			$address3   = $fields['address3'];
			$bcity      = $fields['adr_one_locality'];
			$bstate     = $fields['adr_one_region'];
			$bzip       = $fields['adr_one_postalcode'];
			$bcountry   = $fields['adr_one_countryname'];
			$one_dom    = $fields['one_dom'];
			$one_intl   = $fields['one_intl'];
			$one_parcel = $fields['one_parcel'];
			$one_postal = $fields['one_postal'];

			$hstreet    = $fields['adr_two_street'];
			$hcity      = $fields['adr_two_locality'];
			$hstate     = $fields['adr_two_region'];
			$hzip       = $fields['adr_two_postalcode'];
			$hcountry   = $fields['adr_two_countryname'];
			$btype      = $fields['adr_two_type'];
			$two_dom    = $fields['two_dom'];
			$two_intl   = $fields['two_intl'];
			$two_parcel = $fields['two_parcel'];
			$two_postal = $fields['two_postal'];

			$timezone   = $fields['tz'];
			$bday       = $fields['bday'];
			$notes      = stripslashes($fields['note']);
			$label      = stripslashes($fields['label']);
			$company    = $fields['org_name'];
			$department = $fields['org_unit'];
			$url        = $fields['url'];
			$pubkey     = $fields['pubkey'];
			$access     = $fields['access'];
			if(!$cat_id)
			{
				$cat_id = $fields['cat_id'];
			}
			$cats_link = $this->cat_option($cat_id,True,False,True);

			if($access == 'private')
			{
				$access_check = ' checked';
			}
			else
			{
				$access_check = '';
			}

			if($customfields)
			{
//				while(list($name,$value) = each($customfields))
				foreach($customfields as $name => $value)
				{
					$value = str_replace('_',' ',$value);
					$custom .= '
  <tr bgcolor="' . $GLOBALS['phpgw_info']['theme']['row_off'] . '">
    <td>&nbsp;</td>
    <td><font color="' . $GLOBALS['phpgw_info']['theme']['th_text'] . '" face="" size="-1">'.$value.':</font></td>
    <td colspan="3"><INPUT size="30" name="entry[' . $name . ']" value="' . $fields[$name] . '"></td>
  </tr>
';
				}
			}

			if($format != "view")
			{
				/* Preferred phone number radio buttons */
				$pref[0] = '<font size="-2">';
				$pref[1] = '(' . lang('pref') . ')</font>';
//				while(list($name,$val) = each($this->bo->tel_types))
				foreach($this->bo->tel_types  as $name => $val)
				{
					$str[$name] = "\n".'      <input type="radio" name="entry[tel_prefer]" value="'.$name.'"';
					if($name == $preferred)
					{
						$str[$name] .= ' checked';
					}
					$str[$name] .= '>';
					$str[$name] = $pref[0].$str[$name].$pref[1];
					$GLOBALS['phpgw']->template->set_var("pref_".$name,$str[$name]);
				}

				if(strlen($bday) > 2)
				{
					list($month, $day, $year) = split('/', $bday);
					$temp_month[$month] = ' selected';
					$bday_month = '<select name="entry[bday_month]">'
						. '<option value=""'   . $temp_month[0]  . '>' . '</option>'
						. '<option value="1"'  . $temp_month[1]  . '>' . lang('january')   . '</option>'
						. '<option value="2"'  . $temp_month[2]  . '>' . lang('february')  . '</option>'
						. '<option value="3"'  . $temp_month[3]  . '>' . lang('march')     . '</option>'
						. '<option value="4"'  . $temp_month[4]  . '>' . lang('april')     . '</option>'
						. '<option value="5"'  . $temp_month[5]  . '>' . lang('may')       . '</option>'
						. '<option value="6"'  . $temp_month[6]  . '>' . lang('june')      . '</option>'
						. '<option value="7"'  . $temp_month[7]  . '>' . lang('july')      . '</option>'
						. '<option value="8"'  . $temp_month[8]  . '>' . lang('august')    . '</option>'
						. '<option value="9"'  . $temp_month[9]  . '>' . lang('september') . '</option>'
						. '<option value="10"' . $temp_month[10] . '>' . lang('october')   . '</option>'
						. '<option value="11"' . $temp_month[11] . '>' . lang('november')  . '</option>'
						. '<option value="12"' . $temp_month[12] . '>' . lang('december')  . '</option>'
						. '</select>';
					$bday_day  = '<input maxlength="2" name="entry[bday_day]"  value="' . $day . '" size="2">';
					$bday_year = '<input maxlength="4" name="entry[bday_year]" value="' . $year . '" size="4">';
				}
				else
				{
					$bday_month = '<select name="entry[bday_month]">'
						. '<option value="" selected> </option>'
						. '<option value="1">'  . lang('january')   . '</option>'
						. '<option value="2">'  . lang('february')  . '</option>'
						. '<option value="3">'  . lang('march')     . '</option>'
						. '<option value="4">'  . lang('april')     . '</option>'
						. '<option value="5">'  . lang('may')       . '</option>'
						. '<option value="6">'  . lang('june')      . '</option>'
						. '<option value="7">'  . lang('july')      . '</option>'
						. '<option value="8">'  . lang('august')    . '</option>'
						. '<option value="9">'  . lang('september') . '</option>'
						. '<option value="10">' . lang('october')   . '</option>'
						. '<option value="11">' . lang('november')  . '</option>'
						. '<option value="12">' . lang('december')  . '</option>'
						. '</select>';
					$bday_day  = '<input name="entry[bday_day]"  size="2" maxlength="2">';
					$bday_year = '<input name="entry[bday_year]" size="4" maxlength="4">';
				}

				$time_zone = '<select name="entry[timezone]">' . "\n";
				for($i = -23; $i<24; $i++)
				{
					$time_zone .= '<option value="' . $i . '"';
					if($i == $timezone)
					{
						$time_zone .= ' selected';
					}
					if($i < 1)
					{
						$time_zone .= '>' . $i . '</option>' . "\n";
					}
					else
					{
						$time_zone .= '>+' . $i . '</option>' . "\n";
					}
				}
				$time_zone .= '</select>' . "\n";

				$email_type = '<select name=entry[email_type]>';
//				while($type = each($this->bo->email_types))
				foreach($this->bo->email_types as $type => $name)
				{
					$email_type .= '<option value="' . $type . '"';
					if($type == $emailtype)
					{
						$email_type .= ' selected';
					}
					$email_type .= '>' . $name . '</option>';
				}
				$email_type .= '</select>';

//				reset($this->bo->email_types);
				$hemail_type = '<select name=entry[hemail_type]>';
//				while($type = each($this->bo->email_types))
				foreach($this->bo->email_types as $type => $name)
				{
					$hemail_type .= '<option value="' . $type . '"';
					if($type == $hemailtype)
					{
						$hemail_type .= ' selected';
					}
					$hemail_type .= '>' . $name . '</option>';
				}
				$hemail_type .= '</select>';

//				reset($this->bo->adr_types);
//				while(list($type,$val) = each($this->bo->adr_types))
				foreach($this->bo->adr_types as $type => $val)
				{
					$badrtype .= "\n".'<INPUT type="checkbox" name="entry[one_'.$type.']"';
					$ot = 'one_'.$type;
					eval("
						if(\$$ot=='on') {
							\$badrtype .= ' value=\"on\" checked';
						}
					");
					$badrtype .= '>'.$val;
				}

//				reset($this->bo->adr_types);
//				while(list($type,$val) = each($this->bo->adr_types))
				foreach($this->bo->adr_types as $type => $val)
				{
					$hadrtype .= "\n".'<INPUT type="checkbox" name="entry[two_'.$type.']"';
					$tt = 'two_'.$type;
					eval("
						if(\$$tt=='on') {
							\$hadrtype .= ' value=\"on\" checked';
						}
					");
					$hadrtype .= '>'.$val;
				}

				$notes  = '<TEXTAREA cols="60" name="entry[notes]" rows="4">' . $notes . '</TEXTAREA>';
				$label  = '<TEXTAREA cols="60" name="entry[label]" rows="6">' . $label . '</TEXTAREA>';
				$pubkey = '<TEXTAREA cols="60" name="entry[pubkey]" rows="6">' . $pubkey . '</TEXTAREA>';
			}
			else
			{
				$notes = '<form><TEXTAREA cols="60" name="entry[notes]" rows="4">'
					. $notes . '</TEXTAREA></form>';
				if($bday == '//')
				{
					$bday = '';
				}
			}

			if($action)
			{
				echo '<FORM action="' . $GLOBALS['phpgw']->link('/index.php', $action . '&referer='.urlencode($referer)).'" method="post">';
			}

			if(!ereg('^http://',$url))
			{
				$url = 'http://' . $url;
			}

			$birthday = $GLOBALS['phpgw']->common->dateformatorder($bday_year,$bday_month,$bday_day)
				. '<font face="'.$theme["font"].'" size="-2">'.lang('(e.g. 1969)').'</font>';
			if($format == 'edit')
			{
				$create .= '<tr bgcolor="' . $GLOBALS['phpgw_info']['theme']['th_bg'] . '"><td colspan="2"><font size="-1">' . lang("Created by") . ':</font></td>'
					. '<td colspan="3"><font size="-1">'
					. $GLOBALS['phpgw']->common->grab_owner_name($fields["owner"]);
			}
			else
			{
				$create .= '';
			}

			$GLOBALS['phpgw']->template->set_var('lang_home',lang('Home'));
			$GLOBALS['phpgw']->template->set_var('lang_business',lang('Business'));
			$GLOBALS['phpgw']->template->set_var('lang_personal',lang('Personal'));

			$GLOBALS['phpgw']->template->set_var('lang_lastname',lang('Last Name'));
			$GLOBALS['phpgw']->template->set_var('lastname',$lastname);
			$GLOBALS['phpgw']->template->set_var('lang_firstname',lang('First Name'));
			$GLOBALS['phpgw']->template->set_var('firstname',$firstname);
			$GLOBALS['phpgw']->template->set_var('lang_middle',lang('Middle Name'));
			$GLOBALS['phpgw']->template->set_var('middle',$middle);
			$GLOBALS['phpgw']->template->set_var('lang_prefix',lang('Prefix'));
			$GLOBALS['phpgw']->template->set_var('prefix',$prefix);
			$GLOBALS['phpgw']->template->set_var('lang_suffix',lang('Suffix'));
			$GLOBALS['phpgw']->template->set_var('suffix',$suffix);
			$GLOBALS['phpgw']->template->set_var('lang_birthday',lang('Birthday'));
			$GLOBALS['phpgw']->template->set_var('birthday',$birthday);

			$GLOBALS['phpgw']->template->set_var('lang_company',lang('Company Name'));
			$GLOBALS['phpgw']->template->set_var('company',$company);
			$GLOBALS['phpgw']->template->set_var('lang_department',lang('Department'));
			$GLOBALS['phpgw']->template->set_var('department',$department);
			$GLOBALS['phpgw']->template->set_var('lang_title',lang('Title'));
			$GLOBALS['phpgw']->template->set_var('title',$title);
			$GLOBALS['phpgw']->template->set_var('lang_email',lang('Business Email'));
			$GLOBALS['phpgw']->template->set_var('email',$email);
			$GLOBALS['phpgw']->template->set_var('lang_email_type',lang('Business EMail Type'));
			$GLOBALS['phpgw']->template->set_var('email_type',$email_type);
			$GLOBALS['phpgw']->template->set_var('lang_url',lang('URL'));
			$GLOBALS['phpgw']->template->set_var('url',$url);
			$GLOBALS['phpgw']->template->set_var('lang_timezone',lang('time zone offset'));
			$GLOBALS['phpgw']->template->set_var('timezone',$time_zone);
			$GLOBALS['phpgw']->template->set_var('lang_fax',lang('Business Fax'));
			$GLOBALS['phpgw']->template->set_var('fax',$fax);
			$GLOBALS['phpgw']->template->set_var('lang_wphone',lang('Business Phone'));
			$GLOBALS['phpgw']->template->set_var('wphone',$wphone);
			$GLOBALS['phpgw']->template->set_var('lang_pager',lang('Pager'));
			$GLOBALS['phpgw']->template->set_var('pager',$pager);
			$GLOBALS['phpgw']->template->set_var('lang_mphone',lang('Cell Phone'));
			$GLOBALS['phpgw']->template->set_var('mphone',$mphone);
			$GLOBALS['phpgw']->template->set_var('lang_msgphone',lang('Message Phone'));
			$GLOBALS['phpgw']->template->set_var('msgphone',$msgphone);
			$GLOBALS['phpgw']->template->set_var('lang_isdnphone',lang('ISDN Phone'));
			$GLOBALS['phpgw']->template->set_var('isdnphone',$isdnphone);
			$GLOBALS['phpgw']->template->set_var('lang_carphone',lang('Car Phone'));
			$GLOBALS['phpgw']->template->set_var('carphone',$carphone);
			$GLOBALS['phpgw']->template->set_var('lang_vidphone',lang('Video Phone'));
			$GLOBALS['phpgw']->template->set_var('vidphone',$vidphone);

			$GLOBALS['phpgw']->template->set_var('lang_ophone',lang('Other Number'));
			$GLOBALS['phpgw']->template->set_var('ophone',$ophone);
			$GLOBALS['phpgw']->template->set_var('lang_bstreet',lang('Business Street'));
			$GLOBALS['phpgw']->template->set_var('bstreet',$bstreet);
			$GLOBALS['phpgw']->template->set_var('lang_address2',lang('Address Line 2'));
			$GLOBALS['phpgw']->template->set_var('address2',$address2);
			$GLOBALS['phpgw']->template->set_var('lang_address3',lang('Address Line 3'));
			$GLOBALS['phpgw']->template->set_var('address3',$address3);
			$GLOBALS['phpgw']->template->set_var('lang_bcity',lang('Business City'));
			$GLOBALS['phpgw']->template->set_var('bcity',$bcity);
			$GLOBALS['phpgw']->template->set_var('lang_bstate',lang('Business State'));
			$GLOBALS['phpgw']->template->set_var('bstate',$bstate);
			$GLOBALS['phpgw']->template->set_var('lang_bzip',lang('Business Zip Code'));
			$GLOBALS['phpgw']->template->set_var('bzip',$bzip);
			$GLOBALS['phpgw']->template->set_var('lang_bcountry',lang('Business Country'));
			$GLOBALS['phpgw']->template->set_var('bcountry',$bcountry);
			if($countrylist)
			{
				$GLOBALS['phpgw']->template->set_var('bcountry',$GLOBALS['phpgw']->country->form_select($bcountry,'entry[bcountry]'));
			}
			else
			{
				 $GLOBALS['phpgw']->template->set_var('bcountry','<input name="entry[bcountry]" value="' . $bcountry . '">');
			}
			$GLOBALS['phpgw']->template->set_var('lang_badrtype',lang('Address Type'));
			$GLOBALS['phpgw']->template->set_var('badrtype',$badrtype);

			$GLOBALS['phpgw']->template->set_var('lang_hphone',lang('Home Phone'));
			$GLOBALS['phpgw']->template->set_var('hphone',$hphone);
			$GLOBALS['phpgw']->template->set_var('lang_hemail',lang('Home Email'));
			$GLOBALS['phpgw']->template->set_var('hemail',$hemail);
			$GLOBALS['phpgw']->template->set_var('lang_hemail_type',lang('Home EMail Type'));
			$GLOBALS['phpgw']->template->set_var('hemail_type',$hemail_type);
			$GLOBALS['phpgw']->template->set_var('lang_hstreet',lang('Home Street'));
			$GLOBALS['phpgw']->template->set_var('hstreet',$hstreet);
			$GLOBALS['phpgw']->template->set_var('lang_hcity',lang('Home City'));
			$GLOBALS['phpgw']->template->set_var('hcity',$hcity);
			$GLOBALS['phpgw']->template->set_var('lang_hstate',lang('Home State'));
			$GLOBALS['phpgw']->template->set_var('hstate',$hstate);
			$GLOBALS['phpgw']->template->set_var('lang_hzip',lang('Home Zip Code'));
			$GLOBALS['phpgw']->template->set_var('hzip',$hzip);
			$GLOBALS['phpgw']->template->set_var('lang_hcountry',lang('Home Country'));
			if($countrylist)
			{
				$GLOBALS['phpgw']->template->set_var('hcountry',$GLOBALS['phpgw']->country->form_select($hcountry,'entry[hcountry]'));
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('hcountry','<input name="entry[hcountry]" value="' . $hcountry . '">');
			}
			$GLOBALS['phpgw']->template->set_var('lang_hadrtype',lang('Address Type'));
			$GLOBALS['phpgw']->template->set_var('hadrtype',$hadrtype);

			$GLOBALS['phpgw']->template->set_var('create',$create);
			$GLOBALS['phpgw']->template->set_var('lang_notes',lang('notes'));
			$GLOBALS['phpgw']->template->set_var('notes',$notes);
			$GLOBALS['phpgw']->template->set_var('lang_label',lang('label'));
			$GLOBALS['phpgw']->template->set_var('label',$label);
			$GLOBALS['phpgw']->template->set_var('lang_pubkey',lang('Public Key'));
			$GLOBALS['phpgw']->template->set_var('pubkey',$pubkey);
			$GLOBALS['phpgw']->template->set_var('access_check',$access_check);

			$GLOBALS['phpgw']->template->set_var('lang_private',lang('Private'));

			$GLOBALS['phpgw']->template->set_var('lang_cats',lang('Category'));
			$GLOBALS['phpgw']->template->set_var('cats_link',$cats_link);
			if($customfields)
			{
				$GLOBALS['phpgw']->template->set_var('lang_custom',lang('Custom Fields').':');
				$GLOBALS['phpgw']->template->set_var('custom',$custom);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('lang_custom','');
				$GLOBALS['phpgw']->template->set_var('custom','');
			}
			$GLOBALS['phpgw']->template->set_var('th_bg',   $GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('th_text', $GLOBALS['phpgw_info']['theme']['th_text']);
			$GLOBALS['phpgw']->template->set_var('row_on',  $GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('row_off', $GLOBALS['phpgw_info']['theme']['row_off']);
			$GLOBALS['phpgw']->template->set_var('row_text',$GLOBALS['phpgw_info']['theme']['row_text']);

			$GLOBALS['phpgw']->template->pfp('out','form');
		} /* end form function */
	}
?>

<?php
	/**************************************************************************
	* eGroupWare API - Accounts manager for SQL                                *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	*        and Dan Kuykendall <seek3r@phpgroupware.org>                      *
	*        and Bettina Gille [ceb@phpgroupware.org]                          *
	* View and manipulate account records using SQL                            *
	* Copyright (C) 2000 - 2002 Joseph Engo                                    *
	* Copyright (C) 2003 Joseph Engo, Bettina Gille                            *
	* ------------------------------------------------------------------------ *
	* This library is part of the eGroupWare API                               *
	* http://www.egroupware.org                                                *
	* ------------------------------------------------------------------------ *
	* This library is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU Lesser General Public License as published by *
	* the Free Software Foundation; either version 2.1 of the License,         *
	* or any later version.                                                    *
	* This library is distributed in the hope that it will be useful, but      *
	* WITHOUT ANY WARRANTY; without even the implied warranty of               *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     *
	* See the GNU Lesser General Public License for more details.              *
	* You should have received a copy of the GNU Lesser General Public License *
	* along with this library; if not, write to the Free Software Foundation,  *
	* Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            *
	**************************************************************************/
	/* $Id: class.accounts_sql.inc.php,v 1.114.2.4 2004/08/31 15:16:56 mgalgoci Exp $ */

	/*!
	 @class_start accounts
	 @abstract Class for handling user and group accounts
	*/
	class accounts_
	{
		var $db;
		var $account_id;
		var $data;
		var $total;

		function accounts_()
		{
			//copyobj($GLOBALS['phpgw']->db,$this->db);
			$this->db = is_object($GLOBALS['phpgw']->db) ? $GLOBALS['phpgw']->db : $GLOBALS['phpgw_setup']->db;
			
			$this->table = 'phpgw_accounts';
			$this->db->set_app('phpgwapi');	// to load the right table-definitions for insert, select, update, ...
			$this->FormConfig($args);
		}

		function list_methods($_type='xmlrpc')
		{
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}

			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'get_list' => array(
							'function'  => 'get_list',
							'signature' => array(array(xmlrpcStruct)),
							'docstring' => lang('Returns a full list of accounts on the system.  Warning: This is return can be quite large')
						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
						)
					);
					return $xml_functions;
					break;
				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
					break;
			}
		}

		/*!
		@function read_repository
		@abstract grabs the records from the data store
		*/
		function read_repository()
		{
			$this->db->select($this->table,"`account_id`,`account_lid`, `account_firstname`, `account_lastname`, `account_lastlogin`, `account_lastloginfrom`, `account_lastpwd_change`, `account_status`, `account_expires`, `account_type`, `account_primary_group`, `account_email`, `account_linkedin`, DATE_FORMAT(`account_membership_date`,'%d/%m/%y') as account_membership_date",array('account_id'=>$this->account_id),__LINE__,__FILE__);
			$this->db->next_record();

			$this->data['userid']            = $this->db->f('account_lid');
			$this->data['account_id']        = $this->db->f('account_id');
			$this->data['account_lid']       = $this->db->f('account_lid');
			$this->data['firstname']         = $this->db->f('account_firstname');
			$this->data['lastname']          = $this->db->f('account_lastname');
			$this->data['fullname']          = $this->db->f('account_firstname') . ' ' . $this->db->f('account_lastname');
			$this->data['lastlogin']         = $this->db->f('account_lastlogin');
			$this->data['lastloginfrom']     = $this->db->f('account_lastloginfrom');
			$this->data['lastpasswd_change'] = $this->db->f('account_lastpwd_change');
			$this->data['status']            = $this->db->f('account_status');
			$this->data['expires']           = $this->db->f('account_expires');
			$this->data['person_id']         = $this->db->f('person_id');
			$this->data['account_primary_group'] = $this->db->f('account_primary_group');
			$this->data['email']             = $this->db->f('account_email');
			$this->data['linkedin']          = $this->db->f('account_linkedin');
			$this->data['membership_date']          = $this->db->f('account_membership_date');

			return $this->data;
		}

		/*!
		@function save_repository
		@abstract saves the records to the data store
		*/
		function save_repository()
		{
			$this->db->update($this->table,array(
				'account_firstname' => $this->data['firstname'],
				'account_lastname'  => $this->data['lastname'],
				'account_status'    => $this->data['status'],
				'account_expires'   => $this->data['expires'],
				'account_lid'       => $this->data['account_lid'],
				'person_id'         => $this->data['person_id'],
				'account_primary_group' => $this->data['account_primary_group'],
				'account_email'     => $this->data['email'],
				'account_linkedin'     => $this->data['linkedin'],
				'account_membership_date' => $this->data['membership_date'],
			),array(
				'account_id'        => $this->account_id
			),__LINE__,__FILE__);
		}

		function delete($accountid = '')
		{
			$account_id = get_account_id($accountid);

			/* Do this last since we are depending upon this record to get the account_lid above */
			$this->db->lock(Array($this->table));
			$this->db->delete($this->table,array('account_id'=>$account_id),__LINE__,__FILE__);
			$this->db->unlock();
		}
		

		function get_online_count($_type='both',$start = '',$sort = '', $order = '', $query = '', $offset = '',$query_type='')
		{
			if (! $sort)
			{
				$sort = "DESC";
			}
			$data = $GLOBALS['phpgw']->crypto->decrypt($data);
			if (!empty($order) && preg_match('/^[a-zA-Z_0-9, ]+$/',$order) && (empty($sort) || preg_match('/^(DESC|ASC|desc|asc)$/',$sort)))
			{
				$orderclause = "ORDER BY $order $sort, account_lid ASC";
			}
			else
			{
				$orderclause = "ORDER BY account_lid ASC";
			}

			switch($_type)
			{
				case 'accounts':
					$whereclause = "WHERE session_lid != 'anonymous' AND session_flags !='A' AND session_lid != 'admin'";
					break;
				case 'groups':
					$whereclause = "WHERE ";
					break;
				default:
					$whereclause = '';
			}

						
			
			$this->db->query("SELECT count(distinct session_lid) FROM phpgw_sessions $whereclause");

			//$this->db->query("SELECT count(distinct session_lid) FROM phpgw_sessions". 
      //                           " WHERE session_flags !='A' AND session_lid != 'admin'");

			$this->db->next_record();
			$total = $this->db->f(0);
			return $total;
		}		
		
		function get_guest_count($_type='both')
		{
                $sql="SELECT count(distinct session_ip) FROM phpgw_sessions where session_flags='A' and session_logintime > ".(time() - $GLOBALS['phpgw_info']['server']['sessions_timeout']);
			
			$this->db->query($sql);
			$this->db->next_record();
			$total = $this->db->f(0);
			return $total;
		}		
        
		function get_online_members($_type='both',$start = '',$sort = '',$order='',$query='',$offset= '',$query_type='',$offline=TRUE)
		{
			if (! $sort)
			{
				$sort = "DESC";
			}

			if (!empty($order) && preg_match('/^[a-zA-Z_0-9, ]+$/',$order) && (empty($sort) || preg_match('/^(DESC|ASC|desc|asc)$/',$sort)))
			{
				$orderclause = "ORDER BY account_session DESC, $order $sort,account_firstname ASC, account_lastname ASC";
			}
			else
			{
				$orderclause = "ORDER BY  account_session ASC, account_lid ASC";
			}

			switch($_type)
			{
				case 'accounts':
					$whereclause = "WHERE account_type = 'u' AND  account_primary_group != 6 AND account_primary_group != 35 AND account_lid != 'anonymous'";
					break;
				case 'accounts_a':
					$whereclause = "WHERE account_status = 'A' And account_type = 'u' AND  account_primary_group != 6 AND account_primary_group != 35 AND account_lid != 'anonymous'";
					break;	
				case 'accounts_p':
					$whereclause = "WHERE account_status != 'A' And account_type = 'u' AND  account_primary_group != 6 AND account_primary_group != 35 AND account_lid != 'anonymous'";
					break;	
				case 'groups':
					$whereclause = "WHERE account_type = 'g'";
					break;
				default:
					$whereclause = '';
			}
			if ($query)
			{
				
				if ($whereclause)
				{
					$whereclause .= ' AND ( ';
				}
				else
				{
					$whereclause = ' WHERE ( ';
				}
				switch($query_type)
				{
					case 'all':
					default:
						$query = '%'.$query;
						// fall-through
					case 'start':
						$query .= '%';
						// fall-through
					case 'exact':
						$query = $this->db->quote($query);
						$whereclause .= " account_firstname LIKE $query OR account_lastname LIKE $query OR account_lid LIKE $query )";
						break;
					case 'firstname':
						$query = $this->db->quote($query."%");
						$whereclause .= " account_firstname LIKE $query )";
						break;

					case 'lastname':
						$query = $this->db->quote($query."%");
						$whereclause .= " account_lastname LIKE $query )";
						break;
                                        case 'account_status':
						//$query = $this->db->quote($query);
						if ($query == 'A'){
						$whereclause .= " account_status = 'A' )";
						}else{
						$whereclause .= " account_status != 'A' )";
						}
						break;
					case 'lid':
					case 'email':
						$query = $this->db->quote('%'.$query.'%');
						$whereclause .= " account_$query_type LIKE $query )";
						break;
				}
			}
			$joiner= ($offline) ? " LEFT " : "";


			$sql = "select distinct
                                b.account_id, 
                                b.`account_lid`, 
                                
                                    IF(
                                        
                                          UNIX_TIMESTAMP()-`s`.`session_logintime`
                                         
                                        <= ".
                                        $GLOBALS['phpgw_info']['server']['sessions_timeout'].
                                        ",1,0
                                      )
                                     as account_session, 
                                b.`account_firstname`, 
                                b.`account_lastname`, 
                                b.`account_lastlogin`, 
                                b.`account_lastloginfrom`, 
                                b.`account_lastpwd_change`, 
                                b.`account_status`, 
                                b.`account_expires`, 
                                b.`account_type`, 
                                b.`account_primary_group`,
                                b.`account_email`,
                                b.`account_linkedin`,
                                pdo.`value` as account_occupation,
                                pdi.`value` as account_industry,
                                DATE_FORMAT(b.`account_membership_date`,'%d/%m/%y') as account_membership_date 
                                FROM `phpgw_accounts` as b".
                                $joiner."JOIN `phpgw_sessions` as s 
                                  on `account_lid`=REPLACE(`session_lid`,'@default','') ".
                                "LEFT JOIN members_profile_data pdo on b.account_id = pdo.owner  AND pdo.name='occupation' ".
                                "LEFT JOIN members_profile_data pdi on b.account_id = pdi.owner  AND pdi.name='industry' ".
                                $whereclause." ".
                                $orderclause;
                        echo $sql;
			if ($offset)
			{
				$this->db->limit_query($sql,$start,__LINE__,__FILE__,$offset);
			}
			elseif (is_numeric($start))
			{
				$this->db->limit_query($sql,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$accounts[] = Array(
					'account_id'        => $this->db->f('account_id'),
					'account_lid'       => $this->db->f('account_lid'),
					'account_session'       => $this->db->f('account_session'),
					'account_type'      => $this->db->f('account_type'),
					'account_firstname' => $this->db->f('account_firstname'),
					'account_lastname'  => $this->db->f('account_lastname'),
					'account_status'    => $this->db->f('account_status'),
					'account_expires'   => $this->db->f('account_expires'),
					'person_id'         => $this->db->f('person_id'),
					'account_primary_group' => $this->db->f('account_primary_group'),
					'account_email'     => $this->db->f('account_email'),
					'account_linkedin'     => $this->db->f('account_linkedin'),
					'account_membership_date'  => $this->db->f('account_membership_date'),
					'account_occupation'=> $this->db->f('account_occupation'),
				);
				$this->total = $this->total+1;
			}
			/*$this->db->query("SELECT count(*) FROM $this->table $whereclause");
			$this->db->next_record();
			$this->total = $this->db->f(0);*/

			return $accounts;
		}

		/**/
		function GetMySQLArray($sql)
		{
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$result = explode("\n", $this->db->f(0));
			$result = array_map("trim", $result);
			return $result;
		}
		
		var $formCfg, $lang;
		function FormConfig($args)
		{
			$this->lang = $GLOBALS['phpgw']->preferences->data[common][lang]; 
			
			$prof_profile = $this->GetMySQLArray("SELECT data from other_data where name='prof_profile' and lang='".$this->lang."'");
			$countries = $this->GetMySQLArray("SELECT data from other_data where name='countries_list'");
			$sports = $this->GetMySQLArray("SELECT data from other_data where name='favorite_sport' and lang='".$this->lang."'");
			$hobbies = $this->GetMySQLArray("SELECT data from other_data where name='interestsBase' and lang='".$this->lang."'");
            $industries = $this->GetMySQLArray("SELECT data from other_data where name='industries' and lang='".$this->lang."'");
            $occ_areas = $this->GetMySQLArray("SELECT data from other_data where name='occ_areas' and lang='".$this->lang."'");
			$ac_degree = $this->GetMySQLArray("SELECT data from other_data where name='ac_degree' and lang='".$this->lang."'");
			$how_did_u = $this->GetMySQLArray("SELECT data from other_data where name='how_did_u' and lang='".$this->lang."'");
			$sex = $this->GetMySQLArray("SELECT data from other_data where name='sex' and lang='".$this->lang."'");
			
			$this->formCfg = array	(
									"lists"	 => array(
														"prof_profile" => array(
																"control_id" => "prof_profile",
																"control_type" => "DDL",
																"source" 		=> $prof_profile,
																"use_key" => true,
																"eLggExternal" => true
																),
														"residence_country" => array(
																"control_id" => "residence_country",
																"control_type" => "DDL",
																"use_key" => false,
																"source" 		=> $countries,
																"eLggExternal" => true
																),

														"sex" => array(
																"control_id" => "sex",
																"control_type" => "DDL",
																"use_key" => true,
																"source" 		=> $sex,
																"eLggExternal" => true
																),
														"ac_degree" => array(
																"control_id" => "ac_degree",
																"control_type" => "DDL",
																"use_key" => true,
																"source" 		=> $ac_degree,
																"eLggExternal" => true
																),
														"favorite_sport" => array(
																"control_id" => "favorite_sport",
																"control_type" => "MDDL",
																"use_key" => true,
																"source" 		=> $sports,
																"eLggExternal" => true
																),
																
														"interestsBase" => array(
																"control_id" => "interestsBase",
																"control_type" => "MDDL",
																"use_key" => true,
																"source" 		=> $hobbies,
																"eLggExternal" => true
																),
														"industries" => array(
																"control_id" => "industries",
																"control_type" => "DDL",
																"use_key" => true,
																"source" 		=> $industries,
																"eLggExternal" => true
																),
														"occ_areas" => array(
																"control_id" => "occ_areas",
																"control_type" => "MDDL",
																"use_key" => true,
																"source" 		=> $occ_areas,
																"eLggExternal" => true
																)
																
													  )
									);
		}
		
		var $sqlRule;
		function get_where_clause($_type, $sort, $order, $useQueryString = true)
		{
			/*if($this->sqlRule) 
				return $this->sqlRule;*/
			
			if (isset($_POST["query"]))
				$query = $_POST["query"];

			if (!$sort)
			{
				$sort = "DESC";
			}
			
			if (!empty($order) && preg_match('/^[a-zA-Z_0-9, ]+$/',$order) && (empty($sort) || preg_match('/^(DESC|ASC|desc|asc)$/',$sort)))
			{
				$orderclause = "ORDER BY $order $sort, account_lid ASC";
			}
			else
			{
				$orderclause = "ORDER BY account_lid ASC";
			}
			$whereclause = "where 1=1 ";
			
			if ($_POST["wordChar"] && $useQueryString)
			{
				$letter = $this->db->quote($_POST["wordChar"]."%");
				$whereclause .= " AND ( account_firstname LIKE $letter OR account_lastname LIKE $letter OR account_lid LIKE $letter )";
			}
			switch($_type)
			{
				case 'accounts':
					$whereclause .= " AND account_type = 'u' AND  account_primary_group != 6 AND account_primary_group != 35 AND account_lid != 'anonymous'";
					break;
				case 'accounts_a':
					$whereclause .= " AND account_status = 'A' And account_type = 'u' AND  account_primary_group != 6 AND account_primary_group != 35 AND account_lid != 'anonymous'";
					break;	
				case 'accounts_p':
					$whereclause .= " AND account_status != 'A' And account_type = 'u' AND  account_primary_group != 6 AND account_primary_group != 35 AND account_lid != 'anonymous'";
					break;	
				case 'groups':
					$whereclause .= " AND account_type = 'g'";
					break;
				default:
					$whereclause .= '';
			}
			
			if ($query && $useQueryString)
			{
				switch($query_type)
				{
					case 'all':
					default:
					case 'start':	
						$whereclause .=	$this->get_AdditionalElggSearchClause($query);
						
						$query = '%'.$query;
						// fall-through
						$query .= '%';
						// fall-through
					case 'exact':
						$query = $this->db->quote($query);
						//$whereclause .= "  AND (account_firstname LIKE $query OR account_lastname LIKE $query OR account_lid LIKE $query )";
						break;
					case 'firstname':
					  $query = $this->db->quote($query."%");
						$whereclause .= "  AND (account_firstname LIKE $query )";
						break;
					case 'lastname':
						$query = $this->db->quote($query."%");
						$whereclause .= "  AND (account_lastname LIKE $query )";
						break;

					case 'lid':
					case 'email':
						$query = $this->db->quote('%'.$query.'%');
						$whereclause .= "  AND (account_$query_type LIKE $query)";
						break;
				}
			}
			
			
			if($useQueryString)
			{
				$whereclause .=	$this->get_AdditionalElggEqualClause("prof_profile");
				$whereclause .=	$this->get_AdditionalElggEqualClause("residence_country");
				$whereclause .=	$this->get_AdditionalElggEqualClause("occ_areas");
				$whereclause .=	$this->get_AdditionalElggEqualClause("industries");
			}
			$this->sqlRule = array("where"=>$whereclause, "order"=>$orderclause);
			//DebugLog($this->sqlRule);
			return $this->sqlRule;
		}
		
		function get_AdditionalElggSearchClause($query)
		{
			$t=trim($query);
			if($t == "")
				return ;
			//and (1=1 OR ... OR ...
			$elggEqual = " OR account_lid in (select username from members_users where ident in 
								(select distinct owner from members_profile_data where value like %s and (access='PUBLIC' or access='LOGGED_IN')) 
								or name like %s
						 )";
			$result = "";
			
			$arr = split(" ", $t);
			for($i=0; $i<count($arr); $i++)
				if(trim($arr[$i]) != "")
				{
					$value = $this->db->quote("%".$arr[$i]."%");
					$result .= sprintf($elggEqual, $value, $value);
				}
			$result = " AND (".substr($result, 4).")";
			return $result;
		}
		
		function get_AdditionalElggEqualClause($id)
		{
			$elggEqual = " AND account_lid in (select username from members_users where ident in 
								(select distinct owner from members_profile_data where name='%s' and value=%s and (access='PUBLIC' or access='LOGGED_IN') ))";
			if (isset($_POST[$id]) && $_POST[$id] != "")
			{
				$value = $this->db->quote($_POST[$id]);
				
				return sprintf($elggEqual, $id, $value);
			}
		}
		
		function get_count($_type='both',$start = '',$sort = '', $order = '', $query = '', $offset = '',$query_type='', $useQueryString=true)
		{
			$sqlRule = $this->get_where_clause($_type, $sort, $order, $useQueryString);
			$sql = "SELECT count(*) FROM $this->table ".$sqlRule[where];
			$this->db->query($sql);
			$this->db->next_record();
			$total = $this->db->f(0);
			return $total;
		}
		
		function get_online_list($_type='both',$start = '',$sort = '',$order='',$query='',$offset= '',$query_type='',$offline=TRUE)
		{
			$sqlRule = $this->get_where_clause($_type, $sort, $order);
			$joiner= ($offline) ? " LEFT " : "";

			$sql = "select distinct b.account_id, b.`account_lid`, LENGTH(s.session_id) as account_pwd, 
						b.`account_firstname`, b.`account_lastname`, b.`account_lastlogin`, 
						b.`account_lastloginfrom`, b.`account_lastpwd_change`, b.`account_status`, b.`account_expires`, 
						b.`account_type`, b.`person_id`, b.`account_primary_group`, b.`account_email`, 
						b.`account_linkedin`, DATE_FORMAT(b.`account_membership_date`,'%d/%m/%y') as account_membership_date 
					FROM `phpgw_accounts` as b".$joiner."JOIN `phpgw_sessions` as s on `account_lid`=REPLACE(`session_lid`,'@default','') 
					".$sqlRule[where]." ". $sqlRule[order];
			if ($offset)
			{
				$this->db->limit_query($sql,$start,__LINE__,__FILE__,$offset);
			}
			elseif (is_numeric($start))
			{
				$this->db->limit_query($sql,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$accounts[] = Array(
					'account_id'        => $this->db->f('account_id'),
					'account_lid'       => $this->db->f('account_lid'),
					'account_pwd'       => $this->db->f('account_pwd'),
					'account_type'      => $this->db->f('account_type'),
					'account_firstname' => $this->db->f('account_firstname'),
					'account_lastname'  => $this->db->f('account_lastname'),
					'account_status'    => $this->db->f('account_status'),
					'account_expires'   => $this->db->f('account_expires'),
					'person_id'         => $this->db->f('person_id'),
					'account_primary_group' => $this->db->f('account_primary_group'),
					'account_email'     => $this->db->f('account_email'),
					'account_linkedin'     => $this->db->f('account_linkedin'),
					'account_membership_date'  => $this->db->f('account_membership_date'),
				);
			}
			/*$this->db->query("SELECT count(*) FROM $this->table $whereclause");
			$this->db->next_record();
			$this->total = $this->db->f(0);*/

			return $accounts;
		}

		function get_list($_type='both',$start = '',$sort = '', $order = '', $query = '', $offset = '',$query_type='')
		{
			if (! $sort)
			{
				$sort = "DESC";
			}

			if (!empty($order) && preg_match('/^[a-zA-Z_0-9, ]+$/',$order) && (empty($sort) || preg_match('/^(DESC|ASC|desc|asc)$/',$sort)))
			{
				$orderclause = "ORDER BY $order $sort";
			}
			else
			{
				$orderclause = "ORDER BY account_lid ASC";
			}

			switch($_type)
			{
				case 'accounts':
					$whereclause = "WHERE account_type = 'u'";
					break;
				case 'groups':
					$whereclause = "WHERE account_type = 'g'";
					break;
				default:
					$whereclause = '';
			}

			if ($query || $query_type)
			{
				if ($whereclause)
				{
					$whereclause .= ' AND ( ';
				}
				else
				{
					$whereclause = ' WHERE ( ';
				}
				switch($query_type)
				{
					case 'all':
					default:
						$query = '%'.$query;
						// fall-through
					case 'start':
						$query .= '%';
						// fall-through
					case 'exact':
						$query = $this->db->quote($query);
						$whereclause .= " account_firstname LIKE $query OR account_lastname LIKE $query OR account_lid LIKE $query )";
						break;
					case 'firstname':
					case 'lastname':
					case 'lid':
                                        case 'real_only':
                                                $whereclause .= " account_primary_group != 35 AND account_primary_group != 6 )";
                                                break;
					case 'email':
						$query = $this->db->quote('%'.$query.'%');
						$whereclause .= " account_$query_type LIKE $query )";
						break;
				}
			}

			$sql = "SELECT * FROM $this->table $whereclause $orderclause";
			if ($offset)
			{
				$this->db->limit_query($sql,$start,__LINE__,__FILE__,$offset);
			}
			elseif (is_numeric($start))
			{
				$this->db->limit_query($sql,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql,__LINE__,__FILE__);
			}
            //echo $sql;
			while ($this->db->next_record())
			{
				$accounts[] = Array(
					'account_id'        => $this->db->f('account_id'),
					'account_lid'       => $this->db->f('account_lid'),
					'account_type'      => $this->db->f('account_type'),
					'account_firstname' => $this->db->f('account_firstname'),
					'account_lastname'  => $this->db->f('account_lastname'),
					'account_status'    => $this->db->f('account_status'),
					'account_expires'   => $this->db->f('account_expires'),
					'person_id'         => $this->db->f('person_id'),
					'account_primary_group' => $this->db->f('account_primary_group'),
					'account_email'     => $this->db->f('account_email'),
					'account_linkedin'     => $this->db->f('account_linkedin'),
					'account_membership_date'     => $this->db->f('account_membership_date'),
				);
			}
			$this->db->query("SELECT count(*) FROM $this->table $whereclause");
			$this->db->next_record();
			$this->total = $this->db->f(0);

			return $accounts;
		}

		/**
		 * converts a name / unique value from the accounts-table (account_lid,account_email) to an id
		 */
		function name2id($name,$which='account_lid')
		{
			$this->db->select($this->table,'account_id',array($which=>$name),__LINE__,__FILE__);
			if($this->db->next_record())
			{
				return (int)$this->db->f('account_id');
			}
			return False;
		}

		/**
		 * converts an id to the corresponding value of the accounts-table (account_lid,account_email,account_firstname,...)
		 */
		function id2name($account_id,$which='account_lid')
		{
			$this->db->select($this->table,$this->db->name_quote($which),array('account_id'=>$account_id),__LINE__,__FILE__);
			if($this->db->next_record())
			{
				return $this->db->f(0);
			}
			return False;
		}

		function get_type($account_id)
		{
			return $this->id2name($account_id,'account_type');
		}

		function exists($account_lid)
		{
			static $by_id, $by_lid;

			$where = array();
			if(is_numeric($account_lid))
			{
				if(@isset($by_id[$account_lid]) && $by_id[$account_lid] != '')
				{
					return $by_id[$account_lid];
				}
				$where['account_id'] = $account_lid;
			}
			else
			{
				if(@isset($by_lid[$account_lid]) && $by_lid[$account_lid] != '')
				{
					return $by_lid[$account_lid];
				}
				$where['account_lid'] = $account_lid;
			}

			$this->db->select($this->table,'count(*)',$where,__LINE__,__FILE__);
			$this->db->next_record();
			$ret_val = $this->db->f(0) > 0;
			if(is_numeric($account_lid))
			{
				$by_id[$account_lid] = $ret_val;
				$by_lid[$this->id2name($account_lid)] = $ret_val;
			}
			else
			{
				$by_lid[$account_lid] = $ret_val;
				$by_id[$this->name2id($account_lid)] = $ret_val;
			}
			return $ret_val;
		}

		function create($account_info)
		{
			$account_data = array(
				'account_lid'			=> $account_info['account_lid'],
				'account_pwd'			=> $GLOBALS['phpgw']->common->encrypt_password($account_info['account_passwd'],True),
				'account_firstname'		=> $account_info['account_firstname'],
				'account_lastname'		=> $account_info['account_lastname'],
				'account_status'		=> $account_info['account_status'],
				'account_expires'		=> $account_info['account_expires'],
				'account_type'			=> $account_info['account_type'],
				'person_id'				=> $account_info['person_id'],
				'account_primary_group'	=> $account_info['account_primary_group'],
				'account_email'			=> $account_info['account_email'],
				'account_linkedin'              => $_POST['account_linkedin'],
				'account_membership_date'              => $_POST['account_membership_date']
			);
			if (isset($account_info['account_id']) && (int)$account_info['account_id'] && !$this->id2name($account_info['account_id']))
			{
				// only use account_id, if it's not already used
				$account_data['account_id'] = $account_info['account_id'];
			}
			$this->db->insert($this->table,$account_data,False,__LINE__,__FILE__);

			return $this->db->get_last_insert_id($this->table,'account_id');
		}

		function auto_add($accountname, $passwd, $default_prefs = False, $default_acls = False, $expiredate = 0, $account_status = 'A')
		{
			if ($expiredate == 0)
			{
				if(isset($GLOBALS['phpgw_info']['server']['auto_create_expire']) == True)
				{
					if($GLOBALS['phpgw_info']['server']['auto_create_expire'] == 'never')
					{
						$expires = -1;
					}
					else
					{
						$expiredate = time() + $GLOBALS['phpgw_info']['server']['auto_create_expire'];
					}
				}
			}
			else
			{
				/* expire in 30 days by default */
				$expiredate = time() + ((60 * 60) * (30 * 24));
			}

			if ($expires != -1)
			{
				$expires = mktime(2,0,0,date('n',$expiredate), (int)date('d',$expiredate), date('Y',$expiredate));
			}

			$default_group_id  = $this->name2id($GLOBALS['phpgw_info']['server']['default_group_lid']);
			if (!$default_group_id)
			{
				$default_group_id = (int) $this->name2id('Default');
			}
			$primary_group = $GLOBALS['auto_create_acct']['primary_group'] &&
				$this->get_type((int)$GLOBALS['auto_create_acct']['primary_group']) == 'g' ?
				(int) $GLOBALS['auto_create_acct']['primary_group'] : $default_group_id;

			$acct_info = array(
				'account_id'        => (int) $GLOBALS['auto_create_acct']['id'],
				'account_lid'       => $accountname,
				'account_type'      => 'u',
				'account_passwd'    => $passwd,
				'account_firstname' => $GLOBALS['auto_create_acct']['firstname'] ? $GLOBALS['auto_create_acct']['firstname'] : 'New',
				'account_lastname'  => $GLOBALS['auto_create_acct']['lastname'] ? $GLOBALS['auto_create_acct']['lastname'] : 'User',
				'account_status'    => $account_status,
				'account_expires'   => $expires,
				'account_primary_group' => $primary_group,
			);

			/* attempt to set an email address */
			if (isset($GLOBALS['auto_create_acct']['email']) == True && $GLOBALS['auto_create_acct']['email'] != '')
			{
				$acct_info['account_email'] = $GLOBALS['auto_create_acct']['email'];
			}
			elseif(isset($GLOBALS['phpgw_info']['server']['mail_suffix']) == True && $GLOBALS['phpgw_info']['server']['mail_suffix'] != '')
			{
				$acct_info['account_email'] = $accountname . '@' . $GLOBALS['phpgw_info']['server']['mail_suffix'];
			}

			$this->db->transaction_begin();

			$this->create($acct_info); /* create the account */

			$accountid = $this->name2id($accountname); /* grab the account id or an error code */

			if ($accountid) /* begin account setup */
			{
				/* If we have a primary_group, add it as "regular" eGW group (via ACL) too. */
				if ($primary_group)
				{
					$this->db->query("insert into phpgw_acl (acl_appname, acl_location, acl_account, acl_rights) values('phpgw_group', "
						. $primary_group . ', ' . $accountid . ', 1)',__LINE__,__FILE__);
				}

				/* if we have an mail address set it in the uesrs' email preference */
				if (isset($GLOBALS['auto_create_acct']['email']) && $GLOBALS['auto_create_acct']['email'] != '')
				{
					$GLOBALS['phpgw']->acl->acl($accountid);	/* needed als preferences::save_repository calls acl */
					$GLOBALS['phpgw']->preferences->preferences($accountid);
					$GLOBALS['phpgw']->preferences->read_repository();
					$GLOBALS['phpgw']->preferences->add('email','address',$GLOBALS['auto_create_acct']['email']);
					$GLOBALS['phpgw']->preferences->save_repository();
				}
				/* use the default mail domain to set the uesrs' email preference  */
				elseif(isset($GLOBALS['phpgw_info']['server']['mail_suffix']) && $GLOBALS['phpgw_info']['server']['mail_suffix'] != '') 
				{
					$GLOBALS['phpgw']->acl->acl($accountid);	/* needed als preferences::save_repository calls acl */
					$GLOBALS['phpgw']->preferences->preferences($accountid);
					$GLOBALS['phpgw']->preferences->read_repository();
					$GLOBALS['phpgw']->preferences->add('email','address', $accountname . '@' . $GLOBALS['phpgw_info']['server']['mail_suffix']);
					$GLOBALS['phpgw']->preferences->save_repository();
				}

				/* commit the new account transaction */
				$this->db->transaction_commit();

				/* does anyone know what the heck this is required for? */
				$GLOBALS['hook_values']['account_lid']	= $acct_info['account_lid'];
				$GLOBALS['hook_values']['account_id']	= $accountid;
				$GLOBALS['hook_values']['new_passwd']	= $acct_info['account_passwd'];
				$GLOBALS['hook_values']['account_status'] = $acct_info['account_status'];
				$GLOBALS['hook_values']['account_firstname'] = $acct_info['account_firstname'];
				$GLOBALS['hook_values']['account_lastname'] =  $acct_info['account_lastname'];
				$GLOBALS['phpgw']->hooks->process($GLOBALS['hook_values']+array(
					'location' => 'addaccount'
				),False,True);  /* called for every app now, not only enabled ones */

			} /* end account setup */
			else /* if no account id abort the account creation */
			{
				$this->db->transaction_abort();
			}

			/* 
			 * If we succeeded in creating the account (above), return the accountid, else, 
			 * return the error value from $this->name2id($accountname)
			 */
			return $accountid;

		} /* end auto_add() */

		function get_account_name($accountid,&$lid,&$fname,&$lname)
		{
			$this->db->select($this->table,'account_lid,account_firstname,account_lastname',array('account_id'=>$accountid),__LINE__,__FILE__);
			if (!$this->db->next_record())
			{
				return False;
			}
			$lid   = $this->db->f('account_lid');
			$fname = $this->db->f('account_firstname');
			$lname = $this->db->f('account_lastname');

			return True;
		}
	}
	/*!
	 @class_end accounts
	*/

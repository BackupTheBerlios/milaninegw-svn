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

	/* $Id: class.module_lang_block.inc.php,v 1.6.2.1 2004/08/22 11:33:08 ralfbecker Exp $ */
	require_once("classes/cTemplate.php");
	require_once("classes/cTFiller.php");
	require_once("classes/validators.php");
	require_once("classes/cSqlCommand.php");
	require_once(PHPGW_API_INC.'/class.send.inc.php');
	
	define('DB_TYPE', 'mysql');
	
	class module_joinus_form extends Module
	{
		var $mysql_link;
		var $words;
		function module_joinus_form()
		{
			
			$this->arguments = array(
				'recepient' => array('type' => 'textfield','label' => lang('Where to send request')),
                                'invitations_table' => array('type'=>'textfield','label'=>lang('invitations table') ),
                                'members_db_name' => array('type'=>'textfield','label'=>lang('members db name') ),
                                'how_did_u' => array('type'=>'textfield','label'=>lang('how_did_u') ),
				'prof_profile' => array('type'=>'textfield','label'=>lang('Which professional profile better describes you') ),
				'ac_degree' =>array('type'=>'textfield','label'=>lang('academic degree') )
			);
			$this->properties = array();
			$this->title = lang('Join US! Richiedi l\'iscrizione al Club!');
			$this->description = lang('This module lets users to submit registration form');
		}
	
		function get_content(&$arguments,$properties)
		{
			$this->DbConnect();
			$this->GetWords();
			$this->FormConfig($arguments);
			
			extract ($_POST, EXTR_PREFIX_ALL, 'p');
			extract ($_GET, EXTR_PREFIX_ALL, 'g');
			
			$template = new cTFiller(PHPGW_SERVER_ROOT);
			$template->set_filenames( array('form' => 'sitemgr/templates/joinus/form.html') );
			$template->CollectPostedData($this->formCfg, false, false);
			
			if ( count($_POST) > 0 )
			{
				$template->CollectPostedData($this->formCfg, true, false);
				$template->ValidatePostedData($this->formCfg, true);
				if( !CheckDateValue($template->defaults['birth_d'], $template->defaults['birth_m'], $template->defaults['birth_y']) )
					{ $template->errorsBlocks["birth_d_ErrRule"] = $this->words['birthInvalid']; }
				
				//add account validation!!!!!!!!!!!
				if($template->HasValidationErrors())
					$template->assign_block_vars("FORM_ERROR", array("Message"=> $this->words['commonError']) );
				else
				{
					$template->defaults["account_lid"] = strtolower($template->defaults["name"].".".$template->defaults["surname"]);
					$template->defaults["account_pwd"] = md5 (strtolower ($template->defaults["name"]) );
					$template->defaults["email"] = strtolower($template->defaults["email"]);

					$sqlCommand = new cSqlCommand();
					$sqlCommand->SetValuesViaConfig($this->formCfg, $template->defaults);
					$sqlCommand->AddColumnValue("account_type", "u");
					$sqlCommand->AddColumnValue("account_primary_group", 8);
					$sqlCommand->AddColumnValue("account_expires", -1);
					$sqlCommand->AddColumnValue("person_id", 0);
					$sqlCommand->AddColumnValue("account_status", '');
					$sqlCommand->AddColumnValue("account_membership_date", 'CURDATE()');
					
					
					/*$sql =  $sqlCommand->PrepareInsertSQL("phpgw_accounts");
					$res = mysql_query ($sql, $this->mysql_link);
					$user_id =  mysql_insert_id($this->mysql_link);
					
					//set privileges
					$sql = "INSERT into phpgw_acl (`acl_appname`,`acl_location`,`acl_account`,`acl_rights`) VALUES ('phpgw_group',8,".$user_id.",1)";
					$res = mysql_query ($sql, $this->mysql_link);
					$sql = "INSERT into phpgw_acl (`acl_appname`,`acl_location`,`acl_account`,`acl_rights`) VALUES ('phpgw_group',18,".$user_id.",1)";
					$res = mysql_query ($sql, $this->mysql_link);
					$sql = "INSERT into phpgw_acl (`acl_appname`,`acl_location`,`acl_account`,`acl_rights`) VALUES ('preferences','changepassword',".$user_id.",1)";
					$res = mysql_query ($sql, $this->mysql_link);
					$users_opt = 2|4|16|32|64|128|256|512|2048|4096|8192|16384|131072|4194304;
					$sql = "INSERT INTO phpgw_fud_users (last_visit, join_date, theme, alias, login, email, passwd, name, users_opt, egw_id)
						   ".time().", ".time().", 1, '".$template->defaults["account_lid"]."', '".$template->defaults["account_lid"]."', 
						   '".$template->defaults["email"]."', '".$template->defaults["account_pwd"]."', '".$template->defaults["name"]." ".$template->defaults["surname"]."', $users_opt, $user_id)";
					$res = mysql_query ($sql, $this->mysql_link);*/
					
					DebugLog($arguments['recepient']);
					
				/*		
				$log ="";
				$content = "";
				if (isset($p_ic)){
                                          mysql_select_db ($arguments['members_db_name']) or die("cannot select db:".mysql_error($mysql_link));
                                          $invite_query="SELECT i.*,concat(a.account_firstname,' ',a.account_lastname) as inviter,a.account_lid ".
                                          "FROM ".$arguments['members_db_name'].".`".$arguments['invitations_table'].'`i '.
                                          'join '.$GLOBALS['phpgw_domain']['default']['db_name'].'.phpgw_accounts a on i.owner=a.account_id '.
                                          'WHERE (i.code =\''.$g_ic.'\')';
                                          $invite_result=mysql_query ($invite_query, $mysql_link) 
                                            or die ($invite_query."<br/>".mysql_error($mysql_link));
                                          $invitation=mysql_fetch_array($invite_result, MYSQL_BOTH);
                                          mysql_free_result($invite_result);
                                          
                                          if (isset($invitation['ident']))
										  {
                                            $p_email=$invitation['email'];
                                            $remove_invitation_query='DELETE FROM '.$arguments['members_db_name'].".".
                                                                      $arguments['invitations_table'].
                                                                     ' WHERE ident='.$invitation['ident'];
                                          } 
										  else 
										  {
                                            $log.=lang("invitation not found");
                                          }
                                }
				if (isset($p_btn_submit))
				{
					if (empty ($p_name) || empty ($p_surname) ||  empty ($p_email) || empty ($p_msg))
						$log .= lang('you must fill in all of the required fields')."<br/>";
					
					if (!preg_match ("/.+@.+\.[a-z]+/", strtolower($p_email)))
						$log .= lang('you have entered an invalid email address').": [".$p_email."] <br/>";
					
					if (strlen ($p_name)<2 || strlen ($p_surname)<2)
						$log .= lang('too short name')."<br/>";
					if (strlen ($p_prof_profile) <5){
						$log .= lang('Proffessional profile is required')."<br/>";
					}
					if ($p_sex<1){
						$log.=lang('choose your sex')."<br/>";
					}
				}

				if (isset($p_btn_submit) && empty ($log))
				{
 					if ($remove_invitation_query != ""){
                                          $result = mysql_query ($remove_invitation_query, $mysql_link) 
                                          or die ($remove_invitation_query."<br/>".mysql_error($mysql_link));
                                        }
					//Start mail:
					
					$date = date("d.m.Y H:i");
					$link = "http://". $_SERVER['SERVER_NAME']."/egroupware/index.php?menuaction=admin.uiaccounts.edit_user&account_id=$user_id";
					$msg = "";
					
					
					
					$mailer = new send();
					$mailer->Subject = "New membership application";  // change it 
					$mailer->Body = $msg;
					
					$mailer->From = "messenger@milanin.com";  // change it
					$mailer->FromName = "Milan IN website";  // change it
					 
					//$mailer->AddAddress("piercarlo.pozzati@milanin.com"); // change it 
					$mailer->AddAddress($arguments['recepient']);
					
					if(!$mailer->Send())
					{
						$content .= 'There was a problem sending this mail!';
						$content .= $mailer->ErrorInfo;
					}

					$mailer->ClearAddresses();

					$msg = "Gentile Collega,\n
abbiamo ricevuto la tua richiesta di iscrizione al Business Club Milan IN.\n
Nel giro di qualche giorno riceverai da Pier Carlo Pozzati (presidente del Club) la richiesta di collegamento LinkedIn: ti preghiamo di accettarla, dal momento che questa una condizione essenziale per completare la tua iscrizione.\n
Una volta accettata questa richiesta ti verr inviata una welcome letter contenete le istruzioni per il sito, username e password per accedere.\n
Nel caso invece questa richiesta fosse stata inviata per errore ti preghiamo di segnalarlo a silvia.lenich@milanin.com.\n
Grazie per il tuo interesse per il nostro Club e a presto!\n
Silvia Lenich\nSegreteria Business Club Milan IN\n";
					
					$mailer = new send();
					$mailer->Subject = "Richiesta Iscrizione a Milan IN";  // change it 
					$mailer->Body = $msg;
					
					$mailer->From = "iscrizioni@milanin.com";  // change it
					$mailer->FromName = "Segreteria Business Club Milan IN";  // change it
					 
					//$mailer->AddAddress("piercarlo.pozzati@milanin.com"); // change it 
					$mailer->AddAddress($p_email);
					
					if(!$mailer->Send())
{
						$content .= 'There was a problem sending mail to '.$p_email.'!';
						$content .= $mailer->ErrorInfo;
}

					$mailer->ClearAddresses();
					
					$content .= lang("joinus success");
                                        //<h3>Your application for the membership has been sent, the administration is notified, and you will be contacted shortly. <br/>We thank you for your interest</h3>";
					unset ($_POST);
						
				}
				if  (!isset($p_btn_submit) || !empty ($log))
				{
				  $prof_profile=explode(",",$arguments['prof_profile']);
		          $content .= '';
				  $content .= "<p class='error'>$log </p>";
                                        if (isset($g_ic)){
                                          $mysql_link = mysql_connect($GLOBALS['phpgw_domain']['default']['db_host'],
                                          $GLOBALS['phpgw_domain']['default']['db_user'],
                                          $GLOBALS['phpgw_domain']['default']['db_pass']) 
                                          or die( "Unable to connect to SQL server");
					mysql_select_db ($arguments['members_db_name']) or die(mysql_error());
					$invite_query="SELECT i.*,concat(a.account_firstname,' ',a.account_lastname) as inviter,a.account_lid ".
                                        "FROM `".$arguments['invitations_table'].'`i '.
                                        'join '.$GLOBALS['phpgw_domain']['default']['db_name'].'.phpgw_accounts a on i.owner=a.account_id '.
                                        'WHERE (i.code =\''.$g_ic.'\')';
                                        $invite_result=mysql_query ($invite_query, $mysql_link) 
                                          or die ($invite_query."<br/>".mysql_error($mysql_link));
                                        $invitation=mysql_fetch_array($invite_result, MYSQL_BOTH);
                                        mysql_free_result($invite_result);
                                        
                                        if (isset($invitation['ident'])){
                                          
                                          $content .= '<p><h3>'.lang('invitation found').': '.lang('from').
                                            ' <a href="/members/'.$invitation['account_lid'].'">'.
                                            $invitation['inviter'].'</a> '.
                                            lang('to').' <b>'.$invitation['name'].'</b></h3></p>';
                                          
                                          $content .= '<input name="ic" value="'.$invitation['code'].'" type="hidden" />';
					}else{
                                            $content.='<h3><font color="red">'.lang('invitation not found').'</font></h3>';
                                          }
                                        }  
                                        }
				return $content;*/
				}
					
					
				DebugLog( $template->errorsBlocks);
			}
			
			$template->FillBlockWithStaticValues($this->formCfg, "FORM", $this->words, $this->mysql_link);
			return $template->pparse('form');
		}
		
		function DbConnect()
		{
			$vars = $GLOBALS['phpgw_domain']['default'];
			$this->mysql_link = mysql_connect($vars['db_host'].":".$vars['db_port'], $vars['db_user'], $vars['db_pass']) or die( "Unable to connect to SQL server");
			mysql_select_db($vars['db_name']) or die(mysql_error());
		}
		
		function GetMySQLArray($sql)
		{
			$res = mysql_query ($sql, $this->mysql_link) or die ($sql."<br/>".mysql_error($this->mysql_link));
			$rs = mysql_fetch_array($res);
			mysql_free_result($res);
			$result = explode("\n", $rs[0]);
			$result = array_map("trim", $result);
			return $result;
		}
		
		function FormConfig($arguments)
		{
			$countries = $this->GetMySQLArray("SELECT data from other_data where name='countries_list'");
			$sports = $this->GetMySQLArray("SELECT data from other_data where name='sports' and lang='".$GLOBALS['page']->lang."'");
			$hobbies = $this->GetMySQLArray("SELECT data from other_data where name='hobbies' and lang='".$GLOBALS['page']->lang."'");
            $industries = $this->GetMySQLArray("SELECT data from other_data where name='industries' and lang='".$GLOBALS['page']->lang."'");
            $professions = $this->GetMySQLArray("SELECT data from other_data where name='professions' and lang='".$GLOBALS['page']->lang."'");
            $occ_areas = $this->GetMySQLArray("SELECT data from other_data where name='occ_areas' and lang='".$GLOBALS['page']->lang."'");
			$prof_profile = $this->GetMySQLArray("SELECT data from other_data where name='prof_profile' and lang='".$GLOBALS['page']->lang."'");
			$ac_degree = $this->GetMySQLArray("SELECT data from other_data where name='ac_degree' and lang='".$GLOBALS['page']->lang."'");
			
			$this->formCfg = array	(
									"fields" =>array(
													"account_lid" => 
																array("control_id" => "account_lid",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "DbField" => "account_lid"
																	  ),
													"account_pwd" => 
																array("control_id" => "account_pwd",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "DbField" => "account_pwd"
																	  ),
													"name" =>
																array("control_id" => "name",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => true,
																	  "required_message" => $this->words['thisRequired'],
																	  "DbField" => "account_firstname"
																	  ),
													 "surname" =>
																array("control_id" => "surname",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => true,
																	  "required_message" => $this->words['thisRequired'],
																	  "DbField" => "account_lastname"
																	  ),
													 "url" =>
																array("control_id" => "url",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "required_message" => ""
																	  ),
													"phone" =>
																array("control_id" => "phone",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "required_message" => ""
																	  ),
													"email" =>
																array("control_id" => "email",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => true,
																	  "required_message" => $this->words['thisRequired'],
																	  "validatorFun" => "IsValidEmail",
																	  "validator_message" => $this->words['inputValidEmail'],
																	  "DbField" => "account_email"
																	  ),
								  					"msg" =>
																array("control_id" => "msg",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => true,
																	  "required_message" => $this->words['thisRequired']
																	  ),
													"birth_d" =>
																array("control_id" => "birth_d",
																	  "default_value"=>$this->words['dd'],
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "validator_message" => $this->words['birthInvalid']
																	  ),
													"birth_m" =>
																array("control_id" => "birth_m",
																	  "default_value"=>$this->words['mm'],
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "required_message" => ""
																	  ),
													"birth_y" =>
																array("control_id" => "birth_y",
																	  "default_value"=>$this->words['yyyy'],
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "required_message" => ""
																	  ),		  
													"residence_city" =>
																array("control_id" => "residence_city",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "required_message" => ""
																	  ),
													"terms_privacy" =>
																array("control_id" => "terms_privacy", 
																	  "default_value"=>0, 
																	  "required" => true, 
																	  "required_message" => $this->words['thisRequired'],
																	  "value_on"=>1,
																	  "control_type" => "CHK"),
													"terms_services" =>
																array("control_id" => "terms_services", 
																	  "default_value"=>0, 
																	  "required" => true, 
																	  "required_message" => $this->words['thisRequired'],
																	  "value_on"=>1,
																	  "control_type" => "CHK")
																	  
													),
													
									"lists"	 => array(
														"prof_profile" => array(
																"control_id" => "prof_profile",
																"control_type" => "DDL",
																"use_key" => false,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $prof_profile,
																"checked_value" => 'selected="selected"',
																"use_html_replace" => true
																),
														"how_did_u" => array(
																"control_id" => "how_did_u",
																"control_type" => "DDL",
																"use_key" => false,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> explode(",", $arguments['how_did_u']),
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false
																),
														"sex" => array(
																"control_id" => "sex",
																"control_type" => "DDL",
																"use_key" => true,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> array("1"=>$this->words['female'], "2"=>$this->words['male']),
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false
																),
														"languages" => array(
																"control_id" => "languages",
																"control_type" => "DDL",
																"use_key" => true,
																"required" => true,
																"required_message" => "",
																"source" 		=> "select * FROM phpgw_languages where lang_id in ("."'".str_replace(",", "','", $GLOBALS['sitemgr_info']['site_languages'])."'".") ORDER BY lang_name",
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false,
																"default_value" => "it"
																),
														"residence_country" => array(
																"control_id" => "residence_country",
																"control_type" => "DDL",
																"use_key" => false,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $countries,
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false
																),
														"ac_degree" => array(
																"control_id" => "ac_degree",
																"control_type" => "DDL",
																"use_key" => false,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $ac_degree,
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false
																),
														"favorite_sport" => array(
																"control_id" => "favorite_sport",
																"control_type" => "DDL",
																"use_key" => false,
																"required" => false,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $sports,
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false
																),
																
														"interests" => array(
																"control_id" => "interests",
																"control_type" => "MDDL",
																"use_key" => false,
																"required" => false,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $hobbies,
																"checked_value" => 'checked',
																"use_html_replace" => false,
																"colCount" => 2
																),
														"industries" => array(
																"control_id" => "industries",
																"control_type" => "MDDL",
																"use_key" => false,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $industries,
																"checked_value" => 'checked',
																"use_html_replace" => false,
																"colCount" => 2
																),
																
														"professions" => array(
																"control_id" => "professions",
																"control_type" => "MDDL",
																"use_key" => false,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $professions,
																"checked_value" => 'checked',
																"use_html_replace" => false,
																"colCount" => 2
																),
																
														"occ_areas" => array(
																"control_id" => "occ_areas",
																"control_type" => "MDDL",
																"use_key" => false,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $occ_areas,
																"checked_value" => 'checked',
																"use_html_replace" => false,
																"colCount" => 2
																),		
																
													  )
									);
		}
		//
		function GetWords()
		{
			$words = array();
			$words['requiredfields'] = lang('required fields');
			$words['Name'] = lang('Name');
			$words['LastName'] = lang('last name');
			$words['ProfessionalProfile'] = lang("Which professional profile better describes you");
			$words['send'] = lang('send');
			$words['phonenumber'] = lang('phone number');
			$words['email'] = lang('email');
			$words['howknow'] = lang("How did you know about the club");
			$words['personaldata'] = lang("personal data");
			$words['sex'] = lang('sex');
			$words['birthdate'] = lang('birth date');
			$words['countryresidence'] = lang("country of residence");
			$words['cityresidence'] = lang('city of residence');
			$words['academicdegree'] = lang("your academic degree");
			$words['favoritesport'] = lang("favorite sport");
			$words['interests'] = lang("interests");
			$words['professionaldata'] = lang("professional data");
			$words['industry'] = lang("industry");
			$words['profession'] = lang("profession");
			$words['occ_area'] = lang("occ_area");
			$words['termsacceptance'] = lang("terms acceptance");
			$words['privacyterms'] = lang('privacy terms');
			$words['accept'] = lang('accept');
			$words['servicesterms'] = lang('services terms');
			$words['female'] = lang("female");
			$words['male'] = lang("male");
			$words['dd'] = lang('dd');
			$words['mm'] = lang('mm');
			$words['yyyy'] = lang('yyyy');
			$words['MainLanguage'] = lang('MainLanguage');
			$words['requestingReason'] = lang('Reason for requesting Club Membership');
			$words['LinkedinProfile'] = lang('URL to Linkedin Profile');
			
			$words['thisRequired'] = lang("thisRequired");
			$words['commonError'] = lang("commonError");
			$words['inputValidEmail'] = lang("inputValidEmail");
			$words['birthInvalid'] = lang("birthInvalid");
			
			$this->words = $words;
		}
	}
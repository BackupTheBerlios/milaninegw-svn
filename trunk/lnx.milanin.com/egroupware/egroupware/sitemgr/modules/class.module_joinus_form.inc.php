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
	
		function IsDebug()
		{
			return false;
		}
		
		function onInitContent(&$arguments, $properties)
		{
			$this->DbConnect();
			$this->GetWords();
			$this->FormConfig($arguments);
			
			$template = new cTFiller(PHPGW_SERVER_ROOT);
			$template->set_filenames( array('form' => 'sitemgr/templates/joinus/form.html') );
			$template->CollectPostedData($this->formCfg, false, false);
			if($_GET["isGolub"] == 1)
				$template->defaults = unserialize(stripslashes($_GET["query"]));
			$this->words["moneydonateDisplay"] = ($template->defaults["isdonate"] == "1") ? "block" : "none";
			return $template;
		}
		
		function getNewUniqueId($template)
		{
			$sqlCommand = new cSqlCommand();
			$sqlCommand->SetValuesViaConfig($this->formCfg, $template->defaults);
			$sqlCommand->AddColumnValue("account_type", "u");
			$sqlCommand->AddColumnValue("account_primary_group", 8);
			$sqlCommand->AddColumnValue("account_expires", -1);
			$sqlCommand->AddColumnValue("person_id", 0);
			$sqlCommand->AddColumnValue("account_status", '');
			$sqlCommand->AddColumnValue("account_membership_date", date("Y-m-d"));
			$sqlCommand->AddColumnValue("account_linkedin", ValidateLinkedin($template->defaults["linkedin"], true));
			if($this->IsUserWantToDonate($template))
				$sqlCommand->AddColumnValue("paypal_amount", $template->defaults["moneydonate"]);
				
			$sql =  $sqlCommand->PrepareInsertSQL("phpgw_accounts");
			if(!$this->IsDebug())
			{
				$res = mysql_query ($sql, $this->mysql_link);
				$userID =  mysql_insert_id($this->mysql_link);
			}
			else
			{
				DebugLog($sql);
				return 666;
			}
			
			return $userID;
		}
		
		function setPrivilegesToNewUser($user_id, $template)
		{
			//set privileges
			if(!$this->IsDebug())
			{
				$sql = "INSERT into privacy_confirmations (`owner`,`date`,`state`) VALUES (".$user_id.", ".time().", 1)";
				$res = mysql_query ($sql, $this->mysql_link);
				
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
				$res = mysql_query ($sql, $this->mysql_link);
			}
		}
		
		function getNewElggUniqueId($userID, $template)
		{
			/*begin: block adds a member to eLgg*/
			$cmd = new cSqlCommand();
			$cmd->AddColumnValue("username", $template->defaults["account_lid"]);
			$cmd->AddColumnValue("password", $template->defaults["account_pwd"]);
			$cmd->AddColumnValue("email", $template->defaults["email"]);
			
			$cmd->AddColumnValue("name", ucwords(strtolower($template->defaults["name"]." ".$template->defaults["surname"])) );
			$sql = $cmd->PrepareInsertSQL("members_users");
			if(!$this->IsDebug())
			{
				$res = mysql_query ($sql, $this->mysql_link);
				$elggUserID =  mysql_insert_id($this->mysql_link);
			}
			else
			{
				DebugLog($sql);
				return 666;
			}
			return $elggUserID;
			/*end: block adds a member to eLgg*/
		}
		
		function DoELggInsert($elggUserID, $accessMask, $columnName, $columnValue)
		{
			if(trim($columnValue) == "") return;
			$cmd = new cSqlCommand();
			$cmd->AddColumnValue("owner", $elggUserID);
			$cmd->AddColumnValue("access", $accessMask); //
			if($columnName == 'linkedin')
				$columnValue = ValidateLinkedin($columnValue, true);
			$cmd->AddColumnValue("name", $columnName);
			$cmd->AddColumnValue("value", $columnValue);
			$sql = $cmd->PrepareInsertSQL("members_profile_data");
			if(!$this->IsDebug())
			{
				$res = mysql_query ($sql, $this->mysql_link);
			}
			else
			{
				DebugLog($sql);
				return 666;
			}
		}
		
		function appendElggProfileData($elggUserID, $template)
		{
			$cfg = $this->formCfg;
			//insert eLgg fields values.
			while ( is_array($cfg["fields"]) && list($field, $ctrlCfg) = each($cfg["fields"]))
			{
				if($ctrlCfg["eLggExternal"] === true)
				{
					$this->DoELggInsert($elggUserID, $ctrlCfg["eLggPublic"] === true ? 'PUBLIC' : 'LOGGED_IN', $ctrlCfg["control_id"], $template->defaults[$ctrlCfg["control_id"]]);
				}
			}
			
			if(is_array($cfg["lists"])) reset($cfg["lists"]);
			while ( is_array($cfg["lists"]) && list($cfgKey, $ctrlCfg) = each($cfg["lists"]))
			{
				if($ctrlCfg["eLggExternal"] === true)
				{
					$arr = array();
					if(is_array($ctrlCfg["source"]))
						$arr = $ctrlCfg["source"];
					elseif(function_exists ($ctrlCfg["source"]))
						$arr = call_user_func($ctrlCfg["source"]);
					elseif( strtolower(substr($ctrlCfg["source"], 0, 7)) == 'select ')
					{
						if($this->mysql_link != null)
						{
							if($res = mysql_query ($ctrlCfg["source"], $this->mysql_link))
							while($rs = mysql_fetch_row($res))
								$arr[ $rs[0] ] = $rs[1];
						}
						else
						{
							if($res = $db->sql_query($ctrlCfg["source"]))
							while($rs = $db->sql_fetchrow($res))
								$arr[ $rs[0] ] = $rs[1];
						}
					}//end get array bind values.
					$value = $template->defaults[$ctrlCfg["control_id"]];
					if(is_array($value))
						$value = implode(",", $value);
					//DebugLog($value);
					$this->DoELggInsert($elggUserID, $ctrlCfg["eLggPublic"] === true ? 'PUBLIC' : 'LOGGED_IN', $ctrlCfg["control_id"], $value);
				}
			}//end list while
		}
		
		function ModifyStringForUID($value, &$template, $blockError)
		{
			$value = strtolower($value);
			$value = preg_replace("/\s+/", "", $value);
			
			$chrs = 	array( "/".chr(236)."/", "/".chr(232)."/", "/".chr(249)."/", "/".chr(237)."/", "/".chr(243)."/",
							   "/".chr(242)."/", "/".chr(224)."/", "/".chr(233)."/", "/".chr(231)."/", "/".chr(225)."/", "/".chr(250)."/");
							   
			$rChrs = 	array( "i", "e", "u", "i", "o",
							   "o", "a", "e", "c", "a", "u" );
			
			$value = preg_replace($chrs, $rChrs, $value);

			$tmp = preg_replace("/[^A-Za-z]/", "", $value);
			if($tmp != $value)
			{
				$template->errorsBlocks[$blockError."_ErrRule"] = $this->words['invalidLetters'];
				
			}
			$value = $tmp;
			return $value;
		}
		
		function setUserUID(&$template)
		{
			$result = "";
			$name = $this->ModifyStringForUID($template->defaults["name"], &$template, "name");
			$surname = $this->ModifyStringForUID($template->defaults["surname"], &$template, "surname");
			$result = $name.".".$surname;

			return $result;
		}
		
		function getRandomPwd($count=8)
		{
		 mt_srand((double)microtime()*1000000);
		 $key = "";
		 for ($i=0; $i<$count; $i++)
		 {
   			$c = mt_rand(0,2);
		    if ($c==0)
		    {
		      $key .= chr(mt_rand(65,90));
		    }
		    elseif ($c==1)
		    {
		     $key .= chr(mt_rand(97,122));
		    }
		    else
		    {
		      $key .= mt_rand(0,9);
		    }
		 }
		 return $key;
		}
		
		function OnPostData(&$template)
		{
			$template->defaults["name"] = ucwords(trim($template->defaults["name"]));
			$template->defaults["surname"] = ucwords(trim($template->defaults["surname"]));
			 
			$template->defaults["pwd"] = $this->getRandomPwd();
			$template->defaults["account_pwd"] = md5 ( $template->defaults["pwd"] );
			$template->defaults["email"] = strtolower($template->defaults["email"]);
			
			//calculate linkedin_url_view
			if(str_is_int($template->defaults["linkedin"]))
				$template->defaults["linkedin_url_view"] = "https://www.linkedin.com/profile?viewProfile=&key=".$template->defaults["linkedin"];
			else 
			{
				$value = strtolower($template->defaults["linkedin"]);
				if( strpos($value, "http://") === 0 || strpos($value, "https://") === 0 )
				{
					$template->defaults["linkedin_url_view"] = $template->defaults["linkedin"];
				}
				else $template->defaults["linkedin_url_view"] = "#linkedin_url";
			}
		}
		
		function _file_put_contents($filename, $data, $file_append = false)
		{
        	$fp = fopen($filename, (!$file_append ? 'w+' : 'a+'));
            if(!$fp) 
			{
                //DebugLog("can't write");
				return;
            }
            fputs($fp, $data);
            fclose($fp);
		}

		function WriteDebugInformation($template)
		{
			if($GLOBALS['phpgw']->session->appsession('debugKeyName') == "")
				$GLOBALS['phpgw']->session->appsession('debugKeyName','',time());
			$tmp = $GLOBALS["GLOBALS"]["phpgw_info"]["server"]["temp_dir"];
			
			$fileName = $tmp."/".$GLOBALS['phpgw']->session->appsession('debugKeyName');
			$text = $GLOBALS["site_url"]."Joinus?isGolub=1&query=".str_replace("&", ".", serialize($template->defaults));
			$this->_file_put_contents($fileName, $text);
		}
		
		function ClearData()
		{
			if($GLOBALS['phpgw']->session->appsession('debugKeyName') != "")
			{
				$tmp = $GLOBALS["GLOBALS"]["phpgw_info"]["server"]["temp_dir"];
				$fileName = $tmp."/".$GLOBALS['phpgw']->session->appsession('debugKeyName');
				unlink( $fileName );
				$GLOBALS['phpgw']->session->appsession('debugKeyName', '', '');
			}
		}
		
		function ModuleLogFile($postfix="")
		{
			$tmp = $GLOBALS["GLOBALS"]["phpgw_info"]["server"]["temp_dir"];
			$fileName = $tmp."/sub/module_joinus$postfix.log";
			return $fileName;
		}
		
		function ValidPaypalTransaction()
		{
			$posted = $_GET;
			$postdata="";
			foreach ($posted as $key=>$value) $postdata.=$key."=".urlencode($value)."&";
			$postdata .= "cmd=_notify-validate";
			
			if (function_exists('curl_init'))
			{
				$curl = curl_init("https://www.paypal.com/cgi-bin/webscr"); 
				curl_setopt ($curl, CURLOPT_HEADER, 0);  
				curl_setopt ($curl, CURLOPT_POST, 1); 
				curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata); 
	  			curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);  
	  			curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1); 
	  			curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 1); 
	  			$response = curl_exec ($curl); 
	  			curl_close ($curl); 
			}
  			return ($response == "VERIFIED");
		}
		
		function isDonateRequest()
		{
			return isset($_GET["payer_id"]) && isset($_GET["verify_sign"]) && isset($_GET["receiver_email"]) && $_GET["receiver_email"] == "mbrembil@gmail.com";
			//return true;
		}
		
		function get_content(&$arguments, $properties)
		{
			$template = $this->onInitContent(&$arguments, $properties);
			if(!$this->isDonateRequest())
			{
			
					if ( count($_POST) > 0)
					{
						if( $GLOBALS['phpgw']->session->appsession('isWelcome') != 1 )
						{
							$template->CollectPostedData($this->formCfg, true, false);
							$this->words["moneydonateDisplay"] = ($template->defaults["isdonate"] == "1") ? "block" : "none";
							$this->OnPostData(&$template);
							//begin: Validation block
							$template->ValidatePostedData($this->formCfg, true);
							
							$template->defaults["account_lid"] = $this->setUserUID(&$template);
							
							if( ( $template->defaults['birth_y'] != "" && !str_is_int( $template->defaults['birth_y'] ) )
								 || ($template->defaults['birth_y'] != "" && str_is_int( $template->defaults['birth_y'] ) && intval($template->defaults['birth_y']) > intval(date("Y")) - 16) )
								{ $template->errorsBlocks["birth_y_ErrRule"] = $this->words['birthInvalid']; }
							else
							{
								//$template->defaults['birthDate'] = sprintf("%d-%d-%d", $template->defaults['birth_d'], $template->defaults['birth_m'], $template->defaults['birth_y']);
								$template->defaults['birthDate'] = $template->defaults['birth_y'];
							}
							if($template->HasValidationErrors())
							{
								$template->assign_block_vars("FORM_ERROR", array("Message"=> $this->words['commonError']) );
								$this->WriteDebugInformation($template);
							}
							else
							{
								$userID = $this->getNewUniqueId($template);
								if($userID == 0)
								{
									$template->assign_block_vars("UNIQUE_ERROR", array("Message"=> $this->words['uniqueError']) );
									$this->WriteDebugInformation($template);
								}
								else
								{
									$GLOBALS['phpgw']->session->appsession('joinusUserid', '', $userID);
									$template->defaults['account_user_id'] = $userID;
									$this->setPrivilegesToNewUser($userID, $template);
									$elggUserID = $this->getNewElggUniqueId($userID, $template);
									$this->appendElggProfileData($elggUserID, $template);
									$this->SendRegistrationEmail($arguments, $template);
									$GLOBALS['phpgw']->session->appsession('isWelcome', '', '');
									$GLOBALS['phpgw']->session->appsession('isWelcome', '', 1);
								}
							}
							//end:   Validation block
						}
					}
					else
					{
						$GLOBALS['phpgw']->session->appsession('isWelcome', '', '');
						$GLOBALS['phpgw']->session->appsession('isWelcome', '', 0);
					}
		
					if( $GLOBALS['phpgw']->session->appsession('isWelcome') == 0)
					{
						$template->FillBlockWithStaticValues($this->formCfg, "FORM", $this->words, $this->mysql_link);
					}
					else
					{
						$this->ClearData();
						$template->assign_block_vars("REGISTER_COMPLETE", array("JoinUsSuccess"=> lang("joinus success")) );
						if($this->IsUserWantToDonate($template))
						{
							$template->assign_block_vars("DONATE", array_merge(array("amount"=>$template->defaults["moneydonate"], 
																		"FLANG"=>strtoupper($GLOBALS['page']->lang),
																		"TitleText"=>$this->words["DonateTitleText"],
																		"USERID"=>$GLOBALS['phpgw']->session->appsession('joinusUserid')
																		), $_SERVER)
														);
							$template->assign_block_vars("DONATE.".strtoupper($GLOBALS['page']->lang), array()); 
						}
					}
			}
			else // this is a donate request from paypal. we should verify it.
			{
				if($this->ValidPaypalTransaction() && str_is_int($_GET["item_number"]))
				{
					$template->assign_block_vars("DONATE_VALID", array("text"=>$this->words["validDonate"]) );
					$sql = "update phpgw_accounts set paypal_accepted = 1 where paypal_accepted = 0 and account_id = ".$_GET["item_number"];
					$res = mysql_query ($sql, $this->mysql_link);
				}
				else
				{
					$template->assign_block_vars("DONATE_INVALID", array("text"=>$this->words["invalidDonate"]) ); 
				}
			}
			return $template->pparse('form');
		}
		
		function IsUserWantToDonate($template)
		{
			return $template->defaults["isdonate"] == "1" && $template->defaults["moneydonate"] != "";
		}
		
		function SendRegistrationEmail($arguments, $template)
		{
			$tEmail = new cTFiller(PHPGW_SERVER_ROOT);
			$tEmail->set_filenames( array('admin' => 'sitemgr/templates/joinus/email-to-admin.html', 'user' => 'sitemgr/templates/joinus/email-to-user.html') );

			$tEmail->assign_vars($template->defaults);
			$tEmail->assign_var("CURRENT_DATE", date("Y-m-d"));
			$tEmail->assign_var("prof_profile_text", $this->formCfg["lists"]["prof_profile"]["source"][$template->defaults["prof_profile"]]);
			$tEmail->assign_var("how_did_u_text", $this->formCfg["lists"]["how_did_u"]["source"][$template->defaults["how_did_u"]]);
			
			$tEmail->assign_var("industries_text", $this->formCfg["lists"]["industries"]["source"][$template->defaults["industries"]]);
			$tEmail->assign_var("occ_areas_text", $this->formCfg["lists"]["occ_areas"]["source"][$template->defaults["occ_areas"]]);

			//send email to ADMIN user.				
			$mailer = new send();
			$mailer->Subject = "New membership application";  // change it
			$mailer->Body = $tEmail->pparse('admin');
			$mailer->From = "messenger@milanin.com";  // change it
			$mailer->FromName = "Milan IN website";  // change it
			if($this->IsDebug())
			{
				$mailer->AddAddress("borisan@mail.ru");
				$mailer->AddAddress("andrey@milanin.com");
			}
			else
			{
				$mailer->AddAddress($arguments['recepient']);
				$mailer->AddBCC("andrey@milanin.com");
			}
			
			$mailer->AddReplyTo("no-reply@milanin.com", "no-reply@milanin.com");

			$mailer->Send();
			
			$mailer = new send();
			//send email to registered user
			$mailer->Subject = "Richiesta Iscrizione a Milan IN";  // change it 
			$mailer->Body = $tEmail->pparse('user');
			$mailer->From = "iscrizioni@milanin.com";  // change it
			$mailer->FromName = "Segreteria Business Club Milan IN";  // change it
			
			if($this->IsDebug())
			{
				$mailer->AddAddress("borisan@mail.ru");
				$mailer->AddAddress("andrey@milanin.com");
			}
			else
			{
				$mailer->AddAddress($template->defaults["emailaddress"]);
			}
			$mailer->AddReplyTo("no-reply@milanin.com", "no-reply@milanin.com");
			$mailer->Send();
			$mailer->ClearAddresses();
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
			$sports = $this->GetMySQLArray("SELECT data from other_data where name='favorite_sport' and lang='".$GLOBALS['page']->lang."'");
			$hobbies = $this->GetMySQLArray("SELECT data from other_data where name='interestsBase' and lang='".$GLOBALS['page']->lang."'");
            $industries = $this->GetMySQLArray("SELECT data from other_data where name='industries' and lang='".$GLOBALS['page']->lang."'");
            //$professions = $this->GetMySQLArray("SELECT data from other_data where name='professions' and lang='".$GLOBALS['page']->lang."'");
            $occ_areas = $this->GetMySQLArray("SELECT data from other_data where name='occ_areas' and lang='".$GLOBALS['page']->lang."'");
			$prof_profile = $this->GetMySQLArray("SELECT data from other_data where name='prof_profile' and lang='".$GLOBALS['page']->lang."'");
			$ac_degree = $this->GetMySQLArray("SELECT data from other_data where name='ac_degree' and lang='".$GLOBALS['page']->lang."'");
			$how_did_u = $this->GetMySQLArray("SELECT data from other_data where name='how_did_u' and lang='".$GLOBALS['page']->lang."'");
			$sex = $this->GetMySQLArray("SELECT data from other_data where name='sex' and lang='".$GLOBALS['page']->lang."'");
			
			$this->formCfg = array	(
									"fields" =>array(
													
													
													"account_lid" => 
																array("control_id" => "account_lid",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "DbField" => "account_lid"
																	  ),
													"linkedin_url_view" => 
																array("control_id" => "linkedin_url_view",
																	  "default_value"=>"#linkedin_url",
																	  "control_type" => "TXT",
																	  "required" => false
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
													 "linkedin" =>
																array("control_id" => "linkedin",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "required_message" => $this->words['thisRequired'],
																	  /*"validatorFun"=>"ValidateLinkedin",
																	  "validator_message" => $this->words['LinkedinValidatorRule'],*/
																	  "eLggExternal" => true
																	  ),
													"phone" =>
																array("control_id" => "phone",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "required_message" => "",
																	  "eLggExternal" => true
																	  ),
													"emailaddress" =>
																array("control_id" => "emailaddress",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => true,
																	  "required_message" => $this->words['thisRequired'],
																	  "validatorFun" => "IsValidEmail",
																	  "validator_message" => $this->words['inputValidEmail'],
																	  "DbField" => "account_email",
																	  "eLggExternal" => true
																	  ),
								  					"requestReason" =>
																array("control_id" => "requestReason",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => true,
																	  "required_message" => $this->words['thisRequired'],
																	  "DbField" => "account_reason"
																	  ),
													"birth_d" =>
																array("control_id" => "birth_d",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "validator_message" => $this->words['birthInvalid']
																	  ),
													"birth_m" =>
																array("control_id" => "birth_m",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "required_message" => ""
																	  ),
													"birth_y" =>
																array("control_id" => "birth_y",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "required_message" => ""
																	  ),
													"birthDate" =>
																array("control_id" => "birthDate",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "eLggExternal" => true
																	  ),		  
																		
													"residence_city" =>
																array("control_id" => "residence_city",
																	  "default_value"=>"",
																	  "control_type" => "TXT",
																	  "required" => false,
																	  "required_message" => "",
																	  "eLggExternal" => true,
																	  "eLggPublic" => true
																	  ),
													"terms_privacy" =>
																array("control_id" => "terms_privacy", 
																	  "default_value"=>0, 
																	  "required" => false, 
																	  "required_message" => $this->words['thisRequired'],
																	  "value_on"=>1,
																	  "control_type" => "CHK"),
													"terms_services" =>
																array("control_id" => "terms_services", 
																	  "default_value"=>0, 
																	  "required" => true, 
																	  "required_message" => $this->words['thisRequired'],
																	  "value_on"=>1,
																	  "control_type" => "CHK"),
																	  
													),
													
									"lists"	 => array(
														"isdonate" => array(
																"control_id" => "isdonate",
																"control_type" => "DDL",
																"use_key" => true,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> array(0=>$this->words["I_not_donate"], 1=>$this->words["I_donate"]),
																"checked_value" => 'checked',
																"default_value" => -1,
																),
														"moneydonate" => array(
																"control_id" => "moneydonate",
																"control_type" => "DDL",
																"use_key" => false,
																"required" => false,
																"source" 		=> array(10, 20, 30, 50),
																"checked_value" => 'checked',
																"default_value" => 10
																),
																
														"prof_profile" => array(
																"control_id" => "prof_profile",
																"control_type" => "DDL",
																"use_key" => true,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $prof_profile,
																"checked_value" => 'selected="selected"',
																"use_html_replace" => true,
																"eLggExternal" => true,
																"default_value" => -1,
																"invalid_value" => -1
																),
														"how_did_u" => array(
																"control_id" => "how_did_u",
																"control_type" => "DDL",
																"use_key" => true,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $how_did_u,
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false,
																"eLggExternal" => true,
																"default_value" => -1,
																"invalid_value" => -1
																),
														"sex" => array(
																"control_id" => "sex",
																"control_type" => "DDL",
																"use_key" => true,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $sex,
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false,
																"eLggExternal" => true,
																"default_value" => -1,
																"invalid_value" => -1
																),
														"languages" => array(
																"control_id" => "languages",
																"control_type" => "DDL",
																"use_key" => true,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> "select * FROM phpgw_languages where lang_id in ("."'".str_replace(",", "','", $GLOBALS['sitemgr_info']['site_languages'])."'".") ORDER BY lang_name",
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false,
																"default_value" => "it",
																"eLggExternal" => true,
																"eLggPublic" => true,
																"invalid_value" => -1
																),
														"residence_country" => array(
																"control_id" => "residence_country",
																"control_type" => "DDL",
																"use_key" => false,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $countries,
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false,
																"eLggPublic" => true,
																"eLggExternal" => true,
																"default_value" => "Italy",
																"invalid_value" => -1
																),
														"ac_degree" => array(
																"control_id" => "ac_degree",
																"control_type" => "DDL",
																"use_key" => true,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $ac_degree,
																"checked_value" => 'selected="selected"',
																"use_html_replace" => false,
																"eLggExternal" => true,
																"default_value" => -1,
																"invalid_value" => -1
																),
														"favorite_sport" => array(
																"control_id" => "favorite_sport",
																"control_type" => "DDL",
																"use_key" => true,
																"required" => false,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $sports,
																"checked_value" => 'checked',
																"use_html_replace" => false,
																"colCount" => 2,
																"eLggExternal" => true,
																"default_value" => -1
																),
																
														"interestsBase" => array(
																"control_id" => "interestsBase",
																"control_type" => "MDDL",
																"use_key" => true,
																"required" => false,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $hobbies,
																"checked_value" => 'checked',
																"use_html_replace" => false,
																"colCount" => 2,
																"eLggExternal" => true,
																"default_value" => -1
																),
														"industries" => array(
																"control_id" => "industries",
																"control_type" => "MDDL",
																"use_key" => true,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $industries,
																"checked_value" => 'checked',
																"use_html_replace" => false,
																"colCount" => 2,
																"eLggExternal" => true,
																"default_value" => -1
																),
														"occ_areas" => array(
																"control_id" => "occ_areas",
																"control_type" => "MDDL",
																"use_key" => true,
																"required" => true,
																"required_message" => $this->words['thisRequired'],
																"source" 		=> $occ_areas,
																"checked_value" => 'checked',
																"use_html_replace" => false,
																"colCount" => 2,
																"eLggExternal" => true,
																"default_value" => -1
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
			$words['interestsBase'] = lang("Base Interests");
			$words['professionaldata'] = lang("professional data");
			$words['industry'] = lang("industry");
			//$words['profession'] = lang("profession");
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
			$words['Invalid_characters'] = lang('Invalid characters');
			$words['thisRequired'] = lang("thisRequired");
			$words['commonError'] = lang("commonError");
			$words['inputValidEmail'] = lang("inputValidEmail");
			$words['birthInvalid'] = lang("birthInvalid");
			
			$words["I_not_donate"] = lang("I won't donate to Milan-IN");
			$words["I_donate"] = lang("I will donate to Milan-IN");
			$words['uniqueError'] = lang("User with same id already exists");
			$words['invalidLetters'] = lang("Please type your name in plain English");
			
			$words['LinkedinRule']= lang("Input just a profile's number or direct link to profile.");
			$words['LinkedinValidatorRule']= lang("You should input valid value.");
			$words['DonateTitleText'] = lang("You select donation.");
			$words["validDonate"] = lang("Thank you for donation.");
			$words["invalidDonate"] = lang("We couldn't process your donate. Please call us directly.");

			$this->words = $words;
		}
	}
	/*				if (isset($p_ic)){
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
								
								
								
								/*
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
				return $content;
				
				/*if ($remove_invitation_query != ""){
	                                          $result = mysql_query ($remove_invitation_query, $mysql_link) 
	                                          or die ($remove_invitation_query."<br/>".mysql_error($mysql_link)); $date = date("d.m.Y H:i");
												$link = "http://". $_SERVER['SERVER_NAME']."/egroupware/index.php?menuaction=admin.uiaccounts.edit_user&account_id=$user_id";
	                                        }
*/
/*$posted = Array
					(
					    "page_name" => "Joinus",
					    "mc_gross" => 0.01,
					    "address_status" => "unconfirmed",
					    "payer_id" => "DLBNF8ZY3VFLU",
					    "tax" => 0.00,
					    "address_street" => "Via Dei Caduti,26",
					    "payment_date" => "07:24:09 Jun 17, 2008 PDT",
					    "payment_status" => "Completed",
					    "charset" => "windows-1252",
					    "address_zip" => 20020,
					    "first_name" => "Michael",
					    "mc_fee" => 0.01,
					    "address_country_code" => "IT",
					    "address_name" => "Michael Tabolsky",
					    "notify_version" => 2.4,
					    "custom" => "",
					    "payer_status" => "verified",
					    "business" => "tesoreria@milanin.com",
					    "address_country" => "Italy",
					    "address_city" => "Arese",
					    "quantity" => 0,
					    "payer_email" => "mtabolsky@gmail.com", 
					    "verify_sign" => "A33ruGD7coVRDZKptpgWMT0AdMjjAYWk7Qx0ubztIj4fv1gKydhqUg0w",
					    "txn_id" => "99232146S5864270W",
					    "payment_type" => "instant",
					    "last_name" => "Tabolsky",
					    "address_state" => "Milano",
					    "receiver_email" => "mbrembil@gmail.com",
					    "payment_fee" => "",
					    "receiver_id" => "FFL42LAWPNF4G",
					    "txn_type" => "web_accept",
					    "item_name" => "Donate to Milan IN B.C.",
					    "mc_currency" => "EUR",
					    "item_number" => 666,
					    "residence_country" => "IT",
					    "payment_gross" => "",
					    "merchant_return_link" => "Return to Business Club Milan IN"
					);*/
?>

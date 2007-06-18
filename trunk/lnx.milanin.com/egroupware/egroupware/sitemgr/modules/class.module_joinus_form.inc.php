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

	class module_joinus_form extends Module
	{
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
				
				extract ($_POST, EXTR_PREFIX_ALL, 'p');
				extract ($_GET, EXTR_PREFIX_ALL, 'g');
				
				$log ="";
				$content = "";
				$mysql_link = mysql_connect($GLOBALS['phpgw_domain']['default']['db_host'],
                                $GLOBALS['phpgw_domain']['default']['db_user'], 
                                $GLOBALS['phpgw_domain']['default']['db_pass']) 
                                  or die( "Unable to connect to SQL server");
				
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
                                          
                                          if (isset($invitation['ident'])){
                                            $p_email=$invitation['email'];
                                            $remove_invitation_query='DELETE FROM '.$arguments['members_db_name'].".".
                                                                      $arguments['invitations_table'].
                                                                     ' WHERE ident='.$invitation['ident'];
                                          } else {
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
					
					$account_lid = strtolower($p_name.".".$p_surname);
					$account_pwd = md5 (strtolower ($p_name) );
                                        
                                        mysql_select_db ($GLOBALS['phpgw_domain']['default']['db_name']) or die(mysql_error());
					$query = "INSERT INTO phpgw_accounts 
					(`account_lid`, `account_pwd`, `account_firstname`, `account_lastname`, 
					`account_type`, `account_primary_group`, `account_email`, `account_expires`, `person_id`, `account_status`,`account_membership_date`)
					VALUES ('$account_lid', '$account_pwd', '$p_name', '$p_surname', 'u', 8, '".strtolower($p_email)."', '-1', 0,'',CURDATE())";
					
					$result = mysql_query ($query, $mysql_link) or die ($query."<br/>".mysql_error($mysql_link));
					$user_id =  mysql_insert_id($mysql_link);
					$query="INSERT into phpgw_acl (".
                                               "`acl_appname`,`acl_location`,`acl_account`,`acl_rights`) ".
                                               "VALUES ('phpgw_group',8,".$user_id.",1)";
                                        $result = mysql_query ($query, $mysql_link) or die ($query."<br/>".mysql_error($mysql_link));
                                        $query="INSERT into phpgw_acl (".
                                               "`acl_appname`,`acl_location`,`acl_account`,`acl_rights`) ".
                                               "VALUES ('phpgw_group',18,".$user_id.",1)";
                                        $result = mysql_query ($query, $mysql_link) or die ($query."<br/>".mysql_error($mysql_link));
                                        $query="INSERT into phpgw_acl (".
                                               "`acl_appname`,`acl_location`,`acl_account`,`acl_rights`) ".
                                               "VALUES ('preferences','changepassword',".$user_id.",1)";
                                        $result = mysql_query ($query, $mysql_link) or die ($query."<br/>".mysql_error($mysql_link));
					//$query = "INSERT into phpgw_fud_users 
					$users_opt = 2|4|16|32|64|128|256|512|2048|4096|8192|16384|131072|4194304;
			
			$query="INSERT INTO phpgw_fud_users (last_visit, join_date, theme, alias, login, email, passwd, name, users_opt, egw_id) VALUES("
                        .time().", "
                        .time().",
                        1,
                        '$account_lid', 
                        '$account_lid', 
                        '$account_email', 
                        '$account_pwd', 
                        '$p_name $p_surname',
                        $users_opt, $user_id)";
                                        $result = mysql_query ($query, $mysql_link) or die ($query."<br/>".mysql_error($mysql_link));
                                        
					if ($remove_invitation_query != ""){
                                          $result = mysql_query ($remove_invitation_query, $mysql_link) 
                                          or die ($remove_invitation_query."<br/>".mysql_error($mysql_link));
                                        }
					mysql_close($mysql_link);	
					
					
					
					//Start mail:
					
					$date = date("d.m.Y H:i");
					$link = "http://". $_SERVER['SERVER_NAME']."/egroupware/index.php?menuaction=admin.uiaccounts.edit_user&account_id=$user_id";
					$msg = "A new application for membership has been received by Milan IN Web Site on $date\n".(
                                        (isset($p_ic)) ? lang('invited by').":\n".$invitation['inviter'].
                                                   "\nhttp://".$_SERVER['SERVER_NAME']."/members/".$invitation['account_lid']."\n" : "")
                                        ."User Data follows:\n".
                                        "---- First Name ----\n$p_name\n ----\n---- Last Name ----\n$p_surname\n----\n".
                                        "---- Phone ----\n$p_phone\n----\n---- e-mail ----\n$p_email\n---- URL to LinkedIn ----\n$p_url\n".
                                        "---- Comment ----\n$p_msg\n----\n".
                                        "---- How did ----\n$p_how_did_u\n----\n".
					"---- Proffessional Profile ----\n$p_prof_profile\n----\n".
                                        "\n Follow this link to view and edit the new user account:\n $link\n";
					
					require_once(PHPGW_API_INC.'/class.send.inc.php');
					
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
                                        /*"<h3>Your application for the membership has been sent, the administration is notified, and you will be contacted shortly. <br/>We thank you for your interest</h3>";*/
					unset ($_POST);
						
				}
				mysql_select_db ($GLOBALS['phpgw_domain']['default']['db_name']) or die(mysql_error());
				$query = "SELECT data from other_data where name='countries_list'";
				$countries_result=mysql_query ($query, $mysql_link) 
                                          or die ($query."<br/>".mysql_error($mysql_link));
                                $countries=mysql_fetch_array($countries_result);
				mysql_free_result($countries_result);
				$countries=explode("\n",$countries[0]);
                                $query = "SELECT data from other_data where name='sports' and lang='".
                                          $GLOBALS['page']->lang."'";
                                $result=mysql_query ($query, $mysql_link) 
                                          or die ($query."<br/>".mysql_error($mysql_link));
                                $sports=mysql_fetch_array($result);
                                mysql_free_result($result);
                                $sports=explode("\n",$sports[0]);
                                $query = "SELECT data from other_data where name='hobbies' and lang='".
                                          $GLOBALS['page']->lang."'";
                                $result=mysql_query ($query, $mysql_link) 
                                          or die ($query."<br/>".mysql_error($mysql_link));
                                $hobbies=mysql_fetch_array($result);
                                mysql_free_result($result);
                                $hobbies=explode("\n",$hobbies[0]);
                                
                                $query = "SELECT data from other_data where name='industries' and lang='".
                                          $GLOBALS['page']->lang."'";
                                $result=mysql_query ($query, $mysql_link) 
                                          or die ($query."<br/>".mysql_error($mysql_link));
                                $industries=mysql_fetch_array($result);
                                mysql_free_result($result);
                                $industries=explode("\n",$industries[0]);
                                
                                $query = "SELECT data from other_data where name='professions' and lang='".
                                          $GLOBALS['page']->lang."'";
                                $result=mysql_query ($query, $mysql_link) 
                                          or die ($query."<br/>".mysql_error($mysql_link));
                                $professions=mysql_fetch_array($result);
                                mysql_free_result($result);
                                $professions=explode("\n",$professions[0]);
                                
                                $query = "SELECT data from other_data where name='occ_areas' and lang='".
                                          $GLOBALS['page']->lang."'";
                                $result=mysql_query ($query, $mysql_link) 
                                          or die ($query."<br/>".mysql_error($mysql_link));
                                $occ_areas=mysql_fetch_array($result);
                                mysql_free_result($result);
                                $occ_areas=explode("\n",$occ_areas[0]);
                                
				if  (!isset($p_btn_submit) || !empty ($log))
				{
                                  $how_did_u=explode(",",$arguments['how_did_u']);
				  $prof_profile=explode(",",$arguments['prof_profile']);
				  $ac_degree=explode(",",$arguments['ac_degree']);

			          $content .= '<p><font color="red">*</font> - '.lang('required fields').'</p>';
                                  $content .= '<form name="joinus" method="post" action="">';
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
                                          $content .= '<table>';
                                          $content .= '
                                          <tr>
                                                  <td>'.lang('Name').' <font color="red">*</font> </td>
                                                  <td><input type="text" name="name" value='.$p_name.'></td>
                                          </tr>
                                          <tr>
                                                  <td>'.lang('last name').' <font color="red">*</font></td>
                                                  <td><input type="text" name="surname" value='.$p_surname.'></td>
                                          </tr>
					  <tr>
                                                  <td>'.lang("Which professional profile better describes you").'<font color="red">*</font></td>
                                                  <td><select name="prof_profile">';
                                          foreach ($prof_profile as $opt){
                                            $content.='<option value="'.$opt.'">'.$opt.'</option>'."\n";
                                          }
                                          $content.='</select></td>
                                          </tr>
                                          <tr>
                                                  <td><a href="#linkedin_url">URL to Linkedin Profile</a> </td>
                                                  <td><input type="text" name="url" value='.$p_url.'></td>
                                          </tr>
                                          <tr>
                                                  <td>'.lang('phone number').'</td>
                                                  <td><input type="text" name="phone" value='.$p_phone.'></td>
                                          </tr>
                                          <tr>
                                                  <td>'.lang('email').'<font color="red">*</font></td>
                                                  <td><input type="text" name="email" value='.$p_email.'></td>
                                          </tr>
                                          <tr>
                                                  <td>Reason for requesting Club Membership <font color="red">*</font></td>
                                                  <td><textarea name="msg" rows="10">'.$p_msg.'</textarea></td>
                                          </tr><tr>
                                                  <td>'.lang("How did you know about the club").'<font color="red">*</font></td>
                                                  <td><select name="how_did_u">';
                                          foreach ($how_did_u as $opt){
                                            $content.='<option value="'.$opt.'">'.$opt.'</option>'."\n";
					  }	
                                          $content.='</select></td>
                                          </tr>
					<tr>
					<th colspan="2">'.lang("personal data").'</th>
					</tr>
					<tr>
					<td>'.lang('sex').'<font color="red">*</font></td>
						<td>
						<select name="sex">
						<option value="-1">---</option>
						<option value="1">'.lang("female").'</option>
						<option value="2">'.lang("male").'</option>
						</select>
					</td>
                                          </tr>
					<tr>
                                                  <td>'.lang('birth date').'</td>
                                                  <td>
							<input size="2" type="text" name="birth_d" value='.
							((isset($p_birth_d))?$p_birth_d:lang('dd')).' />
							<input size="2" type="text" name="birth_m" value='.
							((isset($p_birth_m))?$p_birth_m:lang('mm')).' />
							<input size="4" type="text" name="birth_y" value='.
							((isset($p_birth_y))?$p_birth_y:lang('yyyy')).' />
						</td>
                                          </tr>
					<tr>
                                                  <td>'.lang("country of residence").'<font color="red">*</font></td>
                                                  <td><select name="residence_country">';
					  $countries[]=lang("not in the list");
                                          foreach ($countries as $country){
					    $country=rtrim($country);
                                            $content.='<option value="'.$country.'"'.
						((isset($p_residence_country) && strcmp($p_residence_country,$country)==0)
						?" selected" :"").
					    '>'.$country.'</option>'."\n";
                                          }
                                          $content.='</select></td>
					  </tr>
					 <tr>
                                                  <td>'.lang('city of residence').'</td>
                                                  <td><input type="text" name="residence_city" value='.$p_residence_city.'></td>
                                          </tr>
                                          <tr>
                                                  <td>'.lang("your academic degree").'<font color="red">*</font></td>
                                                  <td><select name="ac_degree">';
                                          foreach ($ac_degree as $opt){
                                            $content.='<option value="'.$opt.'">'.$opt.'</option>'."\n";
                                          }     
                                          $content.='</select></td>
                                          </tr>
                                          <tr>
                                                  <td>'.lang("favorite sport").'<font color="red">*</font></td>
                                                  <td><select name="favorite_sport">';
                                          $sports[]=lang("not in the list");
                                          foreach ($sports as $sport){
                                            $sport=rtrim($sport);
                                            $content.='<option value="'.$sport.'"'.
                                                ((isset($p_favorite_sport) && strcmp($p_favorite_sport,$sport)==0)
                                                ?" selected" :"").
                                            '>'.$sport.'</option>'."\n";
                                          }
                                          $content.='</select></td>
                                          </tr>
                                          <tr>
                                            <td>'.lang("interests").'<font color="red">*</font></td>
                                            <td>';
                                          $hobbies[]=lang("other");
                                          foreach ($hobbies as $hobby){
                                            $hobby=rtrim($hobby);
                                            $hobby_chkbox_name=preg_replace('/[^A-Za-z]/',"_",$hobby);
                                            $content.='<input '.
                                                ((isset($p_interests[$hobby_chkbox_name]))?"checked ":" ").
                                                'type="checkbox"'.
                                                'name="interests['.$hobby_chkbox_name.']">'.
                                                $hobby.'<br/>';
                                          }
                                          $content.='</td>
                                          </tr>
                                          <tr>
                                        <th colspan="2">'.lang("professional data").'</th>
                                        </tr>
                                        <tr>
                                            <td>'.lang("industry").'<font color="red">*</font></td>
                                            <td>';
                                          $industries[]=lang("other");
                                          foreach ($industries as $industry){
                                            $industry=rtrim($industry);
                                            $industry_chkbox_name=preg_replace('/[^A-Za-z]/',"_",$industry);
                                            $content.='<input '.
                                                ((isset($p_industries[$industry_chkbox_name]))?"checked ":" ").
                                                'type="checkbox"'.
                                                'name="industries['.$industry_chkbox_name.']">'.
                                                $industry.'<br/>';
                                          }
                                          $content.='</td>
                                          </tr>
                                          <tr>
                                            <td>'.lang("profession").'<font color="red">*</font></td>
                                            <td>';
                                          $professions[]=lang("other");
                                          foreach ($professions as $profession){
                                            $profession=rtrim($profession);
                                            $profession_chkbox_name=preg_replace('/[^A-Za-z]/',"_",$profession);
                                            $content.='<input '.
                                                ((isset($p_professions[$profession_chkbox_name]))?"checked ":" ").
                                                'type="checkbox"'.
                                                'name="professions['.$profession_chkbox_name.']">'.
                                                $profession.'<br/>';
                                          }
                                          $content.='</td>
                                          </tr>
                                          <tr>
                                            <td>'.lang("occ_area").'<font color="red">*</font></td>
                                            <td>';
                                          $occ_areas[]=lang("other");
                                          foreach ($occ_areas as $occ_area){
                                            $occ_area=rtrim($occ_area);
                                            $occ_area_chkbox_name=preg_replace('/[^A-Za-z]/',"_",$occ_area);
                                            $content.='<input '.
                                                ((isset($p_occ_areas[$occ_area_chkbox_name]))?"checked ":" ").
                                                'type="checkbox"'.
                                                'name="occ_areas['.$occ_area_chkbox_name.']">'.
                                                $occ_area.'<br/>';
                                          }
                                          $content.='</td>
                                          </tr>
                                          <tr>
                                        <th colspan="2">'.lang("terms acceptance").'</th>
                                        </tr>
                                        <tr>
                                              <td>'.lang('privacy terms').'</td>
                                              <td><input type="checkbox" name="terms_privacy" />'.lang('accept').'
                                        </tr>
                                        <tr>
                                              <td>'.lang('services terms').'</td>
                                              <td><input type="checkbox" name="terms_services" />'.lang('accept').'
                                        </tr>    
                                          <tr>
                                                  <td colspan="2"><input type="submit" class="button" name="btn_submit" value="'.lang('send').'"></td>
                                          </tr>
                                          </table>';
                                          $content .= '</form>';
                                        }
				
				return $content;
		}
	}

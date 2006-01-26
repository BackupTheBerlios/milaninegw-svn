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
				'recepient' => array('type' => 'textfield', 'label' => lang('Where to send request')),
			);
			$this->properties = array();
			$this->title = lang('Join US! Richiedi l\'iscrizione al Club!');
			$this->description = lang('This module lets users to submit registration form');
		}
	
		function get_content(&$arguments,$properties)
		{
				//print_r ($_POST);
				//print_r ($arguments['recepient']);
				
				extract ($_POST, EXTR_PREFIX_ALL, 'p');
				
				$log ="";
				$content = "";
				
				if (isset($p_btn_submit))
				{
					if (empty ($p_name) || empty ($p_surname) ||  empty ($p_email) || empty ($p_msg))
						$log .= lang('you must fill in all of the required fields')."<br>";
					
					if (!preg_match ("/.+@.+\.[a-z]+/", $p_email))
						$log .= lang('you have entered an invalid email address')."<br>";
					
					if (strlen ($p_name)<2 || strlen ($p_surname)<2)
						$log .= lang('too short name')."<br>";
				}
				if (isset($p_btn_submit) && empty ($log))
				{
					
					/// Dtart Database INSERT
					
					$mysql_link = mysql_connect($GLOBALS['phpgw_domain']['default']['db_host'], $GLOBALS['phpgw_domain']['default']['db_user'], $GLOBALS['phpgw_domain']['default']['db_pass']) or die( "Unable to connect to SQL server");
					mysql_select_db ($GLOBALS['phpgw_domain']['default']['db_name']) or die(mysql_error());
					
					$account_lid = strtolower($p_name.".".$p_surname);
					$account_pwd = md5 (strtolower ($p_name) );
					
					$query = "INSERT INTO phpgw_accounts 
					(`account_lid`, `account_pwd`, `account_firstname`, `account_lastname`, 
					`account_type`, `account_primary_group`, `account_email`, `account_expires`, `person_id`, `account_status`)
					VALUES ('$account_lid', '$account_pwd', '$p_name', '$p_surname', 'u', 18, '$p_email', '-1', 0,'')";
					
					$result = mysql_query ($query, $mysql_link) or die ($query."<br>".mysql_error($mysql_link));
					$user_id =  mysql_insert_id($mysql_link);
					mysql_close($mysql_link);	
					
					
					
					//Start mail:
					
					$date = date("d.m.Y H:i");
					$link = "http://". $_SERVER['SERVER_NAME']."/egroupware/index.php?menuaction=admin.uiaccounts.edit_user&account_id=$user_id";
					$msg = "There was a new post from Milan-IN Web Site on $date\nUser Data\nName: $p_name $p_surname\n Phone: $p_phone\n e-mail: $p_email\n URL to LinkedIn: $p_url\n $p_msg\n\n\n Follow this link to view new user profile\n $link";
					
					require_once(PHPGW_API_INC.'/class.send.inc.php');
					
					$mailer = new send();
					$mailer->Subject = "New member request";  // change it 
					$mailer->Body = $msg;
					
					$mailer->From = "webmaster@milanin.com";  // change it
					$mailer->FromName = "MilanIn webmaster";  // change it
					 
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
					
					$content .= "<h3>Your application for the membership has been sent, the administration is notified, and you will be contacted shortly. <br/>We thank you for your interest</h3>";
					unset ($_POST);
						
				}
				
				if  (!isset($p_btn_submit) || !empty ($log))
				{
					$content .=  "<p class='error'>$log </p>";
					$content .= '<p><font color="red">*</font> - '.lang('required fields').'</p>';
					$content .= '<form name="joinus" method="post" action="">';
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

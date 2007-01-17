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

	class module_not_only_milanin extends Module
	{
		function module_not_only_milanin()
		{
			$this->arguments = array(
				'recepient' => array('type' => 'textfield','label' => lang('Where to send recommendation')),
                                /*'invitations_table' => array('type'=>'textfield','label'=>lang('invitations table') ),
                                'members_db_name' => array('type'=>'textfield','label'=>lang('members db name') ),*/
                                'how_did_u' => array('type'=>'textfield','label'=>lang('how_did_u')),
                                'loc' => array('type'=>'textfield','label'=>lang('Local events areas list'))
			);
			$this->properties = array();
			$this->title = lang('recommend a club');
			$this->description = lang('This module lets visitors to submit a club');
		}
	
		function get_content(&$arguments,$properties)
		{
				
				extract ($_POST, EXTR_PREFIX_ALL, 'p');
				extract ($_GET, EXTR_PREFIX_ALL, 'g');
                                
				if (isset($p_btn_submit))
				{
					if (empty ($p_name) || empty ($p_email) ||  empty ($p_club_name) || empty ($p_club_address) || empty ($p_club_descr))
						$log .= lang('you must fill in all of the required fields')."<br/>";
					
					if (!preg_match ("/.+@.+\.[a-z]+/", strtolower($p_email)))
						$log .= lang('you have entered an invalid email address').": [".$p_email."] <br/>";
					
					if (strlen ($p_name)<2 )
						$log .= lang('too short name')."<br/>";
                                        if (strlen ($p_club_name)<2)
						$log .= lang('too short club name')."<br/>";
                                        if (strlen ($p_club_descr)<2)
						$log .= lang('too short description')."<br/>";
                                        if (strlen ($p_club_address)<4)
						$log .= lang('too short address')."<br/>";
				}
					//Start mail:
				if (isset($p_btn_submit) && empty($log)) {
					$date = date("d.m.Y H:i");
					$msg = "A new club was recommended on the site $date\n".
                                        "Recommendation Data follows:\n".
                                        "---- Name ----\n$p_name\n ----".
                                        "---- Phone ----\n$p_phone\n----\n---- e-mail ----\n$p_email\n----\n".
                                        "---- Club Name ----\n$p_club_name\n----\n".
                                        "---- Club Address ----\n$p_club_address\n----\n".
                                        "---- Club Description ----\n$p_club_descr\n----\n".
                                        "---- How did ----\n$p_how_did_u\n----\n".
                                        "---- Location ----\n$p_location\n----\n";;
					
					require_once(PHPGW_API_INC.'/class.send.inc.php');
					
					$mailer = new send();
					$mailer->Subject = "New club recommendation";
					$mailer->Body = $msg;
					
					$mailer->From = "webmaster@milanin.com";
					$mailer->FromName = "Milan IN website";
					 foreach (explode(",",$arguments['recepient']) as $rcpt){
                                          $mailer->AddAddress($rcpt);
                                        }
					
					if(!$mailer->Send())
					{
						$content .= lang('There was a problem sending this mail!');
						$content .= $mailer->ErrorInfo;
					}

					$mailer->ClearAddresses();

					$msg = lang("Thank you for your recommendation");
					
					$mailer = new send();
					$mailer->Subject = lang("Recommendation is accepted");  // change it 
					$mailer->IsHTML(true);
					$mailer->Body = $msg;
					
					$mailer->From = "iscrizioni@milanin.com";  // change it
					$mailer->FromName = "Segreteria Business Club Milan IN";  // change it
					 
					$mailer->AddAddress($p_email);
					
					if(!$mailer->Send())
{
						$content .= lang('There was a problem sending mail to ')." ".$p_email.'!';
						$content .= $mailer->ErrorInfo;
}

					$mailer->ClearAddresses();
					
					$content .= lang("Thank you for your recommendation");
					unset ($_POST);
						
				}
				
				if  (!isset($p_btn_submit) || !empty ($log)){
                                
                                  $how_did_u=explode(",",$arguments['how_did_u']);
                                  $location=explode(",",$arguments['loc']);
                                          $content .=  "<p class='error'>$log </p>";
                                          $content .= '<p><font color="red">*</font> - '.lang('required fields').'</p>';
                                          $content .= '<form name="recommend_a_club" method="post" action="">';
                                          $content .= '<table>';
                                          $content .= '<tr><th colspan="2">'.lang('Personal data').'</th></tr>
                                          <tr>
                                                  <td>'.lang('your name').' <font color="red">*</font> </td>
                                                  <td><input type="text" name="name" value='.$p_name.'></td>
                                          </tr>
                                          <tr>
                                                  <td>'.lang('your phone number').'</td>
                                                  <td><input type="text" name="phone" value='.$p_phone.'></td>
                                          </tr>
                                          <tr>
                                                  <td>'.lang('your email').'<font color="red">*</font></td>
                                                  <td><input type="text" name="email" value='.$p_email.'></td>
                                          </tr>
                                           <tr><th colspan="2">'.lang('Club data').'</th></tr>
                                          <tr>
                                                  <td>'.lang('Club Name').' <font color="red">*</font> </td>
                                                  <td><input type="text" name="club_name" value='.$p_club_name.'></td>
                                          </tr>
                                          <tr>
                                                  <td>'.lang('Club Address').' <font color="red">*</font> </td>
                                                  <td><input type="text" name="club_address" value='.$p_club_address.'></td>
                                          </tr>
                                          <tr>
                                                  <td>'.lang('Club Description').'<font color="red">*</font></td>
                                                  <td><textarea name="club_descr" rows="10">'.$p_club_descr.'</textarea></td>
                                          </tr><tr>
                                                  <td>'.lang("How did you know about the club").'<font color="red">*</font></td>
                                                  <td><select name="how_did_u">';
                                          foreach ($how_did_u as $opt){
                                            $content.='<option value="'.lang($opt).'">'.lang($opt).'</option>'."\n";
                                            }
                                          
                                          $content.='</select></td>
                                          </tr><tr>
                                                  <td>'.lang("Local events area").'<font color="red">*</font></td>
                                                  <td><select name="location">';
                                          foreach ($location as $opt){
                                            $content.='<option value="'.lang($opt).'">'.lang($opt).'</option>'."\n";
                                            }
                                          
                                          $content.='</select></td>
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
		
	

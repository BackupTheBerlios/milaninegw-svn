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

	class module_contactus_form extends Module
	{
		function module_contactus_form()
		{
			$this->arguments = array(
				'subject_list'=> array('type' => 'textfield', 'label' => lang('List of request subjects (comma separated)')),
				'recepient_list' => array('type' => 'textfield', 'label' => lang('List of corresponding e-mails (comma separated)')),
			);
			$this->properties = array();
			$this->title = lang('Contact US!');
			$this->description = lang('This module lets users to send e-mail to administration');
		}
	
		function get_content(&$arguments,$properties)
		{
				//print_r ($_POST);
				//print_r ($arguments);
				
				extract ($_POST, EXTR_PREFIX_ALL, 'p');
				
				$log ="";
				$content = "";
				
				if (isset($p_btn_submit))
				{
					if (empty ($p_name) || empty ($p_email) || empty ($p_msg))
						$log .= lang('you must fill in all of the required fields')."<br/>";
					
					if (!preg_match ("/.+@.+\.[a-z]+/", $p_email))
						$log .= lang('you have entered an invalid email address')."<br/>";
					
					if (strlen ($p_name)<2)
						$log .= lang('too short name')."<br/>";
				}
				
				$recepients = explode (",", $arguments['recepient_list']);
				$subjects = explode (",", $arguments['subject_list']);
				
				if (isset($p_btn_submit) && empty ($log))
				{
					//Start mail:
					require_once(PHPGW_API_INC.'/class.send.inc.php');
					$mailer = new send();
					
					$date = date("d.m.Y H:i");
					$msg = "There was a new post from Milan-IN Web Site on $date\nUser Data\nName: $p_name\n Phone: $p_phone\n e-mail: $p_email\n $p_msg";
					
					$mailer->Subject = $subjects[$p_subj];
					foreach ($recepients as $rcpt){
					  $mailer->AddAddress($rcpt);
					}
					$mailer->Body = $msg;
					
					$mailer->From = "webmaster@milanin.com";  // change it
					$mailer->FromName = "MilanIn webmaster";  // change it
					 
					//$mailer->AddAddress("piercarlo.pozzati@milanin.com"); // change it 
					
					if(!$mailer->Send())
					{
						$content .= 'There was a problem sending this mail!';
						$content .= $mailer->ErrorInfo;
					}
					else
						$content .= "<h3>Your message was sent successfully</h3>";

					$mailer->ClearAddresses();

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
						<td>'.lang('phone number').'</td>
						<td><input type="text" name="phone" value='.$p_phone.'></td>
					</tr>
					<tr>
						<td>'.lang('email').'<font color="red">*</font></td>
						<td><input type="text" name="email" value='.$p_email.'></td>
					</tr>
					<tr>
						<td>'.lang('subject').'</td>
						<td><select name="subj">';
						
					foreach ($subjects as $index => $subject)
					{
						$selected = ( $index == $p_subj )? " selected ":"";
						$content .= "<option value='$index' $selected>$subject</option>\n";
					}
						
					$content .='</select></td>
						
					
					<tr>
						<td>'.lang('message').' <font color="red">*</font></td>
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

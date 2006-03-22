<?php
/*
 * Copyright 2005-2006 Michael Tabolsky
 * This file is part of Milalnegw.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 * Description:  Handle creating and sending notifications
 */
class Notify {
	
	var $Name;
	var $Context;
	
	function Notify(&$Context)
        {
          $this->Name='Notify';
          $this->Context = &$Context;
          
        }

        function NotifyComment($CommentID,$CommentManager)
        {
          $Comment=$CommentManager->GetCommentById($CommentID,"0");
          $this->SendNotify($this->GetDiscussionWatchers($Comment->DiscussionID),
                                 '['.agAPPLICATION_TITLE.'] '. $this->Context->GetDefinition('NewCommentIn').' : '.$Comment->Discussion,
                                 $Comment->Discussion.", ".$this->Context->GetDefinition('From')." ".$Comment->AuthFullName."\n".
                                 $this->Context->GetDefinition('FollowTheLink').
                                  " : http://".agDOMAIN."/comments.php?DiscussionID=".$Comment->DiscussionID."&#Comment_".$CommentID);
        }
        function NotifyDiscussion($DiscussionID,$DiscussionManager)
        {
          $Discussion=$DiscussionManager->GetDiscussionById($DiscussionID);
          $this->SendNotify($this->GetCategoryWatchers($Discussion->CategoryID),
                                 '['.agAPPLICATION_TITLE.'] '. $this->Context->GetDefinition('NewDiscussionIn').' : '.$Discussion->Category,
                                 $Discussion->Name.", ".$this->Context->GetDefinition('StartedBy')." ".$Discussion->AuthFullName."\n".
                                 $this->Context->GetDefinition('FollowTheLink').
                                  " : http://".agDOMAIN."/comments.php?DiscussionID=".$DiscussionID);
        }
        function GetDiscussionWatchers($DiscussionID)
        {
          $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
          $s->SetMainTable("UserBookmark","ub");
          $s->AddSelect("UserID","ub");
          $s->AddSelect("account_email","a");
          $s->AddJoin("accounts","a","account_id","ub","UserID","left join","phpgw_");
          $s->AddWhere("ub.DiscussionID",$DiscussionID,"=");
          $Data=$this->Context->Database->Select($this->Context, 
                                                  $s, 
                                                  $this->Name, 
                                                  "GetDiscussionWatchers",
                                                  "An error occurred while retrieving list of Discussion Watchers.");
          while ($Row=$this->Context->Database->GetRow($Data))
          {
            $Watchers[]=$Row['account_email'];
          }
          return $Watchers;
        }
        
        function GetCategoryWatchers($CategoryID)
        {
          $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
          $s->SetMainTable("acl","acl","phpgw_");
          $s->AddSelect("account_email","a");
          $s->AddJoin("accounts","a","account_id","acl","acl_account","left join","phpgw_");
          $s->AddJoin("categories","c","cat_owner","acl","acl_location","join","phpgw_");
          $s->AddJoin("UserCategoryWatch","cw","CategoryID","c","cat_id and cw.UserId=acl.acl_account","left join");
          $s->AddWhere("acl.acl_appname","phpgw_group","=");
          $s->AddWhere("c.cat_id",$CategoryID,"=","and","","0");
          $s->AddWhere("ISNULL(cw","CategoryID)",".","and","","0");
          $Data=$this->Context->Database->Select($this->Context, 
                                                  $s, 
                                                  $this->Name, 
                                                  "GetCategoryWatchers",
                                                  "An error occurred while retrieving list of Category Watchers.");
          while ($Row=$this->Context->Database->GetRow($Data))
          {
            $Watchers[]=$Row['account_email'];
          }
          return $Watchers;

        }
        
        function SendNotify($Rcpts=Array(),$Subject="",$Body="")
        {
          require_once(agEGW_APPLICATION_PATH.'/phpgwapi/inc/class.phpmailer.inc.php');
          $mailer = new PHPMailer;
          $mailer_settings=$this->GetMailerSettings();
          $mailer->From     = agSUPPORT_EMAIL;
          $mailer->FromName = agSUPPORT_NAME;
          $mailer->Host     = $mailer_settings['smtp_server'];
          $mailer->Mailer   = "smtp";
          $mailer->Body    = $Body;
          $mailer->Subject = $Subject;
          //$mailer->AddAddress(agSUPPORT_EMAIL,agSUPPORT_NAME);
          foreach ($Rcpts as $bcc){
            $mailer->AddBCC($bcc);
          }
          $mailer->SetLanguage("en",agEGW_APPLICATION_PATH.'/phpgwapi/setup/');

          if (!$mailer->Send())
          {
//               echo "<!--There has been a mail error sending: \n".$mailer->ErrorInfo."-->";
              return False;
          }
          $mailer->ClearAddresses();
          $mailer->ClearAttachments();
          return True;
          
        }
        
        function GetMailerSettings()
        {
          $mailer_settings=Array('smtp_server'=>'',
                                 'smtp_port'=>'');
            if (!isset($_SESSION['phpgwapi']['mailer']))
            {
              foreach(array_keys($mailer_settings) as $setting)
              {
                $s = $this->Context->ObjectFactory->NewContextObject($this->Context, "SqlBuilder");
                $s->SetMainTable("config","config","phpgw_");
                $s->AddSelect("config_value","config","Value");
                $s->AddWhere("config_name",$setting,"=");
                $Data=$this->Context->Database->Select($this->Context, 
                                                      $s, 
                                                      $this->Name, 
                                                      "GetMailerSettings",
                                                      "An error occurred while retrieving ".$setting.".");
                while ($Row=$this->Context->Database->GetRow($Data))
                {
                  $_SESSION['phpgwapi']['mailer'][$setting]=$Row['Value'];
                }
              }
            }
          return $_SESSION['phpgwapi']['mailer'];
        }



}

?>
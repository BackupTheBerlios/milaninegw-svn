<script language="JavaScript" type="text/javascript">
				<!--
				
				function doInvite(i)
         {
				  getInvitationMsg();
				  
				  //alert(document.invite_form.inv_salut.value+"--"+document.invite_form.invite_title.value);
				  if (i == 2)
				  document.invite_form.action.value='invite_invite';
				  
				  if (i == 1)
				  document.invite_form.action.value='invite_preview';
				  
				  //alert(document.invite_form.action);
				  return true;
				}
				-->
			</script>
<?php

$inv_msg_lang=title1;
if ($_REQUEST['inv_idx_title'] != null) {
$inv_msg_lang=$_REQUEST['inv_idx_title'];
}
$inv_idx_lang=language1;
if ($_REQUEST['inv_idx_lang'] != null) {
$inv_idx_lang=$_REQUEST['inv_idx_lang'];
}
$inv_idx_salut="";
if ($_REQUEST['inv_idx_salut'] != null) {
$inv_idx_salut=$_REQUEST['inv_idx_salut'];
}

$invite_title="";
if ($_REQUEST['invite_title'] != null) {
$invite_title=$_REQUEST['invite_title'];
}

$inv_salut="";
if ($_REQUEST['inv_salut'] != null) {
$inv_salut=$_REQUEST['inv_salut'];
}

$invite_name="";
if ($_REQUEST['invite_name'] != null) {
$invite_name=$_REQUEST['invite_name'];
}
$invite_email="";
if ($_REQUEST['inv_idx_lang'] != null) {
$invite_email=$_REQUEST['invite_email'];
}
$invite_action="invite_edit";
if ($_REQUEST['action'] != null) {
$invite_action=$_REQUEST['action'];
}    
    
    if ($invite_action == "invite_edit"){
    include('invite_edit.php');
    }
    if ($invite_action == "invite_preview"){
    include('invite_preview.php');
    }
$run_result .= <<< END
<script language="JavaScript" type="text/javascript">
<!--
getInvitationMsg();

-->
</script>		
END;

?>


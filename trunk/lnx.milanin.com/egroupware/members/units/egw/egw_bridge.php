<?/*
Bridge from elgg (www.elgg.net) to egroupware (www.egroupware.org)
Started at Sun Aug 28 16:10:27 CEST 2005, for MilanIN (www.milanin.org)
Copyright: Tabolsky Michael (www.gfdsa.org) and Valeria (lera.gfdsa.org)
License: GPL (http://www.gnu.org/licenses/gpl.html)
*/
//Here goes config to access egw DB
$egw['db_name']=db_name;//'Sql73134_1';
$egw['db_user']=db_user;//'Sql73134';
$egw['db_password']=db_password;//'4e455633';
$egw['db_tables_accounts']='phpgw_accounts';
$egw['db_tables_sessions']='phpgw_sessions';
$egw['session_storage']='db';
$egw['session_timeout']='db';

//Now some functions
//echo "<pre>";
//echo egw_is_new_user();

  //Returns account_id from egw db associated with account_lid stored in the session
function egw_get_id_by_lid(){
  global $egw;
  if ($egw['session_storage'] == 'db') {
    $sessionid = $_COOKIE['sessionid'];
    $sql="SELECT * FROM ".$egw['db_name'].".".$egw['db_tables_sessions']."  WHERE session_id='".$sessionid."' and session_flags != 'A'";
	$result = db_query($sql);
    if($row = $result[0]) 
	{
      $sql='SELECT account_id FROM '.$egw['db_name'].'.'.$egw['db_tables_accounts'].' WHERE '.
           'account_lid = \''.substr($row->session_lid,0,strpos($row->session_lid,'@')).'\' or account_lid = \''.$row->session_lid.'\'';
	  $result = db_query($sql);
      if($row = $result[0]) {
        return $row->account_id;
      }else{
        return -1;
      }
    }
  }
  $sql='SELECT account_id FROM '.$egw['db_name'].'.'.$egw['db_tables_accounts'].' WHERE '.
  'account_lid = \''.$_SESSION['phpgw_session']['session_lid'].'\'';
  $result = db_query($sql);
  if($row = $result[0]) {
    return $row->account_id;
  }else{
    return -1;
  }
}
//returs array of account information from egw db
function egw_get_account_info($id){
  global $egw;
  $sql = 'SELECT account_id AS ident, '.
       'account_lid AS username, '.
       'account_pwd AS password, '.
       'account_status AS active,'.
       'account_type AS user_type,'.
       'CONCAT(account_firstname,\' \',account_lastname) AS name,'.
       'account_email AS email,'.
       'account_linkedin AS linkedin ,'.
       'DATE_FORMAT(`account_membership_date`,\'%d/%m/%y\') as membership_date '.
       'FROM '.$egw['db_name'].'.'.$egw['db_tables_accounts'].
       ' WHERE account_id ='.$id;
  $result = db_query($sql);
  if($row = $result[0]) {
    //$row->user_type = ($row->user_type == "u") ? "person" : "community";
    //$row->active = ($row->active == "A") ? "yes" : "no";
    return $row;
  }else{
    return -1;
  }
}

//Creates new record in the ".tbl_prefix."users.table based on object passed from egw_get_account_info
function egw_is_new_user(){
  global $egw; 
  $id = egw_get_id_by_lid();
  if($id > 0) return 0; //veb: special fix. We have already eLgg profile. So we don't need to create it.
  
  $sql = "SELECT ident from ".tbl_prefix."users WHERE ident = ".$id;
  $result = db_query($sql);
  if (!$result[0]){
    $row=egw_get_account_info($id);
  
    $sql="insert into ".tbl_prefix."users (ident, username, password, email, name) values(".
        $row->ident.",'".
        $row->username."','".
        $row->password."','".
        $row->email."','".
        $row->name."')";
    $result = db_query($sql);
    if (!$result) echo mysql_error();
    
    $sql = "insert into ".tbl_prefix."profile_data (ident, owner, access, name, value) values(".
        '\'\','.
        $row->ident.",".
        '\'PUBLIC\','.
        '\'linkedin\','.
        '\''.$row->linkedin.'\')';
    $result = db_query($sql);
    if (!$result) echo mysql_error();
    $sql = "insert into ".tbl_prefix."profile_data (ident, owner, access, name, value) values(".
        '\'\','.
        $row->ident.",".
        '\'PUBLIC\','.
        '\'membership_date\','.
        '\''.$row->account_membership_date.'\')';
    $result = db_query($sql);
    if (!$result) echo mysql_error();
    
    return 1;
  }
  return 0;
}  
?>

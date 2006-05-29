<?
global $page_owner;

if (isset($_POST['home']['title']) && isset($_POST['home']['data'])) {
  //Do we have a page already?
    $result = db_query("select ident from ".tbl_prefix."home_data where owner = $page_owner AND name = 'body'");
    if (isset($result[0]->ident) && $result[0]->ident>0){
      $body_sql="update home_data set value='".$_POST['home']['data']."' where ident =".$result[0]->ident;
    }else{
      $body_sql="insert into ".tbl_prefix."home_data ( `ident` , `owner` , `access` , `name` , `value` ) values ('',".$page_owner.",0,'body','".$_POST['home']['data']."')";
    }
  //Do we have a page title already ?
    $result = db_query("select ident from ".tbl_prefix."home_data where owner = $page_owner AND name = 'title'");
    if (isset($result[0]->ident)){
      $title_sql="update home_data set value='".$_POST['home']['title']."' where ident =".$result[0]->ident;
    }else{
      $title_sql="insert into ".tbl_prefix."home_data ( `ident` , `owner` , `access` , `name` , `value` ) values ('',".$page_owner.",0,'title','".$_POST['home']['title']."')";
    }
  //Should be ready to sql...
    db_query($body_sql);
    db_query($title_sql);
    $messages[]="Page and Title updated";
}else{
  $messages[] = "Page or Title are empty, not updated";
  //print_r($_POST);
}
?>
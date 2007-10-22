<?
require_once("../../includes.php");
require_once('../users/conf.php');
require_once('../users/function_session_start.php');

if ($_SESSION['userid']) {

  if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
    $postText = trim(file_get_contents('php://input'));
  }
  header('Content-Type: text/xml');
  header("Cache-Control: no-cache, must-revalidate");
//A date in the past
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
 $xml_parser = new Simple_Parser;
 $xml_parser->parse($postText);
 $xml=$xml_parser->data;
 

 if ($xml) {
   echo "<?xml version='1.0' ?>\n".
        "<clubincall_action_results>\n";
   
   $action=$xml['CLUBINCALL_ACTION'][0]['attribs']['ACTION'];
   $target=$xml['CLUBINCALL_ACTION'][0]['attribs']['TARGET'];
   $id=$xml['CLUBINCALL_ACTION'][0]['attribs']['ID'];
   if (isset($xml['CLUBINCALL_ACTION'][0]['child']['CONTROL']) ){
      $controls=array();
      foreach ($xml['CLUBINCALL_ACTION'][0]['child']['CONTROL'] as $control){
        $controls[$control['attribs']['ID']]=$control['attribs']['VALUE'];
      }
   }
   switch ($action){
      case 'debug' : {
        $result=0;
        $msg="what do I debug?";
        break;
      }
        case 'save': {
        $result=array();
        switch ($target){
          case 'number' : {
            if (!is_numeric($id)) $id=NULL;
            check_save_number($controls,$id,$result);
            break;
          }
          case 'dst' :{
            if (!is_numeric($id)) $id=NULL;
            check_save_dst($controls,$id,$result);
            break;
          }
          default : {
            $result['result']=-1;
            $result['msg']='Unknown target :['.$target.']';
            break;
          }
        }
        break;
      }
      case 'remove': {
        switch ($target){
          case 'number' : {
            check_remove_number($id,$result);
            break;
          }
          case 'dst' :{
            check_remove_dst($id,$result);
            break;
          }
          default : {
            $result['result']=-1;
            $result['msg']='Unknown target :['.$target.']';
            break;
          }
        }
        break;
      }
      case 'add' : {
        $result['result']=-1;
        $result['msg']='TBI';
        break;
      }
      default: {
        $result['result']=-1;
        $result['msg']='Unknown action :['.$action.']';
        break;
      }
    }
  

echo "<result>".$result['result']."</result>\n".
     "<msg><![CDATA[".$result['msg']."]]></msg>\n".
     "<debug><![CDATA[".$result['debug']."]]></debug>\n".
     "</clubincall_action_results>\n";
}
}
function check_remove_dst(&$id,&$result){
  $query="DELETE FROM clubincall_dsts where ident=".$id." and owner=".$_SESSION['userid'];
      db_query($query);
      if (db_affected_rows()!=1){
        $result['result']=3;
        $result['msg']='Failed to remove the number';
        $result['debug']=$query;
      }else{
        $result['result']=0;
        $result['msg']="Removed";
      }
}
function check_remove_number(&$id,&$result){
  $query="SELECT IF(ISNULL(gd.used),0,gd.used) as used FROM clubincall_numbers n ".
         "left join ".
         "(select count(*) as used,dst from clubincall_dsts d ".
           "where d.owner=".$_SESSION['userid']." group by dst) gd".
           " on gd.dst=n.ident ".
         "where n.owner=".$_SESSION['userid']." and n.ident=".$id;
  $used=db_query($query);
  if (!$used || count($used)!=1) {
    $result['result']=3;
    $result['msg']='Failed to find the number to remove';
    $result['debug']=$query;
  }else{
    if ($used[0]->used == 0 ){
      $query="DELETE FROM clubincall_numbers where ident=".$id;
      db_query($query);
      if (db_affected_rows()!=1){
        $result['result']=3;
        $result['msg']='Failed to remove the number';
        $result['debug']=$query;
      }else{
        $result['result']=0;
        $result['msg']="Removed";
      }
    }else{
      $result['result']=2;
      $result['msg']="The number is used by rules";
    }
  }
}
function check_save_dst (&$controls,&$id,&$result){
  check_given_sch($controls['wstart_select_'.$id],
        $controls['wend_select_'.$id],
        $controls['hstart_select_'.$id],
        $controls['hend_select_'.$id],
        &$result);
  if ($result['result']!=0) return;
  if (!is_null($id)) { 
    check_given_dst($controls['number_select_'.$id],$result);
    if ($result['result']!=0) return;
  }
  save_dst($controls,$id,&$result);
}

function check_given_sch($wstart,$wend,$hstart,$hend,&$result){
  if ($wstart > $wend ){
    $result['result']=1;
    $result['msg']="Start weekday is after end weekday";
    return;
  }
  if ($hstart>$hend){
    $result['result']=1;
    $result['msg']="Start hour is after end hour";
    return;
  }
  $result['result']=0;
  $result['msg']="Schedule OK";
  return;
}
function check_given_dst($dst,&$result){
  $query='SELECT number from clubincall_numbers where ident='.$dst.
  ' and owner='.$_SESSION['userid'];
  $number=db_query($query);
  if (count($number)!=1){
    $result['result']=1;
    $result['msg']="Could not find corresponding number";
  }else{
    $result['result']=0;
    $result['msg']="Number OK";
  }
}
function check_save_number(&$controls,&$id,&$result){
  check_given_number($controls['number_input_'.$id],&$result);
  if ($result['result']!=0) return;
  save_number($controls,$id,$result);
  if ($result['result']!=0) return;
}

function save_dst(&$controls,&$id,&$result){
  if (is_null($id)){
    $query="insert into clubincall_dsts(owner,wstart,wend,hstart,hend,dst) VALUES".
         "(".$_SESSION['userid'].",".
         $controls['wstart_select_'].",".
         $controls['wend_select_'].",".
         $controls['hstart_select_'].",".
         $controls['hend_select_'].",".
         $controls['numbers_select_'].")";
  }else{
    $query="update clubincall_dsts set ".
           "wstart=".$controls['wstart_select_'.$id].",".
           "wend=".$controls['wend_select_'.$id].",".
           "hstart=".$controls['hstart_select_'.$id].",".
           "hend=".$controls['hend_select_'.$id].",".
           "wend=".$controls['wend_select_'.$id].",".
           "dst=".$controls['numbers_select_'.$id]." ".
           "where ident=".$id." and owner=".$_SESSION['userid'];
  }
  db_query($query);
  $rows=db_affected_rows();
  if ($rows>0){
    $result['result']=0;
    $result['msg']="Affected $rows record".($rows>1?"s":"");
  }elseif ($rows==0 && mysql_errno()==0 ){
    $result['result']=0;
    $result['msg']="No changes done";
  }else{
    $result['result']=3;
    $result['msg']="Failed to save the rule";
    $result['debug']="[".mysql_error()."]\n$query\n";
  }
}
    
function save_number(&$controls,&$id,&$result){
  if (is_null($id)){
  $query="insert into clubincall_numbers(owner,number,description,screened) VALUES".
         "(".$_SESSION['userid'].",".$controls['number_input_'].",".
         "'".$controls['number_desc_input_']."',1)";
  }else{
  $query="update clubincall_numbers set number=".$controls['number_input_'.$id].",".
         "description='".$controls['number_desc_input_'.$id]."' where ident=".$id." ".
         "and owner=".$_SESSION['userid'];
  }
  db_query($query);
  $rows=db_affected_rows();
  if ($rows>0){
    $result['result']=0;
    $result['msg']="Affected $rows record".($rows>1?"s":"");
  }elseif ($rows==0 && mysql_errno()==0 ){
    $result['result']=0;
    $result['msg']="No changes done";
  }else{
    $result['result']=3;
    $result['msg']="Failed to save number";
    $result['debug']="[".mysql_error()."]\n$query\n";
  }
}

function check_given_number($mydst,&$result){
    if (preg_match("/^\d+$/",$mydst)>0){
      $query="select max(length(prefix)) as longest_prefix from clubincall_prefixes";
      $longest_prefix=db_query($query);
      $longest_prefix=$longest_prefix[0]->longest_prefix;
      for ($i=1;$i<=$longest_prefix;$i++){
        $in.=($i==1 ? "(":"").substr($mydst,0,$i).($i==$longest_prefix ? ")" : ",");
      }
      $query="SELECT prefix,avail,type from clubincall_prefixes WHERE prefix IN $in ORDER BY LENGTH(prefix) DESC LIMIT 1";
      $prefix_match=db_query($query);
      $prefix_match=$prefix_match[0];
      if ($prefix_match->avail!=1){
        $result['result']=1;
        $result['msg']="The number starting with ".$prefix_match->prefix.
                      " of type '".$prefix_match->type."' cannot be called. Sorry.";
      }else{
        $result['result']=0;
        $result['msg']="The number starting with ".$prefix_match->prefix.
                      " of type '".$prefix_match->type."' is saved";
      }
    }else{
      $result['result']=-1;
      $result['msg']="Invalid number received: ".$mydst;
    }
}



  class Simple_Parser
  {
      var $parser;
      var $error_code;
      var $error_string;
      var $current_line;
      var $current_column;
      var $data = array();
      var $datas = array();
    
      function parse($data)
      {
          $this->parser = xml_parser_create('UTF-8');
          xml_set_object($this->parser, $this);
          xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
          xml_set_element_handler($this->parser, 'tag_open', 'tag_close');
          xml_set_character_data_handler($this->parser, 'cdata');
          if (!xml_parse($this->parser, $data))
          {
              $this->data = array();
              $this->error_code = xml_get_error_code($this->parser);
              $this->error_string = xml_error_string($this->error_code);
              $this->current_line = xml_get_current_line_number($this->parser);
              $this->current_column = xml_get_current_column_number($this->parser);
          }
          else
          {
              $this->data = $this->data['child'];
          }
          xml_parser_free($this->parser);
      }
  
      function tag_open($parser, $tag, $attribs)
      {
          $this->data['child'][$tag][] = array('data' => '', 'attribs' => $attribs, 'child' => array());
          $this->datas[] =& $this->data;
          $this->data =& $this->data['child'][$tag][count($this->data['child'][$tag])-1];
      }
  
      function cdata($parser, $cdata)
      {
          $this->data['data'] .= $cdata;
      }
  
      function tag_close($parser, $tag)
      {
          $this->data =& $this->datas[count($this->datas)-1];
          array_pop($this->datas);
      }
  
}

/*<?xml version="1.0"?>
<clubincall_action action="save" target="number" id="save">
<control id="number_input_1" value="number_input_1" /><control id="number_desc_input_1" value="number_desc_input_1" /></clubincall_action>*/
?>
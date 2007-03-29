<?
global $page_owner;
$url = url;
		
if ($page_owner != $_SESSION['userid']) {
	$currentvideo = db_query("select
        videos.filename, ".tbl_prefix."users.video
        from ".tbl_prefix."users left join
        ".tbl_prefix."videos videos on
        videos.ident = ".tbl_prefix."users.video
        where ".tbl_prefix."users.ident =
        $page_owner");
	$currentvideo = $currentvideo[0]->filename;
} else {
			$currentvideo = $_SESSION['video'];
}
if (isset($currentvideo)){
  $run_result .='<table width="95%" class="profiletable" align="center" style="margin-bottom: 3px">
                 <tr>
                 <td width="20%" class="fieldname">
                 My video
                 </td>
                 <td width="80%">
                 <center>
              <embed
  src="'.url.'_videos/data/'.$currentvideo.'"
  type="video/x-ms-wmv" controller="false" autoplay="false" loop="false"
  height="280" width="320"/></center>';
  $run_result.="</td></tr></table>";
}else{
  $run_result .='';/*'<div id="currentvideo">
              <img
              src="'.url.'_videos/data/default.jpg"  
              height="280" width="320"/></div>';*/
}

?>
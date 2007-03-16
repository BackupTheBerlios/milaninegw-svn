<?
if (isset($_REQUEST['ytvid'])){
  $ytp=file_get_contents("http://youtube.com/watch?v=".$_REQUEST['ytvid']);
//    echo '<pre>'.$ytp.'</pre>';
  preg_match('%/player2.swf\?video_id=(.+)", "m%',$ytp,$video_url);
  //echo print_r($video_url,1);
  $video_url="http://youtube.com/get_video?video_id=".$video_url[1];
  header("HTTP/1.1 301 Moved Permanently");
  header('Location: '.$video_url);

}else{
  $video_url="14_1173574132.wmv.flv";
}
?>
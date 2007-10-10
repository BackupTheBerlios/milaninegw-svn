<?
global $page_owner;
global $selected_video;
global $selected_video_id;
$url = url;
$player=file(player_skin_path."/player.html");
if (count($player)>1){
  $videos_list=run("videos:list:personal");
  $player=preg_replace(
              array("/\{\{PATH\}\}/","/\{\{VIDEOS_LIST\}\}/","/\{\{SELECTED_VIDEO\}\}/"),
              array(url."_videos/players/".basename(player_skin_path),$videos_list,$selected_video),
              $player);
  $run_result=join("\n",$player);
}else{
  $run_result="Empty player or no player configured";
}

?>
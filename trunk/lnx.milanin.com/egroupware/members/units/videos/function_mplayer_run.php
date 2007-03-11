<?
$videofile=$parameter;
$mplayer="/usr/local/mplayer/bin/mplayer -vf scale=128:128 -vo jpeg:outdir=".dirname($videofile).
         " -frames 1  -ao null ".$videofile;
exec($mplayer.' 2>&1',$output,$retval);
if ($retval != 0){
  $return_array['result']=1;
}else{
  foreach ($output as $line){
    if (preg_match ( '/^Selected.+\((.+)\)$/', $line,$matches)>0) {
      $line=$matches[1];
      $return_array['description'][]=$line;
//       $messages[]=print_r($matches,1);
    }elseif (preg_match ( '/^VO:.+=>\s(\d+x\d+\s.+)$/', $line)){
      $line=$matches[1];
      $return_array['description'][]=$line;
//       $messages[]=print_r($matches,1);
    }
    
  }
  if (!rename(dirname($videofile)."/00000001.jpg",
      dirname($videofile)."/".basename($videofile).".jpg")){
      $return_array['result']=2;
  }else{
    $return_array['result']=0;
  }
  }
  $return_array['debug']=$output;
  $run_result=$return_array;

?> 

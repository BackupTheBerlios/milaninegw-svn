<?
global $page_owner;
global $current_dst;
if (!isset($current_dst) || count($current_dst<1)){
  run('clubincall:init');
}

$run_result='<pre>'.join("\n",$current_dst).'</pre>';

?>

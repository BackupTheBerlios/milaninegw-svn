<?php
	/**************************************************************************\
	* eGroupWare SiteMgr - Web Content Management                              *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.module_vod.inc.php,v 1.3 2004/02/10 14:56:33 ralfbecker Exp $ */

class module_vod extends Module 
{
	function module_vod()
	{
		$this->arguments = array(
			'path' => array(
				'type' => 'textfield', 
				'label' => lang('where to look for the files')
			),
                        'player' => array(
				'type' => 'textfield', 
				'label' => lang('path to the player template')
                        ),
                        'list_item' => array(
				'type' => 'textfield', 
				'label' => lang('path to the videos list item template')
			),
                        'num' => array(
				'type' => 'textfield', 
				'label' => lang('number of videos in the list')
			),
                        'url' => array(
				'type' => 'textfield', 
				'label' => lang('base url')
			),
                        'mask' => array(
				'type' => 'textfield', 
				'label' => lang('videos filename pattern')
			)
		);
		$this->post = array('start' => array('type' => 'hidden'));
		$this->session = array('start');
		$this->title = lang('VOD browser');
		$this->description = lang('VOD browser for MilanIN TV');
                $this->data=array();
	}
        function get_videos_list($path,$mask,$start,$count){
          $videos=array_flip(array_slice( glob($path."/".$mask,GLOB_BRACE+GLOB_NOSORT),
                                $start,$count));
          foreach (array_keys($videos) as $v){
              if (is_readable($v) && 
                  is_file($v) &&
                  (filesize($v) >0) &&
                  is_readable($v.".meta") && 
                  is_file($v.".meta") &&
                  (filesize($v.".meta") >0) ){
                $meta=file($v.".meta");
                foreach ($meta as $s){
                  if (preg_match ( '/^description\s+(.+)$/',$s,$matches ) ){
                    $description=$matches[1];
                  }elseif (preg_match ( "/^author\s+(.+)$/",$s,$matches ) ){
                    $author=$matches[1];
                  }elseif (preg_match ( "/^date\s+(.+)$/",$s,$matches ) ){
                    $date=$matches[1];
                  }elseif (preg_match ( "/^name\s+(.+)$/",$s,$matches ) ){
                    $name=$matches[1];
                  }
                }
                $videos[$v]=array(  
                'filename'=>basename($v),
                'filesize'=>filesize($v),
                'description'=>$description,
                'author'=>$author,
                'date'=>$date,
                'name'=>$name,
                );
              }
            }
         return $videos;
        }
        function render_controls(&$arguments,&$data){
          //if (!$data['start']){ $data['start']=0;}
          $total=count(glob($arguments['path']."/".$arguments['mask'],GLOB_BRACE+GLOB_NOSORT));
          $rest=$total-($data['start']+$arguments['num']);
          //$controls="controls renderer got:[".print_r($data,1)."] [".$rest."]\n";
          $controls.="<table width=\"100%\"><tr><td align=\"left\">";
          if ($data['start']>0){
            $controls.='<form name="form_prev" method="post" id="form_prev">
                       <input name="start" type="hidden" value="'.
                       ($data['start']-$arguments['num']).
                       '" /><input name="back" type="button" value="<<"
                       onclick="document.getElementById(\'form_prev\').submit()" />
                       </form>';
          }
          $controls.="</td><td align=\"right\">";
          if ($rest>0){
            $controls.='<form name="form_next" method="post" id="form_next">
                       <input name="start" type="hidden" value="'.
                       ($data['start']+$arguments['num']).
                       '" />
                       <input name="forward" type="button" value=">>"
                       onclick="document.getElementById(\'form_next\').submit()" />
                       </form>';
          }
         $controls.="</td></tr></table>";
         return $controls;
        }
        function template_list(&$arguments){
          $tpl=join("\n",file($arguments['path']."/".
                              $arguments['list_item']."/".
                              "list_item.html"));
          $id=0;
          $list=$this->get_videos_list($arguments['path'],
                                       $arguments['mask'],
                                       $_POST['start'],
                                       $arguments['num']
                                       );
          foreach ($list as $i){
            $ttpl=$tpl;
            foreach (array_keys($i) as $k){
              $ttpl=preg_replace('/\{\{'.$k.'\}\}/',$i[$k],$ttpl);
              $ttpl=preg_replace('/\{\{ID\}\}/',$id,$ttpl);
              $ttpl=preg_replace('/\{\{url\}\}/',$arguments['url'],$ttpl);
            }
            $id++;
            $retval.=$ttpl;
          }
          return '<ul>'.$retval.'</ul>';
        }
        function template_player(&$arguments,&$data){
          $player=file($arguments['path']."/".$arguments['player']."/player.html");
          if (count($player)>1){
            $videos_list=$this->template_list($arguments).$this->render_controls($arguments,$_POST);
            $player=preg_replace(
                        array("/\{\{PATH\}\}/","/\{\{VIDEOS_LIST\}\}/"
                        #,"/\{\{SELECTED_VIDEO\}\}/
                             ),
                        array($arguments['url']."/".$arguments['player'],$videos_list,$selected_video),
                        $player);
            $run_result=join("\n",$player);
          }else{
            $run_result="Empty player or no player configured";
          }
          return $run_result;
        }
        function validate(&$data){
          $this->data=$data;
          return true;
        }
	function get_content(&$arguments,$properties) 
	{
          return print_r($data,1).$this->template_player($arguments,$data);
		/*return $this->template_list($arguments['path']."/".
                                            $arguments['list_item']."/".
                                            "list_item.html",
                                            $this->get_videos_list(
                                                                    $arguments['path'],
                                                                    $arguments['mask'],
                                                                    $data['start'],
                                                                    $arguments['num']
                                                                    )
                                            );*/
                
		/*return lang('vod') . ' ' . $arguments['name'] . '<br><form method="post">' . 
			$this->build_post_element('name',lang('Enter a name')) .
			'</form>';*/
	}
	
}

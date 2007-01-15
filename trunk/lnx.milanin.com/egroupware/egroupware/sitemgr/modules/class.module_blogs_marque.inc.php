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

	/* $Id: class.module_hello.inc.php,v 1.3 2004/02/10 14:56:33 ralfbecker Exp $ */

class module_blogs_marque extends Module 
{
	function module_blogs_marque()
	{
		$this->arguments = array(
			'count' => array(
				'type' => 'textfield', 
				'label' => lang('Number of entries to show')
			),
                        'posts_table' => array(
				'type' => 'textfield', 
				'label' => lang('Posts table')
			),
                        'users_table' => array(
				'type' => 'textfield', 
				'label' => lang('Users table')
			)
		);
		//$this->post = array('' => array('type' => 'textfield'));
		//$this->session = array('name');
		$this->title = lang('Blog entries');
		$this->description = lang('A module to show a marque with last blog entries from elgg');
                $this->db = $GLOBALS['phpgw']->db;
	}

	function get_content(&$arguments,$properties) 
	{
                $this->db->query("select posts.*, users.username , users.name from `".$arguments['posts_table']."` posts left join `".$arguments['users_table']."` users on posts.owner=users.ident where access = 'PUBLIC' order by posted desc",_LINE_,_FILE_,0, $arguments['count']);
		while($this->db->next_record()){
                  $row = $this->db->row();
                  $ret_val=$ret_val.'<a href="/members/'.$row['username'].'/weblog/'.$row['ident'].'.html">'.
                                stripslashes($row['title']).
                                '</a> :: ';
                }
/*                return '<script type="text/javascript">var marqueecontent=\'<nobr>'.$ret_val.'</nobr>\'
                           </script>
                          <script type="text/javascript" src="/egroupware/jscripts/marq.js" >
                          </script>
                          <script type="text/javascript">
                            window.onload=initmarquee(marqueecontent)
                          </script>';*/
                  return '<script language="JavaScript1.2">

/*
Cross browser Marquee script- © Dynamic Drive (www.dynamicdrive.com)
For full source code, 100s more DHTML scripts, and Terms Of Use, visit http://www.dynamicdrive.com
Credit MUST stay intact
*/

//Specify the marquees width (in pixels)
var marqueewidth="700px"
//Specify the marquees height
var marqueeheight="25px"
//Specify the marquees marquee speed (larger is faster 1-10)
var marqueespeed=1
//configure background color:
var marqueebgcolor="#D0D9DB"
//Pause marquee onMousever (0=no. 1=yes)?
var pauseit=1

//Specify the marquees content (dont delete <nobr> tag)
//Keep all content on ONE line, and backslash any single quotations (ie: that\'s great):

var marqueecontent=\'<nobr>'.$ret_val.'</nobr>\'


////NO NEED TO EDIT BELOW THIS LINE////////////
marqueespeed=(document.all)? marqueespeed : Math.max(1, marqueespeed-1) //slow speed down by 1 for NS
var copyspeed=marqueespeed
var pausespeed=(pauseit==0)? copyspeed: 0
var iedom=document.all||document.getElementById
if (iedom)
document.write(\'<span id="temp" style="visibility:hidden;position:absolute;top:-100px;left:-9000px">\'+marqueecontent+\'</span>\')
var actualwidth=\'\'
var cross_marquee, ns_marquee

function populate(){
if (iedom){
cross_marquee=document.getElementById? document.getElementById("iemarquee") : document.all.iemarquee
cross_marquee.style.left=parseInt(marqueewidth)+8+"px"
cross_marquee.innerHTML=marqueecontent
actualwidth=document.all? temp.offsetWidth : document.getElementById("temp").offsetWidth
}
else if (document.layers){
ns_marquee=document.ns_marquee.document.ns_marquee2
ns_marquee.left=parseInt(marqueewidth)+8
ns_marquee.document.write(marqueecontent)
ns_marquee.document.close()
actualwidth=ns_marquee.document.width
}
lefttime=setInterval("scrollmarquee()",20)
}
window.onload=populate

function scrollmarquee(){
if (iedom){
if (parseInt(cross_marquee.style.left)>(actualwidth*(-1)+8))
cross_marquee.style.left=parseInt(cross_marquee.style.left)-copyspeed+"px"
else
cross_marquee.style.left=parseInt(marqueewidth)+8+"px"

}
else if (document.layers){
if (ns_marquee.left>(actualwidth*(-1)+8))
ns_marquee.left-=copyspeed
else
ns_marquee.left=parseInt(marqueewidth)+8
}
}
//;background-color:\'+marqueebgcolor+\'" 
if (iedom||document.layers){
with (document){
document.write(\'<table border="0" cellspacing="0" cellpadding="0"><td valign="middle">\')
if (iedom){
write(\'<div style="position:relative;width:\'+marqueewidth+\';height:\'+marqueeheight+\';overflow:hidden">\')
write(\'<div style="position:absolute;width:\'+marqueewidth+\';height:\'+marqueeheight+\'"   onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=marqueespeed">\')
write(\'<div id="iemarquee" style="position:absolute;left:0px;top:0px"></div>\')
write(\'</div></div>\')
}
else if (document.layers){
write(\'<ilayer width=\'+marqueewidth+\' height=\'+marqueeheight+\' name="ns_marquee" bgColor=\'+marqueebgcolor+\'>\')
write(\'<layer name="ns_marquee2" left=0 top=0 onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=marqueespeed"></layer>\')
write(\'</ilayer>\')
}
document.write(\'</td></table>\')
}
}
</script>';
	}
}

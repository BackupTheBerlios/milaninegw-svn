<?php
	/**************************************************************************\
	* eGroupWare                                                               *
	* http://www.egroupware.org                                                *
	* The file written by Joseph Engo <jengo@phpgroupware.org>                 *
	* This file modified by Greg Haygood <shrykedude@bellsouth.net>            *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: home.php,v 1.46.2.1 2004/09/11 23:57:02 alpeb Exp $ */

	$phpgw_info = array();
	if (!is_file('header.inc.php'))
	{
		Header('Location: setup/index.php');
		exit;
	}

	$GLOBALS['sessionid'] = @$_GET['sessionid'] ? $_GET['sessionid'] : @$_COOKIE['sessionid'];
	if (!isset($GLOBALS['sessionid']) || !$GLOBALS['sessionid'])
	{
		Header('Location: login.php');
		exit;
	}

	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader'                => True,
		'nonavbar'                => True,
		'currentapp'              => 'home',
		'enable_network_class'    => True,
		'enable_contacts_class'   => True,
		'enable_nextmatchs_class' => True
	);
	include('header.inc.php');
	$GLOBALS['phpgw_info']['flags']['app_header']=lang('home');

	// Commented by alpeb: The following prevented anonymous users to get a home page. Perhaps it was done with anonymous users such as the ones
	// used by  wiki and sitemgr in mind. However, if you mark a normal user as anonymous just to avoid being shown in sessions and access log (like you would for an admin that doesn't want to be noticed), the user won't be able to login anymore. That's why I commented the code.
	/*if ($GLOBALS['phpgw']->session->session_flags == 'A')
	{
		if ($_SERVER['HTTP_REFERER'] && strstr($_SERVER['HTTP_REFERER'],'home.php') === False)
		{
			$GLOBALS['phpgw']->redirect($_SERVER['HTTP_REFERER']);
		}
		else
		{
			// redirect to the login-page, better then giving an empty page
			$GLOBALS['phpgw']->redirect('login.php');
		}
		exit;
	}*/

	if ($GLOBALS['phpgw_info']['server']['force_default_app'] && $GLOBALS['phpgw_info']['server']['force_default_app'] != 'user_choice')
	{
		$GLOBALS['phpgw_info']['user']['preferences']['common']['default_app'] = $GLOBALS['phpgw_info']['server']['force_default_app'];
	}

	if (($GLOBALS['phpgw_info']['user']['preferences']['common']['useframes'] &&
		$GLOBALS['phpgw_info']['server']['useframes'] == 'allowed') ||
		($GLOBALS['phpgw_info']['server']['useframes'] == 'always'))
		{
			if ($_GET['cd'] == 'yes')
			{
				if (! $navbarframe && ! $framebody)
				{
					$tpl = new Template(PHPGW_TEMPLATE_DIR);
					$tpl->set_file(array(
						'frames'       => 'frames.tpl',
						'frame_body'   => 'frames_body.tpl',
						'frame_navbar' => 'frames_navbar.tpl'
					));
					$tpl->set_var('navbar_link',$GLOBALS['phpgw']->link('index.php','navbarframe=True&cd=yes'));
					if ($GLOBALS['forward'])
					{
						$tpl->set_var('body_link',$GLOBALS['phpgw']->link($GLOBALS['forward']));
					}
					else
					{
						$tpl->set_var('body_link',$GLOBALS['phpgw']->link('index.php','framebody=True&cd=yes'));
					}

					if ($GLOBALS['phpgw_info']['user']['preferences']['common']['frame_navbar_location'] == 'bottom')
					{
						$tpl->set_var('frame_size','*,60');
						$tpl->parse('frames_','frame_body',True);
						$tpl->parse('frames_','frame_navbar',True);
					}
					else
					{
						$tpl->set_var('frame_size','60,*');
						$tpl->parse('frames_','frame_navbar',True);
						$tpl->parse('frames_','frame_body',True);
					}
					$tpl->pparse('out','frames');
				}
				if ($navbarframe)
				{
					$GLOBALS['phpgw']->common->phpgw_header();
					echo parse_navbar();
				}
			}
		}
		elseif ($_GET['cd']=='yes' && $GLOBALS['phpgw_info']['user']['preferences']['common']['default_app'] &&
			$GLOBALS['phpgw_info']['user']['apps'][$GLOBALS['phpgw_info']['user']['preferences']['common']['default_app']])
		{
			$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/' . $GLOBALS['phpgw_info']['user']['preferences']['common']['default_app'] . '/' . 'index.php'));
		}
		else
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}

		$GLOBALS['phpgw']->translation->add_app('mainscreen');
		if (lang('mainscreen_message') != 'mainscreen_message*')
		{
			echo '<center>' . stripslashes(lang('mainscreen_message')) . '</center>';
		}
                echo blog_marq(array(
			'count' => 10,
                        'posts_table' => 'members_weblog_posts',
                        'users_table' => 'members_users'));

		if ((isset($GLOBALS['phpgw_info']['user']['apps']['admin']) &&
			$GLOBALS['phpgw_info']['user']['apps']['admin']) &&
			(isset($GLOBALS['phpgw_info']['server']['checkfornewversion']) &&
			$GLOBALS['phpgw_info']['server']['checkfornewversion']))
		{
			$GLOBALS['phpgw']->network->set_addcrlf(False);
			$lines = $GLOBALS['phpgw']->network->gethttpsocketfile('http://www.egroupware.org/currentversion');
			for($i=0; $i<count($lines); $i++)
			{
				if(strstr($lines[$i],'currentversion'))
				{
					$line_found = explode(':',chop($lines[$i]));
				}
			}
			if($GLOBALS['phpgw']->common->cmp_version_long($GLOBALS['phpgw_info']['server']['versions']['phpgwapi'],$line_found[1]))
			{
				echo '<p>There is a new version of eGroupWare available. <a href="'
					. 'http://www.egroupware.org">http://www.egroupware.org</a></p>';
			}

			$_found = False;
			$GLOBALS['phpgw']->db->query("select app_name,app_version from phpgw_applications",__LINE__,__FILE__);
			while($GLOBALS['phpgw']->db->next_record())
			{
				$_db_version  = $GLOBALS['phpgw']->db->f('app_version');
				$_app_name    = $GLOBALS['phpgw']->db->f('app_name');
				$_app_dir = $GLOBALS['phpgw']->common->get_app_dir($_app_name);
				$_versionfile = $_app_dir . '/setup/setup.inc.php';
				if($_app_dir && file_exists($_versionfile))
				{
					include($_versionfile);
					$_file_version = $setup_info[$_app_name]['version'];
					$_app_title    = $GLOBALS['phpgw_info']['apps'][$_app_name]['title'];
					unset($setup_info);

					if($GLOBALS['phpgw']->common->cmp_version_long($_db_version,$_file_version))
					{
						$_found = True;
						$_app_string .= '<br>' . $_app_title;
					}
					unset($_file_version);
					unset($_app_title);
				}
				unset($_db_version);
				unset($_versionfile);
			}
			if($_found)
			{
				echo '<br>' . lang('The following applications require upgrades') . ':' . "\n";
				echo $_app_string . "\n";
				echo '<br><a href="setup/" target="_blank">' . lang('Please run setup to become current') . '.' . "</a>\n";
				unset($_app_string);
			}
		}

	if (isset($GLOBALS['phpgw_info']['user']['apps']['notifywindow']) &&
		$GLOBALS['phpgw_info']['user']['apps']['notifywindow'])
	{
?>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
	var NotifyWindow;

	function opennotifywindow()
	{
		if (NotifyWindow)
		{
			if (NotifyWindow.closed)
			{
				NotifyWindow.stop;
				NotifyWindow.close;
			}
		}
		NotifyWindow = window.open("<?php echo $GLOBALS['phpgw']->link('/notify.php')?>", "NotifyWindow", "width=300,height=35,location=no,menubar=no,directories=no,toolbar=no,scrollbars=yes,resizable=yes,status=yes");
		if (NotifyWindow.opener == null)
		{
			NotifyWindow.opener = window;
		}
	}
</SCRIPT>

<?php
		echo '<a href="javascript:opennotifywindow()">' . lang('Open notify window') . '</a>';
	}

	/* This initializes the users portal_order preference if it does not exist. */
	if(!is_array($GLOBALS['phpgw_info']['user']['preferences']['portal_order']) && $GLOBALS['phpgw_info']['apps'])
	{
		$GLOBALS['phpgw']->preferences->delete('portal_order');
		@reset($GLOBALS['phpgw_info']['apps']);
		$order = 0;
		while (list(,$p) = each($GLOBALS['phpgw_info']['apps']))
		{
			if($GLOBALS['phpgw_info']['user']['apps'][$p['name']])
			{
				$GLOBALS['phpgw']->preferences->add('portal_order',$order++,$p['id']);
			}
		}
		$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->save_repository();
	}

	if(is_array($GLOBALS['phpgw_info']['user']['preferences']['portal_order']))
	{
		$app_check = Array();
		@ksort($GLOBALS['phpgw_info']['user']['preferences']['portal_order']);
		while(list($order,$app) = each($GLOBALS['phpgw_info']['user']['preferences']['portal_order']))
		{
			if(!isset($app_check[(int)$app]) || !$app_check[(int)$app])
			{
				$app_check[(int)$app] = True;
				$sorted_apps[] = $GLOBALS['phpgw']->applications->id2name((int)$app);
			}
		}
	}
	else
	{
		$sorted_apps = Array(
			'calendar',
			'email',
			'infolog',
			'news_admin'
		);
	}

	// Now add the rest of the user's apps, to make sure we pick up any additions to the home display
	@reset($GLOBALS['phpgw_info']['user']['apps']);
	while(list(,$p) = each($GLOBALS['phpgw_info']['user']['apps']))
	{
		$sorted_apps[] = $p['name'];
	}
	//$GLOBALS['phpgw']->hooks->process('home',$sorted_apps);

	function migrate_pref($appname,$var_old,$var_new,$type='user')
	{
		if(empty($appname) || empty($var_old) || empty($var_new))
		{
			return false;
		}
		$allowedtypes = array('user','default','forced');
		if($type=='all')
		{
			$types = $allowedtypes;
		}
		elseif(in_array($type,$allowedtypes)) 
		{
			$types[] = $type;
		}
		else
		{
			return false;
		}
		$result = false;
		foreach($types as $_type)
		{
			if(isset($GLOBALS['phpgw']->preferences->$_type[$appname][$var_old]))
			{
				$GLOBALS['phpgw']->preferences->$_type[$appname][$var_new] =
					$GLOBALS['phpgw']->preferences->$_type[$appname][$var_old];
				$result = true;
				$GLOBALS['phpgw_info']['user']['preferences'] =
					$GLOBALS['phpgw']->preferences->save_repository(false,$_type);
			}
		}
		return $result;
	}
	function blog_marq($arguments) 
	{
                echo "<!-- Starting marquee-->";
                $this->db = $GLOBALS['phpgw']->db;
                $this->db->query("select posts.*, users.username , users.name from `".$arguments['posts_table']."` posts left join `".$arguments['users_table']."` users on posts.owner=users.ident where access = 'PUBLIC' order by posted desc",_LINE_,_FILE_,0, $arguments['count']);
                echo "<!-- queried -->";
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
//                           </script>';*/
                  echo "<!-- Finishing marquee-->";
                  return '<br/><center><script language="JavaScript1.2">

/*
Cross browser Marquee script- © Dynamic Drive (www.dynamicdrive.com)
For full source code, 100s more DHTML scripts, and Terms Of Use, visit http://www.dynamicdrive.com
Credit MUST stay intact
*/

//Specify the marquees width (in pixels)
var marqueewidth="800px"
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
</script></center>';
  }
	$portal_oldvarnames = array('mainscreen_showevents', 'homeShowEvents','homeShowLatest','mainscreen_showmail','mainscreen_showbirthdays','mainscreen_show_new_updated');
	$migrate_oldvarnames = false;
	if($migrate_oldvarnames)
	{
		$_apps = $GLOBALS['phpgw_info']['user']['apps'];
		@reset($_apps);
		foreach($_apps as $_appname)
		{
			@reset($portal_oldvarnames);
			foreach($portal_oldvarnames as $varname)
			{
				//echo "Want to migrate '$appname' from '$varname' to 'homepage_display'.<br>";
				//migrate_pref($appname,$varname,'homepage_display','all');
			}
		}
	}

	$neworder = array();
	$done = array();
	// Display elements, within appropriate table cells
	print '<table border="0" cellpadding="5" cellspacing="0" width="100%">';
	$tropen=0;
	$tdopen=0;
	$lastd = 0;
	$numcols = 2;
	$curcol = 1;
	@reset($sorted_apps);
	foreach($sorted_apps as $appname)
	{
		if((int)$done[$appname] == 1 || empty($appname))
		{
			continue;
		}
		$varnames = $portal_oldvarnames;
		$varnames[] = 'homepage_display';
		$thisd = 0;
		foreach($varnames as $varcheck)
		{
			//echo "$appname:$varcheck=".$GLOBALS['phpgw_info']['user']['preferences'][$appname][$varcheck]."<br>";
			if($GLOBALS['phpgw_info']['user']['preferences'][$appname][$varcheck]=='True')
			{
				$thisd = 1;
				break;
			}
			else 
			{
				$_thisd = (int)$GLOBALS['phpgw_info']['user']['preferences'][$appname][$varcheck];
				if($_thisd>0)
				{
					//echo "Found $appname=$_thisd through $varcheck<br>";
					$thisd = $_thisd;
					break;
				}
			}
		}
		//echo "$appname: $thisd<br>";
		if($thisd>0)
		{
			if((($curcol++>$numcols) || ($thisd+$lastd==3)) && $tropen==1)
			{
				print '</tr>';
				$tropen = 0;
				//$curcol = 1;
			}
			if(!$tropen)
			{
				print '<tr>';
				$tropen=1;
			}
			$tdwidth = ($thisd==2)?'50':'100';
			$colspan = ($thisd==2)?'1':'2';
			print '<td valign="top" colspan="'.$colspan.'" width="'.$tdwidth.'%">';
			$result = $GLOBALS['phpgw']->hooks->single('home',$appname);
			print '</td>';
			if(($thisd!=2 || ($thisd==2&&$lastd==2)) && $tropen)
			{
				print '</tr>';
				$tropen = 0;
				$lastd = 0;
				$curcol = 1;
			} 
			else 
			{
				$lastd = $thisd;
			}
			$neworder[] = $appname;
		}
		$done[$appname] = 1;
	}
	print '</table>';

	// Update stored value of order
	//_debug_array($neworder);
	if(count($neworder)>0)//$GLOBALS['portal_order'])
	{
		$GLOBALS['phpgw']->preferences->delete('portal_order');
		@reset($neworder);
		while(list($app_order,$app_name) = each($neworder))
		{
			$app_id = $GLOBALS['phpgw']->applications->name2id($app_name);
			//echo "neworder: $app_order=$app_id:$app_name<br>";
			$GLOBALS['phpgw']->preferences->add('portal_order',$app_order,$app_id);
		}
		$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->save_repository();
	}
	//_debug_array($GLOBALS['phpgw_info']['user']['preferences']);

	//$phpgw->common->debug_phpgw_info();
	//$phpgw->common->debug_list_core_functions();
	$GLOBALS['phpgw']->common->phpgw_footer();
?>

<?php
	/**************************************************************************\
	* eGroupWare - Setup                                                       *
	* http://www.egroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: default_records.inc.php,v 1.9.2.1 2004/11/06 12:15:57 ralfbecker Exp $ */

	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type,event_extra) values('hours limit','percent',90)",__LINE__,__FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type,event_extra) values('budget limit','percent',90)",__LINE__,__FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type,event_extra) values('project date due','limits',7)",__LINE__,__FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type,event_extra) values('milestone date due','limits',7)",__LINE__,__FILE__);

	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type) values('assignment to project','assignment')",__LINE__,__FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type) values('assignment to role','assignment')",__LINE__,__FILE__);

	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type) values('project dependencies','dependencies')",__LINE__,__FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT into phpgw_p_events (event_name,event_type) values('changes of project data','dependencies')",__LINE__,__FILE__);
	
	// give Admins group rights for projects and add it to the project's administrators
	$admingroup = $GLOBALS['phpgw_setup']->add_account('Admins','Admin','Group',False,False);
	$GLOBALS['phpgw_setup']->add_acl('projects','run',$admingroup);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_p_projectmembers (project_id,account_id,type) values (0,$admingroup,'ag')",__LINE__,__FILE__);
	
	// setting some reasonable defaults
	$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_config WHERE config_app='projects'",__LINE__,__FILE__);
	foreach(array(
		'hwday' => 8,
		'accounting' => 'own',
		'activity_bill' => 'h',
		'dateprevious' => 'no',
	) as $name => $value)
	{
		$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app,config_name,config_value) VALUES ('projects','$name','$value')",__LINE__,__FILE__);
	}

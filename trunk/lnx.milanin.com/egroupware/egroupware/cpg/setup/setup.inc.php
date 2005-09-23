<?php
  /**************************************************************************\
  * eGroupWare - Coppermine Setup                                            *
  * http://www.eGroupWare.org                                                *
  * --------------------------------------------                             *
  * This program is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU General Public License as published by the    *
  * Free Software Foundation; either version 2 of the License, or (at your   *
  * option) any later version.                                               *
  \**************************************************************************/

  /* $Id: class.db_tools.inc.php,v 1.27 2004/08/15 20:58:12 ralfbecker Exp $ */
  
	$setup_info['cpg']['name']      = 'cpg';
	$setup_info['cpg']['title']     = 'Coppermine Photo Gallery';
	$setup_info['cpg']['version']   = '1.3.2.000';
	$setup_info['cpg']['app_order'] = 4;		// at the beginning in the development time
	$setup_info['cpg']['enable']    = 1;

        /* The hooks this app includes, needed for hooks registration */
        $setup_info['cpg']['hooks'][] = 'sidebox_menu'; 

	$setup_info['cpg']['tables'] = array('phpgw_cpg_albums','phpgw_cpg_banned','phpgw_cpg_categories','phpgw_cpg_comments','phpgw_cpg_config','phpgw_cpg_ecards','phpgw_cpg_exif','phpgw_cpg_filetypes','phpgw_cpg_pictures','phpgw_cpg_temp_data','phpgw_cpg_usergroups','phpgw_cpg_users','phpgw_cpg_votes');
	
	$setup_info['cpg']['only_db'] = array('mysql');	// keep setup from trying to install on other db's

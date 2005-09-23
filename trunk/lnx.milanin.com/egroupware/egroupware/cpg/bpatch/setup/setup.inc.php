<?php
	$setup_info['cpg']['name']      = 'cpg';
	$setup_info['cpg']['title']     = 'Coppermine Photo Gallery';
	$setup_info['cpg']['version']   = '0.1.0.001';
	$setup_info['cpg']['app_order'] = 4;		// at the beginning in the development time
	$setup_info['cpg']['enable']    = 1;

        /* The hooks this app includes, needed for hooks registration */
        $setup_info['cpg']['hooks'][] = 'sidebox_menu'; 
?>

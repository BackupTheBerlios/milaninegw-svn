<?php
   /*
   JiNN - Jinn is Not Nuke, a mutli-user, multi-site CMS for eGroupWare
   Copyright (C)2004 Pim Snel <pim@lingewoud.nl>

   eGroupWare - http://www.egroupware.org

   This file is part of JiNN

   JiNN is free software; you can redistribute it and/or modify it under
   the terms of the GNU General Public License as published by the Free
   Software Foundation; either version 2 of the License, or (at your 
   option) any later version.

   JiNN is distributed in the hope that it will be useful,but WITHOUT ANY
   WARRANTY; without even the implied warranty of MERCHANTABILITY or 
   FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
   for more details.

   You should have received a copy of the GNU General Public License 
   along with JiNN; if not, write to the Free Software Foundation, Inc.,
   59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
   */

   /* 
   plugin.vorder.php contains the standard image-upload plugin for 
   JiNN number off standardly available 
   plugins for JiNN. 
   */

   $this->plugins['vorder']['name']				= 'vorder';
   $this->plugins['vorder']['title']			= 'Visual Ordering plugin';
   $this->plugins['vorder']['author']			= 'Pim Snel';
   $this->plugins['vorder']['version']			= '0.1.0';
   $this->plugins['vorder']['enable']			= 1;

   $this->plugins['vorder']['description']		= '
   Plugin that lets the user order their record in a visual way.
   ';

   $this->plugins['vorder']['db_field_hooks']	= array
   (
	  'int',
	  'smallint',
	  'tinyint'
   );

   function plg_fi_vorder($field_name,$value,$config,$attr_arr)
   {	

	  return $value. ' <input type="hidden" name="'.$field_name.'">';
   }

	function plg_sf_vorder($field_name,$HTTP_POST_VARS,$HTTP_POST_FILES,$config)
	{
	   global $local_bo;
	   $local_bo->so->site_db_connection($local_bo->site_id);

	   $table = $local_bo->site_object[table_name];

	   $field_name= substr($field_name,3);

	   $SQL="SELECT * FROM `$table` ORDER BY `$field_name` DESC LIMIT 1";
	   $local_bo->so->site_db->query($SQL,__LINE__,__FILE__);
	   $local_bo->so->site_db->next_record();
	   $newval = $local_bo->so->site_db->f($field_name)+1;

	   return $newval;
	}

   function plg_bv_vorder($value,$config,$where_val_enc,$fieldname)
   {
	  global $local_bo;

	  $baselink=$GLOBALS[phpgw]->link('/index.php','menuaction=jinn.bouser.get_plugin_afa');

	  // FIXME clean up
	  //up-attributes
	  $attributes_up['move']='up';
	  $attributes_up['myname']=$fieldname;
	  $attributes_up['myval']=$value;

	  //down-attributes
	  $attributes_down['move']='down';
	  $attributes_down['myname']=$fieldname;
	  $attributes_down['myval']=$value;

	  //thirst-attributes
	  $attributes_thirst['move']='thirst';
	  $attributes_thirst['myname']=$fieldname;
	  $attributes_thirst['myval']=$value;

	  //last-attributes
	  $attributes_last['move']='last';
	  $attributes_last['myname']=$fieldname;
	  $attributes_last['myval']=$value;

	  $enc_attributes_up=base64_encode(serialize($attributes_up));
	  $enc_attributes_down=base64_encode(serialize($attributes_down));
	  $enc_attributes_thirst=base64_encode(serialize($attributes_thirst));
	  $enc_attributes_last=base64_encode(serialize($attributes_last));


	  $up_image = '<img src="'. $GLOBALS['phpgw']->common->image('phpgw','up2.png').'" border="0">';
	  $down_image = '<img src="'. $GLOBALS['phpgw']->common->image('phpgw','down2.png').'" border="0">';
	  $last_image = '<img src="'. $GLOBALS['phpgw']->common->image('phpgw','last2.png').'" border="0">';

	  
	  $display='<a href="'.$baselink.'&plg=vorder&attributes='.$enc_attributes_up.'&where='.$where_val_enc.'" title="'.lang('move up').'">'.$up_image.'</a>';
	  
	  $display.=' <a href="'.$baselink.'&plg=vorder&attributes='.$enc_attributes_down.'&where='.$where_val_enc.'" title="'.lang('move down').'">'.$down_image.'</a>';
	  
	  $display.=' <a href="'.$baselink.'&plg=vorder&attributes='.$enc_attributes_last.'&where='.$where_val_enc.'" title="'.lang('move to last position').'">'.$last_image.'</a>';

	  return $display;
   }

   // debut of the AUTONOME FORM ACTION PLUGIN !!! WHOOPIE
   function plg_afa_vorder($where_val_enc,$attributes,$conf_arr)
   {
	  global $local_bo;
	  $debug=1;

	  $attr = unserialize(base64_decode($attributes));
	  $table = $local_bo->site_object[table_name];
	  $where=base64_decode($where_val_enc);
	  $where_not=str_replace('=','!=',$where);

	  $local_bo->so->site_db_connection($local_bo->site_id);
	  $SQL1="SELECT * FROM $table WHERE `{$attr[myname]}`=0 OR `{$attr[myname]}`=NULL";

	  if($local_bo->so->site_db->query($SQL1,__LINE__,__FILE__))
	  {
		 $totalnulls=$local_bo->so->site_db->num_rows();
		 if($totalnulls>0)
		 {
			for($i=1;$i<=$totalnulls;$i++)
			{
			   $SQL3="UPDATE $table SET `{$attr[myname]}` = $i WHERE `{$attr[myname]}`=0 OR `{$attr[myname]}`=NULL LIMIT 1";
			   $local_bo->so->site_db->query($SQL3,__LINE__,__FILE__);	   
			}

			$diff=$attr[myval]-$totalnulls;
			if($diff<=0)
			{
			   $SQL2="UPDATE $table SET `{$attr[myname]}`=`{$attr[myname]}`+$totalnulls+2 WHERE ((`{$attr[myname]}`!=0) OR (`{$attr[myname]}`!=NULL))";	
			   $attr[myval]=$attr[myval]+$totalnulls+1;
			   $local_bo->so->site_db->query($SQL2,__LINE__,__FILE__);

			}
		 }
	  }

	  /* UP */
	  if($attr[move]=='up')
	  {
		 $SQL4="SELECT * FROM $table WHERE (`{$attr[myname]}`< {$attr[myval]}) AND $where_not ORDER BY `{$attr[myname]}` DESC LIMIT 1";
		 $local_bo->so->site_db->query($SQL4,__LINE__,__FILE__);
		 $local_bo->so->site_db->next_record();
		 $newval = $local_bo->so->site_db->f($attr[myname]);

		 if($newval==$attr[myval]) $newval=$attr[myval]-1;

		 $SQL5="UPDATE $table SET `{$attr[myname]}`={$attr[myval]} WHERE (`{$attr[myname]}`< {$attr[myval]}) AND $where_not ORDER BY `{$attr[myname]}` DESC LIMIT 1";
		 $local_bo->so->site_db->query($SQL5,__LINE__,__FILE__);

		 $SQL6="UPDATE $table SET `{$attr[myname]}`={$newval} WHERE $where LIMIT 1";
		 $local_bo->so->site_db->query($SQL6,__LINE__,__FILE__);

		 $local_bo->message[info]=lang('Moved Record one row up.');
	  }
	  /* DOWN */
	  elseif($attr[move]=='down')
	  {
		 $SQL4="SELECT * FROM $table WHERE (`{$attr[myname]}`> {$attr[myval]}) AND $where_not ORDER BY `{$attr[myname]}` ASC LIMIT 1";
		 $local_bo->so->site_db->query($SQL4,__LINE__,__FILE__);
		 $local_bo->so->site_db->next_record();
		 $newval = $local_bo->so->site_db->f($attr[myname]);

		 if($newval==$attr[myval]) $newval=$attr[myval]+1;

		 $SQL5="UPDATE $table SET `{$attr[myname]}`={$attr[myval]} WHERE (`{$attr[myname]}`> {$attr[myval]}) AND $where_not ORDER BY `{$attr[myname]}` ASC LIMIT 1";
		 $local_bo->so->site_db->query($SQL5,__LINE__,__FILE__);

		 $SQL6="UPDATE $table SET `{$attr[myname]}`={$newval} WHERE $where LIMIT 1";
		 $local_bo->so->site_db->query($SQL6,__LINE__,__FILE__);

		 $local_bo->message[info]=lang('Moved Record one row down.');
	  }
	  elseif($attr[move]=='last')
	  {
		 $SQL4="SELECT * FROM `$table` ORDER BY `{$attr[myname]}` DESC LIMIT 1";

		 $local_bo->so->site_db->query($SQL4,__LINE__,__FILE__);
		 $local_bo->so->site_db->next_record();
		 $newval = $local_bo->so->site_db->f($attr[myname]) + 1;

		 $SQL6="UPDATE $table SET `{$attr[myname]}`={$newval} WHERE $where LIMIT 1";
		 $local_bo->so->site_db->query($SQL6,__LINE__,__FILE__);

		 $local_bo->message[info]=lang('Moved Record to last row.');
	  }

	  // FIXME not yet finished
	  elseif($attr[move]=='thirst')
	  {
		 $SQL4="SELECT * FROM `$table` ORDER BY `{$attr[myname]}` ASC LIMIT 1";

		 $local_bo->so->site_db->query($SQL4,__LINE__,__FILE__);
		 $local_bo->so->site_db->next_record();

		 $SQL5="UPDATE $table SET `{$attr[myname]}`=`{$attr[myname]}`+1 ORDER BY `{$attr[myname]}`  LIMIT 1";
		 $local_bo->so->site_db->query($SQL5,__LINE__,__FILE__);
 
		 $newval = $local_bo->so->site_db->f($attr[myname]);

		 $SQL6="UPDATE $table SET `{$attr[myname]}`={$newval} WHERE $where LIMIT 1";
		 $local_bo->so->site_db->query($SQL6,__LINE__,__FILE__);

		 $local_bo->message[info]=lang('Moved Record to last row.');
	  }
	  else
	  {
		 $local_bo->message[error]=lang('An error occured.');
	  }

	  $local_bo->save_sessiondata();
	  $local_bo->common->exit_and_open_screen('jinn.uiuser.browse_objects&orderby='.$attr[myname].' ASC');
   }

?>

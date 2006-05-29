<?php

	// Actions

		global $template;
		
		if (isset($_REQUEST['action']) && logged_on) {
			
			switch($_REQUEST['action']) {
				
				case "templates:select":	if (isset($_REQUEST['selected_template'])) {
											$id = (int) $_REQUEST['selected_template'];
											if ($id == -1) {
												$exists = 1;
											} else {
												$exists = db_query("select count(ident) as template_exists from templates where ident = $id and (owner = ".$_SESSION['userid']." or public='yes')");
												$exists = $exists[0]->template_exists;
											}
											if ($exists) {
												db_query("update ".tbl_prefix."users set template_id = $id where ident = " . $_SESSION['userid']);
												$messages[] = "Your current template has been changed.";
											}
										}
										break;
				case "templates:save":
										if (
												isset($_REQUEST['template'])
												&& isset($_REQUEST['save_template_id'])
												&& isset($_REQUEST['templatetitle'])
											) {
												$id = (int) $_REQUEST['save_template_id'];
												unset($_SESSION['template_element_cache'][$id]);
												$exists = db_query("select count(ident) as template_exists from templates where ident = $id and owner = ".$_SESSION['userid']);
												$exists = $exists[0]->template_exists;
												if ($exists) {
													$templatetitle = addslashes($_REQUEST['templatetitle']);
													db_query("update templates set name = '$templatetitle' where ident = $id");
													db_query("delete from template_elements where template_id = $id");
													foreach($_REQUEST['template'] as $name => $content) {
														$slashname = addslashes($name);
														$slashcontent = addslashes($content);
														if ($content != "" && $content != $template[$name]) {
															db_query("insert into ".tbl_prefix."template_elements set name='$slashname', content = '$slashcontent', template_id = $id");
														}
													}
													$messages[] = "Your template has been updated.";
												}
											}
										break;
				case "deletetemplate":	if (
												isset($_REQUEST['delete_template_id'])
											) {
												$id = (int) $_REQUEST['delete_template_id'];
												unset($_SESSION['template_element_cache'][$id]);
												$exists = db_query("select count(ident) as template_exists from templates where ident = $id and owner = ".$_SESSION['userid']);
												$exists = $exists[0]->template_exists;
												if ($exists) {
													db_query("update ".tbl_prefix."users set template_id = -1 where template_id = $id");
													db_query("delete from template_elements where template_id = $id");
													db_query("delete from templates where ident = $id");
													$messages[] = "Your template was deleted.";
												}
											}
										break;
				case "templates:create":
										if (
												isset($_REQUEST['new_template_name'])
												&& isset($_REQUEST['template_based_on'])
											) {
												$based_on = (int) $_REQUEST['template_based_on'];
												$name = addslashes($_REQUEST['new_template_name']);
												db_query("insert into ".tbl_prefix."templates set name = '$name', public = 'no', owner = " . $_SESSION['userid']);
												$new_template_id = db_id();
												if ($based_on != -1) {
													$exists = db_query("select count(ident) as template_exists from templates where ident = $based_on and (owner = ".$_SESSION['userid']." or public = 'yes')");
													$exists = $exists[0]->template_exists;
													var_export($exists);
													if ($exists) {
														$elements = db_query("select * from template_elements where template_id = $based_on");
														if (sizeof($elements) > 0) {
															foreach($elements as $element) {
																db_query("insert into ".tbl_prefix."template_elements set name = '".$element->name."', content = '".$element->content."', template_id = '".$new_template_id."'");
															}
														}
													}
												}
											}
										break;
				
			}
			
		}

?>
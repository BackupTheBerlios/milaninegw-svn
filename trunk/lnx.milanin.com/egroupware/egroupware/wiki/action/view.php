<?php
// $Id: view.php,v 1.5 2004/04/12 13:02:06 ralfbecker Exp $

require('parse/main.php');
require('parse/macros.php');
require('parse/html.php');
require(TemplateDir . '/view.php');
require('lib/headers.php');

// Parse and display a page.
function action_view()
{
  global $page, $pagestore, $ParseEngine, $version;

  $pg = $pagestore->page($page);
  if($version != '')
    { $pg->version = $version; }
  $pg->read();

  gen_headers($pg->time);

  template_view(array('page'      => $pg->as_array(),
                      'title'     => $pg->title,
                      'html'      => parseText($pg->text, $ParseEngine, $page),
                      'editable'  => $pg->acl_check(),
                      'timestamp' => $pg->time,
                      'archive'   => $version != '',
                      'version'   => $pg->version));
}
?>

<?php
// $Id: find.php,v 1.8 2004/04/12 13:02:09 ralfbecker Exp $

require_once(TemplateDir . '/common.php');

// The find template is passed an associative array with the following
// elements:
//
//   find      => A string containing the text that was searched for.
//   pages     => A string containing the XHTML markup for the list of pages
//                found containing the given text.

function template_find($args)
{
  template_common_prologue(array('norobots' => 1,
                                 'title'    => lang('Find').' ' . htmlspecialchars($args['find']),
                                 'heading'  => lang('Find').' ' . htmlspecialchars($args['find']),
                                 'headlink' => '',
                                 'headsufx' => '',
                                 'toolbar'  => 1));
?>
<div id="body">
<?php print $args['pages']; ?>
</div>
<?php
  template_common_epilogue(array('twin'      => '',
                                 'edit'      => '',
                                 'editver'   => 0,
                                 'history'   => '',
                                 'timestamp' => '',
                                 'nosearch'  => 0));
}
?>

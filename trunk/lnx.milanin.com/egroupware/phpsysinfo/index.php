<?php
//
// phpSysInfo - A PHP System Information Script
// http://phpsysinfo.sourceforge.net/
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//
// $Id: index.php,v 1.5 2004/01/21 02:54:16 shrykedude Exp $
//

// our version number
$VERSION="2.1";

$phpgw_info['flags'] = array(
       'currentapp' => 'phpsysinfo'
);
include('../header.inc.php');

$lng = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
$template = $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'];

define('APP_ROOT', dirname(__FILE__));

if(isset($lng) && preg_match("/\.\.|\//", $lng)) {
   $lng = 'en';
}

// check to see if we have a random template first
if (isset($template) && $template == 'random') {
    $dir = opendir('templates/');
    while (($file = readdir($dir))!=false) {
        if ($file != 'CVS' && $file != '.' && $file != '..') {
            $buf[] = $file;
        }
    }
    $template = $buf[array_rand($buf, 1)];
    $random = True;
}

// figure out if we got a template passed in the url
if (!(isset($template) && file_exists("templates/$template"))) {
    // default template we should use if we don't get a argument.
    $template = 'idsociety';
}

define('TEMPLATE_SET', $template);

// get our current language
// default to english, but this is negotiable.
if (!(isset($lng) && file_exists('./includes/lang/' . $lng . '.php'))) {
    $lng = 'en';
    // see if the browser knows the right languange.
    if(isset($HTTP_ACCEPT_LANGUAGE)) {
        $plng = split(',', $HTTP_ACCEPT_LANGUAGE);
        if(count($plng) > 0) {
            while(list($k,$v) = each($plng)) {
                $k = split(';', $v, 1);
                $k = split('-', $k[0]);
                if(file_exists('./includes/lang/' . $k[0] . '.php')) {
                    $lng = $k[0];
                    break;
                }
            }
        }
    }
}

require('./includes/lang/' . $lng . '.php');   // get our language include

// Figure out which OS where running on, and detect support
if (file_exists(dirname(__FILE__) . '/includes/os/class.' . PHP_OS . '.inc.php')) {
    require('./includes/os/class.' . PHP_OS . '.inc.php');
    $sysinfo = new sysinfo;
} else {
    echo '<center><b>Error: ' . PHP_OS . ' is not currently supported</b></center>';
    exit;
}

require('./includes/common_functions.php'); // Set of common functions used through out the app
require('./includes/xml/vitals.php');
require('./includes/xml/network.php');
require('./includes/xml/hardware.php');
require('./includes/xml/memory.php');
require('./includes/xml/filesystems.php');

$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
$xml .= "<!DOCTYPE phpsysinfo SYSTEM \"phpsysinfo.dtd\">\n\n";
$xml .= created_by();
$xml .= "<phpsysinfo>\n";
$xml .= "  <Generation version=\"$VERSION\" timestamp=\"" . time() . "\"/>\n";
$xml .= xml_vitals();
$xml .= xml_network();
$xml .= xml_hardware();
$xml .= xml_memory();
$xml .= xml_filesystems();
$xml .= "</phpsysinfo>";


// If they have GD complied into PHP, find out the height of the image
// to make this cleaner
    if (function_exists('getimagesize') && $template != 'xml') {
      $image_prop = getimagesize(APP_ROOT . '/templates/' . TEMPLATE_SET . '/images/bar_middle.gif');
      define('BAR_HEIGHT', $image_prop[1]);
      unset($image_prop);
    } else {
      // Until they complie GD into PHP, this could look ugly
      define('BAR_HEIGHT', 16);
    }

    // Store the current template name in a cookie, set expire date to one month later
    // Store 'random' if we want a random template
    if ($random) {
        setcookie("template", 'random', (time() + 2592000));
    } else {
        setcookie("template", $template, (time() + 2592000));
    }

    // fire up the template engine
    $tpl = new Template(dirname(__FILE__) . '/templates/' . TEMPLATE_SET);
    $tpl->set_file(array(
        'form' => 'form.tpl'
    ));

    // print out a box of information
    function makebox ($title, $content) {
        $t = new Template(dirname(__FILE__) . '/templates/' . TEMPLATE_SET);

        $t->set_file(array(
            'box'  => 'box.tpl'
        ));

        $t->set_var('title', $title);
        $t->set_var('content', $content);

        return $t->parse('out', 'box');
    }

    // Fire off the XPath class
    require('./includes/XPath.class.php');
    $XPath = new XPath();
    $XPath->importFromString($xml);

    // let the page begin.
    //require('./includes/system_header.php');

    $tpl->set_var('title', $text['title'] . ': ' . $XPath->getData('/phpsysinfo/Vitals/Hostname') . ' (' . $XPath->getData('/phpsysinfo/Vitals/IPAddr') . ')');

    $tpl->set_var('vitals', makebox($text['vitals'], html_vitals(), '100%'));
    $tpl->set_var('network', makebox($text['netusage'], html_network(), '100%'));
    $tpl->set_var('hardware', makebox($text['hardware'], html_hardware(), '100%'));
    $tpl->set_var('memory', makebox($text['memusage'], html_memory(), '100%'));
    $tpl->set_var('filesystems', makebox($text['fs'], html_filesystems(), '100%'));

    // parse our the template
    $tpl->pparse('out', 'form');

    // finally our print our footer
    $phpgw->common->phpgw_footer();
?>

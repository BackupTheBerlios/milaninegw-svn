<?php
// $Id: macros.php,v 1.5.2.1 2004/07/31 10:50:07 ralfbecker Exp $

// Prepare a category list.
function view_macro_category($args)
{
  global $pagestore, $MinEntries, $DayLimit, $full, $page, $Entity;
  global $FlgChr;

  $text = '';
  if(strstr($args, '*'))                // Category containing all pages.
  {
    $list = $pagestore->allpages();
  }
  else if(strstr($args, '?'))           // New pages.
  {
    $list = $pagestore->newpages();
  }
  else if(strstr($args, '~'))           // Zero-length (deleted) pages.
  {
    $list = $pagestore->emptypages();
  }
  else                                  // Ordinary list of pages.
  {
    $parsed = parseText($args, array('parse_wikiname', 'parse_freelink'), '');
    $pagenames = array();
    preg_replace('/' . $FlgChr . '(\\d+)' . $FlgChr . '/e', '$pagenames[]=$Entity[\\1][1]', $parsed);
    $list = $pagestore->givenpages($pagenames);
  }

  if(count($list) == 0)
    { return ''; }

  usort($list, 'catSort');

  $now = time();
 
  //for($i = 0; $i < count($list); $i++)
  foreach($list as $i => $lpage)
  {
    $editTime = $lpage['time'];
    if($DayLimit && $i >= $MinEntries
       && !$full && ($now - $editTime) > $DayLimit * 24 * 60 * 60)
      { break; }

    $text = $text . html_category($lpage['time'], $lpage,
                                  $lpage['author'], $lpage['username'],
                                  $lpage['comment']);
    $text .= html_newline();
  }

  if($i < count($list)-1)
    { $text .= html_fulllist($page, count($list)); }

  return $text;
}

function catSort($p1, $p2)
  { return strcmp($p2['time'], $p1['time']); }

function sizeSort($p1, $p2)
  { return $p2['length'] - $p1['length']; }

function nameSort($p1, $p2)
{
	$titlecmp = strcmp($p1['title'], $p2['title']);
	return $titlecmp ? $titlecmp : strcmp($p1['lang'],$p2['lang']);
}

// Prepare a list of pages sorted by size.
function view_macro_pagesize()
{
  global $pagestore;

  $first = 1;
  $list = $pagestore->allpages();

  usort($list, 'sizeSort');

  $text = '';

  foreach($list as $page)
  {
    if(!$first)                         // Don't prepend newline to first one.
      { $text = $text . "\n"; }
    else
      { $first = 0; }

    $text = $text .
            $page[4] . ' ' . html_ref($page[1], $page[1]);
  }

  return html_code($text);
}

// Prepare a list of pages and those pages they link to.
function view_macro_linktab()
{
	global $pagestore;

	$text = '';
	foreach($pagestore->get_links() as $page => $data)
	{
		foreach($data as $lang => $links)
		{
			$text .= ($text ? "\n" : '') . html_ref(array('page' => $page,'lang' => $lang), "$page:$lang") . ' |';

			foreach($links as $link)
			{
				$text .= ' ' . html_ref($link, $link);
			}
		}
	}
	return html_code($text);
}

// Prepare a list of pages with no incoming links.
function view_macro_orphans()
{
  global $pagestore, $LkTbl;

  $text = '';
  $first = 1;

  $pages = $pagestore->allpages();
  usort($pages, 'nameSort');

  foreach($pages as $page)
  {
    $esc_page = addslashes($page[1]);
    $q2 = $pagestore->dbh->query("SELECT page FROM $LkTbl " .
                                 "WHERE link='$esc_page' AND page!='$esc_page'",__LINE__,__FILE__);
    if(!($r2 = $pagestore->dbh->result($q2)) || empty($r2[0]))
    {
      if(!$first)                       // Don't prepend newline to first one.
        { $text = $text . "\n"; }
      else
        { $first = 0; }

      $text = $text . html_ref($page[1], $page[1]);
    }
  }

  return html_code($text);
}

// Prepare a list of pages linked to that do not exist.
function view_macro_wanted()
{
  global $pagestore, $LkTbl, $PgTbl;

  $text = '';
  $first = 1;

  $q1 = $pagestore->dbh->query("SELECT l.link, SUM(l.count) AS ct, p.title " .
                               "FROM $LkTbl AS l LEFT JOIN $PgTbl AS p " .
                               "ON l.link = p.title " .
                               "GROUP BY l.link " .
                               "HAVING p.title IS NULL " .
                               "ORDER BY ct DESC, l.link",__LINE__,__FILE__);

  while(($result = $pagestore->dbh->result($q1)))
  {
    if(!$first)                         // Don't prepend newline to first one.
      { $text = $text . "\n"; }
    else
      { $first = 0; }

    $text = $text . '(' .
            html_url(findURL($result[0]), $result[1]) .
            ') ' . html_ref($result[0], $result[0]);
  }

  return html_code($text);
}

// Prepare a list of pages sorted by how many links they contain.
function view_macro_outlinks()
{
  global $pagestore, $LkTbl;

  $text = '';
  $first = 1;

  $q1 = $pagestore->dbh->query("SELECT page, SUM(count) AS ct FROM $LkTbl " .
                               "GROUP BY page ORDER BY ct DESC, page",__LINE__,__FILE__);
  while(($result = $pagestore->dbh->result($q1)))
  {
    if(!$first)                         // Don't prepend newline to first one.
      { $text = $text . "\n"; }
    else
      { $first = 0; }

    $text = $text .
            '(' . $result[1] . ') ' . html_ref($result[0], $result[0]);
  }

  return html_code($text);
}

// Prepare a list of pages sorted by how many links to them exist.
function view_macro_refs()
{
  global $pagestore, $LkTbl, $PgTbl;

  $text = '';
  $first = 1;

// It's not quite as straightforward as one would imagine to turn the
// following code into a JOIN, since we want to avoid multiplying the
// number of links to a page by the number of versions of that page that
// exist.  If anyone has some efficient suggestions, I'd be welcome to
// entertain them.  -- ScottMoonen

  $q1 = $pagestore->dbh->query("SELECT link, SUM(count) AS ct FROM $LkTbl " .
                               "GROUP BY link ORDER BY ct DESC, link",__LINE__,__FILE__);
  while(($result = $pagestore->dbh->result($q1)))
  {
    $esc_page = addslashes($result[0]);
    $q2 = $pagestore->dbh->query("SELECT MAX(version) FROM $PgTbl " .
                                 "WHERE title='$esc_page'",__LINE__,__FILE__);
    if(($r2 = $pagestore->dbh->result($q2)) && !empty($r2[0]))
    {
      if(!$first)                       // Don't prepend newline to first one.
        { $text = $text . "\n"; }
      else
        { $first = 0; }

      $text = $text . '(' .
              html_url(findURL($result[0]), $result[1]) . ') ' .
              html_ref($result[0], $result[0]);
    }
  }

  return html_code($text);
}

// This macro inserts an HTML anchor into the text.
function view_macro_anchor($args)
{
  preg_match('/^([A-Za-z][-A-Za-z0-9_:.]*)$/', $args, $result);

  if($result[1] != '')
    { return html_anchor($result[1]); }
  else
    { return ''; }
}

// This macro transcludes another page into a wiki page.
function view_macro_transclude($args)
{
  global $pagestore, $ParseEngine, $ParseObject;
  static $visited_array = array();
  static $visited_count = 0;

  if(!validate_page($args))
    { return '[[Transclude ' . $args . ']]'; }

  $visited_array[$visited_count++] = $ParseObject;
  for($i = 0; $i < $visited_count; $i++)
  {
    if($visited_array[$i] == $args)
    {
      $visited_count--;
      return '[[Transclude ' . $args . ']]';
    }
  }

  $pg = $pagestore->page($args);
  $pg->read();
  if(!$pg->exists)
  {
    $visited_count--;
    return '[[Transclude ' . $args . ']]';
  }

  $result = parseText($pg->text, $ParseEngine, $args);
  $visited_count--;
  return $result;
}

?>

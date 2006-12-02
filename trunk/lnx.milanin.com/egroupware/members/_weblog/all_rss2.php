<?php

	//	ELGG weblog RSS 2.0 page

	// Run includes
		require("../includes.php");
		
		run("profile:init");
		run("friends:init");
		run("weblogs:init");
		
		global $profile_id;
		global $individual;
		global $page_owner;
		
		$individual = 1;
		
		$sitename = htmlentities(sitename);
		
		header("Content-type: text/xml");
		
				$name = htmlentities("MilanIN Webloggers");
                                $description = htmlentities(sitename." Bloggers Feed");
				$mainurl = htmlentities(url .  "_weblog/everyone.php");
			
			echo <<< END
<?xml version='1.0' encoding='UTF-8'?>
<rss version='2.0'   xmlns:dc='http://purl.org/dc/elements/1.1/'>
<!-- all_rss -->
END;
                        echo <<< END
  <channel xml:base='$mainurl'>
    <title>$name</title>
    <description>$description</description>
    <language>en-gb</language>
    <link>$mainurl</link>
END;
				if (!isset($_REQUEST['tag'])) {
					$entries = db_query("select posts.*, users.username , users.name from ".tbl_prefix."weblog_posts posts left join ".tbl_prefix."users users on posts.owner=users.ident where access = 'PUBLIC' order by posted desc limit 30");
				} else {
					$tag = addslashes($_REQUEST['tag']);
					$entries = db_query("select ".tbl_prefix."weblog_posts.* from ".tbl_prefix."tags left join ".tbl_prefix."weblog_posts.on ".tbl_prefix."weblog_posts.ident = tags.ref where  ".tbl_prefix."weblog_posts.access = 'PUBLIC' and tags.tag = '$tag' and tags.tagtype = 'weblog' order by ".tbl_prefix."weblog_posts.posted desc limit 10");
				}
				if (sizeof($entries) > 0) {
					foreach($entries as $entry) {
						$title = htmlentities(stripslashes($entry->name.' :: '.$entry->title));
						$link = url .$entry->username . "/weblog/" . $entry->ident . ".html";
						$body = htmlentities(run("weblogs:text:process",stripslashes($entry->body)));
						$pubdate = gmdate("D, d M Y H:i:s T", $entry->posted);
						$keywords = db_query("select * from ".tbl_prefix."tags where tagtype = 'weblog' and ref = '".$entry->ident."'");
						$keywordtags = "";
						if (sizeof($keywords) > 0) {
							foreach($keywords as $keyword) {
								$keywordtags .= "\n        <dc:subject>".htmlentities(stripslashes($keyword->tag)) . "</dc:subject>";
							}
						}
						echo <<< END
    <item>
        <title>$title</title>
        <link>$link</link>
        <pubDate>$pubdate</pubDate>$keywordtags
        <description>$body</description>
    </item>
END;
					}
				}
				echo <<< END
  </channel>
</rss>
END;

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
		
		if (isset($page_owner)) {
                  $info = db_query("select * from ".tbl_prefix."users where ident = $page_owner");
			if (sizeof($info) > 0) {
				$info = $info[0];
				$name = htmlentities(run('profile:display:weblog_title'),ENT_QUOTES,'UTF-8');
                                $description = htmlentities(run('profile:display:weblog_description'),ENT_QUOTES,'UTF-8');
				$username = htmlentities(stripslashes($info->username));
				$mainurl = htmlentities(url . $username . "/weblog/");
			
			echo <<< END
<?xml version='1.0' encoding='UTF-8'?>
<rss version='2.0'   xmlns:dc='http://purl.org/dc/elements/1.1/'>
END;
                        echo <<< END
  <channel xml:base='$mainurl'>
    <title>$name</title>
    <description>$description</description>
    <language>en-gb</language>
    <link>$mainurl</link>
END;
				if (!isset($_REQUEST['tag'])) {
					$entries = db_query("select * from ".tbl_prefix."weblog_posts where weblog = $page_owner and access = 'PUBLIC' order by posted desc limit 10");
				} else {
					$tag = addslashes($_REQUEST['tag']);
					$entries = db_query("select ".tbl_prefix."weblog_posts.* from ".tbl_prefix."tags left join ".tbl_prefix."weblog_posts.on ".tbl_prefix."weblog_posts.ident = tags.ref where ".tbl_prefix."weblog_posts.weblog = $page_owner and ".tbl_prefix."weblog_posts.access = 'PUBLIC' and tags.tag = '$tag' and tags.tagtype = 'weblog' order by ".tbl_prefix."weblog_posts.posted desc limit 10");
				}
				if (sizeof($entries) > 0) {
					foreach($entries as $entry) {
						$title = htmlentities(stripslashes($entry->title));
						$link = url . $username . "/weblog/" . $entry->ident . ".html";
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
        <guid>$link</guid>
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
		}
	}
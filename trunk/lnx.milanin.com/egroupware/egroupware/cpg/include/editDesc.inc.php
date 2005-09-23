<?php
// ------------------------------------------------------------------------- //
// Coppermine Photo Gallery 1.3.0                                            //
// ------------------------------------------------------------------------- //
// Copyright (C) 2002,2003 Gregory DEMAR                                     //
// http://www.chezgreg.net/coppermine/                                       //
// ------------------------------------------------------------------------- //
// Updated by the Coppermine Dev Team                                        //
// (http://coppermine.sf.net/team/)                                          //
// see /docs/credits.html for details                                        //
// ------------------------------------------------------------------------- //
// This program is free software; you can redistribute it and/or modify      //
// it under the terms of the GNU General Public License as published by      //
// the Free Software Foundation; either version 2 of the License, or         //
// (at your option) any later version.                                       //
// ------------------------------------------------------------------------- //

/*
$Id: editDesc.inc.php,v 1.2 2004/04/09 18:31:27 joestewart Exp $
*/

function process_post_data()
{
        global $HTTP_POST_VARS, $CONFIG;
        global $lang_errors;

                $pid             = (int)$HTTP_POST_VARS['pid'];
                $aid         = (int)$HTTP_POST_VARS['aid'];
                $title       = $HTTP_POST_VARS['title'];
                $caption     = $HTTP_POST_VARS['caption'];
                $keywords    = $HTTP_POST_VARS['keywords'];
                $user1       = $HTTP_POST_VARS['user1'];
                $user2       = $HTTP_POST_VARS['user2'];
                $user3       = $HTTP_POST_VARS['user3'];
                $user4       = $HTTP_POST_VARS['user4'];

                $read_exif    = isset($HTTP_POST_VARS['read_exif']);
                $reset_vcount = isset($HTTP_POST_VARS['reset_vcount']);
                $reset_votes  = isset($HTTP_POST_VARS['reset_votes']);
                $del_comments = isset($HTTP_POST_VARS['del_comments']) || $delete;

                $query = "SELECT category, filepath, filename FROM {$CONFIG['TABLE_PICTURES']}, {$CONFIG['TABLE_ALBUMS']} WHERE {$CONFIG['TABLE_PICTURES']}.aid = {$CONFIG['TABLE_ALBUMS']}.aid AND pid='$pid'";
                $result = db_query($query);
                if (!mysql_num_rows($result)) cpg_die(CRITICAL_ERROR, $lang_errors['non_exist_ap'], __FILE__, __LINE__);
                $pic = mysql_fetch_array($result);
                mysql_free_result($result);

                if (!GALLERY_ADMIN_MODE) {
                        if ($pic['category'] != FIRST_USER_CAT + USER_ID) cpg_die(ERROR, $lang_errors['perm_denied']."<br />(picture category = {$pic['category']}/ $pid)", __FILE__, __LINE__);
                        if (!isset($user_album_set[$aid])) cpg_die(ERROR, $lang_errors['perm_denied']."<br />(target album = $aid)", __FILE__, __LINE__);
                }

                $update  = "aid = '".$aid."'";
                $update .= ", title = '".addslashes($title)."'";
                $update .= ", caption = '".addslashes($caption)."'";
                $update .= ", keywords = '".addslashes($keywords)."'";
                $update .= ", user1 = '".addslashes($user1)."'";
                $update .= ", user2 = '".addslashes($user2)."'";
                $update .= ", user3 = '".addslashes($user3)."'";
                $update .= ", user4 = '".addslashes($user4)."'";

                if ($reset_vcount) $update .= ", hits = '0'";
                if ($reset_votes) $update .= ", pic_rating = '0', votes = '0'";

                if ($del_comments) {
                        $query = "DELETE FROM {$CONFIG['TABLE_COMMENTS']} WHERE pid='$pid'";
                        $result =db_query($query);

                } else {
                        $query = "UPDATE {$CONFIG['TABLE_PICTURES']} SET $update WHERE pid='$pid' LIMIT 1";
                        $result = db_query($query);
                }

}

function get_user_albums($user_id)
{
        global $CONFIG, $USER_ALBUMS_ARRAY, $user_albums_list;

        if (!isset($USER_ALBUMS_ARRAY[$user_id])) {
                $user_albums = db_query("SELECT aid, title FROM {$CONFIG['TABLE_ALBUMS']} WHERE category='".(FIRST_USER_CAT + $user_id)."' ORDER BY title");
                if (mysql_num_rows($user_albums)) {
                    $user_albums_list=db_fetch_rowset($user_albums);
                } else {
                        $user_albums_list = array();
                }
                mysql_free_result($user_albums);
                $USER_ALBUMS_ARRAY[$user_id] = $user_albums_list;
        } else {
                $user_albums_list = &$USER_ALBUMS_ARRAY[$user_id];
        }
}

function form_alb_list_box()
{
        global $CONFIG, $CURRENT_PIC;
        global $user_albums_list, $public_albums_list, $lang_editpics_php;
        $sel_album = $CURRENT_PIC['aid'];

        echo <<<EOT
        <tr>
            <td class="tableb" style="white-space: nowrap;">
                        {$lang_editpics_php['album']}
        </td>
        <td class="tableb" valign="top">
                <select name="aid" class="listbox">

EOT;
                foreach($public_albums_list as $album){
                        echo '                        <option value="'.$album['aid'].'"'.($album['aid'] == $sel_album ? ' selected' : '').'>'.$album['title'] . "</option>\n";
                }
                foreach($user_albums_list as $album){
                        echo '                        <option value="'.$album['aid'].'"'.($album['aid'] == $sel_album ? ' selected' : '').'>* '.$album['title'] . "</option>\n";
                }
        echo <<<EOT
                        </select>
                </td>
        </tr>

EOT;
}

if (isset($HTTP_POST_VARS['submitDescription'])) process_post_data();

$result = db_query("SELECT * FROM {$CONFIG['TABLE_PICTURES']} WHERE pid = '$pid'");
$CURRENT_PIC = mysql_fetch_array($result);
mysql_free_result($result);

$thumb_url = get_pic_url($CURRENT_PIC, 'thumb');
$thumb_link = 'displayimage.php?&pos='.(-$CURRENT_PIC['pid']);
$filename = htmlspecialchars($CURRENT_PIC['filename']);

$THUMB_ROWSPAN=5;
if ($CONFIG['user_field1_name'] != '') $THUMB_ROWSPAN++;
if ($CONFIG['user_field2_name'] != '') $THUMB_ROWSPAN++;
if ($CONFIG['user_field3_name'] != '') $THUMB_ROWSPAN++;
if ($CONFIG['user_field4_name'] != '') $THUMB_ROWSPAN++;


if (GALLERY_ADMIN_MODE) {
    $public_albums = db_query("SELECT aid, title FROM {$CONFIG['TABLE_ALBUMS']} WHERE category < '".FIRST_USER_CAT."' ORDER BY title");
        if (mysql_num_rows($public_albums)) {
            $public_albums_list=db_fetch_rowset($public_albums);
        } else {
                $public_albums_list = array();
        }
        mysql_free_result($public_albums);
} else {
        $public_albums_list = array();
}

get_user_albums(USER_ID);

starttable("100%", $lang_editpics_php['desc'], 3);
echo <<<EOT
<SCRIPT LANGUAGE="JavaScript">
function textCounter(field, maxlimit) {
        if (field.value.length > maxlimit) // if too long...trim it!
        field.value = field.value.substring(0, maxlimit);
}
</script>
EOT;

$pic_info = sprintf($lang_editpics_php['pic_info_str'], $CURRENT_PIC['pwidth'], $CURRENT_PIC['pheight'], ($CURRENT_PIC['filesize'] >> 10), $CURRENT_PIC['hits'], $CURRENT_PIC['votes']);

print <<<EOT
<table align="center" width="100%" cellspacing="1" cellpadding="0" class="maintableb">
        <form method="post">
        <input type="hidden" name="pid" value="{$CURRENT_PIC['pid']}">
        <tr>
                <td class="tableh2" colspan="3">
                        <b>$filename</b>
                </td>
        </tr>
        <tr>
                <td class="tableb" style="white-space: nowrap;">
                        {$lang_editpics_php['pic_info']}
                </td>
                <td class="tableb">
                        $pic_info
                </td>
                   <td class="tableb" align="center" rowspan="$THUMB_ROWSPAN">
                        <img src="$thumb_url" class="image" border="0"><br />
            </td>
        </tr>
EOT;

form_alb_list_box();

print <<<EOT
        <tr>
            <td class="tableb" style="white-space: nowrap;">
                {$lang_editpics_php['title']}
        </td>
        <td width="100%" class="tableb" valign="top">
                <input type="text" style="width: 100%" name="title" maxlength="255" value="{$CURRENT_PIC['title']}" class="textinput">
                </td>
        </tr>
EOT;
echo <<<EOT
        <tr>
                <td class="tableb" valign="top" style="white-space: nowrap;">
                        {$lang_editpics_php['desc']}
                </td>
                <td class="tableb" valign="top">
                        <textarea name="caption" ROWS="5" COLS="40" WRAP="virtual"  class="textinput" STYLE="WIDTH: 100%;" onKeyDown="textCounter(this, {$CONFIG['max_img_desc_length']});" onKeyUp="textCounter(this, {$CONFIG['max_img_desc_length']});">{$CURRENT_PIC['caption']}</textarea>
                </td>
        </tr>
        <tr>
            <td class="tableb" style="white-space: nowrap;">
                {$lang_editpics_php['keywords']}
        </td>
        <td width="100%" class="tableb" valign="top">
                <input type="text" style="width: 100%" name="keywords" maxlength="255" value="{$CURRENT_PIC['keywords']}" class="textinput">
                </td>
        </tr>

EOT;
if ($CONFIG['user_field1_name'] != ''){
echo <<<EOT
        <tr>
            <td class="tableb" style="white-space: nowrap;">
                {$CONFIG['user_field1_name']}
        </td>
        <td width="100%" class="tableb" valign="top">
                <input type="text" style="width: 100%" name="user1" maxlength="255" value="{$CURRENT_PIC['user1']}" class="textinput">
                </td>
        </tr>
EOT;
}
if ($CONFIG['user_field2_name'] != ''){
echo <<<EOT
        <tr>
            <td class="tableb" style="white-space: nowrap;">
                {$CONFIG['user_field2_name']}
        </td>
        <td width="100%" class="tableb" valign="top">
                <input type="text" style="width: 100%" name="user2" maxlength="255" value="{$CURRENT_PIC['user2']}" class="textinput">
                </td>
        </tr>
EOT;
}if ($CONFIG['user_field3_name'] != ''){
echo <<<EOT
        <tr>
            <td class="tableb" style="white-space: nowrap;">
                {$CONFIG['user_field3_name']}
        </td>
        <td width="100%" class="tableb" valign="top">
                <input type="text" style="width: 100%" name="user3" maxlength="255" value="{$CURRENT_PIC['user3']}" class="textinput">
                </td>
        </tr>
EOT;
}if ($CONFIG['user_field4_name'] != ''){
echo <<<EOT
        <tr>
            <td class="tableb" style="white-space: nowrap;">
                {$CONFIG['user_field1_name']}
        </td>
        <td width="100%" class="tableb" valign="top">
                <input type="text" style="width: 100%" name="user4" maxlength="255" value="{$CURRENT_PIC['user4']}" class="textinput">
                </td>
        </tr>
EOT;
}
print <<<EOT
        <tr>
                <td class="tableb" colspan="3" align="center">
                        <b><input type="checkbox" name="read_exif" value="1" class="checkbox">{$lang_editpics_php['read_exif']}</b>&nbsp;
                        <b><input type="checkbox" name="reset_vcount" value="1" class="checkbox">{$lang_editpics_php['reset_view_count']}</b>&nbsp;
                        <b><input type="checkbox" name="reset_votes" value="1" class="checkbox">{$lang_editpics_php['reset_votes']}</b>&nbsp;
                        <b><input type="checkbox" name="del_comments" value="1" class="checkbox">{$lang_editpics_php['del_comm']}</b>&nbsp;
                </td>
        </tr>
        <tr>
                <td colspan="3" align="center" class="tablef">
                        <input type="submit" value="{$lang_editpics_php['apply']}" name="submitDescription" class="button">
                </td>
                </form>
        </tr>
</table>
EOT;

?>
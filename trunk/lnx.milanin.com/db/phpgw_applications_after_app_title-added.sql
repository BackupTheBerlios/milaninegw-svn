-- phpMyAdmin SQL Dump
-- version 2.6.4-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: 62.149.150.38
-- Generato il: 22 Nov, 2005 at 01:29 PM
-- Versione MySQL: 4.0.25
-- Versione PHP: 4.3.11
-- 
-- Database: `Sql73134_1`
-- 

-- --------------------------------------------------------

-- 
-- Struttura della tabella `phpgw_applications`
-- 

CREATE TABLE `phpgw_applications` (
  `app_id` int(11) NOT NULL auto_increment,
  `app_name` varchar(25) NOT NULL default '',
  `app_title` varchar(64) default NULL,
  `app_enabled` int(11) NOT NULL default '0',
  `app_order` int(11) NOT NULL default '0',
  `app_tables` text NOT NULL,
  `app_version` varchar(20) NOT NULL default '0',
  PRIMARY KEY  (`app_id`),
  UNIQUE KEY `app_name` (`app_name`),
  KEY `app_enabled` (`app_enabled`,`app_order`)
) TYPE=MyISAM AUTO_INCREMENT=42 ;

-- 
-- Dump dei dati per la tabella `phpgw_applications`
-- 

INSERT INTO `phpgw_applications` VALUES (1, 'phpgwapi', 'phpgwapi', 3, 1, 'phpgw_config,phpgw_applications,phpgw_acl,phpgw_accounts,phpgw_preferences,phpgw_sessions,phpgw_app_sessions,phpgw_access_log,phpgw_hooks,phpgw_languages,phpgw_lang,phpgw_nextid,phpgw_categories,phpgw_addressbook,phpgw_addressbook_extra,phpgw_log,phpgw_log_msg,phpgw_interserv,phpgw_vfs,phpgw_history_log,phpgw_async', '1.0.0.007');
INSERT INTO `phpgw_applications` VALUES (2, 'admin', 'admin', 1, 1, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (3, 'preferences', 'preferences', 2, 1, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (4, 'notifywindow', 'notifywindow', 2, 1, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (5, 'addressbook', 'addressbook', 1, 4, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (6, 'backup', 'backup', 0, 41, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (7, 'bookmarks', 'bookmarks', 1, 12, 'phpgw_bookmarks', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (8, 'calendar', 'calendar', 1, 3, 'phpgw_cal,phpgw_cal_holidays,phpgw_cal_repeats,phpgw_cal_user,phpgw_cal_extra', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (9, 'comic', 'comic', 0, 21, 'phpgw_comic,phpgw_comic_admin,phpgw_comic_data', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (10, 'developer_tools', 'developer_tools', 1, 61, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (11, 'email', 'email', 1, 2, 'phpgw_anglemail', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (12, 'emailadmin', 'emailadmin', 1, 10, 'phpgw_emailadmin', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (13, 'etemplate', 'etemplate', 2, 60, 'phpgw_etemplate', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (14, 'filemanager', 'filemanager', 1, 6, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (15, 'forum', 'forum', 0, 7, 'phpgw_forum_body,phpgw_forum_categories,phpgw_forum_forums,phpgw_forum_threads', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (16, 'ftp', 'ftp', 0, 20, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (37, 'fudforum', 'fudforum', 1, 7, 'phpgw_fud_action_log,phpgw_fud_ann_forums,phpgw_fud_announce,phpgw_fud_attach,phpgw_fud_avatar,phpgw_fud_blocked_logins,phpgw_fud_buddy,phpgw_fud_cat,phpgw_fud_custom_tags,phpgw_fud_email_block,phpgw_fud_ext_block,phpgw_fud_forum,phpgw_fud_fc_view,phpgw_fud_forum_notify,phpgw_fud_forum_read,phpgw_fud_group_cache,phpgw_fud_group_members,phpgw_fud_group_resources,phpgw_fud_groups,phpgw_fud_index,phpgw_fud_ip_block,phpgw_fud_level,phpgw_fud_mime,phpgw_fud_mlist,phpgw_fud_mod,phpgw_fud_mod_que,phpgw_fud_msg,phpgw_fud_msg_report,phpgw_fud_nntp,phpgw_fud_pmsg,phpgw_fud_poll,phpgw_fud_poll_opt,phpgw_fud_poll_opt_track,phpgw_fud_read,phpgw_fud_replace,phpgw_fud_search,phpgw_fud_search_cache,phpgw_fud_ses,phpgw_fud_smiley,phpgw_fud_stats_cache,phpgw_fud_themes,phpgw_fud_thr_exchange,phpgw_fud_thread,phpgw_fud_thread_notify,phpgw_fud_thread_rate_track,phpgw_fud_thread_view,phpgw_fud_title_index,phpgw_fud_user_ignore,phpgw_fud_users', '0.0.1');
INSERT INTO `phpgw_applications` VALUES (18, 'headlines', 'headlines', 1, 13, 'phpgw_headlines_sites,phpgw_headlines_cached', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (19, 'jinn', 'jinn', 0, 15, 'phpgw_jinn_acl,phpgw_jinn_sites,phpgw_jinn_site_objects,phpgw_jinn_adv_field_conf', '0.7.003');
INSERT INTO `phpgw_applications` VALUES (20, 'messenger', 'messenger', 1, 19, 'phpgw_messenger_messages', '0.8.1');
INSERT INTO `phpgw_applications` VALUES (21, 'news_admin', 'news_admin', 1, 16, 'phpgw_news,phpgw_news_export', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (22, 'phpbrain', 'phpbrain', 1, 25, 'phpgw_kb_articles,phpgw_kb_comment,phpgw_kb_questions,phpgw_kb_ratings,phpgw_kb_related_art,phpgw_kb_search', '1.0.2');
INSERT INTO `phpgw_applications` VALUES (23, 'phpldapadmin', 'phpldapadmin', 0, 42, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (24, 'phpsysinfo', 'phpsysinfo', 2, 99, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (25, 'polls', 'polls', 1, 17, 'phpgw_polls_data,phpgw_polls_desc,phpgw_polls_user,phpgw_polls_settings', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (26, 'registration', 'registration', 1, 40, 'phpgw_reg_accounts,phpgw_reg_fields', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (27, 'sitemgr', 'sitemgr', 1, 14, 'phpgw_sitemgr_pages,phpgw_sitemgr_pages_lang,phpgw_sitemgr_categories_state,phpgw_sitemgr_categories_lang,phpgw_sitemgr_modules,phpgw_sitemgr_blocks,phpgw_sitemgr_blocks_lang,phpgw_sitemgr_content,phpgw_sitemgr_content_lang,phpgw_sitemgr_active_modules,phpgw_sitemgr_properties,phpgw_sitemgr_sites', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (28, 'stocks', 'stocks', 0, 18, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (29, 'tts', 'tts', 1, 10, 'phpgw_tts_tickets,phpgw_tts_views,phpgw_tts_states,phpgw_tts_transitions', '1.0.002');
INSERT INTO `phpgw_applications` VALUES (30, 'sitemgr-link', 'sitemgr-link', 1, 9, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (31, 'felamimail', 'felamimail', 1, 2, 'phpgw_felamimail_cache,phpgw_felamimail_folderstatus,phpgw_felamimail_displayfilter', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (32, 'infolog', 'infolog', 1, 5, 'phpgw_infolog,phpgw_links,phpgw_infolog_extra', '1.0.0.001');
INSERT INTO `phpgw_applications` VALUES (33, 'wiki', 'wiki', 1, 11, 'phpgw_wiki_links,phpgw_wiki_pages,phpgw_wiki_rate,phpgw_wiki_interwiki,phpgw_wiki_sisterwiki,phpgw_wiki_remote_pages', '1.0.0.001');
INSERT INTO `phpgw_applications` VALUES (34, 'manual', 'manual', 4, 5, '', '1.0.0');
INSERT INTO `phpgw_applications` VALUES (35, 'projects', 'projects', 1, 8, 'phpgw_p_projects,phpgw_p_activities,phpgw_p_budget,phpgw_p_projectactivities,phpgw_p_hours,phpgw_p_projectmembers,phpgw_p_invoice,phpgw_p_invoicepos,phpgw_p_delivery,phpgw_p_deliverypos,phpgw_p_mstones,phpgw_p_roles,phpgw_p_costs,phpgw_p_ttracker,phpgw_p_events,phpgw_p_alarm,phpgw_p_resources', '1.0.0.004');
INSERT INTO `phpgw_applications` VALUES (38, 'sitemgr_module_guestbook', 'sitemgr_module_guestbook', 1, 100, '', '0');
INSERT INTO `phpgw_applications` VALUES (39, 'coppermine', 'coppermine', 0, 12, 'phpgw_cpg_albums;phpgw_cpg_banned;phpgw_cpg_categories;phpgw_cpg_comments;phpgw_cpg_config;phpgw_cpg_ecards;phpgw_cpg_exif;phpgw_cpg_filetypes;phpgw_cpg_pictures;phpgw_cpg_temp_data;phpgw_cpg_usergroups;phpgw_cpg_users;phpgw_cpg_votes', '1.3.2.000');
INSERT INTO `phpgw_applications` VALUES (40, 'cpg', 'cpg', 1, 4, 'phpgw_cpg_albums,phpgw_cpg_banned,phpgw_cpg_categories,phpgw_cpg_comments,phpgw_cpg_config,phpgw_cpg_ecards,phpgw_cpg_exif,phpgw_cpg_filetypes,phpgw_cpg_pictures,phpgw_cpg_temp_data,phpgw_cpg_usergroups,phpgw_cpg_users,phpgw_cpg_votes', '1.3.2.000');
INSERT INTO `phpgw_applications` VALUES (41, 'elgg-link', 'Members List', 1, 2, '', '0.0.1');

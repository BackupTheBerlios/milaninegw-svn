 
insert into `templates` (`ident`, `name`, `owner`, `public`) values (7, 'invite_title', 1, 'yes')
insert into `template_elements` (`name`, `content`, `template_id`) values ('<option>Standart invitation</option>', 'Standart invitation body', 7)
insert into `template_elements` (`name`, `content`, `template_id`) values ('<option>Invite a friend</option>', 'Invite a friend body', 7)
insert into `template_elements` (`name`, `content`, `template_id`) values ('<option selected=true>Invite your colleague</option>', 'Invite your colleague body', 7)
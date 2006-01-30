delete FROM `templates` where `ident` > 5 and `ident` < 11;
delete FROM `template_elements` where `template_id` > 5 and `template_id` < 11;
insert into  templates (ident, name, owner, public) values(6, 'invitation message template', 1, 'yes');
insert into `template_elements` (`name`, `content`, `template_id`) values ('language1', 'English',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('language2', 'Italian',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('language3', 'Russian',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('title1:language1', 'en title1:Title1 text',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('title2:language1', 'en title2:Title2 text',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('title3:language1', 'en title3:Title3 text',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('title1:language2', 'it title1:Title1 text',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('title2:language2', 'it title2:Title2 text',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('title1:language3', 'ru title1:Title1 text',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('msg1:title1:language1', 'en msg1:message body1 text english',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('msg2:title2:language1', 'en msg2:message body2 text english',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('msg3:title3:language1', 'en msg3:message body3 text english',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('msg1:title1:language2', 'it msg1:message body1 text italiano',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('msg2:title2:language2', 'it msg2:message body2 text italiano',6);
insert into `template_elements` (`name`, `content`, `template_id`) values ('msg1:title1:language3', 'ru msg1:message body1 text russian',6);


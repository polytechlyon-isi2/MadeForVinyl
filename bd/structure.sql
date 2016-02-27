drop table if exists t_article;

create table t_article (
art_id integer not null primary key auto_increment,
art_title varchar(100) not null,
art_content varchar(2000) not null,
art_price integer not null
) engine=innodb character set utf8 collate utf8_unicode_ci;

drop table if exists t_user;

create table t_user (
user_id integer not null primary key auto_increment,
user_name varchar(200) not null,
user_pass varchar(20) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;
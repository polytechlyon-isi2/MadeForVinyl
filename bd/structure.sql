drop table if exists t_vinyl;

create table t_vinyl (
vinyl_id integer not null primary key auto_increment,
vinyl_title varchar(100) not null,
vinyl_artist varchar(200) not null,
vinyl_category varchar(100) not null,
vinyl_year integer not null
) engine=innodb character set utf8 collate utf8_unicode_ci;

drop table if exists t_user;

create table t_user (
user_id integer not null primary key auto_increment,
user_name varchar(200) not null,
user_pass varchar(20) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;
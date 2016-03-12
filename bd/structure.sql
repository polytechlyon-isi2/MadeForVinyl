drop table if exists t_category;

create table t_category (
category_id integer not null primary key auto_increment,
category_title varchar(200) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;

drop table if exists t_vinyl;

create table t_vinyl (
vinyl_id integer not null primary key auto_increment,
vinyl_title varchar(100) not null,
vinyl_artist varchar(200) not null,
vinyl_category integer not null,
vinyl_year integer not null,
vinyl_sleeve varchar(500) not null,
vinyl_price double not null,
foreign key (vinyl_category) references t_category(category_id)
)engine=innodb character set utf8 collate utf8_unicode_ci;

drop table if exists t_user;

create table t_user (
    usr_id integer not null primary key auto_increment,
    usr_name varchar(50) not null,
    usr_surname varchar(50) not null,
    usr_adress varchar(250) not null,
    usr_postalCode integer not null,
    usr_town varchar(50) not null,
    usr_login varchar(50) not null,
    usr_password varchar(88) not null,
    usr_salt varchar(23) not null,
    usr_role varchar(50) not null 
) engine=innodb character set utf8 collate utf8_unicode_ci;
ALTER TABLE `t_user` ADD UNIQUE(`usr_login`);
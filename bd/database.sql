create database if not exists madeforvinyl character set utf8 collate utf8_unicode_ci;
use madeforvinyl;

grant all privileges on madeforvinyl.* to 'madefor_user'@'localhost' identified by 'secret';
INSERT INTO t_category(category_title) VALUES ("Country"),("Electro"),("Hard Rock, Metal"),("Jazz, Blues"),("Musique Classique"),("Musique du Monde"),("Pop"),("R&B, Soul, Funk"),("Rap"),("Reggae"),("Rock'n'Roll"),("Variete francaise");

INSERT INTO t_vinyl(vinyl_title, vinyl_artist, vinyl_category, vinyl_year, vinyl_sleeve, vinyl_price) VALUES 
("Invasion","Junior Disprol & Mr Rumage", "9", "2016", "https://f1.bcbits.com/img/a1085261866_10.jpg",10.00),
("Things Fall Apart","The Roots", "9", "1999", "https://i.imgur.com/VdgFply.jpg",10.00),
("Mecca and the Soul Brother", "Pete Rock and C.L. Smooth", "9", "1992", "http://static.stereogum.com/blogs.dir/2/files/2012/06/Pete-Rock-CL-Smooth-Mecca-And-The-Soul-Brother.jpg",10.00),
("Only Built 4 Cuban Linx", "Raekwon", "9", "1995", "http://static.stereogum.com/uploads/2015/07/Raekwon-Only-Built-4-Cuban-Linx.jpg",10.00),
("House of Pain (Fine Malt Lyrics)", "House of Pain", "9", "1992", "https://fanart.tv/fanart/music/c2d6a6fb-7999-4bb3-b2df-7d752fdf4e95/albumcover/house-of-pain-4f9430975d40d.jpg",10.00),
("Led Zeppelin", "Led Zeppelin", "11", "1969", "http://static.stereogum.com/uploads/2014/10/ledzeppelin1.jpeg",10.00);

/* raw password is '@dm1n' */
insert into t_user values
(3, 'admin', 'gqeuP4YJ8hU3ZqGwGikB6+rcZBqefVy+7hTLQkOD+jwVkp4fkS7/gr1rAQfn9VUKWc7bvOD7OsXrQQN5KGHbfg==', 'EDDsl&fBCJB|a5XUtAlnQN8', 'ROLE_ADMIN');
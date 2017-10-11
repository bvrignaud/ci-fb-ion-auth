CREATE TABLE `facebook_user` (
  `idfacebook_user` bigint(20) NOT NULL,
  `users_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`idfacebook_user`),
  KEY `fk_facebook_user_users1_idx` (`users_id`),
  CONSTRAINT `fk_facebook_user_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users` 
ADD COLUMN `avatar` VARCHAR(45) NULL DEFAULT NULL;

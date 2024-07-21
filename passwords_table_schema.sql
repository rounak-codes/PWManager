CREATE TABLE `passwords` (
  `id` int NOT NULL,
  `user` varchar(45) NOT NULL,
  `site_name` varchar(45) NOT NULL
  `username` varchar(45) NOT NULL,
  `pwds` varbinary(260) NOT NULL,
  `iv` varbinary(260) NOT NULL,
  `encryption_key` varbinary(260) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
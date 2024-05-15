CREATE TABLE `passwords` (
  `id` int NOT NULL,
  `username` varchar(45) NOT NULL,
  `pwds` varchar(260) NOT NULL,
  `iv` varbinary(260) NOT NULL,
  `encryption_key` varchar(260) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
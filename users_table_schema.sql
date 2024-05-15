CREATE TABLE `users` (
  `username` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `encrypted_password` varbinary(128) NOT NULL,
  `iv` varbinary(16) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
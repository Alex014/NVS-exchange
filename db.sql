CREATE TABLE `slots` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `slot_id` varchar(32) NOT NULL,
  `addr` TEXT,
  `name` varchar(256) NOT NULL,
  `value` text  NOT NULL,
  `status` enum('GENERATED','PAYED') NOT NULL DEFAULT 'GENERATED',
  `created` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `slots`
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `slot` (`slot_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created` (`created`);
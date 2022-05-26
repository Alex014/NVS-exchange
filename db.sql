
-- --------------------------------------------------------

--
-- Table structure for table `slots`
--

CREATE TABLE `slots` (
  `id` int(10) UNSIGNED NOT NULL,
  `slot_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addr` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('GENERATED','PAYED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GENERATED',
  `created` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `slots`
--
ALTER TABLE `slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `slot` (`slot_id`),
  ADD KEY `addr` (`addr`),
  ADD KEY `status` (`status`),
  ADD KEY `created` (`created`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `slots`
--
ALTER TABLE `slots`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
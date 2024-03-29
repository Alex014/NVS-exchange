CREATE TABLE IF NOT EXISTS `slots` (
  `id` INTEGER PRIMARY KEY,
  `slot_id` TEXT KEY NOT NULL,
  `addr` TEXT KEY,
  `name` TEXT UNIQUE,
  `value` TEXT  NOT NULL,
  `status` TEXT KEY CHECK( `status` IN ('GENERATED','UPDATED','PAYED') )   NOT NULL DEFAULT 'GENERATED',
  `created` INTEGER UNSIGNED KEY NOT NULL 
);
# eProtect self-hosted screenshots!
This is a simple PHP script which handles uploads for eProtect.

Instructions:
1. Download and drop this file into your webserver.
2. Create a MySQL database and set the credentials in the config, the DB schema can be found below.
3. Modify the in-game files to use your URL, this can be found in the config file.

DB Schema ( Run as SQL in PHPMyAdmin ):
```
CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `img_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `b64` mediumblob NOT NULL,
  `created` int(10) NOT NULL,
  `ip` varchar(39) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
```

CREATE TEMPORARY TABLE `vic_admin`.`param_id` (`id` INT(10) UNSIGNED NOT NULL UNIQUE)
 SELECT `id` FROM `vic_admin`.`parameter` p1
 JOIN (SELECT MIN(`id`) AS `minid`,`name`,COUNT(`name`) AS `cname`  FROM `vic_admin`.`parameter` GROUP BY `name` HAVING COUNT(`name`)>1) `p2`
 ON `p2`.`minid`<>`p1`.`id` AND `p2`.`name`=`p1`.`name`;
DELETE FROM `vic_admin`.`parameter` WHERE `id` IN (SELECT `id` FROM `vic_admin`.`param_id`);
DROP TABLE `vic_admin`.`param_id`;

ALTER TABLE `vic_admin`.`parameter` ADD UNIQUE (
`name`
);

CREATE TEMPORARY TABLE `vic_main`.`tmp_user`(`id` INT(10) UNSIGNED NOT NULL);

START TRANSACTION;

INSERT INTO `vic_main`.`tmp_user`(`id`)
 SELECT `user_id` FROM `vic_main`.`uzivatel`
 WHERE NOT `anonymous`=1 AND `nick` NOT IN ('klinger','vesely','stastny','bohal');

DELETE FROM `vic_main`.`uzivatel_im_data`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_main`.`point_account`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_main`.`webpay_order`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_main`.`limity_user`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_main`.`user_bank_account`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_main`.`user_bet_rating`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_main`.`user_search`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_main`.`user_tracking`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_admin`.`user_has_parameter`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_main`.`financial_transaction_history`
 WHERE `transaction_id` IN (
  SELECT `t`.`transaction_id` FROM `vic_main`.`financial_transaction` `t`
  JOIN `vic_main`.`tmp_user` `u` ON `t`.`user_id`=`u`.`id`
 )
 ORDER BY `transaction_id` DESC;

DELETE FROM `vic_main`.`financial_transaction`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_main`.`point_transaction`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

DELETE FROM `vic_main`.`uzivatel`
 WHERE `user_id` IN (SELECT `id` FROM `vic_main`.`tmp_user`);

COMMIT;

DROP TABLE `vic_main`.`tmp_user`;

CREATE TEMPORARY TABLE vic_main.tmp_user (user_id INT(11) UNSIGNED NOT NULL, account_id INT(11) UNSIGNED NULL);
START TRANSACTION;
INSERT INTO vic_main.tmp_user(user_id, account_id)
 SELECT user_id, NULL FROM vic_main.user_bank_account WHERE is_current=1 GROUP BY user_id HAVING COUNT(user_id)>1;
UPDATE vic_main.tmp_user tu
 SET tu.account_id=(SELECT ua.account_id FROM vic_main.user_bank_account ua WHERE ua.user_id=tu.user_id ORDER BY ua.account_id DESC LIMIT 1);
UPDATE vic_main.user_bank_account ua
 JOIN vic_main.tmp_user tu ON ua.user_id=tu.user_id
 SET ua.is_current=(ua.account_id=tu.account_id);
COMMIT;
DROP TABLE vic_main.tmp_user;

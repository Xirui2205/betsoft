-- renaming fn_get_money_* stored procedures

DELIMITER ;;
DROP FUNCTION IF EXISTS `fn_get_money_uzivatel2euro`;;
DROP FUNCTION IF EXISTS `fn_currency_user2central`;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_currency_user2central`(userId INTEGER(11), amount DECIMAL(10,2)) RETURNS decimal(10,2)
  DETERMINISTIC
BEGIN
  RETURN (
    SELECT (amount / k.kurz)
    FROM uzivatel u
    JOIN mena_kurz k
    ON u.mena_id = k.mena_id
    WHERE u.user_id = userId
    ORDER BY k.mena_kurz_id DESC
    LIMIT 1
  );
END;;
DROP FUNCTION IF EXISTS `fn_get_money_euro2uzivatel`;;
DROP FUNCTION IF EXISTS `fn_currency_central2user`;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_currency_central2user`(userId INTEGER(11), amount DECIMAL(10,2)) RETURNS decimal(10,2)
  DETERMINISTIC
BEGIN
  RETURN (
    SELECT (amount * k.kurz)
    FROM uzivatel u
    JOIN mena_kurz k
    ON u.mena_id=k.mena_id
    WHERE u.user_id=userId
    ORDER BY k.mena_kurz_id DESC
    LIMIT 1
  );
END;;
DELIMITER ;

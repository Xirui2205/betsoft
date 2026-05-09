START TRANSACTION;

INSERT INTO `vic_admin`.`parameter` (
 `id`,
 `name`,
 `value`,
 `is_host`,
 `is_branch`,
 `is_user`,
 `type`,
 `mandatory`,
 `is_admin`,
 `is_editable`,
 `description`
) VALUES
(NULL, 'entry.bonus.minRate.1', '1.77', '0', '0', '0', NULL , '1', '0', '1', 'Min.kurz tiketu (resp.kombinace) pro započítání do vst.bonusu (0=vždy)'),
(NULL, 'entry.bonus.minBets.1', '0', '0', '0', '0', NULL , '1', '0', '1', 'Min.počet příležitostí na tiketu (resp.kombinaci) pro započítání do vst.bonusu (0=vždy)'),
(NULL, 'entry.bonus.applicableTime.1', '6', '0', '0', '0', NULL , '1', '0', '1', 'Doba během které hráč muže získat vst.bonus (v měsících)'),
(NULL, 'entry.bonus.maxChangePerDay.1', '5000', '0', '0', '0', NULL , '1', '0', '1', 'Max.přírůstek zůstatku bonusu za den (centrální měna)'),
(NULL, 'entry.bonus.maxTotal.1', '2000', '0', '0', '0', NULL , '1', '0', '1', 'Max.celková uplatnitelná výše vst.bonusu (centrální měna)'),
(NULL, 'entry.bonus.minTotalStakesRatio.1', '10.0', '0', '0', '0', NULL , '1', '0', '1', 'Násobek vst.bonusu, který je třeba protočit pro jeho uplatnění');

COMMIT;

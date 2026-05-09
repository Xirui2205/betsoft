INSERT INTO `parameter` (`name`, `value`, `is_host`, `is_branch`, `is_user`, `type`, `mandatory`, `is_admin`, `is_editable`, `description`) VALUES
('entry.bonus.minRate.2', '2', 0, 0, 0, NULL, 1, 0, 1, 'Min.kurz tiketu (resp.kombinace) pro započítání do vst.bonusu (0=vždy)'),
('entry.bonus.minBets.2', '0', 0, 0, 0, NULL, 1, 0, 1, 'Min.počet příležitostí na tiketu (resp.kombinaci) pro započítání do vst.bonusu (0=vždy)'),
('entry.bonus.applicableTime.2', '6', 0, 0, 0, NULL, 1, 0, 1, 'Doba během které hráč muže získat vst.bonus (v měsících)'),
('entry.bonus.maxChangePerDay.2', '5000', 0, 0, 0, NULL, 1, 0, 1, 'Max.přírůstek zůstatku bonusu za den (centrální měna)'),
('entry.bonus.maxTotal.2', '5000', 0, 0, 0, NULL, 1, 0, 1, 'Max.celková uplatnitelná výše vst.bonusu (centrální měna)'),
('entry.bonus.minTotalStakesRatio.2', '10.0', 0, 0, 0, NULL, 1, 0, 1, 'Násobek vst.bonusu, který je třeba protočit pro jeho uplatnění');


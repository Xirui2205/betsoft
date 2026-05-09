INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`,
`note`) VALUES
 ('7213', 1 , 'Indexes for ticket_statistics (mantis:413)');

ALTER TABLE `ticket_statistics_payout` ADD INDEX i__ticket_statistics_payout__hour ( `hour` );
ALTER TABLE `ticket_statistics_cashflow` ADD INDEX i__ticket_statistics_cashflow__hour ( `hour` );

ALTER TABLE `ticket_statistics_payout` ADD INDEX i__ticket_statistics_payout__user_id ( `user_id` );
ALTER TABLE `ticket_statistics_cashflow` ADD INDEX i__ticket_statistics_cashflow__user_id ( `user_id` );

ALTER TABLE `ticket_statistics_payout` ADD INDEX i__ticket_statistics_payout__host_id ( `host_id` );
ALTER TABLE `ticket_statistics_cashflow` ADD INDEX i__ticket_statistics_cashflow__host_id ( `host_id` );

ALTER TABLE `ticket_statistics_payout` ADD INDEX i__ticket_statistics_payout__branch_id ( `branch_id` );
ALTER TABLE `ticket_statistics_cashflow` ADD INDEX i__ticket_statistics_cashflow__branch_id ( `branch_id` );

ALTER TABLE `ticket_statistics_payout` ADD INDEX i__ticket_statistics_payout__sport_id ( `sport_id` );
ALTER TABLE `ticket_statistics_cashflow` ADD INDEX i__ticket_statistics_cashflow__sport_id ( `sport_id` );

ALTER TABLE `ticket_statistics_payout` ADD INDEX i__ticket_statistics_payout__udalost_id ( `udalost_id` );
ALTER TABLE `ticket_statistics_cashflow` ADD INDEX i__ticket_statistics_cashflow__udalost_id ( `udalost_id` );

ALTER TABLE `ticket_statistics_payout` ADD INDEX i__ticket_statistics_payout__typ_id ( `typ_id` );
ALTER TABLE `ticket_statistics_cashflow` ADD INDEX i__ticket_statistics_cashflow__typ_id ( `typ_id` );

ALTER TABLE `ticket_statistics_payout` ADD INDEX i__ticket_statistics_payout__type ( `type` );
ALTER TABLE `ticket_statistics_cashflow` ADD INDEX i__ticket_statistics_cashflow__type ( `type` );

ALTER TABLE `ticket_statistics_payout` ADD INDEX i__ticket_statistics_payout__location_id ( `location_id` );
ALTER TABLE `ticket_statistics_cashflow` ADD INDEX i__ticket_statistics_cashflow__location_id ( `location_id` );

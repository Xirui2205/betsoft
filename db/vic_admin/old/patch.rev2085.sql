-- !!! IPs 10.6.*.* are for DEVEL environments, change to 10.81.*.* for TESTING environments

START TRANSACTION;

DELETE FROM `vic_admin`.`host` WHERE `id`=1;

INSERT INTO `vic_admin`.`host` (`id`, `branch_id`, `name`, `allowed`, `version`, `is_online`, `hardware`, `display`, `printer`, `ip`, `win_sn`, `provider_dns`, `provider_gateway`, `provider_ip`, `provider_username`, `provider_password`, `vic_email`, `vic_email_password`, `vic_admin_password`, `vic_employee_password_1`, `vic_employee_password_2`, `note`) VALUES
(1, 1, 'Internet', 1, '1', 0, 'Unknown', 'Unknown', 'Unknown', '10.6.6.5', 'Linux', NULL, NULL, NULL, NULL, NULL, 'info@compbet.com', NULL, NULL, NULL, NULL, 'Web machine');

INSERT INTO `vic_admin`.`host` (`id`, `branch_id`, `name`, `allowed`, `version`, `is_online`, `hardware`, `display`, `printer`, `ip`, `win_sn`, `provider_dns`, `provider_gateway`, `provider_ip`, `provider_username`, `provider_password`, `vic_email`, `vic_email_password`, `vic_admin_password`, `vic_employee_password_1`, `vic_employee_password_2`, `note`) VALUES
(100, 2, 'Host 1 na pobočce ID 2', 1, '1', 0, 'Unknown', 'Unknown', 'Unknown', '10.6.0.5', 'Linux', NULL, NULL, NULL, NULL, NULL, 'kasa1@compbet.com', NULL, NULL, NULL, NULL, 'Pro testovací účely');

COMMIT;

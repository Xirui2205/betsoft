START TRANSACTION;

INSERT INTO `vic_main`.`static_page_layout` (`layout_id`, `layout_name`, `layout_file`, `required_data`) VALUES
(1, 'default', 'default.phtml', 'leftRightNews'),
(2, 'popup', 'popup.phtml', '');

COMMIT;

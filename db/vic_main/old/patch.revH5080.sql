START TRANSACTION;

INSERT INTO `database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H5080', 0, 'vyprsela platnost prihlaseni - html');

UPDATE static_page SET page_content = '<div class="content_block">
<p>Vypr&scaron;ela platnost přihl&aacute;&scaron;en&iacute;, byl(a) jste automaticky odhl&aacute;&scaron;en(a).</p>
<br />
</div>' WHERE page_id = 13;

COMMIT;
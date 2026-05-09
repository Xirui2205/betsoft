START TRANSACTION;
UPDATE vic_main.seo_url SET url=CONCAT('/', url) WHERE NOT(url LIKE '/%');
UPDATE vic_main.seo_url SET url=CONCAT(url, '/') WHERE NOT(url LIKE '%/');
COMMIT;

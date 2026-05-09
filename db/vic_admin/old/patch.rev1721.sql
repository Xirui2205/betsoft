START TRANSACTION;

UPDATE vic_admin.sekce SET controller=NULL, action=NULL WHERE sekce_id=40;

COMMIT;

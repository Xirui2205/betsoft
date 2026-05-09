start transaction;

UPDATE preklady `preklady` SET `text` = 'Tiket nebyl přijat.' WHERE `index_pole` = 'ticket_notprove' AND `lang_id` = 1;
UPDATE preklady `preklady` SET `text` = 'Ticket was not accepted.' WHERE `index_pole` = 'ticket_notprove' AND `lang_id` = 2;
UPDATE preklady `preklady` SET `text` = 'Tiket nebol prijatý.' WHERE `index_pole` = 'ticket_notprove' AND `lang_id` = 16;

commit;

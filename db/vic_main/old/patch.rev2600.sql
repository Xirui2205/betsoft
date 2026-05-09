-- removing FK vic_main.ticket.zrusil_bookmaker_id referencing vic_main.bookmaker.bookmaker_id
ALTER TABLE `vic_main`.`ticket` DROP FOREIGN KEY `FK_ticket_2` ;

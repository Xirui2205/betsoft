START TRANSACTION;

INSERT INTO `vic_main`.`database_patch`(`revision`, `not_reapplicable`, `note`) VALUES
 ('H1304', NULL, 'New content for NewUserRegistration email (mantis:1304)');

REPLACE INTO `vic_main`.`preklady`(`lang_id`, `index_pole`, `short_text`, `text`, `translate`) VALUES
 (1, 'mail_reg_detail_html', NULL, 'Gratulujeme k registraci Vašeho hráčského účtu u sázkové kanceláře BetService!<br>
<br>
<strong>
  Pro dokončení Vašeho hráčského účtu navštivte jednu z našich TOP poboček,
  kde Vám obsluha vytiskne vyplněný registrační formulář a zároveň aktivuje
  Váš účet. Pro ověření totožnosti si, prosím, vezměte s sebou občanský průkaz.
</strong><br>
<br>
<strong>Seznam TOP poboček najdete <a href="{{top_branches_url}}" style="color: yellow;">zde</a></strong><br>
<br>
Pokud by byla TOP pobočka od Vašeho místa bydliště příliš vzdálená,
můžete navštívit nejbližší pobočku <a href="{{nearest_branch_url}}" style="color: yellow;">zde</a>.<br>
<br>
<span style="font-weight:bold;font-size:1.2em;">Vaše údaje</span><br>
<br>
Vaše uživatelské jméno je: <strong>{{username}}</strong><br>
<br>
Děkujeme Vám za registraci a přejeme hodně štěstí ve hře.<br>
<br>', 0),

 (1, 'mail_reg_detail', NULL, 'Gratulujeme k registraci Vašeho hráčského účtu u sázkové kanceláře BetService!
 
Pro dokončení Vašeho hráčského účtu navštivte jednu z našich TOP poboček,
kde Vám obsluha vytiskne vyplněný registrační formulář a zároveň aktivuje
Váš účet. Pro ověření totožnosti si, prosím, vezměte s sebou občanský průkaz.

Seznam TOP poboček najdete zde: {{top_branches_url}}

Pokud by byla TOP pobočka od Vašeho místa bydliště příliš vzdálená,
můžete navštívit nejbližší pobočku zde: {{nearest_branch_url}}

Vaše uživatelské jméno je: {{username}}
', 0);

COMMIT;

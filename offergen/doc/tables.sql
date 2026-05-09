insert into offergen_typ_tiskopisu (typ_tiskopisu_nazev, obnova1, obnova2, mazat)
values ('nabidka', 300, 600, 0);

insert into offergen_typ_tiskopisu (typ_tiskopisu_nazev, obnova1, obnova2, mazat)
values ('vysledky', 300, 600, 0);


-----------------------------------------

create table offergen_typ_tiskopisu
(
id_typ_tiskopisu int not null auto_increment,
typ_tiskopisu_nazev varchar(30) not null default '',
obnova1 int,
obnova2 int,
mazat int,
primary key (id_typ_tiskopisu)
);

------------------------------------------

create table offergen_tiskopis
(
id_tiskopis int not null auto_increment,
id_typ_tiskopisu int not null,
parametry varchar(100),
pozadavek timestamp not null default CURRENT_TIMESTAMP,
pripraven timestamp,
pocet_stran int,
jazyk int,
primary key (id_tiskopis)
);

------------------------------------------




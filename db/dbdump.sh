#!/bin/bash

if [[ "0" == "$#" ]]; then
	echo "Usage: $0 db_name"
	exit 1
fi

DBNAME="$1";
DBDIR="./$DBNAME";
DBDIRDEVEL="$DBDIR/devel";
DBUSER='root';
DBPASSWD='sumperk';

if [[ -d "$DBDIR" ]]; then
	rm -rf "$DBDIR"
fi
mkdir "$DBDIR"
mkdir "$DBDIRDEVEL"

mysqldump -h localhost -u "$DBUSER" "-p$DBPASSWD" --no-data --routines --triggers -r "$DBDIR/db.struc.sql" "$DBNAME"

while read T;
do
	if [[ ! -z "$T" ]]; then
		mysqldump -h localhost -u "$DBUSER" "-p$DBPASSWD" --no-create-info -r "$DBDIR/table.$T.data.sql.tmp" "$DBNAME" "$T"
		cat "$DBDIR/table.$T.data.sql.tmp" | sed 's/),(/),\n(/g' > "$DBDIR/table.$T.data.sql"
		rm "$DBDIR/table.$T.data.sql.tmp"
	fi
done < <(cat ./${DBNAME}_tables);

while read T;
do
	if [[ ! -z "$T" ]]; then
		mysqldump -h localhost -u "$DBUSER" "-p$DBPASSWD" --no-create-info -r "$DBDIRDEVEL/table.$T.data.sql.tmp" "$DBNAME" "$T"
		cat "$DBDIRDEVEL/table.$T.data.sql.tmp" | sed 's/),(/),\n(/g' > "$DBDIRDEVEL/table.$T.data.sql"
		rm "$DBDIRDEVEL/table.$T.data.sql.tmp"
	fi
done < <(cat ./${DBNAME}_tables_devel);

exit 0

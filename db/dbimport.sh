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

echo "Starting to import structure..."
mysql -h localhost -u "$DBUSER" "-p$DBPASSWD" "$DBNAME" <  "$DBDIR/db.struc.sql"
echo "Importing structure finished. Starting to import data from tables..."

while read T;
do
	if [[ ! -z "$T" ]]; then
		mysql -h localhost -u "$DBUSER" "-p$DBPASSWD" "$DBNAME" < "$DBDIR/table.$T.data.sql"
		echo "$T"
	fi
done < <(cat ./${DBNAME}_tables);

while read T;
do
	if [[ ! -z "$T" ]]; then
		mysql -h localhost -u "$DBUSER" "-p$DBPASSWD" "$DBNAME" < "$DBDIRDEVEL/table.$T.data.sql"
		echo "$T"
	fi
done < <(cat ./${DBNAME}_tables_devel);

exit 0

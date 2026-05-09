#!/bin/bash
# - zapíše nastavení souborů config_local.php v adresářích:
#   admin, betting-service, bimportd, confirmd, cronjob a web
# - prostředí se určí podle názvu nadřazeného adresáře

configFileName='config_local-devel_local.php';
if [[ $PWD == *kuchar23.vshosting.cz* ]]
then
	configFileName='config_local_production.php';
elif [[ $PWD == *kuchar23-test.vshosting.cz* ]]
then
	configFileName='config_local_testing.php';
fi

dirNames=('admin' 'betting-service' 'bimportd' 'confirmd' 'cronjob' 'web');
for dirName in ${dirNames[*]}
do
	cat $dirName'/'$configFileName >  $dirName'/config_local.php';
done
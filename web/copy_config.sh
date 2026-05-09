#!/bin/bash
PS3='Vaše volba: '
options=("config_local-devel_local.php" "config_local_testing.php" "config_local_production.php" "Zavřít")
file='config_local.php'

echo -e "\nZvolte konfigurační soubor, který chcete použít pro nastavení:"
select opt in "${options[@]}"
do
    case $opt in
        ${options[0]})
	    cp ${options[0]} ${file}
            echo ${options[0]}" => "${file}
            break;;
        ${options[1]})
	    cp ${options[1]} ${file}
            echo ${options[1]}" => "${file}
            break;;
        ${options[2]})
	    cp ${options[2]} ${file}
            echo ${options[2]}" => "${file}
            break;;
        ${options[3]})
            break;;
        *) echo špatná volba;;
    esac
done

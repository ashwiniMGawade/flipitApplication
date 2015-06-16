#!/bin/sh

# ------------------------------ #
#       Define variables         #
# ------------------------------ #

locales=(se it br za us my ca no jp dk uk id at tr fi sg au ru)

rootpath=/var/www/flipit.com

prodpath=/home/flipit

# ------------------------------ #
#           DB import            #
# ------------------------------ #

echo "#### Resetting DB's"

db_user="root"
db_user_pass="1aadfbd44a"
dir_to_db="$rootpath/dev"
START=$(date +%s)

for locale in "${locales[@]}"
do
    mysql -u "${db_user}" -p"${db_user_pass}" --batch --execute="CREATE DATABASE flipit_$locale;"
    mysql --max_allowed_packet=2000M -u "${db_user}" -p"${db_user_pass}" "flipit_$locale" < "${dir_to_db}/backup_site_default.sql"
    mysql -u "${db_user}" -p"${db_user_pass}" --execute="GRANT ALL PRIVILEGES  ON flipit_$locale.* TO 'flipit_usr'@'localhost' IDENTIFIED BY 'amPQxCJwP6v7HX8w' WITH GRANT OPTION;"
done

printf "#### DB's reset"
END=$(date +%s)
DIFF=$(( $END - $START ))
MIN=$(( $DIFF / 60 ))
SEC=$(( $DIFF - ($MIN * 60) ))
printf " in %d min %02d sec\n" $MIN $SEC


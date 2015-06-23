locales=(at au be br ca ch de dk es fi fr id in it jp my no pl pt se sg tr uk us za)

# Unzip DB's
#gzip -d dev/backup_site_kc.sql.gz
#gzip -d dev/backup_user.sql.gz

db_user="root"
db_user_pass="password"
dir_to_db="/c/wamp/www/kortingscode.nl/kortingscode.nl/application/migration/dev"
START=$(date +%s)

# dumb existing db's
mysql -u "${db_user}" -p"${db_user_pass}" --execute="DROP DATABASE kortingscode_site;"
mysql -u "${db_user}" -p"${db_user_pass}" --execute="DROP DATABASE kortingscode_user;"
    # create db's
mysql -u "${db_user}" -p"${db_user_pass}" --batch --execute="CREATE DATABASE kortingscode_site;"
mysql -u "${db_user}" -p"${db_user_pass}" --batch --execute="CREATE DATABASE kortingscode_user;"
#import new db's
mysql --max_allowed_packet=2000M -u "${db_user}" -p"${db_user_pass}" "kortingscode_site" < "${dir_to_db}/backup_site_kc.sql"
mysql --max_allowed_packet=2000M -u "${db_user}" -p"${db_user_pass}" "kortingscode_user" < "${dir_to_db}/backup_user.sql"
#grant
mysql -u "${db_user}" -p"${db_user_pass}" --execute="GRANT ALL PRIVILEGES  ON kortingscode_site.* TO 'flipit_usr'@'localhost' IDENTIFIED BY 'amPQxCJwP6v7HX8w' WITH GRANT OPTION;"
mysql -u "${db_user}" -p"${db_user_pass}" --execute="GRANT ALL PRIVILEGES  ON kortingscode_user.* TO 'flipit_usr'@'localhost' IDENTIFIED BY 'amPQxCJwP6v7HX8w' WITH GRANT OPTION;"

for locale in "${locales[@]}"
do
            #gzip -d dev/backup_site_$locale.sql.gz
            mysql -u "${db_user}" -p"${db_user_pass}" --execute="DROP DATABASE flipit_$locale;"
            mysql -u "${db_user}" -p"${db_user_pass}" --batch --execute="CREATE DATABASE flipit_$locale;"
            mysql --max_allowed_packet=2000M -u "${db_user}" -p"${db_user_pass}" "flipit_$locale" < "${dir_to_db}/backup_site_$locale.sql"
            mysql -u "${db_user}" -p"${db_user_pass}" --execute="GRANT ALL PRIVILEGES  ON flipit_$locale.* TO 'flipit_usr'@'localhost' IDENTIFIED BY 'amPQxCJwP6v7HX8w' WITH GRANT OPTION;"
done

printf "#### DB's reset"
END=$(date +%s)
DIFF=$(( $END - $START ))
MIN=$(( $DIFF / 60 ))
SEC=$(( $DIFF - ($MIN * 60) ))
printf " in %d min %02d sec\n" $MIN $SEC

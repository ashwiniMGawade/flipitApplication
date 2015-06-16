locales=(th id)

rootpath=/var/www/flipit.com/current/Database

scipt_name="KC_empty_db"

db_user="root"
db_user_pass="password"

dir_to_db="$rootpath"

START=$(date +%s)

for locale in "${locales[@]}"
do
    	mysql -u "${db_user}" -p"${db_user_pass}" --batch --execute="CREATE DATABASE flipit_$locale;"
        mysql --max_allowed_packet=2000M -u "${db_user}" -p"${db_user_pass}" "flipit_$locale" < "${dir_to_db}/${scipt_name}.sql"
        mysql -u "${db_user}" -p"${db_user_pass}" --execute="GRANT ALL PRIVILEGES  ON flipit_$locale.* TO '${db_user}'@'localhost' IDENTIFIED BY '${db_user_pass}' WITH GRANT OPTION;"
done

printf "#### DB's reset"
END=$(date +%s)
DIFF=$(( $END - $START ))
MIN=$(( $DIFF / 60 ))
SEC=$(( $DIFF - ($MIN * 60) ))
printf " in %d min %02d sec\n" $MIN $SEC
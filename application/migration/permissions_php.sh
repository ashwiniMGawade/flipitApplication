# @author Daniel Bakker
# this script sets the files and folders right for a given dir and user
# call this script with 3 params (user) (www_user) (path_to_dir /var/www/vhosts/danielbakker.nl/httpdocs)
# permissions_php.sh danielbakker _www:staff /home/kortingscode.nl/kortingscode.nl

# argument passed to script?
if [ $# -eq 3 ]; then
    user=$1
    webuser=$2
    web="$3"

else
    echo "call this script with 3 params (user) (www_user) (path_to_dir /var/www/vhosts/danielbakker.nl/httpdocs)"
    echo "Listing users on this server"
    cat /etc/passwd
    exit
fi

if [ -d "$web" ]; then

    echo "chown -R $user $web/"
    sudo chown -R $user "$web/"

    echo "set 644 (rw-r--r--) on files"
    sudo find  "$web/" -type f -exec chmod 644 {} \;

    echo "set 755 (rwxr-xr-x) on directories:"
    sudo find "$web/" -type d -exec chmod 755 {} \;

    # change owner of some website directories to apache user
    array=($web/public/language/ $web/public/tmp $web/public/cache $web/public/excels $web/public/images/upload $web/public/language/ $web/public/tmp $web/public/cache $web/public/excels $web/public/images/upload $web/public/be/ $web/public/ch/ $web/public/de/ $web/public/es/ $web/public/fr/ $web/public/in/ $web/public/pl/ )
    for folder in "${array[@]}"
    do
        if [ -d "$folder" ]; then
            echo "sudo chown -R $webuser $folder"
            sudo chown -R $webuser $folder
            echo "sudo chmod -R 775 $folder"
            #sudo chmod -R 775 $folder
        fi
    done
    
else
    echo "dir $web not found!"    
    exit
fi

sudo chown apache:flipit /home/flipit/public_html/public/be/js/back_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/ch/js/back_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/de/js/back_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/es/js/back_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/fr/js/back_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/in/js/back_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/pl/js/back_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/js/back_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/be/js/back_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/ch/js/back_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/de/js/back_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/es/js/back_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/fr/js/back_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/in/js/back_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/pl/js/back_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/js/back_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/be/js/front_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/ch/js/front_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/de/js/front_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/es/js/front_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/fr/js/front_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/in/js/front_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/pl/js/front_end/gtData.js
sudo chown apache:flipit /home/flipit/public_html/public/js/front_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/be/js/front_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/ch/js/front_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/de/js/front_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/es/js/front_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/fr/js/front_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/in/js/front_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/pl/js/front_end/gtData.js
sudo chmod 775 /home/flipit/public_html/public/js/front_end/gtData.js
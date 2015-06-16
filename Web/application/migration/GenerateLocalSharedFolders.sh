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

echo "#### Copying new locale folders"

for locale in "${locales[@]}"
do
	cp -R shared/it shared/$locale
done


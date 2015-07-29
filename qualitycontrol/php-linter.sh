#!/bin/bash

for file in `find ./KC-PROD-BUILD/flipit_application/`
do
    EXTENSION="${file##*.}"

    if [ "$EXTENSION" == "php" ]
    then
        RESULTS=`php -l $file`

        if [ "$RESULTS" != "No syntax errors detected in $file" ]
        then
            echo $RESULTS
        fi
    fi
done
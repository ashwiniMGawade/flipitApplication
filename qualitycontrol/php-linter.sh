#!/bin/bash

for file in `find ./`
do
    EXTENSION="${file##*.}"

    if [ "$EXTENSION" = "php" ]
    then
        echo $EXTENSION
        RESULTS=`php -l $file`

        if [ "$RESULTS" != "No syntax errors detected in $file" ]
        then
            echo $RESULTS
        fi
    fi
done
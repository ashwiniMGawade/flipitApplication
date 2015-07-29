#!/bin/bash

for file in `find ./`
do
    EXTENSION="${file##*.}"
    EXITSTATUS=0

    if [ "$EXTENSION" = "php" ]
    then
        echo $EXTENSION
        RESULTS=`php -l $file`

        if [ "$RESULTS" != "No syntax errors detected in $file" ]
        then
            echo $RESULTS
            EXITSTATUS=1
        fi
    fi
done

exit $EXITSTATUS
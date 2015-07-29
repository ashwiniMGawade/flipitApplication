#!/bin/bash

EXITSTATUS=0

for file in `find ./`
do
    EXTENSION="${file##*.}"

    if [ "$EXTENSION" = "php" ]
    then
        RESULTS=`php -l $file`

        if [ "$RESULTS" != "No syntax errors detected in $file" ]
        then
            echo $RESULTS
            EXITSTATUS=$((EXITSTATUS=1))
        fi
    fi
done
echo $EXITSTATUS
exit $EXITSTATUS
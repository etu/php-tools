#!/bin/sh

. ${0%/*}/config.sh

####
# Print log files
##
print_log='false'
if [ "$1" = '--no-log' ]; then
    print_log='true'
fi

###
# Check code style in both PSR-1 and PSR-2 for \1, and
##
checkStyle() {
    DIR=$1
    ARG=$2

    ###
    # PSR-1
    ##
    LOG1=$LOG_DIR/$DIR'_psr1_checkstyle.xml'

    $PHP_CODE_SNIFFER --standard=PSR1 --report=checkstyle $DIR $ARG > $LOG1
    if grep -q file $LOG1; then
        if [ $print_log = "true" ]; then
            cat $LOG1
        else
            echo 'PSR-1 problems found, logs in '$LOG1
        fi
    fi

    ###
    # PSR-2
    ##
    LOG2=$LOG_DIR/$DIR'_psr2_checkstyle.xml'

    $PHP_CODE_SNIFFER --standard=PSR2 --report=checkstyle $DIR $ARG > $LOG2
    if grep -q file $LOG2; then
        if [ $print_log = "true" ]; then
            cat $LOG2
        else
            echo 'PSR-2 problems found, logs in '$LOG2
        fi
    fi
}

checkStyle tests
checkStyle src

#!/bin/sh

. ${0%/*}/config.sh

LOG_FILE=$LOG_DIR'/lint.txt'

####
# Print log files
##
print_log='false'
if [ "$1" = '--no-log' ]; then
    print_log='true'
fi

###
# Function to wrap usage of Lint
##
lint() {
    DIR=$1

    ${PARALLEL_LINT} ${DIR} > ${LOG_FILE}
    if ! grep 'no syntax error found' ${LOG_FILE}; then
        echo 'Found lint error, see the lint log file'

        if [ $print_log = 'true' ]; then
            cat $LOG_FILE
        fi

        exit 2
    fi
}

###
# Lint all PHP files in the source tree and pipe output to the log file
##
lint tests
lint src

exit 0

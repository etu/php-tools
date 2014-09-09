#!/bin/sh

. ${0%/*}/config.sh

$PHPUNIT -c $UNITTEST_DIR/phpunit.xml \
    --log-junit $LOG_DIR/unittest_report.xml \
    --coverage-html $LOG_DIR/unittest_coverage \
    --coverage-clover $LOG_DIR/unittest_coverage.xml $UNITTEST_DIR

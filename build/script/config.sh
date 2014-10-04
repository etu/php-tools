#!/bin/sh

###
# Globaly used directories
##
PROJECT_DIR=${0%/*}/../../
VENDOR_DIR=$PROJECT_DIR/vendor
TEST_DIR=$PROJECT_DIR/tests
LOG_DIR=$PROJECT_DIR/build/log

###
# Used by: prepare.sh
##
COMPOSER=$PROJECT_DIR/composer.phar

###
# Used by: lint.sh
##
PARALLEL_LINT=$VENDOR_DIR/bin/parallel-lint

###
# Used by: psrCompliance.sh
##
PHP_CODE_SNIFFER="${VENDOR_DIR}/bin/phpcs --encoding=UTF-8"

###
# Used by: unittest.sh
##
UNITTEST_DIR=$TEST_DIR/unit
PHPUNIT=$VENDOR_DIR/bin/phpunit

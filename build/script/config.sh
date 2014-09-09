#!/bin/sh

###
# Globaly used directories
##
SCRIPT_DIR=${0%/*}
VENDOR_DIR=$SCRIPT_DIR/../../vendor
TEST_DIR=$SCRIPT_DIR/../../tests
LOG_DIR=$SCRIPT_DIR/../log

###
# Used by: prepare.sh
##
COMPOSER=$SCRIPT_DIR/../../composer.phar

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

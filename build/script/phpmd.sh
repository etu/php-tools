#!/bin/sh

. ${0%/*}/config.sh

$PHPMD $PROJECT_DIR/src,$PROJECT_DIR/tests text codesize,cleancode

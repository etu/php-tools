#!/bin/sh

. ${0%/*}/config.sh

# Install composer deps
if [ "$1" == '--no-dev' ]; then
    echo 'Installing production dependencies'

    $COMPOSER install --no-dev --no-interaction || {
        echo 'Composer failed to install production dependencies';
        exit 1;
    }
else
    echo 'Installing development and production dependencies'

    $COMPOSER install --no-interaction || {
        echo 'Composer failed to install development and production dependencies';
        exit 1;
    }
fi

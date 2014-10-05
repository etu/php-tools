# Get root dir of project
PROJECT_DIR := $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
LOG_DIR := $(PROJECT_DIR)/log

all:
	make lint
	make phpcs
	make phpmd
	make unittest

unittest:
	$(PROJECT_DIR)/vendor/bin/phpunit                               \
		--configuration   $(PROJECT_DIR)/tests/unit/phpunit.xml \
		--log-junit       $(LOG_DIR)/unittest_report.xml        \
		--coverage-html   $(LOG_DIR)/unittest_coverage          \
		--coverage-clover $(LOG_DIR)/unittest_coverage.xml

phpmd:
	$(PROJECT_DIR)/vendor/bin/phpmd $(PROJECT_DIR)/src,$(PROJECT_DIR)/tests text codesize,cleancode

phpcs:
	$(PROJECT_DIR)/vendor/bin/phpcs --standard=PSR1 $(PROJECT_DIR)/src $(PROJECT_DIR)/tests
	$(PROJECT_DIR)/vendor/bin/phpcs --standard=PSR2 $(PROJECT_DIR)/src $(PROJECT_DIR)/tests

lint:
	$(PROJECT_DIR)/vendor/bin/parallel-lint $(PROJECT_DIR)/src $(PROJECT_DIR)/tests

clean:
	rm -r $(LOG_DIR)/*

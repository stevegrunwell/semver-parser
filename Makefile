.PHONY: *

build:
	composer install

# Roll-up all of the test commands
test: unit-tests standards static-analysis

# Check coding standards with PHP_CodeSniffer
standards:
	vendor/bin/phpcs

# Static code analysis via PHPStan
static-analysis:
	vendor/bin/phpstan analyse src tests --level=7

# Generate test coverage, which will be written to tests/coverage
test-coverage:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html=tests/coverage
	@open tests/coverage/index.html 2> /dev/null \
		|| echo "📊 Coverage has been written to ${CURDIR}/tests/coverage"

# Execute the PHPUnit test suite
unit-tests:
	vendor/bin/phpunit --testdox --colors=always

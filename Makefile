DIR?=${CURDIR}

all: deps test ecs-check

deps:
	composer install --ignore-platform-reqs

test:
	./vendor/bin/phpunit

ecs-check:
	./vendor/bin/ecs check .

ecs-fix:
	./vendor/bin/ecs check --fix .

changelog-dump:
	php bin/console changelog:generate

changelog-update:
	php bin/console changelog:generate --write
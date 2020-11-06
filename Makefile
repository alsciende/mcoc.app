test: deptrac schema phpunit phpstan

phpstan:
	vendor/bin/phpstan analyse src tests --level=5 --memory-limit=128M

phpunit: reset-test
	vendor/bin/phpunit

deptrac:
	deptrac

schema:
	bin/console d:s:v

reset:
	bin/console d:d:d --force
	bin/console d:d:c
	bin/console d:m:m -n

reset-test:
	bin/console d:d:d --force --env=test
	bin/console d:d:c --env=test
	bin/console d:m:m -n --env=test

import:
	bin/console app:import:champions:hook
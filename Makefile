up:
	docker compose up -d --build

down:
	docker compose down

restart:
	docker compose down
	docker compose up -d --build

bash:
	docker compose exec app bash

migrate:
	docker compose exec app php artisan migrate

fresh:
	docker compose exec app php artisan migrate:fresh --seed

test:
	docker compose exec app php artisan test

pint:
	docker compose exec app ./vendor/bin/pint

phpstan:
	docker compose exec app ./vendor/bin/phpstan analyse --memory-limit=1G

routes:
	docker compose exec app php artisan route:list

logs:
	docker compose logs -f app nginx postgres redis

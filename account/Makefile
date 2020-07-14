build: ## build develoment environment
	cp .env.example .env
	composer install
	docker-compose build
	docker-compose run --rm webchat composer install
	docker-compose run --rm webchat php artisan key:generate
	npm i
serve:
	docker-compose up -d
stop:
	docker-compose stop
down:
	docker-compose down -v
migrate:
	docker-compose run --rm webchat php artisan migrate
	docker-compose run --rm webchat php artisan db:seed
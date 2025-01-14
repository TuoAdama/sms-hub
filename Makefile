.PHONY: deploy install

include .env.local
export

install:
	composer install
	php bin/console doctrine:schema:update --complete --force
	php bin/console c:c
	yarn encore dev --force


deploy:
	git push deploy main
	echo $(SERVER_USERNAME)@$(SERVER_HOST) "cd $(SERVER_APP_ROOT) && git pull origin main && make install"

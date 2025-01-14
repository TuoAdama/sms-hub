include .env.local

.PHONY: install
install:
	composer install
	php bin/console doctrine:schema:update --complete --force
	php bin/console c:c


.PHONY: deploy
deploy:
	git push deploy
	ssh -v $(SERVER_USERNAME)@$(SERVER_HOST) "cd $(SERVER_APP_ROOT) && make install"

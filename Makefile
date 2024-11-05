compose:
	docker compose up

compose-d:
	docker compose up -d

exec-db:
	docker exec -it postgres /bin/sh
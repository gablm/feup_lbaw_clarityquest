compose:
	docker compose up

compose-d:
	docker compose up -d

exec-db:
	docker exec -it lbaw24125-postgres-1 /bin/sh
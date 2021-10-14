## Insomnia

Insomnia is a forum where people can start new threads or participate in existing threads. The forum members have their own profiles, where they can share their personal information and their activity on the forum. Members can also post on other member's profiles or leave comments on the existing posts. If members want to communicate with each other privately, they can start a conversation.

## Requirements

-   **Docker**

## Installation

1. Clone this repository `git clone https://github.com/Orest-Divintari/insomnia.git`
2. Run `cd insomnia` to go to the app directory.
3. Run `mv .env.example .env` to rename the `.env.example` file to `.env`.
4. Run `cp .env ./docker` to Copy the `.env` file in the docker folder.
5. Run `cd docker` to go to the docker folder.
6. Run `docker-compose run --rm composer install` to install the dependencies.
7. Run `docker-compose up -d` to build and start the docker containers.
8. Run `docker-compose run --rm artisan storage:link --relative` to create a link for the public files.
9. Run `docker-compose run --rm artisan migrate:fresh --seed` to run the migrations and seed the database.
10. Visit the app in your browser by visiting **[127.0.0.1:82](http://127.0.0.1:82)**.

If you seeded the database, you can login with the following credentials:

-   _username_ : john
-   _password_ : example123

## Commands

> You don't have to install anything to run Composer, Artisan and NPM commands.These commands are executed using docker containers and are destroyed after the execution.

| Command                                          | Description                           |
| ------------------------------------------------ | ------------------------------------- |
| `docker-compose run --rm composer [command]`     | Run composer commands                 |
| `docker-compose run --rm npm [command]`          | Run npm commands                      |
| `docker-compose run --rm artisan [command]`      | Run artisan commands                  |
| `docker-compose run --rm artisan redis:flushall` | Clear redis cache                     |
| `docker-compose run --rm artisan scout:flushall` | Remove all records from Elasticsearch |

## Ports

| Container         | Exposed port |
| ----------------- | ------------ |
| **nginx**         | `:82`        |
| **mysql**         | `:3306`      |
| **redis**         | `:6379`      |
| **elasticsearch** | `:9200`      |

## Data persistence

By default the MySQL and Redis data will not be deleted after you remove the docker containers. In order to delete the data after the containers are removed, you have to do the following:

Go to the `docker-compose.yml` file and:

1. In order to delete the MySQL data, remove following line `- ./data/mysql:/var/lib/mysql`.
2. In order to delete the Redis data, remove the following line `- ./data/redis:/data`.

## License

The MIT License. Please see [the license file](LICENSE.md) for more information.

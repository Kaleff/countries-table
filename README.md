# Countries Table

A Laravel + Livewire app that imports European Economic Area (EEA) countries from an
external API and displays them in a sortable, paginated table. Runs on
[Laravel Sail](https://laravel.com/docs/sail) (Docker).

## Requirements

- [Docker](https://docs.docker.com/get-docker/) with Docker Compose

## Launch the application

1. **Start the containers** (app, MySQL, phpMyAdmin):

   ```bash
   docker compose up -d
   ```

2. **Open a shell inside the app container:**

   ```bash
   docker exec -it countries-table-laravel.test-1 bash
   ```

3. **Generate the application key:**

   ```bash
   php artisan key:generate
   ```

4. **Run the database migrations:**

   ```bash
   php artisan migrate
   ```

> All `php`, `artisan`, and `composer` commands must be run **inside** the
> `laravel.test` container (steps 3–4 assume you ran step 2 first). You can also run
> them without an interactive shell, e.g. `docker compose exec laravel.test php artisan migrate`.

## Links

- **Countries table:** [http://localhost/countries](http://localhost/countries)
- **phpMyAdmin:** [http://localhost:8080](http://localhost:8080)

## Importing data

The Countries page has **Import** and **Truncate** buttons that call the API
(`POST` / `DELETE /api/countries`) — equivalent to the `countries:import` and
`countries:truncate` console commands.

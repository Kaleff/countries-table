# Countries Table

A Laravel + Livewire app that imports European Economic Area (EEA) countries from an
external API and displays them in a sortable, paginated table. Runs on
[Laravel Sail](https://laravel.com/docs/sail) (Docker).

## Requirements

- [Docker](https://docs.docker.com/get-docker/) with Docker Compose

## Launch the application

1. **Clone the repository:**

   ```bash
   git clone https://github.com/Kaleff/countries-table.git
   cd countries-table
   ```

2. **Start the containers** (app, MySQL, phpMyAdmin):

   ```bash
   docker compose up -d
   ```

3. **Open a shell inside the app container:**

   ```bash
   docker exec -it countries-table-laravel.test-1 bash
   ```

4. **Install PHP dependencies:**

   ```bash
   composer install
   ```

5. **Generate the application key:**

   ```bash
   php artisan key:generate
   ```

6. **Run the database migrations:**

   ```bash
   php artisan migrate
   ```

7. **Open the app** in your browser at
   [http://localhost/countries](http://localhost/countries) and click **Import**
   to load the EEA countries.

> All `php`, `artisan`, and `composer` commands must be run **inside** the
> `laravel.test` container (steps 4–7 assume you ran step 3 first). You can also run
> them without an interactive shell, e.g. `docker compose exec laravel.test composer install`.

## Key files

The request lifecycle flows **Request → Controller → Service → Resource**, keeping
validation, orchestration, business logic, and serialization in separate layers.

### Controller

[`app/Http/Controllers/CountryController.php`](app/Http/Controllers/CountryController.php)

The HTTP entry point for the `/api/countries` endpoints. It is thin by design — it
delegates all work to `CountryService` and wraps the result in JSON responses:

- `index(IndexRequest)` — reads the validated `sort_by` / `sort_order`, asks the
  service for a paginated list, and serializes it through `CountryDetailsResource`.
- `store()` — triggers an import of the EEA countries from the external API.
- `destroy()` — truncates all stored countries.

### Service

[`app/Services/CountryService.php`](app/Services/CountryService.php)

Holds the business logic so the controller stays slim and the logic stays testable
and reusable (the console commands call it too):

- `getCountries($sortBy, $sortOrder)` — returns a `LengthAwarePaginator`, eager-loading
  the `flag` and `index` relations and handling sorting on related-table columns
  (`gini`, `hdi`) via a join.
- `storeEeaCountries()` — fetches from the external API and persists `Country`,
  `CountryFlag`, and `CountryIndex` inside a DB transaction.
- `truncateCountries()` — clears all three tables.

### Request

[`app/Http/Requests/Country/IndexRequest.php`](app/Http/Requests/Country/IndexRequest.php)

A `FormRequest` that validates and whitelists the query string **before** the
controller runs, so only known-safe values reach the service:

- `page` — optional positive integer.
- `sort_by` — must be one of the allowed sort fields (`name`, `official_name`,
  `cca3`, `cca2`, `gini`, `hdi`).
- `sort_order` — `asc` or `desc`.

### Resource

[`app/Http/Resources/Country/`](app/Http/Resources/Country/)

API resources shape the model data into the JSON the front end consumes:

- [`CountryDetailsResource`](app/Http/Resources/Country/CountryDetailsResource.php) —
  the top-level country payload (`name`, `official_name`, `cca2`, `cca3`), nesting the
  flag and index resources.
- [`CountryFlagResource`](app/Http/Resources/Country/CountryFlagResource.php) — the
  flag emoji and image URLs.
- [`CountryIndexResource`](app/Http/Resources/Country/CountryIndexResource.php) — the
  socio-economic indices (`gini`, `gini_year`, `hdi`) plus a derived **`gini_rating`**
  that classifies income equality from the Gini coefficient:

  | Gini        | Level    | Badge colour              |
  | ----------- | -------- | ------------------------- |
  | below 30    | `good`   | green                     |
  | 30 – 36     | `ok`     | lime (green with yellow)  |
  | above 36    | `so-so`  | yellow                    |

  The countries table renders this rating as a coloured badge in its **Gini rating**
  column.

## Links

- **Countries table:** [http://localhost/countries](http://localhost/countries)
- **phpMyAdmin:** [http://localhost:8080](http://localhost:8080)

## Importing data

The Countries page has **Import** and **Truncate** buttons that call the API
(`POST` / `DELETE /api/countries`) — equivalent to the `countries:import` and
`countries:truncate` console commands.

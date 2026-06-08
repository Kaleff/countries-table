<?php

namespace App\Livewire\Countries;

use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Countries')]
class Index extends Component
{
    /**
     * Columns the countries API will accept for `sort_by`.
     * Mirrors App\Http\Requests\Country\IndexRequest.
     */
    public const SORTABLE = ['name', 'official_name', 'cca3', 'cca2', 'gini', 'hdi'];

    #[Url(as: 'sort')]
    public string $sortBy = 'name';

    #[Url(as: 'order')]
    public string $sortOrder = 'asc';

    #[Url]
    public int $page = 1;

    /**
     * Feedback shown after an import/truncate action.
     */
    public ?string $status = null;

    public bool $statusFailed = false;

    /**
     * Import the EEA countries (same as the `countries:import` command),
     * by sending a POST request to the countries API.
     */
    public function import(): void
    {
        $this->callApi('post', 'Countries imported successfully.');
    }

    /**
     * Remove all stored countries (same as the `countries:truncate` command),
     * by sending a DELETE request to the countries API.
     */
    public function truncate(): void
    {
        $this->page = 1;
        $this->callApi('delete', 'Countries removed successfully.');
    }

    /**
     * Send a write request to the countries API and capture the outcome.
     */
    private function callApi(string $method, string $successMessage): void
    {
        $this->status = null;
        $this->statusFailed = false;

        $response = Http::acceptJson()
            ->timeout(60)
            ->{$method}($this->endpoint());

        if ($response->successful()) {
            $this->status = $response->json('message', $successMessage);
        } else {
            $this->statusFailed = true;
            $this->status = $response->json('message')
                ?? 'The request could not be completed. Please try again.';
        }
    }

    /**
     * Toggle sorting on a column, resetting to the first page.
     */
    public function sort(string $column): void
    {
        if (! in_array($column, self::SORTABLE, true)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortOrder = 'asc';
        }

        $this->page = 1;
    }

    public function gotoPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function render()
    {
        $response = Http::acceptJson()
            ->timeout(15)
            ->get($this->endpoint(), [
                'sort_by' => $this->sortBy,
                'sort_order' => $this->sortOrder,
                'page' => $this->page,
            ]);

        $countries = [];
        $meta = [];
        $error = null;

        if ($response->successful()) {
            $countries = $response->json('data.countries', []);
            $meta = $response->json('data.meta', []);
        } else {
            $error = $response->json('message')
                ?? 'Unable to load countries right now. Please try again.';
        }

        return view('livewire.countries.index', [
            'countries' => $countries,
            'meta' => $meta,
            'error' => $error,
        ]);
    }

    private function endpoint(): string
    {
        return rtrim(config('services.countries_api.url'), '/').'/countries';
    }
}

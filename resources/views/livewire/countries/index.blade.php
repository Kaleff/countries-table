@php
    // label => API sort key (null = not sortable)
    $columns = [
        ['key' => null,             'label' => 'Flag'],
        ['key' => 'name',           'label' => 'Name'],
        ['key' => 'official_name',  'label' => 'Official name'],
        ['key' => 'cca2',           'label' => 'CCA2'],
        ['key' => 'cca3',           'label' => 'CCA3'],
        ['key' => 'hdi',            'label' => 'HDI'],
        ['key' => 'gini',           'label' => 'Gini'],
        ['key' => null,             'label' => 'Gini rating'],
    ];
@endphp

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Countries') }}</flux:heading>
            <flux:subheading>{{ __('European Economic Area countries, served from the API.') }}</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            @if (! empty($meta['total']))
                <flux:badge color="zinc" size="lg">{{ $meta['total'] }} {{ __('total') }}</flux:badge>
            @endif

            <flux:button
                size="sm"
                variant="primary"
                icon="arrow-down-tray"
                wire:click="import"
                wire:loading.attr="disabled"
                wire:target="import"
            >
                {{ __('Import') }}
            </flux:button>

            <flux:button
                size="sm"
                variant="danger"
                icon="trash"
                wire:click="truncate"
                wire:confirm="{{ __('Remove all stored countries?') }}"
                wire:loading.attr="disabled"
                wire:target="truncate"
            >
                {{ __('Truncate') }}
            </flux:button>
        </div>
    </div>

    @if ($status)
        <flux:callout
            variant="{{ $statusFailed ? 'danger' : 'success' }}"
            icon="{{ $statusFailed ? 'exclamation-triangle' : 'check-circle' }}"
        >
            {{ $status }}
        </flux:callout>
    @endif

    @if ($error)
        <flux:callout variant="danger" icon="exclamation-triangle" heading="{{ __('Could not load countries') }}">
            {{ $error }}
        </flux:callout>
    @endif

    <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        {{-- Loading overlay --}}
        <div
            wire:loading.flex
            wire:target="sort, gotoPage, import, truncate"
            class="absolute inset-0 z-10 items-center justify-center bg-white/60 backdrop-blur-sm dark:bg-zinc-900/60"
        >
            <flux:icon.loading class="size-6" />
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-neutral-200 bg-neutral-50 text-xs uppercase tracking-wide text-neutral-500 dark:border-neutral-700 dark:bg-zinc-800/50 dark:text-neutral-400">
                    <tr>
                        @foreach ($columns as $column)
                            <th scope="col" class="px-4 py-3 font-medium whitespace-nowrap">
                                @if ($column['key'])
                                    <button
                                        type="button"
                                        wire:click="sort('{{ $column['key'] }}')"
                                        class="inline-flex items-center gap-1 transition hover:text-neutral-900 dark:hover:text-white"
                                    >
                                        {{ $column['label'] }}
                                        @if ($sortBy === $column['key'])
                                            <flux:icon
                                                name="{{ $sortOrder === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                                class="size-3.5"
                                            />
                                        @else
                                            <flux:icon name="chevron-up-down" class="size-3.5 opacity-40" />
                                        @endif
                                    </button>
                                @else
                                    {{ $column['label'] }}
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse ($countries as $country)
                        <tr wire:key="country-{{ $country['cca3'] ?? $loop->index }}" class="transition hover:bg-neutral-50 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3 text-xl">
                                {{ $country['flag']['emoji'] ?? '🏳️' }}
                            </td>
                            <td class="px-4 py-3 font-medium text-neutral-900 dark:text-white">
                                {{ $country['name'] ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-300">
                                {{ $country['official_name'] ?? '—' }}
                            </td>
                            <td class="px-4 py-3 font-mono text-neutral-500 dark:text-neutral-400">
                                {{ $country['cca2'] ?? '—' }}
                            </td>
                            <td class="px-4 py-3 font-mono text-neutral-500 dark:text-neutral-400">
                                {{ $country['cca3'] ?? '—' }}
                            </td>
                            <td class="px-4 py-3 tabular-nums text-neutral-600 dark:text-neutral-300">
                                {{ $country['index']['hdi'] ?? '—' }}
                            </td>
                            <td class="px-4 py-3 tabular-nums text-neutral-600 dark:text-neutral-300">
                                {{ $country['index']['gini'] ?? '—' }}
                                @if (! empty($country['index']['gini_year']))
                                    <span class="text-xs text-neutral-400">({{ $country['index']['gini_year'] }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if (! empty($country['index']['gini_rating']))
                                    <flux:badge color="{{ $country['index']['gini_rating']['color'] }}" size="sm">
                                        {{ __($country['index']['gini_rating']['label']) }}
                                    </flux:badge>
                                @else
                                    <span class="text-neutral-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" class="px-4 py-10 text-center text-neutral-500 dark:text-neutral-400">
                                @unless ($error)
                                    {{ __('No countries to show. Import them with POST /api/countries first.') }}
                                @endunless
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if (! empty($meta['last_page']) && $meta['last_page'] > 1)
        @php
            $currentPage = (int) ($meta['current_page'] ?? 1);
            $lastPage = (int) $meta['last_page'];
            $isFirstPage = $currentPage <= 1;
            $isLastPage = $currentPage >= $lastPage;
        @endphp
        <div class="flex items-center justify-between gap-4">
            <flux:text class="text-sm">
                {{ __('Showing') }} {{ $meta['from'] ?? 0 }}–{{ $meta['to'] ?? 0 }}
                {{ __('of') }} {{ $meta['total'] ?? 0 }}
            </flux:text>

            <div class="flex items-center gap-2">
                <flux:button
                    size="sm"
                    variant="ghost"
                    icon="chevron-left"
                    wire:click="gotoPage({{ $currentPage - 1 }})"
                    :disabled="$isFirstPage"
                >
                    {{ __('Previous') }}
                </flux:button>

                <flux:text class="px-2 text-sm tabular-nums">
                    {{ $meta['current_page'] ?? 1 }} / {{ $meta['last_page'] }}
                </flux:text>

                <flux:button
                    size="sm"
                    variant="ghost"
                    icon:trailing="chevron-right"
                    wire:click="gotoPage({{ $currentPage + 1 }})"
                    :disabled="$isLastPage"
                >
                    {{ __('Next') }}
                </flux:button>
            </div>
        </div>
    @endif
</div>

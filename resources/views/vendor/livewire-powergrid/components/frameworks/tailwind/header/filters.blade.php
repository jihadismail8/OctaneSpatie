<div
    wire:key="toggle-filters-{{ $tableName }}')"
    id="toggle-filters"
    class="flex mr-2 mt-2 sm:mt-0 gap-3"
>
    <button
        wire:click="toggleFilters"
        type="button"
        class="focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 text-gray-600 ring-gray-300 bg-white rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto"
    >
        <x-livewire-powergrid::icons.filter class="h-4 w-4 text-pg-primary-500" />
    </button>
</div>

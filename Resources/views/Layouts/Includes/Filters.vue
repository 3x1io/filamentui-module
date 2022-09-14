<script setup>
import {ref, watch, useAttrs, onMounted, onUpdated, computed} from 'vue';
import { useTrans } from '@@/Composables/useTrans';
import { useResourceStore } from '@@/Stores/resourceStore';
import { useReload } from '@@/Composables/useReload';
import debounce from 'lodash/debounce';
import { useCookies } from 'vue3-cookies';
import { storeToRefs } from 'pinia';
import {usePage} from "@inertiajs/inertia-vue3";

const props = defineProps({
    setSearch: String,
    showFilter: Boolean,
    rows: Array,
    url: String,
});

const emit = defineEmits([
    'reload',
    'filter',
    'reset',
    'getSearch',
    'changeShowedCols',
]);

const store = useResourceStore();
const attrs = useAttrs();

/*
Global Methods
 */
const { cookies } = useCookies();
let { cols } = storeToRefs(useResourceStore());
const { reload } = useReload();
const { trans } = useTrans();
let { lang } = attrs;

/*
Set Data
 */
let search = ref("");
let filters = ref({});
let last_page = ref(1);
let openModal = ref(false);
let showRows = ref(false);
let cookieName = `${props.url}-cols`;

/*
Filter Rows
 */

let filterRows = computed(()=> {
    return props.rows.filter(function (item){
        if(item.list){
            return item;
        }
    })
})

/*
Methods
 */
function searchFilter() {
    let getSearchData = search.value;
    store.currentPage = 1; // reset the current page
    emit('getSearch', getSearchData);
}
function resetFilter() {
    emit('reset');
}
function filter(filterBy, filterValue) {
    emit('filter', { filterBy: filterValue });
}

function hideCol(col) {
    store.toggleCol(col);
    cookies.set(cookieName, cols.value);
}

function storeSearch() {
    store.search = search;
    reload();
}
onMounted(()=>{
    if(usePage().props.value.list.search){
        search.value = usePage().props.value.list.search;

    }
})
</script>

<template>
    <!-- Search -->
    <div class="flex-1 flex items-center justify-end">
        <div class="filament-tables-search-input">
            <label for="tableSearchInput" class="sr-only">
                {{trans('global.search')}}
            </label>

            <div class="relative group">
                <span class="absolute inset-y-0 left-0 flex items-center justify-center w-9 h-9 text-gray-400 pointer-events-none group-focus-within:text-primary-500">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>

                <input
                        @change="storeSearch"
                        v-model="search"
                        id="tableSearchInput"
                        :placeholder="trans('global.search')"
                        type="search"
                        autocomplete="off"
                        class="block w-full max-w-xs h-9 pl-9 placeholder-gray-400 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>
        </div>
    </div>

    <!-- Cols Filter -->
    <div class="filament-dropdown filament-tables-column-toggling shrink-0">
        <div @click="showRows = !showRows" class="filament-dropdown-trigger cursor-pointer" aria-expanded="false">
            <button title="Toggle columns" type="button" class="filament-icon-button flex items-center justify-center rounded-full relative hover:bg-gray-500/5 focus:outline-none text-primary-500 focus:bg-primary-500/10 dark:hover:bg-gray-300/5 w-10 h-10 filament-tables-column-toggling-trigger">
                <span class="sr-only">
                    Toggle columns
                </span>

                <svg class="filament-icon-button-icon w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                </svg>
            </button>
        </div>

        <div class="right-20 my-3 filament-dropdown-panel absolute z-10 w-full rounded-lg bg-white shadow-lg ring-1 ring-black/5 transition dark:bg-gray-800 dark:ring-white/10 max-w-[14rem]" v-show="showRows">
            <div class="p-4">
                <div class="grid grid-cols-1   lg:grid-cols-1   filament-forms-component-container gap-6">
                    <div class="col-span-1">
                        <div class="filament-forms-field-wrapper">
                            <div class="space-y-2">
                                <!-- Col Filter Item -->
                                <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse"
                                     v-for="(row, key) in filterRows"
                                     :key="key"
                                     :class="{selected_column: cols[row.name]}">
                                    <label  :for="row.name" class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse">
                                        <input
                                            type="checkbox"
                                            @change="hideCol(row.name)"
                                            :checked="cols[row.name]"
                                            :id="row.name"
                                            class="filament-forms-checkbox-component text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:checked:bg-primary-500 border-gray-300 dark:border-gray-600">
                                        <span class="text-sm font-medium leading-4 text-gray-700 dark:text-gray-300">
                                            {{ row.label ?? row.name }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Inputs -->
    <div class="filament-dropdown filament-tables-filters shrink-0" v-if="showFilter">
        <div @click="openModal = !openModal" class="filament-dropdown-trigger cursor-pointer">
            <button title="Filter" type="button" class="filament-icon-button flex items-center justify-center rounded-full relative hover:bg-gray-500/5 focus:outline-none text-primary-500 focus:bg-primary-500/10 dark:hover:bg-gray-300/5 w-10 h-10 filament-tables-filters-trigger">
                                    <span class="sr-only">
                                        Filter
                                    </span>

                <svg class="filament-icon-button-icon w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
            </button>
        </div>

        <div class="
            filament-dropdown-panel
            absolute
            right-10
            z-10
            my-3
            w-full
            rounded-lg
            bg-white
            shadow-lg
            ring-1
            ring-black/5
            transition
            dark:bg-gray-800
            dark:ring-white/10
            max-w-xs
        " v-show="openModal">
            <div class="filament-tables-filters-form space-y-6 p-4">
                <div class="grid grid-cols-1 lg:grid-cols-1 filament-forms-component-container gap-6">
                    <div class="col-span-1">
                        <div>
                            <div class="grid grid-cols-1 filament-forms-component-container gap-6">
                                <div class="col-span-1">
                                    <div class="filament-forms-field-wrapper">
                                        <div class="space-y-2">
                                           <slot></slot>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="button" @click.prevent="resetFilter()" class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400">
                        {{ trans('global.filters.reset')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

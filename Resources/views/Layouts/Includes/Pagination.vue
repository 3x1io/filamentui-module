<script setup>
import { onMounted, watch, useAttrs, ref } from 'vue';
import { useResourceStore } from '@@/Stores/resourceStore';
import { useReload } from '@@/Composables/useReload';
import { useTrans } from '@@/Composables/useTrans';
import { storeToRefs } from 'pinia';

const props = defineProps({
    collection: Object,
});

const emit = defineEmits(['reload']);

const attrs = useAttrs();
let { lang } = attrs;
let { trans } = useTrans();

let { per_page, currentPage } = storeToRefs(useResourceStore());

let { reload } = useReload();

let start_page = ref(1);
let last_page = ref(1);

function makeReload(type, page = 1) {
    switch (type) {
        case 'pageNum':
            currentPage.value = page;
            break;
        case 'perPage':
            currentPage.value = 1;
    }

    reload();
}

function next() {
    return props.collection.current_page === last_page.value
        ? reload(last_page.value)
        : reload(props.collection.current_page + 1);
}

function back() {
    return props.collection.current_page === 1
        ? reload(1)
        : reload(props.collection.current_page - 1);
}

function getNumbers(start, stop) {
    if(start && stop){
        return new Array(stop - start).fill(start).map((n, i) => n + i);
    }
    else {
        return 0;
    }

}

onMounted(() => {
    if (props.collection.total >= 7 && props.collection.current_page > 3) {
        start_page.value = props.collection.current_page - 3;
    } else {
        start_page.value = 1;
    }

    if (props.collection.last_page === start_page.value) {
        last_page.value = props.start_page + 1;
    } else if (start_page.value + 7 <= props.collection.last_page) {
        last_page.value = start_page.value + 7;
    } else {
        let get_last = props.collection.last_page - start_page.value;
        last_page.value = start_page.value + get_last + 1;
    }
});

</script>
<template>
    <div class="p-2 dark:border-gray-700">
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between filament-tables-pagination">
            <div class="items-center flex-1 grid-cols-3 mx-auto lg:grid">
                <div class="flex items-center justify-center mx-4 my-4 lg:justify-start">
                    <div class="pl-2 text-sm font-medium dark:text-white">
                        {{ trans('global.show') }} {{ collection.from }}
                        {{ trans('global.to') }} {{ collection.to }}
                        {{ trans('global.of') }}
                        {{ collection.total }}
                        {{ trans('global.results') }}
                    </div>
                </div>

                <div class="flex items-center justify-center mx-4 my-4">
                    <div class="flex items-center space-x-2 filament-tables-pagination-records-per-page-selector rtl:space-x-reverse">
                        <select
                            v-model="per_page"
                            @change="makeReload('perPage')"
                            id="tableRecordsPerPageSelect" class="h-8 pr-8 text-sm leading-none transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:focus:border-primary-500">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>

                        <label for="tableRecordsPerPageSelect" class="text-sm font-medium dark:text-white">
                            {{ trans('global.per_page') }}
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-center mx-4 my-4 lg:justify-end" v-show="collection.total > collection.per_page">
                    <div class="py-3 border rounded-lg dark:border-gray-600">
                        <ol class="flex items-center text-sm text-gray-500 divide-x divide-gray-300 rtl:divide-x-reverse dark:text-gray-400 dark:divide-gray-600">

                            <li>
                                <!-- v-show="collection.current_page !== 1" -->
                                <button
                                    @click.prevent="back()"
                                    :class="
                                        collection.current_page !== 1
                                            ? ''
                                            : 'text-gray-300 pointer-events-none'
                                    "
                                    type="button"
                                    class="filament-tables-pagination-item relative flex items-center justify-center font-medium min-w-[2rem] px-1.5 h-8 -my-3 rounded-md focus:outline-none hover:bg-gray-500/5 focus:bg-primary-500/10 focus:ring-2 focus:ring-primary-500 dark:hover:bg-gray-400/5 transition text-primary-600"
                                    aria-label="Previous"
                                    rel="prev"
                                >
                                    <svg class="w-5 h-5 rtl:scale-x-[-1]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </li>
                            <li
                                v-for="(page, key) in getNumbers(
                                    start_page,
                                    last_page
                                )"
                                :key="key"
                            >
                                <button
                                    v-if="collection.current_page === page"
                                    @click.prevent="makeReload('pageNum', page)"
                                    type="button"
                                    class="filament-tables-pagination-item relative flex items-center justify-center font-medium min-w-[2rem] px-1.5 h-8 -my-3 rounded-md focus:outline-none transition text-primary-600 focus:underline bg-primary-500/10 ring-2 ring-primary-500"
                                    aria-label="Go to page 1"
                                >
                                    <span>{{ page }}</span>
                                </button>
                                <button
                                    v-else
                                    @click.prevent="makeReload('pageNum', page)"
                                    type="button"
                                    class="filament-tables-pagination-item relative flex items-center justify-center font-medium min-w-[2rem] px-1.5 h-8 -my-3 rounded-md focus:outline-none hover:bg-gray-500/5 focus:bg-primary-500/10 focus:ring-2 focus:ring-primary-500 dark:hover:bg-gray-400/5 focus:text-primary-600 transition"
                                    aria-label="Go to page 2"
                                >
                                    <span>{{ page }}</span>
                                </button>
                            </li>
                            <li>
                                <button
                                    @click.prevent="next()"
                                    :class="
                                        collection.current_page !==
                                        collection.last_page
                                            ? ''
                                            : 'text-gray-300 pointer-events-none'
                                    "
                                    type="button"
                                    class="filament-tables-pagination-item relative flex items-center justify-center font-medium min-w-[2rem] px-1.5 h-8 -my-3 rounded-md focus:outline-none hover:bg-gray-500/5 focus:bg-primary-500/10 focus:ring-2 focus:ring-primary-500 dark:hover:bg-gray-400/5 transition text-primary-600"
                                    aria-label="Next"
                                    rel="next"
                                >
                                    <svg class="w-5 h-5 rtl:scale-x-[-1]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</template>

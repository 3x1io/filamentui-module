<script setup>
import {mdiForwardburger, mdiBackburger, mdiMenu} from '@mdi/js';
import {ref, computed, watch, onMounted, useAttrs} from 'vue';
import NavBar from '@@/Layouts/Includes/Navbar.vue';
import Aside from "@@/Layouts/Includes/Aside.vue";
import Footer from "@@/Layouts/Includes/Footer.vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";
import {useLayoutStore} from '@@/Stores/layout.js';
import {useStyleStore} from '@@/Stores/style.js';
import {useResourceStore} from '@@/Stores/resourceStore';

import {storeToRefs} from "pinia/dist/pinia";


const attrs = useAttrs();

const props = defineProps({
    //Load Form Resource
    collection: Object,
    rows: Array,
    roles: Object,
    list: Object,
    render: Object,

    //Load Form Global Data
    data: Object,
    errorBags: Array,
    errors: Object,
    jetstream: Object,
    user: Object,
});

//Load Storage

let {
    goNext,
    goBack,
    search,
    per_page,
    edit,
    orderBy,
    desc,
    last_page,
    showFilter,
    filters,
    currentPage,
    cols,
} = storeToRefs(useResourceStore());

let store = useResourceStore();

// Check if The Sidebar is expanded or not
const layoutStore = useLayoutStore();
Inertia.on('navigate', () => {
    layoutStore.isAsideMobileExpanded = false;
});

layoutStore.Breadcrumbs = [];

// Load Style form the storage
const styleStore = useStyleStore();

</script>

<template>
    <div class="filament-body bg-gray-100 text-gray-900 dark:text-gray-100 dark:bg-gray-900">
        <div class="filament-app-layout flex w-full min-h-screen overflow-v-clip">
            <Aside />
            <div
                :class="{
                    'lg:pl-24 lg:pl-24 rtl:lg:pr-24 rtl:lg:pl-0': layoutStore.isAsideLgActive,
                    'lg:pl-80 lg:pl-80 rtl:lg:pr-80 rtl:lg:pl-0': !layoutStore.isAsideLgActive
                }"
                class="
                    flex
                    filament-main
                    flex-col
                    gap-y-6
                    w-screen
                    flex-1
                    hidden
                    h-full
                    transition-all
                    filament-main-sidebar-open
                "
                style="display: flex"
            >
                <NavBar />

                <div class="filament-main-content flex-1 w-full px-4 mx-auto md:px-6 lg:px-8 max-w-7xl">
                    <!-- SLOT -->
                    <slot></slot>
                </div>

                <!-- Footer -->
                <Footer/>
            </div>
        </div>
    </div>
</template>

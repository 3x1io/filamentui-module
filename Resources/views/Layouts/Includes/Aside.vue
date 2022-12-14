<script setup>
// Check if The Sidebar is expanded or not
import {Inertia} from "@inertiajs/inertia";
import {useLayoutStore} from '@@/Stores/layout.js';
import {useStyleStore} from '@@/Stores/style.js';
import {usePage, Link} from "@inertiajs/inertia-vue3";
import {computed, onMounted, ref} from "vue";
import {useTrans} from "@@/Composables/useTrans";
import {VueDraggableNext} from 'vue-draggable-next'

const {trans} = useTrans();
const layoutStore = useLayoutStore();
Inertia.on('navigate', () => {
    layoutStore.isAsideMobileExpanded = false;
});

const styleStorge = useStyleStore();

// Load Menus
const dashboardMenu = usePage().props.value.data.menus.dashboard;

</script>

<template>
    <div
        v-show="layoutStore.isAsideMobileExpanded"
        @click="layoutStore.asideMobileToggle()"
        class="fixed inset-0 z-20 w-full h-full filament-sidebar-close-overlay bg-gray-900/50 lg:hidden"
    >

    </div>
    <aside
        :class="{
            'hidden': !layoutStore.isAsideMobileExpanded,
            'w-24': layoutStore.isAsideLgActive,
            'w-80': !layoutStore.isAsideLgActive
        }"
        class="
                    lg:flex
                    filament-sidebar-open
                    translate-x-0
                    max-w-[20em]
                    lg:max-w-[var(--sidebar-width)]
                    filament-sidebar
                    fixed
                    inset-y-0
                    left-0
                    rtl:left-auto
                    rtl:right-0
                    z-20
                    flex
                    flex-col
                    h-screen
                    overflow-hidden
                    shadow-2xl
                    transition-all
                    bg-white
                    lg:border-r
                    rtl:lg:border-r-0
                    rtl:lg:border-l
                    lg:z-0
                    dark:bg-gray-800
                    dark:border-gray-700
                    filament-sidebar-open
                    translate-x-0
                    max-w-[20em]
                    lg:max-w-[var(--sidebar-width)]
                ">

        <header
            class="
                        filament-sidebar-header
                        border-b
                        h-[4rem]
                        shrink-0
                        px-6 flex items-center dark:border-gray-700"
        >

            <Link
                :href="route('dashboard')"
                class="block w-full font-bold"
                v-show="true"
                data-turbo="false"
                style=""
            >
                <div v-if="usePage().props.value.data.theme.logo">
                    <img
                        v-if="layoutStore.isAsideLgActive && !styleStorge.darkMode"
                        :src="usePage().props.value.data.appUrl +'/'+usePage().props.value.data.theme.sm_logo"
                        class="h-8"
                        alt="logo"
                    />
                    <img
                        v-else-if="layoutStore.isAsideLgActive && styleStorge.darkMode"
                        :src="usePage().props.value.data.appUrl +'/'+usePage().props.value.data.theme.dark_sm_logo"
                        class="h-8"
                        alt="logo"
                    />
                    <img
                        v-else-if="styleStorge.darkMode"
                        :src="usePage().props.value.data.appUrl +'/'+usePage().props.value.data.theme.dark_logo"
                        class="h-8"
                        alt="logo"
                    />
                    <img
                        v-else
                        :src="usePage().props.value.data.appUrl +'/'+usePage().props.value.data.theme.logo"
                        class="h-8"
                        alt="logo"
                    />
                </div>
                <div v-else>
                    <span v-if="layoutStore.isAsideLgActive" class="capitalize">
                        {{  usePage().props.value.data.appName.substring(0, 4) }}
                    </span>
                    <span v-else>
                         {{ usePage().props.value.data.appName }}
                    </span>
                </div>

            </Link>
        </header>

        <nav class="flex-1 py-6 overflow-y-auto filament-sidebar-nav">
            <div>
                <div>
                    <ul class="mx-3 mt-2 space-y-1 text-sm">
                        <li class="filament-sidebar-item ">
                            <Link :href="route('dashboard')" :class="{
                                'bg-primary-500 text-white': route('dashboard').replace(usePage().props.value.data.appUrl, '') === usePage().url.value,
                                'hover:bg-gray-500/5 focus:bg-gray-500/5 dark:text-gray-300 dark:hover:bg-gray-700': route('dashboard').replace(usePage().props.value.data.appUrl, '') !== usePage().url.value
                            }"
                                  class="flex items-center justify-center gap-3 px-3 py-2 font-medium transition rounded-lg ">

                                <i class="w-5 h-5 shrink-0 bx bxs-home"  style="font-size: 20px"></i>

                                <div class="flex flex-1" v-show="!layoutStore.isAsideLgActive" style="">
                                <span>
                                    {{ trans('global.dashboard') }}
                                </span>
                                </div>
                            </Link>
                        </li>
                    </ul>
                </div>
                <div class="my-6 ml-6 border-t rtl:ml-auto rtl:mr-6 dark:border-gray-700" v-if="Object.keys(dashboardMenu).length"></div>
            </div>
            <div v-for="(item, key) in dashboardMenu" :key="key">
                <button @click.prevent="layoutStore.setAsideMenuGroup(item.label)"
                    v-show="!layoutStore.isAsideLgActive && (item && (item.label!=='main'))"
                    class="flex items-center justify-between w-full px-6">
                    <div class="flex items-center gap-4 text-gray-600 dark:text-gray-300">
                        <p class="flex-1 text-xs font-bold tracking-wider uppercase">
                            {{trans(item.label)}}
                        </p>
                    </div>

                    <svg v-show="!layoutStore.AsideMenuGroup[item.label]"
                        class="w-3 h-3 text-gray-600 transition-all dark:text-gray-300" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                    <svg v-show="layoutStore.AsideMenuGroup[item.label]"
                        class="w-3 h-3 text-gray-600 transition-all rotate-180 dark:text-gray-300"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div v-if="item.label==='main'">
                    <ul class="mx-3 mt-2 space-y-1 text-sm">
                        <li class="filament-sidebar-item " v-for="(menuItem, index) in item.menu" :key="index">
                            <Link :href="route(menuItem.route)" :class="{
                                'bg-primary-500 text-white': route(menuItem.route).replace(usePage().props.value.data.appUrl, '') === usePage().url.value,
                                'hover:bg-gray-500/5 focus:bg-gray-500/5 dark:text-gray-300 dark:hover:bg-gray-700': route(menuItem.route).replace(usePage().props.value.data.appUrl, '') !== usePage().url.value
                            }"
                                class="flex items-center justify-center gap-3 px-3 py-2 font-medium transition rounded-lg ">

                            <i class="w-5 h-5 shrink-0" :class="menuItem.icon" style="font-size: 20px"></i>

                            <div class="flex flex-1" v-show="!layoutStore.isAsideLgActive" style="">
                                <span>
                                    {{ trans(menuItem.label) }}
                                </span>
                                <span v-if="menuItem.badge"
                                    class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal text-primary-700 bg-primary-500/10 dark:text-primary-500">
                                    {{ menuItem.badge }}
                                </span>
                            </div>
                            </Link>
                        </li>
                    </ul>
                </div>
                <div v-else>
                    <ul class="mx-3 mt-2 space-y-1 text-sm"
                        v-show="layoutStore.AsideMenuGroup[item.label] || layoutStore.isAsideLgActive ">
                        <li class="filament-sidebar-item " v-for="(menuItem, index) in item.menu" :title="trans(menuItem.label)">
                            <Link :href="route(menuItem.route)" :class="{
                                'bg-primary-500 text-white': route(menuItem.route).replace(usePage().props.value.data.appUrl, '') === usePage().url.value,
                                'hover:bg-gray-500/5 focus:bg-gray-500/5 dark:text-gray-300 dark:hover:bg-gray-700': route(menuItem.route).replace(usePage().props.value.data.appUrl, '') !== usePage().url.value
                            }"
                                class="flex items-center justify-center w-full gap-3 px-3 py-2 font-medium transition rounded-lg ">

                            <i class="w-5 h-5 shrink-0" :class="menuItem.icon" style="font-size: 20px"></i>

                            <div class="flex flex-1" v-show="!layoutStore.isAsideLgActive" style="">
                                <span>
                                    {{ trans(menuItem.label) }}
                                </span>
                                <span v-if="menuItem.badge"
                                    class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal text-primary-700 bg-primary-500/10 dark:text-primary-500">
                                    {{ menuItem.badge }}
                                </span>
                            </div>
                            </Link>
                        </li>
                    </ul>
                </div>

                <div class="my-6 ml-6 border-t rtl:ml-auto rtl:mr-6 dark:border-gray-700"
                    v-if="item.label !== dashboardMenu[dashboardMenu.length-1]"></div>
            </div>
        </nav>
    </aside>
</template>
<style>
.tooltip {
    z-index: 9999 !important;
}
</style>

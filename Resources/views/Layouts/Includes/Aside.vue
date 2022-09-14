<script setup>
// Check if The Sidebar is expanded or not
import {Inertia} from "@inertiajs/inertia";
import {useLayoutStore} from '@@/Stores/layout.js';
import {usePage, Link} from "@inertiajs/inertia-vue3";
import {computed, onMounted, ref} from "vue";
import {useTrans} from "@@/Composables/useTrans";
import {VueDraggableNext} from 'vue-draggable-next'

const {trans} = useTrans();
const layoutStore = useLayoutStore();
Inertia.on('navigate', () => {
    layoutStore.isAsideMobileExpanded = false;
});

let orderingMenu = ref({});

// Load Menus
const dashboardMenu = usePage().props.value.data.menus.dashboard;

let finalMenu = computed(() => {
    let menu = dashboardMenu;
    let ordering = [];
    let menuArray = [];
    let getOrderingFromStorage = JSON.parse(localStorage.getItem('orderingMenu'));
    let key = Object.keys(menu)
    for(let i=0; i<key.length; i++){
        if(!getOrderingFromStorage) {
            menu[key[i]].ordering = i;
            ordering[i] = key[i];
            menuArray[i] = menu[key[i]];
        }
        else {
            menu[key[i]].ordering = getOrderingFromStorage.indexOf(key[i]);
            ordering[i] = key[i];
            menuArray[i] = menu[key[i]];
        }
    }

    if(!getOrderingFromStorage){
        localStorage.setItem('orderingMenu', JSON.stringify(ordering));
    }

    let finalMenuItems = menuArray.sort((a, b) => a.ordering - b.ordering);
    let returnedObject = {};
    for(let r=0; r<finalMenuItems.length; r++){
        returnedObject[finalMenuItems[r].label] = finalMenuItems[r];
    }

    return returnedObject;
});

function log(log){
    let ordering = JSON.parse(localStorage.getItem('orderingMenu'));

    let oldItem = ordering[log.oldIndex];
    let newItem = ordering[log.newIndex];

    ordering[log.newIndex] = oldItem;
    ordering[log.oldIndex] = newItem;

    localStorage.setItem('orderingMenu', JSON.stringify(ordering));

    orderingMenu.value = ordering;
}

onMounted(()=>{
    orderingMenu.value = JSON.parse(localStorage.getItem('orderingMenu'));
})

</script>

<template>
    <div
        v-show="layoutStore.isAsideMobileExpanded"
        @click="layoutStore.asideMobileToggle()"
        class="
                filament-sidebar-close-overlay
                fixed
                inset-0
                z-20
                w-full
                h-full
                bg-gray-900/50
                lg:hidden"
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
                <span v-if="layoutStore.isAsideLgActive" class="capitalize">
                    {{ usePage().props.value.data.appName.substring(0, 4) }}

                </span>
                <span v-else>
                     {{ usePage().props.value.data.appName }}
                </span>
            </Link>
        </header>

        <nav class="flex-1 py-6 overflow-y-auto filament-sidebar-nav">
            <VueDraggableNext class="space-y-6" :list="orderingMenu" @end="log" >
                <div v-for="(item, key) in finalMenu" :key="key">
                    <button
                        @click.prevent="layoutStore.setAsideMenuGroup(item.label)"
                        v-show="!layoutStore.isAsideLgActive && (item && (item.label!=='main'))"
                        class="flex items-center justify-between w-full px-6"
                    >
                        <div class="flex items-center gap-4 text-gray-600 dark:text-gray-300">
                            <p class="flex-1 font-bold uppercase text-xs tracking-wider">
                                {{trans(item.label)}}
                            </p>
                        </div>

                        <svg v-show="!layoutStore.AsideMenuGroup[item.label]" class="w-3 h-3 text-gray-600 transition-all dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <svg v-show="layoutStore.AsideMenuGroup[item.label]" class="rotate-180 w-3 h-3 text-gray-600 transition-all dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div  v-if="item.label==='main'">
                        <ul class="text-sm space-y-1 mx-3 mt-2">
                            <li class="filament-sidebar-item " v-for="(menuItem, index) in item.menu" :key="index">
                                <Link
                                    :href="route(menuItem.route)"
                                    :class="{
                                        'bg-primary-500 text-white': route(menuItem.route).replace(usePage().props.value.data.appUrl, '') === usePage().url.value,
                                        'hover:bg-gray-500/5 focus:bg-gray-500/5 dark:text-gray-300 dark:hover:bg-gray-700': route(menuItem.route).replace(usePage().props.value.data.appUrl, '') !== usePage().url.value
                                    }"
                                    class="
                                    flex
                                    items-center
                                    justify-center
                                    gap-3
                                    px-3
                                    py-2
                                    rounded-lg
                                    font-medium
                                    transition
                                    "
                                >

                                    <i class="h-5 w-5 shrink-0" :class="menuItem.icon" style="font-size: 20px"></i>

                                    <div class="flex flex-1" v-show="!layoutStore.isAsideLgActive" style="">
                                        <span>
                                            {{ trans(menuItem.label) }}
                                        </span>
                                        <span v-if="menuItem.badge" class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal text-primary-700 bg-primary-500/10 dark:text-primary-500">
                                            {{ menuItem.badge }}
                                        </span>
                                    </div>
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div v-else>
                        <ul class="text-sm space-y-1 mx-3 mt-2" v-show="layoutStore.AsideMenuGroup[item.label] || layoutStore.isAsideLgActive ">
                            <li class="filament-sidebar-item " v-for="(menuItem, index) in item.menu" :title="trans(menuItem.label)">
                                <Link
                                    :href="route(menuItem.route)"
                                    :class="{
                                        'bg-primary-500 text-white': route(menuItem.route).replace(usePage().props.value.data.appUrl, '') === usePage().url.value,
                                        'hover:bg-gray-500/5 focus:bg-gray-500/5 dark:text-gray-300 dark:hover:bg-gray-700': route(menuItem.route).replace(usePage().props.value.data.appUrl, '') !== usePage().url.value
                                    }"
                                    class="
                                    flex
                                    items-center
                                    justify-center
                                    gap-3
                                    px-3
                                    py-2
                                    w-full
                                    rounded-lg
                                    font-medium
                                    transition
                                    "
                                >

                                    <i class="h-5 w-5 shrink-0" :class="menuItem.icon" style="font-size: 20px"></i>

                                    <div class="flex flex-1" v-show="!layoutStore.isAsideLgActive" style="">
                                        <span>
                                            {{ trans(menuItem.label) }}
                                        </span>
                                        <span v-if="menuItem.badge" class="inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal text-primary-700 bg-primary-500/10 dark:text-primary-500">
                                            {{ menuItem.badge }}
                                        </span>
                                    </div>
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div class="border-t ml-6 rtl:ml-auto rtl:mr-6 dark:border-gray-700 my-6" v-if="item.label !== orderingMenu[orderingMenu.length-1]"></div>
                </div>
            </VueDraggableNext>
        </nav>
    </aside>
</template>
<style>
.tooltip {
    z-index: 9999 !important;
}
</style>

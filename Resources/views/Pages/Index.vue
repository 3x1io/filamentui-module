<script setup>
import { Head } from '@inertiajs/inertia-vue3'
import { computed, ref, onMounted } from "vue";
import { useMainStore } from "@@/Stores/main";
import { useLayoutStore } from "@@/Stores/layout";
import {usePage} from "@inertiajs/inertia-vue3";
import {useTrans} from "@@/Composables/useTrans";
import {Inertia} from "@inertiajs/inertia";

const {trans} = useTrans();

const mainStore = useMainStore();

const clientBarItems = computed(() => mainStore.clients.slice(0, 4));

const transactionBarItems = computed(() => mainStore.history);

const username = computed(() => usePage().props.value.user.name);

const avatar = computed(
    () => {
        return usePage().props.value.avatar ??
            `https://avatars.dicebear.com/api/initials/${username}.svg`
    }
);


function logout(){
    Inertia.post(route('logout'));
}

// Check if The Sidebar is expanded or not
const layoutStore = useLayoutStore();
layoutStore.Breadcrumbs = [];

layoutStore.setBreadcrumbs({
    route: route("dashboard"),
    label: trans('global.dashboard')
});

</script>
<script>
import AppLayout from '@@/Layouts/AppLayout.vue';

export default {
    layout: AppLayout
}
</script>

<template>

    <div  class="filament-page filament-dashboard-page">
        <div class="space-y-6">
            <header
                class="items-start justify-between space-y-2 filament-header sm:flex sm:space-y-0 sm:space-v-4 sm:rtl:space-v-reverse sm:py-4">
                <h1 class="text-2xl font-bold tracking-tight md:text-3xl filament-header-heading">
                    Dashboard
                </h1>
            </header>


            <!-- Dashboard Widgets -->
            <div class="grid grid-cols-1 gap-4 mb-6 filament-widgets-container lg:grid-cols-2 lg:gap-8">

                <div class="col-span-1 filament-widget filament-account-widget">
                    <div class="p-2 space-y-2 bg-white shadow rounded-xl dark:border-gray-600 dark:bg-gray-800">
                        <div class="space-y-2">
                            <div class="px-4 py-2 space-y-4">
                                <div class="flex items-center h-12 space-v-4 rtl:space-v-reverse">
                                    <div class="w-10 h-10 mx-4 bg-gray-200 bg-center bg-cover rounded-full dark:bg-gray-900"
                                        :style="'background-image: url(\''+avatar+'\')'">
                                    </div>

                                    <div>
                                        <h2 class="text-lg font-bold tracking-tight sm:text-xl">
                                            Welcome, {{username}}
                                        </h2>

                                        <form action="" @submit.prevent="logout" method="post" class="text-sm">
                                            <button type="submit"
                                                class="text-gray-600 hover:text-primary-500 focus:outline-none focus:underline dark:text-gray-300 dark:hover:text-primary-500">
                                                Sign out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                            </div>
        </div>
    </div>

</template>

<script setup>
// Check if The Sidebar is expanded or not
import {Inertia} from "@inertiajs/inertia";
import {useLayoutStore} from '@@/Stores/layout.js';
import {useStyleStore} from '@@/Stores/style.js';
import {useTrans} from '@@/Composables/useTrans'
import {computed, onMounted,ref} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";
import {initializeApp} from "firebase/app";
import {getMessaging, getToken, onMessage} from "firebase/messaging";
import moment from "moment";

const props = defineProps({
    breadcrumbs: {
        type: Array,
        default: []
    }
})

let data = usePage().props.value.data;

const {trans} = useTrans();

let notifications = ref([]);

const layoutStore = useLayoutStore();
Inertia.on('navigate', () => {
    layoutStore.isAsideMobileExpanded = false;
});
// Load Style form the storage
const styleStore = useStyleStore();

const username = computed(() => usePage().props.value.user.name);

const avatar = computed(
    () => {
        return usePage().props.value.avatar ??
            `https://avatars.dicebear.com/api/initials/${username}.svg`
    }
);

// Set menu click actions
function langSwitch(){
    Inertia.post(
        route("translations.switch"),
        {language: JSON.parse(localStorage.getItem("lang"))},
        {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                let htmlEl = document.querySelector("html");

                if (
                    JSON.parse(localStorage.getItem("lang")).id === "ar"
                ) {
                    htmlEl.setAttribute("dir", "rtl");
                    localStorage.setItem(
                        "lang",
                        JSON.stringify({
                            id: "en",
                            name: "English",
                        })
                    );
                } else {
                    htmlEl.setAttribute("dir", "ltr");
                    localStorage.setItem(
                        "lang",
                        JSON.stringify({
                            id: "ar",
                            name: "Arabic",
                        })
                    );
                }
            },
        }
    );
}
function logout(){
    Inertia.post(route('logout'));
}

let menu = usePage().props.value.data.menus.profile.main.menu;

// Mounted
onMounted(() => {
    // Set Lang on local
    if (!localStorage.getItem("lang")) {
        localStorage.setItem(
            "lang",
            JSON.stringify({
                id: "ar",
                name: "Arabic",
            })
        );
    }
});

function handleFirebase(permission){
    if(data.fcm){
        const firebaseConfig = {
            apiKey: data.fcm.config.apiKey,
            authDomain: data.fcm.config.authDomain,
            projectId: data.fcm.config.projectId,
            storageBucket: data.fcm.config.storageBucket,
            messagingSenderId: data.fcm.config.messagingSenderId,
            appId: data.fcm.config.appId,
            measurementId: data.fcm.config.measurementId
        };
        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        if (permission === "granted") {
            console.log("Notification permission granted.");
            if ("serviceWorker" in navigator) {
                navigator.serviceWorker
                    .register("/firebase-messaging-sw.js")
                    .then(function (registration) {
                        console.log(
                            "Registration successful, scope is:",
                            registration.scope
                        );
                    })
                    .catch(function (err) {
                        console.log(
                            "Service worker registration failed, error:",
                            err
                        );
                    });
            }
            if(!data.token){
                getToken(messaging, {
                    vapidKey:data.fcm.vapidKey,
                })
                    .then((currentToken) => {
                        if (currentToken) {
                            // Send the token to your server and update the UI if necessary
                            // ...
                            console.log(currentToken);
                            Inertia.post(
                                route("admin.notifications.token"),
                                {
                                    token: currentToken,
                                    provider: "fcm-web",
                                    model: "App\\Models\\User",
                                    model_id: usePage().props.value.user.id,
                                },
                                {
                                    onSuccess: () => {
                                        console.log(
                                            "Registration successful"
                                        );
                                    },
                                }
                            );
                        } else {
                            // Show permission request UI
                            console.log(
                                "No registration token available. Request permission to generate one."
                            );
                            // ...
                        }
                    })
                    .catch((err) => {
                        console.log(
                            "An error occurred while retrieving token. ",
                            err
                        );
                        // ...
                    });
            }
        }
        else {
            console.log("Can't Access Notifications");
        }
    }
    Notification.requestPermission().then((permission) => {
        handleFirebase(permission);
    });
}

Notification.requestPermission().then((permission) => {
    if(data.fcm) {
        if (permission === "granted") {
            const firebaseConfig = {
                apiKey: data.fcm.config.apiKey,
                authDomain: data.fcm.config.authDomain,
                projectId: data.fcm.config.projectId,
                storageBucket: data.fcm.config.storageBucket,
                messagingSenderId: data.fcm.config.messagingSenderId,
                appId: data.fcm.config.appId,
                measurementId: data.fcm.config.measurementId
            };
            const app = initializeApp(firebaseConfig);
            const messaging = getMessaging(app);

            navigator.serviceWorker.getRegistration().then((reg) => {
                onMessage(messaging, (payload) => {
                    // var audio = new Audio('https://devsuez.emalleg.net/storage/sound/notifications.mp3');
                    // audio.play();
                    notifications.value.unshift({
                        title: payload.data.title,
                        url: payload.data.url,
                        icon: payload.data.icon,
                        image: payload.data.image,
                        description: payload.data.message,
                        type: payload.data.type,
                        date: moment().fromNow(),
                    });
                    // push notification can send event.data.json() as well
                    const options = {
                        body: payload.data.message,
                        icon: payload.data.image,
                        tag: "alert",
                    };
                    let notification = reg.showNotification(
                        payload.data.title,
                        options
                    );
                    // link to page on clicking the notification
                    notification.onclick = (payload) => {
                        window.open(payload.data.url);
                    };
                });
            });

        }
    }
});

</script>
<template>
    <header class="
                    filament-main-topbar
                    sticky
                    top-0
                    z-10
                    flex
                    h-16
                    w-full
                    shrink-0
                    items-center
                    border-b
                    bg-white
                    dark:bg-gray-800
                    dark:border-gray-700
                ">
        <div class="flex items-center w-full px-2 sm:px-4 md:px-6 lg:px-8">
            <!-- hide button -->
            <button
                class="
                            filament-sidebar-open-button
                            shrink-0
                            flex
                            items-center
                            justify-center
                            w-10
                            h-10
                            transition
                            text-primary-500
                            rounded-full
                            hover:bg-gray-500/5
                            focus:bg-primary-500/10
                            focus:outline-none
                            lg:z-10
                            lg:hover:bg-white
                            lg:focus:bg-white
                            lg:hover:text-gray-600
                            lg:focus:text-gray-600
                            lg:-ml-[44px]
                            lg:w-6
                            lg:h-6
                            lg:text-gray-500
                            lg:bg-white
                            lg:shadow
                            lg:dark:bg-gray-900
                            lg:dark:hover:text-gray-400
                            lg:dark:focus:text-gray-400
                            lg:mr-4
                            rtl:lg:ml-4
                            rtl:lg:-mr-[44px]

                        "

            >
                <div
                    @click="layoutStore.asideMobileToggle()"
                    class="relative lg:absolute visible lg:invisible">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </div>


                <div class="absolute ltr:lg:relative invisible ltr:lg:visible">
                    <svg v-show="!layoutStore.isAsideLgActive" @click="layoutStore.asideExpandToggle()" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <svg v-show="layoutStore.isAsideLgActive" @click="layoutStore.asideExpandToggle()" class=" w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="display: none;">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>

                <div class="absolute rtl:lg:relative invisible rtl:lg:visible">
                    <svg v-show="!layoutStore.isAsideLgActive" @click="layoutStore.asideExpandToggle()" class=" w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="display: none;">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <svg v-show="layoutStore.isAsideLgActive" @click="layoutStore.asideExpandToggle()" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </button>

            <div class="flex items-center justify-between flex-1">

                <!-- breadcrumbs -->
                <div class="filament-breadcrumbs flex-1">
                    <div class="hidden gap-4 items-center font-medium text-sm lg:flex dark:text-white" v-if="layoutStore.Breadcrumbs.length">
                        <div v-for="(item, key) in layoutStore.Breadcrumbs" :key="key">
                            <div class="flex justify-start">
                                <Link :href="item.route" class="dark:text-gray-400 my-auto" :class="{'text-gray-500':key === layoutStore.Breadcrumbs.length-1}">
                                    {{item.label}}
                                </Link>
                                <div class="h-6 border-gray-300 -skew-x-12 dark:border-gray-500 ml-4" :class="{'border-r': key !== layoutStore.Breadcrumbs.length-1}"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search -->
                <div class="filament-global-search flex items-center ml-4">
                    <div class="relative">
                        <div class="filament-global-search-input">
                            <label for="globalSearchInput" class="sr-only">
                                Global search
                            </label>

                            <div class="relative group max-w-md">
                                        <span class="absolute inset-y-0 left-0 flex items-center justify-center w-10 h-10 text-gray-500 pointer-events-none group-focus-within:text-primary-500 dark:text-gray-400">
                                            <svg  class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <svg v-if="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="animate-spin w-5 h-5">
                                                <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor"></path>
                                                <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" fill="currentColor"></path>
                                            </svg>
                                        </span>

                                <input  id="globalSearchInput" placeholder="Search" type="search" autocomplete="off" class="block w-full h-10 pl-10 bg-gray-400/10 placeholder-gray-500 border-transparent transition duration-75 rounded-lg focus:bg-white focus:placeholder-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div>
                    <div class="filament-notifications pointer-events-none fixed inset-4 z-50 mx-auto flex justify-end gap-3 items-end flex-col-reverse" role="status">
                    </div>

                    <!-- Notifications -->
                    <div>
                        <!-- Open Notification Modal -->
                        <div @click="layoutStore.asideNotificationToggle()" class="inline-block">
                            <button
                                title="filament::layout.database_notifications"
                                type="button"
                                class="filament-icon-button flex items-center justify-center rounded-full relative hover:bg-gray-500/5 focus:outline-none text-primary-500 focus:bg-primary-500/10 dark:hover:bg-gray-300/5 w-10 h-10 ml-4 -mr-1">
                                            <span class="sr-only">
                                                filament::layout.database_notifications
                                            </span>

                                <svg class="filament-icon-button-icon w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span class="filament-icon-button-indicator absolute rounded-full text-xs inline-block w-4 h-4 -top-0.5 -right-0.5 bg-primary-500/10">
                                            {{notifications.length ? notifications.length : 0}}
                                        </span>
                            </button>
                        </div>

                        <!-- Notifications Modal -->
                        <div role="dialog" aria-modal="true" class="filament-modal inline-block">
                            <div v-show="layoutStore.isNotificationActive"
                                 class="fixed inset-0 z-40 flex items-center min-h-screen overflow-y-auto transition"

                            >
                                <!-- Close Modal -->
                                <div
                                    @click="layoutStore.asideNotificationToggle()"
                                    aria-hidden="true"
                                    class="filament-modal-close-overlay fixed inset-0 w-full h-full bg-black/50 cursor-pointer">

                                </div>

                                <div v-show="layoutStore.isNotificationActive"

                                     class="relative w-full cursor-pointer pointer-events-none"

                                >
                                    <div class="filament-modal-window w-full p-2 bg-white cursor-default pointer-events-auto dark:bg-gray-800 relative h-screen overflow-y-auto ml-auto max-w-md">
                                        <button
                                            @click="layoutStore.asideNotificationToggle()"
                                            tabindex="-1"
                                            type="button"
                                            class="absolute top-2 right-2"
                                        >
                                            <svg title="__('filament-support::components/modal.actions.close.label')" tabindex="-1" class="filament-modal-close-button h-4 w-4 cursor-pointer text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="sr-only">
                                                        Close
                                                    </span>
                                        </button>

                                        <div class="space-y-2" v-if="notifications.length">
                                            <div class="filament-modal-header px-4 py-2">
                                                <h2 class="filament-modal-heading text-xl font-bold tracking-tight relative">
                                                            <span>
                                                                Notifications
                                                            </span>

                                                    <span class="inline-flex absolute items-center justify-center top-0 ml-1 min-w-[1rem] h-4 rounded-full text-xs text-primary-700 bg-primary-500/10 dark:text-primary-500">
                                                                {{notifications.length ? notifications.length : 0}}
                                                            </span>
                                                </h2>

                                                <div>
                                                    <div class="mt-2 text-sm">
                                                        <button
                                                            type="button"
                                                            class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-gray-600 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200"
                                                            tabindex="-1"
                                                        >
                                                            Mark all as read
                                                        </button>

                                                        <span>
                                                                    •
                                                                </span>
                                                        <button
                                                            type="button"
                                                            class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-gray-600 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200"
                                                            tabindex="-1"
                                                        >
                                                            Clear
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div aria-hidden="true" class="filament-hr border-t dark:border-gray-700"></div>

                                            <div class="filament-modal-content space-y-2">
                                                <div class="px-4 py-2 space-y-4">
                                                    <div class="mt-[calc(-1rem-1px)]">
                                                        <div class="-mx-6 border-b border-t dark:border-gray-800">
                                                            <div v-for="notification in notifications" class="py-2 pl-4 pr-2 bg-gray-50 -mb-px dark:bg-gray-700">
                                                                <div

                                                                    class="filament-notifications-notification pointer-events-auto invisible flex gap-3 w-full transition duration-300"
                                                                    style="display: flex; visibility: visible;">

                                                                    <img v-if="notification.image" :src="notification.image" class="filament-notifications-icon h-6 w-6 text-gray-400"/>
                                                                    <svg v-else class="filament-notifications-icon h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                                    </svg>

                                                                    <div class="grid flex-1">
                                                                        <div class="filament-notifications-title flex h-6 items-center text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                            <p>{{notification.title}}</p>
                                                                        </div>

                                                                        <p class="filament-notifications-date text-xs text-gray-500 dark:text-gray-300">
                                                                            {{notification.date}}
                                                                        </p>

                                                                        <div class="filament-notifications-body mt-1 text-sm text-gray-500 dark:text-gray-300">
                                                                            <p><strong>{{notification.description}}</strong></p>
                                                                        </div>

                                                                        <div class="filament-notifications-actions mt-2 flex gap-3">
                                                                            <a class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-notifications-link-action" :href="notification.url">

                                                                                View

                                                                            </a>

                                                                        </div>
                                                                    </div>

                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="filament-notifications-close-button h-4 w-4 cursor-pointer text-gray-400">
                                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-2" v-else>


                                            <div class="filament-modal-content space-y-2">

                                                <div class="px-4 py-2 space-y-4">
                                                    <div class="flex flex-col items-center justify-center mx-auto my-6 space-y-4 text-center bg-white dark:bg-gray-800">
                                                        <div class="flex items-center justify-center w-12 h-12 text-primary-500 rounded-full bg-primary-50 dark:bg-gray-700">
                                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                                            </svg>    </div>

                                                        <div class="max-w-md space-y-1">
                                                            <h2 class="text-lg font-bold tracking-tight dark:text-white">
                                                                No notifications here
                                                            </h2>

                                                            <p class="whitespace-normal text-sm font-medium text-gray-500 dark:text-gray-400">
                                                                Please check again later
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div></div>
                </div>


                <div class="filament-dropdown">
                    <div class="filament-dropdown-trigger cursor-pointer ml-4" @click="layoutStore.asideProfileToggle()">
                        <!-- Profile -->
                        <div class="w-10 h-10 rounded-full bg-gray-200 bg-cover bg-center dark:bg-gray-900"
                             :style="'background-image: url(\''+avatar+'\')'">
                        </div>
                    </div>

                    <div
                        @click="layoutStore.asideProfileToggle()"
                        v-show="layoutStore.isProfileMenuActive"
                        class="
                                    filament-dropdown-panel
                                    right-16
                                    top-[59.5px]
                                    rtl:left-16
                                    rtl:right-auto
                                    absolute z-10 w-full rounded-lg bg-white shadow-lg ring-1 ring-black/5 transition dark:bg-gray-800 dark:ring-white/10 max-w-[14rem]"
                    >

                        <ul class="py-1 space-y-1">
                            <!-- Profile Dropdown -->
                            <div v-for="(item, key) in menu" :key="key">
                                <Link :href="route(item.route)"
                                      class="filament-dropdown-item flex items-center w-full h-8 px-3 text-sm font-medium group whitespace-nowrap focus:outline-none hover:text-white focus:text-white hover:bg-primary-600 focus:bg-primary-700"

                                >
                                    <i :class="item.icon" class="mr-2 -ml-1 w-5 h-5 flex-shrink-0 rtl:ml-2 rtl:-mr-1 group-hover:text-white group-focus:text-white text-primary-500" style="font-size: 18px"></i>
                                    <span class="truncate" v-if="item.route === 'profile.show'">
                                        {{username}}
                                    </span>
                                    <span class="truncate" v-else>
                                        {{trans(item.label)}}
                                    </span>
                                </Link>
                            </div>

                            <!-- Toggle Theme -->
                            <div>
                                <div>
                                    <button
                                        @click="styleStore.setDarkMode()"
                                        v-show="!styleStore.darkMode"
                                        type="button"
                                        class="filament-dropdown-item flex items-center w-full h-8 px-3 text-sm font-medium group whitespace-nowrap focus:outline-none hover:text-white focus:text-white hover:bg-primary-600 focus:bg-primary-700"
                                    >

                                        <svg class="mr-2 -ml-1 w-5 h-5 flex-shrink-0 rtl:ml-2 rtl:-mr-1 group-hover:text-white group-focus:text-white text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="truncate">
                                                    Toggle light mode
                                                </span>
                                    </button>
                                </div>

                                <div>
                                    <button
                                        @click="styleStore.setDarkMode()"
                                        type="button" class="filament-dropdown-item flex items-center w-full h-8 px-3 text-sm font-medium group whitespace-nowrap focus:outline-none hover:text-white focus:text-white hover:bg-primary-600 focus:bg-primary-700"
                                        v-show="styleStore.darkMode">
                                        <svg class="mr-2 -ml-1 w-5 h-5 flex-shrink-0 rtl:ml-2 rtl:-mr-1 group-hover:text-white group-focus:text-white text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                        </svg>
                                        <span class="truncate">
                                                    Toggle dark mode
                                                </span>

                                    </button>
                                </div>
                            </div>

                            <!-- Lang -->
                            <div>
                                <div>
                                    <button
                                        @click="langSwitch()"
                                        type="button"
                                        class="filament-dropdown-item flex items-center w-full h-8 px-3 text-sm font-medium group whitespace-nowrap focus:outline-none hover:text-white focus:text-white hover:bg-primary-600 focus:bg-primary-700"
                                    >
                                        <svg class="mr-2 -ml-1 w-5 h-5 flex-shrink-0 rtl:ml-2 rtl:-mr-1 group-hover:text-white group-focus:text-white text-primary-500"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802" />
                                        </svg>
                                        <span class="truncate">
                                                    Change Language
                                                </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Logout Dropdown -->
                            <div>
                                <form action="" @submit.prevent="logout" method="post">
                                    <button type="submit" class="filament-dropdown-item flex items-center w-full h-8 px-3 text-sm font-medium group whitespace-nowrap focus:outline-none hover:text-white focus:text-white hover:bg-primary-600 focus:bg-primary-700">
                                        <svg class="mr-2 -ml-1 w-5 h-5 flex-shrink-0 rtl:ml-2 rtl:-mr-1 group-hover:text-white group-focus:text-white text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="truncate">
                                                    Sign out
                                                </span>

                                    </button>
                                </form>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

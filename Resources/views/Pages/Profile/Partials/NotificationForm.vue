<script setup>
import {initializeApp} from "firebase/app";
import {getMessaging, getToken, onMessage} from "firebase/messaging";
import moment from "moment";
import {Inertia} from "@inertiajs/inertia";
import {usePage} from "@inertiajs/inertia-vue3";
import {onMounted, reactive, ref} from 'vue';
import {createToaster} from "@meforma/vue-toaster";

let notifications = ref([]);
let isActive = ref(false);
const toaster = createToaster();

navigator.serviceWorker.getRegistrations().then(registrations => {
    registrations.forEach(function (item){
        if(item.active.scriptURL.includes("firebase-messaging-sw.js")){
            isActive.value = true;
        }
        else {
            isActive.value = false;
        }
    })
});

console.log(isActive.value);
// const channel = new BroadcastChannel("firebase-run");
// channel.addEventListener("message", (event) => {
//     console.log(event.data)
// });



function allowNotifications(){
    if (!("Notification" in window)) {
        console.log("This browser does not support desktop notification");
    }
    else {
        Notification.requestPermission().then((permission) => {
            if (permission === "granted") {
                const firebaseConfig = {
                    apiKey: usePage().props.value.data.fcm.config.apiKey,
                    authDomain: usePage().props.value.data.fcm.config.authDomain,
                    projectId: usePage().props.value.data.fcm.config.projectId,
                    storageBucket: usePage().props.value.data.fcm.config.storageBucket,
                    messagingSenderId: usePage().props.value.data.fcm.config.messagingSenderId,
                    appId: usePage().props.value.data.fcm.config.appId,
                    measurementId: usePage().props.value.data.fcm.config.measurementId
                };
                const app = initializeApp(firebaseConfig);
                const messaging = getMessaging(app);

                console.log("Notification permission granted.");
                let isRegister = false;
                navigator.serviceWorker.getRegistrations().then(registrations => {
                    registrations.forEach(function (item){
                        item.active.scriptURL.replace(item.scope, '') === "firebase-messaging-sw.js" || item.active.scriptURL.replace(item.scope, '') === "firebase-cloud-messaging-push-scope" ? isRegister = true : isRegister = false;
                    })
                });
                if(!isRegister){
                    if ("serviceWorker" in navigator) {
                        navigator.serviceWorker
                            .register("/firebase-messaging-sw.js")
                            .then(function (registration) {
                                toaster.success('Your Notification Has Been Active', {
                                    position: 'top-right',
                                    useDefaultCss: true
                                });
                                isActive.value = true;
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
                }
                getToken(messaging, {
                    vapidKey:usePage().props.value.data.fcm.vapidKey,
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
        });
    }
}
</script>
<template>
    <jet-action-section>
        <template #title>
            Account Notifications
        </template>

        <template #description>
            To Allow Notifications inside your admin panel
        </template>

        <template #content>
            <div class="w-full text-sm text-gray-600 dark:text-gray-100">
                The Browser ask you to allow notifications to get subscribed to the notifications
            </div>

            <div class="mt-5">
                <jet-secondary-button @click="allowNotifications">
                   Allow Notifications
                </jet-secondary-button>
            </div>
        </template>
    </jet-action-section>
</template>

<script>
    import { defineComponent } from 'vue'
    import JetActionSection from '@@/Jetstream/ActionSection.vue'
    import JetDialogModal from '@@/Jetstream/DialogModal.vue'
    import JetDangerButton from '@@/Jetstream/DangerButton.vue'
    import JetInput from '@@/Jetstream/Input.vue'
    import JetInputError from '@@/Jetstream/InputError.vue'
    import JetSecondaryButton from '@@/Jetstream/SecondaryButton.vue'


    export default defineComponent({
        components: {
            JetActionSection,
            JetDangerButton,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetSecondaryButton,
        },

        data() {
            return {
                confirmingUserDeletion: false,

                form: this.$inertia.form({
                    password: '',
                })
            }
        },

    })
</script>

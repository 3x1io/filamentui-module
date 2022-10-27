<script setup>
import {Link, usePage} from "@inertiajs/inertia-vue3";
import JetDialogModal from "@@/Jetstream/DialogModal.vue";
import JetSecondaryButton from "@@/Jetstream/SecondaryButton.vue";
import JetButton from "@@/Jetstream/Button.vue";
import JetInput from "@@/Jetstream/Input.vue";
import JetInputError from "@@/Jetstream/InputError.vue";
import JetLabel from "@@/Jetstream/Label.vue";
import { Tabs, Tab } from "vue3-tabs-component";
import {computed, onMounted, onUpdated, ref, watch} from "vue";
import ViltForm from '$$/ViltForm.vue'
import {Inertia} from "@inertiajs/inertia";
import {createToaster} from "@meforma/vue-toaster";
import {useTrans} from "@@/Composables/useTrans";

const props = defineProps({
    show: Boolean,
    errors: Object,
    edit: Boolean,
    item: Object,
    title: String,
    url: String,
    rows: Array,
    type: {
        String,
        default: "modal",
    },
})

const emit = defineEmits([
    "success",
    "create",
    "update",
    "close"
])

onUpdated(()=>{
    form.value = props.item;
    showModal.value = props.show
})


/*
Set Data
 */

let isDisabledBtn = ref(false);
let showModal = ref(false);
let form = ref({});
let index = ref(false);
let lastTab = ref(false);
let tabs = ref(null);
const toaster = createToaster({ /* options */});
let {trans} = useTrans();

/*
Computed Methods
 */

const lang = computed(()=>{
    return usePage().props.value.data.trans;
});

let rowFilterCreate = computed(()=>{
    let rows = props.rows;
    let rowValue = [];
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].create) {
            rowValue.push(rows[i]);
        }
    }

    return rowValue;
});

let rowFilterEdit = computed(()=>{
    let rows = props.rows;
    let rowValue = [];
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].edit) {
            rowValue.push(rows[i]);
        }
    }

    return rowValue;
});

/*
Disabled Button On Load
 */
onMounted(()=>{
    Inertia.on("start", () => {
        isDisabledBtn.value = true
    });
    Inertia.on("finish", () => {
        isDisabledBtn.value = false
    });
})

/*
Methods
 */
function close(){
    form.value = ref({})
    showModal.value = false;
    emit('close');
}
let reactiveItem = computed(() => {
    let reactiveObject = {};
    for(let i =0; i<props.rows.length; i++){
        if(props.rows[i].reactive){
            if(typeof form.value[props.rows[i].reactiveRow] === 'boolean'){
                let checkItem = props.rows[i].reactiveWhere === "1" ? true : false;
                reactiveObject[props.rows[i].name] = checkItem === form.value[props.rows[i].reactiveRow];
            }
            else {
                reactiveObject[props.rows[i].name] = JSON.stringify(props.rows[i].reactiveWhere) === JSON.stringify(form.value[props.rows[i].reactiveRow]);
            }
        }
        else {
            reactiveObject[props.rows[i].name] =  true;
        }
    }

    return reactiveObject;
});
function changeTab(selectedTab) {
    let countActive = 0;
    for (let i = 0; i < props.type.tabs.length; i++) {
        if (props.type.tabs[i].name === selectedTab.tab.computedId) {
            index.value = i;
        }
    }
    if (index.value === props.type.tabs.length - 1) {
        lastTab.value = true;
    } else {
        lastTab.value  = false;
    }
}
function nextTab() {
    index.value += 1;
    tabs.value.selectTab("#" + props.type.tabs[index.value].name);
}
function success(id = null) {
    if (props.edit) {
        emit("update", { form: form.value, id: id });
        isDisabledBtn.value = false;
    } else {
        emit("create", form.value);
        isDisabledBtn.value = false;
    }
}
function saveRecord() {
    Inertia.post( route(props.url + ".store"), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            emit("success");
            isDisabledBtn.value = false;
        },
        onError: () => {
            if (props.type.name === "tabs") {
                for (let r = 0; r < props.rows.length; r++) {
                    if (
                        Object.keys(props.errors)[0] ===
                        props.rows[r].name
                    ) {
                        if (tabs) {
                            tabs.value.selectTab(
                                "#" + props.rows[r].tab
                            );
                        }

                        break;
                    }
                }
            }
            toaster.error(trans("global.error.message"), {
                position: "top",
            });
        },
    });
}
function updateRecord(id) {
    Inertia.post(route(props.url + ".update", id),form.value, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            emit("success");
            isDisabledBtn.value = false;
        },
        onError: () => {
            if (props.type.name === "tabs") {
                for (let r = 0; r < props.rows.length; r++) {
                    if (
                        Object.keys(props.errors)[0] ===
                        props.rows[r].name
                    ) {
                        if (tabs) {
                            tabs.value.selectTab(
                                "#" + props.rows[r].tab
                            );
                        }

                        break;
                    }
                }
            }
            toaster.error(trans("global.error.message"), {
                position: "top",
            });
        },
    });
}

</script>

<template>
    <JetDialogModal :show="showModal">
        <template #title>
            <h2 class="filament-modal-heading text-xl font-bold tracking-tight dark:text-gray-200">{{ title }}</h2>
        </template>

        <template #content>
            <div v-if="props.type.name === 'modal'">
                <form
                    class="grid grid-cols-1 gap-3 lg:grid-cols-1"
                    action=""
                    v-if="!props.edit"
                >
                    <slot name="create-top"></slot>

                    <ViltForm :rows="rowFilterCreate" :errors="props.errors" v-model="form" />
                    <slot name="create"></slot>
                </form>
                <form
                    class="grid grid-cols-1 gap-3 md:grid-cols-1"
                    action=""
                    v-else
                >
                    <slot name="edit-top"></slot>
                    <ViltForm :rows="rowFilterEdit" :edit="true" :errors="props.errors" v-model="form" />
                    <slot name="edit"></slot>
                </form>
            </div>
            <div v-else-if="props.type.name === 'tabs'">

                <Tabs
                    cache-lifetime="0"
                    :options="{ defaultTabHash: props.type.tabs[0].key }"
                    @changed="changeTab"
                    ref="tabs"
                    nav-class="flex justify-start space-x-2"
                    nav-item-class="py-2 px-4 rounded-lg"
                    nav-item-active-class="bg-main  py-2 px-4 rounded-lg text-white"
                >
                    <Tab
                        v-for="(tab, key) in props.type.tabs"
                        :key="key"
                        :name="tab.label"
                        :id="tab.name"
                        :is-disabled="
                            (!tab.create && !props.edit) || (!tab.edit && props.edit)
                        "
                    >
                        <form
                            class="grid grid-cols-1 lg:grid-cols-1"
                            action=""
                            v-if="!props.edit"
                        >
                            <slot name="create-top"></slot>
                            <ViltForm :rows="rowFilterCreate" :errors="props.errors" v-model="form" />
                            <slot name="create"></slot>
                        </form>
                        <form
                            class="grid grid-cols-1 md:grid-cols-1"
                            action=""
                            v-else
                        >
                            <slot name="edit-top"></slot>
                            <ViltForm :rows="rowFilterEdit" :edit="true"  :errors="props.errors" v-model="form" />
                            <slot name="edit"></slot>
                        </form>
                    </Tab>
                </Tabs>
                <form
                    class="grid grid-cols-1 lg:grid-cols-1"
                    action=""
                    v-if="!props.edit"
                >
                    <slot name="create-top"></slot>
                    <ViltForm :rows="rowFilterCreate" :errors="props.errors" v-model="form" />
                    <slot name="create"></slot>
                </form>
                <form class="grid grid-cols-1 md:grid-cols-1" action="" v-else>
                    <slot name="edit-top"></slot>
                    <ViltForm :rows="rowFilterEdit" :edit="true"  :errors="props.errors" v-model="form" />
                    <slot name="edit"></slot>
                </form>
            </div>
        </template>

        <template #footer>
            <slot name="footer-button"></slot>

            <div class="filament-modal-actions flex flex-wrap items-center gap-4 rtl:space-x-reverse">
                <button
                    class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-modal-button-action"
                    :disabled="isDisabledBtn"
                    v-if="props.type.name === 'tabs' && !lastTab"
                    @click.prevent="nextTab()"
                >
                    {{ trans("global.next") }}
                </button>
                <button
                    class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-modal-button-action"
                    :disabled="isDisabledBtn"
                    v-if="!props.edit && lastTab && props.type.name === 'tabs'"
                    @click.prevent="saveRecord()"
                >
                    <svg v-if="isDisabledBtn" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ trans("global.save") }}
                </button>
                <button
                    class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-modal-button-action"
                    :disabled="isDisabledBtn"
                    v-else-if="!props.edit && props.type.name === 'modal'"
                    @click.prevent="saveRecord()"
                >
                    <svg v-if="isDisabledBtn" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ trans("global.save") }}
                </button>
                <button
                    class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-modal-button-action"
                    :disabled="isDisabledBtn"
                    v-else-if="props.edit"
                    @click.prevent="updateRecord(form.id)"
                >
                    <svg v-if="isDisabledBtn" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ trans("global.update") }}
                </button>
                <button
                    class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-page-modal-button-action"
                    @click="close">
                    <svg v-if="isDisabledBtn" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ trans("global.close") }}
                </button>
            </div>
        </template>
    </JetDialogModal>
</template>

<script setup>
import {ref, computed, watch, onMounted} from 'vue';
import VueEasyLightbox from 'vue-easy-lightbox';
import { useForm, usePage, Head } from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";
import {useGetForm} from "@@/Composables/useGetForm";
import { useResourceStore } from '@@/Stores/resourceStore';
import {storeToRefs} from "pinia/dist/pinia";
import Header from '@@/Layouts/Includes/Header.vue';
import List from '@@/Layouts/Includes/Table.vue';
import Pagination from '@@/Layouts/Includes/Pagination.vue';
import {useTrans} from "@@/Composables/useTrans";
import JetSecondaryButton from '@@/Jetstream/SecondaryButton.vue';
import JetButton from '@@/Jetstream/Button.vue';
import JetDialogModal from '@@/Jetstream/DialogModal.vue';
import CreateModal from '@@/Modals/Create.vue';
import ViewModal from '@@/Modals/View.vue';
import DeleteModal from '@@/Modals/Delete.vue';
import BulkModal from '@@/Modals/Bulk.vue';
import ViltForm from '$$/ViltForm.vue';
import Filters from '@@/Layouts/Includes/Filters.vue';
import Bulk from '@@/Layouts/Includes/Bulk.vue';
import {useReload} from "@@/Composables/useReload";
import {useCookies} from "vue3-cookies";
import {createToaster} from "@meforma/vue-toaster";
import {useLayoutStore} from '@@/Stores/layout.js';


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
    orderBy,
    desc,
    last_page,
    showFilter,
    filters,
    currentPage,
    cols,
} = storeToRefs(useResourceStore());

let store = useResourceStore();


// Set DatamodalAction

/*
Data To Image Popup
 */
let visible = ref(false);
let images = ref([]);
let index = ref(1);
let form = ref({});
const toaster = createToaster({
    position: "right",
});

/*
Actions / Modals Data
 */
let actionModal = ref({});
let modalAction = ref({});
let selectedID = ref(null);

/*
Bulk Actions Data
 */
let showBlukModal = ref(false);
let bulkModal = ref(false);
let bulkItems = ref({});
let bulkActionTitle = ref("");

/*
Filter Actions Data
 */

let filterItem = ref({});
let filterObj = ref({});
let filtersObj = ref({});
let {reload} = useReload();
const {cookies} = useCookies();

/*
Modals Data
 */
let createModal = ref(false);
let edit = ref(false);
let viewModal = ref(false);
let deleteModal = ref(false);

// Methods
let {formMake} = useGetForm();



let getAction = ref({});
let showConfirmation = ref(false);

function openModal(name, id = null, confirmed=false){
    getAction.value = {};
    if(confirmed){
        getAction.value = {id: id, name: name, type: 'modal'};
        showConfirmation.value = !showConfirmation.value;
    }
    else {
        Inertia.reload({
            preserveScroll: true,
            preserveState: true,
        });
        selectedID.value = id;
        actionModal.value[name] = !actionModal.value[name];
    }
}

function fireAction (name, id = null, confirmed=false, method="post"){
    getAction.value = {};
    if(confirmed){
        getAction.value = {id: id, name: name, type: 'action'};
        showConfirmation.value = !showConfirmation.value;
    }
    else{
        if(method === 'post'){
            Inertia.post(route(name), {
                id: id ? id : selectedID.value,
            }, {
                onSuccess: () => {
                    success();
                    reload();
                }
            });
        }
        else {
            Inertia.get(route(name, id));
        }
    }
}

function modalActionRun(modal, action, confirmed=false, method="post") {
    getAction.value = {};
    if(confirmed){
        getAction.value = {modal: modal, action: action, type: 'action'};
        showConfirmation.value = !showConfirmation.value;
    }
    else {
        if (selectedID.value) {
            modalAction.value[modal].id = selectedID.value;
        }
        let modelForm = useForm(modalAction.value[modal]);
        if(method==="post"){
            Inertia.post(route(action), modelForm,{
                preserveScroll: true,
                onSuccess: () => {
                    modelForm.reset();
                    actionModal.value[modal] = false;
                    success();
                },
            });
        }
        else {
            Inertia.get(route(action), modelForm,{
                preserveScroll: true,
                onSuccess: () => {
                    modelForm.reset();
                    actionModal.value[modal] = false;
                    success();
                },
            });
        }

    }
}

function openUrl(url, confirmed=false){
    getAction.value = {};
    if(confirmed){
        getAction.value = {url: url, type: 'url'};
        showConfirmation.value = !showConfirmation.value;
    }else {
        window.open(url);
    }
}

function processCurrentAction(){
    if(getAction.value.type === 'modal'){
        showConfirmation.value = !showConfirmation.value;
        openModal(getAction.value.name, getAction.value.id);
    }
    if(getAction.value.type === 'action'){
        showConfirmation.value = !showConfirmation.value;
        fireAction(getAction.value.name, getAction.value.id);
    }
    if(getAction.value.type === 'url'){
        showConfirmation.value = !showConfirmation.value;
        openUrl(getAction.value.url);
    }
}

/*
Cookies Handle
 */
function handleCookie() {
    let cookieName = `${props.list.url}-cols`;
    if (!cookies.get(cookieName)) {
        for (let i = 0, ii = props.rows.length; i < ii; i++) {
            if (props.rows[i].list) {
                cols.value[props.rows[i].name] = true;
            }
        }
        store.setCols(cols.value);
        cookies.set(cookieName, cols.value);
    } else {
        store.setCols(cookies.get(cookieName));
    }
}
/*
Filter Handle
 */

function setFilter(filter) {
    filterObj.value = filter;
}

function makeFilter(filterBy, filterValue) {
    let filter = {};
    filter[filterBy] = filterValue;
}

function getSendedFilter() {
    const requestedFilter = JSON.parse(
        JSON.stringify(filtersObj.value[filterObj.value.name])
    )[filterObj.value.name];

    let ids = "";
    if(Array.isArray(requestedFilter)){
        ids = requestedFilter.map((item) => item.id);
    }
    else {
        ids = requestedFilter;
    }

    const sendedFilter = {};
    sendedFilter[filterObj.value.name] = ids;
    return sendedFilter;
}
function resetFilter(){
    Inertia.get(route(props.list.url + '.index'), {}, {
        preserveScroll: true,
    })
}

function searchFilter(search){

}

function filter(filter){

}

/*
Bulk Methods
 */
function bulkAction(action) {
    showBlukModal.value = false
    bulkActionTitle.value = action;
    bulkModal.value = true;

}
function bulkAll(value) {
    if (!value) {
        bulkItems.value = {};
    } else {
        for (let i = 0; i < props.collection.data.length; i++) {
            bulkItems.value[props.collection.data[i].id] = true;
        }
    }
}
function switchBulk(id) {
    if (bulkItems.value.hasOwnProperty(id)) {
        delete bulkItems.value[id];
    } else {
        bulkItems.value[id] = true;
    }
}

/*
List Methods
 */

function popUp(getImages){
    visible.value = true;
    images.value = getImages;
}

function createItem(){
    Inertia.reload({
        preserveScroll: true,
        preserveState: true,
        onSuccess: ()=>{
            form.value = useForm(formMake());
            if(props.render.form.name === 'page'){
                Inertia.get(route(props.list.url + '.create'), {}, {
                    preserveScroll: true,
                })
            }
            else {
                edit.value = false
                form.value = useForm(formMake);
                createModal.value = !createModal.value;
            }
        }
    });

}

function editItem(item){
    Inertia.reload({
        preserveScroll: true,
        preserveState: true,
        onSuccess: ()=>{
            if(props.render.form.name === 'page'){
                Inertia.visit(route(props.list.url + '.edit', item.id))
            }
            else {
                let getFormResponse = {};
                axios.get(route(props.list.url + '.show', item.id)).then((response) => {
                    form.value = useForm(response.data.data);
                    createModal.value = true;
                });
            }
            edit.value = true;
        }
    });
};

function viewItem(item){
    form.value = useForm(formMake());
    if(props.render.form.name === 'page'){
        Inertia.visit(route(props.list.url + '.view', item.id))
    }
    else {
        axios.get(route(props.list.url + '.show', item.id)).then((response) => {
            form.value = useForm(response.data.data);
            viewModal.value = true;
        });
    }
}

function deleteItem(item){
    form.value = useForm(item);
    deleteModal.value = true;
}

function reloadList(){
    reload()
}

/*
Fire Success Response / Error Response
 */

const getMessage = computed({
    get() {
        return props.data.message
    },
    set(value) {
        return value;
    },
});
const success = () => {
    const message = getMessage.value;
    if (typeof message === 'object') {
        if (message.type === 'error') {
            toaster.error(message.message, {
                position: 'top',
            });
        } else if (message.type === 'success') {
            toaster.success(message.message, {
                position: 'top',
            });
        } else {
            toaster.success(message.message, {
                position: 'top',
            });
        }
    } else {
        toaster.success(message, {
            position: 'top',
        });
    }

    edit.value = false;
    createModal.value = false;
    deleteModal.value = false;
    reload(1, 'orderBy', 'id', 'desc');
}

// Load Components
const modals = computed(() => {
    let modalsArray = [];
    if(props.render.components){
        for (let i = 0; i < props.render.components.length; i++) {
            if(props.render.components[i].classType === 'modal'){
                modalsArray.push(props.render.components[i]);
            }
        }
    }

    return modalsArray;
});
const widgets = computed(() => {
    let widgetsArray = [];
    if(props.render.components) {
        for (let i = 0; i < props.render.components.length; i++) {
            if (props.render.components[i].classType === 'widget') {
                widgetsArray.push(props.render.components[i]);
            }
        }
    }
    return widgetsArray;
});
const actions = computed(() => {
    let actionsArray = [];
    if(props.render.components) {
        for (let i = 0; i < props.render.components.length; i++) {
            if (props.render.components[i].classType === 'action') {
                actionsArray.push(props.render.components[i]);
            }
        }
    }
    return actionsArray;
});

// Load Languages
const gLang = computed(
    () => usePage().props.value.data.trans
);
const rLang = computed(
    () => props.render.lang
)

let {trans} = useTrans();

//Load Roles
const roles = props.roles;

// Mounted
onMounted(() => {
    handleCookie();

    //Set Form
    // form.value = useForm(formMake());

    //Set Current List Filters
    search.value = props.list.search;
    per_page.value = props.list.per_page;
    orderBy.value = props.list.orderBy;
    filters.value = props.list.filters;
    createModal.value = !!props.list.create;
    props.list.desc === 'asc' ? store.setDesc(false) : store.setDesc(true);
    store.setCurrentUrl(props.list.url);

    //Set Modals
    if(modals.value.length){
        for (let i = 0; i < modals.value.length; i++) {
            actionModal.value[modals.value[i].name] = false;
            let actionModalRows = {};
            for (let j = 0; j < modals.value[i].rows.length; j++) {
                if(modals.value[i].rows[j].default){
                    actionModalRows[modals.value[i].rows[j].name] = modals.value[i].rows[j].default;
                }
                else {
                    if(modals.value[i].rows[j].vue === 'ViltRelation.vue' || modals.value[i].rows[j].vue === 'ViltRepeater.vue' || modals.value[i].rows[j].vue === 'ViltSchema.vue'){
                        actionModalRows[modals.value[i].rows[j].name] = [];
                    }
                    else {
                        actionModalRows[modals.value[i].rows[j].name] = "";
                    }
                }
            }
            modalAction.value[modals.value[i].name] = useForm({});
        }
    }
})


watch(
    filtersObj,
    () => {
        let routeObj = route().params;
        let sendedFilter = getSendedFilter();
        store.setFilters(sendedFilter);

        Inertia.get(route(props.list.url + ".index"), {
            ...routeObj,
            ...sendedFilter,
        }, {
            preserveScroll: true,
            preserveState: true
        });
    },
    {
        deep: true,
    }
);

// Check if The Sidebar is expanded or not
const layoutStore = useLayoutStore();
layoutStore.Breadcrumbs = [];

layoutStore.setBreadcrumbs({
    route: route("dashboard"),
    label: trans('global.dashboard')
});
layoutStore.setBreadcrumbs({
    route: route(props.list.url +".index"),
    label: rLang.value ? rLang.value.index : ''
});
function closeModal(type){
    if(type === 'createModal'){
        createModal.value = !createModal.value;
    }
    if(type === 'viewModal'){
        viewModal.value = !viewModal.value;
    }
    if(type === 'deleteModal'){
        deleteModal.value = !deleteModal.value;
    }
    if(type === 'bulkModal'){
        bulkModal.value = !bulkModal.value;
    }
}

let firstTime = ref(false);
let collectionData = JSON.parse(JSON.stringify(props.collection.data));

function activeSelectedAction(row, item, value, index){
    if(firstTime.value || (collectionData[index][row.name] === 0 && row.vue === 'ViltSwitch.vue')){
        Inertia.post(route(props.list.url + ".action", item.id), {
            action: row.name,
            value: value
        }, {
            preserveScroll: true,
            preserveState: true
        });
        firstTime.value = true;
    }
    else {
        firstTime.value = true;
    }
}
</script>

<template>
    <div>

        <!-- Images Light Box -->
        <vue-easy-lightbox
            ref="lightbox"
            :visible="visible"
            :imgs="images"
            :index="index"
            @hide="visible = !visible"
        ></vue-easy-lightbox>
        <div class="filament-page filament-resources-list-records-page" :class="'filament-resources-'+props.list.url">
            <!-- Main Resource Header -->
            <Head :title="rLang ? rLang.index : ''"></Head>
            <Header
                v-if="rLang"
                :canCreate="roles.create ? roles.create : false"
                :title="rLang ? rLang.index : ''"
                :button="rLang ? rLang.create: ''"
                :url="props.list.url+'.index'"
                @createItem="createItem"
            >
                <!-- Actions Generator -->
                <a
                    class="
                filament-button
                inline-flex
                items-center
                justify-center
                py-1
                gap-1
                font-medium
                rounded-lg
                border
                transition-colors
                focus:outline-none
                focus:ring-offset-2
                focus:ring-2
                focus:ring-inset
                dark:focus:ring-offset-0
                min-h-[2.25rem]
                px-4
                text-sm
                text-white
                shadow
                focus:ring-white
                border-transparent
                bg-primary-600
                hover:bg-primary-500
                focus:bg-primary-700
                focus:ring-offset-primary-700
                filament-page-button-action"
                    v-for="(action, index) in actions"
                    :key="index"
                    :href="action.url ? action.url : '#'"
                    @click.prevent="
                        !action.url
                            ? action.modal
                                ? openModal(action.modal, null, action.confirmed)
                                : fireAction(action.action, null, action.confirmed, action.actionMethod)
                            : openUrl(action.url, action.confirmed)
                    "
                >
                <i v-if="action.icon" :class="action.icon" style="font-size: 20px"></i>
                <span class="">
                    {{ action.label }}
                </span>
                </a>
            </Header>

            <!-- Widgets Generator -->
            <div class="grid grid-cols-1 gap-4 mb-6 filament-widgets-container lg:grid-cols-2 lg:gap-8" v-if="widgets.length">
                <div class="filament-widget col-span-full filament-stats-overview-widget">
                    <div>
                        <div class="grid gap-4 filament-stats lg:gap-8 md:grid-cols-4"
                        >
                            <div v-for="(item, key) in widgets"
                                class="relative p-6 bg-white shadow filament-stats-card rounded-2xl dark:bg-gray-800 filament-stats-overview-widget-card"
                                :class="{
                                                             'col-span-4 lg:col-span-4 md:col-span-4 sm:col-span-4': widgets.length === 1,
                                                             'col-span-4 lg:col-span-2 md:col-span-2 sm:col-span-2': widgets.length === 2,
                                                         }">
                                <div class="space-y-2">
                                    <div
                                        class="flex items-center space-x-2 text-sm font-medium text-gray-500 rtl:space-x-reverse dark:text-gray-200">

                                        <span>{{item.label}}</span>
                                    </div>

                                    <div class="text-3xl">
                                        {{item.value}}
                                    </div>

                                    <div v-if="item.icon || item.description"
                                        class="flex items-center space-x-1 text-sm font-medium rtl:space-x-reverse " :class="{
                                                                        'text-success-600': item.type === 'success',
                                                                        'text-danger-600': item.type === 'danger',
                                                                        'text-primary-600': item.type === 'primary',
                                                                        'text-warning-600': item.type === 'warning',
                                                                    }">
                                        <i v-if="item.icon" class="w-4 h-4 " :class="item.icon" style="font-size:16px"></i>
                                        <span v-if="item.description">{{item.description}}</span>
                                    </div>

                                </div>

                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>

                            <div class="my-4 filament-tables-component">
                                <div
                                    class="bg-white border border-gray-300 shadow-sm rounded-xl filament-tables-container dark:bg-gray-800 dark:border-gray-700">
                                    <div>
                                        <div class="flex items-center justify-between p-2 h-14">
                                            <div class="flex items-center gap-2">
                                                <Bulk :bulk="bulkItems" :show="showBlukModal" :collection="props.collection"
                                                    @close="showBlukModal = !showBlukModal">
                                                    <button v-if="roles.deleteAny" @click="bulkAction('delete')" type="button"
                                                        class="flex items-center w-full h-8 px-3 text-sm font-medium filament-dropdown-item group whitespace-nowrap focus:outline-none hover:text-white focus:text-white hover:bg-danger-600 focus:bg-danger-700">
                                                        <svg class="flex-shrink-0 w-5 h-5 mr-2 -ml-1 rtl:ml-2 rtl:-mr-1 group-hover:text-white group-focus:text-white text-danger-500"
                                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                                            aria-hidden="true">
                                                            <path fill-rule="evenodd"
                                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                        <span class="truncate">
                                                            {{ rLang.bulk }}
                                                        </span>
                                                    </button>
                                                </Bulk>
                                            </div>


                                            <div class="flex items-center justify-end w-full gap-2 md:max-w-md">
                                                <Filters :showFilter="
                                                                (props.render.table.filters &&
                                                                props.render.table.filters.length)? true : false
                                                            " :setSearch="search" :rows="rows" :url="props.list.url" @reset="resetFilter">
                                                    <ViltForm v-for="(filter, key) in props.render.table.filters" v-model="filtersObj[filter.name]"
                                                        :rows="filter.rows" :key="key" @update:modelValue="setFilter(filter)" />
                                                </Filters>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Table -->
                                    <div class="relative overflow-y-auto border-t dark:border-gray-700">
                                        <List @media="popUp" :roles="roles" :table="props.render.table" :rows="props.rows"
                                            :collection="props.collection" :bulk="bulkItems" :url="props.list.url" :desc="desc" @reload="reloadList"
                                            @view="viewItem" @edit="editItem" @delete="deleteItem" @all="bulkAll" @switch="switchBulk">
                                            <template v-if="props.render.table.rows && props.render.table.rows.length" #th>
                                                <th class="p-0 capitalize filament-tables-header-cell" v-for="(th, key) in props.render.table.rows"
                                                    :key="key">
                                                    <span
                                                        class="flex items-center w-full px-4 py-2 space-x-1 text-sm font-medium text-gray-600 whitespace-nowrap rtl:space-x-reverse dark:text-gray-300 ">
                                   {{th.label}}
                                </span>
                            </th>
                        </template>
                        <template v-if="props.render.table.rows && props.render.table.rows.length" #td="{ item, key }">
                            <td v-for="(td, key) in props.render.table.rows" :key="key" class="w-full">
                                <Component
                                    :is="td.vue.replace('.vue', '')"
                                    :row="td"
                                    :message="errors[td.name]"
                                    v-model="item[td.name]"
                                    @update:modelValue="activeSelectedAction(td, item, item[td.name], key)"
                                    :view="td.viewType"
                                    :label="false"
                                />
                            </td>
                        </template>
                        <template #actions="{ id }">
                            <div v-for="(action, index) in props.render.table.actions" :key="index">
                                <button
                                    v-if="action.can"
                                    @click.prevent="
                                      !action.url
                                        ? action.modal
                                          ? openModal(action.modal, id, action.confirmed)
                                          : fireAction(action.action, id, action.confirmed, action.actionMethod)
                                        : openUrl(action.url +'/'+id, action.confirmed)
                                    "
                                    class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action"
                                    :class="'text-' + action.type + '-700 hover:text-' + action.type + '-600'"
                                    role="button"
                                >
                                    <i class="text-[16px] filament-link-icon w-4 h-4 mr-1 rtl:ml-1" :class="action.icon"></i>
                                    {{ action.label }}
                                </button>
                            </div>
                        </template>
                    </List>
                </div>
                <div class="p-2" :class="{'border-t':props.collection.data.length}" v-show="props.collection.data.length">
                    <Pagination class="px-2" :collection="props.collection"/>
                </div>
            </div>
        </div>
        <!-- Actions Modals -->
        <CreateModal
            v-if="props.render.form.name !== 'page'"
            :url="props.list.url"
            :title="edit ? rLang.edit_title : rLang.create_title"
            :errors="props.errors"
            :show="createModal"
            :edit="edit"
            :item="form"
            :rows="props.rows"
            :type="props.render.form"
            @close="closeModal('createModal')"
            @success="success"
        />
        <ViewModal
            @media="popUp"
            :title="rLang.view_title"
            :show="viewModal"
            :item="form"
            :rows="props.rows"
            @close="closeModal('viewModal')"
        />
        <DeleteModal
            :url="props.list.url"
            :title="rLang.delete_title"
            :show="deleteModal"
            :item="form"
            @close="closeModal('deleteModal')"
            @success="success"
        />
        <BulkModal
            :url="props.list.url"
            :title="rLang.bulk_title"
            :action="bulkActionTitle"
            :bulk="bulkItems"
            :show="bulkModal"
            @close="closeModal('bulkModal')"
            @success="success"
        />

        <!-- Modals Generator -->
        <JetDialogModal
            v-for="(item, key) in modals"
            :key="key"
            :show="actionModal[item.name]"
        >
            <template #title>
                <div class="flex justify-between">
                    <h2 class="text-xl font-bold tracking-tight filament-modal-heading">{{ item.label }}</h2>
                </div>
            </template>

            <template #content>
                <form action="" enctype="multipart/form-data" v-if="!item.body">
                    <ViltForm
                        v-model="modalAction[item.name]"
                        :rows="item.rows"
                        :errors="props.errors"
                    />
                </form>
                <div v-else>
                    <div v-html="item.body"></div>
                </div>
            </template>

            <template #footer>
                <div class="flex flex-wrap items-center gap-4 filament-modal-actions rtl:space-x-reverse">
                    <button
                        class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-modal-button-action"
                        v-for="(button, key) in item.buttons"
                        :key="key"
                        @click.prevent="modalActionRun(item.name, button.action)"
                    >{{ button.label }}
                    </button>
                    <button
                        class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-page-modal-button-action"
                        @click="actionModal[item.name] = !actionModal[item.name]"
                    >
                        {{ trans('global.close') }}
                    </button>
                </div>
            </template>
        </JetDialogModal>

        <!-- Confirm Generator -->
        <JetDialogModal
            maxWidth="sm"
            :show="showConfirmation"
        >
            <template #content>
                <div class="p-4 space-y-2 text-center dark:text-white">
                    <h2 class="text-xl font-bold tracking-tight filament-modal-heading" id="ozo9fHMwMlC1FjErUz3l-table-bulk-action.heading">
                        Action
                    </h2>
                    <h3 class="text-gray-500 filament-modal-subheading">
                        Do You Went to Process This Action?
                    </h3>
                </div>
            </template>

            <template #footer>
                <div class="filament-modal-actions grid gap-2 grid-cols-[repeat(auto-fit,minmax(0,1fr))]">
                    <button
                        class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700 filament-tables-modal-button-action"
                        @click.prevent="processCurrentAction()"
                    >
                        Confirm
                    </button>
                    <button
                        class="filament-button inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:hover:border-gray-500 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 dark:focus:bg-gray-800 filament-tables-modal-button-action"
                        @click="showConfirmation = !showConfirmation"
                    >
                        {{ trans('global.close') }}
                    </button>
                </div>
            </template>
        </JetDialogModal>
    </div>
</template>

<script>
import AppLayout from "@@/Layouts/AppLayout.vue"
import ViltText from '$$/ViltText.vue';
import ViltTel from '$$/ViltTel.vue';
import ViltTextArea from '$$/ViltTextArea.vue';
import ViltNumber from '$$/ViltNumber.vue';
import ViltSelect from '$$/ViltSelect.vue';
import ViltSwitch from '$$/ViltSwitch.vue';
import ViltColor from '$$/ViltColor.vue';
import ViltEmail from '$$/ViltEmail.vue';
import ViltDate from '$$/ViltDate.vue';
import ViltDateTime from '$$/ViltDateTime.vue';
import ViltTime from '$$/ViltTime.vue';
import ViltMedia from '$$/ViltMedia.vue';
import ViltRepeater from '$$/ViltRepeater.vue';
import ViltSchema from '$$/ViltSchema.vue';
import ViltRich from '$$/ViltRich.vue';
import ViltRelation from '$$/ViltRelation.vue';
import ViltHasOne from '$$/ViltHasOne.vue';
import ViltSlug from '$$/ViltSlug.vue';

export default {
    layout: AppLayout,
    components: {
        ViltText,
        ViltTel,
        ViltTextArea,
        ViltNumber,
        ViltSelect,
        ViltSwitch,
        ViltColor,
        ViltEmail,
        ViltDate,
        ViltDateTime,
        ViltTime,
        ViltMedia,
        ViltRepeater,
        ViltSchema,
        ViltRich,
        ViltRelation,
        ViltHasOne,
        ViltSlug,
    }
};
</script>

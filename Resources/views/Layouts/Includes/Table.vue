<script setup>
import { computed, ref } from 'vue';
import { useReload } from '@@/Composables/useReload';
import { useCookies } from 'vue3-cookies';
import { storeToRefs } from 'pinia';
import {useTrans} from "@@/Composables/useTrans";
import { useResourceStore } from '@@/Stores/resourceStore';
import {useForm} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    rows: Array,
    cols: {
        type: Boolean,
        default: true
    },
    hasBulk: {
      type: Boolean,
      default: true
    },
    roles: Object,
    collection: Object,
    url: String,
    bulk: Object,
    desc: {
        type: Boolean,
        default: true
    },
    table: Object,
});

/*
Load Store
 */
let store = useResourceStore();

/*
Main Method
 */
let { reload } = useReload();
let {trans} = useTrans();
let { cols } = storeToRefs(useResourceStore());
const { cookies } = useCookies();

const listRows = computed(() => {
    let list = [];
    for (let i = 0, ii = props.rows.length; i < ii; i++) {
        if(props.cols){
            if (props.rows[i].list && cols.value[props.rows[i].name]) {
                list.push(props.rows[i]);
            }
        }
        else {
            if (props.rows[i].list) {
                list.push(props.rows[i]);
            }
        }
    }
    return list;
});

/*
Set Data
 */

let bulkValue = ref(false)


const emit= defineEmits([
    "all",
    "view",
    "edit",
    "delete",
    "media",
    "switch",
])
/*
Events
 */
function bulkAll() {
    emit('all', bulkValue.value);
}
function viewItem(item) {
    emit('view', item);
}
function editItem(item) {
    emit('edit', item);
}
function deleteItem(item) {
    emit('delete', item);
}
function popUp(images) {
    emit('media', images);
}
function switchValue(id) {
    emit('switch', id);
}
/*
Methods
 */

// Convert Number To Money Type
function money(item) {
    if (localStorage.getItem('lang')) {
        let lang = JSON.parse(localStorage.getItem('lang'));
        if (lang.id === 'ar') {
            return item.toLocaleString('en-US', {
                style: 'currency',
                currency: 'EGP',
            });
        } else if (lang.id === 'en') {
            return item.toLocaleString('ar-EG', {
                style: 'currency',
                currency: 'EGP',
            });
        }
    }
}
// Update Data On Click For Toggle
function updateData(item) {
    let form = useForm(item);
    Inertia.post(route(props.url + '.update', item.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            defineEmits('success', item);
        },
    });
}
// Trans Item When it has Multi Lang
function transItem(item, key) {
    if (
        item[key].hasOwnProperty('ar') &&
        item[key].hasOwnProperty('en')
    ) {
        if (localStorage.getItem('lang')) {
            let lang = JSON.parse(localStorage.getItem('lang'));
            if (lang.id === 'ar') {
                return item[key].en;
            } else if (lang.id === 'en') {
                return item[key].ar;
            }
        }
    } else {
        return item[key];
    }
}

let isDataObject = typeof props.collection.data === 'object';

let dataLength = computed(()=>{
    let getDataLength = 0;
    if(isDataObject){
        getDataLength = Object.keys(props.collection.data).length;
    }
    else {
        getDataLength = props.collection.data.length;
    }

    return getDataLength;
})

let filterData = computed(()=>{
    if(isDataObject){
        for(let i=0; i<dataLength; i++){

            for(let r=0; r<props.rows.length; r++){
                if(props.rows[r].name.includes('.')){
                    let nameArray = props.rows[r].name.split('.');
                    if(props.rows[r].max){
                        props.collection.data[Object.keys(props.collection.data)[i]][props.rows[r].name] =  nameArray.reduce((value, entry) => value[entry], props.collection.data[Object.keys(props.collection.data)[i]]).split(' ').slice(0, props.rows[r].max).join('  ')+'...';
                    }
                    else {
                        props.collection.data[Object.keys(props.collection.data)[i]][props.rows[r].name] =  nameArray.reduce((value, entry) => value[entry], props.collection.data[Object.keys(props.collection.data)[i]]);
                    }
                }
            }
        }
    }
    else {
        for(let i=0; i<dataLength; i++){
            for(let r=0; i<props.rows.length; i++){
                let nameArray = props.rows.name.split('.');
                props.collection.data[i][props.rows.name] =  nameArray.reduce((value, entry) => value[entry], props.collection.data[i]);
            }
        }
    }

    return props.collection.data;

})

</script>
<template>
    <div v-if="table.name === 'table'">
        <table
            class="w-full text-left divide-y table-auto filament-tables-table rtl:text-right dark:divide-gray-700"
            v-if="dataLength"
        >
            <thead>
                <tr
                    class="bg-gray-500/5"
                >
                    <th
                        v-if="props.hasBulk"
                        class="w-4 px-4 filament-tables-checkbox-cell whitespace-nowrap"
                    >
                        <input
                            @change="bulkAll()"
                            v-model="bulkValue"
                            class="block border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                            type="checkbox"
                        />
                    </th>
                    <th
                        class="p-0 capitalize filament-tables-header-cell"
                        v-for="(item, key) in listRows"
                        :key="key"
                    >
                        <button
                            v-if="item.sortable"
                            @click.prevent="
                                store.setDesc(!desc);
                                store.setOrderBy(item.name);
                                reload();
                            "
                            :class="{
                                'font-bold': store.orderBy == item.name,
                            }"
                            class="flex items-center w-full px-4 py-2 space-x-1 text-sm font-medium text-gray-600 whitespace-nowrap rtl:space-x-reverse dark:text-gray-300 "
                        >
                            <span class="capitalize">{{ item.label ? item.label : item.name }}</span>
                            <svg v-if="store.orderBy !== item.name"
                                class="w-3 h-3 opacity-25 filament-tables-header-cell-sort-icon dark:text-gray-300"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <svg v-if="!desc && (store.orderBy == item.name)"
                                class="w-3 h-3 filament-tables-header-cell-sort-icon dark:text-gray-300" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <svg v-else-if="desc && (store.orderBy == item.name)"
                                class="w-3 h-3 filament-tables-header-cell-sort-icon dark:text-gray-300" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            </button>
                            <span v-else
                                class="flex items-center w-full px-4 py-2 space-x-1 text-sm font-medium text-gray-600 whitespace-nowrap rtl:space-x-reverse dark:text-gray-300 ">
                                {{ item.label ? item.label : item.name }}
                            </span>
                            </th>
                            <slot name="th"></slot>

                            <th class="w-5">
                            </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y whitespace-nowrap dark:divide-gray-700">
                                <tr class="filament-tables-row hover:bg-gray-50 dark:hover:bg-gray-500/10" v-for="(item, key) in filterData"
                                    :key="key">
                                    <th v-if="props.hasBulk" class="w-4 px-4 filament-tables-checkbox-cell whitespace-nowrap">
                                        <input @input="switchValue(item.id)" :checked="bulk[item.id]"
                                            class="block border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-primary-200 focus:ring-opacity-50 table-row-checkbox"
                                            value="1" type="checkbox" />
                                    </th>
                                    <td v-for="(field, index) in listRows" :key="index"
                                        class="w-full px-2 cursor-pointer">
                                        <Component :is="field.vue.replace('.vue', '')" :row="field" v-model="item[field.name]" view="table" />
                                    </td>
                                    <slot name="td" :item="item" :key="index"></slot>
                                    <td v-if="
                                                        $slots['actions'] ||
                                                        props.roles.view ||
                                                        props.roles.viewAny ||
                                                        props.roles.edit ||
                                                        props.roles.delete ||
                                                        props.roles.deleteAny
                                                    " class="px-4 py-3 whitespace-nowrap filament-tables-actions-cell" :class="{
                                                        sorting: store.orderBy == item.field,
                                                    }">
                                        <div class="flex items-center justify-end gap-4 my-auto">
                                            <slot name="actions" v-bind="item"></slot>
                                            <div v-if="props.roles.view || props.roles.viewAny">
                                                <button @click.prevent="viewItem(item)"
                                                    class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-gray-600 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200 filament-tables-link-action">
                                                    <svg class="w-4 h-4 mr-1 filament-link-icon rtl:ml-1" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                        <path fill-rule="evenodd"
                                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ trans('global.view') }}
                                                </button>
                                            </div>
                                            <div v-if="props.roles.edit">
                                                <button @click.prevent="editItem(item)"
                                                    class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action"
                                                    role="button">
                                                    <svg class="w-4 h-4 mr-1 filament-link-icon rtl:ml-1" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                        </path>
                                                    </svg>
                                                    {{ trans('global.edit') }}
                                                </button>
                                            </div>
                                            <form v-if="
                                                                    props.roles.delete || props.roles.deleteAny
                                                                ">
                                                <button @click.prevent="deleteItem(item)" type="submit"
                                                    class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400 filament-tables-link-action">
                                                    <svg class="w-4 h-4 mr-1 filament-link-icon rtl:ml-1" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ trans('global.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            </table>
                            <div class="relative p-8 text-center" v-else>
                                <div class="flex items-center justify-center p-4">
                                    <div
                                        class="flex flex-col items-center justify-center flex-1 p-6 mx-auto space-y-6 text-center bg-white filament-tables-empty-state dark:bg-gray-800">
                                        <div
                                            class="flex items-center justify-center w-16 h-16 rounded-full text-primary-500 bg-primary-50 dark:bg-gray-700">
                                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>

                                        </div>

                                        <div class="max-w-md space-y-1">
                                            <h2 class="text-xl font-bold tracking-tight filament-tables-empty-state-heading dark:text-white">
                                                {{ trans('global.empty') }}
                                            </h2>

                                            <p
                                                class="text-sm font-medium text-gray-500 whitespace-normal filament-tables-empty-state-description dark:text-gray-400">

                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
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
import ViltIcon from '$$/ViltIcon.vue';

export  default {
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
        ViltIcon,
    }
}
</script>

<script setup>
import { computed, ref } from 'vue';
import { useReload } from '@@/Composables/useReload';
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
import { useCookies } from 'vue3-cookies';
import { storeToRefs } from 'pinia';
import {useTrans} from "@@/Composables/useTrans";
import { useResourceStore } from '@@/Stores/resourceStore';
import {useForm} from "@inertiajs/inertia-vue3";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    rows: Array,
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
        if (props.rows[i].list && cols.value[props.rows[i].name]) {
            list.push(props.rows[i]);
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

</script>
<template>
    <div v-if="table.name === 'table'">
        <table
            class="filament-tables-table w-full text-left rtl:text-right divide-y table-auto dark:divide-gray-700"
            v-if="props.collection.data.length"
        >
            <thead>
                <tr
                    class="bg-gray-500/5"
                >
                    <th
                        class="filament-tables-checkbox-cell w-4 px-4 whitespace-nowrap"
                    >
                        <input
                            @change="bulkAll()"
                            v-model="bulkValue"
                            class="block border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                            type="checkbox"
                        />
                    </th>
                    <th
                        class="filament-tables-header-cell p-0"
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
                            class="flex items-center w-full px-4 py-2 whitespace-nowrap space-x-1 rtl:space-x-reverse font-medium text-sm text-gray-600 dark:text-gray-300 "
                        >
                            <span>{{ item.label ? item.label : item.name }}</span>
                            <svg v-if="store.orderBy !== item.name" class="filament-tables-header-cell-sort-icon h-3 w-3 dark:text-gray-300 opacity-25" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <svg v-if="!desc && (store.orderBy == item.name)" class="filament-tables-header-cell-sort-icon h-3 w-3 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <svg v-else-if="desc && (store.orderBy == item.name)" class="filament-tables-header-cell-sort-icon h-3 w-3 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <span
                            v-else
                            class="flex items-center w-full px-4 py-2 space-x-1 text-sm font-normal cursor-default dark:text-white whitespace-nowrap rtl:space-x-reverse"
                        >
                            {{ item.label ? item.label : item.name }}
                        </span>
                    </th>
                    <slot name="th"></slot>

                    <th class="w-5">
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y whitespace-nowrap dark:divide-gray-700">
                <tr
                    class="filament-tables-row hover:bg-gray-50 dark:hover:bg-gray-500/10"
                    v-for="(item, key) in props.collection.data"
                    :key="key"
                >
                    <th
                        class="filament-tables-checkbox-cell w-4 px-4 whitespace-nowrap"
                    >
                        <input
                            @input="switchValue(item.id)"
                            :checked="bulk[item.id]"
                            class="block border-gray-300 rounded shadow-sm text-primary-600 focus:border-primary-600 focus:ring focus:ring-primary-200 focus:ring-opacity-50 table-row-checkbox"
                            value="1"
                            type="checkbox"
                        />
                    </th>
                    <td
                        class="filament-tables-cell dark:text-white"
                        v-for="(field, index) in listRows"
                        :key="index"
                    >
                        <ViltText
                            v-if="field.vue === 'ViltText.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltEmail
                            v-if="field.vue === 'ViltEmail.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltTel
                            v-if="field.vue === 'ViltTel.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltTextArea
                            v-if="field.vue === 'ViltTextArea.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltNumber
                            v-if="field.vue === 'ViltNumber.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltSelect
                            v-if="field.vue === 'ViltSelect.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltSwitch
                            v-if="field.vue === 'ViltSwitch.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltColor
                            v-if="field.vue === 'ViltColor.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltDate
                            v-if="field.vue === 'ViltDate.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltDateTime
                            v-if="field.vue === 'ViltDateTime.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltTime
                            v-if="field.vue === 'ViltTime.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltMedia
                            v-if="field.vue === 'ViltMedia.vue'"
                            @popUp="popUp"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltRepeater
                            v-if="field.vue === 'ViltRepeater.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltSchema
                            v-if="field.vue === 'ViltSchema.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltRich
                            v-if="field.vue === 'ViltRich.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltRelation
                            v-if="field.vue === 'ViltRelation.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />
                        <ViltHasOne
                            v-if="field.vue === 'ViltHasOne.vue'"
                            :row="field"
                            v-model="item[field.name]"
                            view="table"
                        />


                        <slot name="td"></slot>
                    </td>

                    <td
                        v-if="
                            $slots['actions'] ||
                            props.roles.view ||
                            props.roles.viewAny ||
                            props.roles.edit ||
                            props.roles.delete ||
                            props.roles.deleteAny
                        "
                        class="px-4 py-3 whitespace-nowrap filament-tables-actions-cell"
                        :class="{
                            sorting: store.orderBy == item.field,
                        }"
                    >
                        <div class="flex items-center justify-end gap-4 my-4">
                            <slot name="actions" v-bind="item"></slot>
                            <div>
                                <button
                                    v-if="props.roles.view || props.roles.viewAny"
                                    @click.prevent="viewItem(item)"
                                    class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-gray-600 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200 filament-tables-link-action"
                                >
                                    <svg class="filament-link-icon w-4 h-4 mr-1 rtl:ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ trans('global.view') }}
                                </button>
                            </div>
                            <div>
                                <button
                                    v-if="props.roles.edit"
                                    @click.prevent="editItem(item)"
                                    class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action"
                                    role="button"
                                >
                                    <svg class="filament-link-icon w-4 h-4 mr-1 rtl:ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                    {{ trans('global.edit') }}
                                </button>
                            </div>
                            <form>
                                <button
                                    v-if="
                                        props.roles.delete || props.roles.deleteAny
                                    "
                                    @click.prevent="deleteItem(item)"
                                    type="submit"
                                    class="filament-link inline-flex items-center justify-center gap-0.5 font-medium hover:underline focus:outline-none focus:underline text-sm text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400 filament-tables-link-action"
                                >
                                    <svg class="filament-link-icon w-4 h-4 mr-1 rtl:ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
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
                <div class="filament-tables-empty-state flex flex-1 flex-col items-center justify-center p-6 mx-auto space-y-6 text-center bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-center w-16 h-16 text-primary-500 rounded-full bg-primary-50 dark:bg-gray-700">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>

                    </div>

                    <div class="max-w-md space-y-1">
                        <h2 class="filament-tables-empty-state-heading text-xl font-bold tracking-tight dark:text-white">
                            {{ trans('global.empty') }}
                        </h2>

                        <p class="filament-tables-empty-state-description whitespace-normal text-sm font-medium text-gray-500 dark:text-gray-400">

                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

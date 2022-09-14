<script setup>
import {ref} from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    row: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        required: false,
    },
})

const emit = defineEmits(['update:modelValue']);

let value = ref(props.modelValue);
</script>
<template>
    <div :class="'col-span-'+row.col ? row.col : '1'">
        <div class="filament-forms-field-wrapper">
            <div class="space-y-2">
                <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                    <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" :for="props.row.name">
                        <span
                            class="text-sm font-medium leading-4 text-gray-700 dark:text-gray-300"
                              :class="props.errors ? 'text-danger-700' : ''"
                        >
                            {{props.row.label ? props.row.label : props.row.name}}
                            <sup class="font-medium text-danger-700" v-if="props.row.required">*</sup>
                        </span>
                    </label>
                </div>
                <div class="filament-forms-text-input-component flex items-center space-x-2 rtl:space-x-reverse group">
                    <div class="flex-1">
                        <input :type="props.row.type"
                               :class="props.errors ? 'border-danger-600 ring-danger-600 dark:border-gray-600 border-gray-300' : ''"
                               v-model="value"
                               @input="emit('update:modelValue', value)"
                               autocomplete="on"
                               :id="props.row.name"
                               :required="props.row.required"
                               class="block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 border-gray-300 dark:border-gray-600"
                        >
                    </div>
                </div>
                <p class="filament-forms-field-wrapper-error-message text-sm text-danger-600" v-if="props.errors">
                   {{props.errors}}
                </p>
            </div>
        </div>
    </div>
</template>

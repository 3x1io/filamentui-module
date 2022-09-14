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

function update(){
    if(form.value[0] === 'on'){
        emit('update:modelValue', 1);
    }
    else {
        emit('update:modelValue', 0);
    }
}

let form = ref([]);

if(props.modelValue === "1"){
    form.value = ['on'];
}
else {
    form.value = [];
}

</script>
<template>
    <div :class="'col-span-'+row.col ? row.col : '1'">
        <div class="filament-forms-field-wrapper">
            <div class="space-y-2">
                <div class="flex items-center justify-between space-x-2 rtl:space-x-reverse">
                    <label class="filament-forms-field-wrapper-label inline-flex items-center space-x-3 rtl:space-x-reverse" for="remember">
                        <input
                            type="checkbox"
                            v-model="form"
                            @change="update"
                            :id="props.row.name"
                            :required="props.row.required"
                            class="filament-forms-checkbox-component text-primary-600 transition duration-75 rounded shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:checked:bg-primary-500 border-gray-300 dark:border-gray-600">

                        <span class="text-sm font-medium leading-4 text-gray-700 dark:text-gray-300">
                            {{props.row.label ? props.row.label : props.row.name}}
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

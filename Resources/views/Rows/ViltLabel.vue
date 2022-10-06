<template>
    <label v-if="row.name" :for="row.name" class="text-sm font-medium leading-4 text-gray-700 dark:text-gray-300 capitalize" :class="{'text-red-600': message}">{{
            row.label ? row.label : row.name
        }}
        <span v-if="row.required" class="text-red-600 text-bold">*</span>
    </label>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        row: {
            Object,
            default: {},
        },
    },
});
</script>

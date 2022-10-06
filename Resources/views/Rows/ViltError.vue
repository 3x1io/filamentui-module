<template>
    <small v-if="row.hint" class="text-gray-400 mx-2">{{row.hint}}</small>
    <JetInputError v-if="message" :message="message" class="mt-2" />
</template>

<script>
import { defineComponent } from "vue";
import JetInputError from '@@/Jetstream/InputError.vue';

export default defineComponent({
    components:{
        JetInputError
    },
    props: {
        row: {
            Object,
            default: {},
        },
        message: {
            String,
            default: null,
        },
    },
});
</script>

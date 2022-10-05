<template>
    <div>
        <div v-for="(item, key) in rows" :key="key">
            <Component v-if="item.vue === 'ViltHasOne.vue'" :is="item.vue.replace('.vue', '')" :row="item" :view="view" @update:modelValue="update" v-model="form[item.name]" :message="errors[item.name]"></Component>
            <Component v-else :is="item.vue.replace('.vue', '')" :row="item" :view="view" @update:modelValue="update" v-model="form[item.name]" :message="errors[item.name]"></Component>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import JetInputError from '@@/Jetstream/InputError.vue';
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

export default defineComponent({
    components: {
        JetInputError,
        ViltTextArea,
        ViltText,
        ViltTel,
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
    },
    data() {
        return {
            form: {},
        };
    },
    props: {
        modelValue: {},
        rows: {
            Array,
            default: [],
        },
        errors: {
            Object,
            default: {},
        },
        edit: {
            Boolean,
            default: false,
        },
        value: {
            Object,
            default: {},
        },
        view: {
            String,
            default: "input",
        },
    },
    computed:{
        getRows(){
            let rows = this.$props.rows;
            let getRows = {};
            for (let i = 0; i < rows.length; i++) {
                if(rows[i].default){
                    getRows[rows[i].name] = rows[i].default;
                }
                else {
                    if(rows[i].vue === 'ViltRelation.vue' || rows[i].vue === 'ViltRepeater.vue' || rows[i].vue === 'ViltSchema.vue'){
                        getRows[rows[i].name] = [];
                    }
                    else {
                        getRows[rows[i].name] = "";
                    }
                }
            }

            return getRows;
        }
    },
    mounted() {
        if(Object.keys(this.value).length){
            this.form = this.value;
        }
        else {
            this.form = this.getRows;
        }
        const elemetns = document.querySelectorAll("[autocomplete=\"off\"]");
        elemetns.forEach((element)=>{
            element.setAttribute('readonly', 'readonly');
            element.style.backgroundColor = "inherit";

            setTimeout(()=>{
                element.removeAttribute("readonly");
            }, 500)
        })

    },
    beforeUpdate() {
        if(this.edit){
            this.form = this.modelValue
        }
    },
    methods: {
        update() {
            if (this.form.hasOwnProperty('errors')) {
                this.$emit('update:modelValue', this.form);
            } else {
                this.$emit('update:modelValue', this.form);
            }
        },
    },
});
</script>

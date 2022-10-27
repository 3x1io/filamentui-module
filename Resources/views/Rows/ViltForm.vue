<template>
    <div class="grid grid-cols-12 gap-4">
        <div v-for="(item, key) in getFinalRows" :key="key" :class="'col-span-'+item.col">
            <div v-if="item.reactive">
                <Component
                    v-if="item.vue === 'ViltHasOne.vue' && form[item.reactiveRow] && (form[item.reactiveRow][item.reactiveBy] === item.reactiveWhere || item.reactiveWhere === null)"
                    :is="item.vue.replace('.vue', '')" :row="item" :view="view" @update:modelValue="update"
                    v-model="form[item.name]" :message="errors[item.name]"></Component>
                <Component
                    v-else-if="item.vue === 'ViltSlug.vue' && form[item.reactiveRow] && (form[item.reactiveRow][item.reactiveBy] === item.reactiveWhere || item.reactiveWhere === null)"
                    :is="item.vue.replace('.vue', '')" :row="item" :view="view" @update:modelValue="update"
                    v-model="form[item.name]" :message="errors[item.name]"></Component>
                <Component v-else-if="form[item.reactiveRow] && (form[item.reactiveRow][item.reactiveBy] === item.reactiveWhere || item.reactiveWhere === null)"
                    :is="item.vue.replace('.vue', '')" :row="item" :view="view" @update:modelValue="update"
                    v-model="form[item.name]" :message="errors[item.name]"></Component>
            </div>
            <div v-if="!item.reactive">
                <Component v-if="item.vue === 'ViltHasOne.vue'" :is="item.vue.replace('.vue', '')" :row="item" :view="view"
                    @update:modelValue="update" v-model="form[item.name]" :message="errors[item.name]"></Component>
                <Component v-else-if="item.vue === 'ViltSlug.vue'" :is="item.vue.replace('.vue', '')" :row="item" :view="view"
                    @update:modelValue="update" v-model="form[item.name]" :message="errors[item.name]"></Component>
                <Component v-else :is="item.vue.replace('.vue', '')" :row="item" :view="view" @update:modelValue="update"
                    v-model="form[item.name]" :message="errors[item.name]"></Component>
            </div>
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
import ViltSlug from '$$/ViltSlug.vue';
import ViltIcon from '$$/ViltIcon.vue';
import ViltSection from '$$/ViltSection.vue';

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
        ViltSlug,
        ViltIcon,
        ViltSection
    },
    data() {
        return {
            form: {},
            getFinalRows: []
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
            this.getFinalRows = this.$props.rows;
            let getRows = {};
            for (let i = 0; i <  this.getFinalRows.length; i++) {
                if ( this.getFinalRows[i].default) {
                    getRows[ this.getFinalRows[i].name] =  this.getFinalRows[i].default;
                }
                else {
                    if ( this.getFinalRows[i].vue === 'ViltRelation.vue' ||  this.getFinalRows[i].vue === 'ViltRepeater.vue' ||  this.getFinalRows[i].vue === 'ViltSchema.vue') {
                        getRows[ this.getFinalRows[i].name] = [];
                    }
                    else {
                        getRows[ this.getFinalRows[i].name] = "";
                    }
                }

            }
            return getRows;
        }
    },
    watch:{
        form: {
            handler: function (val) {
                for(let i=0; i<this.$props.rows.length; i++){
                    if(this.$props.rows[i].vue === 'ViltSlug.vue'){
                        this.form[this.$props.rows[i].name] = this.vueSlug(val[this.$props.rows[i].reactiveRow] ? val[this.$props.rows[i].reactiveRow]: "");
                    }
                }
                for (let i = 0; i < this.getFinalRows.length; i++) {
                    if(this.getFinalRows[i].vue === 'ViltSelect.vue' && this.getFinalRows[i].options.length){

                        for(let x=0; x<this.getFinalRows[i].options.length; x++){
                            if(this.getFinalRows[i].options[x].hasOwnProperty('apiRow') && this.getFinalRows[i].options[x].apiRow !== null){
                                for(let y=0; y<this.getFinalRows.length; y++){
                                    if(this.getFinalRows[y].name === this.getFinalRows[i].options[x].apiRow){
                                        if(this.form[this.getFinalRows[i].name] && this.form[this.getFinalRows[i].name][this.getFinalRows[i].trackById] === this.getFinalRows[i].options[x].id){
                                            this.getFinalRows[y].label = this.getFinalRows[i].options[x].apiLabel;
                                            this.getFinalRows[y].model = this.getFinalRows[i].options[x].apiModel;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            },
            deep: true,
        },
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
        vueSlug: function(title) {
            var slug = "";
            // Change to lower case
            var titleLower = title.toLowerCase();
            // Letter "e"
            slug = titleLower.replace(/e|é|è|ẽ|ẻ|ẹ|ê|ế|ề|ễ|ể|ệ/gi, 'e');
            // Letter "a"
            slug = slug.replace(/a|á|à|ã|ả|ạ|ă|ắ|ằ|ẵ|ẳ|ặ|â|ấ|ầ|ẫ|ẩ|ậ/gi, 'a');
            // Letter "o"
            slug = slug.replace(/o|ó|ò|õ|ỏ|ọ|ô|ố|ồ|ỗ|ổ|ộ|ơ|ớ|ờ|ỡ|ở|ợ/gi, 'o');
            // Letter "u"
            slug = slug.replace(/u|ú|ù|ũ|ủ|ụ|ư|ứ|ừ|ữ|ử|ự/gi, 'u');
            // Letter "d"
            slug = slug.replace(/đ/gi, 'd');
            // Trim the last whitespace
            slug = slug.replace(/\s*$/g, '');
            // Change whitespace to "-"
            slug = slug.replace(/\s+/g, '-');

            return slug;
        },
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

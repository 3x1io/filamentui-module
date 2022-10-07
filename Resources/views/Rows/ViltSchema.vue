<template>
    <div class="py-2 px-2" v-if="view === 'input'">
        <ViltLabel :row="row" v-if="label" />

        <div>
            <div
                class="p-4 my-4 border rounded-lg"
                v-if="main"
            >
                <div v-for="(item, key) in row.options">
                    <ViltText v-if="item.vue === 'ViltText.vue'" :row="item" v-model="main[item.name]" />
                    <ViltEmail v-if="item.vue === 'ViltEmail.vue'" :row="item" v-model="main[item.name]" />
                    <ViltTel v-if="item.vue === 'ViltTel.vue'" :row="item" v-model="main[item.name]" />
                    <ViltTextArea v-if="item.vue === 'ViltTextArea.vue'" :row="item" v-model="main[item.name]" />
                    <ViltNumber v-if="item.vue === 'ViltNumber.vue'" :row="item" v-model="main[item.name]" />
                    <ViltSelect v-if="item.vue === 'ViltSelect.vue'" :row="item" v-model="main[item.name]" />
                    <ViltSwitch v-if="item.vue === 'ViltSwitch.vue'" :row="item" v-model="main[item.name]" />
                    <ViltColor v-if="item.vue === 'ViltColor.vue'" :row="item" v-model="main[item.name]" />
                    <ViltDate v-if="item.vue === 'ViltDate.vue'" :row="item" v-model="main[item.name]" />
                    <ViltDateTime v-if="item.vue === 'ViltDateTime.vue'" :row="item" v-model="main[item.name]" />
                    <ViltTime v-if="item.vue === 'ViltTime.vue'" :row="item" v-model="main[item.name]" />
                    <ViltMedia v-if="item.vue === 'ViltMedia.vue'" :row="item" v-model="main[item.name]" />
                </div>
            </div>
        </div>
        <ViltError :row="row" :message="message" />
    </div>

    <div v-if="view === 'view' && main">
        <div class="flex justify-between my-4">
            <div>
                <p class="font-bold capitalize">{{ row.label ? row.label : row.name }}</p>
            </div>
            <div></div>
        </div>
        <div>
            <table class="w-full table-normal">
                <tbody>
                <tr
                    v-for="(item, key) in row.options"
                    :key="key"
                    class="py-2 px-2"
                >
                    <th class="border">
                        {{item.label ? item.label : item.name}}
                    </th>
                    <td class="border w-full">
                        <div v-if="item.vue === 'ViltMedia.vue'">
                            <img :src="main[item.name]" alt="" class="w-16" />
                        </div>
                        <div v-else>
                            <p v-if="main[item.name]" class="px-2 py-2">{{ main[item.name] }}</p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div v-if="view === 'table' && main">
        <div class="mx-1 my-1">
            <table class="w-full table-normal">
                <tbody>
                <tr
                    v-for="(item, key) in row.options"
                    :key="key"
                    class="py-2 px-2"
                >
                    <th class="border py-2 px-2">
                        {{item.label ? item.label : item.name}}
                    </th>
                    <td class="border w-full py-2 px-2">
                        <div v-if="item.vue === 'ViltMedia.vue'">
                            <img :src="main[item.name]" alt="" class="w-16" />
                        </div>
                        <div v-else>
                            <p v-if="main[item.name]" class="px-2 py-2">{{ main[item.name] }}</p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetInputError from "@@/Jetstream/InputError.vue";
import ViltText from '$$/ViltText.vue'
import ViltTel from '$$/ViltTel.vue'
import ViltTextArea from "$$/ViltTextArea.vue";
import ViltNumber from "$$/ViltNumber.vue";
import ViltSelect from "$$/ViltSelect.vue";
import ViltSwitch from "$$/ViltSwitch.vue";
import ViltColor from "$$/ViltColor.vue";
import ViltEmail from "$$/ViltEmail.vue";
import ViltDate from "$$/ViltDate.vue";
import ViltDateTime from "$$/ViltDateTime.vue";
import ViltTime from "$$/ViltTime.vue";
import ViltMedia from "$$/ViltMedia.vue";
import ViltLabel from "$$/ViltLabel.vue";
import ViltError from "$$/ViltError.vue";

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
        ViltLabel,
        ViltError
    },
    data() {
        return {
            main: {},
        };
    },
    watch:{
      main:{
        handler: function (val, oldVal) {
          if(this.view === 'input'){
              this.$emit('update:modelValue', val);
          }
        },
        deep: true
      },
        modelValue: function (val) {
            if(this.view === 'input' && this.modelValue) {
                this.main = val;
            }
        },
    },
    mounted() {
        if (this.modelValue !== null) {
            this.main = this.modelValue;
        }
        else {
            this.main = this.optionRows;
        }
    },
    beforeUpdate() {
        if (this.row.default) {
            this.main = this.row.default;
        }
    },
    props: {
        modelValue: {},
        row: {
            Object,
            default: {},
        },
        view: {
            String,
            default: "input",
        },
        message: {
            String,
            default: null,
        },
        label: {
            Boolean,
            default: true,
        }
    },
    computed:{
        optionRows(){
            let rows = {};
            for(let i=0; i<this.row.options.length; i++){
                if(this.row.options[i].default){
                    rows[this.row.options[i].name] = this.row.options[i].default;
                }
                else {
                    if(this.row.options[i].vue === 'ViltSwitch.vue'){
                        rows[this.row.options[i].name] = false;
                    }
                    else if(this.row.options[i].vue === 'ViltColor.vue'){
                        rows[this.row.options[i].name] = "#000000";
                    }
                    else {
                        rows[this.row.options[i].name] = "";
                    }
                }
            }
            return rows;
        }
    },
});
</script>

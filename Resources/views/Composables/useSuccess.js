import { useLang } from '@@/Composables/useLang';
import {computed} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";
import {createToaster} from "@meforma/vue-toaster";

export function useSuccess() {
    const toaster = createToaster();


    let getMessage = computed(()=>{
        return usePage().props.value.data?.message;
    })
    function success() {
        const message = getMessage.value;
        if (typeof message === 'object') {
            if (message.type === 'error') {
                toaster.error(message.message, {
                    position: 'top-right',
                    useDefaultCss: true
                });
            } else if (message.type === 'success') {
                toaster.success(message.message, {
                    position: 'top-right',
                    useDefaultCss: true
                });
            } else {
                toaster.success(message.message, {
                    position: 'top-right',
                    useDefaultCss: true
                });
            }
        } else {
            toaster.success(message, {
                position: 'top-right',
                useDefaultCss: true
            });
        }
    }
    return { success };
}

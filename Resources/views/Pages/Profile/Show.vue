<template>
        <div>
            <Container>
                <template #header>
                    <Header title="Edit Profile" />
                </template>
                <template #body>
                    <div class="grid grid-cols-1 filament-forms-component-container gap-8 mt-8">
                        <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                            <update-profile-information-form :user="$page.props.user" />
                        </div>

                        <div v-if="$page.props.jetstream.canUpdatePassword">
                            <update-password-form />
                        </div>

                        <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                            <two-factor-authentication-form/>
                        </div>

                        <logout-other-browser-sessions-form :sessions="sessions" />

                        <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                            <delete-user-form />
                        </template>

                        <NotificationForm />
                    </div>
                </template>
        </Container>
        </div>
</template>

<script>
    import { defineComponent } from 'vue'
    import NotificationForm from '@@/Pages/Profile/Partials/NotificationForm.vue'
    import DeleteUserForm from '@@/Pages/Profile/Partials/DeleteUserForm.vue'
    import JetSectionBorder from '@@/Jetstream/SectionBorder.vue'
    import LogoutOtherBrowserSessionsForm from '@@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue'
    import TwoFactorAuthenticationForm from '@@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue'
    import UpdatePasswordForm from '@@/Pages/Profile/Partials/UpdatePasswordForm.vue'
    import UpdateProfileInformationForm from '@@/Pages/Profile/Partials/UpdateProfileInformationForm.vue'
    import AppLayout from "@@/Layouts/AppLayout.vue";
    import Container from "@@/Components/Container.vue";
    import Header from "@@/Layouts/Includes/Header.vue";

    export default defineComponent({
        props: ['sessions'],
        layout: AppLayout,
        components: {
            Header,
            Container,
            DeleteUserForm,
            JetSectionBorder,
            LogoutOtherBrowserSessionsForm,
            TwoFactorAuthenticationForm,
            UpdatePasswordForm,
            UpdateProfileInformationForm,
            NotificationForm,
        },
    })
</script>

<template>
    <Head title="Profile" />
  
    <MainLayout :user="auth.user">
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Profile</h2>
      </template>
  
      <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
          <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
            <UpdateProfileInformationForm
              :must-verify-email="mustVerifyEmail"
              :status="status"
              class="max-w-xl"
            />
          </div>
  
          <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
            <UpdatePasswordForm class="max-w-xl" />
          </div>
  
          <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
            <DeleteUserForm class="max-w-xl" />
          </div>
        </div>
      </div>
    </MainLayout>
  </template>
  
  <script>
  import { Head } from '@inertiajs/vue3'
  import MainLayout from '@/Layouts/MainLayout.vue'
  import DeleteUserForm from './Partials/DeleteUserForm.vue'
  import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
  import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
  
  export default {
    props: {
      mustVerifyEmail: Boolean,
      status: String,
      auth: Object,
    },
    components: {
      Head,
      MainLayout,
      DeleteUserForm,
      UpdatePasswordForm,
      UpdateProfileInformationForm,
    },
  }
  </script>
  
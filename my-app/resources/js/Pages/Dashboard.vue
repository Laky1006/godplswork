<script setup>

import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <MainLayout :user="auth.user">
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script>
    import MainLayout from '@/Layouts/MainLayout.vue'

    export default {
    props: {
        auth: Object, // auth will have user inside it
    },
    components: {
        MainLayout,
    },
    }
</script>

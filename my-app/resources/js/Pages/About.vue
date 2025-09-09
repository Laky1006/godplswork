<template>
  <MainLayout :user="auth?.user">
    <section class="max-w-4xl mx-auto py-10 px-6">
      <h1 class="text-3xl font-bold mb-4">About Learnora</h1>
      <p class="text-gray-700 text-lg leading-relaxed">
        Learnora is a platform that connects students with teachers offering personalized lessons.
        Whether it's math, music, or languages - you can find the right mentor here.
      </p>
    </section>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
  props: {
    auth: Object,
  },
  components: {
    MainLayout,
  },
};
</script>

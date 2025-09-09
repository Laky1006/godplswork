<template>
  <div
    class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 overflow-hidden"
  >
    <!-- Clickable if linkTo is provided -->
    <component :is="linkTo ? Link : 'div'" :href="linkTo ? route(linkTo, lesson.id) : null" class="block">
      <img
        :src="lesson.banner ? `/storage/${lesson.banner}` : '/images/default-banner.jpg'"
        alt="Lesson Banner"
        class="w-full h-40 object-cover"
      />
      <div class="p-4">
        <h3 class="text-xl font-semibold mb-1">{{ lesson.title }}</h3>
        <p class="text-sm text-gray-600">Teacher: {{ teacherName }}</p>
        <p class="text-yellow-500 text-sm">⭐ {{ lesson.rating }}/5</p>
      </div>
    </component>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  props: {
    lesson: Object,
    linkTo: {
      type: String,
      default: null,
    },
  },
  components: {
    Link,
  },
  computed: {
    teacherName() {
      return this.lesson.teacher?.user?.name || 'Unknown';
    },
  },
};
</script>

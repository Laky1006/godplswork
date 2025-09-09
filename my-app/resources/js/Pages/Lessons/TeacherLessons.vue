<template>
  <MainLayout :user="auth.user">
    <section class="max-w-7xl mx-auto py-10 px-6">
      <h1 class="text-2xl font-bold mb-6">My Lessons</h1>

      <!-- New Lesson Button -->
      <div class="mb-6">
        <Link
          :href="route('lessons.create')"
          class="bg-[#0e66a1] text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          + New Lesson
        </Link>
      </div>

      <div v-if="lessons.length">
        <ul class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
          <li
            v-for="lesson in lessons"
            :key="lesson.id"
            class="bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden"
          >
            <!-- Banner -->
            <img
              :src="lesson.banner ? `/storage/${lesson.banner}` : '/images/default-banner.jpg'"
              alt="Lesson banner"
              class="w-full h-40 object-cover"
            />

            <!-- Content -->
            <div class="p-4">
              <h2 class="text-xl font-semibold mb-1">{{ lesson.title }}</h2>
              <p class="text-sm text-gray-600 mb-2">
                {{ lesson.description.length > 20 ? lesson.description.slice(0, 50) + '...' : lesson.description }}
              </p>

              <!-- Star Rating -->
              <div class="flex items-center gap-2 mb-4">
                <div class="flex text-yellow-500 text-lg">
                  <span
                    v-for="n in 5"
                    :key="n"
                    :class="lesson.rating >= n ? 'text-yellow-500' : 'text-gray-300'"
                  >
                    ★
                  </span>
                </div>
                <span class="text-gray-600 text-sm">
                  {{ lesson.rating ? `${lesson.rating}/5` : 'Not Rated' }}
                </span>
              </div>

              <!-- Edit Button -->
              <Link
                :href="route('lessons.edit', lesson.id)"
                class="inline-block bg-gray-800 text-white text-sm px-4 py-2 rounded hover:bg-gray-900"
              >
                Edit
              </Link>
            </div>
          </li>
        </ul>
      </div>

      <div v-else>
        <p class="text-gray-500">You haven't created any lessons yet.</p>
      </div>
    </section>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Link } from '@inertiajs/vue3'

export default {
  props: {
    lessons: Array,
    auth: Object,
  },
  components: {
    MainLayout,
    Link,
  },
}
</script>

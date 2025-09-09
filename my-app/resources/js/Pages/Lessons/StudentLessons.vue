<template>
  <MainLayout :user="auth.user">
    <section class="max-w-4xl mx-auto py-10 px-6">
      <h1 class="text-2xl font-bold mb-6">My Booked Lessons</h1>

      <div v-if="lessons.length">
        <transition-group name="fade" tag="ul" class="space-y-4">
          <li
            v-for="lesson in lessons"
            :key="lesson.id + lesson.date + lesson.time"
            v-show="!cancelingLessonIds.includes(lesson.id + lesson.date + lesson.time)"
            class="border border-gray-200 rounded p-4 bg-white shadow-sm flex items-center justify-between gap-4"
          >
            <!-- Left: Banner + Info -->
            <div class="flex items-center gap-4">
              <img
                :src="lesson.banner ? `/storage/${lesson.banner}` : '/images/default-banner.jpg'"
                alt="Lesson banner"
                class="w-24 h-24 object-cover rounded-md flex-shrink-0"
              />
              <div>
                <div class="text-xl font-semibold text-gray-800">
                  {{ lesson.title }}
                </div>
                <div class="text-sm text-gray-600 mt-1">
                  With: {{ lesson.teacher }}
                </div>
                <div class="mt-2 text-gray-700">
                  📅 {{ lesson.date }} &nbsp;&nbsp; 🕒 {{ lesson.time }}
                </div>
              </div>
            </div>

            <!-- Right: Cancel Button -->
            <div>
              <button
                @click="cancelBooking(lesson)"
                class="bg-red-600 text-white px-5 py-2 text-base rounded hover:bg-red-700 transition"
              >
                Cancel
              </button>
            </div>
          </li>
        </transition-group>
      </div>

      <div v-else class="text-gray-500">
        You haven't booked any lessons yet.
      </div>
    </section>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
  props: {
    auth: Object,
    lessons: Array,
  },
  components: {
    MainLayout,
  },
  data() {
    return {
      cancelingLessonIds: [],
    };
  },
  methods: {
    cancelBooking(lesson) {
      const confirmed = confirm(
        `Are you sure you want to cancel your booking for "${lesson.title}" on ${lesson.date} at ${lesson.time}?`
      );

      if (!confirmed) return;

      this.cancelingLessonIds.push(lesson.id + lesson.date + lesson.time);

      this.$inertia.post(
        route('lessons.cancel'),
        {
          lesson_id: lesson.id,
          date: lesson.date,
          time: lesson.time,
        },
        {
          preserveScroll: true,
          onSuccess: () => {
            setTimeout(() => {
              this.lessons = this.lessons.filter(
                l => l.id + l.date + l.time !== lesson.id + lesson.date + lesson.time
              );
              this.cancelingLessonIds = this.cancelingLessonIds.filter(
                id => id !== lesson.id + lesson.date + lesson.time
              );
            }, 300);
          },
        }
      );
    },
  },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-leave-to {
  opacity: 0;
}
</style>

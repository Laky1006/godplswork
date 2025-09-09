<template>
  <MainLayout :user="auth.user">
    <section class="max-w-4xl mx-auto py-10 px-6">
      <h1 class="text-2xl font-bold mb-6">Notifications</h1>

      <div v-if="notifications.length" class="space-y-4">
        <div
          v-for="note in notifications"
          :key="note.id"
          class="p-4 border rounded bg-white shadow-sm"
        >
          <div v-if="note.type === 'lesson_booked'">
            📌 <strong>{{ note.student_name }}</strong> booked your lesson
            "<em>{{ note.lesson_title }}</em>" on {{ note.date }} at {{ note.time }}.
          </div>

          <div v-else-if="note.type === 'review_left'">
            💬 <strong>{{ note.student_name }}</strong> left a review on
            "<em>{{ note.lesson_title }}</em>".
          </div>

          <div v-else-if="note.type === 'upcoming_reminder'">
            ⏰ Reminder: Your lesson "<em>{{ note.lesson_title }}</em>" is on {{ note.date }} at {{ note.time }}.
          </div>

          <div class="text-sm text-gray-500 mt-1">
            {{ note.created_at }}
          </div>
        </div>
      </div>

      <div v-else class="text-gray-500 italic">No notifications yet.</div>
    </section>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
  props: {
    auth: Object,
    notifications: Array,
  },
  components: {
    MainLayout,
  },
};
</script>

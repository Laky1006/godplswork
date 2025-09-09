<template>
  <MainLayout :user="auth.user">
<section class="max-w-5xl mx-auto py-10 px-6">
  <!-- White card container -->
  <div class="bg-white shadow sm:rounded-lg overflow-hidden">

    <!-- Banner -->
    <img
      :src="lesson.banner ? `/storage/${lesson.banner}` : '/images/default-banner.jpg'"
      alt="Lesson Banner"
      class="w-full h-64 object-cover"
    />

    <div class="p-6 space-y-6">
    <!-- Title + Rating -->
    <div>
      <h1 class="text-3xl font-bold">{{ lesson.title }}</h1>
      <div class="flex items-center gap-2">
        <div class="flex text-2xl">
          <span
            v-for="n in 5"
            :key="n"
            :class="lesson.rating >= n ? 'text-yellow-500' : 'text-gray-300'"
          >
            ★
          </span>
        </div>
        <span class="text-gray-600 text-lg">
          {{ lesson.rating ? `${lesson.rating}/5` : 'Not Rated' }}
        </span>
      </div>
    </div>

    <!-- Description -->
    <div>
      <h2 class="text-xl font-semibold mb-2">Description</h2>
      <p class="text-gray-700">{{ lesson.description }}</p>
    </div>

    <!-- Contact Info & Price (side by side) -->
    <div class="flex flex-col sm:flex-row gap-6">
      <div class="flex-1">
        <h2 class="text-xl font-semibold mb-2">&#128222; Contact Info</h2>
        <p class="text-gray-700">{{ lesson.phone ?? 'N/A' }}</p>
      </div>
      <div class="flex-1">
        <h2 class="text-xl font-semibold mb-2">&#128181; Price</h2>
        <p class="text-gray-700">{{ lesson.price ?? 'Free' }}</p>
      </div>
    </div>

    <!-- Date Picker -->
    <div>
      <h2 class="text-lg font-semibold mb-2">&#128197; Pick a Lesson Date</h2>
      <Datepicker
        v-model="selectedDate"
        :inline="true"
        :highlight="highlightedDates"
      />
    </div>

    <!-- Available Time Slots -->
    <div v-if="selectedSlots.length" class="mt-4">
      <h3 class="font-semibold mb-2">Times on {{ formattedSelectedDate }}</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div
          v-for="(time, idx) in selectedSlots"
          :key="idx"
          class="bg-gray-100 px-4 py-2 rounded flex justify-between items-center space-x-4"
        >
          <span>&#128338; {{ time }}</span>
          <button
            v-if="auth.user && auth.user.role === 'student'"
            @click="submitSignup(time)"
            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm"
          >
            Sign Up
          </button>
          <div v-else class="text-base text-black-700 font-bold">
            Log in as student to apply
          </div>
        </div>
      </div>
    </div>
    <div v-else class="text-sm text-gray-500">No available times on this date.</div>
    </div>
  </div>

  <!-- Reviews -->
  <div class="bg-white mt-8 p-6 shadow sm:rounded-lg space-y-4">
    <h2 class="text-xl font-semibold mb-4">
      Student Reviews <span class="text-gray-500">({{ reviews.length }})</span>
    </h2>

    <div v-if="auth.user?.role === 'student'" class="mb-6">
      <div class="flex items-center gap-1 mb-2">
        <span v-for="n in 5" :key="n" class="text-2xl cursor-pointer"
          :class="n <= form.rating ? 'text-yellow-500' : 'text-gray-300'"
          @click="form.rating = n"
        >★</span>
      </div>
      <textarea v-model="form.comment" rows="3"
        class="w-full border rounded px-3 py-2"
        placeholder="Write a comment (optional)"
      ></textarea>
      <div v-if="$page.props.errors?.[0]?.includes('already reviewed')" class="text-red-500 text-sm mt-2">
        You have already left a review here.
      </div>
      <button @click="submitReview"
        class="mt-2 bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 text-sm"
      >
        Post Review
      </button>
    </div>

    <div v-else class="text-gray-500 italic mb-6">
      You must be signed in as a student to leave a review.
    </div>

    <div v-if="reviews.length">
      <div
        v-for="(review, index) in sortedReviews"
        :key="index"
        class="border rounded p-4 bg-white shadow-sm"
      >
        <div class="flex items-center gap-3 mb-1">
          <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-sm font-bold">
            {{ review.student.name.charAt(0) }}
          </div>
          <div>
            <div class="font-medium text-l">{{ review.student.name }}</div>
            <div class="text-xs text-gray-500">{{ review.created_at }}</div>
          </div>
        </div>
        <div class="mb-1">
          <span v-for="n in 5" :key="n" :class="n <= review.rating ? 'text-yellow-500' : 'text-gray-300'" class="text-xl">★</span>
          <span class="ml-2 text-sm text-gray-700">({{ review.rating }}/5)</span>
        </div>
        <p v-if="review.comment" class="text-gray-700 text-sm">{{ review.comment }}</p>
        <div v-if="auth.user?.student?.id === review.student_id" class="text-right">
          <button @click="deleteReview(review.id)" class="text-sm text-red-500 hover:text-red-700">Delete</button>
        </div>
      </div>
    </div>
    <div v-else class="text-sm text-gray-500">No reviews yet.</div>
  </div>
</section>

  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
  props: {
    auth: Object,
    lesson: Object,
    reviews: Array,
  },
  components: {
    MainLayout,
    Datepicker,
  },
  setup(props) {
    const selectedDate = ref(null);

    const form = ref({
      rating: 0,
      comment: '',
    });

    const submitting = ref(false);

    const submitReview = () => {
      if (!form.value.rating && !form.value.comment.trim()) return;

      submitting.value = true;

      router.post(
        route('reviews.store'),
        {
          lesson_id: props.lesson.id,
          rating: form.value.rating,
          comment: form.value.comment,
        },
        {
          preserveScroll: true,
          onFinish: () => {
            form.value.comment = '';
            form.value.rating = 0;
            submitting.value = false;
          },
        }
      );
    };

    return {
      selectedDate,
      form,
      submitReview,
    };
  },
  computed: {
    slotMap() {
      const map = {};
      const slots = this.lesson.slots?.filter(s => s.is_available);

      if (Array.isArray(slots)) {
        slots.forEach(slot => {
          if (!map[slot.date]) map[slot.date] = [];
          map[slot.date].push(slot.time);
        });
      }

      return map;
    },
    highlightedDates() {
      return Object.keys(this.slotMap)
        .filter(dateStr => /^\d{4}-\d{2}-\d{2}$/.test(dateStr))
        .map(dateStr => {
          const [year, month, day] = dateStr.split('-').map(Number);
          return new Date(year, month - 1, day);
        });
    },
    selectedSlots() {
      if (!this.selectedDate) return [];

      const date = new Date(this.selectedDate);
      const dateKey = `${date.getFullYear()}-${(date.getMonth() + 1)
        .toString()
        .padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;

      return this.slotMap[dateKey] || [];
    },

    sortedReviews() {
    if (!this.reviews || !Array.isArray(this.reviews)) return [];

    if (this.auth?.user?.role === 'student' && this.auth.user.student?.id) {
      const studentId = this.auth.user.student.id;

      const ownReview = this.reviews.find(r => r.student_id === studentId);
      const others = this.reviews.filter(r => r.student_id !== studentId);

      return ownReview ? [ownReview, ...others] : others;
    }

    return this.reviews;
  },
  },
  methods: {
    submitSignup(time) {
      const date = new Date(this.selectedDate);
      const dateKey = `${date.getFullYear()}-${(date.getMonth() + 1)
        .toString()
        .padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;

      const confirmed = confirm(`Do you want to book this slot on ${dateKey} at ${time}?`);
      if (!confirmed) return;

      this.$inertia.post(
        route('lessons.book', this.lesson.id),
        {
          date: dateKey,
          time: time,
        },
        {
          preserveScroll: true,
        }
      );
    },

    deleteReview(reviewId) {
      if (confirm('Are you sure you want to delete this review?')) {
        this.$inertia.delete(route('reviews.destroy', reviewId), {
          preserveScroll: true,
        });
      }
    },
  },
};
</script>


<style >
.dp__theme_light {
  --dp-highlight-color: rgba(16, 0, 158, 0.329);
}
</style>
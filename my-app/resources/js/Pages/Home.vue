<template>
    <MainLayout :user="auth.user">

    <section class="py-10 px-6 max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold mb-6">Available Lessons</h2>
        
        <!-- Controls Row -->
        <div ref="filterContainer" class="flex flex-wrap items-center gap-4 mb-4 relative">
          <!-- Search Input -->
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search lessons..."
            class="px-4 py-2 w-full sm:w-1/3 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm"
          />

          <!-- Sort Dropdown Button -->
          <div class="relative" ref="sortDropdown" data-sort-container>
            <button
              @click="toggleSortDropdown"
              class="flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-300 shadow-sm hover:bg-blue-200 transition"
              data-sort-toggle
            >
              &#128218; Sort by: {{ sortOptions[sortOption] }}
            </button>

            <div
              v-if="showSortDropdown"
              class="absolute top-full mt-2 w-48 bg-white border border-gray-200 shadow-lg rounded-lg z-20"
            >
              <button
                v-for="(label, key) in sortOptions"
                :key="key"
                @click="selectSortOption(key)"
                class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
              >
                {{ label }}
              </button>
            </div>
          </div>



          <!-- Filter Toggle Button -->
          <button
            class="flex items-center gap-2 px-4 py-2 rounded-full bg-white text-black hover:bg-blue-200 border border-gray-300 shadow-sm transition"
            @click="toggleFilterDropdown"
            data-filter-toggle
          >
            <span>&#128317;</span> Filter
          </button>

          <!-- Filter Dropdown -->
          <div
            v-if="showFilterDropdown"
            data-filter-container
            class="absolute top-full mt-2 bg-white border border-gray-200 shadow-lg rounded-lg z-10 w-72 p-3 left-0"
          >
            <div class="flex flex-wrap gap-2 ">
              <button
                v-for="label in availableLabels"
                :key="label"
                @click="toggleLabelFilter(label)"
                :class="[
                  'px-3 py-1 rounded-full text-sm',
                  selectedLabels.includes(label)
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
                ]"
              >
                {{ label }}
              </button>
            </div>
          </div>
        </div>

        <!-- Show selected filters as bubbles -->
        <div v-if="selectedLabels.length" class="flex flex-wrap gap-2 mb-6">
          <span
            v-for="label in selectedLabels"
            :key="label"
            class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center"
          >
            {{ label }}
            <button @click="removeLabelFilter(label)" class="ml-2 text-red-600 hover:text-red-800">×</button>
          </span>
        </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <Link
                  v-for="lesson in filteredLessons"
                  :key="lesson.id"
                  :href="route('lessons.show', lesson.id)"
                  class="block overflow-hidden rounded-lg shadow transition duration-300 transform hover:scale-[1.02] hover:shadow-lg cursor-pointer bg-white"
                >
                  <!-- Banner: full width, no padding or background -->
                  <img
                    :src="lesson.banner ? `/storage/${lesson.banner}` : '/images/default-banner.jpg'"
                    alt="Lesson Banner"
                    class="w-full h-40 object-cover"
                  />

                  <!-- Card content on white -->
                  <div class="p-4 ">
                    <h3 class="text-xl font-semibold mb-2">{{ lesson.title }}</h3>
                    <p class="text-sm text-gray-600 mb-1">Teacher: {{ lesson.teacher?.user?.name || 'Unknown' }}</p>

                    <div class="flex items-center text-sm">
                      <div class="flex">
                        <span
                          v-for="n in 5"
                          :key="n"
                          class="text-sm"
                          :class="{
                            'text-yellow-500': lesson.rating >= n,
                            'text-gray-300': lesson.rating < n
                          }"
                        >★</span>
                      </div>
                      <span class="ml-2 text-gray-600">
                        {{ lesson.rating ? `${lesson.rating}/5` : 'Not rated' }}
                      </span>

                      
                    </div>

                    <p class="text-sm text-gray-700 mt-2">Price: € {{ lesson.price ?? 'Free' }}</p>


                    <div v-if="lesson.labels?.length" class="flex flex-wrap gap-2 mt-2">
                      <span
                        v-for="(label, i) in lesson.labels"
                        :key="i"
                        class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs transition-opacity duration-300 hover:opacity-80"
                      >
                        {{ label }}
                      </span>
                    </div>

                  </div>
                </Link>
            </div>
        

    </section>
    </MainLayout>
  </template>
  
<script>
import MainLayout from '@/Layouts/MainLayout.vue'
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    MainLayout,
    Link,
  },
  props: {
    lessons: Array,
    auth: Object,
  },
  setup(props) {
    const searchQuery = ref('')
    const showFilterDropdown = ref(false)
    const selectedLabels = ref([])
    const showSortDropdown = ref(false)
    const sortOption = ref('az')

    const availableLabels = [
      'Math',
      'English',
      'Latvian',
      'Science',
      'Drawing',
      'Leisure activity',
      'Cooking',
      'Coding',
      'Music',
      'Creative',
      'Fun',
    ]

    const sortOptions = {
      az: 'A-Z',
      za: 'Z-A',
      ratingHigh: 'Highest Rating',
      ratingLow: 'Lowest Rating'
    }

    const toggleFilterDropdown = () => {
      showFilterDropdown.value = !showFilterDropdown.value
    }

    const toggleSortDropdown = () => {
      showSortDropdown.value = !showSortDropdown.value
    }

    const selectSortOption = (key) => {
      sortOption.value = key
      showSortDropdown.value = false
    }

    const toggleLabelFilter = (label) => {
      if (selectedLabels.value.includes(label)) {
        selectedLabels.value = selectedLabels.value.filter(l => l !== label)
      } else {
        selectedLabels.value.push(label)
      }
    }

    const removeLabelFilter = (label) => {
      selectedLabels.value = selectedLabels.value.filter(l => l !== label)
    }

    const handleClickOutside = (event) => {
      const filterBox = document.querySelector('[data-filter-container]')
      const sortBox = document.querySelector('[data-sort-container]')
      const isFilter = filterBox?.contains(event.target)
      const isSort = sortBox?.contains(event.target)

      if (!isFilter && !event.target.closest('[data-filter-toggle]')) {
        showFilterDropdown.value = false
      }

      if (!isSort && !event.target.closest('[data-sort-toggle]')) {
        showSortDropdown.value = false
      }
    }

    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
    })

    onBeforeUnmount(() => {
      document.removeEventListener('click', handleClickOutside)
    })

    const filteredLessons = computed(() => {
      return props.lessons
        .filter(lesson =>
          lesson.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          lesson.teacher?.user?.name?.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
        .filter(lesson =>
          selectedLabels.value.length === 0 ||
          selectedLabels.value.every(label =>
            (lesson.labels || []).includes(label)
          )
        )
        .sort((a, b) => {
          if (sortOption.value === 'az') return a.title.localeCompare(b.title)
          if (sortOption.value === 'za') return b.title.localeCompare(a.title)
          if (sortOption.value === 'ratingHigh') return (b.rating || 0) - (a.rating || 0)
          if (sortOption.value === 'ratingLow') return (a.rating || 0) - (b.rating || 0)
          return 0
        })
    })

    return {
      searchQuery,
      sortOption,
      sortOptions,
      showSortDropdown,
      toggleSortDropdown,
      selectSortOption,
      showFilterDropdown,
      selectedLabels,
      availableLabels,
      toggleFilterDropdown,
      toggleLabelFilter,
      removeLabelFilter,
      filteredLessons,
    }
  },
}
</script>




<style scoped>
.lesson-card {
  background: white;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
  
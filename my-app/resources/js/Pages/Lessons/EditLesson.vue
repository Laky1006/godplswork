<template>
  <MainLayout :user="auth.user">
<section class="py-12">
  <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">

    <!-- Edit Lesson Form Card -->
    <div class="bg-white p-6 shadow sm:rounded-lg sm:p-8">
      <h1 class="text-2xl font-bold mb-6">Edit Lesson</h1>

      <form @submit.prevent="submitForm" class="space-y-6">

        <!-- Title -->
        <div>
          <InputLabel for="title" value="Lesson Title" />
          <TextInput v-model="form.title" id="title" class="w-full" />
          <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
        </div>

        <!-- Description -->
        <div>
          <InputLabel for="description" value="Description" />
          <textarea v-model="form.description" id="description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
          <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
        </div>

        <!-- Phone -->
        <div>
          <InputLabel for="phone" value="Contact Phone Number" />
          <TextInput v-model="form.phone" id="phone" class="w-full" />
          <p v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</p>
        </div>

        <!-- Price -->
        <div>
          <InputLabel for="price" value="Price (optional)" />
          <TextInput
            v-model="form.price"
            id="price"
            class="w-full"
            type="number"
            step="0.01"
            min="0"
          />
          <p v-if="form.errors.price" class="text-red-500 text-sm mt-1">{{ form.errors.price }}</p>
        </div>


        <!-- Banner -->
        <div>
          <InputLabel for="banner" value="Banner Image (optional)" />
          <input type="file" @change="handleBannerChange" class="w-full mt-1" accept="image/*" />
          <p v-if="form.errors.banner" class="text-red-500 text-sm mt-1">{{ form.errors.banner }}</p>

          <div v-if="form.banner === null && lesson.banner" class="mt-2">
            <div class="relative w-1/2 aspect-video rounded border overflow-hidden">
              <img :src="`/storage/${lesson.banner}`" alt="Current banner" class="absolute inset-0 w-full h-full object-cover" />
            </div>
          </div>
        </div>

        <!-- Labels -->
        <div>
          <InputLabel value="Labels (max 10)" />
          <div class="flex flex-wrap gap-2 mb-2">
            <button
              v-for="suggestion in labelSuggestions"
              :key="suggestion"
              type="button"
              class="px-3 py-1 bg-gray-200 rounded-full hover:bg-gray-300 text-sm"
              @click="addLabel(suggestion)"
            >
              {{ suggestion }}
            </button>
          </div>
          <input
            v-model="newLabel"
            @keyup.enter.prevent="addLabel(newLabel)"
            type="text"
            class="border px-3 py-2 rounded w-full"
            placeholder="Type a new label and press Enter or Add"
            :disabled="form.labels.length >= 10"
          />
          <button
            type="button"
            class="mt-2 bg-blue-600 text-white px-4 py-1 rounded text-sm"
            @click="addLabel(newLabel)"
            :disabled="form.labels.length >= 10"
          >
            Add
          </button>
          <div class="flex flex-wrap gap-2 mt-3">
            <span
              v-for="(label, index) in form.labels"
              :key="index"
              class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center"
            >
              {{ label }}
              <button type="button" class="ml-2 text-red-500 hover:text-red-700" @click="removeLabel(index)">x</button>
            </span>
          </div>
          <p v-if="form.labels.length >= 10" class="text-red-500 text-sm mt-1">Maximum 10 labels allowed.</p>
        </div>

        <!-- Time Slots -->
        <div>
          <InputLabel value="Available Lesson Slots" />
          <div v-for="(slot, index) in form.available_slots" :key="index" class="flex gap-4 items-center mt-2">
            <input type="date" v-model="slot.date" class="border px-2 py-1 rounded w-1/3" />
            <input type="time" v-model="slot.time" class="border px-2 py-1 rounded w-1/3" />
            <button type="button" @click="removeSlot(index)" class="text-red-500 text-sm hover:underline">Remove</button>
          </div>
          <button type="button" @click="addSlot" class="mt-2 text-blue-500 hover:underline text-sm">+ Add Slot</button>
        </div>

        <!-- Submit -->
        <div class="pt-4">
          <PrimaryButton type="submit">Save Changes</PrimaryButton>
        </div>
      </form>
    </div>

    <!-- Delete Section -->
    <div class="bg-white p-6 shadow sm:rounded-lg sm:p-8">
      <h2 class="text-xl font-semibold text-red-600 mb-4">Danger Zone</h2>
      <p class="text-sm text-gray-700 mb-4">This will permanently delete the lesson and all associated bookings.</p>
      <button @click="deleteLesson" class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700">Delete Lesson</button>
    </div>

  </div>
</section>

  </MainLayout>
</template>

<script>
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue' 
import MainLayout from '@/Layouts/MainLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

export default {
  props: {
    auth: Object,
    lesson: Object,
  },
  components: {
    MainLayout,
    InputLabel,
    TextInput,
    PrimaryButton,
  },
  setup(props) {
    const newLabel = ref('')
    const labelSuggestions = [
      'Math', 'English', 'Latvian', 'Biology', 'Language', 'Coding', 'Leisure',
    ]

    const form = useForm({
      title: props.lesson.title,
      description: props.lesson.description,
      phone: props.lesson.phone,
      banner: null,
      available_slots: [...props.lesson.slots],
      labels: [...(props.lesson.labels || [])],
      price: props.lesson.price || '',
    })

    function handleBannerChange(event) {
      form.banner = event.target.files[0]
    }

    function submitForm() {
      form.transform(data => ({
        ...data,
        _method: 'PUT',
      })).post(route('lessons.update', props.lesson.id), {
        forceFormData: true,
        onSuccess: () => {
          router.visit(route('my-lessons'))
        },
      })
    }

    function addSlot() {
      form.available_slots.push({ date: '', time: '' })
    }

    function removeSlot(index) {
      form.available_slots.splice(index, 1)
    }

    function deleteLesson() {
      if (confirm("Are you sure you want to delete this lesson?")) {
        router.delete(route('lessons.destroy', props.lesson.id), {
          onSuccess: () => router.visit(route('my-lessons')),
        })
      }
    }

    // Labels
    function addLabel(label) {
      const trimmed = label.trim()
      if (
        trimmed &&
        !form.labels.includes(trimmed) &&
        form.labels.length < 10
      ) {
        form.labels.push(trimmed)
      }
      newLabel.value = ''
    }

    function removeLabel(index) {
      form.labels.splice(index, 1)
    }

    return {
      form,
      submitForm,
      handleBannerChange,
      addSlot,
      removeSlot,
      deleteLesson,
      newLabel,
      labelSuggestions,
      addLabel,
      removeLabel,
    }
  },
}

</script>

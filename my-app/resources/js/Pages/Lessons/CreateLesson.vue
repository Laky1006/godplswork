<template>
  <MainLayout :user="auth.user">
    <section class="py-12">
      <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">

        <!-- Lesson Creation Card -->
        <div class="bg-white p-6 shadow sm:rounded-lg sm:p-8">
          <h1 class="text-2xl font-bold mb-6">Create a New Lesson</h1>

          <form @submit.prevent="submitForm" class="space-y-6">
            <!-- Title -->
            <div>
              <InputLabel for="title" value="Lesson Title" />
              <TextInput v-model="form.title" id="title" class="w-full" placeholder="e.g. Algebra Basics" />
              <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
            </div>

            <!-- Description -->
            <div>
              <InputLabel for="description" value="Description" />
              <textarea
                v-model="form.description"
                id="description"
                rows="4"
                class="w-full border rounded px-3 py-2"
                placeholder="Write a short description..."
              ></textarea>
              <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
            </div>

            <!-- Banner Image -->
            <div>
              <InputLabel for="banner" value="Lesson Banner Image" />
              <input type="file" @change="handleBannerChange" class="w-full mt-1" accept="image/*" />

              <!-- Preview -->
              <div v-if="bannerPreview" class="mt-4">
                <p class="text-sm text-gray-500 mb-1">Image Preview:</p>
                <img
                  :src="bannerPreview"
                  alt="Banner Preview"
                  class="rounded border w-1/2 max-h-64 object-cover"
                />
              </div>
            </div>

            <!-- Contact Info -->
            <div>
              <InputLabel for="phone" value="Contact Phone Number" />
              <TextInput v-model="form.phone" id="phone" class="w-full" placeholder="+1234567890" />
              <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.phone  }}</p>
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
                placeholder="e.g. 29.99"
              />
              <p v-if="form.errors.price" class="text-red-500 text-sm mt-1">{{ form.errors.price }}</p>
            </div>


            <!-- Labels -->
            <div>
              <InputLabel value="Labels (max 10)" />

              <!-- Suggestions -->
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

              <!-- Input field for new label -->
              <input
                v-model="newLabel"
                @keyup.enter.prevent="addLabel(newLabel)"
                type="text"
                class="border px-3 py-2 rounded w-full"
                placeholder="Type a new label and press Enter or Add"
                :disabled="form.labels.length >= 10"
              />

              <!-- Add button (optional) -->
              <button
                type="button"
                class="mt-2 bg-blue-600 text-white px-4 py-1 rounded text-sm"
                @click="addLabel(newLabel)"
                :disabled="form.labels.length >= 10"
              >
                Add
              </button>

              <!-- Label bubbles -->
              <div class="flex flex-wrap gap-2 mt-3">
                <span
                  v-for="(label, index) in form.labels"
                  :key="index"
                  class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center"
                >
                  {{ label }}
                  <button
                    type="button"
                    class="ml-2 text-red-500 hover:text-red-700"
                    @click="removeLabel(index)"
                  >
                    x
                  </button>
                </span>
              </div>

              <p v-if="form.labels.length >= 10" class="text-red-500 text-sm mt-1">Maximum 10 labels allowed.</p>
            </div>

            <!-- Available Slots -->
            <div>
              <InputLabel value="Available Lesson Dates & Times" />
              <div v-for="(slot, index) in form.available_slots" :key="index" class="flex gap-4 items-center mt-2">
                <input
                  type="date"
                  v-model="slot.date"
                  class="border px-2 py-1 rounded w-1/3"
                />
                <input
                  type="time"
                  v-model="slot.time"
                  class="border px-2 py-1 rounded w-1/3"
                />
                <button
                  type="button"
                  @click="removeSlot(index)"
                  class="text-red-500 text-sm hover:underline"
                >
                  Remove
                </button>
              </div>
              <button
                type="button"
                @click="addSlot"
                class="mt-2 text-blue-500 hover:underline text-sm"
              >
                + Add another slot
              </button>
            </div>

            <div v-if="Object.keys(form.errors).length" class="bg-red-100 border border-red-400 text-red-700 p-2 rounded mb-6">
              Please fix the errors above before submitting.
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
              <PrimaryButton type="submit">Create Lesson</PrimaryButton>
            </div>
          </form>
        </div>

      </div>
    </section>
  </MainLayout>
</template>


<script>
import { useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

export default {
  props: {
    auth: Object,
  },
  components: {
    MainLayout,
    InputLabel,
    TextInput,
    PrimaryButton,
  },
  data() {
    return {
      newLabel: '',
      bannerPreview: null,
      labelSuggestions: [
        'Math', 'English', 'Latvian', 'Biology', 'Language', 'Coding', 'Leisure',
      ],
      form: useForm({
        title: '',
        description: '',
        phone: '',
        banner: null,
        available_slots: [{ date: '', time: '' }],
        labels: [],
        price: '',
      }),
    }
  },
  methods: {
    addSlot() {
      this.form.available_slots.push({ date: '', time: '' })
    },
    removeSlot(index) {
      this.form.available_slots.splice(index, 1)
    },
    handleBannerChange(event) {
      this.form.banner = event.target.files[0]
    },
    addLabel(label) {
      const trimmed = label.trim()
      if (
        trimmed &&
        !this.form.labels.includes(trimmed) &&
        this.form.labels.length < 10
      ) {
        this.form.labels.push(trimmed)
      }
      this.newLabel = ''
    },
    removeLabel(index) {
      this.form.labels.splice(index, 1)
    },
    submitForm() {
      this.form.post(route('lessons.store'), {
        forceFormData: true,
        onSuccess: () => {
          this.form.reset()
          this.$inertia.visit('/my-lessons')
        },
      })
    },

    handleBannerChange(event) {
      const file = event.target.files[0]
      if (file) {
        this.form.banner = file
        this.bannerPreview = URL.createObjectURL(file)
      }
    },
  },
}
</script>


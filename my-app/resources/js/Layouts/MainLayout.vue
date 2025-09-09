<template>
    <div ref="layoutRoot" class="flex flex-col min-h-screen bg-[#f0f7fa]">

      <!-- -----HEADER----- -->
      <header class="text-white px-9 py-11 flex justify-between items-center shadow-[0_6px_8px_rgba(0,0,0,0.45)]"
        style="background-image: url('/images/nav-bg1.png'); background-size: cover; background-position: center;">
        <h1 class="font-righteous text-5xl ">Learnora</h1>
  
        <nav class="flex items-center gap-10 text-lg">
          <a href="/" class="hover:underline">Home</a>
          <a :href="route('about')" class="hover:underline">About</a>
  
          <!-- YES loggedin -->
          <div v-if="user" class="relative" ref="dropdown">
            <button @click="toggleMenu" class="flex items-center gap-2 focus:outline-none hover:underline">
              <span>{{ user.username }}</span>
              <img
                v-if="user.profile_photo"
                :src="`/storage/${user.profile_photo}`"
                alt="Profile Photo"
                class="w-11 h-11 rounded-full object-cover"
              />
              <img
                v-else
                src="/images/default-profile.jpg"
                alt="Default Profile Photo"
                class="w-11 h-11 rounded-full object-cover"
              />
            </button>

            <div
              v-if="showMenu"
              class="absolute right-0 mt-2 w-40 bg-white text-black shadow-md rounded-lg overflow-hidden z-50"
            >
              <Link :href="route('profile.edit')" class="block px-4 py-2 hover:bg-gray-100">Profile</Link>
              <Link :href="route('my-lessons')" class="block px-4 py-2 hover:bg-gray-100">My Lessons</Link>
              <Link :href="route('notifications.index')" class="block px-4 py-2 hover:bg-gray-100">Notifications</Link>

              <form @submit.prevent="logout" class="block">
                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Log Out</button>
              </form>
            </div>
          </div>
  

          <!-- NOT logedin -->
          <div v-else>
            <a href="/login" class="hover:underline">Log In</a>
          </div>

        </nav>

      </header>
  
      <!-- Page Content -->
      <main class="flex-1">
        <slot />
      </main>
  
      <!-- Footer -->
      <footer class="bg-gray-800 text-white text-center py-4 mt-8">
        &copy; 2025 WebPage. All rights reserved.
      </footer>
    </div>
</template>
  
<script>
  import { Link, router } from '@inertiajs/vue3'
  
  export default {
    props: {
      user: Object,
    },

    components: {
        Link,
    },

    data() {
      return {
        showMenu: false,
      }
    },

    methods: {
      toggleMenu() {
        this.showMenu = !this.showMenu
      },
      logout() {
        router.post('/logout')
      },
      handleClickOutside(event) {
        if (this.showMenu && this.$refs.dropdown && !this.$refs.dropdown.contains(event.target)) {
          this.showMenu = false
        }
      },
    },

    mounted() {
      document.addEventListener('click', this.handleClickOutside)
    },

    beforeUnmount() {
      document.removeEventListener('click', this.handleClickOutside)
    },
  }
</script>
  
<style scoped>
/* keeps footer down */
footer { 
  position: relative;
  bottom: 0;
  width: 100%;
  margin-top: auto;
}
</style>
<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    _method: 'PATCH',
    name: user.name,
    email: user.email,
    username: user.username,
    profile_photo: null,
    grade: user.student?.grade || '',
    education: user.teacher?.education || '',
    bio: user.teacher?.bio || '',
});

function handlePhotoChange(event) {
    form.profile_photo = event.target.files[0];
}

function submitForm() {
    form.post(route('profile.update'), {
        preserveScroll: true,
        forceFormData: true, // for file upload
        onSuccess: () => form.reset('profile_photo'),
    });
}

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <!-- <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6"></form> -->
        <form @submit.prevent="submitForm" class="mt-6 space-y-6">
            
            <div class="col-span-6 sm:col-span-4">

                <InputLabel for="profile_photo" value="Profile Photo" />

                <img
                :src="user.profile_photo ? `/storage/${user.profile_photo}` : '/images/default-profile.jpg'"
                alt="Profile Photo"
                class="w-24 h-24 rounded-full object-cover"
                />

                <input
                    id="profile_photo"
                    type="file"
                    class="mt-1 block w-full"
                    accept="image/*"
                    @change="handlePhotoChange"
                />

                <InputError :message="form.errors.profile_photo" class="mt-2" />
            </div>

            <div>
                <InputLabel for="username" value="Username" />

                <TextInput
                    id="username"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.username"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <!-- For students -->
            <div v-if="user.role === 'student'">
            <InputLabel for="grade" value="Grade" />
            <TextInput
                id="grade"
                type="text"
                v-model="form.grade"
                class="mt-1 block w-full"
            />
            <InputError class="mt-2" :message="form.errors.grade" />
            </div>

            <!-- For teachers -->
            <div v-if="user.role === 'teacher'">
            <InputLabel for="education" value="Education" />
            <TextInput
                id="education"
                type="text"
                v-model="form.education"
                class="mt-1 block w-full"
            />
            <InputError class="mt-2" :message="form.errors.education" />

            <InputLabel for="bio" value="Bio" class="mt-4" />
            <textarea
                id="bio"
                v-model="form.bio"
                class="mt-1 block w-full border rounded px-3 py-2"
                rows="4"
            ></textarea>
            <InputError class="mt-2" :message="form.errors.bio" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

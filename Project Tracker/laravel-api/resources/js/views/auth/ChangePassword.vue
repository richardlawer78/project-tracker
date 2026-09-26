<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  password: '',
  password_confirmation: ''
})

const submit = () => {
  form.post('/change-password')
}
</script>

<template>
  <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f5f7fb; padding: 20px;">
    <div style="background: #fff; border-radius: 12px; padding: 40px; max-width: 400px; width: 100%; box-shadow: 0 2px 9px #16356b08;">
      <h1 style="font-size: 22px; margin: 0 0 8px;">Set Your New Password</h1>
      <p style="color: #8490a5; margin: 0 0 24px;">You're using a temporary password. Please choose a new one to continue.</p>

      <form @submit.prevent="submit">
        <div style="margin-bottom: 16px;">
          <label style="font-weight: 600; display: block; margin-bottom: 6px;">New Password</label>
          <input
            v-model="form.password"
            type="password"
            required
            minlength="8"
            style="width: 100%; padding: 10px; border: 1px solid #dce3ee; border-radius: 7px;"
          />
          <div v-if="form.errors.password" style="color: #cc3e49; font-size: 13px; margin-top: 4px;">
            {{ form.errors.password }}
          </div>
        </div>

        <div style="margin-bottom: 24px;">
          <label style="font-weight: 600; display: block; margin-bottom: 6px;">Confirm Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            required
            minlength="8"
            style="width: 100%; padding: 10px; border: 1px solid #dce3ee; border-radius: 7px;"
          />
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          style="width: 100%; padding: 12px; background: #3268cd; color: #fff; border: 0; border-radius: 8px; font-weight: 600; cursor: pointer;"
        >
          {{ form.processing ? 'Saving...' : 'Set Password' }}
        </button>
      </form>
    </div>
  </div>
</template>
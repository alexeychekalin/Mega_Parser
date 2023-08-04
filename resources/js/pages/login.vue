<script setup>
import logo from '@images/logo.svg?raw'
const isPasswordVisible = ref(false)
</script>

<script>
import { mapActions } from 'vuex'
import store from '@/store/index'
export default {
  data() {
    return {
      auth: {
        TNumber: '',
        password: '',
      },
      visible: false
    }
  },
  methods: {
    ...mapActions({
      signIn: 'auth/login',
    }),
    async login() {
      await this.$axios.get('/sanctum/csrf-cookie')
      await this.$axios.post('/login', this.auth).then(() => {
        this.signIn().then(() => {
          this.$router.replace('dashboard')
        })
      }).catch(() => {
        this.visible = true;
      }).finally(() => {
      })
    }
  }
}
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <VCard
      class="auth-card pa-4 pt-7"
      max-width="448"
    >
      <VCardItem class="justify-center">
        <template #prepend>
          <div class="d-flex">
            <div v-html="logo" />
          </div>
        </template>

        <VCardTitle class="font-weight-semibold text-2xl text-uppercase">
          Mega Parser
        </VCardTitle>
      </VCardItem>

      <VCardText class="pt-2">
        <h5 class="text-h5 font-weight-semibold mb-1">
          Привет! 👋🏻
        </h5>
        <p class="mb-0">
          Для доступа залогинься по номеру телефона
        </p>
      </VCardText>
      <v-alert v-show="visible" text="Неверный логин/пароль" type="error"></v-alert>
      <VCardText>
        <VForm @submit.prevent="login">
          <VRow>
            <!-- email -->
            <VCol cols="12">
              <VTextField
                v-model="auth.TNumber"
                label="Телефон"
                required
              />
            </VCol>

            <!-- password -->
            <VCol cols="12">
              <VTextField
                v-model="auth.password"
                label="Пароль"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
                required
              />

              <!-- remember me checkbox
              <div class="d-flex align-center justify-space-between flex-wrap mt-1 mb-4">
                <VCheckbox
                  v-model="form.remember"
                  label="Remember me"
                />

                <a
                  class="ms-2 mb-1"
                  href="javascript:void(0)"
                >
                  Forgot Password?
                </a>
              </div>
              -->
              <!-- login button -->
              <VBtn
                class="mt-10 mb-4"
                block
                type="submit"
              >
                Login
              </VBtn>
            </VCol>

          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style lang="scss">
@use "@core-scss/pages/page-auth.scss";
</style>

<script setup>
import misc404 from '@images/pages/404.png'
import miscMaskDark from '@images/pages/misc-mask-dark.png'
import miscMaskLight from '@images/pages/misc-mask-light.png'
import { useTheme } from 'vuetify'

const vuetifyTheme = useTheme()


const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? miscMaskLight : miscMaskDark
})
</script>
<script>
import store from "@/store";
export default {

  methods: {
    async logout() {
      await this.$axios.post('/logout').then(() => {
        store.dispatch('auth/logout')
        this.$router.replace('login')
      })
    },
  }
}
</script>
<template>
  <div class="misc-wrapper">
    <ErrorHeader
      error-code="BLOCKED"
      error-title="Ваш аккаунт заблокирован ⚠️"
      error-description="Обратитесь к администраторам для восстановления доступа"
    />

    <!-- 👉 Image -->
    <div class="misc-avatar w-100 text-center">
      <VImg
        :src="misc404"
        alt="Coming Soon"
        :max-width="800"
        class="mx-auto"
      />
      <VBtn
        @click="logout"

        class="mt-10"
      >
        Назад
      </VBtn>
    </div>

  </div>
</template>

<style lang="scss">
@use "@core-scss/pages/misc.scss";

.misc-footer-tree {
  inline-size: 15.625rem;
  inset-block-end: 3.5rem;
  inset-inline-start: 0.375rem;
}
</style>

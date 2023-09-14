<script setup>
import triangleDark from '@images/misc/triangle-dark.png'
import triangleLight from '@images/misc/triangle-light.png'
import trophy from '@images/misc/trophy.png'
import { useTheme } from 'vuetify'
import { useToast } from "vue-toastification";

const { global } = useTheme()
const triangleBg = computed(() => global.name.value === 'light' ? triangleLight : triangleDark)
</script>

<template>
  <VCard
    title="Мониторинг в 9:00 и 19:00"
    subtitle="автозапуск настроен"
    class="position-relative"
  >
    <VCardText>
      <h5 class="text-2xl font-weight-medium text-primary">
        777 товаров в 22 категорях
      </h5>
      <p>отслеживается </p>
      <VBtn
        size="small"
        @click = "this.$axios.get('/api/shell/run').then(() => { useToast().info('Запрос на запуск парсинга отправлен')})"
      >
        Запустить сейчас
      </VBtn>
    </VCardText>

    <!-- Triangle Background -->
    <VImg
      :src="triangleBg"
      class="triangle-bg flip-in-rtl"
    />

  </VCard>
</template>

<style lang="scss">
@use "@layouts/styles/mixins" as layoutsMixins;

.v-card .triangle-bg {
  position: absolute;
  inline-size: 10.375rem;
  inset-block-end: 0;
  inset-inline-end: 0;
}

.v-card .trophy {
  position: absolute;
  inline-size: 4.9375rem;
  inset-block-end: 2rem;
  inset-inline-end: 2rem;
}
</style>

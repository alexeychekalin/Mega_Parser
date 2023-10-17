<script setup>
import triangleDark from '@images/misc/triangle-dark.png'
import triangleLight from '@images/misc/triangle-light.png'
import trophy from '@images/misc/trophy.png'
import { useTheme } from 'vuetify'
import { useToast } from "vue-toastification";

const { global } = useTheme()
const triangleBg = computed(() => global.name.value === 'light' ? triangleLight : triangleDark)
</script>

<script>
import store from "@/store";
import {useToast} from "vue-toastification";
export default {
  data: () => ({
    countAll : 0
  }),

  methods: {
    run(){
      if(this.countAll === 0){
        useToast().error('Ни по одному товару не выставлено отслеживание', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        return;
      }
      useToast().info('Парсинг запущен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
      this.$axios.get('/api/shell/run').then((res) => { res.data.answer === 1 ? useToast().success('Парсинг товаров уже запущен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16}) : useToast().error('Произошла ошибка парсинга. Смотрите логи', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})})
    },

    Statistics() {
      this.$axios.get('/api/product/countMonitor').then((res) => {
        this.countAll = res.data[0].count
      })
    },
  },

  mounted () {
  this.Statistics()
  },
}

</script>

<template>
  <VCard
    title="Мониторинг в 9:00 и 18:00"
    subtitle="автозапуск настроен"
    class="position-relative"
  >
    <VCardText>
      <h5 class="text-2xl font-weight-medium text-primary">
        {{countAll}} товаров
      </h5>
      <p>отслеживается </p>
      <VBtn
        size="small"
        @click = "run()"
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

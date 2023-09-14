
<script>
import robot from '@images/avatars/robot.png'
import robot2 from '@images/avatars/robot2.png'
import broken from '@images/avatars/brokenrobot.png'
import { useToast } from "vue-toastification";
import web from '@images/avatars/www.png'
import axios from "axios";
export default {
  data: () => ({
    solidCardData : [
      {
        title: 'Парсинг',
        icon: 'mdi-spider-web',
        state: {
          color: '#56CA00',
          text: 'Работает',
          image: robot
        }
      },
      {
        title: 'Прием сообщений ',
        icon: 'mdi-telegram',
        state: {
          color: '#56CA00',
          text: 'Работает',
          image: robot
        }
      },
      {
        title: 'Классификация',
        icon: 'mdi-sitemap',
        state: {
          color: '#56CA00',
          text: 'Работает',
          image: robot
        }
      },
      {
        title: 'Административная панель',
        icon: 'mdi-cog-sync',
        state: {
          color: '#56CA00',
          text: 'Работает',
          image: web
        }
      },
      ] ,
    colors : ['#56CA00', '#FFB400', '#FF4C51'],
    state : [
      {
        color: '#56CA00',
        text: 'Работает',
        image: robot
      },
      {
        color: '#FFB400',
        text: 'Ожидание ответа',
        image: robot2
      },
      {
        color: '#FF4C51',
        text: 'Подсистема недоступна',
        image: broken
      }
    ]
  }),
  computed: {

  },

  methods: {
    checkState(){
      this.$axios.get('/api/check').then((res) => {
        this.stats = res.data
      })
    }
    /*
    checkState(key){
      useToast().info('Сервис проверки работает в режиме эмуляции ответов!')
      //TODO: На карточке админпанель в рабочем состоянии должен быть глобус
      this.solidCardData[key].state = this.state[Math.floor(Math.random() * this.state.length)]
    }
     */
  },
}
</script>

<template>
  <VRow>
    <VCol
      v-for="(data, key) in solidCardData"
      :key="data.icon"
      cols="12"
      md="6"
      lg="3"
    >
      <VCard :color="data.state.color">
        <VCardItem>
          <template #prepend>
            <VIcon
              size="1.9rem"
              color="white"
              :icon="data.icon"
            />
          </template>
          <VCardTitle class="text-white">
            {{ data.title }}
          </VCardTitle>
        </VCardItem>

        <VCardText class="d-flex justify-space-between align-center flex-wrap">
          <div class="text-no-wrap">
            <VAvatar
              size="34"
              :image="data.state.image"
            />
            <span class="text-white ms-2">{{ data.state.text }}</span>
          </div>

          <div class="d-flex align-center">
            <v-tooltip text="Запрос состояния">
              <template v-slot:activator="{ props }">
                <IconBtn
                  icon="mdi-update"
                  color="white"
                  class="me-1"
                  v-bind="props"
                  @click="checkState(key)"
                />
              </template>
            </v-tooltip>

          </div>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

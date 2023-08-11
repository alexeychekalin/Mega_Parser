<script setup>
const statistics = [
  {
    title: 'Sales',
    stats: '245k',
    icon: 'mdi-trending-up',
    color: 'primary',
  },
  {
    title: 'Customers',
    stats: '12.5k',
    icon: 'mdi-account-outline',
    color: 'success',
  },
  {
    title: 'Product',
    stats: '1.54k',
    icon: 'mdi-cellphone-link',
    color: 'warning',
  },
  {
    title: 'Revenue',
    stats: '$88k',
    icon: 'mdi-currency-usd',
    color: 'info',
  },
]
</script>

<script>
import robot from '@images/avatars/robot.png'
import robot2 from '@images/avatars/robot2.png'
import broken from '@images/avatars/brokenrobot.png'
import { useToast } from "vue-toastification";
import web from '@images/avatars/www.png'
export default {
  data: () => ({
    solidCardData : [
      {
        title: 'Парсер',
        icon: 'mdi-spider-web',
        state: {
          color: 'success',
          text: 'Работает',
          image: robot
        }
      },
      {
        title: 'Сообщения',
        icon: 'mdi-telegram',
        state: {
          color: 'success',
          text: 'Работает',
          image: robot
        }
      },
      {
        title: 'Классификатор',
        icon: 'mdi-sitemap',
        state: {
          color: 'success',
          text: 'Работает',
          image: robot
        }
      },
      {
        title: 'Админ панель',
        icon: 'mdi-cog-sync',
        state: {
          color: 'success',
          text: 'Работает',
          image: web
        }
      },
    ] ,
    //colors : ['#56CA00', '#FFB400', '#FF4C51'],
    colors : ['success', 'warning', 'error'],
    state : [
      {
        color: 'success',
        text: 'Работает',
        image: robot
      },
      {
        color: 'warning',
        text: 'Ожидание ответа',
        image: robot2
      },
      {
        color: 'error',
        text: 'Подсистема недоступна',
        image: broken
      }
    ]
  }),
  computed: {

  },

  methods: {
    checkState(key){
      useToast().info('Сервис проверки работает в режиме эмуляции ответов!')
      //TODO: На карточке админпанель в рабочем состоянии должен быть глобус
      this.solidCardData.forEach(e => e.state = this.state[Math.floor(Math.random() * this.state.length)])
      //this.solidCardData[0].state = this.state[Math.floor(Math.random() * this.state.length)]
    }
  },
}
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Мониторинг состояния подсистем</VCardTitle>

      <template #append>
        <div class="me-n3">
          <v-tooltip text="Запрос состояния">
            <template v-slot:activator="{ props }">
              <IconBtn
                icon="mdi-update"
                class="me-1"
                v-bind="props"
                @click="checkState()"
              />
            </template>
          </v-tooltip>
        </div>
      </template>
    </VCardItem>

    <VCardText>
      <h6 class="text-sm font-weight-medium mb-12">
        <span>Все системы работают нормально</span>
      </h6>

      <VRow>
        <VCol
          v-for="item in solidCardData"
          :key="item.title"
          cols="6"
          sm="3"
        >
          <div class="d-flex align-center">
            <div class="me-3">
              <VAvatar
                :color="item.state.color"
                rounded
                size="62"
                class="elevation-1"
              >
                <VIcon
                  size="44"
                  :icon="item.icon"
                />
              </VAvatar>
            </div>

            <div class="d-flex flex-column">
              <span class="text-caption">
                {{ item.state.text }}
              </span>
              <span class="text-h6">{{ item.title }}</span>
            </div>
          </div>
        </VCol>
      </VRow>
    </VCardText>
  </VCard>
</template>

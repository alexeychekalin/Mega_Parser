<script setup>
import robot from '@images/avatars/robot.png'
import robot2 from '@images/avatars/robot2.png'
import broken from '@images/avatars/brokenrobot.png'
import web from '@images/avatars/www.png'

const solidCardData = [
  {
    cardBg: '#FFB400',
    title: 'Парсинг',
    icon: 'mdi-spider-web',
    avatarImg: robot2,
    avatarName: 'Ожидание ответа',
  },
  {
    cardBg: '#56CA00',
    title: 'Прием сообщений ',
    icon: 'mdi-telegram',
    avatarImg: robot,
    avatarName: 'Работает',
  },
  {
    cardBg: '#FF4C51',
    title: 'Классификация',
    icon: 'mdi-sitemap',
    avatarImg: broken,
    avatarName: 'Подсистема недоступна',
  },
  {
    cardBg: '#56CA00',
    title: 'Административная панель',
    icon: 'mdi-cog-sync',
    avatarImg: web,
    avatarName: 'Работает',
  },
]

const colors = ['#56CA00', '#FF4C51', '#FFB400']

</script>

<template>
  <VRow>
    <VCol
      v-for="data in solidCardData"
      :key="data.icon"
      cols="12"
      md="6"
      lg="3"
    >
      <VCard :color="data.cardBg">
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
              :image="data.avatarImg"
            />
            <span class="text-white ms-2">{{ data.avatarName }}</span>
          </div>

          <div class="d-flex align-center">
            <v-tooltip text="Запрос состояния">
              <template v-slot:activator="{ props }">
                <IconBtn
                  icon="mdi-update"
                  color="white"
                  class="me-1"
                  v-bind="props"
                />
              </template>
            </v-tooltip>

          </div>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

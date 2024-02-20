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
          color: 'warning',
          text: 'Ожидание',
          image: robot
        }
      },
      {
        title: 'Сообщения',
        icon: 'mdi-telegram',
        state: {
          color: 'warning',
          text: 'Ожидание',
          image: robot
        }
      },
      {
        title: 'Классификатор',
        icon: 'mdi-sitemap',
        state: {
          color: 'warning',
          text: 'Ожидание',
          image: robot
        }
      },
      {
        title: 'Web',
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
        color: 'error',
        text: 'Недоступна',
        image: broken
      },
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
    ],
    status: 'Запросите состояние подсистем',
    answer: '',
    dialog: false,
  }),
  created() {
   // this.checkState();
  },

  methods: {
    triggerDialog(){
      this.dialog = this.answer !== ''
    },
    /*
    checkState(key){
      useToast().info('Сервис проверки работает в режиме эмуляции ответов!')
      this.solidCardData.forEach(e => e.state = this.state[Math.floor(Math.random() * this.state.length)])
      //this.solidCardData[0].state = this.state[Math.floor(Math.random() * this.state.length)]
    }
     */
    checkState(){
      this.$axios.get('/api/shell/status').then((res) => {
        useToast().info('Опрос систем завершен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        this.solidCardData[0].state = this.state[res.data.answer]
        this.solidCardData[1].state = this.state[res.data.answer]
        this.solidCardData[2].state = this.state[res.data.answer]
        this.status = res.data.answer === 0 ? 'Некоторые сервисы недоступны' : 'Все системы работают нормально'
      })
    }
  },
}
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Состояние подсистем</VCardTitle>

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
        <span>{{status}}</span>
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
                size="42"
                class="elevation-1"
              >
                <VIcon
                  size="30"
                  :icon="item.icon"
                  @click="triggerDialog()"
                />
              </VAvatar>
            </div>

            <div class="d-flex flex-column">
              <span class="text-caption">
                {{ item.state.text }}
              </span>
              <span class="text-overline">{{ item.title }}</span>
            </div>
          </div>
        </VCol>
      </VRow>
    </VCardText>
    <v-dialog v-model="dialog" persistent max-width="290">
      <v-card>
        <tr>{{ answer }}</tr>
      </v-card>
    </v-dialog>
  </VCard>
</template>

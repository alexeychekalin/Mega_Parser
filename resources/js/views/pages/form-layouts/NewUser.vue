<script>
import { useToast } from "vue-toastification";
import axios from "axios";
import store from "@/store";
export default {
  data: () => ({
    editedItem: {
      FIO: '',
      TNumber: '',
      Access: 0,
      AccessWeb: 0,
      password: '',
      ChatId: '',
    },
    selectsItem: [
      {access: 1, value: 'Доступен'},
      {access: 0, value: 'Запрещено'}
    ],
    rules: {
      required: value => !!value || 'Поле обязательно',
      //number: value => value.length <= 20 || 'Max 20 characters',
      number: value => {
        const pattern = /^\d*(\.\d{1,2})?$/
        return pattern.test(value) || 'Введите число, формат 12345.67'
      },
      phone: value => {
        const pattern = /^\d+$/
        return pattern.test(value) || 'В телефоне буквы?'
      },
      phoneFormat: value => {
        const pattern = /^\d+$/
        return pattern.test(value) || 'В телефоне буквы?'
      }

    }
  }),
  computed: {

  },

  methods: {
    clear(){
      this.Bonus = false
      this.Monitor = false
      this.CardCash = false
      console.log(this.CardCash)
    },
    newUser(){
      let name = this.editedItem.FIO
      axios.post('/api/users',
        {
          FIO: this.editedItem.FIO,
          TNumber: this.editedItem.TNumber,
          ChatID: null,
          WebAccess: this.editedItem.WebAccess,
          TelegramAccess: this.editedItem.TelegramAccess,
          password: this.editedItem.password
        } )
        .then(res => {
          useToast().success('Пользователь создан')
          this.$refs.newUser.reset()
        })
        .catch(function (error) {
          useToast().error('Ошибка создания пользователя')
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID, Message: 'Ошибка при СОЗДАНИИ пользователя: '+ name + '. Описание: ' + error, Place: 'Dashboard/NewUser.vue' })
        });
    }
  },
}
</script>

<template>
  <VForm
    @submit.prevent="newUser"
    ref="newUser"
  >
    <VRow>
      <v-col
        cols="12"
        md="6"
      >
        <v-text-field
          v-model="editedItem.FIO"
          label="ФИО"
          :rules="[rules.required]"
          prepend-inner-icon="mdi-account-tie"
        ></v-text-field>
      </v-col>
      <v-col
        cols="12"
        md="6"
      >
        <v-text-field
          v-model="editedItem.TNumber"
          label="Телефон"
          :rules="[rules.required, rules.phone]"
          prepend-inner-icon="mdi-cellphone"
        ></v-text-field>
      </v-col>
        <v-col
          cols="12"
          md="3"
        >
          <v-select
            v-model="editedItem.TelegramAccess"
            :items="selectsItem"
            item-title="value"
            item-value="access"
            label="Telegram"
            persistent-hint
            prepend-inner-icon="mdi-telegram"
          ></v-select>
        </v-col>
        <v-col
          cols="12"
          md="3"
        >
          <v-select
            v-model="editedItem.WebAccess"
            :items="selectsItem"
            item-title="value"
            item-value="access"
            label="ЛК"
            persistent-hint
            prepend-inner-icon="mdi-web"
          ></v-select>
        </v-col>
        <v-col
          cols="12"
          md="6"
        >
          <v-text-field
            v-model="editedItem.password"
            label="Пароль"
            :rules="[rules.required]"
            prepend-inner-icon="mdi-lock"
          ></v-text-field>
        </v-col>
      <VCol
        cols="12"
        class="d-flex gap-6"
        md="3"

      >
        <VBtn
          type="submit"
          variant="text"
        >
          Добавить
        </VBtn>

        <VBtn
          type="reset"
          color="secondary"
          variant="text"
          @click="clear"
        >
          Очистить
        </VBtn>
      </VCol>
    </VRow>
  </VForm>
</template>

<template>
  <VCard title="Все пользователи системы">
    <v-data-table
  :headers="headers"
  :items="users"
  :search="search"
  :sort-by="[{ key: 'FIO', order: 'asc' }]"
  class="elevation-1"
  hover="true"
>
  <template v-slot:top>
    <v-toolbar
      density="compact"
    >
      <v-toolbar-title>
        <v-text-field
          v-model="search"
          density="compact"
          variant="plain"
          append-icon="mdi-magnify"
          label="Поиск по всем полям"
          single-line
          hide-details
        ></v-text-field>
      </v-toolbar-title>
      <v-divider
        class="mx-4"
        inset
        vertical
      ></v-divider>
      <v-dialog
        v-model="dialog"
        max-width="500px"
      >
        <template v-slot:activator="{ props }">
          <v-btn
            color="primary"
            dark
            class="mb-2"
            v-bind="props"
          >
            Создать
          </v-btn>
        </template>
        <v-form @submit.prevent="save">
        <v-card style="max-height: 700px;">
          <v-card-title>
            <span class="text-h5">{{ formTitle }}</span>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col
                  cols="12"
                  sm="6"
                  md="6"
                >
                  <v-text-field
                    v-model="editedItem.FIO"
                    label="ФИО"
                    :rules="[rules.required]"
                  ></v-text-field>
                </v-col>
                <v-col
                  cols="12"
                  sm="6"
                  md="6"
                >
                  <v-text-field
                    v-model="editedItem.TNumber"
                    label="Телефон"
                    :rules="[rules.required, rules.phoneFormat]"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col
                  cols="12"
                  sm="6"
                  md="6"
                >
                  <v-select
                    v-model="editedItem.TelegramAccess"
                    :items="selectsItem"
                    item-title="value"
                    item-value="access"
                    label="Telegram"
                    persistent-hint
                  ></v-select>
                </v-col>
                <v-col
                  cols="12"
                  sm="6"
                  md="6"
                >
                  <v-select
                    v-model="editedItem.WebAccess"
                    :items="selectsItem"
                    item-title="value"
                    item-value="access"
                    label="ЛК"
                    persistent-hint
                  ></v-select>
                </v-col>
              </v-row>
              <v-row v-if="editedIndex === -1">
                <v-col
                  cols="12"
                  sm="6"
                  md="12"
                >
                  <v-text-field
                    v-model="addPassword"
                    label="Пароль"
                    :rules="[rules.required]"

                  ></v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="blue-darken-1"
              variant="text"
              @click="close"
            >
              Закрыть
            </v-btn>
            <v-btn
              :disabled="false"
              color="blue-darken-1"
              variant="text"
              type="submit"
            >
              Сохранить
            </v-btn>
          </v-card-actions>
        </v-card>
        </v-form>
      </v-dialog>
      <v-dialog v-model="dialogDelete" max-width="500px">
        <v-card>
          <v-card-title class="text-h5">Удалить?</v-card-title>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue-darken-1" variant="text" @click="closeDelete">Отмена</v-btn>
            <v-btn color="blue-darken-1" variant="text" @click="deleteItemConfirm">Удалить</v-btn>
            <v-spacer></v-spacer>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-toolbar>
  </template>

  <template v-slot:item.actions="{ item }">
        <div style="white-space: nowrap">
          <v-tooltip
            location="top"
          >
            <template v-slot:activator="{ props }">
              <v-btn
                icon
                v-bind="props"
                @click="editItem(item.raw)"
                color="primary"
                size="40px"
              >
                <v-icon color="grey-lighten-1">
                  mdi-pencil
                </v-icon>
              </v-btn>
            </template>
            <span>Редактировать</span>
          </v-tooltip>

          <v-tooltip
            location="top"
          >
            <template v-slot:activator="{ props }">
              <v-btn
                icon
                v-bind="props"
                color="error"
                class="ma-1"
                @click="deleteItem(item.raw)"
              >
                <v-icon color="grey-lighten-1">
                  mdi-delete
                </v-icon>
              </v-btn>
            </template>
            <span>Удалить</span>
          </v-tooltip>
        </div>
      </template>

  <template v-slot:item.TelegramAccess="{ item }">
    <VAvatar
      size="40"
      variant="tonal"
      :color="getColor(item.columns.TelegramAccess)"
      class="me-3"
    >
      {{ getAccess(item.columns.TelegramAccess) }}
    </VAvatar>
  </template>
  <template v-slot:item.WebAccess="{ item }">
    <VAvatar
      size="40"
      variant="tonal"
      :color="getColor(item.columns.WebAccess)"
      class="me-3"
    >
      {{ getAccess(item.columns.WebAccess) }}
    </VAvatar>
  </template>

  <template v-slot:no-data>
    <p class="text-subtitle-1 text-truncate">
      Ничего не найдено
    </p>
  </template>
</v-data-table>
  </VCard>
</template>


<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
</script>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";
import store from "@/store";
export default {
  data: () => ({
    dessertName: '',
    search: '',
    dialog: false,
    dialogDelete: false,
    addPassword:'',
    selectsItem: [
      {access: 1, value: 'Доступен'},
      {access: 0, value: 'Запрещено'}
    ],
    headers: [
      { title: 'ФИО', align: 'center', key: 'FIO'},
      { title: 'Телефон', key: 'TNumber', align: 'center' },
      { title: 'ChatId', key: 'ChatId', align: 'center' },
      { title: 'Доступ в TG', key: 'TelegramAccess', sortable: false, align: 'center' },
      { title: 'Доступ в ЛК', key: 'WebAccess', sortable: false, align: 'center' },
      { title: 'Действия', key: 'actions', sortable: false, align: 'center' },
    ],
    users: [],
    editedIndex: -1,
    editedItem: {
      FIO: '',
      TNumber: '',
      Access: 0,
      AccessWeb: 0,
      password: '',
      ChatId: '',
    },
    defaultItem: {
      FIO: '',
      TNumber: '',
      Access: 0,
      AccessWeb: 0,
      password:'',
      ChatId: ''
    },
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
        const pattern = /^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{10,11}$/
        return pattern.test(value) || 'Телефон, 10 или 11 цифр'
      }
    }
  }),

  components:{
    axios
  },

  computed: {
    formTitle () {
      return this.editedIndex === -1 ? 'Новый пользователь' : 'Редактирование пользователя'
    },
  },

  watch: {
    dialog (val) {
      val || this.close()
    },
    dialogDelete (val) {
      val || this.closeDelete()
    },
  },

  created () {
    this.getUsers()
  },

  methods: {
    getColor (value) {
      return value ? 'success' : 'error'
    },
    getAccess (value) {
      return value ? 'да' : 'нет'
    },

    getUsers (){
      axios.get('/api/users')
        .then(res => {
          this.users = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка пользователей')
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ пользователей. Описание: ' + error, Place: 'users.vue' })
        });
    },

    editItem (item) {
      this.editedIndex = this.users.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem (item) {
      this.editedIndex = this.users.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialogDelete = true
    },

    deleteItemConfirm () {
      let currentUser = this.users[this.editedIndex]
      axios.post('api/users/delete', {UserID : currentUser.UserID}).then(res => {
        useToast().success('Пользователь удален')
        this.users.splice(this.editedIndex, 1)
        this.closeDelete()
      })
        .catch(function (error) {
          useToast().error('Ошибка удаления пользователя')
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при УДАЛЕНИИ пользователя: '+ currentUser.FIO + '. Описание: ' + error, Place: 'users.vue' })
        });

    },

    close () {
      this.dialog = false
      this.addPassword = ''
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    closeDelete () {
      this.dialogDelete = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    save () {
      if (this.editedIndex > -1) {
        // check NEW phone number, if different set ChatID to NULL
        Object.assign(this.users[this.editedIndex], this.editedItem)
        let ChatID = this.users[this.editedIndex].TNumber === this.editItem.TNumber ? this.users[this.editedIndex].ChatID : null
        this.editedItem.ChatId = ChatID;
        let updatedUser = this.editedItem;
        let pattern = /(\+7|8|7|)[\s(]?(\d{3})[\s)]?(\d{3})[\s-]?(\d{2})[\s-]?(\d{2})/g;
        updatedUser.TNumber = updatedUser.TNumber.toString().replace(pattern, '8$2$3$4$5')
        axios.post('/api/users/update',
            {
                    UserID: updatedUser.UserID,
                    FIO: updatedUser.FIO,
                    TNumber: updatedUser.TNumber,
                    ChatID: ChatID,
                    WebAccess: updatedUser.WebAccess,
                    TelegramAccess: updatedUser.TelegramAccess
            }
        )
          .then(res => {
              useToast().success('Пользователь обновлен')
            this.addPassword = ''
            this.close()
          })
          .catch(function (error) {
            useToast().error('Ошибка обновления пользователя')
            axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ИЗМЕНЕНИИ пользователя: '+ updatedUser.FIO + '. Описание: ' + error, Place: 'users.vue' })
          });
      } else {
        let name = this.editedItem.FIO
        axios.post('/api/users',
          {
                  FIO: this.editedItem.FIO,
                  TNumber: this.editedItem.TNumber,
                  ChatID: null,
                  WebAccess: this.editedItem.WebAccess,
                  TelegramAccess: this.editedItem.TelegramAccess,
                  password: this.addPassword
          } )
          .then(res => {
            useToast().success('Пользователь создан')
            this.users.push(this.editedItem)
            this.addPassword = ''
            this.close()
          })
          .catch(function (error) {
            useToast().error('Ошибка создания пользователя')
            axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID, Message: 'Ошибка при СОЗДАНИИ пользователя: '+ name + '. Описание: ' + error, Place: 'users.vue' })
          });
      }

    },
  },
}
</script>

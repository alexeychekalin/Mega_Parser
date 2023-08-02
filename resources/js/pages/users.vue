<template>

  <v-data-table
    :headers="headers"
    :items="users"
    :search="search"
    :sort-by="[{ key: 'FIO', order: 'asc' }]"
    class="elevation-1"
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
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="editedItem.FIO"
                      label="ФИО"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="editedItem.TNumber"
                      label="Телефон"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="editedItem.Access"
                      label="Telegram"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="editedItem.AccessWeb"
                      label="AccessWeb"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="editedItem.ChatId"
                      label="ChatId"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="editedItem.password"
                      label="Пароль"
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
                Cancel
              </v-btn>
              <v-btn
                color="blue-darken-1"
                variant="text"
                @click="save"
              >
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue-darken-1" variant="text" @click="closeDelete">Cancel</v-btn>
              <v-btn color="blue-darken-1" variant="text" @click="deleteItemConfirm">OK</v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>

    <template v-slot:item.actions="{ item }">
      <v-icon
        size="small"
        class="me-2"
        @click="editItem(item.raw)"
      >
        mdi-pencil
      </v-icon>
      <v-icon
        size="small"
        @click="deleteItem(item.raw)"
      >
        mdi-delete
      </v-icon>
    </template>

    <template v-slot:item.Access="{ item }">
      <VAvatar
        size="40"
        variant="tonal"
        :color="getColor(item.columns.Access)"
        class="me-3"
      >
        {{ getAccess(item.columns.Access) }}
      </VAvatar>
    </template>
    <template v-slot:item.AccessWeb="{ item }">
      <VAvatar
        size="40"
        variant="tonal"
        :color="getColor(item.columns.AccessWeb)"
        class="me-3"
      >
        {{ getAccess(item.columns.AccessWeb) }}
      </VAvatar>
    </template>

    <template v-slot:no-data>
      <p class="text-subtitle-1 text-truncate">
        Ничего не найдено
      </p>
    </template>
  </v-data-table>
</template>


<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import axios from "axios";
</script>

<script>
import axios from "axios";

export default {
  data: () => ({
    search: '',
    dialog: false,
    dialogDelete: false,
    headers: [
      { title: 'ФИО', align: 'center', key: 'FIO'},
      { title: 'Телефон', key: 'TNumber', align: 'center' },
      { title: 'ChatId', key: 'ChatId', align: 'center' },
      { title: 'Доступ в TG', key: 'Access', sortable: false, align: 'center' },
      { title: 'Доступ в ЛК', key: 'AccessWeb', sortable: false, align: 'center' },
      { title: 'Действия', key: 'actions', sortable: false, align: 'center' },
    ],
    users: [],
    editedIndex: -1,
    editedItem: {
      FIO: '',
      TNumber: '',
      Access: 0,
      AccessWeb: 0,
      password:'',
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
      this.users.splice(this.editedIndex, 1)
      this.closeDelete()
    },

    close () {
      this.dialog = false
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
        Object.assign(this.users[this.editedIndex], this.editedItem)
      } else {
        this.users.push(this.editedItem)
      }
      this.close()
    },
  },
}
</script>

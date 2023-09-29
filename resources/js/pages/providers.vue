<template>
  <v-data-table
    :headers="headers"
    :items="providers"
    :search="search"
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
          <v-form @submit.prevent="save">
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                    cols="12"
                  >
                    <v-text-field
                      v-model="editedItem.providerName"
                      label="Название"
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

    <template v-slot:no-data>
      <p class="text-subtitle-1 text-truncate">
        Ничего не найдено
      </p>
    </template>
  </v-data-table>
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
    search: '',
    dialog: false,
    dialogDelete: false,
    headers: [
      { title: 'Название', align: 'center', key: 'providerName'},
      { title: 'Действия', key: 'actions', sortable: false, align: 'center' },
    ],
    providers: [],
    editedIndex: -1,
    editedItem: {
      providerName: '',
    },
    defaultItem: {
      providerName: '',
    },
    rules: {
      required: value => !!value || 'Поле обязательно',
    }
  }),

  components:{
    axios
  },

  computed: {
    formTitle () {
      return this.editedIndex === -1 ? 'Новый поставщик' : 'Редактирование поставщика'
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
    this.getProviders()
  },

  methods: {

    getProviders (){
      axios.get('/api/providers')
        .then(res => {
          this.providers = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка поставщиков')
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ поставщиков. Описание: ' + error, Place: 'providers.vue' })
        });
    },

    editItem (item) {
      this.editedIndex = this.providers.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem (item) {
      this.editedIndex = this.providers.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialogDelete = true
    },

    deleteItemConfirm () {
      let currentProvider = this.providers[this.editedIndex]
      axios.post('api/providers/delete', {providerID : currentProvider.providerID}).then(res => {
        useToast().success('Поставщик удален')
        this.providers.splice(this.editedIndex, 1)
        this.closeDelete()
      })
        .catch(function (error) {
          useToast().error('Ошибка удаления поставщика')
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при УДАЛЕНИИ поставщика: '+ currentProvider.providerName + '. Описание: ' + error, Place: 'providers.vue' })
        });

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
        Object.assign(this.providers[this.editedIndex], this.editedItem)
        let updatedProvider = this.editedItem;
        axios.post('/api/providers/update',
            {
                    providerID: updatedProvider.providerID,
                    providerName: updatedProvider.providerName,
            }
        )
          .then(res => {
              useToast().success('поставщик обновлен')
            this.close()
          })
          .catch(function (error) {
            useToast().error('Ошибка обновления поставщика')
            axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ИЗМЕНЕНИИ поставщика: '+ updatedProvider.providerName + '. Описание: ' + error, Place: 'providers.vue' })
          });
      } else {
        let name = this.editedItem.providerName
        axios.post('/api/providers',
          {
            providerName: this.editedItem.providerName,
          } )
          .then(res => {
            useToast().success('Поставщик добавлен')
            this.providers.push(this.editedItem)
            this.close()
          })
          .catch(function (error) {
            useToast().error('Ошибка добавления поставщика')
            axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID, Message: 'Ошибка при СОЗДАНИИ поставщика: '+ name + '. Описание: ' + error, Place: 'providers.vue' })
          });
      }
    },
  },
}
</script>

<template>
  <VCard title="Список цветов">
    <v-data-table
    :headers="headers"
    :items="colors"
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
            prepend-inner-icon="mdi-magnify"
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
              class="mr-4"
              variant="text"
              icon="mdi-palette"
              v-bind="props"
            >
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
                      v-model="editedItem.Color"
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
    search: '',
    dialog: false,
    dialogDelete: false,
    headers: [
      { title: 'Название', align: 'center', key: 'Color'},
      { title: 'Действия', key: 'actions', sortable: false, align: 'center' },
    ],
    colors: [],
    editedIndex: -1,
    editedItem: {
      Color: '',
    },
    defaultItem: {
      Color: '',
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
      return this.editedIndex === -1 ? 'Новый цвет' : 'Редактирование цвета'
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
    this.getColors()
  },

  methods: {

    getColors (){
      axios.get('/api/colors')
        .then(res => {
          this.colors = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка цветов', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ цветов. Описание: ' + error, Place: 'colors.vue' })
        });
    },

    editItem (item) {
      this.editedIndex = this.colors.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem (item) {
      this.editedIndex = this.colors.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialogDelete = true
    },

    deleteItemConfirm () {
      let currentColor = this.colors[this.editedIndex]
      axios.post('api/colors/delete', {idColor : currentColor.idColor}).then(res => {
        useToast().success('Цвет удален', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        this.colors.splice(this.editedIndex, 1)
        this.closeDelete()
      })
        .catch(function (error) {
          useToast().error('Ошибка удаления цвета', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при УДАЛЕНИИ цвета: '+ currentColor.Color + '. Описание: ' + error, Place: 'colors.vue' })
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
        Object.assign(this.colors[this.editedIndex], this.editedItem)
        let updatedColor = this.editedItem;

        axios.post('/api/colors/update',
            {
                    idColor: updatedColor.idColor,
                    Color: updatedColor.Color,
            }
        )
          .then(res => {
              useToast().success('цвет обновлен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
            this.close()
          })
          .catch(function (error) {
            useToast().error('Ошибка обновления цвета', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
            axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ИЗМЕНЕНИИ цвета: '+ updatedColor.Color + '. Описание: ' + error, Place: 'colors.vue' })
          });
      } else {
        let name = this.editedItem.Color
        axios.post('/api/colors',
          {
              Color: this.editedItem.Color,
          } )
          .then(res => {
            useToast().success('Цвет добавлен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
            this.colors.push(this.editedItem)
            this.close()
          })
          .catch(function (error) {
            useToast().error('Ошибка добавления цвета', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
            axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID, Message: 'Ошибка при СОЗДАНИИ цвета: '+ name + '. Описание: ' + error, Place: 'colors.vue' })
          });
      }
    },
  },
}
</script>

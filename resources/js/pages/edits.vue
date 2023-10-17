<template>
  <Spinner v-if="loading"/>
  <VCard title="Список измененных моделей">
    <v-data-table
    :headers="headers"
    :items="edits"
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
              icon="mdi-link-variant-plus"
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
                      v-model="editedItem.OldModel"
                      label="Старая модель"
                      :rules="[rules.required]"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                  >
                    <v-text-field
                      v-model="editedItem.NewModel"
                      label="Новая модель"
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
import Spinner from "@/layouts/spinner.vue";
</script>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";
import store from "@/store";
export default {
  data: () => ({
    loading: false,
    search: '',
    dialog: false,
    dialogDelete: false,
    headers: [
      { title: 'Старое название', align: 'center', key: 'OldModel'},
      { title: 'Новое название', align: 'center', key: 'NewModel'},
      { title: 'Действия', key: 'actions', sortable: false, align: 'center' },
    ],
    edits: [],
    editedIndex: -1,
    editedItem: {
      OldModel: '',
      NewModel: '',
    },
    defaultItem: {
      OldModel: '',
      NewModel: '',
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
      return this.editedIndex === -1 ? 'Новая связка' : 'Редактирование связки'
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
    this.getEdits()
  },

  methods: {

    getEdits (){
      this.loading = true
      axios.get('/api/edits')
        .then(res => {
          this.edits = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка соответствия моделей', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ списка соответствия моделей. Описание: ' + error, Place: 'edits.vue' })
        });
      this.loading = false
    },

    editItem (item) {
      this.editedIndex = this.edits.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem (item) {
      this.editedIndex = this.edits.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialogDelete = true
    },

    deleteItemConfirm () {
      this.loading = true
      let currentEdit = this.edits[this.editedIndex]
      axios.post('api/edits/delete', {idEdits : currentEdit.idEdits}).then(res => {
        useToast().success('Соответствие удалено', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        this.edits.splice(this.editedIndex, 1)
        this.closeDelete()
      })
        .catch(function (error) {
          useToast().error('Ошибка удаления соответствия моделей', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при УДАЛЕНИИ соответствия моделей: '+ currentEdit.OldModel + '. Описание: ' + error, Place: 'edits.vue' })
        });
      this.loading = false
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
      this.loading = true
      if (this.editedIndex > -1) {
        let updatedEdits = this.editedItem;
        axios.post('/api/edits/update',
            {
                    idEdits: updatedEdits.idEdits,
                    OldModel: updatedEdits.OldModel,
                    NewModel: updatedEdits.NewModel,
            }
        )
          .then(res => {
              Object.assign(this.edits[this.editedIndex], this.editedItem)
              useToast().success('Соответствие обновлено', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
            this.close()
          })
          .catch(function (error) {
            useToast().error('Старая модель с таким названием уже существует', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
            axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ИЗМЕНЕНИИ соответствия моделей: '+ updatedEdits.OldModel + '. Описание: ' + error, Place: 'edits.vue' })
          });
      } else {
        let name = this.editedItem.OldModel
        axios.post('/api/edits',
          {
              OldModel: this.editedItem.OldModel,
              NewModel: this.editedItem.NewModel,
          } )
          .then(res => {
            useToast().success('Соответствие добавлено', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
            this.edits.push(this.editedItem)
            this.close()
          })
          .catch(function (error) {
            useToast().error('Ошибка добавления соответствия моделей', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
            axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID, Message: 'Ошибка при СОЗДАНИИ соответствия моделей: '+ name + '. Описание: ' + error, Place: 'edits.vue' })
          });
      }
      this.loading = false
    },
  },
}
</script>

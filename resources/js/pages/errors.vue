<template>
  <Spinner v-if="loading"/>
  <VCard title="Ошибки парсинга товаров">
    <v-data-table
  v-model:page="page"
  :search="search"
  :headers="headers"
  :items="filteredProducts"
  :items-per-page="itemsPerPage"
  row-class="my-row-class"
  hide-default-footer
  class="custom_table_class"
  v-model:sort-by="sortBy"
  hover="true"
>
  <template v-slot:bottom>
    <VRow>
      <VCol cols="12" md="3"></VCol>
      <VCol cols="12" md="6">
        <div class="text-center pt-2 mt-10">
          <v-pagination
            v-model="page"
            :length="pageCount"
          ></v-pagination>
        </div>
      </VCol>
    </VRow>
  </template>
<!--
  <template v-slot:thead>
    <tr class="grey lighten-3">
      <th v-for="header in headers" :key="header.title">
        <div v-if="filters.hasOwnProperty(header.key)">
          <v-col cols="12" >
            <v-autocomplete
              flat
              hide-details
              multiple
              attach
              chips
              dense
              clearable
              :items="columnValueList(header.key)"
              v-model="filters[header.key]"
              variant="underlined"
              prepend-inner-icon="mdi-magnify"
              class = "filter-search"
            >
              <template v-slot:chip="{ item, index }">
                <v-chip v-if="index < 2">
                     <span>
                      {{ item.value }}
                    </span>
                </v-chip>
                <span v-if="index === 2" class="grey--text caption" >
                    (+{{ filters[header.key].length - 2 }} )
                    </span>
              </template>
            </v-autocomplete>
          </v-col>
        </div>
      </th>
    </tr>
  </template>
-->
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
      <v-btn
        class="ma-2"
        variant="text"
        icon="mdi-microsoft-excel"
        @click="download"
      >
      </v-btn>

      <v-divider
        class="mx-4"
        inset
        vertical
      ></v-divider>
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
            class="ma-2"
            @click="deleteItem(item.raw)"
          >
            <v-icon color="grey-lighten-1">
              mdi-delete
            </v-icon>
          </v-btn>
        </template>
        <span>Удалить</span>
      </v-tooltip>

      <v-tooltip
        location="top"
      >
        <template v-slot:activator="{ props }">
          <v-btn
            icon
            v-bind="props"
            @click="searchSimilar(item.raw.Model, item.raw.ProductId)"
            color="warning"
          >
            <v-icon color="grey-lighten-1">
              mdi-delete-variant
            </v-icon>
          </v-btn>
        </template>
        <span>Мусор</span>
      </v-tooltip>
    </div>
  </template>

  <template v-slot:item.parseDate="{ item }">
      {{item.columns.parseDate !== null ? moment(item.columns.parseDate).format("DD.MM.YY") : ''}}
  </template>

  <template v-slot:no-data>
    <p class="text-subtitle-1 text-truncate">
      Ничего не найдено
    </p>
  </template>

</v-data-table>
  </VCard>
  <v-dialog
    v-model="typesDialog"
    scrollable
    width="auto"
  >
    <template v-slot:default="{ isActive }">
      <v-card>
        <v-alert
          color="warning"
          :title="'Найдены похожие на '+ similar +  ' товары:' "
          variant="outlined"
          border="start"
          border-color="warning"
        >
          <v-list :lines="newTypes.length" select-strategy="classic" style="max-height: 500px;" >
            <v-list-subheader>Выбранный тип: {{newType}}</v-list-subheader>
            <v-list-item v-for="(item, key, index) in newTypes" :value=key>
              <template v-slot:prepend="{ isActive=true }">
                <v-list-item-action start>
                  <v-checkbox :key="item['ProductId']" :label="item['Model']" :model-value="isActive" v-model="newTypes[key]['selected']" color="success"></v-checkbox>
                </v-list-item-action>
              </template>
            </v-list-item>
          </v-list>
          <v-card-actions class="justify-end">
            <v-btn
              variant="text"
              @click="toTrash"
            >Обновить</v-btn>
          </v-card-actions>
        </v-alert>

      </v-card>
    </template>
  </v-dialog>
</template>


<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import Spinner from "@/layouts/spinner.vue";
</script>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";
import store from "@/store";
import * as XLSX from 'xlsx/xlsx.mjs';
import moment from 'moment'
export default {
  data: () => ({
    loading: false,
    sortBy: [{ key: 'parseDate', order: 'desc' }],
    page:1,
    search:'',
    itemsPerPage: 15,
    dialog: false,
    dialogDelete: false,
    provider:[],
    types:[],
    selectsItem: [
      {access: 1, value: 'Доступен'},
      {access: 0, value: 'Запрещено'}
    ],
    filters: {
      Model:[],
      Bonus: [],
      // CardCash: [],
      typeName: [],
      providerName:[],
      //Retailer: []
    },
    headers: [
      //{ title: 'Рент', key: 'profit', align: 'center'  },
      { title: 'Название', align: 'center', key: 'Model'},
      { title: 'Цвет', align: 'center', key: 'Color'},
      { title: 'Тип', key: 'typeName', sortable: false, align: 'center' },
      { title: 'Поставщик', key: 'providerName', align: 'center' },
      //{ title: 'Ретейлер', key: 'Retailer', align: 'center'  },
      { title: 'Закупка', key: 'PurchasePrice', align: 'center' },
      //{ title: 'Продажа', key: 'SellPrice', sortable: false, align: 'center' },
      { title: 'Дата', key: 'parseDate', align: 'center' },
      //{ title: 'Дата СММ', key: 'SberParseDate', align: 'center' },
      //{ title: 'Бонусы', key: 'Bonus', align: 'center' },
      // { title: 'РСТ', key: 'Rostest', align: 'center' },
      // { title: 'Карта', key: 'CardCash', align: 'center' },
      //{ title: 'Монитор', key: 'Monitor', align: 'center' },
      { title: 'Действия', key: 'actions', sortable: false, align: 'center' },
    ],
    products: [],
    editedIndex: -1,
    editedItem: {
      Model:'',
      PurchasePrice:'',
      SellPrice: '',
      Bonus: false,
      CardCash: false,
      Monitor: false,
      Rostest: false,
      Type: '',
      ProductId: '',
      Color: '',
      Wholesaler: '',
      Retailer: '',
    },
    defaultItem: {
      Model:'',
      PurchasePrice:'',
      SellPrice: '',
      Bonus: false,
      CardCash: false,
      Monitor: false,
      Rostest: false,
      Type: '',
      ProductId: '',
      Color: '',
      Wholesaler: '',
      Retailer: '',
    },
    rules: {
      required: value => !!value || 'Поле обязательно',
      //number: value => value.length <= 20 || 'Max 20 characters',
      number: value => {
        const pattern = /^\d*(\.\d{1,2})?$/
        return pattern.test(value) || 'Введите число, формат 12345.67'
      },
    },
    newTypes:[],
    newType:'',
    similar:'',
    typesDialog: false
  }),

  components:{
    axios
  },

  computed: {
    filteredProducts() {
      return this.products.filter((d) => {
        return Object.keys(this.filters).every((f) => {
          return this.filters[f].length < 1 || this.filters[f].includes(d[f]);
        });
      });
    },
    pageCount () {
      return Math.ceil(this.products.length / this.itemsPerPage)
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
    this.getProducts()
    this.getTypes()
    this.getProviders()
  },

  methods: {
    searchSimilar(model, id){
      this.similar = model
      axios.post('api/product/getSimilarTypeTrash', {Model : model}).then(res => {
        if(res.data.length >= 2){
          this.typesDialog = true
          this.newTypes = res.data.map(item => {
            return {
              ...item,
              selected : true
            }
          })
        }
        else{
          axios.post('api/product/addTrash', {trashString : model, ProductID: id}).then(res => {
            this.getProducts()
            useToast().success('Строка помечена как мусор', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          })
            .catch(function (error) {
              useToast().error('Ошибка пометки строки', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
              axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка пометки строки в мусор: ' +model+ '. Описание: ' + error, Place: 'errors.vue/toTrash' })
            });
        }
      })
        .catch(function (error) {
          useToast().error('Ошибка поиска схожих строк', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка поиска схожих строк, MODEL ' + model + '. Описание: ' + error, Place: 'notype.vue/searchSimilar' })
        });
    },

    toTrash(){
      let toUpdate = this.newTypes.filter((x) => x.selected === true);
      axios.post('api/product/trashSimilar', {Models : toUpdate}).then(res => {
        this.newTypes = []
        this.similar = ''
        this.newType = ''
        this.typesDialog = false
        this.getProducts()
        useToast().success('В мусор отправлено - ' + toUpdate.length + ' строк', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
      })
        .catch(function (error) {
          useToast().error('Ошибка пометки строк', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка пометки строк в мусор. Описание: ' + error, Place: 'errors.vue/toTrash' })
        });
    },

    getTypes (){
      axios.get('/api/types')
        .then(res => {
          this.types = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка типов', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ типов. Описание: ' + error, Place: 'product.vue' })
        });
    },

    getProviders (){
      axios.get('/api/providers')
        .then(res => {
          this.provider = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка поставщиков', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ поставщиков. Описание: ' + error, Place: 'product.vue' })
        });
    },

    columnValueList: function (val) {
      return this.products.map((d) => d[val]).filter((value, index, self) => self.indexOf(value) === index);
    },

    download : function() {
      let output = this.products.filter((d) => {
        return Object.keys(this.filters).every((f) => {
          return this.filters[f].length < 1 || this.filters[f].includes(d[f]);
        });
      });
      output.forEach(e => delete e.value)
      const data = XLSX.utils.json_to_sheet(output)
      const wb = XLSX.utils.book_new()
      XLSX.utils.book_append_sheet(wb, data, 'data')
      XLSX.writeFile(wb,'report-errors.xlsx')
    },

    getColor (value) {
      return value === 1 || value === true ? 'success' : 'error'
    },

    getProducts (){
      this.loading = true
      axios.get('/api/product/geterrors')
        .then(res => {
          this.loading = false
          this.products = res.data.map(item => {
            return {
              ...item,
              PurchasePrice : item.PurchasePrice !== null ? Intl.NumberFormat('ru-RU').format(item.PurchasePrice.split(" ")[0].replace('₽', '').replace(',','')) : null,
            }
          });
        })
    },

    editItem (item) {
      this.editedIndex = this.products.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.editedItem.Monitor = this.editedItem.Monitor === 1
      this.editedItem.Bonus = this.editedItem.Bonus === 1
      this.editedItem.CardCash = this.editedItem.CardCash === 1
      this.editedItem.Rostest = this.editedItem.Rostest === 1
      this.editedItem.Type = ''

      this.dialog = true
    },

    deleteItem (item) {
      this.editedIndex = this.products.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialogDelete = true
    },

    deleteItemConfirm () {
      let currentProduct = this.products[this.editedIndex]
      axios.post('api/product/delete', {ProductID : currentProduct.ProductId}).then(res => {
        useToast().success('Товар удален', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        this.products.splice(this.editedIndex, 1)
        this.closeDelete()
      })
        .catch(function (error) {
          useToast().error('Ошибка удаления товара', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при УДАЛЕНИИ товара: '+ currentProduct.Model + '. Описание: ' + error, Place: 'product.vue' })
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
      if(this.editedItem.Type === '') {
        useToast().error('Не указан тип товара', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
        return;
      }
      if (this.editedItem.providerName !== null && this.editedItem.providerName !== "" && !isNaN(this.editedItem.providerName)) {
        let provider = this.provider.find(f => f.providerID === this.editedItem.providerName)
        this.editedItem.Wholesaler = provider.providerID
        this.editedItem.providerName = provider.providerName
      }
      let updatedProduct = this.editedItem;
      axios.post('/api/product/update',
          {
            Model: updatedProduct.Model,
            PurchasePrice: updatedProduct.PurchasePrice,
            SellPrice: updatedProduct.SellPrice,
            Bonus: updatedProduct.Bonus,
            CardCash: updatedProduct.CardCash,
            Monitor: updatedProduct.Monitor,
            Type: updatedProduct.Type,
            ProductId: updatedProduct.ProductId,
            Color: updatedProduct.Color,
            Rostest: updatedProduct.Rostest,
            Wholesaler: updatedProduct.Wholesaler,
            Retailer: updatedProduct.Retailer,
          }
      )
        .then(res => {
          useToast().success('Товар обновлен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          this.close()
          this.editedItem.Bonus = this.editedItem.Bonus === true ? 1 : 0
          this.editedItem.CardCash = this.editedItem.CardCash === true ? 1 : 0
          this.editedItem.Monitor = this.editedItem.Monitor === true ? 1 : 0
          this.editedItem.Rostest = this.editedItem.Rostest === true ? 1 : 0
          Object.assign(this.products[this.editedIndex], this.editedItem)

          this.products[this.editedIndex]['typeName'] = this.types.find(f => f.typeID === updatedProduct.Type).typeName

          if(updatedProduct.Color !== null && updatedProduct.Color !== "" ){
            axios.post('/api/colors/check',{Color: updatedProduct.Color,})
              .then(res => {
                if(!res.data)
                  useToast().success('Цвет добавлен в классификатор', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
              })
              .catch(function (error) {
                useToast().error('Ошибка добавления цвета', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
                axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ДОБАВЛЕНИИ цвета: '+ updatedProduct.Model + '. Описание: ' + error, Place: 'product.vue' })
              });
          }

          if(!([100, 101].includes(updatedProduct.Type))){
            this.products.splice(this.editedIndex, 1)
          }

          useToast().info('Запущен поиск похожих ошибок', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          this.$axios.get('/api/shell/update').then((res) => { res.data.answer === 1 ? useToast().success('Поиск завершен', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16}) : useToast().error('Произошла ошибка. Смотрите логи', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})})

        })
        .catch(function (error) {
          useToast().error('Ошибка обновления товара', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ИЗМЕНЕНИИ товара: '+ updatedProduct.Model + '. Описание: ' + error, Place: 'product.vue' })
        });
    },
  },
}
</script>

<style>
.filter-search{
  margin: 0 10px 5px 10px;
}
.custom_table_class thead tr th {
  font-size: 14px;
  text-transform: uppercase
}
</style>

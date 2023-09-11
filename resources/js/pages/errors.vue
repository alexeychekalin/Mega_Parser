<template>
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
      <VCol cols="12" md="1">
        <v-btn
          color="primary"
          dark
          variant="text"
          @click="download"
        >
          Скачать XLSX
        </v-btn>
      </VCol>
      <v-divider
        class="mx-4"
        inset
        vertical
      ></v-divider>
      <v-dialog
        v-model="dialog"
        max-width="800px"
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
                  sm="6"
                  md="6"
                >
                  <v-text-field
                    v-model="editedItem.Model"
                    label="Название"
                    :rules="[rules.required]"
                  ></v-text-field>
                </v-col>
                <v-col
                  cols="12"
                  sm="6"
                  md="6"
                >
                  <v-select
                    v-model="editedItem.Type"
                    label="Тип"
                    :items="types"
                    item-title="typeName"
                    item-value="typeID"
                    :rules="[rules.required]"
                  ></v-select>
                </v-col>
              </v-row>
              <v-row>
                <v-col
                  cols="12"
                  sm="6"
                  md="6"
                >
                  <v-select
                    v-model="editedItem.Wholesaler"
                    label="Продавец"
                    :items="provider"
                    item-title="providerName"
                    item-value="providerID"
                  ></v-select>
                </v-col>
                <v-col
                  cols="12"
                  sm="6"
                  md="6"
                >
                  <v-text-field
                    v-model="editedItem.Retailer"
                    label="Ретейлер"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row>
                <v-col
                  cols="12"
                  sm="4"
                  md="4"
                >
                  <v-text-field
                    v-model="editedItem.PurchasePrice"
                    label="Цена закупки"
                    :rules="[rules.required]"
                  ></v-text-field>
                </v-col>
                <v-col
                  cols="12"
                  sm="4"
                  md="4"
                >
                  <v-text-field
                    v-model="editedItem.SellPrice"
                    label="Цена продажи"
                  ></v-text-field>
                </v-col>
                <v-col
                  cols="12"
                  sm="4"
                  md="4"
                >
                  <v-text-field
                    v-model="editedItem.Color"
                    label="Цвет"
                  ></v-text-field>
                </v-col>
                <VCol cols="12" md="3" class='d-flex justify-center'>
                  <VCheckbox
                    color="success"
                    true-icon="mdi-alpha-r-circle-outline"
                    false-icon="mdi-alpha-s-circle-outline"
                    label="Ростест"
                    v-model="editedItem.Rostest"
                  ></VCheckbox>
                </VCol>
                <VCol cols="12" md="3" class='d-flex justify-center'>
                  <VCheckbox
                    color="success"
                    true-icon="mdi-credit-card-check-outline"
                    false-icon="mdi-credit-card-off-outline"
                    label="Оплата картой"
                    v-model="editedItem.CardCash"
                  ></VCheckbox>
                </VCol>
                <VCol cols="12" md="3" class='d-flex justify-center'>
                  <VCheckbox
                    color="success"
                    true-icon="mdi-gift-open-outline"
                    false-icon="mdi-gift-off-outline"
                    label="Бонусы Спасибо"
                    v-model="editedItem.Bonus"
                  ></VCheckbox>
                </VCol>
                <VCol cols="12" md="3" class='d-flex justify-center'>
                  <VCheckbox
                    color="success"
                    true-icon="mdi-shopping-search"
                    false-icon="mdi-shopping-search-outline"
                    label="Мониторинг"
                    v-model="editedItem.Monitor"
                  ></VCheckbox>
                </VCol>
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
</template>


<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
</script>

<script>
import axios from "axios";
import { useToast } from "vue-toastification";
import store from "@/store";
import * as XLSX from 'xlsx/xlsx.mjs';
import moment from 'moment'
export default {
  data: () => ({
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
      { title: 'Продавец', key: 'providerName', align: 'center' },
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
    }
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

    getTypes (){
      axios.get('/api/types')
        .then(res => {
          this.types = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка типов')
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ типов. Описание: ' + error, Place: 'product.vue' })
        });
    },

    getProviders (){
      axios.get('/api/providers')
        .then(res => {
          this.provider = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка поставщиков')
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
      axios.get('/api/product/geterrors')
        .then(res => {
          this.products = res.data
        })
    },

    editItem (item) {
      this.editedIndex = this.products.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.editedItem.Monitor = this.editedItem.Monitor === 1
      this.editedItem.Bonus = this.editedItem.Bonus === 1
      this.editedItem.CardCash = this.editedItem.CardCash === 1
      this.editedItem.Rostest = this.editedItem.Rostest === 1

      console.log(this.editedItem.Wholesaler)
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
        useToast().success('Товар удален')
        this.products.splice(this.editedIndex, 1)
        this.closeDelete()
      })
        .catch(function (error) {
          useToast().error('Ошибка удаления товара')
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
          useToast().success('Товар обновлен')
          this.close()
          this.editedItem.Bonus = this.editedItem.Bonus === true ? 1 : 0
          this.editedItem.CardCash = this.editedItem.CardCash === true ? 1 : 0
          this.editedItem.Monitor = this.editedItem.Monitor === true ? 1 : 0
          this.editedItem.Rostest = this.editedItem.Rostest === true ? 1 : 0
          Object.assign(this.products[this.editedIndex], this.editedItem)

          this.products[this.editedIndex]['typeName'] = this.types.find(f => f.typeID === updatedProduct.Type).typeName

          if(updatedProduct.Wholesaler !== null && this.editedItem.Wholesaler !== ""){
            this.products[this.editedIndex]['providerName'] = this.provider.find(f => f.providerID === updatedProduct.Wholesaler).providerName
          }
          if(this.editedItem.SellPrice !== "" && this.editedItem.SellPrice !== null && this.editedItem.PurchasePrice !== "" && this.editedItem.PurchasePrice !== null){
            this.products[this.editedIndex]['profit'] = ((1 - (this.products[this.editedIndex]['PurchasePrice'].split(" ")[0].replace('₽', '').replace(',','')/this.products[this.editedIndex]['SellPrice'].replace(',','').replace('₽', '') ))*100).toFixed(2)
          }

          if(updatedProduct.Color !== null && updatedProduct.Color !== "" ){
            axios.post('/api/colors/check',{Color: updatedProduct.Color,})
              .then(res => {
                if(!res.data)
                  useToast().success('Цвет добавлен в классификатор')
              })
              .catch(function (error) {
                useToast().error('Ошибка добавления цвета')
                axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ДОБАВЛЕНИИ цвета: '+ updatedProduct.Model + '. Описание: ' + error, Place: 'product.vue' })
              });
          }

          if(!([100, 101].includes(updatedProduct.Type))){
            this.products.splice(this.editedIndex, 1)
          }

        })
        .catch(function (error) {
          useToast().error('Ошибка обновления товара')
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

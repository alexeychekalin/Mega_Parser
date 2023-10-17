<template>
  <Spinner v-if="loading"/>
  <VCard title="Товары на мониторинге">
    <v-data-table
  v-model:page="page"
  :search="search"
  :headers="headers"
  :items="filteredProducts"
  :items-per-page="itemsPerPage"
  row-class="my-row-class"
  hide-default-footer
  class="custom_table_class"
  hover="true"
  v-model:sort-by="sortBy"
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
      <v-dialog
        v-model="dialog"
        scrollable
        width="auto"
      >
        <template v-slot:activator="{ props }">
          <v-btn
            class="ma-2"
            variant="text"
            icon="mdi-cart-plus"
            v-bind="props"
          >
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
                  <v-autocomplete
                    v-model="editedItem.Type"
                    label="Тип"
                    :items="types"
                    item-title="typeName"
                    item-value="typeID"
                    :rules="[rules.required]"
                  ></v-autocomplete>
                </v-col>
              </v-row>
              <v-row>
                <v-col
                  cols="12"
                  sm="6"
                  md="6"
                >
                  <v-select
                    v-model="editedItem.providerName"
                    label="Поставщик"
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

<!--
  <template v-slot:item.Bonus="{ item }">
    <VAvatar
      size="40"
      variant="tonal"
      style="cursor: pointer"
      :color="getColor(item.columns.Bonus)"
      class="me-3"
      @click="set(item.raw, 'Bonus', !item.columns.Bonus, 'бонусах')"
    >
      {{ item.columns.Bonus === 1 ? "Да" : "Нет" }}
    </VAvatar>
  </template>
-->
  <template v-slot:item.Rostest="{ item }">
    <VAvatar
      size="40"
      variant="tonal"
      :color="getColor(item.columns.Rostest)"
      class="me-3"
      @click="set(item.raw, 'Rostest', !item.columns.Rostest, 'Ростесте')"
      style="cursor: pointer"
    >
      {{ item.columns.Rostest === 1 ? "Да" : "Нет" }}
    </VAvatar>
  </template>

  <!--
  <template v-slot:item.CardCash="{ item }">
    <VAvatar
      size="40"
      variant="tonal"
      :color="getColor(item.columns.CardCash)"
      class="me-3"
      @click="set(item.raw, 'CardCash', !item.columns.CardCash, 'оплате картой')"
      style="cursor: pointer"
    >
      {{ item.columns.CardCash === 1 ? "Да" : "Нет" }}
    </VAvatar>
  </template>
  -->

  <template v-slot:item.Monitor="{ item }" >
    <VAvatar
      size="40"
      variant="tonal"
      :color="getColor(item.columns.Monitor)"
      class="me-3"
      @click="set(item.raw, 'Monitor', !item.columns.Monitor, 'мониторинге')"
      style="cursor: pointer"
    >
      {{ item.columns.Monitor === 1 || item.columns.Monitor === true ? "Да" : "Нет"}}
    </VAvatar>
  </template>

  <template v-slot:item.parseDate="{ item }">
      {{item.columns.parseDate !== null ? moment(item.columns.parseDate).format("DD.MM.YY") : ''}}
  </template>

  <template v-slot:item.SberParseDate="{ item }">
    {{item.columns.SberParseDate !== null ? moment(item.columns.SberParseDate).format("DD.MM.YY") : ''}}
  </template>

  <template v-slot:item.profit="{ item }">
    <div class="text-success font-weight-medium">
      {{item.columns.profit}}
    </div>
  </template>

  <template v-slot:item.Retailer="{ item }">
    <div class="text-truncate" style="max-width: 70px;">
      <v-tooltip :text=item.columns.Retailer>
        <template v-slot:activator="{ props }">
          <div v-bind="props">
            <a v-if="item.raw.LinkToSMM !== ''" :href="item.raw.LinkToSMM" target="_blank" >{{item.columns.Retailer}}</a>
            <p v-else >{{item.columns.Retailer}}</p>
          </div>
        </template>
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
import * as XLSX from 'xlsx/xlsx.mjs';

export default {
  data: () => ({
    loading: false,
    oldModel: '',
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
      { title: 'Рент', key: 'profit', align: 'center'  },
      { title: 'Название', align: 'center', key: 'Model'},
      { title: 'Цвет', align: 'center', key: 'Color'},
      { title: 'Тип', key: 'typeName', sortable: false, align: 'center' },
      { title: 'Поставщик', key: 'providerName', align: 'center' },
      { title: 'Ретейлер', key: 'Retailer', align: 'center'  },
      { title: 'Закупка', key: 'PurchasePrice', align: 'center' },
      { title: 'Продажа', key: 'SellPrice', sortable: false, align: 'center' },
      { title: 'Дата', key: 'parseDate', align: 'center' },
      { title: 'Дата СММ', key: 'SberParseDate', align: 'center' },
      //{ title: 'Бонусы', key: 'Bonus', align: 'center' },
      { title: 'РСТ', key: 'Rostest', align: 'center' },
     // { title: 'Карта', key: 'CardCash', align: 'center' },
      { title: 'СММ', key: 'Monitor', align: 'center' },
      { title: '', key: 'actions', sortable: false, align: 'right' },
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
          useToast().error('Ошибка получения списка типов', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ типов. Описание: ' + error, Place: 'monitor.vue' })
        });
    },

    getProviders (){
      axios.get('/api/providers')
        .then(res => {
          this.provider = res.data;
        })
        .catch(function (error) {
          useToast().error('Ошибка получения списка поставщиков', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ПОЛУЧЕНИИ поставщиков. Описание: ' + error, Place: 'monitor.vue' })
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
      XLSX.writeFile(wb,'report.xlsx')
    },

    set(item, what, set, toast){
      this.editedIndex = this.products.indexOf(item)
      this.products[this.editedIndex][what] = set ? 1 : 0
      axios.post('api/product/set', {what : what, set : set, ProductId : item.ProductId}).then(res => {
        useToast().success('Данные о ' + toast + ' обновлены', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
      })
        .catch(function (error) {
          useToast().error('Ошибка обновления данных о ' + toast, {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при ОБНОВЛЕНИИ данных о: '+ toast + '. Описание: ' + error, Place: 'monitor.vue' })
        });
    },

    getColor (value) {
      return value === 1 || value === true ? 'success' : 'error'
    },

    formatNumber(i){
      return Intl.NumberFormat('ru-RU').format(i.replace(/[\u0000-\u001F\u007F-\u009F\u00A0]/g, "").replace('₽', '').replace(',','').replace(' ',''))
    },

    formatPrice(i){
      return i.replace(/[\u0000-\u001F\u007F-\u009F\u00A0]/g, "").replace('₽', '').replace(',','').replace(' ','')
    },

    getProducts (){
      this.loading = true
      axios.get('/api/product')
        .then(res => {
          this.loading = false
          this.products = res.data.map(item => {
            return {
              ...item,
              SellPrice : item.SellPrice !== null ? this.formatNumber(item.SellPrice) : null,
              PurchasePrice : item.PurchasePrice !== null ? this.formatNumber(item.PurchasePrice) : null,
              profit: item.SellPrice !== null && item.PurchasePrice !== null ? ((1 - (this.formatPrice(item.PurchasePrice)/this.formatPrice(item.SellPrice)))*100).toFixed(2) : '-',
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
      this.oldModel = item.Model
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
          axios.post('/api/log', {Time: Date.now(), User: store.state.auth.user.UserID , Message: 'Ошибка при УДАЛЕНИИ товара: '+ currentProduct.Model + '. Описание: ' + error, Place: 'monitor.vue' })
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

    save: function () {
      this.loading = true
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
          if (this.editedItem.SellPrice !== "" && this.editedItem.SellPrice !== null && this.editedItem.PurchasePrice !== "" && this.editedItem.PurchasePrice != null) {
            this.products[this.editedIndex]['profit'] = ((1 - (this.products[this.editedIndex]['PurchasePrice'].replace("\u00A0", '') / this.products[this.editedIndex]['SellPrice'].replace("\u00A0", ''))) * 100).toFixed(2)
          }

          if(updatedProduct.Model !== this.oldModel){
            axios.post('/api/product/addEdits', {new: updatedProduct.Model, old : this.oldModel})
              .then(res => {
                if (!res.data)
                  useToast().success('Изменение модели добавлено в классификатор', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
              })
              .catch(function (error) {
                useToast().error('Ошибка добавления измененной модели в классификатор', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
                axios.post('/api/log', {
                  Time: Date.now(),
                  User: store.state.auth.user.UserID,
                  Message: 'Ошибка при ДОБАВЛЕНИИ измененной модели в классификатор: ' + updatedProduct.Model + '. Описание: ' + error,
                  Place: 'monitor.vue'
                })
              });
          }

          if (updatedProduct.Color !== null && updatedProduct.Color !== "") {
            axios.post('/api/colors/check', {Color: updatedProduct.Color,})
              .then(res => {
                if (!res.data)
                  useToast().success('Цвет добавлен в классификатор', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
              })
              .catch(function (error) {
                useToast().error('Ошибка добавления цвета', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
                axios.post('/api/log', {
                  Time: Date.now(),
                  User: store.state.auth.user.UserID,
                  Message: 'Ошибка при ДОБАВЛЕНИИ цвета: ' + updatedProduct.Model + '. Описание: ' + error,
                  Place: 'monitor.vue'
                })
              });
          }
          this.loading = false
        })
        .catch(function (error) {
          useToast().error('Ошибка обновления товара', {timeout:1000,closeOnClick:true,pauseOnFocusLoss:true,pauseOnHover:true,draggable:true,draggablePercent:1.16})
          axios.post('/api/log', {
            Time: Date.now(),
            User: store.state.auth.user.UserID,
            Message: 'Ошибка при ИЗМЕНЕНИИ товара: ' + updatedProduct.Model + '. Описание: ' + error,
            Place: 'monitor.vue'
          })
        });
    },
  },
}
</script>

<style scoped>
.filter-search{
  margin: 0 10px 5px 10px;
}

.custom_table_class thead tr th {
  font-size: 14px;
  text-transform: uppercase
}
</style>

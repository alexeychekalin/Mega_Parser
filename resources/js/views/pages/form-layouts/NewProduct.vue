<script setup>
const firstName = ref('')
const lastName = ref('')
const city = ref('')
const country = ref('')
const company = ref('')
const email = ref('')
const checkbox = ref(false)
</script>


<script>
import { useToast } from "vue-toastification";
export default {
  data: () => ({
    ClassName:'',
    PurchasePrice:'',
    SellPrice: '',
    Bonus: false,
    CardCash: false,
    Monitor: false,
    Type: '',
    rules: {
      required: value => !!value || 'Поле обязательно',
      //number: value => value.length <= 20 || 'Max 20 characters',
      number: value => {
        const pattern = /^\d*(\.\d{1,2})?$/
        return pattern.test(value) || 'Введите число, формат 12345.67'
      },
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
    newProduct(){

    }
  },
}
</script>

<template>
  <VForm @submit.prevent="newProduct">
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <v-text-field
          v-model="ClassName"
          label="Название"
          placeholder="Полное название"
          :rules="[rules.required]"
          prepend-inner-icon="mdi-shopping"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <v-select
          v-model="Type"
          label="Тип"
          :items="['ТИП 1', 'ТИП 2', 'ТИП 3', 'ТИП 4', 'ТИП 5', 'ТИП 6']"
          :rules="[rules.required]"
          prepend-inner-icon="mdi-format-list-bulleted-type"
        ></v-select>
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <v-text-field
          v-model="PurchasePrice"
          label="Закупочная цена"
          placeholder="Закупочная цена"
          :rules="[rules.required, rules.number]"
          prepend-inner-icon="mdi-currency-rub"
        />
      </VCol>

      <VCol
        cols="12"
        md="6"
      >
        <v-text-field
          v-model="SellPrice"
          label="Рыночная цена"
          placeholder="Рыночная цена"
          :rules="[rules.required, rules.number]"
          prepend-inner-icon="mdi-currency-rub"
        />
      </VCol>

      <VCol cols="12" md="4">
        <VCheckbox
          color="success"
          true-icon="mdi-credit-card-check-outline"
          false-icon="mdi-credit-card-off-outline"
          label="Оплата картой"
          v-model="CardCash"
        ></VCheckbox>
      </VCol>
      <VCol cols="12" md="4">
        <VCheckbox
          color="success"
          true-icon="mdi-gift-open-outline"
          false-icon="mdi-gift-off-outline"
          label="Бонусы Спасибо"
          v-model="Bonus"
        ></VCheckbox>
      </VCol>
      <VCol cols="12" md="4">
        <VCheckbox
          color="success"
          true-icon="mdi-shopping-search"
          false-icon="mdi-shopping-search-outline"
          label="Мониторинг"
          v-model="Monitor"
        ></VCheckbox>
      </VCol>

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

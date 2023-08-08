<template>
  <v-data-table
    v-model:page="page"
    :headers="headers"
    :items="filteredDesserts"
    :items-per-page="itemsPerPage"
    item-key="name"
    hide-default-footer
    class="custom_table_class"
  >
    <template v-slot:footer.prepend>
        <v-btn
          color="primary"
          dark
          variant="text"
          class="mr-10"
          @click="download"
        >
          Скачать XLSX
        </v-btn>
    </template>

    <template v-slot:bottom>
      <div class="text-center pt-2">
        <v-pagination
          v-model="page"
          :length="pageCount"
        ></v-pagination>
      </div>
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

  </v-data-table>
</template>

<script>
import * as XLSX from 'xlsx/xlsx.mjs';
export default {
  data: () => ({
    page: 1,
    itemsPerPage: 15,
    selected: [],
    headers: [
      {
        title: 'Dessert (100g serving)',
        align: 'center',
        key: 'name',
        class: 'section-dessert',
        width: '30%'
      },
      { title: 'Calories', key: 'calories', class: 'section-dessert', align: 'center', },
      { title: 'Fat (g)', key: 'fat', class: 'section-dessert', align: 'center', },
      { title: 'Carbs (g)', key: 'carbs', class: 'section-dessert', align: 'center', },
      { title: 'Protein (g)', key: 'protein', class: 'section-dessert', align: 'center', },
      { title: 'Iron (%)', key: 'iron', class: 'section-dessert', align: 'center', },
    ],
    filters: {
      name: [],
      calories: [],
      fat: [],
      carbs: [],
      protein: [],
      iron: [],
    },
    desserts: [
      {
        value: false,
        name: 'Frozen Yogurt',
        calories: 159,
        fat: 6.0,
        carbs: 24,
        protein: 4.0,
        iron: '1%',
      },
      {
        value: false,
        name: 'Ice cream sandwich',
        calories: 237,
        fat: 9.0,
        carbs: 37,
        protein: 4.3,
        iron: '1%',
      },
      {
        value: false,
        name: 'Eclair',
        calories: 262,
        fat: 16.0,
        carbs: 23,
        protein: 6.0,
        iron: '7%',
      },
      {
        value: false,
        name: 'Cupcake',
        calories: 305,
        fat: 3.7,
        carbs: 67,
        protein: 4.3,
        iron: '8%',
      },
      {
        value: false,
        name: 'Gingerbread',
        calories: 356,
        fat: 16.0,
        carbs: 49,
        protein: 3.9,
        iron: '16%',
      },
      {
        value: false,
        name: 'Jelly bean',
        calories: 375,
        fat: 0.0,
        carbs: 94,
        protein: 0.0,
        iron: '0%',
      },
      {
        value: false,
        name: 'Lollipop',
        calories: 392,
        fat: 0.2,
        carbs: 98,
        protein: 0,
        iron: '2%',
      },
      {
        value: false,
        name: 'Honeycomb',
        calories: 408,
        fat: 3.2,
        carbs: 87,
        protein: 6.5,
        iron: '45%',
      },
      {
        value: false,
        name: 'Donut',
        calories: 452,
        fat: 25.0,
        carbs: 51,
        protein: 4.9,
        iron: '22%',
      },
      {
        value: false,
        name: 'KitKat',
        calories: 518,
        fat: 26.0,
        carbs: 65,
        protein: 7,
        iron: '6%',
      },
    ],
  }),
  computed: {
    filteredDesserts() {
      return this.desserts.filter((d) => {
        return Object.keys(this.filters).every((f) => {
          return this.filters[f].length < 1 || this.filters[f].includes(d[f]);
        });
      });
    },
    pageCount () {
      return Math.ceil(this.desserts.length / this.itemsPerPage)
    },
  },

  methods: {
    columnValueList(val) {
      return this.desserts.map((d) => d[val]).filter((value, index, self) => self.indexOf(value) === index);
    },
    download : function() {
      let output = this.desserts.filter((d) => {
        return Object.keys(this.filters).every((f) => {
          return this.filters[f].length < 1 || this.filters[f].includes(d[f]);
        });
      });
      output.forEach(e => delete e.value)
      const data = XLSX.utils.json_to_sheet(output)
      const wb = XLSX.utils.book_new()
      XLSX.utils.book_append_sheet(wb, data, 'data')
      XLSX.writeFile(wb,'demo.xlsx')
    }
  },
}
</script>

<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
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

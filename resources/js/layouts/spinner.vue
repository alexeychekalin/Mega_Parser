<template>
  <v-dialog v-model="show" persistent width="auto" content content-class="centered-dialog">
    <v-container fill-height>
      <v-layout column justify-center align-center>
        <semipolar-spinner :animation-duration="2000" :size="65" color="#ff1d5e" />
      </v-layout>
    </v-container>
  </v-dialog>
</template>
<script>
import {
  SemipolarSpinner
} from 'epic-spinners'
export default {
  name: "spinner",
  components: {
    SemipolarSpinner
  },
  data: function() {
    return {
      show: true
    }
  },
}
</script>

<style>
.dialog.centered-dialog {
  background: #282c2dad;
  box-shadow: none;
  border-radius: 6px;
  width: auto;
  color: whitesmoke;
}
</style>

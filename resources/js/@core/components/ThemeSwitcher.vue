<script setup>
import { useTheme } from 'vuetify'
import store from "@/store";

const props = defineProps({
  themes: {
    type: Array,
    required: true,
  },
})



const {
  name: themeName,
  global: globalTheme,
} = useTheme()

const {
  state: currentThemeName,
  next: getNextThemeName,
  index: currentThemeIndex,
} = useCycleList(props.themes.map(t => t.name), { initialValue: themeName })

const changeTheme = () => {
  globalTheme.name.value = getNextThemeName()
  localStorage.setItem("color", globalTheme.name.value)
}

if(localStorage.getItem("color") !== 'dark')
{
  globalTheme.name.value = 'light'
  getNextThemeName()
}
// Update icon if theme is changed from other sources
watch(() => globalTheme.name.value, val => {
  currentThemeName.value = val
})
</script>

<template>
  <IconBtn @click="changeTheme">
    <VIcon :icon="props.themes[currentThemeIndex].icon" />
    <VTooltip
      activator="parent"
      open-delay="1000"
      scroll-strategy="close"
    >
      <span class="text-capitalize">{{ currentThemeName }}</span>
    </VTooltip>
  </IconBtn>
</template>

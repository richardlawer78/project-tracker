<script setup>
import { ref, onMounted, nextTick } from 'vue'
import AppHeader from '../components/layout/AppHeader.vue'
import AppSidebar from '../components/layout/AppSidebar.vue'
import AppFooter from '../components/layout/AppFooter.vue'

const isDarkMode = ref(false)

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  document.documentElement.classList.toggle('dark', isDarkMode.value)
}

onMounted(() => {
  document.documentElement.setAttribute('data-nav-layout', 'horizontal')
  document.documentElement.setAttribute('data-nav-style', 'menu-click')
  document.documentElement.setAttribute('data-menu-styles', 'light')
  document.documentElement.setAttribute('data-header-styles', 'light')

  nextTick(() => {
    setTimeout(() => {
      if (window.HSStaticMethods && window.HSStaticMethods.autoInit) {
        window.HSStaticMethods.autoInit()
      }
    }, 200)
  })
})
</script>

<template>
  <div class="page" :class="{ 'dark': isDarkMode }">
    <AppHeader @toggle-dark="toggleDarkMode" />
    <AppSidebar />
    <div class="main-content app-content">
      <div class="container-fluid">
        <slot />
      </div>
    </div>
    <AppFooter />
  </div>
</template>

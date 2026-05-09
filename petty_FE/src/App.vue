<template>
  <component :is="layout">
      <router-view></router-view>
  </component>
  <ChatbotWidget />
</template>

<script>
import ChatbotWidget from './layout/components/ChatbotWidget.vue'

const default_layout = "default";
export default {
  components: { ChatbotWidget },
  computed: {
    layout() {
      return (this.$route.meta.layout || default_layout) + "-layout";
    },
  },
};
</script>
<style>
</style>
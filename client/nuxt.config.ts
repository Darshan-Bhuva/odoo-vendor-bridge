export default defineNuxtConfig({
  modules: ['@nuxt/ui', '@nuxt/image'],
  css: ['./app/assets/css/main.css'],
  runtimeConfig: {
    public: {
      apiUrl: 'http://10.197.109.44:8000/api/v1'
    }
  }
})
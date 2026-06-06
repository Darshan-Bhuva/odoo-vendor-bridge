export default defineNuxtRouteMiddleware((to, from) => {
  // Storing tokens in localStorage is a client-only mechanism.
  if (import.meta.server) return

  const token = localStorage.getItem('auth_token')

  // If the user is not logged in and is trying to access a protected page,
  // redirect them to the login page.
  if (!token && to.path !== '/login') {
    return navigateTo('/login')
  }

  // If the user is already logged in and tries to access the login page,
  // redirect them to the home page (dashboard).
  if (token && to.path === '/login') {
    return navigateTo('/')
  }
})

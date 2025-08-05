import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null
  }),

  actions: {
    async register(userData) {
      try {
        const formData = new FormData()
        const fields = ['full_name', 'email', 'password', 'phone', 'address', 'image']
        
        Object.keys(userData).forEach(key => {
          if (fields.includes(key) && userData[key] !== null && userData[key] !== undefined && userData[key] !== '') {
            formData.append(key, userData[key])
          }
        })
        
        const response = await axios.post('/api/auth/register', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        this.token = response.data.token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      } catch (error) {
        console.error('register error:', error.response?.data)
        throw error
      }
    },

    async login(credentials) {
      try {
        const response = await axios.post('/api/auth/login', credentials)
        this.token = response.data.token
        localStorage.setItem('token', this.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        // Fetch user data after login
        await this.initializeAuth()
      } catch (error) {
        console.error('login error:', error.response?.data)
        throw error
      }
    },

    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    },

    async initializeAuth() {
      console.log('[Auth] 1. initializeAuth called.');
      if (this.token) {
        console.log('[Auth] 2. Token found in localStorage.', { token: this.token });
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        try {
          console.log('[Auth] 3. Fetching user data from /api/user...');
          const response = await axios.get('/api/user');
          this.user = response.data;
          console.log('[Auth] 4. User data fetched successfully.', { user: this.user });
        } catch (error) {
          console.error('[Auth] X. Failed to fetch user. Token might be invalid.', { error: error.response?.data });
          this.logout();
        }
      } else {
        console.log('[Auth] No token found in localStorage.');
      }
    }
  }
})

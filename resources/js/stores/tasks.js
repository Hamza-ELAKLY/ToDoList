import { defineStore } from 'pinia'
import axios from 'axios'

export const useTaskStore = defineStore('tasks', {
  state: () => ({
    tasks: []
  }),

  actions: {
    async fetchTasks() {
      try {
        const response = await axios.get('/api/tasks')
        this.tasks = response.data
      } catch (error) {
        console.error('fetch error:', error.response?.data)
      }
    },

    async addTask(title) {
      try {
        const response = await axios.post('/api/tasks', { title })
        this.tasks.push(response.data)
      } catch (error) {
        console.error('add task error:', error.response?.data)
        throw error
      }
    },

    async updateTask(id, data) {
      try {
        const response = await axios.put(`/api/tasks/${id}`, data)
        const index = this.tasks.findIndex(task => task.id === id)
        if (index !== -1) {
          this.tasks[index] = response.data
        }

      } catch (error) {
        console.error('update error:', error.response?.data)
        throw error
      }
    },

    async deleteTask(id) {
      try {
        await axios.delete(`/api/tasks/${id}`)
        this.tasks = this.tasks.filter(task => task.id !== id)
      } catch (error) {
        console.error('delete error:', error.response?.data)
        throw error
      }
    }
  }
})

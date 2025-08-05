<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm border-b">
      <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">To-Do List</h1>
        <div v-if="authStore.user" class="flex items-center gap-4">
          <button @click="showNotifications = !showNotifications" 
                  :class="showNotifications ? 'bg-blue-600' : 'bg-blue-500'" 
                  class="text-white px-4 py-2 rounded hover:bg-blue-600 relative">
            🔔 Notifications
            <span v-if="notifications.length > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
              {{ notifications.length }}
            </span>
          </button>
          <span class="text-gray-600">{{ authStore.user.full_name }}</span>
          <button @click="logout" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
            Logout
          </button>
        </div>
      </div>
    </nav>

    <!-- Notifications -->
    <div v-if="authStore.user && showNotifications" class="bg-yellow-50 border-b border-yellow-200">
      <div class="max-w-4xl mx-auto p-4">
        <div class="bg-white rounded-lg shadow p-4">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">🔔 Real-time Notifications</h2>
            <button @click="clearNotifications" class="text-sm text-red-500 hover:text-red-700">
              Clear All
            </button>
          </div>
          <div v-if="notifications.length === 0" class="text-center text-gray-500 py-4">
            No notifications yet. Create a task to see real-time notifications!
          </div>
          <div v-else class="space-y-2 max-h-60 overflow-y-auto">
            <div v-for="(notification, index) in notifications" :key="index" 
                 class="p-3 bg-green-50 border border-green-200 rounded-lg">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-medium text-green-800">{{ notification.message }}</p>
                  <p class="text-sm text-green-600">by {{ notification.user }}</p>
                  <p class="text-xs text-gray-500">{{ notification.time }}</p>
                </div>
                <button @click="removeNotification(index)" class="text-red-500 hover:text-red-700 text-sm">
                  ×
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="max-w-4xl mx-auto p-4">
      <!-- Auth -->
      <div v-if="!authStore.user" class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex mb-6">
            <button @click="showLogin = true" :class="showLogin ? 'bg-blue-500 text-white' : 'bg-gray-200'" class="flex-1 py-2 px-4 rounded-l">
              Login
            </button>
            <button @click="showLogin = false" :class="!showLogin ? 'bg-blue-500 text-white' : 'bg-gray-200'" class="flex-1 py-2 px-4 rounded-r">
              Register
            </button>
          </div>

          <!-- Login -->
          <form v-if="showLogin" @submit.prevent="login" class="space-y-4">
            <div>
              <input v-model="loginForm.email" type="email" placeholder="Email" required class="w-full p-3 border rounded">
            </div>
            <div>
              <input v-model="loginForm.password" type="password" placeholder="Password" required class="w-full p-3 border rounded">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-3 rounded hover:bg-blue-600">
              Login
            </button>
          </form>

          <!-- Register -->
          <form v-if="!showLogin" @submit.prevent="register" class="space-y-4">
            <div>
              <input v-model="registerForm.full_name" type="text" placeholder="Full Name" required class="w-full p-3 border rounded">
            </div>
            <div>
              <input v-model="registerForm.email" type="email" placeholder="Email" required class="w-full p-3 border rounded">
            </div>
            <div>
              <input v-model="registerForm.phone" type="text" placeholder="Phone" class="w-full p-3 border rounded">
            </div>
            <div>
              <input v-model="registerForm.address" type="text" placeholder="Address" class="w-full p-3 border rounded">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Profile Image (optional)</label>
              <input @change="handleImageUpload" type="file" accept="image/*" class="w-full p-3 border rounded">
            </div>
            <div>
              <input v-model="registerForm.password" type="password" placeholder="Password" required class="w-full p-3 border rounded">
            </div>
            <div>
              <input v-model="registerForm.password_confirmation" type="password" placeholder="Confirm Password" required class="w-full p-3 border rounded">
            </div>
            <button type="submit" class="w-full bg-green-500 text-white py-3 rounded hover:bg-green-600">
              Register
            </button>
          </form>
        </div>
      </div>

      <!-- Tasks  -->
      <div v-if="authStore.user">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <form @submit.prevent="addTask" class="flex gap-4">
            <input v-model="newTask" type="text" placeholder="Add a new task..." required 
                   class="flex-1 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">
              Add Task
            </button>
          </form>
        </div>

        <!-- Tasks List -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Your Tasks</h2>
          </div>
          <div v-if="taskStore.tasks.length === 0" class="p-6 text-center text-gray-500">
            No tasks yet. Add one above!
          </div>
          <div v-else class="divide-y">
            <div v-for="task in taskStore.tasks" :key="task.id" 
                 class="p-4 flex items-center justify-between hover:bg-gray-50">
              <div class="flex items-center gap-3">
                <input type="checkbox" v-model="task.completed" @change="updateTask(task)"
                       class="w-5 h-5">
                <span :class="task.completed ? 'line-through text-gray-500' : ''">
                  {{ task.title }}
                </span>
              </div>
              <button @click="deleteTask(task.id)" 
                      class="text-red-500 hover:text-red-700 px-3 py-1">
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import { useTaskStore } from './stores/tasks'

const authStore = useAuthStore()
const taskStore = useTaskStore()

const showLogin = ref(true)
const newTask = ref('')
const showNotifications = ref(false)
const notifications = ref([])

const loginForm = ref({
  email: '',
  password: ''
})

const registerForm = ref({
  full_name: '',
  email: '',
  phone: '',
  address: '',
  password: '',
  password_confirmation: ''
})

const login = async () => {
  try {
    await authStore.login(loginForm.value)
    await taskStore.fetchTasks()
    setupEcho()
  } catch (error) {
    console.error('login error:', error)
  }
}

const register = async () => {
  try {
    await authStore.register(registerForm.value)
    await taskStore.fetchTasks()
    setupEcho()
  } catch (error) {
    console.error('registration error:', error)
  }
}

const logout = async () => {
  await authStore.logout()
  taskStore.tasks = []
}

const addTask = async () => {
  if (newTask.value.trim()) {
    await taskStore.addTask(newTask.value.trim())
    newTask.value = ''
  }
}

const updateTask = async (task) => {
  await taskStore.updateTask(task.id, { completed: task.completed })
}

const deleteTask = async (id) => {
  await taskStore.deleteTask(id)
}

// notifications stuff
const clearNotifications = () => {
  notifications.value = []
}

const removeNotification = (index) => {
  notifications.value.splice(index, 1)
}

const addNotification = (data) => {
  notifications.value.unshift({
    message: data.message,
    user: data.user,
    time: new Date().toLocaleTimeString(),
    task: data.task
  })
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    registerForm.value.image = file
  }
}

const setupEcho = () => {
  console.log('[D] setupEcho: Function called.');
  if (window.Echo && authStore.user) {
    console.log('[E] setupEcho: Echo and user are valid. Setting up connection...');

    const pusher = window.Echo.connector.pusher;

    pusher.connection.bind('state_change', (states) => {
        console.log("[State Change]", states);
    });
    
    pusher.connection.bind('connected', () => {
      console.log('%c[F] setupEcho: WebSocket CONNECTED!', 'color: #00ff00; font-weight: bold;');
    });

    pusher.connection.bind('error', (err) => {
      console.error('[X] setupEcho: WebSocket CONNECTION FAILED!', err);
    });

    const channel = window.Echo.channel('tasks');
    console.log('[G] setupEcho: Subscribing to "tasks" channel.');

    channel.on('pusher:subscription_succeeded', () => {
      console.log('%c[H] setupEcho: Successfully SUBSCRIBED to "tasks" channel!', 'color: #00ff00;');
    });

    channel.on('pusher:subscription_error', (status) => {
      console.error('[X] setupEcho: FAILED to subscribe to "tasks" channel!', status);
    });

    channel.listen('.TaskCreated', (data) => {
      console.log('%c[I] setupEcho: NOTIFICATION RECEIVED!', 'color: #00ff00; font-weight: bold;', data);
      addNotification(data);
    });

  } else {
    console.error('[X] setupEcho: Pre-requisites not met.', { echo: !!window.Echo, user: !!authStore.user });
  }
}

onMounted(async () => {
  console.log('[A] onMounted: Component is mounted. Initializing auth.');
  await authStore.initializeAuth();
  console.log('[B] onMounted: Auth initialized. User object:', authStore.user);
  if (authStore.user) {
    console.log('[C] onMounted: User is authenticated. Fetching tasks and setting up WebSocket.');
    taskStore.fetchTasks();
    setupEcho();
  } else {
    console.error('[X] onMounted: User is NOT authenticated after init. WebSocket will not be set up.');
  }
});
</script>

<script setup>
import { ref } from 'vue'
import { store } from '../store'

const emit = defineEmits(['auth-success'])

const activeTab = ref('signin') // 'signin' or 'signup'

// Sign In Fields
const loginUsername = ref('')
const loginPassword = ref('')
const loginPasswordVisible = ref(false)
const loginLoading = ref(false)
const loginError = ref('')

// Sign Up Fields
const registerName = ref('')
const registerUsername = ref('')
const registerPassword = ref('')
const registerPasswordVisible = ref(false)
const selectedGender = ref(null)
const signupLoading = ref(false)
const signupError = ref('')

const toggleLoginPassword = () => {
  loginPasswordVisible.value = !loginPasswordVisible.value
}

const toggleRegisterPassword = () => {
  registerPasswordVisible.value = !registerPasswordVisible.value
}

const selectGender = (gender) => {
  selectedGender.value = gender
}

const handleSignIn = async () => {
  if (!loginUsername.value || !loginPassword.value) {
    loginError.value = 'Please enter username and password'
    return
  }
  loginError.value = ''
  loginLoading.value = true
  
  try {
    const success = await store.login(loginUsername.value, loginPassword.value)
    loginLoading.value = false
    if (success) {
      emit('auth-success')
    } else {
      loginError.value = 'Invalid login details'
    }
  } catch (e) {
    loginError.value = e.message || 'Login failed'
    loginLoading.value = false
  }
}

const handleSignUp = async () => {
  if (!registerName.value || !registerUsername.value || !registerPassword.value) {
    signupError.value = 'Please fill all fields'
    return
  }
  signupError.value = ''
  signupLoading.value = true
  
  try {
    const success = await store.register(
      registerName.value,
      registerUsername.value,
      registerPassword.value,
      selectedGender.value || 'male'
    )
    signupLoading.value = false
    if (success) {
      emit('auth-success')
    } else {
      signupError.value = 'Registration failed'
    }
  } catch (e) {
    signupError.value = e.message || 'Registration failed'
    signupLoading.value = false
  }
}
</script>

<template>
  <div class="auth-container scrollable-y animate-fade-in">
    <div class="logo-header">
      <div class="logo-icon">⚡</div>
      <h1 class="brand-title">PlayConnect</h1>
      <p class="brand-subtitle">Sportigo Matchmaker Platform</p>
    </div>

    <!-- Tab Selector -->
    <div class="auth-tabs">
      <button 
        class="auth-tab-btn" 
        :class="{ active: activeTab === 'signin' }"
        @click="activeTab = 'signin'"
      >
        Sign In
      </button>
      <button 
        class="auth-tab-btn" 
        :class="{ active: activeTab === 'signup' }"
        @click="activeTab = 'signup'"
      >
        Sign Up
      </button>
    </div>

    <!-- Sign In Panel -->
    <div v-if="activeTab === 'signin'" class="form-panel animate-fade-in">
      <h2 class="form-title">Welcome Back</h2>
      <p class="form-subtitle">Sign in to join your next match</p>

      <div v-if="loginError" class="error-banner">{{ loginError }}</div>

      <div class="input-group">
        <label class="input-label">Username</label>
        <div class="input-wrapper">
          <span class="input-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="input-svg"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <input 
            v-model="loginUsername" 
            type="text" 
            placeholder="Enter your username" 
            class="form-input"
            @keyup.enter="handleSignIn"
          />
        </div>
      </div>

      <div class="input-group">
        <label class="input-label">Password</label>
        <div class="input-wrapper">
          <span class="input-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="input-svg"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
          <input 
            v-model="loginPassword" 
            :type="loginPasswordVisible ? 'text' : 'password'" 
            placeholder="Enter your password" 
            class="form-input"
            @keyup.enter="handleSignIn"
          />
          <button class="password-toggle-btn" @click="toggleLoginPassword">
            <svg v-if="loginPasswordVisible" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="toggle-svg"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="toggle-svg"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
      </div>

      <div class="forgot-pwd">
        <a href="#" class="text-link">Forgot Password?</a>
      </div>

      <button class="submit-btn" :disabled="loginLoading" @click="handleSignIn">
        <span v-if="loginLoading" class="loader"></span>
        <span v-else>Sign In</span>
      </button>
    </div>

    <!-- Sign Up Panel -->
    <div v-else class="form-panel animate-fade-in">
      <h2 class="form-title">Create Account</h2>
      <p class="form-subtitle">Join the Sportigo community</p>

      <div v-if="signupError" class="error-banner">{{ signupError }}</div>

      <div class="input-group">
        <label class="input-label">Full Name</label>
        <div class="input-wrapper">
          <span class="input-icon">👤</span>
          <input 
            v-model="registerName" 
            type="text" 
            placeholder="Enter your full name" 
            class="form-input"
          />
        </div>
      </div>

      <div class="input-group">
        <label class="input-label">Username</label>
        <div class="input-wrapper">
          <span class="input-icon">📧</span>
          <input 
            v-model="registerUsername" 
            type="text" 
            placeholder="Choose a username" 
            class="form-input"
          />
        </div>
      </div>

      <div class="input-group">
        <label class="input-label">Password</label>
        <div class="input-wrapper">
          <span class="input-icon">🔒</span>
          <input 
            v-model="registerPassword" 
            :type="registerPasswordVisible ? 'text' : 'password'" 
            placeholder="Create a password" 
            class="form-input"
          />
          <button class="password-toggle-btn" @click="toggleRegisterPassword">
            {{ registerPasswordVisible ? '👁️' : '🙈' }}
          </button>
        </div>
      </div>

      <!-- Gender Selector -->
      <div class="gender-section">
        <label class="input-label">Gender Identity (Optional)</label>
        <div class="gender-cards">
          <div 
            class="gender-card male" 
            :class="{ active: selectedGender === 'male' }"
            @click="selectGender('male')"
          >
            <span class="gender-icon">♂️</span>
            <span class="gender-label">Male</span>
          </div>
          <div 
            class="gender-card female" 
            :class="{ active: selectedGender === 'female' }"
            @click="selectGender('female')"
          >
            <span class="gender-icon">♀️</span>
            <span class="gender-label">Female</span>
          </div>
          <div 
            class="gender-card other" 
            :class="{ active: selectedGender === 'other' }"
            @click="selectGender('other')"
          >
            <span class="gender-icon">⚧️</span>
            <span class="gender-label">Other</span>
          </div>
        </div>
      </div>

      <button class="submit-btn" :disabled="signupLoading" @click="handleSignUp">
        <span v-if="signupLoading" class="loader"></span>
        <span v-else>Sign Up</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
.auth-container {
  display: flex;
  flex-direction: column;
  width: 100%;
}

.logo-header {
  text-align: center;
  margin-top: 40px;
  margin-bottom: 32px;
}

.logo-icon {
  font-size: 3rem;
  margin-bottom: 12px;
  display: inline-block;
  animation: pulseGlow 2s infinite;
}

.brand-title {
  font-size: 1.8rem;
  font-weight: 800;
  color: var(--primary);
  margin-bottom: 4px;
}

.brand-subtitle {
  font-size: 0.85rem;
  color: var(--on-surface-variant);
  font-weight: 500;
}

.auth-tabs {
  display: flex;
  background-color: var(--surface-dim);
  border-radius: var(--radius-md);
  padding: 4px;
  margin-bottom: 32px;
}

.auth-tab-btn {
  flex: 1;
  border: none;
  background: none;
  padding: 10px;
  font-weight: 700;
  font-size: 0.9rem;
  border-radius: 12px;
  color: var(--on-surface-variant);
  cursor: pointer;
  transition: all 0.2s ease;
}

.auth-tab-btn.active {
  background-color: var(--surface);
  color: var(--primary);
  box-shadow: var(--shadow-sm);
}

.form-panel {
  display: flex;
  flex-direction: column;
}

.form-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 6px;
}

.form-subtitle {
  font-size: 0.88rem;
  color: var(--on-surface-variant);
  margin-bottom: 28px;
}

.error-banner {
  background-color: rgba(186, 26, 26, 0.1);
  color: var(--error);
  padding: 12px;
  border-radius: var(--radius-sm);
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 16px;
  border: 1px solid rgba(186, 26, 26, 0.2);
}

.input-group {
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
}

.input-label {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--on-surface);
  margin-bottom: 8px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--outline);
}

.input-svg {
  stroke: var(--outline);
  transition: stroke 0.2s ease;
}

.input-wrapper:focus-within .input-svg {
  stroke: var(--primary);
}

.form-input {
  width: 100%;
  padding: 14px 14px 14px 40px;
  background-color: var(--surface);
  border: 1px solid var(--outline-variant);
  border-radius: var(--radius-md);
  font-size: 0.95rem;
  font-weight: 500;
  color: var(--on-surface);
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-input:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.08);
}

.password-toggle-btn {
  position: absolute;
  right: 14px;
  background: none;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--outline);
}

.toggle-svg {
  stroke: var(--outline);
  transition: stroke 0.2s ease;
}

.password-toggle-btn:hover .toggle-svg {
  stroke: var(--primary);
}

.forgot-pwd {
  text-align: right;
  margin-bottom: 28px;
}

.text-link {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--primary);
  text-decoration: none;
}

.submit-btn {
  background-color: var(--primary);
  color: var(--on-primary);
  border: none;
  border-radius: var(--radius-md);
  padding: 16px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: var(--shadow-sm);
  transition: filter 0.2s ease;
}

.submit-btn:hover {
  filter: brightness(1.1);
}

.submit-btn:disabled {
  background-color: var(--outline-variant);
  color: var(--outline);
  cursor: not-allowed;
}

/* Gender selector design */
.gender-section {
  margin-bottom: 28px;
}

.gender-cards {
  display: flex;
  gap: 10px;
}

.gender-card {
  flex: 1;
  background-color: var(--surface);
  border: 1px solid var(--outline-variant);
  border-radius: var(--radius-md);
  padding: 14px 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.gender-icon {
  font-size: 1.6rem;
  color: var(--on-surface-variant);
}

.gender-label {
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--on-surface);
}

/* Active gender classes */
.gender-card.male.active {
  background-color: rgba(33, 150, 243, 0.08);
  border-color: #2196F3;
  box-shadow: 0 4px 10px rgba(33, 150, 243, 0.15);
}
.gender-card.male.active .gender-icon {
  color: #2196F3;
}

.gender-card.female.active {
  background-color: rgba(233, 30, 99, 0.08);
  border-color: #E91E63;
  box-shadow: 0 4px 10px rgba(233, 30, 99, 0.15);
}
.gender-card.female.active .gender-icon {
  color: #E91E63;
}

.gender-card.other.active {
  background-color: rgba(156, 39, 176, 0.08);
  border-color: #9C27B0;
  box-shadow: 0 4px 10px rgba(156, 39, 176, 0.15);
}
.gender-card.other.active .gender-icon {
  color: #9C27B0;
}

/* Loader animation spinner */
.loader {
  width: 20px;
  height: 20px;
  border: 2px solid var(--on-primary);
  border-bottom-color: transparent;
  border-radius: 50%;
  display: inline-block;
  animation: rotation 1s linear infinite;
}

@keyframes rotation {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@media (min-width: 1025px) {
  .logo-header {
    display: none;
  }
}
</style>

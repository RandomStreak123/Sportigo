<script setup>
import { ref, computed } from 'vue'
import { store } from '../store'
import { getPlayerAvatar } from '../utils/sportImageHelper'
import { supabase } from '../utils/supabase'

const emit = defineEmits(['auth-logout', 'toast-message'])

const props = defineProps({
  isCurrentUser: {
    type: Boolean,
    default: true
  },
  playerName: {
    type: String,
    default: ''
  },
  profilePicture: {
    type: String,
    default: null
  }
})

const selectedSport = ref('Football')
const activeSegmentTab = ref(0) // 0: Activity, 1: Achievements, 2: Streaks

// Self-ratings dictionary
const sportRatings = ref({
  'Football': 5,
  'Cricket': 4,
  'Badminton': 4,
  'Basketball': 3,
  'Tennis': 5,
  'Padel': 4
})

const sportsList = [
  { name: 'Football', icon: '⚽' },
  { name: 'Cricket', icon: '🏏' },
  { name: 'Badminton', icon: '🏸' },
  { name: 'Basketball', icon: '🏀' },
  { name: 'Tennis', icon: '🎾' },
  { name: 'Padel', icon: '🏓' }
]

const currentUser = computed(() => {
  if (props.isCurrentUser) {
    return store.state.currentUser || { name: 'Champ', gender: 'male', profilePhotoUrl: null }
  } else {
    return {
      name: props.playerName || 'Player',
      gender: 'male',
      profilePhotoUrl: props.profilePicture
    }
  }
})

const avatarUrl = computed(() => {
  return getPlayerAvatar(currentUser.value.profilePhotoUrl, currentUser.value.gender)
})

const isLavenderTheme = computed(() => {
  return store.isWomenMode.value
})

const getSportColor = (sport) => {
  switch (sport) {
    case 'Football': return '#2E7D32'
    case 'Cricket': return '#1E88E5'
    case 'Basketball': return '#FF9100'
    case 'Tennis': return '#AFB42B'
    case 'Padel': return '#008080'
    case 'Badminton': return '#8E24AA'
    default: return '#1a237e'
  }
}

const currentSportColor = computed(() => {
  return getSportColor(selectedSport.value)
})

const handleRate = (stars) => {
  if (!props.isCurrentUser) return
  sportRatings.value[selectedSport.value] = stars
  emit('toast-message', `Rated ${selectedSport.value} as ${stars} Stars! ⭐`)
}

const handleThemeToggle = (e) => {
  const checked = e.target.checked
  store.setThemePreference(checked ? 'elegantLavender' : 'activeSteelBlue')
}

const handleLogout = () => {
  store.logout()
  emit('auth-logout')
}

const handleSettingsInfo = (msg) => {
  emit('toast-message', msg)
}

// Supabase Avatar Upload
const fileInput = ref(null)
const isUploading = ref(false)

const onFileSelected = async (event) => {
  const files = event.target.files
  if (!files || files.length === 0) return

  const file = files[0]
  const userId = currentUser.value.id || 'guest'

  try {
    isUploading.value = true
    emit('toast-message', 'Uploading avatar to Supabase Storage... ⏳')

    // 1. Prepare file path
    const fileExt = file.name.split('.').pop()
    const filePath = `${userId}/avatar-${Date.now()}.${fileExt}`

    // 2. Upload file to avatar bucket
    const { error: uploadError } = await supabase.storage
      .from('avatar')
      .upload(filePath, file, {
        cacheControl: '3600',
        upsert: true
      })

    if (uploadError) throw uploadError

    // 3. Get public URL
    const { data: urlData } = supabase.storage
      .from('avatar')
      .getPublicUrl(filePath)

    const publicUrl = urlData.publicUrl

    // 4. Update Laravel backend database
    await store.updateProfile(currentUser.value.name, currentUser.value.gender, publicUrl)

    emit('toast-message', 'Profile picture updated successfully! 🎉')
  } catch (error) {
    console.error('Upload error:', error.message)
    emit('toast-message', `Upload failed: ${error.message} ❌`)
  } finally {
    isUploading.value = false
    if (fileInput.value) {
      fileInput.value.value = '' // reset input
    }
  }
}
</script>

<template>
  <div 
    class="profile-container scrollable-y animate-fade-in"
    :style="{ background: `linear-gradient(180deg, ${currentSportColor}26 0%, var(--scaffold-bg) 40%, var(--scaffold-bg) 100%)` }"
  >
    <!-- Custom Header -->
    <div class="profile-header">
      <h2 class="title">Player Profile</h2>
      <button v-if="isCurrentUser" class="settings-nav-btn" @click="handleSettingsInfo('Navigating to Settings panel... ⚙️')">
        ⚙️
      </button>
    </div>

    <!-- Profile Info Card -->
    <div class="profile-card">
      <div class="avatar-wrap">
        <img :src="avatarUrl" class="card-avatar" @error="(e) => e.target.src = '/assets/images/players/download.jpg'" />
        <button v-if="isCurrentUser" class="camera-btn" @click="fileInput.click()" :disabled="isUploading">
          <span v-if="isUploading">⏳</span>
          <span v-else>📷</span>
        </button>
        <input 
          ref="fileInput"
          type="file"
          accept="image/*"
          style="display: none"
          @change="onFileSelected"
        />
      </div>

      <h3 class="card-name">{{ currentUser.name }}</h3>
      <span class="card-level" :style="{ color: currentSportColor }">LEVEL 18</span>

      <!-- Action tags -->
      <div class="action-badges-row">
        <button class="badge-btn" @click="handleSettingsInfo('Profile details shared! 🔗')">
          📤 Share Profile
        </button>
        <button v-if="isCurrentUser" class="badge-btn edit" @click="handleSettingsInfo('Edit profile details coming soon! 📝')">
          ✏️ Edit Profile
        </button>
      </div>
    </div>

    <!-- XP system -->
    <div class="xp-container">
      <div class="xp-row">
        <span class="xp-lbl">Level Progression</span>
        <span class="xp-val">4,250 / 5,000 XP</span>
      </div>
      <div class="progress-track">
        <div class="progress-fill" :style="{ width: '85%', backgroundColor: currentSportColor }"></div>
      </div>
      <span class="xp-sub-lbl">750 XP to Level 19</span>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-box">
        <span class="stat-val">142</span>
        <span class="stat-lbl">Matches</span>
      </div>
      <div class="stat-box">
        <span class="stat-val">98%</span>
        <span class="stat-lbl">Reliability</span>
      </div>
      <div class="stat-box">
        <span class="stat-val">4.8</span>
        <span class="stat-lbl">Rating</span>
      </div>
      <div class="stat-box">
        <span class="stat-val">7 🔥</span>
        <span class="stat-lbl">Streak</span>
      </div>
    </div>

    <!-- Achievements segments selector -->
    <div class="segmented-bar">
      <button 
        class="segment-btn" 
        :class="{ active: activeSegmentTab === 0 }"
        @click="activeSegmentTab = 0"
      >
        Activity
      </button>
      <button 
        class="segment-btn" 
        :class="{ active: activeSegmentTab === 1 }"
        @click="activeSegmentTab = 1"
      >
        Achievements
      </button>
      <button 
        class="segment-btn" 
        :class="{ active: activeSegmentTab === 2 }"
        @click="activeSegmentTab = 2"
      >
        Streaks
      </button>
    </div>

    <!-- Active Segment panels -->
    <div class="segment-panel">
      <!-- Activity -->
      <div v-if="activeSegmentTab === 0" class="panel-content animate-fade-in">
        <div class="activity-history-tile">
          <span class="act-emoji">⚽</span>
          <div class="act-details">
            <span class="act-title">Played Friday 5v5 Turf Friendly</span>
            <span class="act-date">3 days ago · Sportego Arena</span>
          </div>
        </div>
        <div class="activity-history-tile">
          <span class="act-emoji">🏏</span>
          <div class="act-details">
            <span class="act-title">Organized Weekend T10 Practice</span>
            <span class="act-date">1 week ago · Municipal Ground</span>
          </div>
        </div>
      </div>

      <!-- Achievements -->
      <div v-else-if="activeSegmentTab === 1" class="panel-content achievements animate-fade-in">
        <div class="badge-item">🏅 MVP Match Player</div>
        <div class="badge-item">🔥 5 Match Streak Hero</div>
        <div class="badge-item">🤝 Fair Play Badge</div>
      </div>

      <!-- Streaks -->
      <div v-else class="panel-content streaks animate-fade-in">
        <div class="streak-details">
          <span class="streak-large">7</span>
          <span class="streak-label">Consecutive Weekly Matches Played</span>
        </div>
      </div>
    </div>

    <!-- Sports ratings selection -->
    <div class="sports-rating-section">
      <h4 class="section-sub-title">Sports Skill Profile</h4>
      
      <div class="chips-slider">
        <button 
          v-for="sport in sportsList" 
          :key="sport.name"
          class="sport-chip"
          :class="{ active: selectedSport === sport.name }"
          :style="selectedSport === sport.name ? { backgroundColor: currentSportColor, borderColor: currentSportColor } : {}"
          @click="selectedSport = sport.name"
        >
          <span class="chip-emoji">{{ sport.icon }}</span>
          {{ sport.name }}
        </button>
      </div>

      <!-- Rating stars display -->
      <div class="stars-card">
        <span class="stars-title">Rate Skill in {{ selectedSport }}</span>
        <div class="stars-row">
          <span 
            v-for="star in 5" 
            :key="star"
            class="star-item"
            @click="handleRate(star)"
          >
            {{ star <= (sportRatings[selectedSport] || 0) ? '⭐' : '☆' }}
          </span>
        </div>
        <span class="stars-helper-text">
          {{ isCurrentUser ? 'Tap stars to rate your self-assessment skill level' : 'Self-assessment profile rating' }}
        </span>
      </div>
    </div>

    <!-- Settings options -->
    <div v-if="isCurrentUser" class="privacy-section">
      <h4 class="section-sub-title">Personalization</h4>
      
      <!-- Theme Switch -->
      <div class="setting-switch-tile">
        <div class="setting-switch-info">
          <span class="tile-title">🌸 Elegant Lavender Theme</span>
          <span class="tile-desc">
            {{ isLavenderTheme ? 'Lavender palette mode active' : 'Switch to elegant lavender palette' }}
          </span>
        </div>
        <label class="toggle-control">
          <input :checked="isLavenderTheme" type="checkbox" @change="handleThemeToggle" />
          <span class="toggle-slider"></span>
        </label>
      </div>
    </div>

    <!-- Menu settings lists -->
    <div v-if="isCurrentUser" class="settings-menu-list">
      <div class="menu-tile" @click="handleSettingsInfo('Sportigo platform game guide coming soon! 📑')">
        <span class="menu-icon">🛡️</span>
        <div class="menu-info">
          <span class="menu-title">Dynamic Game Rules</span>
          <span class="menu-subtitle">Read platform game guide</span>
        </div>
        <span class="chevron">➔</span>
      </div>

      <div class="menu-tile" @click="handleSettingsInfo('Tournament logs coming soon! 🏆')">
        <span class="menu-icon">📊</span>
        <div class="menu-info">
          <span class="menu-title">Platform Stats History</span>
          <span class="menu-subtitle">Full tournament logs</span>
        </div>
        <span class="chevron">➔</span>
      </div>

      <div class="menu-tile destructive" @click="handleLogout">
        <span class="menu-icon">🚪</span>
        <div class="menu-info">
          <span class="menu-title">Sign Out</span>
          <span class="menu-subtitle">Exit application cleanly</span>
        </div>
        <span class="chevron">➔</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-container {
  padding: 56px 20px 80px;
  transition: background 0.6s ease;
}

.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.title {
  font-family: var(--font-display);
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--on-surface);
}

.settings-nav-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
}

.profile-card {
  background-color: var(--surface);
  border: 1px solid var(--outline-variant);
  border-radius: var(--radius-lg);
  padding: 24px 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-shadow: var(--shadow-sm);
  margin-bottom: 24px;
}

.avatar-wrap {
  position: relative;
  margin-bottom: 16px;
}

.card-avatar {
  width: 88px;
  height: 88px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #ffffff;
  box-shadow: var(--shadow-md);
}

.camera-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  background-color: var(--primary);
  border: none;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  color: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  font-size: 0.8rem;
  box-shadow: var(--shadow-sm);
}

.card-name {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--on-surface);
  margin-bottom: 4px;
}

.card-level {
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  margin-bottom: 16px;
}

.action-badges-row {
  display: flex;
  gap: 10px;
}

.badge-btn {
  background-color: var(--scaffold-bg);
  border: 1px solid var(--outline-variant);
  padding: 8px 14px;
  border-radius: var(--radius-md);
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--on-surface-variant);
  cursor: pointer;
}

.badge-btn.edit {
  background-color: rgba(26, 35, 126, 0.05);
  color: var(--primary);
  border-color: rgba(26, 35, 126, 0.15);
}

/* XP progressions */
.xp-container {
  background-color: var(--surface);
  border: 1px solid var(--outline-variant);
  border-radius: var(--radius-md);
  padding: 16px;
  margin-bottom: 20px;
  box-shadow: var(--shadow-sm);
}

.xp-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.85rem;
  font-weight: 700;
  margin-bottom: 8px;
}

.xp-lbl { color: var(--on-surface-variant); }
.xp-val { color: var(--on-surface); }

.progress-track {
  height: 8px;
  background-color: var(--surface-dim);
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 6px;
}

.progress-fill {
  height: 100%;
  border-radius: 4px;
}

.xp-sub-lbl {
  font-size: 0.7rem;
  color: var(--outline);
  font-weight: 600;
}

/* Stats grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
  margin-bottom: 24px;
}

.stat-box {
  background-color: var(--surface);
  border: 1px solid var(--outline-variant);
  border-radius: var(--radius-md);
  padding: 12px 4px;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-shadow: var(--shadow-sm);
}

.stat-val {
  font-family: var(--font-display);
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--on-surface);
  margin-bottom: 2px;
}

.stat-lbl {
  font-size: 0.65rem;
  font-weight: 700;
  color: var(--outline);
  text-transform: uppercase;
}

/* Segmented selector */
.segmented-bar {
  display: flex;
  background-color: var(--surface-dim);
  border-radius: var(--radius-md);
  padding: 4px;
  margin-bottom: 16px;
}

.segment-btn {
  flex: 1;
  border: none;
  background: none;
  padding: 8px;
  font-size: 0.8rem;
  font-weight: 700;
  border-radius: 12px;
  color: var(--on-surface-variant);
  cursor: pointer;
}

.segment-btn.active {
  background-color: var(--surface);
  color: var(--primary);
  box-shadow: var(--shadow-sm);
}

.segment-panel {
  margin-bottom: 28px;
}

.panel-content {
  background-color: var(--surface);
  border: 1px solid var(--outline-variant);
  border-radius: var(--radius-md);
  padding: 16px;
  min-height: 120px;
  box-shadow: var(--shadow-sm);
}

.activity-history-tile {
  display: flex;
  gap: 12px;
  align-items: center;
  padding-bottom: 12px;
  margin-bottom: 12px;
  border-bottom: 1px solid var(--outline-variant);
}

.activity-history-tile:last-child {
  border-bottom: none;
  padding-bottom: 0;
  margin-bottom: 0;
}

.act-emoji { font-size: 1.25rem; }

.act-details {
  display: flex;
  flex-direction: column;
}

.act-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--on-surface);
}

.act-date {
  font-size: 0.72rem;
  color: var(--outline);
}

.achievements {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.badge-item {
  background-color: var(--scaffold-bg);
  border: 1px solid var(--outline-variant);
  border-radius: var(--radius-md);
  padding: 10px;
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--on-surface);
}

.streak-details {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding-top: 14px;
}

.streak-large {
  font-size: 2.2rem;
  font-weight: 800;
  color: var(--primary);
}

.streak-label {
  font-size: 0.78rem;
  color: var(--on-surface-variant);
  font-weight: 600;
}

/* Sports ratings selection */
.sports-rating-section {
  margin-bottom: 28px;
}

.section-sub-title {
  font-size: 0.95rem;
  font-weight: 700;
  margin-bottom: 14px;
  color: var(--on-surface);
}

.chips-slider {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding-bottom: 12px;
  margin-bottom: 12px;
}

.chips-slider::-webkit-scrollbar {
  display: none;
}

.sport-chip {
  padding: 8px 16px;
  border-radius: 20px;
  border: 1px solid var(--outline-variant);
  background-color: var(--surface);
  color: var(--on-surface-variant);
  font-size: 0.78rem;
  font-weight: 700;
  cursor: pointer;
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 6px;
}

.sport-chip.active {
  color: #ffffff;
}

.stars-card {
  background-color: var(--surface);
  border: 1px solid var(--outline-variant);
  border-radius: var(--radius-md);
  padding: 16px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-shadow: var(--shadow-sm);
}

.stars-title {
  font-size: 0.88rem;
  font-weight: 700;
  margin-bottom: 12px;
}

.stars-row {
  display: flex;
  gap: 8px;
  font-size: 2rem;
  margin-bottom: 8px;
}

.star-item {
  cursor: pointer;
  user-select: none;
  transition: transform 0.1s ease;
}

.star-item:active {
  transform: scale(1.2);
}

.stars-helper-text {
  font-size: 0.7rem;
  color: var(--outline);
}

/* Toggle settings style */
.privacy-section {
  margin-bottom: 28px;
}

.setting-switch-tile {
  background-color: var(--surface);
  border: 1px solid var(--outline-variant);
  border-radius: var(--radius-md);
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: var(--shadow-sm);
}

.setting-switch-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.tile-title {
  font-size: 0.88rem;
  font-weight: 700;
  color: var(--on-surface);
}

.tile-desc {
  font-size: 0.72rem;
  color: var(--outline);
}

/* Custom toggler styling */
.toggle-control {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
  flex-shrink: 0;
}

.toggle-control input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: var(--outline-variant);
  transition: .3s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .3s;
  border-radius: 50%;
}

input:checked + .toggle-slider {
  background-color: var(--primary);
}

input:checked + .toggle-slider:before {
  transform: translateX(20px);
}

/* Settings Menu items list */
.settings-menu-list {
  background-color: var(--surface);
  border: 1px solid var(--outline-variant);
  border-radius: var(--radius-lg);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}

.menu-tile {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px 20px;
  border-bottom: 1px solid var(--outline-variant);
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.menu-tile:hover {
  background-color: var(--scaffold-bg);
}

.menu-tile:last-child {
  border-bottom: none;
}

.menu-icon {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background-color: var(--scaffold-bg);
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.1rem;
}

.menu-tile.destructive .menu-icon {
  background-color: rgba(186, 26, 26, 0.08);
}

.menu-info {
  display: flex;
  flex-direction: column;
  flex: 1;
}

.menu-title {
  font-size: 0.88rem;
  font-weight: 700;
  color: var(--on-surface);
}

.menu-tile.destructive .menu-title {
  color: var(--error);
}

.menu-subtitle {
  font-size: 0.72rem;
  color: var(--outline);
}

.chevron {
  font-size: 0.82rem;
  color: var(--outline-variant);
}
</style>

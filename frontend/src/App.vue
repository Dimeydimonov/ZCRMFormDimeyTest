<template>
  <div class="container">
    <h1>Zoho CRM Form</h1>

    <div v-if="successMessage" class="alert success">
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="alert error">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="submitForm">
      <div class="section">
        <h2>Deal Information</h2>

        <div class="field">
          <label>Deal Name *</label>
          <input v-model="form.deal_name" type="text" placeholder="Enter deal name" />
          <span v-if="errors.deal_name" class="field-error">{{ errors.deal_name }}</span>
        </div>

        <div class="field">
          <label>Deal Stage *</label>
          <select v-model="form.deal_stage">
            <option value="">-- Select stage --</option>
            <option value="Qualification">Qualification</option>
            <option value="Needs Analysis">Needs Analysis</option>
            <option value="Value Proposition">Value Proposition</option>
            <option value="Identify Decision Makers">Identify Decision Makers</option>
            <option value="Perception Analysis">Perception Analysis</option>
            <option value="Proposal/Price Quote">Proposal/Price Quote</option>
            <option value="Negotiation/Review">Negotiation/Review</option>
            <option value="Closed Won">Closed Won</option>
            <option value="Closed Lost">Closed Lost</option>
          </select>
          <span v-if="errors.deal_stage" class="field-error">{{ errors.deal_stage }}</span>
        </div>
      </div>

      <div class="section">
        <h2>Account Information</h2>

        <div class="field">
          <label>Account Name *</label>
          <input v-model="form.account_name" type="text" placeholder="Enter account name" />
          <span v-if="errors.account_name" class="field-error">{{ errors.account_name }}</span>
        </div>

        <div class="field">
          <label>Account Website</label>
          <input v-model="form.account_website" type="text" placeholder="https://example.com" />
          <span v-if="errors.account_website" class="field-error">{{ errors.account_website }}</span>
        </div>

        <div class="field">
          <label>Account Phone *</label>
          <input v-model="form.account_phone" type="tel" placeholder="+380501234567" />
          <span v-if="errors.account_phone" class="field-error">{{ errors.account_phone }}</span>
        </div>
      </div>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Sending...' : 'Create Deal & Account' }}
      </button>
    </form>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'App',
  data() {
    return {
      form: {
        deal_name: '',
        deal_stage: '',
        account_name: '',
        account_website: '',
        account_phone: ''
      },
      errors: {},
      loading: false,
      successMessage: '',
      errorMessage: ''
    }
  },
  methods: {
    validate() {
      this.errors = {}

      if (!this.form.deal_name.trim()) {
        this.errors.deal_name = 'Deal name is required'
      }
      if (!this.form.deal_stage) {
        this.errors.deal_stage = 'Please select a stage'
      }
      if (!this.form.account_name.trim()) {
        this.errors.account_name = 'Account name is required'
      }
      if (!this.form.account_phone.trim()) {
        this.errors.account_phone = 'Phone number is required'
      }

      // check website format if user filled it in
      if (this.form.account_website && !this.form.account_website.startsWith('http')) {
        this.errors.account_website = 'Website must start with http:// or https://'
      }

      // TODO: maybe add phone format validation

      return Object.keys(this.errors).length === 0
    },

    async submitForm() {
      this.successMessage = ''
      this.errorMessage = ''

      if (!this.validate()) {
        return
      }

      this.loading = true

      try {
        const res = await axios.post('http://localhost:8000/api/zoho/submit', this.form, {
          headers: { 'Accept': 'application/json' }
        })
        console.log('Response:', res.data)

        this.successMessage = res.data.message
        // clear form after success
        this.form = {
          deal_name: '',
          deal_stage: '',
          account_name: '',
          account_website: '',
          account_phone: ''
        }
      } catch (err) {
        console.error('Submit error:', err)
        if (err.response && err.response.data) {
          this.errorMessage = err.response.data.message || 'Something went wrong'
        } else {
          this.errorMessage = 'Network error, check if backend is running'
        }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: Arial, sans-serif;
  background-color: #f0f2f5;
}

.container {
  max-width: 600px;
  margin: 40px auto;
  background: #fff;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  margin-bottom: 25px;
  color: #333;
}

h2 {
  margin-bottom: 15px;
  color: #555;
  font-size: 18px;
}

.section {
  margin-bottom: 20px;
  padding: 20px;
  background: #fafafa;
  border-radius: 6px;
}

.field {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #444;
  font-size: 14px;
}

input,
select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
}

input:focus,
select:focus {
  outline: none;
  border-color: #4a90e2;
}

.field-error {
  color: #e74c3c;
  font-size: 12px;
  margin-top: 4px;
  display: block;
}

button {
  width: 100%;
  padding: 12px;
  background-color: #4a90e2;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  margin-top: 10px;
}

button:hover {
  background-color: #357abd;
}

button:disabled {
  background-color: #999;
  cursor: not-allowed;
}

.alert {
  padding: 12px;
  border-radius: 4px;
  margin-bottom: 15px;
  font-size: 14px;
}

.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
</style>

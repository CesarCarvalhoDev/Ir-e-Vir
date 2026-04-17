<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white flex items-center justify-center">
    <!-- Login Card -->
    <div class="w-full max-w-md mx-4">
      <div class="bg-slate-800 rounded-2xl shadow-2xl p-8 border border-slate-700">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-4xl font-bold mb-2">🅿️ Ir e Vir</h1>
          <p class="text-slate-400">Sistema de Estacionamento</p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="space-y-4">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
              E-mail
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="seu@email.com"
              class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors"
              required
            />
            <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email[0] }}</p>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
              Senha
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors"
              required
            />
            <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password[0] }}</p>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="bg-red-500/10 border border-red-500/50 rounded-lg p-3 text-red-400 text-sm">
            {{ error }}
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-600 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors"
          >
            <span v-if="!loading">Entrar</span>
            <span v-else class="flex items-center justify-center gap-2">
              <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              Entrando...
            </span>
          </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center border-t border-slate-700 pt-6">
          <p class="text-slate-400 mb-3">Não tem uma conta?</p>
          <button
            @click="showRegister = true"
            type="button"
            class="text-blue-400 hover:text-blue-300 font-semibold transition-colors"
          >
            Registre-se aqui
          </button>
        </div>
      </div>

      <!-- Register Modal -->
      <div v-if="showRegister" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 rounded-2xl shadow-2xl p-8 border border-slate-700 w-full max-w-md">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Criar Conta</h2>
            <button
              @click="showRegister = false"
              type="button"
              class="text-slate-400 hover:text-white text-2xl"
            >
              ✕
            </button>
          </div>

          <form @submit.prevent="handleRegister" class="space-y-4">
            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                Nome
              </label>
              <input
                id="name"
                v-model="registerForm.name"
                type="text"
                placeholder="Seu nome"
                class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors"
                required
              />
              <p v-if="registerErrors.name" class="text-red-500 text-sm mt-1">{{ registerErrors.name[0] }}</p>
            </div>

            <!-- Email -->
            <div>
              <label for="register-email" class="block text-sm font-medium text-slate-300 mb-2">
                E-mail
              </label>
              <input
                id="register-email"
                v-model="registerForm.email"
                type="email"
                placeholder="seu@email.com"
                class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors"
                required
              />
              <p v-if="registerErrors.email" class="text-red-500 text-sm mt-1">{{ registerErrors.email[0] }}</p>
            </div>

            <!-- Password -->
            <div>
              <label for="register-password" class="block text-sm font-medium text-slate-300 mb-2">
                Senha
              </label>
              <input
                id="register-password"
                v-model="registerForm.password"
                type="password"
                placeholder="••••••••"
                class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors"
                required
              />
              <p v-if="registerErrors.password" class="text-red-500 text-sm mt-1">{{ registerErrors.password[0] }}</p>
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="register-confirm" class="block text-sm font-medium text-slate-300 mb-2">
                Confirmar Senha
              </label>
              <input
                id="register-confirm"
                v-model="registerForm.password_confirmation"
                type="password"
                placeholder="••••••••"
                class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors"
                required
              />
            </div>

            <!-- Error Message -->
            <div v-if="registerError" class="bg-red-500/10 border border-red-500/50 rounded-lg p-3 text-red-400 text-sm">
              {{ registerError }}
            </div>

            <!-- Buttons -->
            <div class="flex gap-3">
              <button
                type="submit"
                :disabled="registerLoading"
                class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-600 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors"
              >
                <span v-if="!registerLoading">Registrar</span>
                <span v-else class="flex items-center justify-center gap-2">
                  <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  Registrando...
                </span>
              </button>
              <button
                type="button"
                @click="showRegister = false"
                class="flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition-colors"
              >
                Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import authService from '../services/authService.js';

export default {
  name: 'LoginPage',
  emits: ['login'],
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      registerForm: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      loading: false,
      registerLoading: false,
      showRegister: false,
      error: '',
      registerError: '',
      errors: {},
      registerErrors: {},
    };
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.error = '';
      this.errors = {};

      try {
        const data = await authService.login(this.form.email, this.form.password);
        this.$emit('login', data);
      } catch (err) {
        this.error = err.message || 'Erro de conexão. Tente novamente.';
        console.error('Login error:', err);
      } finally {
        this.loading = false;
      }
    },

    async handleRegister() {
      this.registerLoading = true;
      this.registerError = '';
      this.registerErrors = {};

      try {
        const data = await authService.register(
          this.registerForm.name,
          this.registerForm.email,
          this.registerForm.password,
          this.registerForm.password_confirmation
        );

        // Reset form and close modal
        this.showRegister = false;
        this.registerForm = {
          name: '',
          email: '',
          password: '',
          password_confirmation: '',
        };

        this.$emit('login', data);
      } catch (err) {
        // Se for um objeto com erros de validação
        if (err.errors) {
          this.registerErrors = err.errors;
          this.registerError = 'Por favor, corrija os erros abaixo.';
        } else {
          this.registerError = err.message || 'Erro de conexão. Tente novamente.';
        }
        console.error('Register error:', err);
      } finally {
        this.registerLoading = false;
      }
    },
  },
};
</script>

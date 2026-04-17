const API_URL = import.meta.env.VITE_API_URL;

class AuthService {
  constructor() {
    // Não carrega do localStorage aqui - apenas ao fazer login
    this.token = null;
    this.user = null;
  }

  // Carrega user do localStorage se existir um token válido
  async loadFromStorage() {
    const token = localStorage.getItem('auth_token');
    const user = localStorage.getItem('user');
    
    if (token && user) {
      this.token = token;
      this.user = JSON.parse(user);
      
      // Valida o token com o backend
      const validUser = await this.getCurrentUser();
      if (!validUser) {
        // Token inválido, limpa tudo
        this.clearAuth();
      }
    }
  }

  clearAuth() {
    this.token = null;
    this.user = null;
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
  }

  async login(email, password) {
    try {
      const response = await fetch(`${API_URL}/auth/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (!response.ok) {
        if (data.errors) {
          // Erros de validação
          const errorMessages = Object.values(data.errors)
            .flat()
            .join(', ');
          throw new Error(errorMessages);
        }
        throw new Error(data.message || 'Login failed');
      }

      this.token = data.token;
      this.user = data.user;
      localStorage.setItem('auth_token', data.token);
      localStorage.setItem('user', JSON.stringify(data.user));

      return data;
    } catch (error) {
      throw error;
    }
  }

  async register(name, email, password, password_confirmation) {
    try {
      const response = await fetch(`${API_URL}/auth/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({ name, email, password, password_confirmation }),
      });

      const data = await response.json();

      if (!response.ok) {
        if (data.errors) {
          // Erros de validação - retorna como objeto para tratamento no componente
          throw { 
            message: 'Validação falhou',
            errors: data.errors 
          };
        }
        throw new Error(data.message || 'Registration failed');
      }

      this.token = data.token;
      this.user = data.user;
      localStorage.setItem('auth_token', data.token);
      localStorage.setItem('user', JSON.stringify(data.user));

      return data;
    } catch (error) {
      throw error;
    }
  }

  async logout() {
    try {
      const response = await fetch(`${API_URL}/auth/logout`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        credentials: 'include',
      });

      this.token = null;
      this.user = null;
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');

      return response.ok;
    } catch (error) {
      console.error('Logout error:', error);
      // Clear local data anyway
      this.token = null;
      this.user = null;
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      return true;
    }
  }

  async getCurrentUser() {
    if (!this.token) {
      return null;
    }

    try {
      const response = await fetch(`${API_URL}/auth/me`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        credentials: 'include',
      });

      if (!response.ok) {
        this.logout();
        return null;
      }

      const data = await response.json();
      this.user = data.user;
      localStorage.setItem('user', JSON.stringify(data.user));

      return data.user;
    } catch (error) {
      console.error('Get user error:', error);
      this.logout();
      return null;
    }
  }

  isAuthenticated() {
    return !!this.token && !!this.user;
  }

  getToken() {
    return this.token;
  }

  getUser() {
    return this.user;
  }

  async fetchCharges() {
    try {
      const response = await fetch(`${API_URL}/charges`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        credentials: 'include',
      });

      if (!response.ok) {
        throw new Error('Failed to fetch charges');
      }

      return response.json();
    } catch (error) {
      console.error('Fetch charges error:', error);
      throw error;
    }
  }

  async fetchChargesByPlate(plate) {
    try {
      const response = await fetch(`${API_URL}/charges/plate/${plate}`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        credentials: 'include',
      });

      if (!response.ok) {
        throw new Error('Failed to fetch charges by plate');
      }

      return response.json();
    } catch (error) {
      console.error('Fetch charges by plate error:', error);
      throw error;
    }
  }
}

export default new AuthService();

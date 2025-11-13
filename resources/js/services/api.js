import axios from 'axios';

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: true,
});

// Add CSRF token to requests
apiClient.interceptors.request.use((config) => {
  const token = document.head.querySelector('meta[name="csrf-token"]')?.content;
  if (token) {
    config.headers['X-CSRF-TOKEN'] = token;
  }
  return config;
});

// Handle response errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Redirect to login if unauthorized
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

/**
 * User API Service
 */
export const userApi = {
  /**
   * Get all users with pagination, filtering, and sorting
   * @param {Object} params - Query parameters (page, limit, search, sort, filter, fields)
   * @returns {Promise}
   */
  getAll(params = {}) {
    return apiClient.get('/users', { params });
  },

  /**
   * Get a single user by ID
   * @param {Number} id - User ID
   * @param {Object} params - Query parameters (fields)
   * @returns {Promise}
   */
  getById(id, params = {}) {
    return apiClient.get(`/users/${id}`, { params });
  },

  /**
   * Create a new user
   * @param {Object} data - User data
   * @returns {Promise}
   */
  create(data) {
    return apiClient.post('/users', data);
  },

  /**
   * Update an existing user
   * @param {Number} id - User ID
   * @param {Object} data - User data
   * @returns {Promise}
   */
  update(id, data) {
    return apiClient.put(`/users/${id}`, data);
  },

  /**
   * Delete a user
   * @param {Number} id - User ID
   * @returns {Promise}
   */
  delete(id) {
    return apiClient.delete(`/users/${id}`);
  },
};

export default apiClient;

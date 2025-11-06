import { $api } from '@/utils/api'

export interface User {
  id: number
  first_name: string
  last_name: string
  name: string
  email: string
  mobile?: string | null
  phone?: string | null
  status: string
  birthdate?: string | null
  gender?: 'male' | 'female' | 'other' | null
  role?: string | null
  email_verified_at?: string | null
  created_at: string
  updated_at?: string
}

export interface CreateUserPayload {
  first_name: string
  last_name: string
  email: string
  mobile?: string
  phone?: string
  status: string
  birthdate?: string
  gender?: 'male' | 'female' | 'other'
  password: string
  password_confirmation: string
  role?: string
}

export interface UpdateUserPayload {
  first_name?: string
  last_name?: string
  email?: string
  mobile?: string
  phone?: string
  status?: string
  birthdate?: string
  gender?: 'male' | 'female' | 'other'
  password?: string
  password_confirmation?: string
  role?: string
}

export interface UsersResponse {
  data: User[]
  links?: any
  meta?: any
}

export const userService = {
  /**
   * Get all users with pagination
   */
  async getUsers(page: number = 1, itemsPerPage: number = 10): Promise<UsersResponse> {
    // TODO: Switch to '/admin/users' after implementing authentication
    const endpoint = import.meta.env.DEV ? '/admin/users/test' : '/admin/users'

    const response = await $api<{ data: User[], links?: any, meta?: any }>(endpoint, {
      method: 'GET',
      params: {
        page,
        per_page: itemsPerPage,
      },
    })

    // Handle Laravel pagination response structure
    return {
      data: response.data || [],
      links: response.links,
      meta: response.meta,
    }
  },

  /**
   * Get a single user by ID
   */
  async getUser(id: number): Promise<{ data: User }> {
    return await $api(`/admin/users/${id}`, {
      method: 'GET',
    })
  },

  /**
   * Create a new user
   */
  async createUser(payload: CreateUserPayload): Promise<{ data: User }> {
    return await $api('/admin/users', {
      method: 'POST',
      body: payload,
    })
  },

  /**
   * Update an existing user
   */
  async updateUser(id: number, payload: UpdateUserPayload): Promise<{ data: User }> {
    return await $api(`/admin/users/${id}`, {
      method: 'PUT',
      body: payload,
    })
  },

  /**
   * Delete a user
   */
  async deleteUser(id: number): Promise<{ success: boolean; message: string }> {
    return await $api(`/admin/users/${id}`, {
      method: 'DELETE',
    })
  },
}

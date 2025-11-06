export interface UserProperties {
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
  avatar?: string
}

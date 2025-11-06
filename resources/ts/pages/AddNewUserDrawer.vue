<script setup lang="ts">
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import type { VForm } from 'vuetify/components/VForm'
import type { CreateUserPayload } from '@/services/userService'

interface Emit {
  (e: 'update:isDrawerOpen', value: boolean): void
  (e: 'userData', value: CreateUserPayload): void
}

interface Props {
  isDrawerOpen: boolean
}

const props = defineProps<Props>()
const emit = defineEmits<Emit>()

const isFormValid = ref(false)
const refForm = ref<VForm>()
const firstName = ref('')
const lastName = ref('')
const email = ref('')
const mobile = ref('')
const phone = ref('')
const birthdate = ref('')
const gender = ref<'male' | 'female' | 'other'>()
const role = ref('user')
const status = ref('active')
const password = ref('')
const passwordConfirmation = ref('')

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)

  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      emit('userData', {
        first_name: firstName.value,
        last_name: lastName.value,
        email: email.value,
        mobile: mobile.value,
        phone: phone.value,
        status: status.value,
        birthdate: birthdate.value,
        gender: gender.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value,
        role: role.value,
      })
      emit('update:isDrawerOpen', false)
      nextTick(() => {
        refForm.value?.reset()
        refForm.value?.resetValidation()
      })
    }
  })
}

const handleDrawerModelValueUpdate = (val: boolean) => {
  emit('update:isDrawerOpen', val)
}
</script>

<template>
  <VNavigationDrawer
    data-allow-mismatch
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <AppDrawerHeaderSection
      title="Add New User"
      @cancel="closeNavigationDrawer"
    />

    <VDivider />

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            v-model="isFormValid"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 First Name -->
              <VCol cols="12">
                <AppTextField
                  v-model="firstName"
                  :rules="[requiredValidator]"
                  label="First Name"
                  placeholder="John"
                />
              </VCol>

              <!-- 👉 Last Name -->
              <VCol cols="12">
                <AppTextField
                  v-model="lastName"
                  :rules="[requiredValidator]"
                  label="Last Name"
                  placeholder="Doe"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <AppTextField
                  v-model="email"
                  :rules="[requiredValidator, emailValidator]"
                  label="Email"
                  placeholder="johndoe@email.com"
                />
              </VCol>

              <!-- 👉 Mobile -->
              <VCol cols="12">
                <AppTextField
                  v-model="mobile"
                  label="Mobile"
                  placeholder="+1-123-456-7890"
                />
              </VCol>

              <!-- 👉 Phone -->
              <VCol cols="12">
                <AppTextField
                  v-model="phone"
                  label="Phone"
                  placeholder="+1-987-654-3210"
                />
              </VCol>

              <!-- 👉 Birthdate -->
              <VCol cols="12">
                <AppTextField
                  v-model="birthdate"
                  type="date"
                  label="Birthdate"
                  placeholder="1990-01-01"
                />
              </VCol>

              <!-- 👉 Gender -->
              <VCol cols="12">
                <AppSelect
                  v-model="gender"
                  label="Select Gender"
                  placeholder="Select Gender"
                  :items="[
                    { title: 'Male', value: 'male' },
                    { title: 'Female', value: 'female' },
                    { title: 'Other', value: 'other' }
                  ]"
                />
              </VCol>

              <!-- 👉 Role -->
              <VCol cols="12">
                <AppSelect
                  v-model="role"
                  label="Select Role"
                  placeholder="Select Role"
                  :rules="[requiredValidator]"
                  :items="[
                    { title: 'Admin', value: 'admin' },
                    { title: 'User', value: 'user' }
                  ]"
                />
              </VCol>

              <!-- 👉 Status -->
              <VCol cols="12">
                <AppSelect
                  v-model="status"
                  label="Select Status"
                  placeholder="Select Status"
                  :rules="[requiredValidator]"
                  :items="[
                    { title: 'Active', value: 'active' },
                    { title: 'Inactive', value: 'inactive' },
                    { title: 'Pending', value: 'pending' }
                  ]"
                />
              </VCol>

              <!-- 👉 Password -->
              <VCol cols="12">
                <AppTextField
                  v-model="password"
                  :rules="[requiredValidator]"
                  label="Password"
                  type="password"
                  placeholder="Enter password"
                />
              </VCol>

              <!-- 👉 Confirm Password -->
              <VCol cols="12">
                <AppTextField
                  v-model="passwordConfirmation"
                  :rules="[requiredValidator]"
                  label="Confirm Password"
                  type="password"
                  placeholder="Confirm password"
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  Submit
                </VBtn>
                <VBtn
                  type="reset"
                  variant="tonal"
                  color="error"
                  @click="closeNavigationDrawer"
                >
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>

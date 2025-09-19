# Profile Components

Komponen-komponen untuk halaman profil pengguna yang modular dan dapat digunakan kembali.

## ProfileForm.vue

Komponen form untuk mengupdate informasi profil pengguna.

### Props
- `formData` (Object): Data form yang berisi informasi profil
- `loading` (Boolean): Status loading saat submit form
- `errors` (Object): Error validasi dari server

### Events
- `submit`: Dipanggil saat form disubmit dengan data yang valid
- `reset`: Dipanggil saat tombol reset diklik

### Fields
- `name`: Nama lengkap pengguna
- `email`: Alamat email pengguna
- `phone`: Nomor telepon pengguna
- `bio`: Biografi singkat pengguna
- `location`: Lokasi pengguna

## SecurityForm.vue

Komponen form untuk mengubah password dengan validasi kekuatan password.

### Props
- `formData` (Object): Data form yang berisi password fields
- `loading` (Boolean): Status loading saat submit form
- `errors` (Object): Error validasi dari server
- `passwordStrength` (Object): Objek yang berisi informasi kekuatan password

### Events
- `submit`: Dipanggil saat form disubmit dengan data yang valid
- `reset`: Dipanggil saat tombol reset diklik
- `password-input`: Dipanggil saat user mengetik password baru untuk validasi kekuatan

### Fields
- `current_password`: Password saat ini
- `password`: Password baru
- `password_confirmation`: Konfirmasi password baru

### Password Strength Validation
Komponen ini memvalidasi kekuatan password berdasarkan:
- Panjang minimal 8 karakter
- Kombinasi huruf besar dan kecil
- Mengandung angka
- Mengandung karakter khusus
- Berbeda dari password saat ini

## Usage Example

```vue
<template>
  <div>
    <!-- Profile Form -->
    <ProfileForm
      :form-data="profileForm"
      :loading="loading.profile"
      :errors="formErrors.profile"
      @submit="handleProfileUpdate"
      @reset="resetProfileForm"
    />

    <!-- Security Form -->
    <SecurityForm
      :form-data="passwordForm"
      :loading="loading.password"
      :errors="formErrors.password"
      :password-strength="passwordStrength"
      @submit="handlePasswordChange"
      @reset="resetPasswordForm"
      @password-input="calculatePasswordStrength"
    />
  </div>
</template>
```

## Styling

Kedua komponen menggunakan:
- Quasar UI components untuk konsistensi
- Scoped styling untuk isolasi CSS
- Responsive design yang mengikuti sistem design aplikasi
- Border radius 8px untuk elemen form
- Konsistensi warna dan spacing dengan halaman lain
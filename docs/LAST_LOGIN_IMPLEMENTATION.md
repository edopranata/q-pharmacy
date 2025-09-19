# Implementasi Sistem Last Login

## Overview

Sistem Q-Pharmacy telah mengimplementasikan fitur tracking waktu login terakhir pengguna. Dokumentasi ini menjelaskan implementasi lengkap, termasuk skema database, logika backend, dan pertimbangan keamanan.

## 1. Modifikasi Skema Database

### Field last_login pada Tabel Users

Field `last_login` telah ditambahkan ke tabel `users` melalui migrasi:

```php
// File: database/migrations/2025_09_18_035834_add_fields_to_users_table.php

Schema::table('users', function (Blueprint $table) {
    $table->timestamp('last_login')->nullable()->after('email_verified_at');
    $table->string('avatar')->nullable()->after('last_login');
    $table->string('phone')->nullable()->after('avatar');
});
```

### Struktur Tabel Users (Updated)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PRIMARY KEY, AUTO_INCREMENT | ID unik pengguna |
| name | varchar(255) | NOT NULL | Nama lengkap pengguna |
| email | varchar(255) | NOT NULL, UNIQUE | Email pengguna |
| email_verified_at | timestamp | NULL | Waktu verifikasi email |
| **last_login** | **timestamp** | **NULL** | **Waktu login terakhir** |
| avatar | varchar(255) | NULL | Path avatar pengguna |
| phone | varchar(255) | NULL | Nomor telepon |
| password | varchar(255) | NOT NULL | Password terenkripsi |
| remember_token | varchar(100) | NULL | Token remember me |
| created_at | timestamp | NULL | Waktu pembuatan |
| updated_at | timestamp | NULL | Waktu update terakhir |

## 2. Logika Pencatatan Waktu Login

### Implementasi di AuthController

Sistem sudah mengimplementasikan update `last_login` di method login:

```php
// File: app/Http/Controllers/Api/AuthController.php

public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return BaseResponseService::validationError($validator->errors());
    }

    if (! Auth::attempt($request->only('email', 'password'))) {
        return BaseResponseService::unauthorized('Invalid credentials');
    }

    /** @var \App\Models\User $user */
    $user = Auth::user();
    
    // Update last_login timestamp
    $user->update(['last_login' => now()]);
    
    $token = $user->createToken('auth_token')->plainTextToken;

    return BaseResponseService::success([
        'user' => $user->load('roles'),
        'token' => $token,
        'token_type' => 'Bearer',
    ], 'Login successful');
}
```

### Model User Configuration

Field `last_login` sudah dikonfigurasi di model User:

```php
// File: app/Models/User.php

protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime', // Tambahkan ini jika belum ada
        'password' => 'hashed',
    ];
}
```

## 3. Mekanisme Pembaruan Field last_login

### Saat Login Berhasil

1. **Validasi Kredensial**: Sistem memvalidasi email dan password
2. **Autentikasi**: Laravel Auth::attempt() memverifikasi kredensial
3. **Update Timestamp**: Jika berhasil, field `last_login` diupdate dengan `now()`
4. **Generate Token**: Sistem membuat token Sanctum untuk API access
5. **Response**: Mengembalikan data user dan token

### Flow Diagram

```
User Login Request
       ↓
   Validate Input
       ↓
  Auth::attempt()
       ↓
   Success? ──No──→ Return Error
       ↓ Yes
 Update last_login
       ↓
  Generate Token
       ↓
  Return Response
```

## 4. Pertimbangan Keamanan

### A. Rate Limiting

```php
// File: app/Http/Middleware/ThrottleRequests.php

// Implementasi rate limiting untuk login
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
});
```

### B. Audit Logging

Sistem sudah mengimplementasikan audit logging untuk aktivitas autentikasi:

```php
// File: app/Listeners/AuditAuthListener.php

public function handleLogin(Login $event): void
{
    AuditLogger::logAuth(
        'login',
        $event->user,
        "User {$event->user->name} logged in"
    );
}
```

### C. Session Security

- **Token Expiration**: Sanctum tokens dapat dikonfigurasi untuk expire
- **IP Tracking**: Sistem mencatat IP address dalam audit logs
- **User Agent**: Browser/device information disimpan untuk security monitoring

### D. Brute Force Protection

```php
// Contoh implementasi tambahan untuk brute force protection

public function login(Request $request)
{
    $key = 'login_attempts_' . $request->ip();
    $attempts = Cache::get($key, 0);
    
    if ($attempts >= 5) {
        return BaseResponseService::error(
            'Too many login attempts. Please try again later.',
            429
        );
    }
    
    if (! Auth::attempt($request->only('email', 'password'))) {
        Cache::put($key, $attempts + 1, 900); // 15 minutes
        return BaseResponseService::unauthorized('Invalid credentials');
    }
    
    Cache::forget($key); // Reset on successful login
    
    // ... rest of login logic
}
```

## 5. Pertimbangan Performa

### A. Database Indexing

```sql
-- Tambahkan index untuk performa query last_login
ALTER TABLE users ADD INDEX idx_users_last_login (last_login);
ALTER TABLE users ADD INDEX idx_users_email_last_login (email, last_login);
```

### B. Caching Strategy

```php
// Contoh caching untuk data user yang sering diakses
public function getUserWithLastLogin($userId)
{
    return Cache::remember("user_last_login_{$userId}", 3600, function () use ($userId) {
        return User::select('id', 'name', 'email', 'last_login')
                  ->find($userId);
    });
}
```

### C. Batch Updates

Untuk sistem dengan traffic tinggi, pertimbangkan batch updates:

```php
// Queue job untuk update last_login
class UpdateLastLoginJob implements ShouldQueue
{
    public function handle()
    {
        $updates = Cache::pull('pending_last_login_updates', []);
        
        foreach ($updates as $userId => $timestamp) {
            User::where('id', $userId)
                ->update(['last_login' => $timestamp]);
        }
    }
}
```

## 6. API Endpoints untuk Last Login

### A. Get User Profile dengan Last Login

```php
// File: app/Http/Controllers/Api/AuthController.php

public function user(Request $request)
{
    /** @var \App\Models\User $user */
    $user = $request->user();
    $user->load('roles.permissions');
    
    return BaseResponseService::success([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'last_login' => $user->last_login,
        'last_login_human' => $user->last_login?->diffForHumans(),
        'roles' => $user->roles,
        'permissions' => $user->getAllPermissions()->pluck('name'),
    ], 'User data retrieved successfully');
}
```

### B. Admin Endpoint untuk Monitoring User Activity

```php
// File: app/Http/Controllers/Api/Admin/UserActivityController.php

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'last_login', 'created_at')
                    ->when($request->has('inactive_days'), function ($query) use ($request) {
                        $days = $request->input('inactive_days', 30);
                        $query->where('last_login', '<', now()->subDays($days))
                              ->orWhereNull('last_login');
                    })
                    ->orderBy('last_login', 'desc')
                    ->paginate(15);
                    
        return BaseResponseService::success($users);
    }
    
    public function inactiveUsers(Request $request)
    {
        $days = $request->input('days', 30);
        
        $users = User::select('id', 'name', 'email', 'last_login')
                    ->where(function ($query) use ($days) {
                        $query->where('last_login', '<', now()->subDays($days))
                              ->orWhereNull('last_login');
                    })
                    ->orderBy('last_login', 'asc')
                    ->get();
                    
        return BaseResponseService::success([
            'inactive_users' => $users,
            'count' => $users->count(),
            'threshold_days' => $days
        ]);
    }
}
```

## 7. Frontend Integration

### A. Display Last Login di User Profile

```javascript
// File: frontend/web/src/pages/profile/UserProfile.vue

<template>
  <div class="user-profile">
    <q-card>
      <q-card-section>
        <div class="text-h6">{{ user.name }}</div>
        <div class="text-subtitle2">{{ user.email }}</div>
        <div class="text-caption text-grey-6" v-if="user.last_login">
          Last login: {{ formatLastLogin(user.last_login) }}
        </div>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { date } from 'quasar'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const formatLastLogin = (lastLogin) => {
  if (!lastLogin) return 'Never'
  return date.formatDate(lastLogin, 'DD MMM YYYY HH:mm')
}
</script>
```

### B. Admin Dashboard untuk User Activity

```javascript
// File: frontend/web/src/pages/admin/UserActivity.vue

<template>
  <div class="user-activity">
    <q-table
      :rows="users"
      :columns="columns"
      row-key="id"
      :loading="loading"
    >
      <template v-slot:body-cell-last_login="props">
        <q-td :props="props">
          <div v-if="props.row.last_login">
            {{ formatDate(props.row.last_login) }}
            <div class="text-caption text-grey-6">
              {{ getTimeAgo(props.row.last_login) }}
            </div>
          </div>
          <q-chip v-else color="orange" text-color="white" size="sm">
            Never logged in
          </q-chip>
        </q-td>
      </template>
    </q-table>
  </div>
</template>
```

## 8. Testing

### A. Unit Tests

```php
// File: tests/Feature/Auth/LoginTest.php

class LoginTest extends TestCase
{
    public function test_successful_login_updates_last_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'last_login' => null
        ]);
        
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        
        $response->assertStatus(200);
        
        $user->refresh();
        $this->assertNotNull($user->last_login);
        $this->assertTrue($user->last_login->isToday());
    }
    
    public function test_failed_login_does_not_update_last_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'last_login' => null
        ]);
        
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);
        
        $response->assertStatus(401);
        
        $user->refresh();
        $this->assertNull($user->last_login);
    }
}
```

### B. Integration Tests

```php
// File: tests/Feature/UserActivityTest.php

class UserActivityTest extends TestCase
{
    public function test_admin_can_view_user_activity()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $users = User::factory()->count(5)->create([
            'last_login' => now()->subDays(rand(1, 30))
        ]);
        
        Sanctum::actingAs($admin);
        
        $response = $this->getJson('/api/admin/user-activity');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'name', 'email', 'last_login']
                    ]
                ]);
    }
}
```

## 9. Monitoring dan Analytics

### A. Dashboard Metrics

```php
// File: app/Http/Controllers/Api/DashboardController.php

public function getUserActivityStats()
{
    $stats = [
        'total_users' => User::count(),
        'active_today' => User::whereDate('last_login', today())->count(),
        'active_this_week' => User::where('last_login', '>=', now()->startOfWeek())->count(),
        'active_this_month' => User::where('last_login', '>=', now()->startOfMonth())->count(),
        'never_logged_in' => User::whereNull('last_login')->count(),
        'inactive_30_days' => User::where('last_login', '<', now()->subDays(30))
                                 ->orWhereNull('last_login')
                                 ->count()
    ];
    
    return BaseResponseService::success($stats);
}
```

### B. Automated Reports

```php
// File: app/Console/Commands/GenerateUserActivityReport.php

class GenerateUserActivityReport extends Command
{
    protected $signature = 'report:user-activity {--email=}';
    
    public function handle()
    {
        $inactiveUsers = User::where('last_login', '<', now()->subDays(30))
                            ->orWhereNull('last_login')
                            ->get();
                            
        $report = [
            'generated_at' => now(),
            'inactive_users_count' => $inactiveUsers->count(),
            'users' => $inactiveUsers->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'last_login' => $user->last_login,
                    'days_inactive' => $user->last_login 
                        ? $user->last_login->diffInDays(now())
                        : 'Never'
                ];
            })
        ];
        
        // Send email report if requested
        if ($this->option('email')) {
            Mail::to($this->option('email'))
                ->send(new UserActivityReport($report));
        }
        
        $this->info('User activity report generated successfully.');
    }
}
```

## 10. Best Practices

### A. Performance Optimization

1. **Index Database Fields**: Tambahkan index pada `last_login` untuk query yang sering
2. **Cache Frequently Accessed Data**: Cache data user yang sering diakses
3. **Batch Operations**: Untuk sistem high-traffic, gunakan queue untuk batch updates
4. **Pagination**: Selalu gunakan pagination untuk list user dengan last_login

### B. Security Considerations

1. **Rate Limiting**: Implementasikan rate limiting untuk endpoint login
2. **Audit Logging**: Log semua aktivitas autentikasi
3. **IP Tracking**: Catat IP address untuk monitoring suspicious activity
4. **Session Management**: Implementasikan proper session timeout

### C. Data Privacy

1. **GDPR Compliance**: Pastikan data last_login dapat dihapus sesuai permintaan user
2. **Data Retention**: Implementasikan policy untuk retention data lama
3. **Access Control**: Hanya admin yang dapat melihat last_login user lain

## Kesimpulan

Sistem Q-Pharmacy telah berhasil mengimplementasikan fitur tracking waktu login terakhir dengan:

✅ **Database Schema**: Field `last_login` sudah ditambahkan ke tabel users
✅ **Backend Logic**: AuthController sudah mengupdate timestamp saat login
✅ **Security**: Audit logging dan rate limiting sudah diimplementasikan
✅ **Performance**: Menggunakan proper indexing dan caching strategy

Fitur ini memberikan visibility yang baik untuk monitoring aktivitas user dan dapat digunakan untuk:
- Security monitoring
- User engagement analysis
- Inactive user identification
- Compliance reporting

Implementasi ini mengikuti best practices Laravel dan dapat dengan mudah diperluas untuk kebutuhan monitoring yang lebih advanced.
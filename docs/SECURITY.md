# Security Policy Q-Pharmacy

## Overview

Dokumen ini menjelaskan kebijakan keamanan komprehensif untuk sistem Q-Pharmacy, mencakup mekanisme enkripsi, audit logging, prosedur pelaporan kerentanan, dan best practices keamanan. Keamanan adalah prioritas utama mengingat sistem ini menangani data sensitif apotek dan transaksi keuangan.

**Security Framework**: OWASP Top 10, ISO 27001, NIST Cybersecurity Framework  
**Compliance**: Peraturan Menteri Kesehatan RI tentang Apotek  
**Last Updated**: 20 September 2025

## Security Architecture

### Defense in Depth Strategy

```
┌─────────────────────────────────────────────────────────────┐
│                    User Interface Layer                     │
│  • Input Validation • XSS Protection • CSRF Protection     │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                   Application Layer                         │
│  • Authentication • Authorization • Session Management      │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                     API Gateway                             │
│  • Rate Limiting • API Security • Request Filtering        │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                   Business Logic Layer                      │
│  • Data Validation • Business Rules • Audit Logging        │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                    Data Access Layer                        │
│  • SQL Injection Prevention • Data Encryption • Access Control │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                     Database Layer                          │
│  • Encryption at Rest • Database Firewall • Backup Security │
└─────────────────────────────────────────────────────────────┘
```

## Authentication & Authorization

### Authentication Mechanisms

#### 1. Multi-Factor Authentication (MFA)

**Implementation:**
```php
// Laravel Sanctum with MFA
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if MFA is required
            if ($user->mfa_enabled) {
                return $this->sendMFAChallenge($user);
            }
            
            return $this->generateTokens($user);
        }
        
        // Log failed attempt
        $this->logFailedAttempt($request->ip(), $credentials['email']);
        
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
    
    private function sendMFAChallenge(User $user)
    {
        $code = random_int(100000, 999999);
        
        Cache::put("mfa_code_{$user->id}", $code, 300); // 5 minutes
        
        // Send via SMS or Email
        Mail::to($user->email)->send(new MFACodeMail($code));
        
        return response()->json([
            'message' => 'MFA code sent',
            'requires_mfa' => true,
            'user_id' => $user->id
        ]);
    }
}
```

#### 2. Password Policy

**Requirements:**
- Minimum 8 characters
- At least 1 uppercase letter
- At least 1 lowercase letter
- At least 1 number
- At least 1 special character
- Cannot reuse last 5 passwords
- Must change every 90 days

**Implementation:**
```php
class PasswordValidationRule implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
    }
    
    public function message()
    {
        return 'Password must contain at least 8 characters with uppercase, lowercase, number, and special character.';
    }
}
```

#### 3. Session Management

**Configuration:**
```php
// config/session.php
return [
    'lifetime' => 480, // 8 hours
    'expire_on_close' => true,
    'encrypt' => true,
    'files' => storage_path('framework/sessions'),
    'connection' => null,
    'table' => 'sessions',
    'store' => null,
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'q_potek_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null),
    'secure' => env('SESSION_SECURE_COOKIE', true),
    'http_only' => true,
    'same_site' => 'strict',
];
```

### Role-Based Access Control (RBAC)

#### Permission Matrix

| Resource | Admin | Supervisor | Kasir | View Only |
|----------|-------|------------|-------|----------|
| Users | CRUD | R | - | - |
| Products | CRUD | RU | R | R |
| Categories | CRUD | RU | R | R |
| Suppliers | CRUD | RU | R | R |
| Sales | CRUD | R | CRU | R |
| Reports | CRUD | R | R | R |
| Settings | CRUD | - | - | - |
| Audit Logs | R | R | - | - |

**Legend:** C=Create, R=Read, U=Update, D=Delete

#### Implementation

```php
// Spatie Laravel Permission
class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $supervisor = Role::create(['name' => 'supervisor']);
        $kasir = Role::create(['name' => 'kasir']);
        $viewer = Role::create(['name' => 'viewer']);
        
        // Create permissions
        $permissions = [
            'users.create', 'users.read', 'users.update', 'users.delete',
            'products.create', 'products.read', 'products.update', 'products.delete',
            'sales.create', 'sales.read', 'sales.update', 'sales.delete',
            'reports.read', 'settings.manage', 'audit.read'
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        $supervisor->givePermissionTo([
            'products.read', 'products.update',
            'sales.read', 'reports.read', 'audit.read'
        ]);
        $kasir->givePermissionTo([
            'products.read', 'sales.create', 'sales.read', 'sales.update'
        ]);
        $viewer->givePermissionTo(['products.read', 'sales.read', 'reports.read']);
    }
}
```

## Data Encryption

### Encryption at Rest

#### Database Encryption

```php
// Encrypted Model Attributes
class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'phone', 'address'
    ];
    
    protected $encrypted = [
        'phone', 'address'
    ];
    
    // Automatic encryption/decryption
    public function getPhoneAttribute($value)
    {
        return decrypt($value);
    }
    
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = encrypt($value);
    }
}
```

#### File Encryption

```php
class FileEncryptionService
{
    public function encryptFile($filePath, $key = null)
    {
        $key = $key ?: config('app.key');
        $data = file_get_contents($filePath);
        $encrypted = encrypt($data);
        
        file_put_contents($filePath . '.enc', $encrypted);
        unlink($filePath); // Remove original
        
        return $filePath . '.enc';
    }
    
    public function decryptFile($encryptedPath, $key = null)
    {
        $key = $key ?: config('app.key');
        $encrypted = file_get_contents($encryptedPath);
        $decrypted = decrypt($encrypted);
        
        $originalPath = str_replace('.enc', '', $encryptedPath);
        file_put_contents($originalPath, $decrypted);
        
        return $originalPath;
    }
}
```

### Encryption in Transit

#### SSL/TLS Configuration

```nginx
# Nginx SSL Configuration
server {
    listen 443 ssl http2;
    server_name q-pharmacy.com;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    # SSL Security
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # Security Headers
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';";
}
```

#### API Encryption

```php
class APIEncryptionMiddleware
{
    public function handle($request, Closure $next)
    {
        // Decrypt incoming request
        if ($request->hasHeader('X-Encrypted')) {
            $encryptedData = $request->getContent();
            $decryptedData = decrypt($encryptedData);
            $request->merge(json_decode($decryptedData, true));
        }
        
        $response = $next($request);
        
        // Encrypt outgoing response
        if ($request->hasHeader('X-Encrypt-Response')) {
            $content = $response->getContent();
            $encryptedContent = encrypt($content);
            $response->setContent($encryptedContent);
            $response->headers->set('X-Encrypted', 'true');
        }
        
        return $response;
    }
}
```

## Input Validation & Sanitization

### SQL Injection Prevention

```php
class ProductRepository
{
    // Use Eloquent ORM (automatically prevents SQL injection)
    public function searchProducts($query)
    {
        return Product::where('name', 'LIKE', "%{$query}%")
                     ->orWhere('barcode', 'LIKE', "%{$query}%")
                     ->get();
    }
    
    // For raw queries, use parameter binding
    public function getProductsByCategory($categoryId)
    {
        return DB::select(
            'SELECT * FROM products WHERE category_id = ? AND is_active = ?',
            [$categoryId, true]
        );
    }
}
```

### XSS Prevention

```php
class XSSProtectionMiddleware
{
    public function handle($request, Closure $next)
    {
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        });
        
        $request->merge($input);
        
        return $next($request);
    }
}
```

### CSRF Protection

```php
// Automatic CSRF protection for all forms
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'api/webhook/*', // Exclude webhook endpoints
    ];
}
```

```javascript
// Frontend CSRF token handling
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
```

## Audit Logging

### Comprehensive Audit Trail

```php
class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'old_values', 'new_values', 'ip_address',
        'user_agent', 'created_at'
    ];
    
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}

class AuditLogger
{
    public static function log($action, $model = null, $oldValues = null, $newValues = null)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
```

### Model Event Logging

```php
class Product extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($model) {
            AuditLogger::log('created', $model, null, $model->toArray());
        });
        
        static::updated(function ($model) {
            AuditLogger::log('updated', $model, $model->getOriginal(), $model->getChanges());
        });
        
        static::deleted(function ($model) {
            AuditLogger::log('deleted', $model, $model->toArray(), null);
        });
    }
}
```

### Security Event Logging

```php
class SecurityLogger
{
    public static function logFailedLogin($email, $ip)
    {
        Log::warning('Failed login attempt', [
            'email' => $email,
            'ip' => $ip,
            'timestamp' => now(),
            'user_agent' => request()->userAgent()
        ]);
        
        // Check for brute force
        $attempts = Cache::get("login_attempts_{$ip}", 0);
        Cache::put("login_attempts_{$ip}", $attempts + 1, 3600);
        
        if ($attempts >= 5) {
            static::logSecurityIncident('brute_force_detected', $ip);
        }
    }
    
    public static function logSecurityIncident($type, $details)
    {
        Log::critical('Security incident detected', [
            'type' => $type,
            'details' => $details,
            'timestamp' => now(),
            'server' => gethostname()
        ]);
        
        // Send alert to security team
        Mail::to(config('security.alert_email'))
            ->send(new SecurityIncidentAlert($type, $details));
    }
}
```

## API Security

### Rate Limiting

```php
// config/rate-limiting.php
class RateLimitingConfig
{
    public static function configure()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
        
        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()->id);
        });
    }
}
```

### API Authentication

```php
class APIAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['error' => 'Token required'], 401);
        }
        
        try {
            $user = Sanctum::findTokenByPlainTextToken($token)?->tokenable;
            
            if (!$user) {
                return response()->json(['error' => 'Invalid token'], 401);
            }
            
            // Check token expiry
            if ($user->currentAccessToken()->created_at->addHours(8)->isPast()) {
                return response()->json(['error' => 'Token expired'], 401);
            }
            
            Auth::setUser($user);
            
        } catch (Exception $e) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }
        
        return $next($request);
    }
}
```

### API Input Validation

```php
class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('products.create');
    }
    
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-\.]+$/',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'unit_id' => 'required|exists:units,id',
            'barcode' => 'required|string|unique:products,barcode|regex:/^[0-9]{13}$/',
            'is_prescription' => 'boolean',
        ];
    }
    
    public function messages()
    {
        return [
            'name.regex' => 'Product name contains invalid characters',
            'barcode.regex' => 'Barcode must be 13 digits',
        ];
    }
}
```

## Data Privacy & Protection

### Personal Data Handling

```php
class PersonalDataService
{
    public function anonymizeUser($userId)
    {
        $user = User::find($userId);
        
        if ($user) {
            $user->update([
                'name' => 'Anonymized User',
                'email' => 'anonymized_' . $userId . '@deleted.local',
                'phone' => null,
                'address' => null,
                'deleted_at' => now()
            ]);
            
            AuditLogger::log('user_anonymized', $user);
        }
    }
    
    public function exportUserData($userId)
    {
        $user = User::with(['sales', 'auditLogs'])->find($userId);
        
        $data = [
            'personal_info' => $user->only(['name', 'email', 'phone', 'address']),
            'account_info' => $user->only(['created_at', 'last_login_at']),
            'sales_history' => $user->sales->toArray(),
            'audit_trail' => $user->auditLogs->toArray()
        ];
        
        return $data;
    }
}
```

### Data Retention Policy

```php
class DataRetentionService
{
    public function cleanupOldData()
    {
        // Delete audit logs older than 7 years
        AuditLog::where('created_at', '<', now()->subYears(7))->delete();
        
        // Archive sales data older than 5 years
        Sale::where('created_at', '<', now()->subYears(5))
            ->chunk(1000, function ($sales) {
                foreach ($sales as $sale) {
                    $this->archiveSale($sale);
                    $sale->delete();
                }
            });
        
        // Delete temporary files older than 30 days
        $tempFiles = Storage::files('temp');
        foreach ($tempFiles as $file) {
            if (Storage::lastModified($file) < now()->subDays(30)->timestamp) {
                Storage::delete($file);
            }
        }
    }
}
```

## Backup & Recovery Security

### Secure Backup Strategy

```bash
#!/bin/bash
# Secure backup script

BACKUP_DIR="/secure/backups"
DATE=$(date +%Y%m%d_%H%M%S)
ENCRYPTION_KEY="/secure/keys/backup.key"

# Create database backup
mysqldump --single-transaction --routines --triggers q_potek > "$BACKUP_DIR/db_$DATE.sql"

# Encrypt backup
gpg --cipher-algo AES256 --compress-algo 1 --s2k-mode 3 --s2k-digest-algo SHA512 --s2k-count 65536 --symmetric --output "$BACKUP_DIR/db_$DATE.sql.gpg" "$BACKUP_DIR/db_$DATE.sql"

# Remove unencrypted backup
rm "$BACKUP_DIR/db_$DATE.sql"

# Upload to secure cloud storage
aws s3 cp "$BACKUP_DIR/db_$DATE.sql.gpg" s3://q-pharmacy-backups/database/ --sse AES256

# Verify backup integrity
sha256sum "$BACKUP_DIR/db_$DATE.sql.gpg" > "$BACKUP_DIR/db_$DATE.sql.gpg.sha256"

# Clean old backups (keep 30 days)
find "$BACKUP_DIR" -name "db_*.sql.gpg" -mtime +30 -delete
```

### Backup Verification

```php
class BackupVerificationService
{
    public function verifyBackup($backupPath)
    {
        // Check file integrity
        $checksumFile = $backupPath . '.sha256';
        if (!file_exists($checksumFile)) {
            throw new Exception('Checksum file not found');
        }
        
        $expectedChecksum = trim(file_get_contents($checksumFile));
        $actualChecksum = hash_file('sha256', $backupPath);
        
        if ($expectedChecksum !== $actualChecksum) {
            throw new Exception('Backup integrity check failed');
        }
        
        // Test restore capability
        $this->testRestore($backupPath);
        
        return true;
    }
    
    private function testRestore($backupPath)
    {
        // Create temporary database for testing
        $testDb = 'q_potek_test_' . time();
        
        try {
            DB::statement("CREATE DATABASE {$testDb}");
            
            // Decrypt and restore backup
            $decryptedPath = $this->decryptBackup($backupPath);
            $this->restoreDatabase($decryptedPath, $testDb);
            
            // Verify critical tables exist
            $tables = ['users', 'products', 'sales', 'audit_logs'];
            foreach ($tables as $table) {
                $count = DB::connection('test')->table($table)->count();
                if ($count === 0) {
                    throw new Exception("Table {$table} is empty after restore");
                }
            }
            
        } finally {
            // Cleanup
            DB::statement("DROP DATABASE IF EXISTS {$testDb}");
            if (isset($decryptedPath)) {
                unlink($decryptedPath);
            }
        }
    }
}
```

## Incident Response

### Security Incident Classification

| Level | Description | Response Time | Escalation |
|-------|-------------|---------------|------------|
| Critical | Data breach, system compromise | 15 minutes | CEO, CTO, Legal |
| High | Unauthorized access, malware | 1 hour | CTO, Security Team |
| Medium | Failed security controls | 4 hours | Security Team |
| Low | Policy violations | 24 hours | IT Manager |

### Incident Response Procedure

```php
class IncidentResponseService
{
    public function handleSecurityIncident($type, $severity, $details)
    {
        $incident = SecurityIncident::create([
            'type' => $type,
            'severity' => $severity,
            'details' => $details,
            'status' => 'open',
            'detected_at' => now(),
            'detected_by' => auth()->id()
        ]);
        
        // Immediate containment
        $this->containIncident($incident);
        
        // Notification
        $this->notifySecurityTeam($incident);
        
        // Evidence collection
        $this->collectEvidence($incident);
        
        // Start investigation
        $this->initiateInvestigation($incident);
        
        return $incident;
    }
    
    private function containIncident($incident)
    {
        switch ($incident->type) {
            case 'brute_force':
                $this->blockSuspiciousIPs($incident->details['ip_addresses']);
                break;
                
            case 'data_breach':
                $this->enableEmergencyMode();
                $this->notifyDataProtectionOfficer($incident);
                break;
                
            case 'malware':
                $this->isolateAffectedSystems($incident->details['systems']);
                break;
        }
    }
}
```

## Vulnerability Management

### Vulnerability Reporting

**Security Contact**: security@q-pharmacy.com  
**PGP Key**: Available at https://q-pharmacy.com/.well-known/security.txt

#### Responsible Disclosure Policy

1. **Report vulnerabilities** to security@q-pharmacy.com
2. **Provide detailed information** including:
   - Vulnerability description
   - Steps to reproduce
   - Potential impact
   - Suggested remediation
3. **Allow reasonable time** for investigation and remediation
4. **Do not exploit** the vulnerability beyond proof of concept
5. **Do not access** or modify user data

#### Vulnerability Assessment Schedule

| Assessment Type | Frequency | Scope |
|----------------|-----------|-------|
| Automated Scanning | Daily | All systems |
| Manual Testing | Monthly | Critical components |
| Penetration Testing | Quarterly | Full application |
| Code Review | Per release | All code changes |
| Dependency Audit | Weekly | Third-party packages |

### Security Testing

```php
// Automated security tests
class SecurityTest extends TestCase
{
    public function test_sql_injection_protection()
    {
        $maliciousInput = "'; DROP TABLE users; --";
        
        $response = $this->postJson('/api/products/search', [
            'query' => $maliciousInput
        ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => 1]); // Table should still exist
    }
    
    public function test_xss_protection()
    {
        $xssPayload = '<script>alert("XSS")</script>';
        
        $response = $this->postJson('/api/products', [
            'name' => $xssPayload,
            'category_id' => 1,
            'supplier_id' => 1,
            'unit_id' => 1
        ]);
        
        $response->assertStatus(422); // Should be rejected
    }
    
    public function test_authentication_required()
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(401);
    }
    
    public function test_authorization_enforced()
    {
        $kasir = User::factory()->create();
        $kasir->assignRole('kasir');
        
        $response = $this->actingAs($kasir)
                        ->getJson('/api/users');
                        
        $response->assertStatus(403);
    }
}
```

## Compliance & Regulations

### Healthcare Data Protection

**Applicable Regulations:**
- Peraturan Menteri Kesehatan RI No. 9 Tahun 2017
- UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi
- ISO 27001:2013 Information Security Management

**Compliance Requirements:**
- Patient data encryption
- Access logging and monitoring
- Data retention policies
- Incident reporting procedures
- Regular security assessments

### Audit Compliance

```php
class ComplianceAuditService
{
    public function generateComplianceReport($startDate, $endDate)
    {
        return [
            'access_logs' => $this->getAccessLogs($startDate, $endDate),
            'data_changes' => $this->getDataChanges($startDate, $endDate),
            'security_incidents' => $this->getSecurityIncidents($startDate, $endDate),
            'user_activities' => $this->getUserActivities($startDate, $endDate),
            'system_changes' => $this->getSystemChanges($startDate, $endDate),
            'backup_verifications' => $this->getBackupVerifications($startDate, $endDate)
        ];
    }
    
    public function validateCompliance()
    {
        $issues = [];
        
        // Check password policy compliance
        $weakPasswords = User::whereRaw('password_updated_at < ?', [now()->subDays(90)])->count();
        if ($weakPasswords > 0) {
            $issues[] = "$weakPasswords users have not updated passwords in 90 days";
        }
        
        // Check MFA compliance
        $noMFA = User::where('mfa_enabled', false)->where('role', 'admin')->count();
        if ($noMFA > 0) {
            $issues[] = "$noMFA admin users do not have MFA enabled";
        }
        
        // Check backup compliance
        $lastBackup = Backup::latest()->first();
        if (!$lastBackup || $lastBackup->created_at->diffInHours() > 24) {
            $issues[] = 'No backup completed in the last 24 hours';
        }
        
        return $issues;
    }
}
```

## Security Monitoring

### Real-time Monitoring

```php
class SecurityMonitoringService
{
    public function monitorSuspiciousActivity()
    {
        // Monitor failed login attempts
        $this->checkFailedLogins();
        
        // Monitor unusual access patterns
        $this->checkAccessPatterns();
        
        // Monitor system resource usage
        $this->checkResourceUsage();
        
        // Monitor database queries
        $this->checkDatabaseActivity();
    }
    
    private function checkFailedLogins()
    {
        $threshold = 10;
        $timeWindow = 15; // minutes
        
        $failedAttempts = AuditLog::where('action', 'failed_login')
                                 ->where('created_at', '>=', now()->subMinutes($timeWindow))
                                 ->count();
        
        if ($failedAttempts >= $threshold) {
            $this->triggerAlert('high_failed_login_rate', [
                'attempts' => $failedAttempts,
                'time_window' => $timeWindow
            ]);
        }
    }
    
    private function checkAccessPatterns()
    {
        // Detect access from unusual locations
        $users = User::with('auditLogs')->get();
        
        foreach ($users as $user) {
            $recentIPs = $user->auditLogs()
                             ->where('created_at', '>=', now()->subDays(7))
                             ->pluck('ip_address')
                             ->unique();
            
            if ($recentIPs->count() > 5) {
                $this->triggerAlert('unusual_access_pattern', [
                    'user_id' => $user->id,
                    'ip_addresses' => $recentIPs->toArray()
                ]);
            }
        }
    }
}
```

### Security Metrics Dashboard

```javascript
// Vue.js Security Dashboard Component
export default {
  name: 'SecurityDashboard',
  data() {
    return {
      securityMetrics: {
        failedLogins: 0,
        activeThreats: 0,
        vulnerabilities: 0,
        complianceScore: 0
      },
      recentIncidents: [],
      threatLevel: 'low'
    }
  },
  
  async mounted() {
    await this.loadSecurityMetrics()
    this.startRealTimeMonitoring()
  },
  
  methods: {
    async loadSecurityMetrics() {
      try {
        const response = await this.$api.get('/security/metrics')
        this.securityMetrics = response.data
        this.updateThreatLevel()
      } catch (error) {
        this.$q.notify({
          type: 'negative',
          message: 'Failed to load security metrics'
        })
      }
    },
    
    startRealTimeMonitoring() {
      this.$echo.channel('security-alerts')
        .listen('SecurityAlert', (event) => {
          this.handleSecurityAlert(event)
        })
    },
    
    handleSecurityAlert(alert) {
      this.$q.notify({
        type: 'negative',
        message: `Security Alert: ${alert.message}`,
        timeout: 0,
        actions: [
          {
            label: 'View Details',
            color: 'white',
            handler: () => this.viewAlertDetails(alert)
          }
        ]
      })
    }
  }
}
```

## Security Training & Awareness

### Security Training Program

**Mandatory Training Topics:**
1. Password security and MFA
2. Phishing and social engineering
3. Data handling and privacy
4. Incident reporting procedures
5. Secure coding practices (for developers)

**Training Schedule:**
- New employee orientation: Security basics
- Quarterly: Security updates and threats
- Annual: Comprehensive security training
- Ad-hoc: Incident-specific training

### Security Policies

**Acceptable Use Policy:**
- Use strong, unique passwords
- Enable MFA on all accounts
- Report suspicious activities immediately
- Do not share login credentials
- Keep software updated
- Use approved applications only

**Data Handling Policy:**
- Encrypt sensitive data
- Use secure communication channels
- Follow data retention guidelines
- Obtain consent for data collection
- Report data breaches within 24 hours

## Emergency Procedures

### Security Incident Response Plan

**Phase 1: Detection and Analysis (0-15 minutes)**
1. Identify and verify the incident
2. Classify severity level
3. Activate incident response team
4. Begin evidence collection

**Phase 2: Containment (15-60 minutes)**
1. Isolate affected systems
2. Prevent further damage
3. Preserve evidence
4. Notify stakeholders

**Phase 3: Eradication and Recovery (1-24 hours)**
1. Remove threat from environment
2. Patch vulnerabilities
3. Restore systems from clean backups
4. Implement additional monitoring

**Phase 4: Post-Incident Activities (24-72 hours)**
1. Document lessons learned
2. Update security procedures
3. Conduct post-incident review
4. Implement preventive measures

### Emergency Contacts

| Role | Contact | Phone | Email |
|------|---------|-------|-------|
| Security Officer | John Doe | +62-xxx-xxx-xxxx | security@q-pharmacy.com |
| IT Manager | Jane Smith | +62-xxx-xxx-xxxx | it@q-pharmacy.com |
| Legal Counsel | Bob Johnson | +62-xxx-xxx-xxxx | legal@q-pharmacy.com |
| CEO | Alice Brown | +62-xxx-xxx-xxxx | ceo@q-pharmacy.com |

---

**Document Control:**
- **Version**: 1.0
- **Classification**: Confidential
- **Owner**: Security Team
- **Approved By**: CTO
- **Next Review**: 15 December 2025
- **Distribution**: Management Team, Development Team
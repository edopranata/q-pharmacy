# Q-Pharmacy - Android Mobile Application

## Daftar Isi
- [Tentang Proyek](#tentang-proyek)
- [Status Pengembangan](#status-pengembangan)
- [Teknologi Stack](#teknologi-stack)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Arsitektur Aplikasi](#arsitektur-aplikasi)
- [Fitur Utama](#fitur-utama)
- [Setup Development](#setup-development)
- [Struktur Proyek](#struktur-proyek)
- [API Integration](#api-integration)
- [Authentication](#authentication)
- [Offline Support](#offline-support)
- [Testing Strategy](#testing-strategy)
- [Build & Deployment](#build--deployment)
- [Performance](#performance)
- [Security](#security)
- [Roadmap](#roadmap)
- [Resources](#resources)

## Tentang Proyek

Aplikasi mobile Android untuk Q-Pharmacy yang akan menyediakan akses mobile ke sistem manajemen apotek. Aplikasi ini dirancang untuk memberikan pengalaman pengguna yang optimal pada perangkat mobile dengan fokus pada kemudahan penggunaan dan performa yang baik.

### Visi Aplikasi
- **Mobile-First Design** - Interface yang dioptimalkan untuk perangkat mobile
- **Offline Capability** - Fungsionalitas dasar tetap berjalan tanpa koneksi internet
- **Real-time Sync** - Sinkronisasi data real-time dengan backend
- **Responsive Performance** - Performa yang cepat dan responsif
- **Secure Access** - Keamanan data dan akses yang terjamin

## Status Pengembangan

> **⚠️ STATUS: BELUM DIRILIS (UNRELEASED)**
> 
> Aplikasi Android Q-Pharmacy saat ini masih dalam tahap perencanaan dan akan dikembangkan pada fase berikutnya dari proyek ini.

### Timeline Pengembangan
- **Q1 2025**: Perencanaan dan desain UI/UX
- **Q2 2025**: Development MVP (Minimum Viable Product)
- **Q3 2025**: Testing dan optimasi
- **Q4 2025**: Release versi 1.0

### Prioritas Fitur
1. **Authentication & User Management**
2. **Product Catalog & Search**
3. **Basic POS Functionality**
4. **Inventory Tracking**
5. **Reports & Analytics**

## Teknologi Stack

### Framework & Platform
- **Platform**: Android 7.0+ (API Level 24+)
- **Language**: Kotlin
- **Framework**: Native Android Development
- **Architecture**: MVVM (Model-View-ViewModel)

### UI/UX Framework
- **UI Toolkit**: Jetpack Compose
- **Material Design**: Material Design 3
- **Navigation**: Navigation Component
- **Animations**: Compose Animations

### Data & Storage
- **Local Database**: Room Database
- **Preferences**: DataStore
- **Caching**: OkHttp Cache
- **File Storage**: Android Storage Access Framework

### Networking & API
- **HTTP Client**: Retrofit 2 + OkHttp
- **JSON Parsing**: Moshi
- **Image Loading**: Coil
- **WebSocket**: OkHttp WebSocket

### Architecture Components
- **Lifecycle**: Lifecycle-aware Components
- **ViewModel**: Android ViewModel
- **LiveData/Flow**: Reactive Data Streams
- **Dependency Injection**: Hilt (Dagger)

### Development Tools
- **IDE**: Android Studio
- **Build System**: Gradle with Kotlin DSL
- **Version Control**: Git
- **CI/CD**: GitHub Actions

### Testing
- **Unit Testing**: JUnit 5 + Mockk
- **UI Testing**: Espresso + Compose Testing
- **Integration Testing**: AndroidX Test

## Persyaratan Sistem

### Minimum Requirements
- **Android Version**: 7.0 (API Level 24)
- **RAM**: 2GB
- **Storage**: 100MB free space
- **Network**: 3G/WiFi connection

### Recommended
- **Android Version**: 10.0+ (API Level 29+)
- **RAM**: 4GB+
- **Storage**: 500MB free space
- **Network**: 4G/WiFi connection
- **Camera**: For barcode scanning

### Permissions
```xml
<!-- Network access -->
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />

<!-- Camera for barcode scanning -->
<uses-permission android:name="android.permission.CAMERA" />

<!-- Storage for offline data -->
<uses-permission android:name="android.permission.WRITE_EXTERNAL_STORAGE" />
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />

<!-- Notifications -->
<uses-permission android:name="android.permission.POST_NOTIFICATIONS" />
```

## Arsitektur Aplikasi

### MVVM Architecture
```
UI Layer (Compose)
    ↓
ViewModel Layer
    ↓
Repository Layer
    ↓
Data Sources (Remote API + Local Database)
```

### Module Structure
```
app/
├── presentation/     # UI Layer (Activities, Fragments, Composables)
├── domain/          # Business Logic (Use Cases, Entities)
├── data/            # Data Layer (Repositories, Data Sources)
├── di/              # Dependency Injection
└── utils/           # Utility Classes
```

### Data Flow
1. **UI** triggers action
2. **ViewModel** processes action
3. **Use Case** executes business logic
4. **Repository** manages data sources
5. **Data Source** fetches from API/Database
6. **Result** flows back through layers

## Fitur Utama

### 1. Authentication & Security
- **Login/Logout** - Secure user authentication
- **Biometric Login** - Fingerprint/Face unlock
- **Session Management** - Auto-logout on inactivity
- **Role-based Access** - Different UI based on user role

### 2. Dashboard & Analytics
- **Sales Overview** - Daily/weekly/monthly sales
- **Inventory Status** - Stock levels and alerts
- **Quick Actions** - Fast access to common tasks
- **Notifications** - Real-time alerts and updates

### 3. Product Management
- **Product Catalog** - Browse and search products
- **Barcode Scanner** - Quick product lookup
- **Stock Management** - Update inventory levels
- **Product Details** - Comprehensive product information

### 4. Point of Sale (POS)
- **Mobile Checkout** - Process sales on mobile
- **Payment Processing** - Multiple payment methods
- **Receipt Generation** - Digital receipts
- **Transaction History** - View past transactions

### 5. Inventory Tracking
- **Stock Monitoring** - Real-time stock levels
- **Low Stock Alerts** - Automated notifications
- **Stock Movements** - Track inventory changes
- **Expiry Tracking** - Monitor product expiration

### 6. Reports & Analytics
- **Sales Reports** - Detailed sales analytics
- **Inventory Reports** - Stock analysis
- **Export Data** - PDF/Excel export
- **Visual Charts** - Graphical data representation

## Setup Development

### Prerequisites
```bash
# Install Android Studio
# Download from: https://developer.android.com/studio

# Install Java 11 or higher
# Install Android SDK (API 24+)
# Install Android Build Tools
```

### Project Setup
```bash
# Clone repository
git clone https://github.com/your-username/q-pharmacy.git
cd q-pharmacy/frontend/android

# Open in Android Studio
# File > Open > Select android folder

# Sync project with Gradle files
# Build > Make Project
```

### Environment Configuration
```kotlin
// app/src/main/java/com/qpharmacy/BuildConfig.kt
object BuildConfig {
    const val API_BASE_URL = "https://api.q-pharmacy.com/v1/"
    const val API_TIMEOUT = 30000L
    const val DATABASE_NAME = "q_pharmacy_db"
    const val SHARED_PREFS_NAME = "q_pharmacy_prefs"
}
```

### Gradle Configuration
```kotlin
// app/build.gradle.kts
android {
    compileSdk = 34
    
    defaultConfig {
        applicationId = "com.qpharmacy.android"
        minSdk = 24
        targetSdk = 34
        versionCode = 1
        versionName = "1.0.0"
    }
    
    buildFeatures {
        compose = true
    }
    
    composeOptions {
        kotlinCompilerExtensionVersion = "1.5.8"
    }
}
```

## Struktur Proyek

```
app/src/main/java/com/qpharmacy/
├── presentation/
│   ├── ui/
│   │   ├── auth/           # Authentication screens
│   │   ├── dashboard/      # Dashboard screens
│   │   ├── products/       # Product management
│   │   ├── pos/           # Point of sale
│   │   └── reports/       # Reports screens
│   ├── components/        # Reusable UI components
│   ├── theme/            # App theming
│   └── navigation/       # Navigation setup
├── domain/
│   ├── entities/         # Business entities
│   ├── usecases/        # Business use cases
│   └── repositories/    # Repository interfaces
├── data/
│   ├── repositories/    # Repository implementations
│   ├── datasources/     # Data source implementations
│   ├── local/          # Local database (Room)
│   ├── remote/         # Remote API (Retrofit)
│   └── models/         # Data models
├── di/                 # Dependency injection modules
├── utils/              # Utility classes
└── MainActivity.kt     # Main activity
```

## API Integration

### Retrofit Setup
```kotlin
// data/remote/ApiService.kt
interface ApiService {
    @GET("products")
    suspend fun getProducts(
        @Query("page") page: Int,
        @Query("per_page") perPage: Int,
        @Query("search") search: String?
    ): Response<ProductsResponse>
    
    @POST("auth/login")
    suspend fun login(
        @Body request: LoginRequest
    ): Response<AuthResponse>
    
    @GET("dashboard/stats")
    suspend fun getDashboardStats(): Response<DashboardStatsResponse>
}
```

### Repository Pattern
```kotlin
// data/repositories/ProductRepositoryImpl.kt
class ProductRepositoryImpl @Inject constructor(
    private val apiService: ApiService,
    private val productDao: ProductDao
) : ProductRepository {
    
    override suspend fun getProducts(page: Int): Flow<Resource<List<Product>>> = flow {
        emit(Resource.Loading())
        
        try {
            // Try to get from API
            val response = apiService.getProducts(page, 20, null)
            if (response.isSuccessful) {
                response.body()?.let { productsResponse ->
                    // Cache in local database
                    productDao.insertProducts(productsResponse.data.map { it.toEntity() })
                    emit(Resource.Success(productsResponse.data))
                }
            } else {
                // Fallback to local data
                val localProducts = productDao.getAllProducts()
                emit(Resource.Success(localProducts.map { it.toDomain() }))
            }
        } catch (e: Exception) {
            // Fallback to local data
            val localProducts = productDao.getAllProducts()
            emit(Resource.Error(e.message ?: "Unknown error", localProducts.map { it.toDomain() }))
        }
    }
}
```

## Authentication

### JWT Token Management
```kotlin
// data/local/TokenManager.kt
class TokenManager @Inject constructor(
    private val dataStore: DataStore<Preferences>
) {
    private val TOKEN_KEY = stringPreferencesKey("auth_token")
    
    suspend fun saveToken(token: String) {
        dataStore.edit { preferences ->
            preferences[TOKEN_KEY] = token
        }
    }
    
    fun getToken(): Flow<String?> {
        return dataStore.data.map { preferences ->
            preferences[TOKEN_KEY]
        }
    }
    
    suspend fun clearToken() {
        dataStore.edit { preferences ->
            preferences.remove(TOKEN_KEY)
        }
    }
}
```

### Biometric Authentication
```kotlin
// utils/BiometricHelper.kt
class BiometricHelper(private val context: Context) {
    
    fun isBiometricAvailable(): Boolean {
        return BiometricManager.from(context)
            .canAuthenticate(BiometricManager.Authenticators.BIOMETRIC_WEAK) == BiometricManager.BIOMETRIC_SUCCESS
    }
    
    fun authenticateWithBiometric(
        activity: FragmentActivity,
        onSuccess: () -> Unit,
        onError: (String) -> Unit
    ) {
        val biometricPrompt = BiometricPrompt(activity as androidx.fragment.app.FragmentActivity,
            ContextCompat.getMainExecutor(context),
            object : BiometricPrompt.AuthenticationCallback() {
                override fun onAuthenticationSucceeded(result: BiometricPrompt.AuthenticationResult) {
                    onSuccess()
                }
                
                override fun onAuthenticationError(errorCode: Int, errString: CharSequence) {
                    onError(errString.toString())
                }
            }
        )
        
        val promptInfo = BiometricPrompt.PromptInfo.Builder()
            .setTitle("Biometric Authentication")
            .setSubtitle("Use your fingerprint to login")
            .setNegativeButtonText("Cancel")
            .build()
            
        biometricPrompt.authenticate(promptInfo)
    }
}
```

## Offline Support

### Room Database
```kotlin
// data/local/AppDatabase.kt
@Database(
    entities = [ProductEntity::class, TransactionEntity::class],
    version = 1,
    exportSchema = false
)
@TypeConverters(Converters::class)
abstract class AppDatabase : RoomDatabase() {
    abstract fun productDao(): ProductDao
    abstract fun transactionDao(): TransactionDao
}
```

### Offline-First Strategy
```kotlin
// data/repositories/OfflineFirstRepository.kt
abstract class OfflineFirstRepository<T> {
    
    protected suspend fun <R> networkBoundResource(
        query: () -> Flow<T>,
        fetch: suspend () -> R,
        saveFetchResult: suspend (R) -> Unit,
        shouldFetch: (T?) -> Boolean = { true }
    ): Flow<Resource<T>> = flow {
        
        val data = query().first()
        val flow = if (shouldFetch(data)) {
            emit(Resource.Loading(data))
            
            try {
                val fetchedData = fetch()
                saveFetchResult(fetchedData)
                query().map { Resource.Success(it) }
            } catch (throwable: Throwable) {
                query().map { Resource.Error(throwable.message ?: "Unknown error", it) }
            }
        } else {
            query().map { Resource.Success(it) }
        }
        
        emitAll(flow)
    }
}
```

## Testing Strategy

### Unit Testing
```kotlin
// test/java/com/qpharmacy/domain/usecases/GetProductsUseCaseTest.kt
class GetProductsUseCaseTest {
    
    @MockK
    private lateinit var repository: ProductRepository
    
    private lateinit var useCase: GetProductsUseCase
    
    @Before
    fun setUp() {
        MockKAnnotations.init(this)
        useCase = GetProductsUseCase(repository)
    }
    
    @Test
    fun `when repository returns success, should return success`() = runTest {
        // Given
        val products = listOf(Product(id = 1, name = "Test Product"))
        coEvery { repository.getProducts(any()) } returns flowOf(Resource.Success(products))
        
        // When
        val result = useCase(1).first()
        
        // Then
        assertTrue(result is Resource.Success)
        assertEquals(products, result.data)
    }
}
```

### UI Testing
```kotlin
// androidTest/java/com/qpharmacy/presentation/LoginScreenTest.kt
@RunWith(AndroidJUnit4::class)
class LoginScreenTest {
    
    @get:Rule
    val composeTestRule = createComposeRule()
    
    @Test
    fun loginScreen_displaysCorrectly() {
        composeTestRule.setContent {
            LoginScreen(
                onLoginClick = { _, _ -> },
                isLoading = false
            )
        }
        
        composeTestRule
            .onNodeWithText("Email")
            .assertIsDisplayed()
            
        composeTestRule
            .onNodeWithText("Password")
            .assertIsDisplayed()
            
        composeTestRule
            .onNodeWithText("Login")
            .assertIsDisplayed()
    }
}
```

## Build & Deployment

### Build Variants
```kotlin
// app/build.gradle.kts
android {
    buildTypes {
        debug {
            isDebuggable = true
            applicationIdSuffix = ".debug"
            buildConfigField("String", "API_BASE_URL", "\"https://api-dev.q-pharmacy.com/v1/\"")
        }
        
        release {
            isMinifyEnabled = true
            proguardFiles(getDefaultProguardFile("proguard-android-optimize.txt"), "proguard-rules.pro")
            buildConfigField("String", "API_BASE_URL", "\"https://api.q-pharmacy.com/v1/\"")
        }
    }
    
    flavorDimensions += "environment"
    productFlavors {
        create("staging") {
            dimension = "environment"
            applicationIdSuffix = ".staging"
        }
        
        create("production") {
            dimension = "environment"
        }
    }
}
```

### CI/CD Pipeline
```yaml
# .github/workflows/android.yml
name: Android CI

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    
    - name: Set up JDK 11
      uses: actions/setup-java@v3
      with:
        java-version: '11'
        distribution: 'temurin'
        
    - name: Cache Gradle packages
      uses: actions/cache@v3
      with:
        path: |
          ~/.gradle/caches
          ~/.gradle/wrapper
        key: ${{ runner.os }}-gradle-${{ hashFiles('**/*.gradle*', '**/gradle-wrapper.properties') }}
        
    - name: Run tests
      run: ./gradlew test
      
    - name: Build APK
      run: ./gradlew assembleDebug
```

## Performance

### Optimization Techniques
- **Lazy Loading** - Load data only when needed
- **Image Caching** - Cache images with Coil
- **Database Indexing** - Optimize database queries
- **Memory Management** - Proper lifecycle management

### Performance Monitoring
```kotlin
// utils/PerformanceMonitor.kt
class PerformanceMonitor {
    
    fun trackScreenLoad(screenName: String) {
        val startTime = System.currentTimeMillis()
        
        // Track loading time
        Firebase.performance
            .newTrace("screen_load_$screenName")
            .start()
    }
    
    fun trackApiCall(endpoint: String, duration: Long) {
        Firebase.performance
            .newHttpMetric(endpoint, "GET")
            .apply {
                setRequestPayloadSize(0)
                setResponseContentType("application/json")
                putAttribute("duration", duration.toString())
                stop()
            }
    }
}
```

## Security

### Security Best Practices
- **Certificate Pinning** - Prevent man-in-the-middle attacks
- **Data Encryption** - Encrypt sensitive local data
- **Obfuscation** - Protect code from reverse engineering
- **Root Detection** - Detect rooted devices

### Certificate Pinning
```kotlin
// di/NetworkModule.kt
@Provides
@Singleton
fun provideOkHttpClient(): OkHttpClient {
    val certificatePinner = CertificatePinner.Builder()
        .add("api.q-pharmacy.com", "sha256/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=")
        .build()
        
    return OkHttpClient.Builder()
        .certificatePinner(certificatePinner)
        .addInterceptor(AuthInterceptor())
        .addInterceptor(HttpLoggingInterceptor().apply {
            level = if (BuildConfig.DEBUG) HttpLoggingInterceptor.Level.BODY else HttpLoggingInterceptor.Level.NONE
        })
        .build()
}
```

## Roadmap

### Phase 1: MVP (Q2 2025)
- [ ] Basic authentication
- [ ] Product catalog
- [ ] Simple POS functionality
- [ ] Basic offline support

### Phase 2: Enhanced Features (Q3 2025)
- [ ] Advanced search and filters
- [ ] Barcode scanning
- [ ] Inventory management
- [ ] Reports and analytics

### Phase 3: Advanced Features (Q4 2025)
- [ ] Biometric authentication
- [ ] Push notifications
- [ ] Advanced offline sync
- [ ] Performance optimizations

### Future Enhancements
- [ ] Tablet optimization
- [ ] Multi-language support
- [ ] Advanced analytics
- [ ] Integration with external services

## Resources

### Documentation
- [Android Developer Guide](https://developer.android.com/guide)
- [Jetpack Compose](https://developer.android.com/jetpack/compose)
- [Kotlin Documentation](https://kotlinlang.org/docs/)
- [Material Design 3](https://m3.material.io/)

### Libraries & Tools
- [Retrofit](https://square.github.io/retrofit/)
- [Room Database](https://developer.android.com/training/data-storage/room)
- [Hilt Dependency Injection](https://dagger.dev/hilt/)
- [Coil Image Loading](https://coil-kt.github.io/coil/)

### Community
- [Android Developers](https://developer.android.com/community)
- [Kotlin Community](https://kotlinlang.org/community/)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/android)

---

**Q-Pharmacy Android Team**  
Version: 0.0.0 (Planned)  
Last Updated: September 2025  
Status: **UNRELEASED - IN PLANNING PHASE**
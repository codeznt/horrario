# Telegram Authentication Middleware

This middleware provides secure authentication for Telegram Mini Apps using hash verification as specified in the official Telegram documentation.

## Components

### 1. TelegramAuthService (`app/Services/TelegramAuthService.php`)

The core service that handles:
- Hash verification using HMAC-SHA256 with bot token
- Data parsing from query string format
- Auth date validation (default: 24 hours)
- User data extraction

**Key Methods:**
- `verifyTelegramHash(array $data)` - Verifies hash authenticity
- `parseAndValidateTelegramData(string $initData)` - Parses and validates init data
- `extractUserData(array $telegramData)` - Extracts user information
- `isAuthDateValid(array $telegramData, int $maxAge = 86400)` - Validates auth timestamp

### 2. TelegramAuth Middleware (`app/Http/Middleware/TelegramAuth.php`)

HTTP middleware that:
- Extracts Telegram init data from multiple request sources
- Validates hash and auth date
- Creates or updates user records
- Authenticates users automatically
- Provides proper error responses

### 3. User Model Updates (`app/Models/User.php`)

Enhanced with:
- Telegram-specific fields (telegram_id, first_name, last_name, etc.)
- `findOrCreateFromTelegram()` method for user management
- Proper field casting and mass assignment protection

## Configuration

Add to your `.env` file:

```bash
# Required: Your Telegram Bot Token from @BotFather
# For development, you can use this mock token:
TELEGRAM_BOT_TOKEN=123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ-0123456789

# Optional: Maximum age for auth data (default: 86400 seconds = 24 hours)
TELEGRAM_AUTH_MAX_AGE=86400

# Optional: Your WebApp URL (defaults to APP_URL)
TELEGRAM_WEBAPP_URL=https://your-webapp.example.com
```

**Important**: After adding environment variables, you may need to:
1. Restart your development server (`composer run dev` or `php artisan serve`)
2. Clear config cache: `php artisan config:clear`
3. Or export manually: `export TELEGRAM_BOT_TOKEN="your_token_here"`

## Usage

### Protecting Routes

Apply the middleware to routes that require Telegram authentication:

```php
// In routes/web.php
Route::middleware(['telegram.auth'])->group(function () {
    Route::get('/app', [TelegramAppController::class, 'index']);
    Route::post('/api/data', [ApiController::class, 'store']);
});

// Or on individual routes
Route::get('/tg-profile', function () {
    return response()->json([
        'user' => auth()->user(),
        'telegram_data' => request('telegram_data'),
    ]);
})->middleware('telegram.auth');
```

### Frontend Integration with Inertia.js

This application uses **Inertia.js** for seamless frontend-backend communication. The Telegram auth system integrates automatically with Inertia's request handling.

The middleware expects Telegram init data in one of these request sources (checked in order):

1. `X-Telegram-Init-Data` header (recommended)
2. `tgWebAppData` query parameter (present on initial launches from Telegram)
3. `_telegram_auth` form parameter (for POST requests)
4. `_auth` form parameter
5. `initData` form parameter

#### Automatic Authentication Setup

The authentication is automatically configured in `resources/js/app.ts`:

```typescript
import { useTelegramAuth } from './composables/useTelegramAuth'

// Setup Telegram authentication for Inertia
const { setupTelegramAuth } = useTelegramAuth()
setupTelegramAuth()
```

This automatically adds the Telegram init data to **all Inertia requests** via the `X-Telegram-Init-Data` header.

#### Manual Usage (if needed)

You can also manually access Telegram data in your Vue components:

```vue
<script setup>
import { useTelegramAuth } from '@/composables/useTelegramAuth'

const { getTelegramUser, isTelegramContext, getTelegramInitData } = useTelegramAuth()

const user = getTelegramUser()
const isInTelegram = isTelegramContext()
const initData = getTelegramInitData()
</script>
```

#### Inertia Request Examples

All standard Inertia patterns work automatically:

```vue
<template>
  <!-- Links automatically include auth -->
  <Link href="/protected-route">Go to Protected Page</Link>
  
  <!-- Forms automatically include auth -->
  <form @submit.prevent="submit">
    <input v-model="form.data" />
    <button type="submit">Submit</button>
  </form>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'

const form = reactive({ data: '' })

const submit = () => {
  // Automatically includes Telegram auth
  router.post('/api/endpoint', form)
}
</script>
```

### Accessing User Data in Controllers

Once authenticated, you can access the user and Telegram data:

```php
public function profile(Request $request)
{
    $user = auth()->user(); // Authenticated user with Telegram fields
    $telegramData = $request->get('telegram_data'); // Raw Telegram data
    
    return response()->json([
        'user' => $user,
        'telegram_user_id' => $user->telegram_id,
        'is_premium' => $user->is_premium,
        'language' => $user->language_code,
        'auth_date' => $telegramData['auth_date'] ?? null,
    ]);
}
```

## Security Features

### Hash Verification

The service implements the official Telegram WebApp authentication algorithm:

1. Removes `hash` from data
2. Sorts remaining data alphabetically by key
3. Creates data check string with `key=value` pairs joined by newlines
4. Generates secret key using `HMAC-SHA256(bot_token, "WebAppData")`
5. Compares `HMAC-SHA256(data_check_string, secret_key)` with provided hash

### Auth Date Validation

- Prevents replay attacks by validating auth timestamp
- Default maximum age: 24 hours
- Configurable via `TELEGRAM_AUTH_MAX_AGE` environment variable

### User Management

- Automatically creates users from Telegram data
- Updates existing users with latest information
- Uses Telegram ID as unique identifier
- Generates safe email addresses (`username@telegram.local`)

## Testing

Run the authentication tests:

```bash
php artisan test --filter TelegramAuth
```

Test with mock data:

```php
use App\Services\TelegramAuthService;

$service = app(TelegramAuthService::class);
$mockData = "auth_date=1234567890&user=%7B%22id%22%3A123%7D&hash=valid_hash";
$result = $service->parseAndValidateTelegramData($mockData);
```

## Error Handling

### Authentication Failures

When authentication fails, the middleware returns:

- **JSON requests**: 401 with error details
- **Web requests**: 401 with `telegram.unauthorized` view

### Common Issues

1. **Invalid bot token**: Check `TELEGRAM_BOT_TOKEN` in `.env`
2. **Hash verification fails**: Ensure init data is passed correctly
3. **Auth date expired**: Check system time and `TELEGRAM_AUTH_MAX_AGE`
4. **Missing user data**: Verify Telegram WebApp is sending user information

### Debugging

Enable debug logging:

```php
// In config/logging.php
'channels' => [
    'telegram' => [
        'driver' => 'single',
        'path' => storage_path('logs/telegram.log'),
        'level' => 'debug',
    ],
]
```

The middleware logs authentication attempts, failures, and user creation events.

## Development with Mock Data

In development, you can use the existing mock environment (`resources/js/telegram/mockEnv.ts`) which provides realistic Telegram data for testing.

- Set `TELEGRAM_BOT_TOKEN` in `.env` to a DEV-ONLY token (e.g., `123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ-0123456789`).
- Set `VITE_TELEGRAM_BOT_TOKEN` in `.env` to the same value. This is used only by the local mock to generate a valid hash and must NOT be set in production builds.
- The mock now generates a valid hash matching the server algorithm; no insecure middleware bypasses are used.

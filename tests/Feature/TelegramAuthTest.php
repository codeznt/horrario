<?php

use App\Models\User;
use App\Services\TelegramAuthService;

beforeEach(function () {
    config(['telegram.bot_token' => 'test_bot_token']);
});

it('can verify valid telegram hash', function () {
    $service = new TelegramAuthService;

    // Mock data similar to what Telegram would send
    $data = [
        'auth_date' => (string) time(),
        'user' => '{"id":99281932,"first_name":"Andrew","last_name":"Rogue","username":"rogue","language_code":"en","is_premium":true,"allows_write_to_pm":true}',
    ];

    // Create expected hash using the same algorithm
    $secretKey = hash_hmac('sha256', 'test_bot_token', 'WebAppData', true);
    $dataCheckString = "auth_date={$data['auth_date']}\nuser={$data['user']}";
    $expectedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

    $data['hash'] = $expectedHash;

    expect($service->verifyTelegramHash($data))->toBeTrue();
});

it('rejects invalid telegram hash', function () {
    $service = new TelegramAuthService;

    $data = [
        'auth_date' => (string) time(),
        'user' => '{"id":123,"first_name":"Test","username":"test"}',
        'hash' => 'invalid_hash',
    ];

    expect($service->verifyTelegramHash($data))->toBeFalse();
});

it('can parse and validate telegram init data', function () {
    $service = new TelegramAuthService;

    $authDate = (string) time();
    $userJson = '{"id":99281932,"first_name":"Andrew","last_name":"Rogue","username":"rogue","language_code":"en","is_premium":true,"allows_write_to_pm":true}';

    // Create valid hash
    $secretKey = hash_hmac('sha256', 'test_bot_token', 'WebAppData', true);
    $dataCheckString = "auth_date={$authDate}\nuser={$userJson}";
    $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

    $initData = "auth_date={$authDate}&user=".urlencode($userJson)."&hash={$hash}";

    $result = $service->parseAndValidateTelegramData($initData);

    expect($result)->not->toBeNull();
    expect($result['auth_date'])->toBe($authDate);
    expect($result['user'])->toBeArray();
    expect($result['user']['id'])->toBe(99281932);
});

it('middleware authenticates user with valid telegram data', function () {
    $authDate = (string) time();
    $userJson = '{"id":99281932,"first_name":"Andrew","last_name":"Rogue","username":"rogue","language_code":"en","is_premium":true,"allows_write_to_pm":true}';

    // Create valid hash
    $secretKey = hash_hmac('sha256', 'test_bot_token', 'WebAppData', true);
    $dataCheckString = "auth_date={$authDate}\nuser={$userJson}";
    $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

    $initData = "auth_date={$authDate}&user=".urlencode($userJson)."&hash={$hash}";

    $response = $this->get('/tg-test', [
        'X-Telegram-Init-Data' => $initData,
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'user' => [
            'telegram_id' => 99281932,
            'first_name' => 'Andrew',
            'username' => 'rogue',
        ],
    ]);

    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->telegram_id)->toBe(99281932);
    expect(auth()->user()->first_name)->toBe('Andrew');
    expect(auth()->user()->username)->toBe('rogue');
});

it('middleware rejects invalid telegram data', function () {
    $response = $this->getJson('/tg-test', [
        'X-Telegram-Init-Data' => 'invalid_data_with_bad_hash',
    ]);

    $response->assertStatus(401);
    $response->assertJson([
        'error' => 'telegram_auth_required',
    ]);

    expect(auth()->check())->toBeFalse();
});

it('middleware allows already authenticated users', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/tg-test');

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'user' => [
            'id' => $user->id,
        ],
    ]);

    expect(auth()->id())->toBe($user->id);
});

it('creates user from telegram data correctly', function () {
    $telegramData = [
        'id' => 123456789,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'username' => 'johndoe',
        'language_code' => 'en',
        'is_premium' => true,
        'allows_write_to_pm' => false,
    ];

    $user = User::findOrCreateFromTelegram($telegramData);

    expect($user->telegram_id)->toBe(123456789);
    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('johndoe@telegram.local');
    expect($user->first_name)->toBe('John');
    expect($user->last_name)->toBe('Doe');
    expect($user->username)->toBe('johndoe');
    expect($user->is_premium)->toBeTrue();
    expect($user->allows_write_to_pm)->toBeFalse();
    expect($user->role)->toBe('user');
});

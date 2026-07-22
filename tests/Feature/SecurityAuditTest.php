<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use App\Models\Box;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Tests\TestCase;

/**
 * Security audit tests — PRD §20
 *
 * Validates security controls: headers, rate limiting, RBAC, file upload, CSRF.
 * Each test maps to a specific PRD §20 requirement.
 */
class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════════════════════════
    //  §20.4: Security Headers
    // ═══════════════════════════════════════════════════════════════

    /**
     * PRD §20.4: X-Content-Type-Options must be "nosniff"
     */
    public function test_security_header_x_content_type_options(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    /**
     * PRD §20.4: X-Frame-Options must be "DENY"
     */
    public function test_security_header_x_frame_options(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-Frame-Options', 'DENY');
    }

    /**
     * PRD §20.4: X-XSS-Protection must be "1; mode=block"
     */
    public function test_security_header_x_xss_protection(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }

    /**
     * PRD §20.4: Referrer-Policy must be "strict-origin-when-cross-origin"
     */
    public function test_security_header_referrer_policy(): void
    {
        $response = $this->get('/');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Security headers present on authenticated pages too.
     */
    public function test_security_headers_on_authenticated_pages(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    // ═══════════════════════════════════════════════════════════════
    //  §20.5: Rate Limiting
    // ═══════════════════════════════════════════════════════════════

    /**
     * PRD §20.5: Register rate limited to 3x per hour.
     */
    public function test_register_rate_limited(): void
    {
        // Register 3 times — all should succeed
        for ($i = 1; $i <= 3; $i++) {
            Volt::test('pages.auth.register')
                ->set('name', "User {$i}")
                ->set('email', "user{$i}@example.com")
                ->set('phone', '08123456789' . $i)
                ->set('ktp_number', str_pad($i, 16, '0', STR_PAD_LEFT))
                ->set('address', "Jl. Test No. {$i}, Jakarta Selatan")
                ->set('password', 'password123')
                ->set('password_confirmation', 'password123')
                ->set('tncAccepted', true)
                ->call('register')
                ->assertRedirect(route('login', absolute: false));
        }

        // 4th attempt should be rate limited (3 per hour per IP)
        Volt::test('pages.auth.register')
            ->set('name', 'User 4')
            ->set('email', 'user4@example.com')
            ->set('phone', '081234567894')
            ->set('ktp_number', '0000000000000004')
            ->set('address', 'Jl. Test No. 4, Jakarta Selatan')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
                ->set('tncAccepted', true)
            ->call('register')
            ->assertHasErrors(['email']);
    }

    /**
     * PRD §20.5: Login rate limited to 5x per 15 minutes.
     */
    public function test_login_rate_limited(): void
    {
        $user = User::factory()->create([
            'status' => User::STATUS_ACTIVE,
            'password' => bcrypt('correct-password'),
        ]);

        // Fail 5 times
        for ($i = 0; $i < 5; $i++) {
            Volt::test('pages.auth.login')
                ->set('form.email', $user->email)
                ->set('form.password', 'wrong-password')
                ->call('login');
        }

        // 6th attempt should be locked out
        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'correct-password');

        $component->call('login');
        $component->assertHasErrors();
    }

    // ═══════════════════════════════════════════════════════════════
    //  §20.2: XSS Prevention — No unescaped user input
    // ═══════════════════════════════════════════════════════════════

    /**
     * PRD §20.2: All {!! !!} usages render hardcoded SVG, not user input.
     */
    public function test_no_user_input_in_unescaped_output(): void
    {
        // Scan blade files for {!! !!} — all must render hardcoded strings
        $bladeFiles = [
            'resources/views/components/empty-state.blade.php',
            'resources/views/layouts/admin.blade.php',
            'resources/views/components/toast.blade.php',
            'resources/views/livewire/admin/settings/index.blade.php',
        ];

        foreach ($bladeFiles as $file) {
            $content = file_get_contents(base_path($file));

            // All {!! !!} must contain only $icons, $item['icon'], $tab['icon'] — hardcoded arrays
            preg_match_all('/\{!!\s*(.+?)\s*!!\}/', $content, $matches);

            foreach ($matches[1] as $expr) {
                $this->assertTrue(
                    str_contains($expr, '$icons') || str_contains($expr, '$item') || str_contains($expr, '$tab'),
                    "Unescaped output in {$file} renders non-hardcoded data: {$expr}"
                );
            }
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  §20.3: SQL Injection Prevention
    // ═══════════════════════════════════════════════════════════════

    /**
     * PRD §20.3: All selectRaw calls use parameter binding, not string concat.
     */
    public function test_no_string_concat_in_raw_queries(): void
    {
        $appFiles = glob(app_path('**/*.php'));
        $violations = [];

        foreach ($appFiles as $file) {
            $content = file_get_contents($file);

            // Find selectRaw/whereRaw/etc. calls
            if (preg_match_all('/->(selectRaw|whereRaw|havingRaw|orderByRaw|groupByRaw)\s*\(\s*["\']/', $content, $matches, PREG_OFFSET_CAPTURE)) {
                // For each match, check if there's variable interpolation in the SQL string
                foreach ($matches[0] as $match) {
                    $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                    $rest = substr($content, $match[1], 300);

                    // Check for PHP variable interpolation in the SQL string
                    if (preg_match('/\$[a-zA-Z_]/', $rest)) {
                        // Allow if it's a parameterized query with bindings array
                        if (!str_contains($rest, ', [') && !str_contains($rest, ', $')) {
                            $violations[] = basename($file) . ":{$line}";
                        }
                    }
                }
            }
        }

        $this->assertEmpty($violations, 'Raw SQL with string concatenation found: ' . implode(', ', $violations));
    }

    // ═══════════════════════════════════════════════════════════════
    //  File Upload Security
    // ═══════════════════════════════════════════════════════════════

    /**
     * All file uploads use random/hashed filenames (not user-provided names).
     */
    public function test_file_uploads_use_random_names(): void
    {
        Storage::fake('public');

        $customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $box = Box::factory()->create(['customer_id' => $customer->id, 'status' => Box::STATUS_OPEN]);

        $this->actingAs($customer);

        Livewire::test(\App\Livewire\Customer\SetorResi::class)
            ->set('boxId', $box->id)
            ->set('name', 'Test Barang')
            ->set('quantity', 1)
            ->set('priceYuan', '50')
            ->set('resiNumber', 'SEC-AUDIT-001')
            ->set('proofCo', UploadedFile::fake()->image('user_uploaded_file.jpg', 100, 100))
            ->set('isSensitive', false)
            ->call('submit');

        $item = \App\Models\Item::where('resi_number', 'SEC-AUDIT-001')->first();
        $this->assertNotNull($item);
        $this->assertStringNotContainsString('user_uploaded_file', $item->proof_co, 'Filename must be hashed, not user-provided');
    }
}

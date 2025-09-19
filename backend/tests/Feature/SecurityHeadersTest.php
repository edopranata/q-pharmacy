<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    /**
     * Test that security headers are applied to API responses.
     */
    public function test_security_headers_are_applied_to_api_responses(): void
    {
        // Test with a simple API endpoint that doesn't require authentication or database
        $response = $this->getJson('/api/test-headers');

        // Test that security headers are present
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        
        // Test the main referrer policy header
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Test additional security headers
        $response->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');
        $response->assertHeader('X-Download-Options', 'noopen');
        
        // Test Content Security Policy
        $response->assertHeaderMissing('Strict-Transport-Security'); // Only for HTTPS
        $this->assertStringContainsString(
            "default-src 'self'",
            $response->headers->get('Content-Security-Policy')
        );
        
        // Verify successful response
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Security headers test']);
    }

    /**
     * Test that HSTS header behavior is documented.
     * Note: HSTS is only applied for actual HTTPS requests in production.
     */
    public function test_hsts_header_documentation(): void
    {
        // Test regular HTTP request (no HSTS expected)
        $response = $this->getJson('/api/test-headers');
        
        // HSTS should NOT be present for HTTP requests
        $response->assertHeaderMissing('Strict-Transport-Security');
        $response->assertStatus(200);
        
        // Note: In production with HTTPS, the SecurityHeaders middleware
        // will automatically add HSTS header when request->isSecure() returns true
    }

    /**
     * Test referrer policy behavior explanation.
     */
    public function test_referrer_policy_explanation(): void
    {
        $response = $this->getJson('/api/test-headers');

        $referrerPolicy = $response->headers->get('Referrer-Policy');
        
        $this->assertEquals('strict-origin-when-cross-origin', $referrerPolicy);
        
        // This policy ensures:
        // 1. Full referrer information is sent when making same-origin requests
        // 2. Only the origin (scheme, host, port) is sent when making cross-origin requests
        // 3. No referrer information is sent when downgrading from HTTPS to HTTP
        
        $response->assertStatus(200);
    }
}
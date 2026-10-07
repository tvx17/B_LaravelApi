<?php

namespace Tests\Feature;

use Tests\TestCase;

class CorsTest extends TestCase
{
    public function test_frontend_preflight_allows_credentials_and_requested_headers(): void
    {
        $this->withHeaders([
            'Origin' => 'http://localhost:9200',
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'Content-Type,X-XSRF-TOKEN',
        ])->options('/api/status')
            ->assertNoContent()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:9200')
            ->assertHeader('Access-Control-Allow-Credentials', 'true')
            ->assertHeader('Access-Control-Allow-Methods', 'POST')
            ->assertHeader('Access-Control-Allow-Headers', 'Content-Type,X-XSRF-TOKEN');
    }

    public function test_other_origins_are_not_reflected_in_the_allow_origin_header(): void
    {
        $this->withHeaders([
            'Origin' => 'http://localhost:9300',
            'Access-Control-Request-Method' => 'GET',
        ])->options('/api/status')->assertHeader('Access-Control-Allow-Origin', 'http://localhost:9200');
    }

    public function test_actual_api_responses_allow_frontend_credentials(): void
    {
        $this->withHeader('Origin', 'http://localhost:9200')->get('/api/status')
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:9200')
            ->assertHeader('Access-Control-Allow-Credentials', 'true');
    }

    public function test_admin_panels_remain_outside_the_cors_paths(): void
    {
        $this->withHeader('Origin', 'http://localhost:9200')->get('/filament/login')
            ->assertOk()->assertHeaderMissing('Access-Control-Allow-Origin');

        $this->withHeader('Origin', 'http://localhost:9200')->get('/artisan/login')
            ->assertOk()->assertHeaderMissing('Access-Control-Allow-Origin');
    }
}

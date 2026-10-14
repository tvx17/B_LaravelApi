<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScrambleTest extends TestCase
{
    use RefreshDatabase;

    public function test_documentation_is_accessible_locally_and_uses_the_request_host(): void
    {
        $this->app['env'] = 'local';
        $this->get('/docs/api')->assertOk();
        $spec = $this->getJson('http://localhost:8090/docs/api.json')->assertOk()->json();
        $this->assertSame('http://localhost:8090', $spec['servers'][0]['url']);
        $this->assertArrayHasKey('/', $spec['paths']);
        $this->assertArrayHasKey('/api/status', $spec['paths']);
        $this->assertArrayHasKey('/api/email/verify/{id}/{hash}', $spec['paths']);
        $this->assertCount(8, $spec['paths']);
        $this->assertSame('cookie', $spec['components']['securitySchemes']['sessionCookie']['in']);
        $this->assertSame(config('session.cookie'), $spec['components']['securitySchemes']['sessionCookie']['name']);
        $this->assertSame([], $spec['paths']['/api/login']['post']['security']);
        $this->assertSame([['sessionCookie' => []]], $spec['security']);

        $login = $spec['paths']['/api/login']['post'];
        $this->assertSame(['email', 'password'], $login['requestBody']['content']['application/json']['schema']['required']);
        $this->assertSame('X-XSRF-TOKEN', $login['parameters'][0]['name']);
        $success = $login['responses']['200']['content']['application/json']['schema'];
        $this->assertSame(['code', 'status', 'data', 'responseStatus'], $success['required']);
        $this->assertSame(['05001'], $success['properties']['code']['enum']);
        $this->assertSame('05002', $login['responses']['401']['content']['application/json']['schema']['properties']['code']['enum'][0]);
        $this->assertArrayHasKey('419', $login['responses']);
        $this->assertArrayHasKey('errors', $login['responses']['422']['content']['application/json']['schema']['properties']['data']['properties']);
        $this->assertArrayNotHasKey('password', $success['properties']['data']['properties']);
        $this->assertArrayNotHasKey('remember_token', $success['properties']['data']['properties']);
    }

    public function test_documentation_is_not_public_in_production(): void
    {
        $this->app['env'] = 'production';
        $this->get('/docs/api')->assertForbidden();
        $this->getJson('/docs/api.json')->assertForbidden();
    }

    public function test_documented_response_codes_match_existing_http_endpoints(): void
    {
        $this->app['env'] = 'local';
        $spec = $this->getJson('/docs/api.json')->assertOk()->json();
        foreach ([['/', 200], ['/api/status', 200], ['/api/user', 401]] as [$path, $status]) {
            $response = $this->getJson($path)->assertStatus($status);
            $schema = $spec['paths'][$path]['get']['responses'][(string) $status]['content']['application/json']['schema'];
            $this->assertContains($response->json('code'), $schema['properties']['code']['enum']);
            $this->assertContains($response->json('status'), $schema['properties']['status']['enum']);
            $this->assertSame($response->json('responseStatus'), $schema['properties']['responseStatus']['const']);
        }
    }
}

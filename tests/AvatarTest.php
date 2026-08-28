<?php

namespace LasseRafn\UiAvatars\Tests;

use PHPUnit\Framework\TestCase;

class AvatarTest extends TestCase
{
    private $baseUrl = 'http://localhost:8678';

    /**
     * Send an HTTP request to the local server.
     */
    private function request($path)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            $this->fail("Failed to connect to local server {$this->baseUrl}: {$error}");
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $headersStr = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        $headers = [];
        foreach (explode("\r\n", $headersStr) as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $headers[strtolower(trim($parts[0]))] = trim($parts[1]);
            }
        }

        return [
            'status' => $httpCode,
            'headers' => $headers,
            'body' => $body,
        ];
    }

    /** @test */
    public function can_generate_default_png_avatar()
    {
        $response = $this->request('/api/Ada%20Lovelace/128?debug&no-cache');
        
        $this->assertEquals(200, $response['status']);
        $this->assertStringContainsString('image/png', $response['headers']['content-type']);
        $this->assertGreaterThan(0, strlen($response['body']));
    }

    /** @test */
    public function can_generate_default_svg_avatar()
    {
        $response = $this->request('/api/Ada%20Lovelace/128/f97316/fff/2/0.4/0/1/1/svg?no-cache');
        
        $this->assertEquals(200, $response['status']);
        $this->assertStringContainsString('image/svg+xml', $response['headers']['content-type']);
        $this->assertStringContainsString('<svg', $response['body']);
        $this->assertStringContainsString('fill="#f97316"', $response['body']);
    }

    /** @test */
    public function png_supports_hex8_transparency()
    {
        $response = $this->request('/api/Ada%20Lovelace/128/f9731633/fff/2/0.4/0/1/1/png?no-cache');
        
        $this->assertEquals(200, $response['status']);
        $this->assertStringContainsString('image/png', $response['headers']['content-type']);
    }

    /** @test */
    public function png_supports_rgba_transparency()
    {
        $response = $this->request('/api/Ada%20Lovelace/128/rgba(249,115,22,0.2)/fff/2/0.4/0/1/1/png?no-cache');
        
        $this->assertEquals(200, $response['status']);
        $this->assertStringContainsString('image/png', $response['headers']['content-type']);
    }

    /** @test */
    public function svg_supports_hex8_transparency()
    {
        $response = $this->request('/api/Ada%20Lovelace/128/f9731633/fff/2/0.4/0/1/1/svg?no-cache');
        
        $this->assertEquals(200, $response['status']);
        $this->assertStringContainsString('image/svg+xml', $response['headers']['content-type']);
        $this->assertStringContainsString('fill="#f9731633"', $response['body']);
    }

    /** @test */
    public function svg_supports_rgba_transparency()
    {
        $response = $this->request('/api/Ada%20Lovelace/128/rgba(34,197,94,0.2)/22c55e/2/0.4/0/1/1/svg?no-cache');
        
        $this->assertEquals(200, $response['status']);
        $this->assertStringContainsString('image/svg+xml', $response['headers']['content-type']);
        // rgba(34,197,94,0.2) is #22c55e33
        $this->assertStringContainsString('fill="#22c55e33"', $response['body']);
    }

    /** @test */
    public function query_parameters_do_not_interfere_with_svg_format()
    {
        $response = $this->request('/api/Ada%20Lovelace/128/f97316/fff/2/0.4/0/1/1/svg?debug=1');
        
        $this->assertEquals(200, $response['status']);
        $this->assertStringContainsString('image/svg+xml', $response['headers']['content-type']);
    }

    /** @test */
    public function can_parse_query_parameters_overrides()
    {
        $response = $this->request('/api/?name=Test+User&size=100&background=000&color=fff&format=svg');
        
        $this->assertEquals(200, $response['status']);
        $this->assertStringContainsString('image/svg+xml', $response['headers']['content-type']);
        $this->assertStringContainsString('fill="#000"', $response['body']);
        $this->assertStringContainsString('fill="#fff"', $response['body']);
        $this->assertStringContainsString('TU', $response['body']);
    }
}

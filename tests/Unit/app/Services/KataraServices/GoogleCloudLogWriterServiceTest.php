<?php

use App\Services\KataraServices\GoogleCloudLogWriterService;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GoogleCloudLogWriterServiceTest extends TestCase
{
    private $googleCloudLogWriterService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->googleCloudLogWriterService = new GoogleCloudLogWriterService;
    }

    public function test_write_log()
    {
        $message = [
            'stack' => 'test',
            'message' => 'test',
        ];
        $response = $this->googleCloudLogWriterService->write(json_encode($message));
        $this->assertEquals(Response::HTTP_CREATED, $response['code']);
    }
}

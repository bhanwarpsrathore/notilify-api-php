<?php

namespace NotilifyAPI\Tests;

use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class TestCase extends BaseTestCase {

    /**
     * @param  array<mixed>  $expectedParams
     */
    public function mockGuzzleRequest(string|StreamInterface|null $expectedResponse, string $expectedEndpoint, array $expectedParams): MockObject {
        $mockResponse = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        if ($expectedResponse) {
            $mockResponse->expects($this->once())
                ->method('getBody')
                ->willReturn($expectedResponse);
        }

        $mockGuzzle = $this->getMockBuilder(GuzzleClient::class)
            ->onlyMethods(['request'])
            ->getMock();
        $mockGuzzle->expects($this->once())
            ->method('request')
            ->with('POST', $expectedEndpoint, $expectedParams)
            ->willReturn($mockResponse);

        return $mockGuzzle;
    }
}

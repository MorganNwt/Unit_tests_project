<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\GithubUserProvider;
use PHPUnit\Framework\TestCase;

class GithubUserProviderTest extends TestCase
{
    public function testLoadUserByUsernameThrowingException()
    {
        $client = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $serializer = $this
            ->getMockBuilder('JMS\Serializer\Serializer')
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this

            ->getMockBuilder('Psr\Http\Message\ResponseInterface')
            ->getMock();
        $client
            ->expects($this->once()) // Nous nous attendons à ce que la méthode get soit appelée une fois
            ->method('get')
            ->willReturn($response);
        $streamedResponse = $this
            ->getMockBuilder('Psr\Http\Message\StreamInterface')
            ->getMock();
        $response
            ->expects($this->once()) // Nous nous attendons à ce que la méthode getBody soit appelée une fois
            ->method('getBody')
            ->willReturn($streamedResponse);
        $streamedResponse
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('foo');
        $serializer
            ->expects($this->once()) // Nous nous attendons à ce que la méthode deserialize soit appelée une fois
            ->method('deserialize')
            ->willReturn([]);

        $this->expectException('LogicException');
        $githubUserProvider = new GithubUserProvider($client, $serializer);
        $githubUserProvider->loadUserByIdentifier('an-access-token');
    }
}

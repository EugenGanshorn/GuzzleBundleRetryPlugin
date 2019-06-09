<?php

use EugenGanshorn\Bundle\GuzzleBundleRetryPlugin\Logger;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LoggerTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testCallback()
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('info')
            ->with(
                $this->equalTo(sprintf('%s - will wait %01.2f seconds and try it again, this is attempt #%d', '', 42.7, 7))
            )
        ;

        $formatter = $this->createMock(MessageFormatter::class);
        $formatter
            ->expects($this->once())
            ->method('format')
            ->with(
                $this->equalTo($request),
                $this->equalTo($response)
            )
            ->willReturn('')
        ;

        $sut = new Logger();
        $sut->setLogger($logger);
        $sut->setFormatter($formatter);
        $sut->callback(7, 42.7, $request, [], $response);
    }
}
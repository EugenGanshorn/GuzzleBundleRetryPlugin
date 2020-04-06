<?php

namespace EugenGanshorn\Bundle\GuzzleBundleRetryPlugin;

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Logger implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var MessageFormatter
     */
    protected $formatter;

    public function setFormatter(MessageFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function callback(
        $attemptNumber,
        $delay,
        Request $request,
        $options,
        ?Response $response = null
    ) {
        $this->logger->info(
            sprintf(
                '%s - will wait %01.2f seconds and try it again, this is attempt #%d',
                $this->formatter->format($request, $response),
                $delay,
                $attemptNumber
            )
        );
    }
}

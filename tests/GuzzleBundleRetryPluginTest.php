<?php

use EightPoints\Bundle\GuzzleBundle\PluginInterface;
use EugenGanshorn\Bundle\GuzzleBundleRetryPlugin\GuzzleBundleRetryPlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GuzzleBundleRetryPluginTest extends TestCase
{
    /** @var GuzzleBundleRetryPlugin */
    protected $plugin;

    public function setUp(): void
    {
        parent::setUp();
        $this->plugin = new GuzzleBundleRetryPlugin();
    }

    public function testSubClassesOfPlugin()
    {
        $this->assertInstanceOf(PluginInterface::class, $this->plugin);
        $this->assertInstanceOf(Bundle::class, $this->plugin);
    }

    public function testGetPluginName()
    {
        $this->assertEquals('retry', $this->plugin->getPluginName());
    }

    public function testDoNotDefineRetryCallBackWhenLoggerNotFound()
    {
        $containerMock = $this->createMock(ContainerBuilder::class);
        $containerMock->expects($this->once())
            ->method('has')
            ->willReturn(false);

        $containerMock->expects($this->once())
            ->method('setDefinition')
            ->with(
                $this->equalTo('guzzle_bundle_retry_plugin.middleware.retry.test_client'),
                $this->callback(function ($middleware) {
                    return null === $middleware->getArgument(0)['on_retry_callback'];
                })
            );

        $handlerMock = $this->createMock(Definition::class);

        $this->plugin->loadForClient(
            $this->defaultRetryConfig(),
            $containerMock,
            'test_client',
            $handlerMock
        );
    }

    public function testSetRetryCallBackWhenLoggerFound()
    {
        $containerMock = $this->createMock(ContainerBuilder::class);
        $containerMock->expects($this->once())
            ->method('has')
            ->willReturn(true);

        $containerMock->expects($this->once())
            ->method('setDefinition')
            ->with(
                $this->equalTo('guzzle_bundle_retry_plugin.middleware.retry.test_client'),
                $this->callback(function (Definition $middleware) {
                    $onRetryCallback = $middleware->getArgument(0)['on_retry_callback'];

                    $definition = $onRetryCallback[0];

                    return $definition instanceof Definition &&
                        EugenGanshorn\Bundle\GuzzleBundleRetryPlugin\Logger::class === $definition->getClass() &&
                        'callback' === $onRetryCallback[1];
                })
            );

        $handlerMock = $this->createMock(Definition::class);

        $this->plugin->loadForClient(
            $this->defaultRetryConfig(),
            $containerMock,
            'test_client',
            $handlerMock
        );
    }

    private static function defaultRetryConfig(): array
    {
        return [
            'retry_enabled' => true,
            'max_retry_attempts' => 10,
            'retry_only_if_retry_after_header' => false,
            'retry_on_status' => [503, 429],
            'default_retry_multiplier' => 1.5,
            'retry_on_timeout' => false,
            'expose_retry_header' => false,
            'retry_header' => 'X-Retry-Counter',
            'retry_after_header' => 'Retry-After',
            'on_retry_callback' => null,
        ];
    }
}

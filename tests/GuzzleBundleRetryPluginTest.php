<?php

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use EugenGanshorn\Bundle\GuzzleBundleRetryPlugin\GuzzleBundleRetryPlugin;
use PHPUnit\Framework\TestCase;
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
        $this->assertInstanceOf(EightPointsGuzzleBundlePlugin::class, $this->plugin);
        $this->assertInstanceOf(Bundle::class, $this->plugin);
    }

    public function testGetPluginName()
    {
        $this->assertEquals('retry', $this->plugin->getPluginName());
    }
}

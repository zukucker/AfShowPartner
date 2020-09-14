<?php

namespace AfShowPartner\Tests;

use AfShowPartner\AfShowPartner as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'AfShowPartner' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['AfShowPartner'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}

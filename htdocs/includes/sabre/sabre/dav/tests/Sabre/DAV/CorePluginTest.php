<?php

namespace Sabre\DAV;

class CorePluginTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInfo()
    {
        $corePlugin = new CorePlugin();
        $this->assertEquals('core', $corePlugin->getPluginInfo()['name']);
    }
}

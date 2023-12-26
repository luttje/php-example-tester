<?php

namespace Luttje\ExampleTester\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Console\Application;

class TestCase extends BaseTestCase
{
    protected Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        $application = new Application();
        $application->setAutoExit(false);
        $this->application = $application;

        require __DIR__.'/../src/commands.php';
    }
}

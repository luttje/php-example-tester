<?php

namespace Luttje\ExampleTester\Tests\Unit;

use Luttje\ExampleTester\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group compile-readme
 */
final class CompileReadmeCommandTest extends TestCase
{
    public function testCanCompileReadme(): void
    {
        $outputFile = __DIR__.'/../../tmp/README-tmp.md';

        if (! is_dir(dirname($outputFile))) {
            mkdir(dirname($outputFile), 0777, true);
        }

        $inputFile = __DIR__.'/../Fixtures/ExampleClassTest.README.md';

        $command = $this->application->find('compile');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--input' => $inputFile,
            '--output' => $outputFile,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Done compiling examples!', $output);

        $this->assertFileEquals(__DIR__.'/../Fixtures/ExampleClassTest.README.expected.md', $outputFile);

        unlink($outputFile);
    }
}

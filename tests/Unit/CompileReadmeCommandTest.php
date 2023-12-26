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

        $inputFile = __DIR__.'/../Fixtures/ExampleClass.README.md';

        $command = $this->application->find('example-tester:compile');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'class' => \Luttje\ExampleTester\Tests\Fixtures\ExampleClass::class,
            '--input' => $inputFile,
            '--output' => $outputFile,
            '--warning-comment' => 'false',
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Done compiling examples!', $output);

        $this->assertFileEquals(__DIR__.'/../Fixtures/ExampleClass.README.expected.md', $outputFile);

        unlink($outputFile);
    }
}

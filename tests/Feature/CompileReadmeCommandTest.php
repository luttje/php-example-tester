<?php

namespace Luttje\ExampleTester\Tests\Feature;

use Luttje\ExampleTester\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group compile-readme
 */
final class CompileReadmeCommandTest extends TestCase
{
    private static $tmpDir;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $tmpDir = __DIR__.'/../../tmp';

        if (! is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        self::$tmpDir = $tmpDir;
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $tmpDir = self::$tmpDir;

        if (is_dir($tmpDir)) {
            rmdir($tmpDir);
        }
    }

    public function testCanCompileReadme(): void
    {
        $outputFile = self::$tmpDir.'/README-tmp.md';
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

    public function testCanCompileReadmeWithDefaultInput(): void
    {
        $outputFile = self::$tmpDir.'/README-tmp.md';
        $inputFile = __DIR__.'/../Fixtures/ExampleClassTest.README.md';

        copy($inputFile, $outputFile);

        $command = $this->application->find('compile');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--output' => $outputFile,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Done compiling examples!', $output);

        $this->assertFileEquals(__DIR__.'/../Fixtures/ExampleClassTest.README.expected.md', $outputFile);

        unlink($outputFile);
    }

    public function testCanCompileReadmeByCreatingSpecifiedOutput(): void
    {
        $outputFile = self::$tmpDir.'/new-README-tmp.md';
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

    public function testWillErrorOnInvalidInput(): void
    {
        $outputFile = self::$tmpDir.'/README-tmp.md';
        $inputFile = __DIR__.'/../Fixtures/ExampleClassTest.README.md-non-existent';

        $command = $this->application->find('compile');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--input' => $inputFile,
            '--output' => $outputFile,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Input file '.$inputFile.' does not exist.', $output);

        $this->assertFileDoesNotExist($outputFile);
    }

    public function testWillErrorOnInvalidOutput(): void
    {
        $outputFile = self::$tmpDir.'/invalid-because-dir';
        $inputFile = __DIR__.'/../Fixtures/ExampleClassTest.README.md';

        mkdir($outputFile, 0777, true);

        $command = $this->application->find('compile');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--input' => $inputFile,
            '--output' => $outputFile,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Output file '.$outputFile.' is a directory.', $output);

        rmdir($outputFile);
    }

    public function testWillErrorForUnwritableOutput(): void
    {
        $outputFile = self::$tmpDir.'/invalid-because-unwritable';
        $inputFile = __DIR__.'/../Fixtures/ExampleClassTest.README.md';

        touch($outputFile);
        chmod($outputFile, 0444);

        $command = $this->application->find('compile');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--input' => $inputFile,
            '--output' => $outputFile,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Output file '.$outputFile.' is not writable.', $output);

        chmod($outputFile, 0777);
        unlink($outputFile);
    }

    public function testWillErrorForBusyOutput(): void
    {
        $outputFile = self::$tmpDir.'/invalid-because-busy';
        $inputFile = __DIR__.'/../Fixtures/ExampleClassTest.README.md';

        touch($outputFile);

        // open the file for and get an exclusive lock
        $handle = fopen($outputFile, 'wr+');
        if (!flock($handle, LOCK_EX)) {
            $this->fail('Unable to lock file '.$outputFile);
        }

        $command = $this->application->find('compile');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--input' => $inputFile,
            '--output' => $outputFile,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Output file '.$outputFile.' is not writable.', $output);

        flock($handle, LOCK_UN);
        fclose($handle);

        unlink($outputFile);
    }
}

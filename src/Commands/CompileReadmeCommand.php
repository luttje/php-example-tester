<?php

namespace Luttje\ExampleTester\Commands;

use Luttje\ExampleTester\Compiler\ReadmeCompiler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'compile',
    description: 'Compile the README.md file for the package based on attributes in the tests. Run this command from the root of your project.',
    aliases: ['compile-readme'],
)]
class CompileReadmeCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption(
                'input',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The path to the README.md file to read from. Defaults to the same file as the output.',
            )
            ->addOption(
                'output',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The path to the README.md file to write to. Defaults to the README.md file in the root of the package.',
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $workingDirectory = getcwd();
        $outputFile = $input->getOption('output') ?? $workingDirectory.'/README.md';
        $inputFile = $input->getOption('input') ?? $outputFile;

        if (!is_file($inputFile)) {
            $output->writeLn("<error>Input file {$inputFile} does not exist.</error>");

            return Command::FAILURE;
        }

        if (is_dir($outputFile)) {
            $output->writeLn("<error>Output file {$outputFile} is a directory.</error>");

            return Command::FAILURE;
        }

        $outputHandle = $this->openExclusive($outputFile);

        if (!$outputHandle) {
            $output->writeLn("<error>Output file {$outputFile} is not writable.</error>");

            return Command::FAILURE;
        }

        if ($inputFile === $outputFile) {
            $output->writeLn("Compiling examples in {$inputFile}...");
            $input = stream_get_contents($outputHandle);
            fseek($outputHandle, 0);
        } else {
            $output->writeLn("Compiling examples from {$inputFile} to {$outputFile}...");
            $input = file_get_contents($inputFile);
        }

        $this->compile($input, $outputHandle);

        $this->closeExclusive($outputHandle);

        $output->writeLn('Done compiling examples!');

        return Command::SUCCESS;
    }

    protected function openExclusive(string $path): mixed
    {
        $handle = @fopen($path, 'c+');

        if (!$handle) {
            return false;
        }

        if (!flock($handle, LOCK_EX|LOCK_NB, $wouldBlock) && $wouldBlock) {
            fclose($handle);
            return false;
        }

        return $handle;
    }

    protected function closeExclusive($handle): bool
    {
        return flock($handle, LOCK_UN) && fclose($handle);
    }

    protected function compile(string $input, mixed $outputHandle): void
    {
        $compiler = new ReadmeCompiler();
        $compiler->compile($input, $outputHandle);
    }
}

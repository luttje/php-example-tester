<?php

namespace Luttje\ExampleTester\Commands;

use ColinODell\Indentation\Indentation;
use Luttje\ExampleTester\Attributes\ReadmeExampleCompiler;
use Luttje\ExampleTester\Compiler\ReadmeCompiler;
use Luttje\ExampleTester\Parser\ReadmeParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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

        if (! is_file($inputFile)) {
            $output->writeLn("<error>Input file {$inputFile} does not exist.</error>");

            return Command::FAILURE;
        }

        if (is_dir($outputFile)) {
            $output->writeLn("<error>Output file {$outputFile} is a directory.</error>");

            return Command::FAILURE;
        }

        // Check if the output file is a valid file path.
        if (is_file($outputFile) && ! is_writable($outputFile)) {
            $output->writeLn("<error>Output file {$outputFile} is not writable.</error>");

            return Command::FAILURE;
        }

        if ($inputFile === $outputFile) {
            $output->writeLn("Compiling examples in {$inputFile}...");
        } else {
            $output->writeLn("Compiling examples from {$inputFile} to {$outputFile}...");
        }

        if (!$this->compile($inputFile, $outputFile, $output)) {
            return Command::FAILURE;
        }

        $output->writeLn('Done compiling examples!');

        return Command::SUCCESS;
    }

    protected function compile(string $inputFile, string $outputFile, OutputInterface $output): bool
    {
        $compiler = new ReadmeCompiler();
        $compiler->compile($inputFile, $outputFile);

        return true;
    }
}

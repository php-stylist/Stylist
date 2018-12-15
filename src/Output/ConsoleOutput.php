<?php declare(strict_types = 1);

namespace Stylist\Output;

use PackageVersions\Versions;
use Stylist\CheckResult;
use Stylist\File;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface as SymfonyOutput;
use Webmozart\PathUtil\Path;


final class ConsoleOutput implements OutputInterface
{

	/** @var string */
	private $workingDirectory;

	/** @var SymfonyOutput */
	private $console;

	/** @var string[] */
	private $paths;

	/** @var int */
	private $counter = 0;


	public function __construct(string $workingDirectory, SymfonyOutput $console)
	{
		$this->workingDirectory = $workingDirectory;
		$this->console = $console;

		$formatter = $this->console->getFormatter();
		$formatter->setStyle('path', new OutputFormatterStyle('white', null, ['bold']));
		$formatter->setStyle('success', new OutputFormatterStyle('white', 'green', ['bold']));
		$formatter->setStyle('mild-success', new OutputFormatterStyle('green', null, ['bold']));
		$formatter->setStyle('err', new OutputFormatterStyle('white', 'red', ['bold']));
		$formatter->setStyle('mild-err', new OutputFormatterStyle('red', null, ['bold']));
		$formatter->setStyle('warn', new OutputFormatterStyle('yellow', null, ['bold']));
		$formatter->setStyle('notice', new OutputFormatterStyle('cyan', null, ['bold']));
	}


	public function initialize(array $paths): void
	{
		$this->console->writeln(\sprintf(
			'Stylist v%s @ PHP %s%s',
			Versions::getVersion('stylist/stylist'),
			\PHP_VERSION,
			\PHP_EOL
		));

		$this->paths = $paths;
		$this->console->writeln('Scanning the following paths:');
		foreach ($paths as $path) {
			$this->console->writeln(\sprintf(' - <path>%s</path>', $path));
		}

		$this->console->writeln('');
	}


	public function checkedFile(File $file): void
	{
		if ($file->isOk()) {
			$this->console->write('.');

		} else {
			$this->console->write('<err>E</err>');
		}

		if (++$this->counter % 60 === 0) {
			$this->console->writeln('');
		}
	}


	public function finish(CheckResult $result): void
	{
		$this->console->write("\n\n\n");

		$files = $result->getFiles();
		foreach ($files as $file) {
			$this->writeFileDetails($file);
		}

		$this->console->writeln('');
		$this->console->writeln(\sprintf(
			'Checked %d files in %.1f seconds.',
			\count($files),
			$result->getTimeTaken()
		));

		$style = $result->isSuccess() ? 'success' : 'err';
		$this->console->writeln(\sprintf(
			'<%s>Finished %s.</>',
			$style,
			$result->isSuccess() ? 'OK' : 'with issues'
		));
	}


	private function writeFileDetails(File $file): void
	{
		if ($file->isOk()) {
			return;
		}

		$this->console->writeln(\sprintf(
			'%d issue%s found in <path>%s</path>',
			$file->countIssues(),
			$file->countIssues() !== 1 ? 's' : '',
			Path::makeRelative($file->getFileInfo()->getPathname(), $this->workingDirectory)
		));

		foreach ($file->getIssues() as $issue) {
			$this->console->writeln(\sprintf(
				' <err>% 4d</err>  %s',
				$issue->getLine(),
				$issue->getMessage()
			));
		}

		if ($file->hasCheckError()) {
			$checkError = $file->getCheckError();
			\assert($checkError !== null);
			$this->console->writeln(\sprintf(
				' <err>FAIL</err>  Could not check further due to uncaught %s: %s',
				\get_class($checkError),
				$checkError->getMessage()
			));
		}

		$this->console->writeln('');
	}

}

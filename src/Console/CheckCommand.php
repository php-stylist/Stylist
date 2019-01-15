<?php declare(strict_types = 1);

namespace Stylist\Console;

use Stylist\DI\ContainerFactory;
use Stylist\Output\ConsoleOutput;
use Stylist\Output\OutputChain;
use Stylist\Output\XmlOutput;
use Stylist\Stylist;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\PathUtil\Path;


final class CheckCommand extends Command
{

	private const OPTION_CONFIG = 'config';
	private const OPTION_ACCEPT = 'accept';
	private const OPTION_EXCLUDE = 'exclude';
	private const OPTION_OUTPUT = 'output';
	private const OPTION_TEMP_DIRECTORY = 'temp';
	private const OPTION_DRY_RUN = 'dry-run';
	private const ARGUMENT_PATHS = 'paths';

	/** @var string */
	private $workingDirectory;

	/** @var ContainerFactory */
	private $containerFactory;


	public function __construct(
		string $workingDirectory,
		ContainerFactory $containerFactory
	)
	{
		parent::__construct();
		$this->workingDirectory = $workingDirectory;
		$this->containerFactory = $containerFactory;
	}


	protected function configure(): void
	{
		$this->setName('check')
			->setDescription('Check PHP code style against a configured set of checks')
			->addOption(self::OPTION_CONFIG, 'c', InputOption::VALUE_REQUIRED, 'Path to configuration file')
			->addOption(self::OPTION_ACCEPT, 'a', InputOption::VALUE_REQUIRED, 'Accepted files mask', '*.php, *.phpt')
			->addOption(self::OPTION_EXCLUDE, 'e', InputOption::VALUE_REQUIRED, 'Excluded files mask', '')
			->addOption(self::OPTION_OUTPUT, 'o', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Output format and optionally file, e.g. --o xml or -o xml=result.xml', ['console'])
			->addOption(self::OPTION_TEMP_DIRECTORY, null, InputOption::VALUE_REQUIRED, 'Temporary directory')
			->addOption(self::OPTION_DRY_RUN, null, InputOption::VALUE_NONE, 'Do not fix reported issues automatically')
			->addArgument(self::ARGUMENT_PATHS, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Paths to check');
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		\set_error_handler(static function ($severity, $message, $file, $line): void {
			if (($severity & \error_reporting()) === $severity) {
				throw new \ErrorException($message, 0, $severity, $file, $line);
			}
		});

		if (\PHP_VERSION_ID < 70100) {
			$output->writeln('<error>Stylist requires PHP >= 7.1</error>');
			return 1;
		}


		// config file

		$configFileOption = $input->getOption(self::OPTION_CONFIG);
		\assert(\is_string($configFileOption) || $configFileOption === null);
		$configFile = $configFileOption !== null
			? Path::makeAbsolute($configFileOption, $this->workingDirectory)
			: Path::join($this->workingDirectory, 'stylist.neon');

		if ( ! (\is_file($configFile) && \is_readable($configFile))) {
			$output->writeln(
				'<error>The configuration file was not found. Create stylist.neon in the current working directory, '
				. 'or indicate a different configuration file via the --config option.</error>'
			);
			return 1;
		}


		// output

		$checkerOutput = new OutputChain();

		$outputOptions = $input->getOption(self::OPTION_OUTPUT);
		\assert(\is_array($outputOptions));
		foreach ($outputOptions as $outputOption) {
			$pieces = \explode('=', $outputOption, 2);
			if (\count($pieces) === 2) {
				[$format, $file] = $pieces;
				$file = Path::makeAbsolute($file, $this->workingDirectory);

			} else {
				$format = $pieces[0];
				$file = 'php://stdout';
			}

			switch ($format) {
				case 'console':
					$checkerOutput->addOutput(new ConsoleOutput($this->workingDirectory, $output));
					break;

				case 'xml':
					$checkerOutput->addOutput(new XmlOutput($file));
					break;

				default:
					$output->writeln(\sprintf(
						'<error>Invalid output format "%s"</error>',
						$format
					));
					return 1;
			}
		}


		// temp directory

		$tempDirectory = $input->getOption(self::OPTION_TEMP_DIRECTORY);
		\assert(\is_string($tempDirectory) || $tempDirectory === null);
		if (\is_string($tempDirectory)) {
			$tempDirectory = Path::makeAbsolute($tempDirectory, $this->workingDirectory);
		}


		// create container

		$container = $this->containerFactory->create(
			$checkerOutput,
			$configFile,
			$tempDirectory
		);


		$accepted = $input->getOption(self::OPTION_ACCEPT);
		\assert(\is_string($accepted));
		$accepted = \array_map('trim', \explode(',', $accepted));

		$excluded = $input->getOption(self::OPTION_EXCLUDE);
		\assert(\is_string($excluded));
		$excluded = \array_map('trim', \explode(',', $excluded));

		$dryRun = (bool) $input->getOption(self::OPTION_DRY_RUN);

		/** @var string[] $pathsOption */
		$pathsOption = $input->getArgument(self::ARGUMENT_PATHS);
		$absolutePaths = \array_map(function (string $path): string {
			return Path::makeAbsolute($path, $this->workingDirectory);
		}, $pathsOption);

		$stylist = $container->getByType(Stylist::class);
		\assert($stylist instanceof Stylist);

		$result = $stylist
			->accept($accepted)
			->exclude($excluded)
			->dryRun($dryRun)
			->check($absolutePaths);

		\restore_error_handler();
		return (int) ! $result->isSuccess();
	}

}

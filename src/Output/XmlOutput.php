<?php declare(strict_types = 1);

namespace Stylist\Output;

use Stylist\CheckResult;
use Stylist\File;


final class XmlOutput implements OutputInterface
{

	/** @var string */
	private $fileName;

	/** @var \SimpleXMLElement */
	private $rootElement;


	public function __construct(string $fileName)
	{
		$this->fileName = $fileName;
		$this->rootElement = new \SimpleXMLElement('<phpcs/>');
		$this->rootElement->addAttribute('version', '1.0.0');
	}


	public function initialize(array $paths): void
	{
	}


	public function checkedFile(File $file): void
	{
		$issuesCount = $file->countIssues();
		if ($file->hasCheckError()) {
			$issuesCount++;
		}

		$fileElement = $this->rootElement->addChild('file');
		$fileElement->addAttribute('name', $file->getFileInfo()->getPathname());
		$fileElement->addAttribute('errors', (string) $issuesCount);
		$fileElement->addAttribute('warnings', '0');

		foreach ($file->getIssues() as $issue) {
			$issueElement = $fileElement->addChild('error', $issue->getMessage());
			$issueElement->addAttribute('line', (string) $issue->getLine());
			$issueElement->addAttribute('source', \get_class($issue->getCheck()));
			$issueElement->addAttribute('severity', '5');
		}

		if ($file->hasCheckError()) {
			$checkError = $file->getCheckError();
			\assert($checkError !== null);

			$errorElement = $fileElement->addChild('error', \sprintf(
				'Could not check further due to uncaught %s: %s',
				\get_class($checkError),
				$checkError->getMessage()
			));

			$errorElement->addAttribute('line', '0');
			$errorElement->addAttribute('source', \get_class($checkError));
			$errorElement->addAttribute('severity', '5');
		}
	}


	public function finish(CheckResult $result): void
	{
		$dom = new \DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;

		$xml = $this->rootElement->asXML();
		\assert(\is_string($xml));
		$dom->loadXML($xml);

		\file_put_contents($this->fileName, $dom->saveXML());
	}

}

<?php declare(strict_types = 1);

namespace Stylist\Checks\Files\Whitespace;

use Nette\Utils\Strings;
use Stylist\Checks\CheckInterface;
use Stylist\File;
use Stylist\Fixing\ChangeSet;
use Stylist\Tokenista\Query;
use Stylist\Tokenista\Token;
use Stylist\Utils\WhitespaceHelpers;


final class IndentationCheck implements CheckInterface
{

	private const TABS = 'tabs';
	private const SPACES = 'spaces';


	/** @var string */
	private $style;

	/** @var int */
	private $width;


	private function __construct(string $style = self::SPACES, int $width = 4)
	{
		$this->style = $style;
		$this->width = $width;
	}


	public static function tabs(int $width = 4): self
	{
		return new self(self::TABS, $width);
	}


	public static function spaces(int $width = 4): self
	{
		return new self(self::SPACES, $width);
	}


	public function check(File $file): void
	{
		$query = (new Query())
			->typeIs(\T_WHITESPACE)
			->valueLike(WhitespaceHelpers::INDENTATION_PATTERN);

		$tokens = $file->getTokens();
		foreach ($tokens->find($query) as $token) {
			if ($this->isWrong($token)) {
				$file->addIssue(
					$this,
					$this->formatErrorMessage(),
					$token->getLine() + WhitespaceHelpers::countNewLines($token->getValue()),
					function (ChangeSet $changeSet) use ($token): void {
						$newValue = Strings::replace(
							$token->getValue(),
							WhitespaceHelpers::INDENTATION_PATTERN,
							\Closure::fromCallable([$this, 'replaceCallback'])
						);

						\assert(\is_string($newValue));
						$changeSet->replaceToken($token, $newValue);
					}
				);
			}
		}
	}


	private function isWrong(Token $token): bool
	{
		$indentation = WhitespaceHelpers::extractIndentation($token);

		if ($this->style === self::TABS) {
			return Strings::contains($indentation, ' ');
		}

		return Strings::contains($indentation, "\t")
			|| (Strings::length($indentation) % $this->width) !== 0;
	}


	private function formatErrorMessage(): string
	{
		return \sprintf(
			'Wrong indentation found. %s must be used.',
			$this->style === self::TABS
				? 'Tabs'
				: \sprintf('Exactly %d spaces', $this->width)
		);
	}


	private function replaceCallback(array $matches): string
	{
		[, $indentation] = $matches;
		$numOfSpaces = \substr_count($indentation, ' ');
		$resultTabs = (int) \ceil($numOfSpaces / $this->width);

		if ($this->style === self::SPACES) {
			$resultTabs += \substr_count($indentation, "\t");
		}

		$replacement = $this->style === self::SPACES
			? \str_repeat(' ', $this->width)
			: "\t";

		return "\n" . \str_repeat($replacement, $resultTabs);
	}

}

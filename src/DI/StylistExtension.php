<?php declare(strict_types = 1);

namespace Stylist\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Stylist\Code\CodeParser;
use Stylist\Code\CodeTokenizer;
use Stylist\FileFactory;
use Stylist\Fixing\FileFixer;
use Stylist\Output\OutputInterface;
use Stylist\Stylist;


final class StylistExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$containerBuilder = $this->getContainerBuilder();

		$phpLexer = $containerBuilder->addDefinition($this->prefix('phpParser.lexer'))
			->setType(Lexer::class)
			->setFactory(Lexer::class, [[
				'usedAttributes' => [
					'comments',
					'startLine',
					'endLine',
					'startTokenPos',
					'endTokenPos',
				],
			]]);

		$containerBuilder->addDefinition($this->prefix('phpParser.parserFactory'))
			->setFactory(ParserFactory::class);

		$containerBuilder->addDefinition($this->prefix('phpParser.parser'))
			->setType(Parser::class)
			->setFactory(new Statement(
				$this->prefix('@phpParser.parserFactory::create'),
				[
					ParserFactory::PREFER_PHP7,
					$phpLexer,
				]
			));

		$containerBuilder->addDefinition($this->prefix('code.parser'))
			->setFactory(CodeParser::class);

		$containerBuilder->addDefinition($this->prefix('code.tokenizer'))
			->setFactory(CodeTokenizer::class);

		$containerBuilder->addDefinition($this->prefix('fixing.fileFixer'))
			->setFactory(FileFixer::class);

		$containerBuilder->addDefinition($this->prefix('fileFactory'))
			->setFactory(FileFactory::class);

		$containerBuilder->addImportedDefinition($this->prefix('output'))
			->setType(OutputInterface::class);

		$this->compiler->addExportedType(Stylist::class);
		$containerBuilder->addDefinition($this->prefix('stylist'))
			->setFactory(Stylist::class);
	}

}

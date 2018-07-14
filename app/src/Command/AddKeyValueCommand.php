<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\ConfigurableContentService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddKeyValueCommand extends Command
{
    private $configurableContentService;

    public function __construct(ConfigurableContentService $configurableContentService)
    {
        parent::__construct();
        $this->configurableContentService = $configurableContentService;
    }

    protected function configure()
    {
        $this
            ->setName('admin:key-value')
            ->setDescription('Adds content to the KV store')
            ->addArgument(
                'key',
                InputArgument::REQUIRED,
                'The key string'
            )
            ->addArgument(
                'value',
                InputArgument::REQUIRED,
                'The value string'
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $key = $input->getArgument('key');
        $value = $input->getArgument('value');

        $this->configurableContentService->addKeyValue($key, $value);

        $output->writeln('Done');
    }
}

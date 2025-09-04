<?php

namespace App\Command;

use App\Repository\LayoutRepository;
use App\Service\LayoutBuilderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:setup-page-layout',
    description: 'Set up default layout for Page entities',
)]
class SetupPageLayoutCommand extends Command
{
    public function __construct(
        private LayoutBuilderService $layoutBuilder,
        private LayoutRepository $layoutRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Check if default Page layout already exists
        $existingLayout = $this->layoutRepository->findDefaultForEntityType('Page');
        if ($existingLayout) {
            $io->note('Default Page layout already exists.');
            return Command::SUCCESS;
        }

        // Create default Page layout
        $pageLayout = $this->layoutBuilder->createLayout(
            'Default Page Layout',
            'Page',
            null,
            true
        );

        // Add blocks to Page layout
        $this->layoutBuilder->addBlockToLayout($pageLayout, 'text', [
            'title' => '{{page.title}}',
            'content' => 'Welcome to {{page.title}}! This page was created on {{page.created_at}}.',
            'css_class' => 'page-header'
        ]);

        $this->layoutBuilder->addBlockToLayout($pageLayout, 'text', [
            'title' => 'About This Page',
            'content' => '{{page.excerpt}}',
            'css_class' => 'page-content'
        ]);

        $io->success('Default Page layout has been created successfully!');
        $io->note('You can now create editorial pages and customize their layouts.');

        return Command::SUCCESS;
    }
}

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
    name: 'app:update-author-layout-text',
    description: 'Update the default Author layout to use Text Block with dynamic author name',
)]
class UpdateAuthorLayoutTextCommand extends Command
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

        // Find the default Author layout
        $authorLayout = $this->layoutRepository->findDefaultForEntityType('Author');
        
        if (!$authorLayout) {
            $io->error('Default Author layout not found. Please run app:setup-default-layouts first.');
            return Command::FAILURE;
        }

        // Clear existing blocks
        foreach ($authorLayout->getBlocks() as $block) {
            $authorLayout->removeBlock($block);
        }

        // Add a Text Block with dynamic author name
        $this->layoutBuilder->addBlockToLayout($authorLayout, 'text', [
            'title' => '{{author.name}}',
            'content' => 'Welcome to {{author.name}}\'s page! This author is from {{author.city}} and has written {{author.books_count}} books.',
            'css_class' => 'author-header'
        ]);

        // Add a related authors block
        $this->layoutBuilder->addBlockToLayout($authorLayout, 'author_list', [
            'limit' => 5,
            'show_books' => true,
            'css_class' => 'related-authors'
        ]);

        $io->success('Default Author layout has been updated successfully!');
        $io->note('The layout now uses a Text Block with dynamic placeholders for author information.');

        return Command::SUCCESS;
    }
}

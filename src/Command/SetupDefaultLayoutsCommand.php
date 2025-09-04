<?php

namespace App\Command;

use App\Service\LayoutBuilderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:setup-default-layouts',
    description: 'Set up default layouts for Author and Book entities',
)]
class SetupDefaultLayoutsCommand extends Command
{
    public function __construct(
        private LayoutBuilderService $layoutBuilder
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Create default Author layout
        $authorLayout = $this->layoutBuilder->createLayout(
            'Default Author Layout',
            'Author',
            null,
            true
        );

        // Add blocks to Author layout
        $this->layoutBuilder->addBlockToLayout($authorLayout, 'text', [
            'title' => 'Author Information',
            'content' => 'This is the default layout for author pages.',
            'css_class' => 'author-info'
        ]);

        $this->layoutBuilder->addBlockToLayout($authorLayout, 'author_list', [
            'limit' => 5,
            'show_books' => true,
            'css_class' => 'related-authors'
        ]);

        // Create default Book layout
        $bookLayout = $this->layoutBuilder->createLayout(
            'Default Book Layout',
            'Book',
            null,
            true
        );

        // Add blocks to Book layout
        $this->layoutBuilder->addBlockToLayout($bookLayout, 'text', [
            'title' => 'Book Details',
            'content' => 'This is the default layout for book pages.',
            'css_class' => 'book-info'
        ]);

        $this->layoutBuilder->addBlockToLayout($bookLayout, 'image', [
            'src' => 'https://via.placeholder.com/300x200?text=Book+Cover',
            'alt' => 'Book Cover',
            'caption' => 'Book cover image',
            'width' => '300px',
            'css_class' => 'book-cover'
        ]);

        $io->success('Default layouts have been created successfully!');
        $io->note('You can now customize these layouts in the Layout Builder interface.');

        return Command::SUCCESS;
    }
}

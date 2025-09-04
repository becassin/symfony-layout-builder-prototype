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
    name: 'app:update-author-layout',
    description: 'Update the default Author layout to use Author Info block',
)]
class UpdateAuthorLayoutCommand extends Command
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

        // Add the new Author Info block
        $this->layoutBuilder->addBlockToLayout($authorLayout, 'author_info', [
            'show_city' => true,
            'show_books' => true,
            'css_class' => 'author-details'
        ]);

        // Add a related authors block
        $this->layoutBuilder->addBlockToLayout($authorLayout, 'author_list', [
            'limit' => 5,
            'show_books' => true,
            'css_class' => 'related-authors'
        ]);

        $io->success('Default Author layout has been updated successfully!');
        $io->note('The layout now includes an Author Info block that displays the current author\'s name and details.');

        return Command::SUCCESS;
    }
}

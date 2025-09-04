<?php

namespace App\Block;

use App\Form\AuthorListBlockConfigurationType;
use App\Repository\AuthorRepository;

class AuthorListBlockType implements BlockTypeInterface
{
    public function __construct(
        private AuthorRepository $authorRepository
    ) {}

    public function getType(): string
    {
        return 'author_list';
    }

    public function getLabel(): string
    {
        return 'Author List Block';
    }

    public function getDescription(): string
    {
        return 'A block that displays a list of authors';
    }

    public function getConfigurationFormType(): string
    {
        return AuthorListBlockConfigurationType::class;
    }

    public function render(array $configuration, ?object $entity = null): string
    {
        $limit = $configuration['limit'] ?? 10;
        $showBooks = $configuration['show_books'] ?? false;
        $cssClass = $configuration['css_class'] ?? '';

        $authors = $this->authorRepository->findBy([], ['name' => 'ASC'], $limit);

        $html = '<div class="author-list-block ' . htmlspecialchars($cssClass) . '">';
        $html .= '<h3>Authors</h3>';
        $html .= '<div class="authors-list">';

        foreach ($authors as $author) {
            $html .= '<div class="author-item">';
            $html .= '<h4>' . htmlspecialchars($author->getName()) . '</h4>';
            $html .= '<p>City: ' . htmlspecialchars($author->getCity()) . '</p>';
            
            if ($showBooks && $author->getBooks()->count() > 0) {
                $html .= '<p>Books: ' . $author->getBooks()->count() . '</p>';
            }
            
            $html .= '</div>';
        }

        $html .= '</div></div>';

        return $html;
    }
}

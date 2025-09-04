<?php

namespace App\Block;

use App\Form\AuthorInfoBlockConfigurationType;

class AuthorInfoBlockType implements BlockTypeInterface
{
    public function getType(): string
    {
        return 'author_info';
    }

    public function getLabel(): string
    {
        return 'Author Info Block';
    }

    public function getDescription(): string
    {
        return 'A block that displays information about the current author';
    }

    public function getConfigurationFormType(): string
    {
        return AuthorInfoBlockConfigurationType::class;
    }

    public function render(array $configuration, ?object $entity = null): string
    {
        $showBooks = $configuration['show_books'] ?? true;
        $showCity = $configuration['show_city'] ?? true;
        $cssClass = $configuration['css_class'] ?? '';

        if (!$entity || !($entity instanceof \App\Entity\Author)) {
            return '<div class="author-info-block ' . htmlspecialchars($cssClass) . '"><p>No author information available.</p></div>';
        }

        $html = '<div class="author-info-block ' . htmlspecialchars($cssClass) . '">';
        $html .= '<h3>' . htmlspecialchars($entity->getName()) . '</h3>';
        
        if ($showCity) {
            $html .= '<p><strong>City:</strong> ' . htmlspecialchars($entity->getCity()) . '</p>';
        }
        
        if ($showBooks) {
            $bookCount = $entity->getBooks()->count();
            $html .= '<p><strong>Books:</strong> ' . $bookCount . '</p>';
            
            if ($bookCount > 0) {
                $html .= '<div class="author-books">';
                $html .= '<h4>Books by ' . htmlspecialchars($entity->getName()) . ':</h4>';
                $html .= '<ul class="list-group list-group-flush">';
                
                foreach ($entity->getBooks() as $book) {
                    $html .= '<li class="list-group-item">';
                    $html .= '<strong>' . htmlspecialchars($book->getTitle()) . '</strong> (' . $book->getYear() . ')';
                    $html .= '</li>';
                }
                
                $html .= '</ul></div>';
            }
        }
        
        $html .= '</div>';

        return $html;
    }
}

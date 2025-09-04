<?php

namespace App\Block;

use App\Form\TextBlockConfigurationType;

class TextBlockType implements BlockTypeInterface
{
    public function getType(): string
    {
        return 'text';
    }

    public function getLabel(): string
    {
        return 'Text Block';
    }

    public function getDescription(): string
    {
        return 'A simple text block with title and content';
    }

    public function getConfigurationFormType(): string
    {
        return TextBlockConfigurationType::class;
    }

    public function render(array $configuration, ?object $entity = null): string
    {
        $title = $configuration['title'] ?? '';
        $content = $configuration['content'] ?? '';
        $cssClass = $configuration['css_class'] ?? '';

        // Replace dynamic placeholders if entity is provided
        if ($entity) {
            if ($entity instanceof \App\Entity\Author) {
                $title = str_replace('{{author.name}}', $entity->getName(), $title);
                $title = str_replace('{{author.city}}', $entity->getCity(), $title);
                $content = str_replace('{{author.name}}', $entity->getName(), $content);
                $content = str_replace('{{author.city}}', $entity->getCity(), $content);
                $content = str_replace('{{author.books_count}}', $entity->getBooks()->count(), $content);
            } elseif ($entity instanceof \App\Entity\Page) {
                $title = str_replace('{{page.title}}', $entity->getTitle(), $title);
                $title = str_replace('{{page.slug}}', $entity->getSlug(), $title);
                $content = str_replace('{{page.title}}', $entity->getTitle(), $content);
                $content = str_replace('{{page.slug}}', $entity->getSlug(), $content);
                $content = str_replace('{{page.excerpt}}', $entity->getExcerpt() ?? '', $content);
                $content = str_replace('{{page.created_at}}', $entity->getCreatedAt()->format('Y-m-d'), $content);
            }
        }

        return sprintf(
            '<div class="text-block %s"><h3>%s</h3><div class="content">%s</div></div>',
            htmlspecialchars($cssClass),
            htmlspecialchars($title),
            nl2br(htmlspecialchars($content))
        );
    }
}

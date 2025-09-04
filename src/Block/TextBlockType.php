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

        return sprintf(
            '<div class="text-block %s"><h3>%s</h3><div class="content">%s</div></div>',
            htmlspecialchars($cssClass),
            htmlspecialchars($title),
            nl2br(htmlspecialchars($content))
        );
    }
}

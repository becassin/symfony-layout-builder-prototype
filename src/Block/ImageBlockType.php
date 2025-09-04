<?php

namespace App\Block;

use App\Form\ImageBlockConfigurationType;

class ImageBlockType implements BlockTypeInterface
{
    public function getType(): string
    {
        return 'image';
    }

    public function getLabel(): string
    {
        return 'Image Block';
    }

    public function getDescription(): string
    {
        return 'An image block with caption and styling options';
    }

    public function getConfigurationFormType(): string
    {
        return ImageBlockConfigurationType::class;
    }

    public function render(array $configuration, ?object $entity = null): string
    {
        $src = $configuration['src'] ?? '';
        $alt = $configuration['alt'] ?? '';
        $caption = $configuration['caption'] ?? '';
        $cssClass = $configuration['css_class'] ?? '';
        $width = $configuration['width'] ?? '';
        $height = $configuration['height'] ?? '';

        $style = '';
        if ($width) {
            $style .= "width: {$width};";
        }
        if ($height) {
            $style .= "height: {$height};";
        }

        $html = '<div class="image-block ' . htmlspecialchars($cssClass) . '">';
        $html .= '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($alt) . '"';
        if ($style) {
            $html .= ' style="' . $style . '"';
        }
        $html .= '>';
        
        if ($caption) {
            $html .= '<div class="caption">' . htmlspecialchars($caption) . '</div>';
        }
        
        $html .= '</div>';

        return $html;
    }
}

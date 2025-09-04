<?php

namespace App\Block;

interface BlockTypeInterface
{
    public function getType(): string;
    public function getLabel(): string;
    public function getDescription(): string;
    public function getConfigurationFormType(): string;
    public function render(array $configuration, ?object $entity = null): string;
}

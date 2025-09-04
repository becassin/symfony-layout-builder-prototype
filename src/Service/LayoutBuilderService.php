<?php

namespace App\Service;

use App\Block\BlockTypeInterface;
use App\Entity\Layout;
use App\Entity\LayoutBlock;
use App\Repository\LayoutRepository;
use Doctrine\ORM\EntityManagerInterface;

class LayoutBuilderService
{
    private array $blockTypes = [];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private LayoutRepository $layoutRepository
    ) {
        $this->registerBlockTypes();
    }

    public function registerBlockType(BlockTypeInterface $blockType): void
    {
        $this->blockTypes[$blockType->getType()] = $blockType;
    }

    public function getBlockTypes(): array
    {
        return $this->blockTypes;
    }

    public function getBlockType(string $type): ?BlockTypeInterface
    {
        return $this->blockTypes[$type] ?? null;
    }

    public function createLayout(string $name, ?string $entityType = null, ?int $entityId = null, bool $isDefault = false): Layout
    {
        $layout = new Layout();
        $layout->setName($name);
        $layout->setEntityType($entityType);
        $layout->setEntityId($entityId);
        $layout->setIsDefault($isDefault);

        $this->entityManager->persist($layout);
        $this->entityManager->flush();

        return $layout;
    }

    public function addBlockToLayout(Layout $layout, string $blockType, array $configuration = [], ?string $region = null): LayoutBlock
    {
        $block = new LayoutBlock();
        $block->setBlockType($blockType);
        $block->setConfiguration($configuration);
        $block->setRegion($region);
        $block->setPosition($layout->getBlocks()->count());

        $layout->addBlock($block);
        $this->entityManager->flush();

        return $block;
    }

    public function removeBlockFromLayout(LayoutBlock $block): void
    {
        $layout = $block->getLayout();
        $layout->removeBlock($block);
        $this->entityManager->remove($block);
        $this->entityManager->flush();
    }

    public function reorderBlocks(Layout $layout, array $blockIds): void
    {
        foreach ($blockIds as $position => $blockId) {
            $block = $this->entityManager->getRepository(LayoutBlock::class)->find($blockId);
            if ($block && $block->getLayout() === $layout) {
                $block->setPosition($position);
            }
        }
        $this->entityManager->flush();
    }

    public function renderLayout(Layout $layout, ?object $entity = null): string
    {
        $html = '<div class="layout-builder">';
        
        foreach ($layout->getBlocks() as $block) {
            $blockType = $this->getBlockType($block->getBlockType());
            if ($blockType) {
                $html .= '<div class="layout-block" data-block-id="' . $block->getId() . '">';
                $html .= $blockType->render($block->getConfiguration(), $entity);
                $html .= '</div>';
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }

    public function getLayoutForEntity(string $entityType, int $entityId): ?Layout
    {
        // First try to find a specific layout for this entity
        $layout = $this->layoutRepository->findForEntity($entityType, $entityId);
        
        // If not found, try to find the default layout for this entity type
        if (!$layout) {
            $layout = $this->layoutRepository->findDefaultForEntityType($entityType);
        }
        
        return $layout;
    }

    private function registerBlockTypes(): void
    {
        // This will be populated by the service configuration
    }
}

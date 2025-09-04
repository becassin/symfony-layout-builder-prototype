<?php

namespace App\Controller;

use App\Entity\Layout;
use App\Entity\LayoutBlock;
use App\Repository\LayoutRepository;
use App\Repository\LayoutBlockRepository;
use App\Service\LayoutBuilderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/layout')]
class LayoutController extends AbstractController
{
    #[Route('/', name: 'app_layout_index', methods: ['GET'])]
    public function index(LayoutRepository $layoutRepository): Response
    {
        return $this->render('layout/index.html.twig', [
            'layouts' => $layoutRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_layout_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LayoutBuilderService $layoutBuilder): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $entityType = $request->request->get('entityType');
            $entityId = $request->request->get('entityId');
            $isDefault = (bool) $request->request->get('isDefault');

            $layout = $layoutBuilder->createLayout($name, $entityType, $entityId ? (int) $entityId : null, $isDefault);

            return $this->redirectToRoute('app_layout_edit', ['id' => $layout->getId()]);
        }

        return $this->render('layout/new.html.twig', [
            'blockTypes' => $layoutBuilder->getBlockTypes(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_layout_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Layout $layout, LayoutBuilderService $layoutBuilder, LayoutBlockRepository $layoutBlockRepository): Response
    {
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            
            switch ($action) {
                case 'add_block':
                    $blockType = $request->request->get('blockType');
                    $configuration = json_decode($request->request->get('configuration', '{}'), true);
                    $region = $request->request->get('region');
                    
                    $layoutBuilder->addBlockToLayout($layout, $blockType, $configuration, $region);
                    break;
                    
                case 'remove_block':
                    $blockId = $request->request->get('blockId');
                    $block = $layoutBlockRepository->find($blockId);
                    if ($block) {
                        $layoutBuilder->removeBlockFromLayout($block);
                    }
                    break;
                    
                case 'reorder_blocks':
                    $blockIds = json_decode($request->request->get('blockIds', '[]'), true);
                    $layoutBuilder->reorderBlocks($layout, $blockIds);
                    break;
            }
            
            return $this->redirectToRoute('app_layout_edit', ['id' => $layout->getId()]);
        }

        return $this->render('layout/edit.html.twig', [
            'layout' => $layout,
            'blockTypes' => $layoutBuilder->getBlockTypes(),
        ]);
    }

    #[Route('/{id}', name: 'app_layout_show', methods: ['GET'])]
    public function show(Layout $layout, LayoutBuilderService $layoutBuilder): Response
    {
        return $this->render('layout/show.html.twig', [
            'layout' => $layout,
            'renderedLayout' => $layoutBuilder->renderLayout($layout),
        ]);
    }

    #[Route('/{id}/preview', name: 'app_layout_preview', methods: ['GET'])]
    public function preview(Layout $layout, LayoutBuilderService $layoutBuilder): Response
    {
        return new Response($layoutBuilder->renderLayout($layout));
    }

    #[Route('/block/{id}/configure', name: 'app_layout_block_configure', methods: ['GET', 'POST'])]
    public function configureBlock(Request $request, LayoutBlock $block, LayoutBuilderService $layoutBuilder, EntityManagerInterface $entityManager): Response
    {
        $blockType = $layoutBuilder->getBlockType($block->getBlockType());
        
        if (!$blockType) {
            throw $this->createNotFoundException('Block type not found');
        }

        $formType = $blockType->getConfigurationFormType();
        $form = $this->createForm($formType, $block->getConfiguration());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $block->setConfiguration($form->getData());
            $entityManager->flush();

            return $this->redirectToRoute('app_layout_edit', ['id' => $block->getLayout()->getId()]);
        }

        return $this->render('layout/configure_block.html.twig', [
            'block' => $block,
            'blockType' => $blockType,
            'form' => $form,
        ]);
    }
}

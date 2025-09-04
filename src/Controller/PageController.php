<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use App\Repository\LayoutRepository;
use App\Service\LayoutBuilderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/page')]
class PageController extends AbstractController
{
    #[Route('/', name: 'app_page_index', methods: ['GET'])]
    public function index(PageRepository $pageRepository): Response
    {
        return $this->render('page/index.html.twig', [
            'pages' => $pageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_page_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Auto-generate slug if empty
            if (empty($page->getSlug())) {
                $page->setSlug($slugger->slug($page->getTitle())->lower());
            }
            
            $page->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($page);
            $entityManager->flush();

            return $this->redirectToRoute('app_page_show', ['id' => $page->getId()]);
        }

        return $this->render('page/new.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_page_show', methods: ['GET'])]
    public function show(Page $page, LayoutBuilderService $layoutBuilder, LayoutRepository $layoutRepository): Response
    {
        $layout = $layoutBuilder->getLayoutForEntity('Page', $page->getId());
        
        // If no specific layout found, get the default layout
        if (!$layout) {
            $layout = $layoutRepository->findDefaultForEntityType('Page');
        }
        
        return $this->render('page/show.html.twig', [
            'page' => $page,
            'layout' => $layout,
            'renderedLayout' => $layout ? $layoutBuilder->renderLayout($layout, $page) : null,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Auto-generate slug if empty
            if (empty($page->getSlug())) {
                $page->setSlug($slugger->slug($page->getTitle())->lower());
            }
            
            $page->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            return $this->redirectToRoute('app_page_show', ['id' => $page->getId()]);
        }

        return $this->render('page/edit.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_page_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $entityManager->remove($page);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_page_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/slug/{slug}', name: 'app_page_public', methods: ['GET'])]
    public function public(Page $page, LayoutBuilderService $layoutBuilder, LayoutRepository $layoutRepository): Response
    {
        if (!$page->isPublished()) {
            throw $this->createNotFoundException('Page not found or not published');
        }

        $layout = $layoutBuilder->getLayoutForEntity('Page', $page->getId());
        
        // If no specific layout found, get the default layout
        if (!$layout) {
            $layout = $layoutRepository->findDefaultForEntityType('Page');
        }
        
        return $this->render('page/public.html.twig', [
            'page' => $page,
            'layout' => $layout,
            'renderedLayout' => $layout ? $layoutBuilder->renderLayout($layout, $page) : null,
        ]);
    }
}

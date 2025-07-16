<?php

declare(strict_types=1);

namespace Gally\SyliusPlugin\Controller\Shop;

use Gally\SyliusPlugin\Form\Type\SearchFormType;
use Gally\SyliusPlugin\Search\Finder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    public function __construct(private Finder $finder)
    {
    }

    public function renderForm(RequestStack $requestStack): Response
    {
        $query = $requestStack->getMainRequest()->get('query');
        // Try the search filter
        if (empty($query)) {
            $query = $requestStack->getMainRequest()->get('criteria', [])['search']['value'] ?? '';
        }

        $searchForm = $this->createForm(
            SearchFormType::class,
            ['query' => $query],
            [
                'action' => $this->generateUrl('gally_search_result_page'),
                'method' => 'POST',
            ]
        );

        return $this->render('@GallySyliusPlugin/shop/shared/components/header/search/form.html.twig', [
            'searchForm' => $searchForm->createView(),
        ]);
    }

    public function getResults(Request $request): Response
    {
        $searchForm = $this->createForm(SearchFormType::class);

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            return new RedirectResponse(
                $this->generateUrl('gally_search_result_page', ['query' => $searchForm->get('query')->getData()])
            );
        }

        return new RedirectResponse('/');
    }

    public function getPreview(Request $request): Response
    {
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $query = $searchForm->get('query')->getData();

            return new JsonResponse([
                'htmlResults' => $this->renderView(
                    '@GallySyliusPlugin/shop/shared/components/header/search/autocomplete/results.html.twig',
                    [
                        'products' => $this->finder->getAutocompleteResults($query),
                        'categories' => $this->finder->getAutocompleteResults($query, 'category', ['source', 'path', 'name']),
                        'view_all_results_link' => $this->generateUrl('gally_search_result_page', [
                            'query' => $query
                        ]),
                    ]
                )
            ]);
        }

        return new JsonResponse(['gallyError' => true]);
    }
}

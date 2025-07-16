<?php

namespace Gally\SyliusPlugin\Search;

use Gally\Sdk\Entity\Metadata;
use Gally\Sdk\GraphQl\Request;
use Gally\Sdk\Service\SearchManager;
use Gally\SyliusPlugin\Indexer\Provider\CatalogProvider;

/**
 * Perform search operations on Gally index and return array of Sylius products
 */
class Finder
{
    public const AUTOCOMPLETE_BATCH_SIZE = 6;

    public function __construct(
        private SearchManager $searchManager,
        private CatalogProvider $catalogProvider,
    ) {
    }

    public function getAutocompleteResults(string $query, string $metadata = 'product', array $fields = ['sku', 'source']): iterable
    {
        $request = new Request(
            $this->catalogProvider->getLocalizedCatalog(),
            new Metadata($metadata),
            true,
            $fields,
            1,
            self::AUTOCOMPLETE_BATCH_SIZE,
            null,
            $query,
            []
        );

        $response = $this->searchManager->search($request);

        return $response->getCollection();
    }
}
<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Gally to newer versions in the future.
 *
 * @package   Gally
 * @author    Stephan Hochdörfer <S.Hochdoerfer@bitexpert.de>, Gally Team <elasticsuite@smile.fr>
 * @copyright 2022-present Smile
 * @license   Open Software License v. 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Gally\SyliusPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

// @phpstan-ignore-next-line
trait GallyChannelTrait
{
    #[ORM\Column(name: 'gally_active', type: 'boolean')]
    protected $gallyActive = false;

    #[ORM\Column(name: 'gally_product_index_batch_size', type: 'integer')]
    protected $gallyProductIndexBatchSize = 50;

    #[ORM\Column(name: 'gally_category_index_batch_size', type: 'integer')]
    protected $gallyCategoryIndexBatchSize = 50;

    #[ORM\Column(name: 'gally_autocomplete_product_max_size', type: 'integer')]
    protected $gallyAutocompleteProductMaxSize = 6;

    #[ORM\Column(name: 'gally_autocomplete_category_max_size', type: 'integer')]
    protected $gallyAutocompleteCategoryMaxSize = 6;

    #[ORM\Column(name: 'gally_autocomplete_attribute_max_size', type: 'integer')]
    protected $gallyAutocompleteAttributeMaxSize = 6;

    public function getGallyActive(): bool
    {
        return $this->gallyActive;
    }

    public function setGallyActive(bool $isGallyActive): void
    {
        $this->gallyActive = $isGallyActive;
    }

    public function getGallyProductIndexBatchSize(): int
    {
        return $this->gallyProductIndexBatchSize;
    }

    public function setGallyProductIndexBatchSize(int $gallyProductIndexBatchSize): void
    {
        $this->gallyProductIndexBatchSize = $gallyProductIndexBatchSize;
    }

    public function getGallyCategoryIndexBatchSize(): int
    {
        return $this->gallyCategoryIndexBatchSize;
    }

    public function setGallyCategoryIndexBatchSize(int $gallyCategoryIndexBatchSize): void
    {
        $this->gallyCategoryIndexBatchSize = $gallyCategoryIndexBatchSize;
    }

    public function getGallyAutocompleteProductMaxSize(): int
    {
        return $this->gallyAutocompleteProductMaxSize;
    }

    public function setGallyAutocompleteProductMaxSize(int $gallyAutocompleteProductMaxSize): void
    {
        $this->gallyAutocompleteProductMaxSize = $gallyAutocompleteProductMaxSize;
    }

    public function getGallyAutocompleteCategoryMaxSize(): int
    {
        return $this->gallyAutocompleteCategoryMaxSize;
    }

    public function setGallyAutocompleteCategoryMaxSize(int $gallyAutocompleteCategoryMaxSize): void
    {
        $this->gallyAutocompleteCategoryMaxSize = $gallyAutocompleteCategoryMaxSize;
    }

    public function getGallyAutocompleteAttributeMaxSize(): int
    {
        return $this->gallyAutocompleteAttributeMaxSize;
    }

    public function setGallyAutocompleteAttributeMaxSize(int $gallyAutocompleteAttributeMaxSize): void
    {
        $this->gallyAutocompleteAttributeMaxSize = $gallyAutocompleteAttributeMaxSize;
    }
}

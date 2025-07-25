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

namespace Gally\SyliusPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250725112500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Add autocomplete columns in 'sylius_channel' table.";
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel ADD gally_autocomplete_product_max_size INT NOT NULL DEFAULT 6, ADD gally_autocomplete_category_max_size INT NOT NULL DEFAULT 6, ADD gally_autocomplete_attribute_max_size INT NOT NULL DEFAULT 6');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel DROP gally_autocomplete_product_max_size, DROP gally_autocomplete_category_max_size, DROP gally_autocomplete_attribute_max_size');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251015101900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add product-level SEO template fields to sylius_taxon_translation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE sylius_taxon_translation "
            . "ADD product_meta_title_template VARCHAR(255) DEFAULT NULL, "
            . "ADD product_meta_description_template TEXT DEFAULT NULL, "
            . "ADD product_og_title_template VARCHAR(255) DEFAULT NULL, "
            . "ADD product_og_description_template TEXT DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE sylius_taxon_translation "
            . "DROP product_meta_title_template, "
            . "DROP product_meta_description_template, "
            . "DROP product_og_title_template, "
            . "DROP product_og_description_template");
    }
}

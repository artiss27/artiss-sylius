<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251014220500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Extend sylius_taxon and sylius_taxon_translation with merchandising/seo and presentation fields';
    }

    public function up(Schema $schema): void
    {
        // sylius_taxon additions
        $this->addSql("ALTER TABLE sylius_taxon ADD visible_in_menu BOOLEAN DEFAULT 1 NOT NULL");
        $this->addSql("ALTER TABLE sylius_taxon ADD template VARCHAR(64) DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon ADD seo_data JSON DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon ADD merchandising JSON DEFAULT NULL");

        // sylius_taxon_translation additions
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD h1 VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD short_description TEXT DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD seo_text_top TEXT DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD seo_text_bottom TEXT DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD meta_title VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD meta_description TEXT DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD breadcrumbs_title VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD badge VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD meta_title_template VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD meta_description_template TEXT DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD og_title_template VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE sylius_taxon_translation ADD og_description_template TEXT DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        // sylius_taxon revert
        $this->addSql("ALTER TABLE sylius_taxon DROP visible_in_menu");
        $this->addSql("ALTER TABLE sylius_taxon DROP template");
        $this->addSql("ALTER TABLE sylius_taxon DROP seo_data");
        $this->addSql("ALTER TABLE sylius_taxon DROP merchandising");

        // sylius_taxon_translation revert
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP h1");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP short_description");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP seo_text_top");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP seo_text_bottom");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP meta_title");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP meta_description");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP breadcrumbs_title");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP badge");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP meta_title_template");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP meta_description_template");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP og_title_template");
        $this->addSql("ALTER TABLE sylius_taxon_translation DROP og_description_template");
    }
}

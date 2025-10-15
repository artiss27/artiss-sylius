<?php

declare(strict_types=1);

namespace App\Entity\Taxonomy;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_taxon')]
class Taxon extends BaseTaxon
{
    #[ORM\Column(name: 'visible_in_menu', type: 'boolean', options: ['default' => true])]
    private bool $visibleInMenu = true;

    #[ORM\Column(name: 'template', type: 'string', length: 64, nullable: true)]
    private ?string $template = null;

    #[ORM\Column(name: 'seo_data', type: 'json', nullable: true)]
    private ?array $seoData = null;

    #[ORM\Column(name: 'merchandising', type: 'json', nullable: true)]
    private ?array $merchandising = null;

    protected function createTranslation(): TaxonTranslationInterface
    {
        return new TaxonTranslation();
    }

    public function isVisibleInMenu(): bool
    {
        return $this->visibleInMenu;
    }

    public function setVisibleInMenu(bool $visibleInMenu): void
    {
        $this->visibleInMenu = $visibleInMenu;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): void
    {
        $this->template = $template;
    }

    public function getSeoData(): ?array
    {
        return $this->seoData;
    }

    public function setSeoData(?array $seoData): void
    {
        $this->seoData = $seoData;
    }

    public function getMerchandising(): ?array
    {
        return $this->merchandising;
    }

    public function setMerchandising(?array $merchandising): void
    {
        $this->merchandising = $merchandising;
    }
}

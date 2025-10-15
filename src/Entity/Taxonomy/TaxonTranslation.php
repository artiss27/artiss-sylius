<?php

declare(strict_types=1);

namespace App\Entity\Taxonomy;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Taxonomy\Model\TaxonTranslation as BaseTaxonTranslation;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_taxon_translation')]
class TaxonTranslation extends BaseTaxonTranslation
{
    #[ORM\Column(name: 'h1', type: 'string', length: 255, nullable: true)]
    private ?string $h1 = null;

    #[ORM\Column(name: 'short_description', type: 'text', nullable: true)]
    private ?string $shortDescription = null;

    #[ORM\Column(name: 'seo_text_top', type: 'text', nullable: true)]
    private ?string $seoTextTop = null;

    #[ORM\Column(name: 'seo_text_bottom', type: 'text', nullable: true)]
    private ?string $seoTextBottom = null;

    #[ORM\Column(name: 'meta_title', type: 'string', length: 255, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(name: 'meta_description', type: 'text', nullable: true)]
    private ?string $metaDescription = null;

    #[ORM\Column(name: 'breadcrumbs_title', type: 'string', length: 255, nullable: true)]
    private ?string $breadcrumbsTitle = null;

    #[ORM\Column(name: 'badge', type: 'string', length: 255, nullable: true)]
    private ?string $badge = null;

    #[ORM\Column(name: 'meta_title_template', type: 'string', length: 255, nullable: true)]
    private ?string $metaTitleTemplate = null;

    #[ORM\Column(name: 'meta_description_template', type: 'text', nullable: true)]
    private ?string $metaDescriptionTemplate = null;

    #[ORM\Column(name: 'og_title_template', type: 'string', length: 255, nullable: true)]
    private ?string $ogTitleTemplate = null;

    #[ORM\Column(name: 'og_description_template', type: 'text', nullable: true)]
    private ?string $ogDescriptionTemplate = null;

    public function getH1(): ?string { return $this->h1; }
    public function setH1(?string $h1): void { $this->h1 = $h1; }

    public function getShortDescription(): ?string { return $this->shortDescription; }
    public function setShortDescription(?string $shortDescription): void { $this->shortDescription = $shortDescription; }

    public function getSeoTextTop(): ?string { return $this->seoTextTop; }
    public function setSeoTextTop(?string $seoTextTop): void { $this->seoTextTop = $seoTextTop; }

    public function getSeoTextBottom(): ?string { return $this->seoTextBottom; }
    public function setSeoTextBottom(?string $seoTextBottom): void { $this->seoTextBottom = $seoTextBottom; }

    public function getMetaTitle(): ?string { return $this->metaTitle; }
    public function setMetaTitle(?string $metaTitle): void { $this->metaTitle = $metaTitle; }

    public function getMetaDescription(): ?string { return $this->metaDescription; }
    public function setMetaDescription(?string $metaDescription): void { $this->metaDescription = $metaDescription; }

    public function getBreadcrumbsTitle(): ?string { return $this->breadcrumbsTitle; }
    public function setBreadcrumbsTitle(?string $breadcrumbsTitle): void { $this->breadcrumbsTitle = $breadcrumbsTitle; }

    public function getBadge(): ?string { return $this->badge; }
    public function setBadge(?string $badge): void { $this->badge = $badge; }

    public function getMetaTitleTemplate(): ?string { return $this->metaTitleTemplate; }
    public function setMetaTitleTemplate(?string $metaTitleTemplate): void { $this->metaTitleTemplate = $metaTitleTemplate; }

    public function getMetaDescriptionTemplate(): ?string { return $this->metaDescriptionTemplate; }
    public function setMetaDescriptionTemplate(?string $metaDescriptionTemplate): void { $this->metaDescriptionTemplate = $metaDescriptionTemplate; }

    public function getOgTitleTemplate(): ?string { return $this->ogTitleTemplate; }
    public function setOgTitleTemplate(?string $ogTitleTemplate): void { $this->ogTitleTemplate = $ogTitleTemplate; }

    public function getOgDescriptionTemplate(): ?string { return $this->ogDescriptionTemplate; }
    public function setOgDescriptionTemplate(?string $ogDescriptionTemplate): void { $this->ogDescriptionTemplate = $ogDescriptionTemplate; }
}

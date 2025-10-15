<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonTranslationType as BaseTaxonTranslationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class TaxonTranslationTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [BaseTaxonTranslationType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('h1', TextType::class, [
                'required' => false,
                'label' => 'H1 title',
                'help' => 'If empty, the category name will be used as H1.',
            ])
            ->add('shortDescription', TextareaType::class, [
                'required' => false,
                'label' => 'Short description',
                'attr' => ['rows' => 3],
            ])
            ->add('seoTextTop', TextareaType::class, [
                'required' => false,
                'label' => 'SEO text (top)',
                'attr' => ['rows' => 6],
            ])
            ->add('seoTextBottom', TextareaType::class, [
                'required' => false,
                'label' => 'SEO text (bottom)',
                'attr' => ['rows' => 10],
            ])
            ->add('metaTitle', TextType::class, [
                'required' => false,
                'label' => 'Meta title',
            ])
            ->add('metaDescription', TextareaType::class, [
                'required' => false,
                'label' => 'Meta description',
                'attr' => ['rows' => 4],
            ])
            ->add('breadcrumbsTitle', TextType::class, [
                'required' => false,
                'label' => 'Breadcrumbs title',
                'help' => 'If empty, the category name will be used in breadcrumbs.',
            ])
            ->add('badge', TextType::class, [
                'required' => false,
                'label' => 'Badge label',
                'help' => 'Short label like “Новинки”, “Розпродаж”.',
            ])
            ->add('metaTitleTemplate', TextType::class, [
                'required' => false,
                'label' => 'Meta title template',
                'help' => 'Placeholders: {{ category_name }}, {{ brand }}, {{ page }}',
            ])
            ->add('metaDescriptionTemplate', TextareaType::class, [
                'required' => false,
                'label' => 'Meta description template',
                'attr' => ['rows' => 4],
                'help' => 'Placeholders: {{ category_name }}, {{ brand }}, {{ page }}',
            ])
            ->add('ogTitleTemplate', TextType::class, [
                'required' => false,
                'label' => 'OG title template',
            ])
            ->add('ogDescriptionTemplate', TextareaType::class, [
                'required' => false,
                'label' => 'OG description template',
                'attr' => ['rows' => 4],
            ])
            ->add('productMetaTitleTemplate', TextType::class, [
                'required' => false,
                'label' => 'Product meta title template',
                'help' => 'Placeholders: {{ product_name }}, {{ brand }}, {{ category_name }}, {{ price }}, {{ sku }}, {{ page }}',
            ])
            ->add('productMetaDescriptionTemplate', TextareaType::class, [
                'required' => false,
                'label' => 'Product meta description template',
                'attr' => ['rows' => 4],
                'help' => 'Placeholders: {{ product_name }}, {{ brand }}, {{ category_name }}, {{ price }}, {{ sku }}, {{ page }}',
            ])
            ->add('productOgTitleTemplate', TextType::class, [
                'required' => false,
                'label' => 'Product OG title template',
            ])
            ->add('productOgDescriptionTemplate', TextareaType::class, [
                'required' => false,
                'label' => 'Product OG description template',
                'attr' => ['rows' => 4],
            ])
        ;
    }
}

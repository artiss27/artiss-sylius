<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\AdminBundle\Form\Type\TaxonType as BaseTaxonType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class TaxonTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [BaseTaxonType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Global presentation
        $builder->add('visibleInMenu', CheckboxType::class, [
            'required' => false,
            'label' => 'Visible in menu',
            'help' => 'If disabled, category stays accessible by URL but is hidden from menus/navigation.',
        ]);

        $builder->add('template', ChoiceType::class, [
            'required' => false,
            'placeholder' => 'Default',
            'label' => 'Category template',
            'choices' => [
                'Default' => 'default',
                'Promo' => 'promo',
                'Landing (wide)' => 'landing_wide',
            ],
            'help' => 'Choose a layout variant for this category page. Empty = default.',
        ]);

        // JSON fields as textareas with (de)serializing transformers
        $builder->add('seoData', TextareaType::class, [
            'required' => false,
            'label' => 'SEO data (JSON)',
            'attr' => ['rows' => 8, 'spellcheck' => 'false', 'class' => 'code'],
            'help' => 'Keys: robots_index, robots_follow, canonical_policy, canonical_custom_url, indexable_facets, extra_meta',
        ]);
        $builder->add('merchandising', TextareaType::class, [
            'required' => false,
            'label' => 'Merchandising (JSON)',
            'attr' => ['rows' => 10, 'spellcheck' => 'false', 'class' => 'code'],
            'help' => 'Keys: default_sort, allowed_sorts, facets[], pagination, experiments',
        ]);

        // Add model transformers to convert array<->json string
        $this->attachJsonTransformer($builder, 'seoData');
        $this->attachJsonTransformer($builder, 'merchandising');
    }

    private function attachJsonTransformer(FormBuilderInterface $builder, string $fieldName): void
    {
        if (!$builder->has($fieldName)) {
            return;
        }

        $builder->get($fieldName)->addModelTransformer(new CallbackTransformer(
            function ($value) {
                // array|null -> json string
                if ($value === null || $value === '') {
                    return '';
                }
                if (is_string($value)) {
                    return $value; // already a string (edge cases)
                }
                try {
                    return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                } catch (\Throwable $e) {
                    return '';
                }
            },
            function ($value) {
                // json string -> array|null
                $trimmed = is_string($value) ? trim($value) : '';
                if ($trimmed === '') {
                    return null;
                }
                $decoded = json_decode($trimmed, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new TransformationFailedException('Invalid JSON: ' . json_last_error_msg());
                }
                return $decoded;
            }
        ));

        // Add submit listener to convert TransformationFailedException into form error (nicer message)
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($fieldName) {
            $form = $event->getForm();
            if ($form->has($fieldName)) {
                $field = $form->get($fieldName);
                $viewData = $field->getViewData();
                if (is_string($viewData) && $viewData !== '') {
                    json_decode($viewData, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $field->addError(new FormError('Invalid JSON: ' . json_last_error_msg()));
                    }
                }
            }
        });
    }
}

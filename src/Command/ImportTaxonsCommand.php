<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Taxonomy\Taxon;
use App\Entity\Taxonomy\TaxonTranslation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import:taxons', description: 'Import or update taxons (categories) from a CSV file')]
final class ImportTaxonsCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::OPTIONAL, 'Path to CSV file', 'etc/import/taxons.csv')
            ->addOption('locale', 'l', InputOption::VALUE_REQUIRED, 'Locale for translations', 'en_US')
            ->addOption('delimiter', 'd', InputOption::VALUE_REQUIRED, 'CSV delimiter (, or ;)', ',')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        $locale = (string) $input->getOption('locale');
        $delimiter = (string) $input->getOption('delimiter');

        if (!is_file($file)) {
            $output->writeln(sprintf('<error>File not found: %s</error>', $file));
            return Command::FAILURE;
        }

        $fh = fopen($file, 'rb');
        if ($fh === false) {
            $output->writeln(sprintf('<error>Cannot open file: %s</error>', $file));
            return Command::FAILURE;
        }

        $headers = fgetcsv($fh, 0, $delimiter);
        if ($headers === false) {
            $output->writeln('<error>CSV appears empty</error>');
            return Command::FAILURE;
        }
        $headers = array_map(static fn($h) => trim((string) $h), $headers);

        $required = ['code', 'name'];
        foreach ($required as $req) {
            if (!in_array($req, $headers, true)) {
                $output->writeln(sprintf('<error>CSV missing required column: %s</error>', $req));
                return Command::FAILURE;
            }
        }

        $index = array_flip($headers);

        $rows = [];
        while (($row = fgetcsv($fh, 0, $delimiter)) !== false) {
            if (count($row) === 1 && trim($row[0]) === '') { continue; }
            $rows[] = $row;
        }
        fclose($fh);

        // First pass: upsert all taxons without parenting
        $repo = $this->em->getRepository(Taxon::class);
        $byCode = [];

        $output->writeln(sprintf('<info>Upserting %d taxons…</info>', count($rows)));
        $progress = new ProgressBar($output, count($rows));
        $progress->start();

        $i = 0;
        foreach ($rows as $row) {
            $code = trim((string) ($row[$index['code']] ?? ''));
            if ($code === '') { continue; }

            /** @var Taxon|null $taxon */
            $taxon = $repo->findOneBy(['code' => $code]);
            if ($taxon === null) {
                $taxon = new Taxon();
                $taxon->setCode($code);
                $this->em->persist($taxon);
            }

            // Enabled
            if (isset($index['enabled'])) {
                $enabled = (string) ($row[$index['enabled']] ?? '1');
                $taxon->setEnabled(in_array(strtolower($enabled), ['1', 'true', 'yes', 'y'], true));
            }

            // Position
            if (isset($index['position'])) {
                $pos = (int) ($row[$index['position']] ?? 0);
                $taxon->setPosition($pos);
            }

            // Visible in menu
            if (isset($index['visible_in_menu'])) {
                $vim = (string) ($row[$index['visible_in_menu']] ?? '1');
                $taxon->setVisibleInMenu(in_array(strtolower($vim), ['1', 'true', 'yes', 'y'], true));
            }

            // Template
            if (isset($index['template'])) {
                $tpl = trim((string) ($row[$index['template']] ?? ''));
                $taxon->setTemplate($tpl !== '' ? $tpl : null);
            }

            // JSON columns (seo_data, merchandising)
            if (isset($index['seo_data'])) {
                $json = trim((string) ($row[$index['seo_data']] ?? ''));
                $taxon->setSeoData($json !== '' ? json_decode($json, true) ?: null : null);
            }
            if (isset($index['merchandising'])) {
                $json = trim((string) ($row[$index['merchandising']] ?? ''));
                $taxon->setMerchandising($json !== '' ? json_decode($json, true) ?: null : null);
            }

            // Translation
            $name = (string) ($row[$index['name']] ?? '');
            $slug = isset($index['slug']) ? trim((string) ($row[$index['slug']] ?? '')) : '';

            /** @var TaxonTranslation $translation */
            $translation = $taxon->getTranslation($locale);
            $translation->setLocale($locale);
            $translation->setName($name);
            if ($slug !== '') { $translation->setSlug($slug); }

            // Optional localized fields
            $setIf = static function (?string $value, callable $setter): void {
                if ($value !== null && trim($value) !== '') { $setter($value); }
            };

            $setIf(isset($index['h1']) ? (string) ($row[$index['h1']] ?? null) : null, [$translation, 'setH1']);
            $setIf(isset($index['short_description']) ? (string) ($row[$index['short_description']] ?? null) : null, [$translation, 'setShortDescription']);
            $setIf(isset($index['seo_text_top']) ? (string) ($row[$index['seo_text_top']] ?? null) : null, [$translation, 'setSeoTextTop']);
            $setIf(isset($index['seo_text_bottom']) ? (string) ($row[$index['seo_text_bottom']] ?? null) : null, [$translation, 'setSeoTextBottom']);
            $setIf(isset($index['meta_title']) ? (string) ($row[$index['meta_title']] ?? null) : null, [$translation, 'setMetaTitle']);
            $setIf(isset($index['meta_description']) ? (string) ($row[$index['meta_description']] ?? null) : null, [$translation, 'setMetaDescription']);
            $setIf(isset($index['breadcrumbs_title']) ? (string) ($row[$index['breadcrumbs_title']] ?? null) : null, [$translation, 'setBreadcrumbsTitle']);
            $setIf(isset($index['badge']) ? (string) ($row[$index['badge']] ?? null) : null, [$translation, 'setBadge']);
            $setIf(isset($index['meta_title_template']) ? (string) ($row[$index['meta_title_template']] ?? null) : null, [$translation, 'setMetaTitleTemplate']);
            $setIf(isset($index['meta_description_template']) ? (string) ($row[$index['meta_description_template']] ?? null) : null, [$translation, 'setMetaDescriptionTemplate']);
            $setIf(isset($index['og_title_template']) ? (string) ($row[$index['og_title_template']] ?? null) : null, [$translation, 'setOgTitleTemplate']);
            $setIf(isset($index['og_description_template']) ? (string) ($row[$index['og_description_template']] ?? null) : null, [$translation, 'setOgDescriptionTemplate']);

            // Product-level SEO templates
            $setIf(isset($index['product_meta_title_template']) ? (string) ($row[$index['product_meta_title_template']] ?? null) : null, [$translation, 'setProductMetaTitleTemplate']);
            $setIf(isset($index['product_meta_description_template']) ? (string) ($row[$index['product_meta_description_template']] ?? null) : null, [$translation, 'setProductMetaDescriptionTemplate']);
            $setIf(isset($index['product_og_title_template']) ? (string) ($row[$index['product_og_title_template']] ?? null) : null, [$translation, 'setProductOgTitleTemplate']);
            $setIf(isset($index['product_og_description_template']) ? (string) ($row[$index['product_og_description_template']] ?? null) : null, [$translation, 'setProductOgDescriptionTemplate']);

            $byCode[$code] = $taxon;

            if ((++$i % 100) === 0) {
                $this->em->flush();
                $this->em->clear();
                // refresh repository map
                $repo = $this->em->getRepository(Taxon::class);
            }
            $progress->advance();
        }
        $this->em->flush();
        $progress->finish();
        $output->writeln('');

        // Second pass: set parents
        $output->writeln('<info>Linking parents…</info>');
        $fh = fopen($file, 'rb');
        fgetcsv($fh, 0, $delimiter); // skip header
        $linked = 0; $skipped = 0;
        while (($row = fgetcsv($fh, 0, $delimiter)) !== false) {
            $code = trim((string) ($row[$index['code']] ?? ''));
            if ($code === '') { continue; }
            /** @var Taxon|null $taxon */
            $taxon = $this->em->getRepository(Taxon::class)->findOneBy(['code' => $code]);
            if (!$taxon) { $skipped++; continue; }

            $parentCode = isset($index['parent_code']) ? trim((string) ($row[$index['parent_code']] ?? '')) : '';
            if ($parentCode !== '') {
                /** @var Taxon|null $parent */
                $parent = $this->em->getRepository(Taxon::class)->findOneBy(['code' => $parentCode]);
                if ($parent) {
                    $taxon->setParent($parent);
                    $linked++;
                }
            }
        }
        fclose($fh);
        $this->em->flush();

        $output->writeln(sprintf('<info>Done.</info> Linked parents: %d; skipped: %d', $linked, $skipped));

        return Command::SUCCESS;
    }
}

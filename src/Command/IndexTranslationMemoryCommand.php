<?php

namespace App\Command;

use App\Entity\ContentRevision;
use App\Entity\Language;
use App\Entity\Product;
use App\Entity\Project;
use App\Repository\ContentRevisionRepository;
use App\TranslationMemory\ProjectIndexer;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IndexTranslationMemoryCommand extends Command
{
    private $projectIndexer;
    private $registry;

    public function __construct(ProjectIndexer $projectIndexer, ManagerRegistry $registry)
    {
        parent::__construct();

        $this->projectIndexer = $projectIndexer;
        $this->registry = $registry;
    }

    public function configure()
    {
        $this
            ->setName('adventist-commons:translation-memory:index-project')
            ->addArgument(
                'product-code',
                InputArgument::REQUIRED,
                'The code of the product to index'
            )
            ->addArgument(
                'language-code',
                InputArgument::REQUIRED,
                'The code of the translated language to index (i.e. "arb" for arabic)'
            )
            ->setHelp(
                <<<'EOT'
                This command launch the indexation in translation memory for a specific project.
                Only validated projects can be indexed in translation memory
EOT
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Product $product */
        $product = $this->registry
            ->getRepository(Product::class)
            ->findOneBy(['slug' => $input->getArgument('product-code')]);
        if (!$product) {
            $output->writeln('Cannot find product');
            return 1;
        }
        $language = $this->registry
            ->getRepository(Language::class)
            ->findOneBy(['code' => $input->getArgument('language-code')]);
        if (!$language) {
            $output->writeln('Cannot find language');
            return 1;
        }
        /** @var Project $project */
        $project = $this->registry
            ->getRepository(Project::class)
            ->findOneBy([
                'product' => $product,
                'language' => $language,
            ]);
        if (!$project) {
            $languages = [];
            foreach ($product->getProjects() as $project) {
                $languages[] = $project->getLanguage()->getCode();
            }
            $output->writeln(sprintf(
                'There is no translation in that language for the product. Available languages are %s',
                implode(', ', $languages)
            ));
            return 1;
        }

        $this->projectIndexer->index($project);
    }
}

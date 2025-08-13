<?php

namespace App\Command;

namespace App\Command;

use App\Entity\Element;
use App\Entity\Exercise;
use App\Entity\ExerciseCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Symfony\Component\String\s;

#[AsCommand(
    name: 'app:database:import',
    description: 'Import exercices, courses, ... from file'
)]
class DatabaseImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'File to import')
            ->addArgument('type', InputArgument::REQUIRED, 'Data type (string)')
            ->addArgument('clear', InputArgument::OPTIONAL, 'If category should be cleared');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = $input->getArgument('file');
        $type = $input->getArgument('type');
        $clear = $input->getArgument('clear') ?? false;

        if (!file_exists($path)) {
            $io->error("'$path' doesn't exist.");
            return Command::FAILURE;
        }

        if (preg_match('/^exercises:file:(\d+)$/', $type, $matches)) {
            if (!is_readable($path)) {
                $io->error("File '$path' isn't readable.");
                return Command::FAILURE;
            }

            $content = file_get_contents($path);

            $categoryId = (int) $matches[1];

            /** @var ExerciseCategory|null $exercise */
            $category = $this->entityManager->getRepository(ExerciseCategory::class)->find($categoryId);

            if (!$category) {
                $io->error("No catégory found with ID $categoryId.");
                return Command::FAILURE;
            }

            $this->importExercises($category, $content, $clear, $io);
            $this->entityManager->flush();
        } if (preg_match('/^exercises:dir:(\d+):(#[0-9A-Fa-f]{6})$/', $type, $matches)) {
            if (!is_dir($path)) {
                $io->error("'$path' isn't a directory.");
                return Command::FAILURE;
            }

            $categoryId = (int) $matches[1];
            $color = $matches[2];

            /** @var ExerciseCategory|null $exercise */
            $category = $this->entityManager->getRepository(ExerciseCategory::class)->find($categoryId);

            if (!$category) {
                $io->error("No category found with ID $categoryId.");
                return Command::FAILURE;
            }

            $removeCategoryRec = function (ExerciseCategory $category) use (&$removeCategoryRec) {
                foreach ($category->getChildren() as $child) {
                    $removeCategoryRec($child);
                }

                foreach ($category->getExercises() as $exercise) {
                    $this->removeExercise($exercise);
                }

                $this->entityManager->remove($category);
            };

            if ($clear) {
                foreach ($category->getChildren() as $child) {
                    $removeCategoryRec($child);
                }

                foreach ($category->getExercises() as $exercise) {
                    $this->removeExercise($exercise);
                }
            }

            $tds = [];

            foreach (scandir($path) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $fullPath = $path . DIRECTORY_SEPARATOR . $file;

                if (is_file($fullPath) && preg_match('/TD([0-9]+)_.*\.tex$/', $fullPath, $matches)) {
                    $fileContent = file_get_contents($fullPath);

                    if (preg_match('/\\\\chapter\{([^}]*)}/', $fileContent, $matchesIn)) {
                        $number = (int) $matches[1];
                        $chapterName = $matchesIn[1];

                        $tds[$number] = [
                            'name' => $chapterName,
                            'content' => $fileContent,
                        ];
                    }
                }
            }

            ksort($tds);

            foreach ($tds as $number => $td) {
                $subCategory = new ExerciseCategory();
                $subCategory->setName('TD n°'.$number.' : '.$td['name']);
                $subCategory->setColor($color); // for tds : '#75b7e8', for ds: '#fc6262'
                $subCategory->setParent($category);
                $subCategory->setSubject($category->getSubject());
                $subCategory->setSortNumber($number);
                $this->entityManager->persist($subCategory);

                $this->importExercises($subCategory, $td['content'], false, $io);
            }

            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }

    private function importExercises($category, $content, $clear, $io): void
    {
        // 1. Delete all correction blocks
        $content = preg_replace('/\\\\begin\{corr\}.*?\\\\end\{corr\}/s', '', $content);

        // 2. Extract each exercise block
        if (preg_match_all('/\\\\begin\{exo\}.*?\\\\end\{exo\}/s', $content, $matches)) {
            if ($clear) {
                foreach ($category->getExercises() as $exercise) {
                    foreach ($exercise->getExercisePrefs() as $pref) {
                        $this->entityManager->remove($pref);
                    }

                    $this->entityManager->remove($exercise);
                }
            }

            foreach ($matches[0] as $exoBlock) {
                $converted = $this->convert($exoBlock);

                $exercise = new Exercise();
                $exercise->setCategory($category);
                $exercise->setSortNumber($converted['number']);
                $exercise->setTitle($converted['title']);

                $statement = new Element();
                $statement->setContent($converted['content']);
                $exercise->setStatement($statement);

                $solution = new Element();
                $solution->setContent('');
                $exercise->setSolution($solution);

                $this->entityManager->persist($solution);
                $this->entityManager->persist($statement);
                $this->entityManager->persist($exercise);
            }
        } else {
            $io->warning("No exercise found.");
        }
    }

    private function convert(string $exoBlock): array
    {
        // 1. Extract title and number
        $title = '';
        $number = 0;
        if (preg_match('/\\\\begin\{exo\}\[(Exercice ([0-9]+).*?)\]/', $exoBlock, $titleMatch)) {
            $title = trim($titleMatch[1]);
            $number = (int) $titleMatch[2];
        }

        // 2. Remove \begin{exo}...\end{exo}
        $exoContent = preg_replace('/\\\\begin\{exo\}(\[.*?\])?/', '', $exoBlock);
        $exoContent = preg_replace('/\\\\end\{exo\}/', '', $exoContent);

        // 3. LaTeX → MathJax
        $exoContent = str_replace(['\\(', '\\)'], '$', $exoContent);
        $exoContent = str_replace(['\\[', '\\]'], '$$', $exoContent);

        // 4. Macros
        $replacements = [
            '\\croch' => '\\cro',
            '\\paren' => '\\par',
            '\\ssi'   => '\\iff',
            '\\imp'   => '\\implies',
            '\\et'   => '\\land',
            '\\ou'   => '\\lor',
            '\\non'   => '\\lnot',
            '\\excluant' => '\\setminus',
            '\\accol' => '\\braces',
            '\\inter\\' => '\\cap\\',
            '\\inter ' => '\\cap ',
            '\\union\\' => '\\cup\\',
            '\\union ' => '\\cup ',
            '\\classe' => '\\Cscr',
            '\\P ' => '\\Pscr ',
            '\\P \\' => '\\Pscr \\',
            '\\tq' => '\\mid',
            '\\rond' => '\\circ',
            '\\inv' => '^{-1}',
            '\\fonction' => '\\funcf',
            '\\groupe' => '\\group',
            '\\begin{dcases}' => '\\begin{cases}',
            '\\end{dcases}' => '\\end{cases}',
            '\\biginter' => '\\displaystyle\\bigcap',
            '\\bigunion' => '\\displaystyle\\bigcup',
            '\\sum' => '\\displaystyle\\sum',
            '\\prod' => '\\displaystyle\\prod',
            '\\i ' => 'i ',
            '\\i\\' => 'i\\',
            '\\i}' => 'i}',
            '\\i{' => 'i{',
            '\\i^' => 'i^',
            '\\i=' => 'i=',
            '\\i$' => 'i$',
            '\\F ' => '\\Fscr ',
            '\\F\\' => '\\Fscr\\',
            '\\F}' => '\\Fscr}',
            '\\F^' => '\\Fscr^',
            '\\F=' => '\\Fscr=',
            '\\F$' => '\\Fscr$',
            '\\poly ' => '\\poly{\\K} ',
            '\\poly\\' => '\\poly{\\K}\\',
            '\\poly}' => '\\poly{\\K}}',
            '\\poly^' => '\\poly{\\K}^',
            '\\poly=' => '\\poly{\\K}=',
            '\\poly$' => '\\poly{\\K}$',
            '\\polydeg{n} ' => '\\polydeg{\\K}{n} ',
            '\\polydeg{n}\\' => '\\polydeg{\\K}{n}\\',
            '\\polydeg{n}}' => '\\polydeg{\\K}{n}}',
            '\\polydeg{n}^' => '\\polydeg{\\K}{n}^',
            '\\polydeg{n}=' => '\\polydeg{\\K}{n}=',
            '\\polydeg{n}$' => '\\polydeg{\\K}{n}$',
            '\\M{n} ' => '\\M{n}{\\K} ',
            '\\M{n}\\' => '\\M{n}{\\K}\\',
            '\\M{n}}' => '\\M{n}{\\K}}',
            '\\M{n}^' => '\\M{n}{\\K}^',
            '\\M{n}=' => '\\M{n}{\\K}=',
            '\\M{n}$' => '\\M{n}{\\K}$',
            '\\GL{n} ' => '\\GL{n}{\\K} ',
            '\\GL{n}\\' => '\\GL{n}{\\K}\\',
            '\\GL{n}}' => '\\GL{n}{\\K}}',
            '\\GL{n}^' => '\\GL{n}{\\K}^',
            '\\GL{n}=' => '\\GL{n}{\\K}=',
            '\\GL{n}$' => '\\GL{n}{\\K}$',
            '\\Mat{u} ' => '\\Mat{}{u} ',
            '\\Mat{u}\\' => '\\Mat{}{u}\\',
            '\\Mat{u}}' => '\\Mat{}{u}}',
            '\\Mat{u}^' => '\\Mat{}{u}^',
            '\\Mat{u}=' => '\\Mat{}{u}=',
            '\\Mat{u}$' => '\\Mat{}{u}$',
            '\\fracrat ' => '\\fracrat{\\K} ',
            '\\fracrat\\' => '\\fracrat{\\K}\\',
            '\\fracrat}' => '\\fracrat{\\K}}',
            '\\fracrat^' => '\\fracrat{\\K}^',
            '\\fracrat=' => '\\fracrat{\\K}=',
            '\\fracrat$' => '\\fracrat{\\K}$',
            '\\interventier' => '\\intervint',
            '\\begin{itemize}' => '\\begin{enumerate}dot',
            '\\end{itemize}' => '\\end{enumerate}',
            '\\begin{description}' => '\\begin{enumerate}none',
            '\\end{description}' => '\\end{enumerate}',
            '\\anneau' => '\\ring',
            '\\nicefrac' => '\\frac',
            '$\\group{G}$' => '$\\group{G}{+}$',
            '$\\group{E}$' => '$\\group{E}{+}$',
            '\\cad' => '$\\cad$',
            '<' => ' < ',
            '>' => ' > ',
        ];
        $exoContent = str_replace(array_keys($replacements), array_values($replacements), $exoContent);

        while (preg_match('/\[([^\]]*)\]/', $exoContent, $matches)) {
            $exoContent = str_replace($matches[0], '{'.$matches[1].'}', $exoContent);
        }

        while (preg_match('/\\\\pdv\{([^}]*)}\{([^}]*,[^}]*)}/', $exoContent, $matches)) {
            $exoContent = str_replace($matches[0], '\\pdv2{'.$matches[1].'}{'.str_replace(',', '}{', $matches[2]).'}', $exoContent);
        }

        while (preg_match('/\\\\Cscr\{([^}]*)}/', $exoContent, $matches)) {
            $exoContent = str_replace($matches[0], '\\Cscr^{'.$matches[1].'}', $exoContent);
        }

        while (preg_match('/([^$])\\\\guillemets\{([^}]*)}/', $exoContent, $matches)) {
            $exoContent = str_replace($matches[0], $matches[1].'$\\guillemets{'.$matches[2].'}$', $exoContent);
        }

        while (preg_match('/\\\\permu\{((?:(?!}|&).)*)}\{((?:(?!}|&).)*)}/', $exoContent, $matches)) {
            $exoContent = str_replace($matches[0], '\\permu{'.str_replace(';', ' & ', $matches[1]).'}{'.str_replace(';', ' & ', $matches[2]).'}', $exoContent);
        }

        while (preg_match('/\\\\cycle\{((?:(?!}|&).)*)}/', $exoContent, $matches)) {
            $exoContent = str_replace($matches[0], '\\cycle{'.str_replace(';', ' & ', $matches[1]).'}', $exoContent);
        }

        // 5. Quantifiers
        $pos = 0;
        while (($result = $this->extractQuantifsContent($exoContent, $pos)) !== false) {
            list($fullMatch, $inside) = $result;
            $replacement = str_replace(';', ',\;', $inside) . ',\;';
            $exoContent = str_replace($fullMatch, $replacement, $exoContent);
        }

        // 6. func: "f: from \to to"
        while (preg_match('/^(\w):\\\\(\w+)\\\\to\\\\(\w+)$/', $exoContent, $matches)) {
            $exoContent = str_replace($matches[0], "\\func{$matches[1]}{$matches[2]}{$matches[3]}", $exoContent);
        }

        // 7. Remove \\ outside blocks
        $exoContent = preg_replace_callback(
            '/(\$\$.*?\$\$|\$.*?\$|[^$]+)/s',
            function ($m) {
                $segment = $m[0];
                if (!str_starts_with($segment, '$')) {
                    $segment = preg_replace('/\\\\\\\\(?:\r?\n)?/', '', $segment);
                }
                return $segment;
            },
            $exoContent
        );

        // 8. Replace enumerate blocks
        $exoContent = $this->convertList($exoContent);

        // 10. Send the final result
        return [
            'title'   => $title,
            'number'  => $number,
            'content' => $exoContent
        ];
    }

    private function convertList(string $content): string
    {
        while (preg_match('/\\\\begin\{enumerate\}((?:(?!\\\\begin\{enumerate\}|\\\\end\{enumerate\}).)*?)\\\\end\{enumerate\}/s', $content, $matches)) {
            $listContent = $matches[1];
            $dot = str_starts_with($listContent, "dot");
            $none = str_starts_with($listContent, "none");
            if ($dot) {
                $listContent = substr($listContent, 3, -1);
            } else if ($none) {
                $listContent = substr($listContent, 4, -1);
            }

            while (preg_match('/\\\\item(?!\[)\s+(.*?)([ \t\r\n]*)(?=(?:\s*\\\\item(?!\[))|\z)/s', $listContent, $matches2)) {
                $listContent = str_replace($matches2[0], '\\item['.$matches2[1].']'.$matches2[2], $listContent);
            }

            $listName = $dot ? "\\listd" : ($none ? "\\listn" : "\\list");
            $content = str_replace($matches[0], $listName.'['.$listContent.']', $content);
        }

        return $content;
    }

    private function extractQuantifsContent($str, &$pos = 0) {
        $startTag = '\\quantifs{';
        $start = strpos($str, $startTag, $pos);
        if ($start === false) return false;

        $start += strlen($startTag);
        $depth = 1;
        $i = $start;
        $len = strlen($str);

        while ($i < $len && $depth > 0) {
            if ($str[$i] === '{') {
                $depth++;
            } elseif ($str[$i] === '}') {
                $depth--;
            }
            $i++;
        }

        if ($depth !== 0) {
            // accolade fermante non trouvée
            return false;
        }

        // extrait le contenu interne sans les accolades extérieures
        $content = substr($str, $start, $i - $start -1);

        $pos = $i; // mise à jour de la position pour la recherche suivante

        return [$startTag . $content . '}', $content];
    }

    private function removeExercise(Exercise $exercise)
    {
        foreach ($exercise->getTags() as $tag) {
            $exercise->removeTag($tag);
        }

        foreach ($exercise->getExercisePrefs() as $pref) {
            $this->entityManager->remove($pref);
        }

        foreach ($exercise->getHints() as $hint) {
            $this->entityManager->remove($hint->getElement());
            $this->entityManager->remove($hint);
        }

        $this->entityManager->remove($exercise->getStatement());
        $this->entityManager->remove($exercise->getSolution());
        $this->entityManager->remove($exercise);
    }
}

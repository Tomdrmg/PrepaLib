<?php

namespace App\Command;

use App\Entity\Element;
use App\Entity\RevisionElement;
use App\Entity\RevisionQuestion;
use App\Entity\RevisionSheet;
use App\Entity\Subject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sheet:import',
    description: 'Import sheet from json'
)]
class ImportSheetCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'File to import')
            ->addArgument('subject', InputArgument::REQUIRED, 'Subject id')
            ->addArgument('name', InputArgument::REQUIRED, 'Sheet name')
            ->addArgument('sortIndex', InputArgument::REQUIRED, 'Sort Index');
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = $input->getArgument('file');
        $name = $input->getArgument('name');

        if (!file_exists($path)) {
            $io->error("'$path' doesn't exist.");
            return Command::FAILURE;
        }

        $subject = $this->entityManager->find(Subject::class, $input->getArgument('subject'));
        if (!$subject) {
            $io->error("Subject not found.");
            return Command::FAILURE;
        }

        $content = file_get_contents($path);
        $data = json_decode($content, true);

        $sheet = new RevisionSheet();
        $sheet->setSubject($subject);
        $sheet->setTitle($name);
        $sheet->setSortNumber($input->getArgument('sortIndex'));
        $this->entityManager->persist($sheet);

        $i = 0;
        foreach ($data as $part) {
            $p = new RevisionSheet();
            $p->setSubject($subject);
            $p->setParent($sheet);
            $p->setTitle($part['title']);
            $p->setSortNumber($i);
            $this->entityManager->persist($p);

            $j = 0;
            foreach ($part['items'] as $item) {
                $element = new RevisionElement();
                $element->setSortNumber($j);
                $element->setRevisionSheet($p);
                $element->setContent($this->elementOf($item['statement']));
                $element->setDetails($this->elementOf(""));
                $this->entityManager->persist($element);

                foreach ($item['questions'] as $question) {
                    $q = new RevisionQuestion();
                    $q->setRevisionElement($element);
                    $q->setQuestion($this->elementOf($question['question']));
                    $q->setAnswer($this->elementOf($question['answer']));

                    $this->entityManager->persist($q);
                }

                $j++;
            }

            $i++;
        }

        $this->entityManager->flush();
        return Command::SUCCESS;
    }

    private function elementOf(string $text): Element
    {
        $element = new Element();
        $element->setContent($text);
        $this->entityManager->persist($element);
        return $element;
    }
}

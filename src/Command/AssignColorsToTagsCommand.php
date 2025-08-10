<?php

namespace App\Command;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:assign-tag-colors',
    description: 'Assign colors to tags'
)]
class AssignColorsToTagsCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tags = $this->em->getRepository(Tag::class)->findAll();
        $total = count($tags);

        if ($total === 0) {
            $output->writeln("<comment>Aucun tag trouvé.</comment>");
            return Command::SUCCESS;
        }

        $maxColors = 30; // nombre max de couleurs uniques
        $pastelColors = [];

        // Générer 30 couleurs pastel sur le spectre
        for ($i = 0; $i < $maxColors; $i++) {
            $hue = ($i / $maxColors) * 360;
            $pastelColors[] = $this->hslToHex($hue, 100, 75);
        }

        foreach ($tags as $index => $tag) {
            $color = $pastelColors[$index % $maxColors];
            $tag->setColor($color);
            $output->writeln("Tag #{$tag->getId()} → {$color}");
        }

        $this->em->flush();

        $output->writeln("<info>Couleurs pastel (max {$maxColors}) assignées à {$total} tags.</info>");
        return Command::SUCCESS;
    }

    private function hslToHex(float $h, float $s, float $l): string
    {
        $s /= 100;
        $l /= 100;

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;

        if ($h < 60) {
            $r = $c; $g = $x; $b = 0;
        } elseif ($h < 120) {
            $r = $x; $g = $c; $b = 0;
        } elseif ($h < 180) {
            $r = 0; $g = $c; $b = $x;
        } elseif ($h < 240) {
            $r = 0; $g = $x; $b = $c;
        } elseif ($h < 300) {
            $r = $x; $g = 0; $b = $c;
        } else {
            $r = $c; $g = 0; $b = $x;
        }

        $r = (int)(($r + $m) * 255);
        $g = (int)(($g + $m) * 255);
        $b = (int)(($b + $m) * 255);

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}

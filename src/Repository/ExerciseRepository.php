<?php

namespace App\Repository;

use App\Entity\Exercise;
use App\Entity\Subject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercise>
 */
class ExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    public function countCorrected(): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->join('e.solution', 's')
            ->where('s.content IS NOT NULL')
            ->andWhere("TRIM(s.content) != ''")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findFilteredExercisesWithCount(
        Subject $subject,
        User $user,
        array $difficulties = [],
        ?bool $done = null,
        ?bool $favorite = null,
        array $tagIds = [],
        ?string $search = null,
        string $tagsMode = 'any',
        int $page = 1,
        int $limit = 10
    ): array {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.category', 'c')
            ->leftJoin('e.statement', 's')
            ->leftJoin('e.exercisePrefs', 'p', 'WITH', 'p.user = :user')
            ->andWhere('c.subject = :subject')
            ->setParameter('subject', $subject)
            ->addSelect('c', 'p')
            ->setParameter('user', $user);

        if (!empty($difficulties)) {
            $qb->andWhere($qb->expr()->in('p.difficulty', ':difficulties'))
                ->setParameter('difficulties', $difficulties);
        }

        if ($done !== null) {
            if ($done) {
                $qb->andWhere('p.done = :done')
                    ->setParameter('done', true);
            } else {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('p.done', ':done'),
                        $qb->expr()->isNull('p.id')
                    )
                )
                    ->setParameter('done', false);
            }
        }

        if ($favorite !== null) {
            if ($favorite) {
                $qb->andWhere('p.favorite = :favorite')
                    ->setParameter('favorite', true);
            } else {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('p.favorite', ':favorite'),
                        $qb->expr()->isNull('p.id')
                    )
                )
                    ->setParameter('favorite', false);
            }
        }

        if ($search) {
            $words = preg_split('/\s+/', strtolower($search));
            foreach ($words as $index => $word) {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('LOWER(e.title)', ':word'.$index),
                        $qb->expr()->like('LOWER(s.content)', ':word'.$index),
                        $qb->expr()->like('LOWER(p.comment)', ':word'.$index)
                    )
                )->setParameter('word'.$index, '%'.$word.'%');
            }
        }

        $qb->orderBy('c.sortNumber', 'ASC')
            ->addOrderBy('e.sortNumber', 'ASC');

        $exercises = $qb->getQuery()->getResult();

        if (!empty($tagIds)) {
            $exercises = array_filter($exercises, function ($e) use ($tagIds, $tagsMode) {
                $tagIdList = array_map(fn($tag) => $tag->getId(), $e->getFullTags());

                $matchedTags = array_intersect($tagIds, $tagIdList);

                return $tagsMode === 'any' ? !empty($matchedTags) : count($matchedTags) === count($tagIds);
            });
        }

        $totalResults = count($exercises);
        $totalPages = (int) ceil($totalResults / $limit);
        $page = min($page, $totalPages);
        $page = max($page, 1);
        $offset = ($page - 1) * $limit;
        $paginated = array_slice($exercises, $offset, $limit);

        return [
            'totalResults' => $totalResults,
            'totalPages' => $totalPages,
            'exercises' => $paginated,
            'page' => $page
        ];
    }

//    /**
//     * @return Exercise[] Returns an array of Exercise objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Exercise
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

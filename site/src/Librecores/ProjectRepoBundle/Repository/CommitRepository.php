<?php

namespace Librecores\ProjectRepoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Librecores\ProjectRepoBundle\Entity\Contributor;
use Librecores\ProjectRepoBundle\Entity\SourceRepo;

/**
 * CommitRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class CommitRepository extends EntityRepository
{
    /**
     * Get the latest commit on the database
     *
     * @param SourceRepo $repo
     * @return mixed
     */
    public function getLatestCommitForRepository(SourceRepo $repo)
    {
        return $this->findOneBy(['repository' => $repo], ['dateCommitted' => 'DESC']);
    }

    /**
     * Get the first commit on the database
     *
     * @param SourceRepo $repo
     * @return mixed
     */
    public function getFirstCommitForRepository(SourceRepo $repo)
    {
        return $this->findOneBy(['repository' => $repo], ['dateCommitted' => 'ASC']);
    }

    /**
     * Delete all commits for the repository
     *
     * @param SourceRepo $repo
     * @return mixed
     */
    public function clearAllCommits(SourceRepo $repo)
    {
        return $this->createQueryBuilder('c')
            ->delete()
            ->where('c.repository = :repo')
            ->setParameter('repo', $repo)
            ->getQuery()
            ->execute();
    }

    /**
     * Get all commits in the repository
     *
     * @param SourceRepo $repo
     * @return array
     */
    public function getCommitsForRepository(SourceRepo $repo)
    {
        return $this->findBy([
            'repository' => $repo
        ]);
    }

    /**
     * Gets the number of commits in a repository
     *
     * @param SourceRepo $repo
     * @return mixed
     */
    public function getCommitCountForRepository(SourceRepo $repo): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(1)')
            ->where('c.repository = :repo')
            ->setParameter('repo', $repo)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Gets the number of commits by a project contributor
     *
     * @param Contributor $contributor
     * @return int
     */
    public function getCommitCountForContributor(Contributor $contributor): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(1)')
            ->where('c.contributor = :contributor')
            ->setParameter('contributor', $contributor)
            ->getQuery()
            ->getSingleScalarResult();
    }
}

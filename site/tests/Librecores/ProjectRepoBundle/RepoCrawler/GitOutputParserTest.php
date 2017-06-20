<?php
/**
 * Created by PhpStorm.
 * User: amitosh
 * Date: 12/6/17
 * Time: 10:40 PM
 */

namespace Tests\Librecores\ProjectRepoBundle\RepoCrawler;

use Doctrine\ORM\EntityManager;
use Librecores\ProjectRepoBundle\Entity\Contributor;
use Librecores\ProjectRepoBundle\Entity\GitSourceRepo;
use Librecores\ProjectRepoBundle\RepoCrawler\GitOutputParser;
use Librecores\ProjectRepoBundle\Repository\ContributorRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GitOutputParserTest extends TestCase
{
    public function testParseCommits()
    {
        $contributors = [
            'janedoe@example.com' => new Contributor('Jane Doe', 'janedoe@example.com'),
            'johndoe@example.com' => new Contributor('John Doe', 'johndoe@example.com')

        ];

        $repository = $this->getMockBuilder(ContributorRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy', 'getEntityManager'])
            ->getMock();

        $repository->expects($this->exactly(7))
            ->method('findOneBy')
            ->willReturnCallback(function (array $criteria) use ($contributors) {
                return $contributors[$criteria['email']];
            });

        $parser = new GitOutputParser($repository, $this->createMock(LoggerInterface::class));
        $mockOutput = file_get_contents(join(DIRECTORY_SEPARATOR, [__DIR__, 'Resources', 'output.txt']));

        $commits = $parser->parseCommits(new GitSourceRepo(), $mockOutput);

        $this->assertEquals($commits[0]->getCommitId(), 'e0889c6');
        $this->assertEquals($commits[1]->getCommitId(), '22b8123');
        $this->assertEquals($commits[2]->getCommitId(), '9e7dd1e');
        $this->assertEquals($commits[3]->getCommitId(), 'b611a9c');
        $this->assertEquals($commits[4]->getCommitId(), 'a4ecaae');
        $this->assertEquals($commits[5]->getCommitId(), '46f2311');
        $this->assertEquals($commits[6]->getCommitId(), '26f16e1');

        $this->assertEquals($commits[0]->getDateCommitted(), new \DateTime('Fri, 26 May 2017 18:32:10 +0530'));
        $this->assertEquals($commits[1]->getDateCommitted(), new \DateTime('Fri, 26 May 2017 20:08:57 +0530'));
        $this->assertEquals($commits[2]->getDateCommitted(), new \DateTime('Sat, 27 May 2017 16:13:23 +0200'));
        $this->assertEquals($commits[3]->getDateCommitted(), new \DateTime('Tue, 30 May 2017 00:33:18 +0300'));
        $this->assertEquals($commits[4]->getDateCommitted(), new \DateTime('Thu, 08 Jun 2017 01:04:02 +0530'));
        $this->assertEquals($commits[5]->getDateCommitted(), new \DateTime('Sun, 11 Jun 2017 14:04:03 +0200'));
        $this->assertEquals($commits[6]->getDateCommitted(), new \DateTime('Mon, 12 Jun 2017 16:24:21 +0200'));

        $this->assertEquals($commits[0]->getContributor()->getEmail(), 'johndoe@example.com');
        $this->assertEquals($commits[1]->getContributor()->getEmail(), 'johndoe@example.com');
        $this->assertEquals($commits[2]->getContributor()->getEmail(), 'johndoe@example.com');
        $this->assertEquals($commits[3]->getContributor()->getEmail(), 'janedoe@example.com');
        $this->assertEquals($commits[4]->getContributor()->getEmail(), 'janedoe@example.com');
        $this->assertEquals($commits[5]->getContributor()->getEmail(), 'johndoe@example.com');
        $this->assertEquals($commits[6]->getContributor()->getEmail(), 'janedoe@example.com');

        $this->assertEquals($commits[0]->getFilesModified(), 0);
        $this->assertEquals($commits[1]->getFilesModified(), 1);
        $this->assertEquals($commits[2]->getFilesModified(), 1);
        $this->assertEquals($commits[3]->getFilesModified(), 7);
        $this->assertEquals($commits[4]->getFilesModified(), 4);
        $this->assertEquals($commits[5]->getFilesModified(), 1);
        $this->assertEquals($commits[6]->getFilesModified(), 4);

        $this->assertEquals($commits[0]->getLinesAdded(), 0);
        $this->assertEquals($commits[1]->getLinesAdded(), 4);
        $this->assertEquals($commits[2]->getLinesAdded(), 5);
        $this->assertEquals($commits[3]->getLinesAdded(), 183);
        $this->assertEquals($commits[4]->getLinesAdded(), 37);
        $this->assertEquals($commits[5]->getLinesAdded(), 3);
        $this->assertEquals($commits[6]->getLinesAdded(), 0);

        $this->assertEquals($commits[0]->getLinesRemoved(), 0);
        $this->assertEquals($commits[1]->getLinesRemoved(), 0);
        $this->assertEquals($commits[2]->getLinesRemoved(), 0);
        $this->assertEquals($commits[3]->getLinesRemoved(), 3);
        $this->assertEquals($commits[4]->getLinesRemoved(), 0);
        $this->assertEquals($commits[5]->getLinesRemoved(), 0);
        $this->assertEquals($commits[6]->getLinesRemoved(), 8);
    }
}

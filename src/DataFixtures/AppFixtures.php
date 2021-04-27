<?php

namespace App\DataFixtures;

use App\Entity\Choice;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppFixtures extends Fixture implements ContainerAwareInterface
{
    /** @var string */
    private $environment; //dev, test

    /** @var EntityManager */
    private $em;

    /** @var ContainerInterface */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $kernel = $this->container->get('kernel');
        if ($kernel) $this->environment = $kernel->getEnvironment();
    }


    public function load(ObjectManager $manager)
    {
        $this->em = $manager;

        $stackLogger = new DebugStack();
        $this->em->getConnection()->getConfiguration()->setSQLLogger($stackLogger);

        //INSERT

        $q1 = new Question();
        $q1->setQuText("Do you like PHP?");
        $this->em->persist($q1);

        $q2 = new Question();
        $q2->setQuText("What is your fav color?");
        $this->em->persist($q2);

        //SAVE CHANGES
        $this->em->flush();
        echo("Questions OK. Queries: ".count($stackLogger->queries)."\n");

        $ans1 = new Choice();
        $ans1->setChoVisible(true);
        $ans1->setChoNumvotes(0);
        $ans1->setChoText("YES");
        $ans1->setChoQuestion($q1);
        $this->em->persist($ans1);

        $ans2 = new Choice();
        $ans2->setChoVisible(true);
        $ans2->setChoNumvotes(0);
        $ans2->setChoText("NO");
        $ans2->setChoQuestion($q1);
        $this->em->persist($ans2);

        $ans3 = new Choice();
        $ans3->setChoVisible(true);
        $ans3->setChoNumvotes(0);
        $ans3->setChoText("RED");
        $ans3->setChoQuestion($q2);
        $this->em->persist($ans3);

        $ans4 = new Choice();
        $ans4->setChoVisible(true);
        $ans4->setChoNumvotes(0);
        $ans4->setChoText("BLUE");
        $ans4->setChoQuestion($q2);
        $this->em->persist($ans4);

        $this->em->flush();
        echo("Choices OK. Queries: ".count($stackLogger->queries)."\n");
        echo("\n\n");

        //READ
        
        $oneChoice = $this->em->getRepository(Choice::class)->findOneBy(["cho_text" => "NO"]);
        $oneChoiceId = $oneChoice->getChoId();
        echo("CHOICE #{$oneChoiceId} FETCHED\n");

        //UPDATE
        $oneChoice->setChoNumvotes(42); //PROXY
        $this->em->persist($oneChoice);
        $this->em->flush();
        echo("MOD OK. Queries: ".count($stackLogger->queries)."\n");

        //READ
        $numVotes = $this->em->getRepository(Choice::class)->find($oneChoiceId)->getChoNumvotes();
        echo("VOTES: {$numVotes} \n");

        //IDENTYTY MAP, invoke clear, because sometimes data is outdated
        $this->em->clear();

        //REMOVE
        $oneChoice = $this->em->getRepository(Choice::class)->find($oneChoiceId);
        $this->em->remove($oneChoice);
        $this->em->flush();
        echo("DEL OK. Queries: ".count($stackLogger->queries)."\n");
    }
}
/*
 * Commands:
 * php bin/console doctrine:database:create
 *
 * php bin/console doctrine:database:drop --force --full-database
 * php bin/console doctrine:schema:update --dump-sql
 * php bin/console doctrine:schema:update --force
 *
 * php bin/console doctrine:fixtures:load --no-interaction -vvv
 */

<?php

namespace HMLB\DateBundle\Tests\Functional\Doctrine;

use HMLB\Date\Date;
use HMLB\DateBundle\Tests\Functional\AbstractDoctrineTest;
use HMLB\DateBundle\Tests\Functional\TestEntity;

/**
 * PersistanceTest.
 *
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 */
class PersistenceTest extends AbstractDoctrineTest
{
    /**
     * @test
     */
    public function itPersists()
    {
        $this->createAndPersistTestEntity();

        $em = $this->doctrine->getManager();
        $repo = $em->getRepository(TestEntity::class);
        $entities = $repo->findAll();

        $this->assertCount(1, $entities);
        /** @var TestEntity $entity */
        $entity = $entities[0];
        $this->assertInstanceOf(Date::class, $entity->getCreated());
        $this->assertInstanceOf(Date::class, $entity->getDay());

        //hmlb_datetime type is persisted with time
        $this->assertEquals('2020-01-01 12:30:20', $entity->getCreated()->toDateTimeString());

        //hmlb_date is persisted without time (00:00:00)
        $this->assertEquals('1980-04-14 00:00:00', $entity->getDay()->toDateTimeString());
    }

    /**
     * @test
     */
    public function itCanBeQueriedByDateObject()
    {
        $this->createAndPersistTestEntity();

        $em = $this->doctrine->getManager();
        $repo = $em->getRepository(TestEntity::class);

        //Different Day
        $entity = $repo->findOneBy(['day' => Date::create(1980, 04, 15, 02, 03, 04)]);
        $this->assertNull($entity);

        //Same day
        $entity = $repo->findOneBy(['day' => Date::create(1980, 04, 14, 02, 03, 04)]);
        $this->assertInstanceOf(TestEntity::class, $entity);

        //Different second
        $entity = $repo->findOneBy(['created' => Date::create(2020, 01, 01, 12, 30, 21)]);
        $this->assertNull($entity);

        //Different minute
        $entity = $repo->findOneBy(['created' => Date::create(2020, 01, 01, 12, 31, 20)]);
        $this->assertNull($entity);

        //Same datetime
        $entity = $repo->findOneBy(['created' => Date::create(2020, 01, 01, 12, 30, 20)]);
        $this->assertInstanceOf(TestEntity::class, $entity);
    }

    private function createAndPersistTestEntity()
    {
        Date::setTestNow(Date::create(2020, 01, 01, 12, 30, 20));
        $entity = new TestEntity(Date::create(1980, 04, 14, 13, 37));
        Date::setTestNow();

        $em = $this->doctrine->getManager();
        $em->persist($entity);
        $em->flush();
        $em->clear();
    }
}

<?php

namespace HMLB\DateBundle\Tests\Functional;

use HMLB\Date\Date;

/**
 * TestEntity.
 *
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 */
class TestEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Date
     */
    private $created;

    /**
     * @var Date
     */
    private $day;

    public function __construct(Date $day)
    {
        $this->created = Date::now();
        $this->day = $day;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Date
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return Date
     */
    public function getDay()
    {
        return $this->day;
    }
}

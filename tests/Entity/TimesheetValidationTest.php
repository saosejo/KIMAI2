<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Entity;

use App\Entity\Activity;
use App\Entity\Customer;
use App\Entity\Project;
use App\Entity\Timesheet;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Entity\Timesheet
 * @group integration
 */
class TimesheetValidationTest extends KernelTestCase
{
    use EntityValidationTestTrait;

    protected function getEntity()
    {
        $customer = new Customer();
        $customer->setName('Test Customer');

        $project = new Project();
        $project->setName('Test Project');
        $project->setCustomer($customer);

        $activity = new Activity();
        $activity->setName('Test');
        $activity->setProject($project);

        $entity = new Timesheet();
        $entity->setUser(new User());
        $entity->setActivity($activity);
        $entity->setProject($project);

        return $entity;
    }

    public function testValidationNeedsActivity()
    {
        $entity = new Timesheet();
        $entity
            ->setUser(new User())
            ->setProject(new Project())
            ->setBegin(new \DateTime())
        ;

        $this->assertHasViolationForField($entity, 'activity');
    }

    public function testValidationNeedsProject()
    {
        $entity = new Timesheet();
        $entity
            ->setUser(new User())
            ->setActivity(new Activity())
            ->setBegin(new \DateTime())
        ;

        $this->assertHasViolationForField($entity, 'project');
    }

    public function testValidationProjectMismatch()
    {
        $customer = new Customer();
        $project = (new Project())->setName('foo')->setCustomer($customer);
        $project2 = (new Project())->setName('bar')->setCustomer($customer);
        $activity = (new Activity())->setName('hello-world')->setProject($project);

        $entity = new Timesheet();
        $entity
            ->setUser(new User())
            ->setActivity($activity)
            ->setProject($project2)
            ->setBegin(new \DateTime())
        ;

        $this->assertHasViolationForField($entity, 'project');
    }

    public function testValidationCustomerInvisible()
    {
        $customer = (new Customer())->setVisible(false);
        $project = (new Project())->setName('foo')->setCustomer($customer);
        $activity = (new Activity())->setName('hello-world')->setProject($project);

        $entity = new Timesheet();
        $entity
            ->setUser(new User())
            ->setActivity($activity)
            ->setProject($project)
            ->setBegin(new \DateTime())
        ;

        $this->assertHasViolationForField($entity, 'customer');
    }

    public function testValidationCustomerInvisibleDoesNotTriggerOnStoppedEntites()
    {
        $customer = (new Customer())->setVisible(false);
        $project = (new Project())->setName('foo')->setCustomer($customer);
        $activity = (new Activity())->setName('hello-world')->setProject($project);

        $entity = new Timesheet();
        $entity
            ->setUser(new User())
            ->setActivity($activity)
            ->setProject($project)
            ->setBegin(new \DateTime())
            ->setEnd(new \DateTime())
        ;

        $this->assertHasNoViolations($entity);
    }

    public function testValidationProjectInvisible()
    {
        $customer = new Customer();
        $project = (new Project())->setName('foo')->setCustomer($customer)->setVisible(false);
        $activity = (new Activity())->setName('hello-world')->setProject($project);

        $entity = new Timesheet();
        $entity
            ->setUser(new User())
            ->setActivity($activity)
            ->setProject($project)
            ->setBegin(new \DateTime())
        ;

        $this->assertHasViolationForField($entity, 'project');
    }

    public function testValidationProjectInvisibleDoesNotTriggerOnStoppedEntites()
    {
        $customer = new Customer();
        $project = (new Project())->setName('foo')->setCustomer($customer)->setVisible(false);
        $activity = (new Activity())->setName('hello-world')->setProject($project);

        $entity = new Timesheet();
        $entity
            ->setUser(new User())
            ->setActivity($activity)
            ->setProject($project)
            ->setBegin(new \DateTime())
            ->setEnd(new \DateTime())
        ;

        $this->assertHasNoViolations($entity);
    }

    public function testValidationActivityInvisible()
    {
        $customer = new Customer();
        $project = (new Project())->setName('foo')->setCustomer($customer);
        $activity = (new Activity())->setName('hello-world')->setProject($project)->setVisible(false);

        $entity = new Timesheet();
        $entity
            ->setUser(new User())
            ->setActivity($activity)
            ->setProject($project)
            ->setBegin(new \DateTime())
        ;

        $this->assertHasViolationForField($entity, 'activity');
    }

    public function testValidationActivityInvisibleDoesNotTriggerOnStoppedEntites()
    {
        $customer = new Customer();
        $project = (new Project())->setName('foo')->setCustomer($customer);
        $activity = (new Activity())->setName('hello-world')->setProject($project)->setVisible(false);

        $entity = new Timesheet();
        $entity
            ->setUser(new User())
            ->setActivity($activity)
            ->setProject($project)
            ->setBegin(new \DateTime())
            ->setEnd(new \DateTime())
        ;

        $this->assertHasNoViolations($entity);
    }

    public function testValidationEndNotEarlierThanBegin()
    {
        $entity = $this->getEntity();
        $begin = new \DateTime();
        $end = clone $begin;
        $end = $end->modify('-1 second');
        $entity->setBegin($begin);
        $entity->setEnd($end);

        $this->assertHasViolationForField($entity, 'end');

        // allow same begin and end
        $entity = $this->getEntity();
        $begin = new \DateTime();
        $end = clone $begin;
        $entity->setBegin($begin);
        $entity->setEnd($end);

        $this->assertHasViolationForField($entity, []);
    }

    public function testDurationMustBeGreatorOrEqualThanZero()
    {
        $entity = $this->getEntity();
        $begin = new \DateTime();
        $end = clone $begin;
        $entity->setBegin($begin);
        $entity->setEnd($end);
        $entity->setDuration(-1);

        $this->assertHasViolationForField($entity, 'duration');

        // allow zero duration
        $entity = $this->getEntity();
        $begin = new \DateTime();
        $end = clone $begin;
        $entity->setBegin($begin);
        $entity->setEnd($end);
        $entity->setDuration(0);

        $this->assertHasViolationForField($entity, []);
    }
}

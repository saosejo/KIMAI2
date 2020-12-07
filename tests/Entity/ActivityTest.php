<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Entity;

use App\Constants;
use App\Entity\Activity;
use App\Entity\ActivityMeta;
use App\Entity\Project;
use App\Entity\Team;
use App\Export\Spreadsheet\ColumnDefinition;
use App\Export\Spreadsheet\Extractor\AnnotationExtractor;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Entity\Activity
 */
class ActivityTest extends TestCase
{
    public function testDefaultValues()
    {
        $sut = new Activity();
        $this->assertNull($sut->getId());
        $this->assertNull($sut->getProject());
        $this->assertNull($sut->getName());
        $this->assertNull($sut->getComment());
        $this->assertTrue($sut->isVisible());
        $this->assertTrue($sut->isGlobal());
        $this->assertNull($sut->getColor());
        self::assertFalse($sut->hasColor());
        $this->assertEquals(0.0, $sut->getBudget());
        $this->assertEquals(0, $sut->getTimeBudget());
        $this->assertInstanceOf(Collection::class, $sut->getMetaFields());
        $this->assertEquals(0, $sut->getMetaFields()->count());
        $this->assertNull($sut->getMetaField('foo'));
        $this->assertInstanceOf(Collection::class, $sut->getTeams());
    }

    public function testSetterAndGetter()
    {
        $sut = new Activity();
        $this->assertInstanceOf(Activity::class, $sut->setName('foo-bar'));
        $this->assertEquals('foo-bar', $sut->getName());
        $this->assertEquals('foo-bar', (string) $sut);

        $this->assertInstanceOf(Activity::class, $sut->setVisible(false));
        $this->assertFalse($sut->isVisible());

        $this->assertInstanceOf(Activity::class, $sut->setComment('hello world'));
        $this->assertEquals('hello world', $sut->getComment());

        self::assertFalse($sut->hasColor());
        $this->assertInstanceOf(Activity::class, $sut->setColor('#fffccc'));
        $this->assertEquals('#fffccc', $sut->getColor());
        self::assertTrue($sut->hasColor());

        $this->assertInstanceOf(Activity::class, $sut->setColor(Constants::DEFAULT_COLOR));
        $this->assertNull($sut->getColor());
        self::assertFalse($sut->hasColor());

        $this->assertInstanceOf(Activity::class, $sut->setBudget(12345.67));
        $this->assertEquals(12345.67, $sut->getBudget());

        $this->assertInstanceOf(Activity::class, $sut->setTimeBudget(937321));
        $this->assertEquals(937321, $sut->getTimeBudget());

        $this->assertTrue($sut->isGlobal());
        $this->assertInstanceOf(Activity::class, $sut->setProject(new Project()));
        $this->assertFalse($sut->isGlobal());
    }

    public function testMetaFields()
    {
        $sut = new Activity();
        $meta = new ActivityMeta();
        $meta->setName('foo')->setValue('bar')->setType('test');
        $this->assertInstanceOf(Activity::class, $sut->setMetaField($meta));
        self::assertEquals(1, $sut->getMetaFields()->count());
        $result = $sut->getMetaField('foo');
        self::assertSame($result, $meta);
        self::assertEquals('test', $result->getType());

        $meta2 = new ActivityMeta();
        $meta2->setName('foo')->setValue('bar')->setType('test2');
        $this->assertInstanceOf(Activity::class, $sut->setMetaField($meta2));
        self::assertEquals(1, $sut->getMetaFields()->count());
        self::assertCount(0, $sut->getVisibleMetaFields());

        $result = $sut->getMetaField('foo');
        self::assertSame($result, $meta);
        self::assertEquals('test2', $result->getType());

        $sut->setMetaField((new ActivityMeta())->setName('blub')->setIsVisible(true));
        $sut->setMetaField((new ActivityMeta())->setName('blab')->setIsVisible(true));
        self::assertEquals(3, $sut->getMetaFields()->count());
        self::assertCount(2, $sut->getVisibleMetaFields());
    }

    public function testTeams()
    {
        $sut = new Activity();
        $team = new Team();
        self::assertEmpty($sut->getTeams());
        self::assertEmpty($team->getActivities());

        $sut->addTeam($team);
        self::assertCount(1, $sut->getTeams());
        self::assertCount(1, $team->getActivities());
        self::assertSame($team, $sut->getTeams()[0]);
        self::assertSame($sut, $team->getActivities()[0]);

        // test remove unknown team doesn't do anything
        $sut->removeTeam(new Team());
        self::assertCount(1, $sut->getTeams());
        self::assertCount(1, $team->getActivities());

        $sut->removeTeam($team);
        self::assertCount(0, $sut->getTeams());
        self::assertCount(0, $team->getActivities());
    }

    public function testExportAnnotations()
    {
        $sut = new AnnotationExtractor(new AnnotationReader());

        $columns = $sut->extract(Activity::class);

        self::assertIsArray($columns);

        $expected = [
            ['label.id', 'integer'],
            ['label.name', 'string'],
            ['label.project', 'string'],
            ['label.color', 'string'],
            ['label.visible', 'boolean'],
            ['label.comment', 'string'],
        ];

        self::assertCount(\count($expected), $columns);

        foreach ($columns as $column) {
            self::assertInstanceOf(ColumnDefinition::class, $column);
        }

        $i = 0;

        foreach ($expected as $item) {
            $column = $columns[$i++];
            self::assertEquals($item[0], $column->getLabel());
            self::assertEquals($item[1], $column->getType());
        }
    }
}

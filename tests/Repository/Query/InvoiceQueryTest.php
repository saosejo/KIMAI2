<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Repository\Query;

use App\Repository\Query\InvoiceQuery;

/**
 * @covers \App\Repository\Query\InvoiceQuery
 * @covers \App\Repository\Query\TimesheetQuery
 */
class InvoiceQueryTest extends TimesheetQueryTest
{
    public function testQuery()
    {
        $sut = new InvoiceQuery();

        $this->assertPage($sut);
        $this->assertPageSize($sut);
        $this->assertOrderBy($sut, 'begin');
        $this->assertOrder($sut, InvoiceQuery::ORDER_DESC);

        $this->assertUser($sut);
        $this->assertCustomer($sut);
        $this->assertProject($sut);
        $this->assertActivity($sut);
        $this->assertState($sut);
        $this->assertExported($sut);
        $this->assertMarkAsExported($sut);
        $this->assertModifiedAfter($sut);

        self::assertTrue($sut->getBillable());
        self::assertTrue($sut->isBillable());
        self::assertFalse($sut->isNotBillable());
        self::assertFalse($sut->isIgnoreBillable());

        self::assertTrue($sut->isBillable());
        self::assertFalse($sut->isNotBillable());
        $this->assertBillable($sut);
    }

    protected function assertMarkAsExported(InvoiceQuery $sut)
    {
        self::assertFalse($sut->isMarkAsExported());

        $sut->setMarkAsExported(true);
        self::assertTrue($sut->isMarkAsExported());
    }
}

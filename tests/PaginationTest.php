<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require __DIR__ . '/../actions/pagination.php';

class PaginationTest extends TestCase
{
    public function test_normal_pagination_returns_correct_values(): void
    {
        [$page, $per_page, $offset, $total_pages] = pagination_limits(100, 1);

        $this->assertEquals(1, $page);
        $this->assertEquals(10, $per_page);
        $this->assertEquals(0, $offset);
        $this->assertEquals(10, $total_pages);
    }

    public function test_page_exceeding_total_clamps_to_last_page(): void
    {
        [$page, $per_page, $offset, $total_pages] = pagination_limits(100, 20);

        $this->assertEquals(10, $page);
        $this->assertEquals(90, $offset);
        $this->assertEquals(10, $total_pages);
    }

    public function test_page_below_minimum_clamps_to_page_one(): void
    {
        [$page, $per_page, $offset, $total_pages] = pagination_limits(100, 0);

        $this->assertEquals(1, $page);
        $this->assertEquals(0, $offset);
        $this->assertEquals(10, $total_pages);
    }

    public function test_zero_rows_returns_one_total_page(): void
    {
        [$page, $per_page, $offset, $total_pages] = pagination_limits(0, 1);

        $this->assertEquals(1, $page);
        $this->assertEquals(0, $offset);
        $this->assertEquals(1, $total_pages);
    }
}
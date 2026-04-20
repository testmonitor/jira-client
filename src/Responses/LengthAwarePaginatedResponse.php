<?php

namespace TestMonitor\Jira\Responses;

class LengthAwarePaginatedResponse
{
    protected array $items;

    protected int $total;

    protected int $perPage;

    protected int $offset;

    /**
     * Create a new paginated response instance.
     */
    public function __construct(array $items, int $total, int $perPage, int $offset = 0)
    {
        $this->items = $items;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->offset = $offset;
    }

    /**
     * Get the items being paginated.
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Get the total number of items being paginated.
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Get the number of items shown per page.
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get the current item offset.
     */
    public function offset(): int
    {
        return $this->offset;
    }

    /**
     * Determine the current page.
     */
    public function currentPage(): int
    {
        if ($this->offset === 0) {
            return 1;
        }

        return floor($this->offset / $this->perPage) + 1;
    }
}

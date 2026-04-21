<?php

namespace TestMonitor\Jira\Responses;

class TokenPaginatedResponse
{
    protected array $items;

    protected int $total;

    protected int $perPage;

    protected ?string $nextPageToken;

    protected bool $isLast;

    /**
     * Create a new paginated response instance.
     */
    public function __construct(
        array $items,
        int $total,
        int $perPage,
        ?string $nextPageToken = null,
        bool $isLast = false
    ) {
        $this->items = $items;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->nextPageToken = $nextPageToken;
        $this->isLast = $isLast;
    }

    /**
     * Get the items being paginated.
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Get the next page token.
     */
    public function nextPageToken(): ?string
    {
        return $this->nextPageToken;
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
     * Determines if this is the last page.
     */
    public function isLastPage(): bool
    {
        return $this->isLast || empty($this->nextPageToken());
    }

    /**
     * Determines if there's a next page of items.
     */
    public function hasNextPage(): bool
    {
        return ! $this->isLastPage();
    }
}

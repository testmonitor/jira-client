<?php

namespace TestMonitor\Jira\Responses;

class TokenPaginatedResponse
{
    /**
     * The items being paginated.
     *
     * @var array
     */
    protected array $items;

    /**
     * The total number of items before slicing.
     *
     * @var int
     */
    protected int $total;

    /**
     * The number of items to be shown per page.
     *
     * @var int
     */
    protected int $perPage;

    /**
     * The token for the next page.
     *
     * @var string
     */
    protected string $nextPageToken;

    /**
     * Whether this is the last page.
     *
     * @var bool
     */
    protected bool $isLast;

    /**
     * Create a new paginated response instance.
     *
     * @param array $items
     * @param int $total
     * @param int $perPage
     * @param string|null $nextPageToken
     * @param bool $isLast
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
     *
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * The next page token.
     *
     * @return string|null
     */
    public function nextPageToken(): ?string
    {
        return $this->nextPageToken;
    }

    /**
     * Get the total number of items being paginated.
     *
     * @return int
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Get the number of items shown per page.
     *
     * @return int
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Determines if this is the last page.
     *
     * @return bool
     */
    public function isLastPage(): bool
    {
        return $this->isLast || empty($this->nextPageToken());
    }

    /**
     * Determines if there's a next page of items.
     *
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return ! $this->isLastPage();
    }
}

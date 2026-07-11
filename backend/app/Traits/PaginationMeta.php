<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait PaginationMeta
{
    protected function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
            'first_page_url' => $paginator->url(1),
            'last_page_url' => $paginator->url($paginator->lastPage()),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'path' => $paginator->path(),
        ];
    }
}

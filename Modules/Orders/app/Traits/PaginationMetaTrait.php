<?php

namespace Modules\Orders\App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait PaginationMetaTrait
{
    /**
     * @return array<string, int>
     */
    protected function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}

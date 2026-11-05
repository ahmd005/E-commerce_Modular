<?php

namespace Modules\Orders\App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait PaginationMetaTrait
{
    /**
     * @return array<string, mixed>
     */
    protected function paginationMeta(LengthAwarePaginator $paginator): array
    {
        $request = request();
        $baseUrl = $request->url();
        $queryParams = $request->query();

        $links = [];
        for ($page = 1; $page <= $paginator->lastPage(); $page++) {
            $params = $queryParams;
            $params['page'] = $page;
            $links[$page] = $baseUrl . '?' . http_build_query($params);
        }

        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'first_page_url' => $this->buildPageUrl($request, 1),
            'last_page_url' => $this->buildPageUrl($request, $paginator->lastPage()),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'links' => $links,
        ];
    }

    protected function buildPageUrl($request, int $page): ?string
    {
        $query = $request->query();
        $query['page'] = $page;

        return $request->url() . '?' . http_build_query($query);
    }
}

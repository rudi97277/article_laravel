<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait ApiResponser
{
    private function successResponse($data, $statusCode, $metaMessage)
    {
        return response()->json(
            [
                'meta' => [
                    'success' => true,
                    'code' => 20000,
                    'message' => $metaMessage ?? 'Request success',
                ],
                'data' => $data,
            ],
            $statusCode
        );
    }

    protected function errorResponse($errorMessage, $statusCode, $metaCode)
    {
        return response()->json(
            [
                'meta' => [
                    'success' => false,
                    'code' => $metaCode,
                    'message' => $errorMessage,
                ],
                'data' => null,
            ],
            $statusCode,
        );
    }

    protected function showOne($object, $statusCode = null, $metaMessage = null)
    {
        return $this->successResponse(
            $object,
            $statusCode ?? 200,
            $metaMessage,
        );
    }

    protected function showPaginate($resultKey, Collection $resultValues, Collection $paginateCollection, $statusCode = null, $metaMessage = null)
    {
        return response()->json(
            [
                'meta' => [
                    'success' => true,
                    'code' => 20000,
                    'message' => $metaMessage ?? 'Request success',
                ],
                'data' => [
                    $resultKey => $resultValues->values(),
                    'page_info' => [
                        'last_page' => $paginateCollection['last_page'],
                        'current_page' => $paginateCollection['current_page'],
                        'path' => $paginateCollection['path'],
                        'total' => $paginateCollection['total']
                    ]
                ]
            ],
            $statusCode ?? 200
        );
    }

    protected function showPagination($resultKey, LengthAwarePaginator $resultValues, $metaMessage = null)
    {
        return response()->json(
            [
                'meta' => [
                    'success' => true,
                    'code' => 20000,
                    'message' => $metaMessage ?? 'Request success',
                ],
                'data' => [
                    $resultKey => $resultValues->items(),
                    'page_info' => [
                        'last_page' => $resultValues->lastPage(),
                        'current_page' => $resultValues->currentPage(),
                        'path' => $resultValues->path(),
                        'total' => $resultValues->total()
                    ]
                ]
            ]
        );
    }

    protected function showPaginate2($resultKey, $resultValues, $current_page, $nextPageUrl, $lastPage = null, $statusCode = null, $metaMessage = null)
    {
        $pageDetail = [
            'meta' => [
                'success' => true,
                'code' => 20000,
                'message' => $metaMessage ?? 'Request success',
            ],
            'data' => [
                $resultKey => $resultValues,
                'page_info' => [
                    'current_page' => $current_page,
                    'path' => $nextPageUrl
                ]
            ]
        ];
        if ($lastPage != null) {
            $pageDetail['data']['page_info']['last_page'] = $lastPage;
        }
        return response()->json(
            $pageDetail,
            $statusCode ?? 200
        );
    }

    protected function showPaginate3($resultKey, $resultValues, $paginate, $lastPage = null, $statusCode = null, $metaMessage = null)
    {
        $pageDetail = [
            'meta' => [
                'success' => true,
                'code' => 20000,
                'message' => $metaMessage ?? 'Request success',
            ],
            'data' => [
                $resultKey => $resultValues,
                'page_info' => [
                    'last_page' => $paginate->lastPage(),
                    'current_page' => $paginate->currentPage(),
                    'path' => $paginate->path(),
                    'total' => $paginate->total()
                ]
            ]
        ];
        if ($lastPage != null) {
            $pageDetail['data']['page_info']['last_page'] = $lastPage;
        }
        return response()->json(
            $pageDetail,
            $statusCode ?? 200
        );
    }
}

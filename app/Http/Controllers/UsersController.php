<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataTable\DTO\DataTableRequest;
use App\DataTable\DTO\DataTableResponse;
use App\Models\User;

class UsersController extends Controller
{
    public function __invoke(DataTableRequest $dataTableRequest): array // @phpstan-ignore-line
    {
        return DataTableResponse::make($dataTableRequest, User::class)->toArray();
    }
}

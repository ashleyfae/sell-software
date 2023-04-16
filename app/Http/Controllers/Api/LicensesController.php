<?php

namespace App\Http\Controllers\Api;

use App\Actions\Licenses\ActivateLicense;
use App\Actions\Licenses\DeactivateLicense;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ActivateLicenseRequest;
use App\Http\Requests\Api\DeactivateLicenseRequest;
use App\Models\License;
use Illuminate\Http\JsonResponse;

class LicensesController extends Controller
{
    public function activate(ActivateLicenseRequest $request, License $license, ActivateLicense $action) : JsonResponse
    {
        $activation = $action->execute($request->validated()['url'], $license);

        return response()->json(
            $activation->toArray(),
            $action->wasCreated() ? 201 : 200
        );
    }

    public function deactivate(DeactivateLicenseRequest $request, License $license, DeactivateLicense $action): JsonResponse
    {
        $action->execute($license);

        return response()->json();
    }
}

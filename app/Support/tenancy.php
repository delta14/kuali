<?php

use App\Models\Business;

if (! function_exists('currentBusinessId')) {
    function currentBusinessId(): ?int
    {
        return app()->bound('currentBusinessId')
            ? app('currentBusinessId')
            : null;
    }
}

if (! function_exists('currentBusiness')) {
    function currentBusiness(): ?Business
    {
        $id = currentBusinessId();
        return $id ? Business::find($id) : null;
    }
}

<?php

namespace App\Repositories\User;

use App\Interfaces\User\MaritalStatusRepositoryInterface;
use App\Models\User\MaritalStatus;

class MaritalStatusRepository implements MaritalStatusRepositoryInterface
{
    public function getAllMaritalStatuses()
    {
        return MaritalStatus::all();
    }

    public function getMaritalStatusById(string $maritalStatusId): MaritalStatus
    {
        return MaritalStatus::findOrFail($maritalStatusId);
    }

    public function deleteMaritalStatus(string $maritalStatusId)
    {
        return MaritalStatus::destroy($maritalStatusId);
    }

    public function createMaritalStatus(array $maritalStatusDetails): MaritalStatus
    {
        return MaritalStatus::create($maritalStatusDetails);
    }

    public function updateMaritalStatus(string $maritalStatusId, array $newDetails)
    {
        return MaritalStatus::whereId($maritalStatusId)->update($newDetails);
    }

    public function getActiveMaritalStatuses()
    {
        return MaritalStatus::where('status', '=', true)->get();
    }
}
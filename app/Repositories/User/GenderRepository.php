<?php

namespace App\Repositories\User;

use App\Interfaces\User\GenderRepositoryInterface;
use App\Models\User\Gender;

class GenderRepository implements GenderRepositoryInterface
{
    public function getAllGenders()
    {
        return Gender::all();
    }
    public function getActiveGenders()
    {
        return Gender::where('status', '=', true)->get();
    }

    public function getGenderById(string $genderId): Gender
    {
        return Gender::findOrFail($genderId);
    }

    public function deleteGender(string $genderId)
    {
        return Gender::destroy($genderId);
    }

    public function createGender(array $genderDetails): Gender
    {
        return Gender::create($genderDetails);
    }

    public function updateGender(string $genderId, array $newDetails)
    {
        return Gender::whereId($genderId)->update($newDetails);
    }
}
<?php

namespace App\Interfaces\User;

interface GenderRepositoryInterface
{
    public function getGenders();

    public function getGenderById(string $genderId);

    public function deleteGender(string $genderId);

    public function createGender(array $genderDetails);

    public function updateGender(string $genderId, array $newDetails);
}

<?php

namespace App\Interfaces\User;

interface MaritalStatusRepositoryInterface
{
    public function getMaritalStatuses();

    public function getMaritalStatusById(string $maritalStatusId);

    public function deleteMaritalStatus(string $maritalStatusId);

    public function createMaritalStatus(array $maritalStatusDetails);

    public function updateMaritalStatus(string $maritalStatusId, array $newDetails);
}

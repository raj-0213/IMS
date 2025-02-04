<?php

namespace App\Interfaces;

use App\Models\molecules;

interface MoleculeRepositoryInterface
{
    public function getAllMolecules();
    public function getMoleculeById($id);
    public function createMolecule(array $data);
    public function updateMolecule($id, array $data);
    public function deleteMolecule($id);
}
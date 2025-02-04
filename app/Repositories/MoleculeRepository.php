<?php

namespace App\Repositories;

use App\Interfaces\MoleculeRepositoryInterface;
use App\Models\molecules;
use Illuminate\Support\Facades\Auth;

class MoleculeRepository implements MoleculeRepositoryInterface
{
    public function getAllMolecules()
    {
        // dump(molecules::all());
        return molecules::all();
    }

    public function getMoleculeById($id)
    {
        // dump("Hello");
        // dump($id);
        // dump(molecules::find($id));
        return molecules::find($id);
    }

    public function createMolecule(array $data)
    {
        return molecules::create($data);
    }

    public function updateMolecule($id, array $data)
    {
        $molecule = molecules::find($id);
        $molecule->update($data);
        return $molecule;
    }

    public function deleteMolecule($id)
    {
        $molecule = molecules::find($id);

        // dump($molecule);


        // dump($molecule['deleted_by']);

        $molecule->update(['deleted_by' => Auth::id()]);

        // dump($molecule->delete());

        return $molecule->delete();
        // return $molecule;
    }
}
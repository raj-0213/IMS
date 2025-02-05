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

        if ($molecule->deleted_at) {
            return response()->json(['message' => 'This molecule has already been deleted.'], 400);
        }

        $molecule->update([
            'deleted_by' => Auth::id(),
            'is_active' => false
        ]);

        $molecule->delete();

        return response()->json(['message' => 'Molecule deleted successfully.'], 200);
    }
}
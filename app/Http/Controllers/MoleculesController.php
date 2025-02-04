<?php

namespace App\Http\Controllers;

use App\Interfaces\MoleculeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class MoleculesController extends Controller
{
    protected $moleculeRepository;

    public function __construct(MoleculeRepositoryInterface $moleculeRepository)
    {
        $this->moleculeRepository = $moleculeRepository;
    }

    public function index()
    {
        try {

            $molecules = $this->moleculeRepository->getAllMolecules();
            return response()->json($molecules);
        } catch (\Exception $e) {
            Log::error('Error fetching molecules: ');
            return response()->json(['error' => 'Error fetching molecules' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        // dump($request->all()); 

        try {
            $validatedData = $request->validate([
                'molecule_name' => 'required|string|max:255',
                'is_active' => 'boolean'
            ]);

            $validatedData['created_by'] = Auth::id();
            $validatedData['updated_by'] = Auth::id();
        } catch (\Exception $e) {
            Log::error('Validation or assignment error: ' . $e->getMessage());
            return response()->json(['error' => 'Validation or assignment error'], Response::HTTP_BAD_REQUEST);
        }

        // dump($validatedData);

        try {
            $molecule = $this->moleculeRepository->createMolecule($validatedData);
            return response()->json($molecule, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating molecule: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating molecule'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {

            // dump($id);

            $molecule = $this->moleculeRepository->getMoleculeById($id);

            // dump($molecule);

            return response()->json($molecule);
        } catch (\Exception $e) {
            Log::error('Error fetching molecule: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching molecule'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'molecule_name' => 'sometimes|required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validatedData['updated_by'] = Auth::id();

        try {
            $molecule = $this->moleculeRepository->updateMolecule($id, $validatedData);
            return response()->json($molecule);
        } catch (\Exception $e) {
            Log::error('Error updating molecule: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating molecule'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function destroy($id)
    {
        try {

            // dump($id);

            $this->moleculeRepository->deleteMolecule($id);
            return response()->json(
                [
                    'message' => 'Molecule Deleted Successfully'
                ],
                200
            );
        } catch (\Exception $e) {
            Log::error('Error deleting molecule: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting molecule'. $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

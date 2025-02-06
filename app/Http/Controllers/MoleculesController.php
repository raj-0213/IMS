<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoleculeRequest;
use App\Interfaces\MoleculeRepositoryInterface;
use Illuminate\Http\Request;
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
            return response()->json(['error' => 'Error fetching molecules'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(MoleculeRequest $request)
    {
        // dump($request->all()); 

        try {
            $validatedData = $request->validated();
            $validatedData['created_by'] = Auth::id();
            $validatedData['updated_by'] = Auth::id();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Validation or assignment error'], Response::HTTP_BAD_REQUEST);
        }

        // dump($validatedData);

        try {
            $molecule = $this->moleculeRepository->createMolecule($validatedData);
            return response()->json($molecule, Response::HTTP_CREATED);
        } catch (\Exception $e) {
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
            return response()->json(['error' => 'Error fetching molecule'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(MoleculeRequest $request, $id)
    {
        $validatedData = $request->validated();

        $validatedData['updated_by'] = Auth::id();

        try {
            $molecule = $this->moleculeRepository->updateMolecule($id, $validatedData);
            return response()->json($molecule);
        } catch (\Exception $e) {
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
            return response()->json(['error' => 'Error deleting molecule'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

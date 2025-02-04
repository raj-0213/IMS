<?php
// filepath: /c:/Users/Medkart/Desktop/IMS/app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return response()->json($this->userRepository->all());
    }

    public function show($id)
    {
        // return DB::table('users')->where('id', $id)->get();
         
        return response()->json($this->userRepository->find($id));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'phone_no' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'sometimes|required|string|min:8',
        ]);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        return response()->json($this->userRepository->update($id, $data));
    }

    public function destroy($id)
    {
        return response()->json($this->userRepository->delete($id));
    }
}
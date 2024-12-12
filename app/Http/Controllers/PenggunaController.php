<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna

;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class PenggunaController extends Controller
{
    //
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:penggunas', // Nama tabel diubah menjadi 'penggunas'
                'password' => 'required|string|min:8',
            ]);

            $pengguna = Pengguna::create([ // Menggunakan model Pengguna
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            \Log::info('Pengguna successfully created:', ['pengguna' => $pengguna]);

            $token = $pengguna->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Pengguna created successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating pengguna:', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to create pengguna',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Optional: Hapus di produksi
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Invalid Login credentials',
                    'error' => 'The provided email or password is incorrect.',
                ], 401);
            }

            $pengguna = Auth::user(); // Menggunakan model Pengguna

            $token = $pengguna->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Pengguna logged in successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error during login:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to log in',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Hapus di produksi
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Menghapus semua token pengguna

        return response()->json([
            'message' => 'User Logout Successfully',
        ], 200);
    }

}

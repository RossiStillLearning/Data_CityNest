<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admins;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminsController extends Controller
{
    //
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:admins',
                    function ($attribute, $value, $fail) {
                        if (!str_ends_with($value, '@admin.com')) {
                            $fail('The email must end with @admin.com.');
                        }
                    },
                ],
                'password' => 'required|string|min:8',
            ]);

            $admins = Admins::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            \Log::info('Admin successfully created:', ['admin' => $admins]);

            $token = $admins->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Admin created successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating admin:', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to create admin',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }


    public function login(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            // Autentikasi user
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Invalid Login credentials',
                    'error' => 'The provided email or password is incorrect.',
                ], 401);
            }

            // Ambil user yang sedang login
            $admins = Auth::user(); // Menggunakan model Admins

            // Buat token
            $token = $admins->createToken('auth_token')->plainTextToken;

            // Respon berhasil
            return response()->json([
                'message' => 'Admin logged in successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error during login:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Respon error ke klien
            return response()->json([
                'message' => 'Failed to log in',
                'error' => $e->getMessage(),
                // Hapus 'trace' untuk produksi, tambahkan hanya saat debugging
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Menghapus semua token pengguna

        return response()->json([
            'message' => 'Admin Logout Successfully',
        ], 200);
    }

}

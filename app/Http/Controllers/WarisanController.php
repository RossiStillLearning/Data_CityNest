<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Warisan;
use Illuminate\Support\Facades\Storage;
use Dotenv\Exception\ValidationException;
class WarisanController extends Controller
{
    //
    public function create(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'nama_warisan' => 'required|string|max:255',
                'asal' => 'required|string|max:255',
                'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);

            // Proses upload gambar
            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar')->store('gambarwarisan', 'public');
                if ($gambar) {
                    Log::info('Gambar berhasil disimpan: ' . $gambar);
                }
                $validated['gambar'] = $gambar;
            }

            // Simpan ke database
            $warisan = Warisan::create($validated);

            // Kembalikan response JSON
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $warisan,
            ], 200);
        } catch (Exception $e) {
            // Tangani error
            return response()->json([
                'status' => false,
                'message' => $e->errors(),
            ], 500);
        }
    }

    // show all
    public function index()
    {
        $warisan = Warisan::all();
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diambil',
            'data' => $warisan,
        ], 200);
    }

    // show by ID
    public function show($id)
    {
        $warisan = Warisan::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diambil',
            'data' => $warisan,
        ], 200);
    }

    //update
    public function update(Request $request, $id)
    {
        try {
            // Validasi data input
            $validated = $request->validate([
                'nama_warisan' => 'string|max:255',
                'asal' => 'string|max:255',
                'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Temukan data berdasarkan ID
            $warisan = Warisan::findOrFail($id);

            // Proses upload gambar baru jika ada
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($warisan->gambar && Storage::disk('public')->exists($warisan->gambar)) {
                    Storage::disk('public')->delete($warisan->gambar);
                }

                // Simpan gambar baru di folder 'gambarwarisan'
                $gambarPath = $request->file('gambar')->store('gambarwarisan', 'public');
                $validated['gambar'] = $gambarPath;
            }

            // Perbarui data
            $warisan->update($validated);

            // Kembalikan response sukses
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diperbarui',
                'data' => $warisan,
            ], 200);
        } catch (ValidationException $e) {
            // Tangani error validasi
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            // Log error dan tangani error umum
            Log::error('Error saat memperbarui data: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // delete
    public function delete($id)
    {
        $warisan = Warisan::findOrFail($id);

        if (!$warisan) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        if ($warisan->gambar && Storage::disk('public')->exists($warisan->gambar)) {
            Storage::disk('public')->delete($warisan->gambar);
        }

        $warisan->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus',
        ], 200);
    }
}

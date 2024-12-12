<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PerumahanController extends Controller
{
    public function create(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'nama_perumahan' => 'required|string|max:255',
                'alamat' => 'required|string|max:255',
                'harga' => 'required|string|max:255',
                'luas' => 'required|string|max:255',
                'deskripsi' => 'required',
                'kontak' => 'required|string|max:12',
                'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);


            // Proses upload gambar
            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar')->store('gambar', 'public');
                if ($gambar) {
                    Log::info('Gambar berhasil disimpan: ' . $gambar);
                }
                $validated['gambar'] = $gambar;
            }


            // Simpan data ke database
            $perumahan = Perumahan::create($validated);

            // Kembalikan response JSON tanpa resource
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $perumahan,
                // Mengembalikan data model langsung

            ], 201);

        } catch (\Exception $e) {
            // Tangkap error dan kirimkan ke response
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage(), // Cetak pesan error
            ], 500);
        }
    }

    // Show All
    public function index()
    {
        $perumahan = Perumahan::all();
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diambil',
            'data' => $perumahan,
        ], 200);
    }

    // Show By ID
    public function show($id)
    {
        $perumahan = Perumahan::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diambil',
            'data' => $perumahan,
        ], 200);
    }


    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'nama_perumahan' => 'string|max:255',
                'alamat' => 'string|max:255',
                'harga' => 'string|max:255',
                'luas' => 'string|max:255',
                'deskripsi' => 'string',
                'kontak' => 'string|max:12',
                'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Temukan data berdasarkan ID
            $perumahan = Perumahan::findOrFail($id);

            // Proses jika ada file gambar baru
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($perumahan->gambar && Storage::disk('public')->exists($perumahan->gambar)) {
                    Storage::disk('public')->delete($perumahan->gambar);
                }

                // Simpan gambar baru ke folder 'gambar' di disk 'public'
                $gambarPath = $request->file('gambar')->store('gambar', 'public');
                $validated['gambar'] = $gambarPath;
            }

            // Perbarui data dengan data yang telah divalidasi
            $perumahan->update($validated);

            // Kembalikan respons sukses
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diupdate',
                'data' => $perumahan,
            ], 200);

        } catch (ValidationException $e) {
            // Tangani error validasi
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            // Log error lainnya
            Log::error('Error saat memperbarui data: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'error' => $e->getMessage(), // Kirimkan pesan error untuk debugging
            ], 500);
        }
    }

    // delete perumahan
    public function delete($id)
    {
        $perumahan = Perumahan::findOrFail($id);

        if (!$perumahan) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        if ($perumahan->gambar && Storage::disk('public')->exists($perumahan->gambar)) {
            Storage::disk('public')->delete($perumahan->gambar);
        }

        $perumahan->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus',
        ], 200);
    }

}


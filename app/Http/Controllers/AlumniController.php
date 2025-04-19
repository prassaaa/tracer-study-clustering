<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class AlumniController extends Controller
{
    /**
     * Display a listing of the alumni.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Hanya admin yang dapat melihat daftar seluruh alumni
        if (Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk mengakses halaman ini.');
        }
        
        $alumni = Alumni::with('user')->paginate(10);
        
        return view('alumni.index', compact('alumni'));
    }

    /**
     * Show the form for creating a new alumni.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Jika user sudah memiliki profil alumni, redirect ke halaman edit
        if (Gate::allows('alumni') && Auth::user()->alumni) {
            return redirect()->route('alumni.edit', Auth::user()->alumni->id)
                ->with('info', 'Anda sudah memiliki profil alumni.');
        }
        
        $majors = $this->getMajorOptions();
        
        return view('alumni.create', compact('majors'));
    }

    /**
     * Store a newly created alumni in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nim' => 'required|string|unique:alumni,nim',
            'full_name' => 'required|string|max:255',
            'graduation_year' => 'required|integer|min:1990|max:' . date('Y'),
            'major' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
        
        if (Gate::allows('admin') && $request->has('create_user')) {
            // Admin membuat alumni baru beserta user
            $userData = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
            ]);
            
            $user = User::create([
                'name' => $validatedData['full_name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => 'alumni'
            ]);
            
            $alumni = new Alumni($validatedData);
            $alumni->user_id = $user->id;
            $alumni->save();
            
            return redirect()->route('alumni.index')
                ->with('success', 'Data alumni berhasil ditambahkan!');
        } else {
            // User alumni membuat profilnya sendiri
            $alumni = new Alumni($validatedData);
            $alumni->user_id = Auth::id();
            $alumni->save();
            
            return redirect()->route('dashboard')
                ->with('success', 'Profil alumni berhasil dibuat!');
        }
    }

    /**
     * Display the specified alumni.
     *
     * @param  \App\Models\Alumni  $alumni
     * @return \Illuminate\Http\Response
     */
    public function show(Alumni $alumni)
    {
        // Cek apakah user adalah admin atau alumni yang bersangkutan
        if (Gate::denies('admin') && Auth::id() !== $alumni->user_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk melihat profil ini.');
        }
        
        // Load relasi yang dibutuhkan
        $alumni->load(['user', 'employmentData' => function ($query) {
            $query->orderBy('start_date', 'desc');
        }]);
        
        return view('alumni.show', compact('alumni'));
    }

    /**
     * Show the form for editing the specified alumni.
     *
     * @param  \App\Models\Alumni  $alumni
     * @return \Illuminate\Http\Response
     */
    public function edit(Alumni $alumni)
    {
        // Cek apakah user adalah admin atau alumni yang bersangkutan
        if (Gate::denies('admin') && Auth::id() !== $alumni->user_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk mengedit profil ini.');
        }
        
        $majors = $this->getMajorOptions();
        
        return view('alumni.edit', compact('alumni', 'majors'));
    }

    /**
     * Update the specified alumni in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Alumni  $alumni
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alumni $alumni)
    {
        // Cek apakah user adalah admin atau alumni yang bersangkutan
        if (Gate::denies('admin') && Auth::id() !== $alumni->user_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk mengedit profil ini.');
        }
        
        $validatedData = $request->validate([
            'nim' => [
                'required',
                'string',
                Rule::unique('alumni', 'nim')->ignore($alumni->id),
            ],
            'full_name' => 'required|string|max:255',
            'graduation_year' => 'required|integer|min:1990|max:' . date('Y'),
            'major' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);
        
        $alumni->update($validatedData);
        
        // Jika yang update data adalah admin, redirect ke daftar alumni
        if (Gate::allows('admin')) {
            return redirect()->route('alumni.index')
                ->with('success', 'Data alumni berhasil diperbarui!');
        }
        
        return redirect()->route('dashboard')
            ->with('success', 'Profil alumni berhasil diperbarui!');
    }

    /**
     * Remove the specified alumni from storage.
     *
     * @param  \App\Models\Alumni  $alumni
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alumni $alumni)
    {
        // Hanya admin yang bisa menghapus data alumni
        if (Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk menghapus data.');
        }
        
        $alumni->delete();
        
        return redirect()->route('alumni.index')
            ->with('success', 'Data alumni berhasil dihapus!');
    }
    
    /**
     * Get the list of available majors.
     */
    private function getMajorOptions()
    {
        return [
            'Informatika' => 'Informatika',
            'Sistem Informasi' => 'Sistem Informasi',
            'Teknik Komputer' => 'Teknik Komputer',
            'Teknik Elektro' => 'Teknik Elektro',
            'Teknik Sipil' => 'Teknik Sipil',
            'Teknik Mesin' => 'Teknik Mesin',
            'Manajemen' => 'Manajemen',
            'Akuntansi' => 'Akuntansi',
            'Ekonomi Pembangunan' => 'Ekonomi Pembangunan',
            'Ilmu Komunikasi' => 'Ilmu Komunikasi',
            'Hubungan Internasional' => 'Hubungan Internasional',
            'Sastra Inggris' => 'Sastra Inggris',
        ];
    }
}
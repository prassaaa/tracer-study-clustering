<?php

namespace App\Http\Controllers;

use App\Models\EmploymentData;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EmploymentDataController extends Controller
{
    /**
     * Display a listing of the employment data.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Admin melihat semua data pekerjaan alumni
        if (Gate::allows('admin')) {
            $employmentData = EmploymentData::with('alumni')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
            return view('employment_data.index', compact('employmentData'));
        }
        
        // Alumni hanya melihat data pekerjaan miliknya
        if (!Auth::user()->alumni) {
            return redirect()->route('alumni.create')
                ->with('message', 'Silakan lengkapi profil alumni terlebih dahulu.');
        }
        
        $employmentData = EmploymentData::where('alumni_id', Auth::user()->alumni->id)
            ->orderBy('start_date', 'desc')
            ->get();
            
        return view('employment_data.index', compact('employmentData'));
    }

    /**
     * Show the form for creating a new employment data.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Alumni harus memiliki profil terlebih dahulu
        if (Gate::allows('alumni') && !Auth::user()->alumni) {
            return redirect()->route('alumni.create')
                ->with('message', 'Silakan lengkapi profil alumni terlebih dahulu.');
        }
        
        $industries = EmploymentData::getIndustryOptions();
        
        // Jika admin, tampilkan form dengan pilihan alumni
        if (Gate::allows('admin')) {
            $alumni = Alumni::orderBy('full_name')->get();
            return view('employment_data.create', compact('industries', 'alumni'));
        }
        
        return view('employment_data.create', compact('industries'));
    }

    /**
     * Store a newly created employment data in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'industry' => 'required|string|in:' . implode(',', array_keys(EmploymentData::getIndustryOptions())),
            'salary' => 'nullable|numeric|min:0',
            'waiting_period' => 'required|integer|min:0',
            'is_relevant' => 'sometimes|boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current_job' => 'sometimes|boolean',
            'alumni_id' => 'sometimes|exists:alumni,id',
        ]);
        
        $validatedData['is_relevant'] = $request->has('is_relevant');
        $validatedData['is_current_job'] = $request->has('is_current_job');
        
        // Jika end_date kosong dan is_current_job true, set end_date menjadi null
        if ($validatedData['is_current_job'] && empty($validatedData['end_date'])) {
            $validatedData['end_date'] = null;
        }
        
        // Jika admin yang menambah data, gunakan alumni_id dari form
        if (Gate::allows('admin')) {
            if (!isset($validatedData['alumni_id'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Silakan pilih alumni.');
            }
        } else {
            // Jika alumni yang menambah data, gunakan alumni_id user yang login
            $validatedData['alumni_id'] = Auth::user()->alumni->id;
        }
        
        // Jika is_current_job true, set semua pekerjaan lain milik alumni ini menjadi bukan current job
        if ($validatedData['is_current_job']) {
            EmploymentData::where('alumni_id', $validatedData['alumni_id'])
                ->where('is_current_job', true)
                ->update(['is_current_job' => false]);
        }
        
        EmploymentData::create($validatedData);
        
        return redirect()->route('employment-data.index')
            ->with('success', 'Data pekerjaan berhasil ditambahkan!');
    }

    /**
     * Display the specified employment data.
     *
     * @param  \App\Models\EmploymentData  $employmentData
     * @return \Illuminate\Http\Response
     */
    public function show(EmploymentData $employmentData)
    {
        // Cek apakah user adalah admin atau alumni yang bersangkutan
        if (Gate::denies('admin') && 
            (!Auth::user()->alumni || Auth::user()->alumni->id !== $employmentData->alumni_id)) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk melihat data ini.');
        }
        
        return view('employment_data.show', compact('employmentData'));
    }

    /**
     * Show the form for editing the specified employment data.
     *
     * @param  \App\Models\EmploymentData  $employmentData
     * @return \Illuminate\Http\Response
     */
    public function edit(EmploymentData $employmentData)
    {
        // Cek apakah user adalah admin atau alumni yang bersangkutan
        if (Gate::denies('admin') && 
            (!Auth::user()->alumni || Auth::user()->alumni->id !== $employmentData->alumni_id)) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk mengedit data ini.');
        }
        
        $industries = EmploymentData::getIndustryOptions();
        
        // Jika admin, tampilkan form dengan pilihan alumni
        if (Gate::allows('admin')) {
            $alumni = Alumni::orderBy('full_name')->get();
            return view('employment_data.edit', compact('employmentData', 'industries', 'alumni'));
        }
        
        return view('employment_data.edit', compact('employmentData', 'industries'));
    }

    /**
     * Update the specified employment data in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmploymentData  $employmentData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmploymentData $employmentData)
    {
        // Cek apakah user adalah admin atau alumni yang bersangkutan
        if (Gate::denies('admin') && 
            (!Auth::user()->alumni || Auth::user()->alumni->id !== $employmentData->alumni_id)) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk mengedit data ini.');
        }
        
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'industry' => 'required|string|in:' . implode(',', array_keys(EmploymentData::getIndustryOptions())),
            'salary' => 'nullable|numeric|min:0',
            'waiting_period' => 'required|integer|min:0',
            'is_relevant' => 'sometimes|boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current_job' => 'sometimes|boolean',
            'alumni_id' => 'sometimes|exists:alumni,id',
        ]);
        
        $validatedData['is_relevant'] = $request->has('is_relevant');
        $validatedData['is_current_job'] = $request->has('is_current_job');
        
        // Jika end_date kosong dan is_current_job true, set end_date menjadi null
        if ($validatedData['is_current_job'] && empty($validatedData['end_date'])) {
            $validatedData['end_date'] = null;
        }
        
        // Jika admin yang mengedit data, gunakan alumni_id dari form
        if (Gate::allows('admin') && isset($validatedData['alumni_id'])) {
            $alumniId = $validatedData['alumni_id'];
        } else {
            // Tetap gunakan alumni_id yang ada
            $alumniId = $employmentData->alumni_id;
            $validatedData['alumni_id'] = $alumniId;
        }
        
        // Jika is_current_job true, set semua pekerjaan lain milik alumni ini menjadi bukan current job
        if ($validatedData['is_current_job'] && !$employmentData->is_current_job) {
            EmploymentData::where('alumni_id', $alumniId)
                ->where('is_current_job', true)
                ->update(['is_current_job' => false]);
        }
        
        $employmentData->update($validatedData);
        
        return redirect()->route('employment-data.index')
            ->with('success', 'Data pekerjaan berhasil diperbarui!');
    }

    /**
     * Remove the specified employment data from storage.
     *
     * @param  \App\Models\EmploymentData  $employmentData
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmploymentData $employmentData)
    {
        // Cek apakah user adalah admin atau alumni yang bersangkutan
        if (Gate::denies('admin') && 
            (!Auth::user()->alumni || Auth::user()->alumni->id !== $employmentData->alumni_id)) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak memiliki izin untuk menghapus data ini.');
        }
        
        $employmentData->delete();
        
        return redirect()->route('employment-data.index')
            ->with('success', 'Data pekerjaan berhasil dihapus!');
    }
}
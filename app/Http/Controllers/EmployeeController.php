<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Position;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('position')->paginate(15);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $positions = Position::all();
        return view('employees.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:employees',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'position_id' => 'required|exists:positions,id',
            'education_level' => 'required|in:SMA,D3,S1,S2,S3',
            'work_experience' => 'required|integer|min:0',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string'
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    public function show(Employee $employee)
    {
        $employee->load(['position', 'assessments.scores.criteria', 'trainingNeeds']);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $positions = Position::all();
        return view('employees.edit', compact('employee', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'nip' => 'required|unique:employees,nip,' . $employee->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'position_id' => 'required|exists:positions,id',
            'education_level' => 'required|in:SMA,D3,S1,S2,S3',
            'work_experience' => 'required|integer|min:0',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string'
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Data pegawai berhasil diperbarui!');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Data pegawai berhasil dihapus!');
    }
}

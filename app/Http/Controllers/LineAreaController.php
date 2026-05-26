<?php

namespace App\Http\Controllers;

use App\Models\LineArea;
use App\Models\Department;
use Illuminate\Http\Request;

class LineAreaController extends Controller
{
    public function index()
    {
        $lineAreas = LineArea::latest()->paginate(10);

        return view('line-areas.index', compact('lineAreas'));
    }

    public function create()
    {
        $departments = Department::where('status', 'Active')
            ->orderBy('name')
            ->get();

        return view('line-areas.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:line_areas,name',
            'code' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
        ]);

        LineArea::create($validated);

        return redirect()->route('line-areas.index')
            ->with('success', 'Line / Area berhasil ditambahkan.');
    }

    public function edit(LineArea $lineArea)
    {
        $departments = Department::where('status', 'Active')
            ->orderBy('name')
            ->get();

        return view('line-areas.edit', compact('lineArea', 'departments'));
    }

    public function update(Request $request, LineArea $lineArea)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:line_areas,name,' . $lineArea->id,
            'code' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $lineArea->update($validated);

        return redirect()->route('line-areas.index')
            ->with('success', 'Line / Area berhasil diperbarui.');
    }

    public function destroy(LineArea $lineArea)
    {
        $lineArea->delete();

        return redirect()->route('line-areas.index')
            ->with('success', 'Line / Area berhasil dihapus.');
    }
}

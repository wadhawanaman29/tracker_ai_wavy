<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CompanyLeaves;
use Illuminate\Http\Request;

class CompanyLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaves = CompanyLeaves::latest()->paginate(10);

        return view('company-leaves.index', compact('leaves'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('company-leaves.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'leave_type' => 'required',
        ]);

        CompanyLeaves::create([
            'name'        => $request->name,
            'leave_type'  => $request->leave_type,
            // 'start_date'  => $request->start_date,
            // 'end_date'    => $request->end_date,
            'leave_date'  => $request->leave_date,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
        ]);

        return redirect()
            ->route('add-leave')
            ->with('success', 'Company leave added successfully.');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $leave = CompanyLeaves::findOrFail($id);

        $leave->delete();

        return redirect()
            ->route('view-leave')
            ->with('success', 'Leave deleted successfully.');
    }
}

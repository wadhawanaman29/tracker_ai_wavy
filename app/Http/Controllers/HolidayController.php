<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;

class HolidayController extends Controller
{

    
    // public function index()
    // {
    //     // $holidays = Holiday::all(); // Fetch all holidays
    //     $holidays = Holiday::orderBy('date', 'asc')->get();

    //     return view('holidays.index', compact('holidays'));
    // }

    public function index(Request $request)
    {

        $sortColumn = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'desc');

        $holidays = Holiday::orderBy($sortColumn, $sortDirection)->paginate(10); // Adjust pagination as needed
    
        if ($request->ajax()) {
            $html = '';
            $startSerialNumber = ($holidays->currentPage() - 1) * $holidays->perPage() + 1;
    
            if ($holidays->isEmpty()) {
                $html .= '<tr><td colspan="4" class="text-center">No data found</td></tr>';
            } else {
                foreach ($holidays as $holiday) {
                    $html .= '<tr>';
                    $html .= '<td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>' . e($holiday->name) . '</strong></td>';
                    $html .= '<td>' . date('d-m-Y', strtotime($holiday->date)) . '</td>';
                    $html .= '<td>';
                    $html .= '<a href="' . route('deleteholiday', $holiday->id) . '" class="delete-btn" data-form-id="deleteForm' . $holiday->id . '" onclick="submitDeleteForm(' . $holiday->id . ')">';
                    $html .= '<i class="bx bx-trash me-1"></i>';
                    $html .= '</a>';
                    // $html .= '<form action="' . route('deleteholiday', $holiday->id) . '" method="POST" id="deleteForm' . $holiday->id . '" style="display: none;">';
                    // $html .= '@csrf @method('DELETE')';
                    // $html .= '</form>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            }
    
            return response()->json(['html' => $html]);
        }
    
        return view('holidays.index', compact('holidays'));
    }
    

    
    public function create()
    {
        return view('holidays.create');
    }

    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        // Create new holiday record
        Holiday::create([
            'name' => $request->name,
            'date' => $request->date,
        ]);

        return redirect()->route('view-holiday')->with('success', 'Holiday added successfully.');
    }


    public function deleteholiday(Holiday $id){
        $id->delete();
        return redirect()->route('view-holiday')
                        ->with('success','Holiday deleted successfully');
    }
}

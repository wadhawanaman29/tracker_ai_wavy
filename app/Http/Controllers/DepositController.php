<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\User;
use App\Notifications\DepositSuccessful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function markAsRead(){
        Auth::user()->unreadNotifications->markAsRead();
        // return redirect()->back();
        return view('notification.index');
    }

    // public function deposit(Request $request){
    //     $deposit = Deposit::create([
    //         // 'user_id' =>Auth::user()->id,
    //         'notification'  => $request->notification,
    //         'startdate'  => $request->start_date,
    //         'enddate'  => $request->end_date
    //     ]);
    //     User::find(Auth::user()->id)->notify(new DepositSuccessful($deposit->notification));
    
    //     //return redirect()->back()->with('status','Your deposit was successful!');
    //    // return view('notification');
    //    return redirect()->route('notification')->with('status', 'Your deposit was successful!');

    // }

    public function deposit(Request $request)
{
    // Validate input
    $request->validate([
        'notification' => 'required',
        'start_date' => 'required|date',
        'end_date' => [
            'required',
            'date',
            function ($attribute, $value, $fail) use ($request) {
                if (strtotime($value) < strtotime($request->start_date)) {
                    $fail('The end date must be equal to or after the start date.');
                }
            },
        ],
    ]);
    
    
    // Create deposit
    $deposit = Deposit::create([
        // Assuming 'user_id' needs to be set, uncomment if necessary:
        // 'user_id' => Auth::user()->id,
        'notification' => $request->notification,
        'startdate' => $request->start_date,
        'enddate' => $request->end_date
    ]);

    // Notify user about deposit
    User::find(Auth::user()->id)->notify(new DepositSuccessful($deposit->notification));

    // Redirect with success message
    return redirect()->route('notification')->with('status', 'Your deposit was successful!');
}

   
    public function notification(Request $request){
        Auth::user()->unreadNotifications->markAsRead();
    
        $allDeposits = Deposit::all();
        // return redirect()->back()->with('status','Your deposit was successful!');

       //dd($allDeposits);
        return view('notification.notification')->with('deposits', $allDeposits);
    }

    public function deletenotification(Deposit $id){
        $id->delete();
        return redirect()->route('notification')
                        ->with('success','Notification deleted successfully');
    }
}

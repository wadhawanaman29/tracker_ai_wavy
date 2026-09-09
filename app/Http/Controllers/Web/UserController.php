<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\GeneralSetting;
use App\Models\Holiday;
use App\Models\Deposit;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Notification;
use App\Notifications\UserloginRequestNotification;

class UserController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->back();
        }
        return view('pages/login');
    }

    public function doLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);



        $credentials = $request->only('email', 'password');
        $credentials['user_status'] = '1';
        $rememberMe = $request->has('remember_me') ? true : false;

        if (Auth::attempt($credentials, $rememberMe)) {
            if (Auth::user()->user_type != 0) {
                // Send email notification to the user
                $userName = check_user_name(Auth::user()->id); // Get username
                $emails = GeneralSetting::where('setting_key', 'email')->pluck('setting_value')->toArray();
                $json_emails = json_decode($emails[0]);
                foreach ($json_emails as $email) {
                    Notification::route('mail', $email)
                        ->notify(new UserloginRequestNotification($userName));
                }
                $request->session()->regenerate();
                return redirect("self_attendence")->with('success', 'Login success');
            } else {
                return redirect("dashboard")->with('success', 'Login success');
            }
        }

        return redirect("/")->with('error', 'You have entered invalid credentials');
    }

    // public function dashboard(){
    //     return view('pages/dashboard');
    // }


    // public function users() {
    //     $users = User::select(
    //         'users.id',
    //         'users.name',
    //         'users.email',
    //         'users.user_type',
    //         'users.user_status',
    //         'users.created_at',
    //         // 'users.email_verified_at',
    //         // 'subscription.payment_type as membership_type'
    //     )
    //     // ->leftJoin('user_info', 'users.id', '=', 'user_info.user_id')
    //     // ->leftJoin('subscription', 'user_info.membership_id', '=', 'subscription.id')
    //     ->where('users.user_type', '!=', '0' )
    //     ->where('users.delete_status', '!=', '1' )
    //     ->paginate(10);

    //     return view('pages/users', ['data' => $users]);
    // }
    public function users(Request $request)
    {
        $sort = ['name', 'email'];

        $direction = ['asc', 'desc'];
        $sortColumn = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        if (!in_array($sortColumn, $sort)) {
            $sortColumn = 'name';
        }
        if (!in_array($sortDirection, $direction)) {
            $sortDirection = 'asc';
        }
        $query = User::select('id', 'name', 'email', 'user_status')
            ->where('user_type', '!=', '0')
            ->where('delete_status', '!=', '1');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }
        $users = $query->orderBy($sortColumn, $sortDirection)
            ->paginate(15)
            ->appends($request->except('page'));

        if ($request->ajax()) {
            $html = '';
            $startSerialNumber = ($users->currentPage() - 1) * $users->perPage() + 1;

            if ($users->isEmpty()) {
                $html .= '<tr><td colspan="5" class="text-center">No data found</td></tr>';
            } else {
                foreach ($users as $userdata) {
                    $status = $userdata->user_status == 1 ?
                        '<span class="badge bg-label-success me-1 status" data-user="' . $userdata->id . '">Active</span>' :
                        '<span class="badge bg-label-danger me-1 status" data-user="' . $userdata->id . '" >Inactive</span>';

                    $html .= '<tr>
                                <td>' . $startSerialNumber++ . '</td>
                                <td>' . $userdata->name . '</td>
                                <td>' . $userdata->email . '</td>
                                <td>' . $status . '</td>
                                <td>
                                    <a href="' . route('editUser', $userdata->id) . '"><i class="bx bx-edit-alt me-1"></i></a>
                                    <a href="' . route('deleteUser', $userdata->id) . '" class="delete-btn" data-form-id="deleteForm' . $userdata->id . '" onclick="submitDeleteForm(' . $userdata->id . ')">
                                        <i class="bx bx-trash me-1"></i>
                                    </a>
                                
                                </td>
                            </tr>';
                }
            }

            return response()->json(['html' => $html]);
        } else {
            return view('pages.users', ['data' => $users]);
        }
    }

    // <a href="javascript:void(0);" class="delete-btn" data-form-id="deleteForm' . $userdata->id . '" onclick="submitDeleteForm(' . $userdata->id . ')">
    // <i class="bx bx-trash me-1"></i>
    // </a>
    // <form action="' . route('deleteUser', $userdata->id) . '" method="get" id="deleteForm' . $userdata->id . '" style="display: none;">
    // @csrf
    // @method('delete')
    // </form>


    public function addUser()
    {
        return view('pages/add_user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'designation' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'employment_status' => 'nullable|in:permanent,probation',
            // 'user_type' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'designation' => $request->designation,
            'password' => Hash::make($request->password),
            'employment_status' => $request->employment_status ?? 'permanent',
        ]);

        if ($user) {
            return redirect()->route('users')->with('success', 'User added successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to create user.');
        }
    }



    public function editUser(User $id)
    {
        $customColumnValue = $id->designation;
        return view('pages/add_user', compact('id'), ['designation' => $customColumnValue]);
    }


    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'password' => 'sometimes|nullable|min:6',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'employment_status' => 'nullable|in:permanent,probation',
        ]);

        try {
            $user = User::findOrFail($id);
            $user->name = $validated['name'];
            $user->designation = $validated['designation'];
            $user->employment_status = $validated['employment_status'] ?? $user->employment_status;

            if (!empty($validated['email']) && $validated['email'] !== $user->email) {
                $user->email = $validated['email'];
            }
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            return redirect()->route('users')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }



    public function deleteUser($userId)
    {
        // Assuming 'User' is the model name
        User::where('id', $userId)->update(['delete_status' => '1']);

        return redirect()->route('users')->with('success', 'User deleted successfully');
    }

    public function getUser()
    {
        $users = User::select(
            '*'
        )
            ->where('users.user_status', '!=', '0')
            ->where('users.user_type', '!=', '0')->get();


        return $users->toArray();
    }



    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }

    public function changePassword()
    {
        return view('pages/change_password');
    }

    public function storeChangePassword(Request $request)
    {

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);


        $user = Auth::user();




        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'The current password is incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    // public function dashboard(){
    //     // $holidays = Holiday::all(); // Fetch all holidays
    //     $holidays = Holiday::orderBy('date', 'asc')->get();
    //     // $deposits = Deposit::all();
    //     $currentDate = Carbon::now()->toDateString(); // Get current date
    //     $deposits = Deposit::whereDate('startdate', '<=', $currentDate)
    //                     ->whereDate('enddate', '>=', $currentDate)
    //                     ->get();
    //     return view('pages/dashboard', compact('holidays','deposits'));
    // }



    public function dashboard(Request $request)
    {
        // $holidays = Holiday::all(); // Fetch all holidays
        $holidays = Holiday::orderBy('date', 'asc')->get();
        // $deposits = Deposit::all();
        $currentDate = Carbon::now()->toDateString(); // Get current date
        $deposits = Deposit::whereDate('startdate', '<=', $currentDate)
            ->whereDate('enddate', '>=', $currentDate)
            ->get();

        //    if (isset($request->selected_date) && $request->selected_date !== '') {
        //         $selectedDate = $request->selected_date;
        //         $data = getWeekWiseDataCount($selectedDate);

        //     }else{
        $data = getWeekWiseDataCount();
        //       echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die("opo");

        // }
        return view('pages/dashboard', compact('holidays', 'deposits', 'data'));
    }


    public function dashboarddata(Request $request)
    {

        $selectedDate = $request->selected_date;
        $data = getWeekWiseDataCount($selectedDate);
        // echo '<pre>';
        // print_r($data);
        // die('ooooo');
        return $data;
    }

    public function addEmail()
    {
        $existingSetting = GeneralSetting::where('setting_key', 'email')->first();
        $emails = $existingSetting ? json_decode($existingSetting->setting_value) : null;
        return view('pages.add_email', compact('emails'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'email' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $emails = array_map('trim', explode(',', $value));

                    foreach ($emails as $email) {
                        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $fail("The $attribute contains invalid email format.");
                        }
                    }
                },
            ],
        ]);

        $emailsString = $request->input('email');
        // Explode the input string by commas to get an array of emails
        $emailsArray = array_map('trim', explode(',', $emailsString));
        // Convert array to JSON format
        $jsonEmails = json_encode($emailsArray);

        // Check if 'email' setting already exists
        $existingSetting = GeneralSetting::where('setting_key', 'email')->first();

        if ($existingSetting) {
            // If setting exists, update its value
            $existingSetting->update(['setting_value' => $jsonEmails]);
        } else {
            // If setting does not exist, create a new setting
            GeneralSetting::create([
                'setting_key' => 'email',
                'setting_value' => $jsonEmails,
            ]);
        }

        return redirect()->back()->with('success', 'Emails added successfully!');
    }


    public function updateStatus(Request $request, $id)
    {

        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Invalid User!",
            ], 400);
        }

        if ($user->user_status === '1') {
            $user->user_status = '0';
        } else if ($user->user_status === '0') {
            $user->user_status = '1';
        }

        $user->save();

        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => "User Status Updated!",
            'status' => $user->user_status == 1 ? 'active' : 'inactive'
        ], 200);
    }
}

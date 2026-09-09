<?php 


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request; 
use DB; 
use Carbon\Carbon; 
use App\Models\User; 
use Mail; 
use Hash;
use Illuminate\Support\Str;
  
class ForgotPasswordController extends Controller
{
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function showForgetPasswordForm()
      {
       // echo "rana";
         return view('forgetpassword.forgetPassword');
      }
  
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function submitForgetPasswordForm(Request $request)
      {
          $request->validate([
              'email' => 'required|email|exists:users',
          ]);
  
          $token = Str::random(64);
  
        $existingToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if ($existingToken) {
        // Email already exists, show a message to the user
        return redirect()->back()->with('message', 'A password reset email has already send.');
        }

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email, 
            'token' => $token, 
            'created_at' => Carbon::now()
          ]);
          
  
          Mail::send('forgetpassword.forgetPasswordemail', ['token' => $token, 'email' => $request->email], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });
        
  
          return back()->with('message', 'We have email your password reset link!');
      }
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function showResetPasswordForm($token) { 

        $passwordResetToken = DB::table('password_reset_tokens')
        ->where('token', $token)
        ->first(); // Retrieve the first matching record

        // $email = $passwordResetToken->email;
        $email = isset($passwordResetToken->email) ? $passwordResetToken->email : '';

        return view('forgetpassword.forgetPasswordLink', ['token' => $token,'email' => $email]);
      }
  
      /**
       * Write code on Method
       *
       * @return response()
       */
    //   public function submitResetPasswordForm(Request $request)
    //   {
    //       $request->validate([
    //           'email' => 'required|email|exists:users',
    //           'password' => 'required|string|min:6|confirmed',
    //           'password_confirmation' => 'required'
    //       ]);
  
    //       $updatePassword = DB::table('password_reset_tokens')
    //                           ->where([
    //                             'email' => $request->email, 
    //                             'token' => $request->token
    //                           ])
    //                           ->first();
  
    //       if(!$updatePassword){
    //           return back()->withInput()->with('error', 'Invalid token!');
    //       }
  
    //       $user = User::where('email', $request->email)
    //                   ->update(['password' => Hash::make($request->password)]);
 
    //       DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();
  
    //       return redirect('/')->with('message', 'Your password has been changed!');
    //   }
    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_reset_tokens')
                            ->where('email', $request->email)
                            ->where('token', $request->token)
                            ->where('created_at', '>=', Carbon::now()->subHours(1)) // Adjust the expiration time as needed
                            ->first();

        if(!$updatePassword){
           // return back()->withInput()->with('error', 'Invalid or expired token!');
            return back()->with('message', 'Invalid or expired token!');
        }

        $user = User::where('email', $request->email)
                    ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/')->with('updatepassword', 'Your password has been changed!');
    }

}
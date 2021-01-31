<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Auth;
use Artisan;
use Illuminate\Support\MessageBag;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login()

        {

            $phone_number='';
            date_default_timezone_set('America/Sao_Paulo');
            $email=$_POST['email'];
            $password=str_replace("-","",$_POST['password']);

            $mailCount = DB::table('ea_users')
            ->where('ea_users.email', $email)
            ->where('ea_users.id_roles', '3')
            ->select('ea_users.email')
            ->count();

            if($mailCount>0){

                $phone_password = DB::table('ea_users')
                ->where('ea_users.email', $email)
                ->where('ea_users.id_roles', '3')
                ->select('ea_users.phone_number')
                ->get();



                foreach($phone_password as $index){
                    $phone_number=$index->phone_number;
                }

            }else{

                return redirect()->back()
                ->withErrors([
                    $this->username() => 'E-mail inválido, tente novamente!',
                ]);

            }




        if(isset($phone_number)){

            $phone_number=str_replace("-","",$phone_number);

            if($phone_number==$password){


                $email=$_POST['email'];

                $now=date('Y-m-d').' 00:00';

                $users = DB::table('ea_appointments')
                ->where('ea_users.email', $email)
                ->where('ea_users.id_roles', '3')
                ->where('start_datetime', '>=', $now)
                ->join ('ea_users','ea_users.id','=','ea_appointments.id_users_customer')
                ->join ('ea_services','ea_services.id','=','ea_appointments.id_services')
                ->join ('ea_users as ea_users2','ea_appointments.id_users_provider','=','ea_users2.id')
                ->select('ea_users.email','ea_users.phone_number','start_datetime','end_datetime','hash','ea_services.name','ea_appointments.notes','ea_users2.last_name','ea_users.id_roles')
                ->orderBy('start_datetime', 'asc')
                ->get();

                return redirect('home')->with('users', $users);

            }else{

                return redirect()->back()
                ->withErrors([
                    'password' => 'Senha inválida',
                ]);

            }
        }else{

            return redirect()->back()
            ->withErrors([
                'password' => 'Senha inválida',
            ]);

        }


    }

        Public function logout()
        {
         Auth::logout();
         session_unset();
        //  Artisan::call('cache:clear');
         return redirect('login');

        }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\BankAccounte;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    protected $db_mysql;
    public function __construct()
    {
        $this ->db_mysql= config('database.connections.mysql.database');
     $this->middleware('auth:api',['except'=>['login','register']]);
    }
    /**
     * Register
     */
    public function register (Request $request)
    {
         //$cvc= DB::connection('mysql_bank')->table('bankaccounts')->select('cvc');
        $validator =Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8',
            'card_number'=>'required|exists:mysql_bank.bank_accountes',
            'cvc'=>'required|exists:mysql_bank.bank_accountes',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        $user=User::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)]
        ));
        $credentials=$request->only(['email','password']);
        $token=Auth::guard('api')->attempt($credentials);
        $banckAccount= BankAccounte::where('card_number',$request->card_number)->first();
        $banckAccount->user_id = $user->id;
        $banckAccount->save();
        return response()->json([
            'message'=>'Register successfully',
            'acces_token'=>$token
        ],201);

    }
    /**
     * Login
     */
    public function login(Request $request)
    {
     $validator =Validator::make($request->all(),[

         'email'=>'required|string|email',
         'password'=>'required|string|min:8',
     ]);
     if ($validator->fails())
     {
         return response()->json($validator->errors()->toJson(),422);
     }
     $credentials=$request->only(['email','password']);

     if(!$token=Auth::guard('api')->attempt($credentials))
     {
       return response()->json(['error'=>'Unauthorized'],401);
     }

     return response()->json([
         'access_token'=>$token,
         'user'=>Auth::guard('api')->user(),

       ]);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
   /* public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
   public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
   /* public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
   /* protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    */


}

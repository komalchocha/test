<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{

    public function registeruser(Request $request)
    {
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),

        ]);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $stripe = new \Stripe\StripeClient(env("STRIPE_SECRET"));

        $stripe->customers->create([
            "name" => $user->name,
            "email" => $user->email,

        ]);

        return response(['user' => $user, 'access_token' => $success]);
    }
    public function loginuser(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user['password'])) {
            return response([
                'message' => ['These Password and Email does not match.']
            ]);
        }
        $success['token'] = $user->createToken('MyApp')->accessToken;

        return response(['user' => $user, 'access_token' => $success,  'message' => 'user login successfully']);
    }
    public function addCard(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env("STRIPE_SECRET"));
        $data =  $stripe->tokens->create([
            'card' => [
                'number' => $request->number,
                'exp_month' => $request->exp_month,
                'exp_year' => $request->exp_year,
                'cvc' => $request->cvc,
                'customer' => $request->customer,
            ],
        ]);
        $charges = $stripe->charges->create([
            'amount' => $request->amount * 100,
            'currency' => $request->currency,
            'source' => $data->id,
            'description' => $request->description,
        ]);
        DD($charges);

        return response($charges);
    }
}

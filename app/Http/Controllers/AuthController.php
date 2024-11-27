<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * ユーザー登録
     */
    public function register(Request $request)
    {
        // バリデーション
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        // トークンの生成
        $token = Str::random(40);

        // ユーザーの作成
        $user = User::create([
            'email' => $request->email,
            'email_verification_token' => $token,
        ]);

        // トークンをメールで送信
        Mail::to($user->email)->send(new VerificationMail($token));

        // レスポンス
        return response()->json(['message' => 'Verification token sent.'], 200);
    }

    /**
     * メールアドレス認証
     */

    public function verify(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'name' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email_verification_token', $request->token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token.'], 400);
        }

        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return response()->json(['message' => 'User verified and registered successfully.'], 200);
    }


    /**
     * ログイン
     */
    public function login(Request $request)
    {
        // バリデーション
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // ユーザーの取得
        $user = User::where('email', $request->email)->first();

        // ユーザーが存在し、パスワードが正しい場合に認証
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json(['message' => 'Login successful', 'token' => $token]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}

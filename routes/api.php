<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\AuthController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/articles/{article}/comments', [CommentController::class, 'store']);  //コメントを投稿
    Route::put('/comments/{comment}', [CommentController::class, 'update']);  //コメントを編集
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);  //コメントを削除
});

Route::get('/articles/{article}/comments', [CommentController::class, 'articleindex']); //記事ごとのコメントを取得
Route::get('/users/{user}/comments', [CommentController::class, 'userindex']);  //ユーザーごとのコメントを取得

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']); // 会員登録（メールアドレス入力）
    Route::post('verify', [AuthController::class, 'verify']);     // アカウント認証（名前とパスワード設定）
    Route::post('login', [AuthController::class, 'login']);       // ログイン
});



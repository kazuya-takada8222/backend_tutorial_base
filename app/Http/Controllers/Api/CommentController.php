<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;

class CommentController extends Controller
{

    //記事ごとにコメントを取得
    public function articleindex(Article $article)
    {
        $comments = $article->comments()
            ->orderBy('created_at', 'desc') //新しい順にソート
            ->get();

        return response()->json($comments, 200);
    }

    //ユーザーごとのコメントを取得
    public function userindex(User $user)
    {
        $comments = $user->comments;
        return response()->json($comments, 200);
    }

    //新しいコメントを投稿
    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:10|max:100',
        ]);

        $comment = $article->comments()->create([
            'content' => $validated['content'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json($comment, 200);
    }

    //コメントを編集
    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'sometimes|required|string|min:10|max:100',
        ]);

        //自分の投稿のみ編集可にする
        if($comment->user_id !== $request->user()->id){
            return response()->json(['message' => 'You are not authorized to update this comment'], 403);
        }

        $comment->update([
            'content' => $validated['content'],
        ]);

        return response()->json($comment, 200);
    }

    //コメントを削除する
    public function destroy(Request $request, Comment $comment)
    {

        //自分の投稿のみ削除可にする
        if($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'You are not authorized to delete this comment'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted', 200]);
    }
}

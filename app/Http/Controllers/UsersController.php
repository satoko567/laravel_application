<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 

class UsersController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);

        // 自分の投稿
        $myStores = $user->stores()->latest()->paginate(9);
        $myStoreCount = $user->stores()->count();

        // いいねした店舗
        $likedStores = $user->likes()->with('user')->latest()->paginate(9);
        $likedStoreCount = $user->likes()->count();

        $data=[
            'user' => $user,
            'myStores' => $myStores,
            'myStoreCount' => $myStoreCount,
            'likedStores' => $likedStores,
            'likedStoreCount' => $likedStoreCount,
        ];

        return view('users.show', $data);
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() !== $user->id) {
            return redirect()->back()->with('error', '不正な操作です。');
        }

        // ① まずユーザーが持つ店舗の画像を削除
        foreach ($user->stores as $store) {
            if ($store->image && strpos($store->image, 'storage/store_images/') === 0) {
                $path = str_replace('storage/', '', $store->image);
                \Storage::disk('public')->delete($path);
            }
        }

        // ② ユーザーが持つ店舗をすべて削除
        $user->stores()->delete();

        // ③ ユーザーを削除
        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', 'ご利用ありがとうございました。アカウントの退会が完了しました。');
    }



}

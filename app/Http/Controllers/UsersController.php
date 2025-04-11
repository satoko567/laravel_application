<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        $myStores = $user->stores()->latest()->paginate(9);

        // 今後のために「いいねした店舗」枠は空のまま
        $likedStores = collect(); 

        $data=[
            'user' => $user,
            'myStores' => $myStores,
            'likedStores' => $likedStores,
        ];

        return view('users.show', $data);
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() !== $user->id) {
            return redirect()->back()->with('error', '不正な操作です。');
        }

        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', 'ご利用ありがとうございました。アカウントの退会が完了しました。');
    }


}

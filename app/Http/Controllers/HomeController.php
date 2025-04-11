<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 全ユーザーから「店舗を持っている人だけ」を取得
        $users = User::whereHas('stores')
            ->with(['latestStore']) // ユーザーの最新店舗を事前取得
            ->get();

        // 各ユーザーの「最新店舗の投稿日時」で並び替え
        $sorted = $users->sortByDesc(function ($user) {
            return optional($user->latestStore)->created_at;
        });

        // ページネーション（1Pあたり9件、全体で最大27件）
        $perPage = 9;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedUsers = new LengthAwarePaginator(
            $sorted->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $currentPage, [
            'path' => $request->url(), 
            'query' => $request->query()
        ]);

        return view('home', ['users' => $pagedUsers]);
    }
}

//下記でも動いた
// $pagedUsers = User::whereHas('stores')
//         ->select('users.*')
//         ->addSelect(DB::raw('(SELECT MAX(created_at) FROM stores WHERE stores.user_id = users.id) as latest_store_created_at'))
//         ->orderByDesc('latest_store_created_at')
//         ->limit(27)
//         ->paginate(9);

//         return view('home', ['users' => $pagedUsers]);
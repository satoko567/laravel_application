<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreRequest;

class StoresController extends Controller
{
    // 一覧表示
    public function index()
    {
        $stores = Store::latest()->paginate(9);
        return view('stores.index', ['stores' => $stores]);
    }

    // 詳細表示
    public function show($id)
    {
    $store = Store::with('user')->findOrFail($id);
    return view('stores.show', ['store' => $store]);
    }

    // 投稿画面
    public function create()
    {
        $user = \Auth::user();
        $stores = $user->stores()->orderBy('id', 'desc')->paginate(9);
        $data = [
            'user' => $user,
            'stores' => $stores,
        ];
        return view('stores.create', $data);
    }

    // 登録処理
    public function store(StoreRequest $request)
    {
        // TODO: ここでGoogle Places APIを使って情報を取得
        // 今は仮データで作成
        $store = Store::create([
            'name' => '仮店舗',
            'google_map_url' => $request->input('google_map_url'),
            'place_id' => '仮ID_' . uniqid(),
            'address' => '新潟県三条市〇〇',
            'latitude' => 37.911111,
            'longitude' => 139.061111,
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('stores.show', $store->id)
            ->with('success', '店舗を登録しました！');
    }


    // 編集画面の表示
    public function edit($id)
    {
        $store = Store::findOrFail($id);

        if (Auth::id() !== $store->user_id) {
            return redirect()->back()->with('error', '編集できません。');
        }

        return view('stores.edit', ['store' => $store]);
    }

    // 編集処理
    public function update(StoreRequest $request, $id)
    {
        $store = Store::findOrFail($id);

        if (Auth::id() !== $store->user_id) {
            return redirect()->back()->with('error', '更新できません。');
        }

        $store->update([
            'google_map_url' => $request->input('google_map_url'),
            'name' => '仮店舗（編集）',
            'address' => '新潟県新潟市△△',
            'place_id' => '仮ID_' . uniqid(),
            'latitude' => 37.911111,
            'longitude' => 139.061111,
        ]);

        return redirect()->route('stores.show', $store->id)
                        ->with('success', '店舗情報を更新しました！');
    }

    // 店舗削除
    public function destroy($id)
    {
        $store = Store::findOrFail($id);
    
        if (Auth::id() !== $store->user_id) {
            return redirect()->back()->with('error', 'この店舗は削除できません。');
        }
        $store->delete();
    
        return redirect()
            ->route('user.show', Auth::id())
            ->with('success', '1件の店舗を削除しました。');
    }

    // 全件削除
    public function bulkDelete()
    {
        $user = Auth::user();
        $user->stores()->delete();

        return redirect()
            ->route('user.show', Auth::id())
            ->with('success', 'すべての店舗を削除しました。');
    }
}

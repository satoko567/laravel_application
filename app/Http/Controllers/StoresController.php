<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreRequest;
use App\Services\GooglePlacesService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
    public function store(Request $request, GooglePlacesService $googlePlacesService)
    {
        // ① 入力バリデーション
        $shortUrl = $request->google_map_url;

        // ② URLから place 情報を取得
        $place = $googlePlacesService->getPlaceDetailsFromSharedUrl($shortUrl);

        if (!$place) {
            return back()->with('error', '店舗情報を取得できませんでした。');
        }

        $placeId = $place['place_id'];

       // ③ 同じユーザーによる重複投稿のチェック
        $existingStore = Store::where('place_id', $placeId)
        ->where('user_id', Auth::id())
        ->first();

        if ($existingStore) {
        return redirect()->route('stores.show', $existingStore->id)
        ->with('info', 'この店舗は既に投稿されています。再度の投稿ありがとう！');
        }


        // ④ サムネイル画像があれば取得、なければデフォルト
        $imageUrl = !empty($place['photos'][0]['photo_reference'])
        ? $googlePlacesService->getPhotoUrl($place['photos'][0]['photo_reference'])
        : asset('images/no_image.jpg');


        // ⑤ DBへ保存
        $store = Store::create([
            'name'         => $place['name'] ?? '不明な店舗',
            'google_map_url' => $shortUrl,
            'place_id'     => $placeId,
            'address'      => $place['formatted_address'] ?? null,
            'latitude'     => $place['geometry']['location']['lat'] ?? null,
            'longitude'    => $place['geometry']['location']['lng'] ?? null,
            'phone_number' => $place['formatted_phone_number'] ?? null,
            'website'      => $place['website'] ?? null,
            'image'        => $imageUrl,
            'user_id'      => Auth::id(),
        ]);

        return redirect()->route('stores.show', $store->id)
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

        // ① 本人以外は編集禁止
        if (Auth::id() !== $store->user_id) {
            return redirect()->back()->with('error', '更新できません。');
        }

        $shortUrl = $request->input('google_map_url');
        $googlePlacesService = app(\App\Services\GooglePlacesService::class);

        // ② URLから詳細取得
        $place = $googlePlacesService->getPlaceDetailsFromSharedUrl($shortUrl);

        if (!$place) {
            return back()->with('error', '店舗情報を取得できませんでした。');
        }

        $placeId = $place['place_id'];

        // ③ 他の投稿と重複していないかチェック
        $duplicate = Store::where('place_id', $placeId)
                        ->where('id', '<>', $store->id)
                        ->first();

        if ($duplicate) {
            return redirect()->route('stores.show', $duplicate->id)
                            ->with('info', 'この店舗はすでに登録されています。再度の登録ありがとう！');
        }

        // ④ 写真の取得
        $photoUrl = !empty($place['photos'][0]['photo_reference'])
            ? $googlePlacesService->getPhotoUrl($place['photos'][0]['photo_reference'])
            : asset('images/no_image.jpg');

        // ⑤ 更新処理
        $store->update([
            'google_map_url' => $shortUrl,
            'name'           => $place['name'] ?? '不明な店舗',
            'address'        => $place['formatted_address'] ?? null,
            'place_id'       => $placeId,
            'latitude'       => $place['geometry']['location']['lat'] ?? null,
            'longitude'      => $place['geometry']['location']['lng'] ?? null,
            'phone_number'   => $place['formatted_phone_number'] ?? null,
            'website'        => $place['website'] ?? null,
            'image'          => $photoUrl,
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

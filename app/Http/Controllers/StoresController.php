<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreRequest;
use App\Services\GooglePlacesService;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Pagination\LengthAwarePaginator;


class StoresController extends Controller
{
    // 一覧表示(いいね数順)
    public function index()
    {
        $stores = Store::withCount('likedUsers')
            ->with('likes') // latestLike のために likes は読み込む
            ->get()
            ->sortByDesc(function ($store) {
                return [$store->liked_users_count, optional($store->latestLike)->created_at];
            })
            ->values();

        // 手動ページネーション
        $perPage = 12;
        $currentPage = request()->input('page', 1);
        $currentItems = $stores->slice(($currentPage - 1) * $perPage, $perPage);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $stores->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('stores.index', ['stores' => $paginated]);
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
        // ① バリデーション
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


        // ④ サムネイル画像があれば取得し保存、なければデフォルト
        $imagePath = null;
        if (!empty($place['photos'][0]['photo_reference'])) {
            $photoUrl = $googlePlacesService->getPhotoUrl($place['photos'][0]['photo_reference']);
            try {
                $imageContents = file_get_contents($photoUrl);
                $filename = 'store_images/' . uniqid() . '.jpg';
                Storage::disk('public')->put($filename, $imageContents);
                $imagePath = 'storage/' . $filename;
            } catch (\Exception $e) {
                $imagePath = asset('images/no_image.jpg');
            }
        } else {
            $imagePath = asset('images/no_image.jpg');
        }     

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
            'image'        => $imagePath,
            'user_id'      => Auth::id(),
        ]);

        return redirect()->route('stores.show', $store->id)
            ->with('post_success', '店舗を登録しました！');
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

        // ④ 写真の取得と保存
        $imagePath = $store->image;
        $oldImagePath = $store->image; // ← 元の画像パスを一時保存

        if (!empty($place['photos'][0]['photo_reference'])) {
            $photoUrl = $googlePlacesService->getPhotoUrl($place['photos'][0]['photo_reference']);
            try {
                $imageContents = file_get_contents($photoUrl);
                $filename = 'store_images/' . uniqid() . '.jpg';
                Storage::disk('public')->put($filename, $imageContents);
                $imagePath = 'storage/' . $filename;

                // 古い画像を削除する
                if ($oldImagePath && strpos($oldImagePath, 'storage/store_images/') === 0) {
                    $oldPath = str_replace('storage/', '', $oldImagePath);
                    Storage::disk('public')->delete($oldPath);
                }

            } catch (\Exception $e) {
                $imagePath = asset('images/no_image.jpg');
            }
        } else {
            $imagePath = asset('images/no_image.jpg');
        }

    
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
            'image'          => $imagePath,
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

        if ($store->image && strpos($store->image, 'storage/store_images/') === 0) {
            $path = str_replace('storage/', '', $store->image);
            Storage::disk('public')->delete($path);
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

        foreach ($user->stores as $store) {
            if ($store->image && strpos($store->image, 'storage/store_images/') === 0) {
                $path = str_replace('storage/', '', $store->image);
                Storage::disk('public')->delete($path);
            }
        }

        $user->stores()->delete();

        return redirect()
            ->route('user.show', Auth::id())
            ->with('success', 'すべての店舗を削除しました。');
    }


    /**
     * 管理者用メソッド：
     * 長期間ログインがなく、かつ一定期間いいねされていない店舗の
     * サムネイル画像のみを削除してストレージを整理します。
     *
     * 投稿自体は削除せず、画像を「no_image.jpg」に差し替えます。
     * 通常の画面からは呼び出されず、artisanコマンドなどから実行する想定です。
     */
    public function cleanUp()
    {
        $days = 180;
        $cutoffDate = now()->subDays($days);

        $stores = Store::with(['likedUsers' => function($query) {
                            $query->orderBy('pivot_created_at', 'desc');
                        }])
                        ->whereHas('user', function ($query) use ($cutoffDate) {
                            $query->where('last_login_at', '<', $cutoffDate);
                        })
                        ->get();

        $deletedCount = 0;

        // 最後にいいねされた日を取得
        foreach ($stores as $store) {
            $lastLikedAt = optional($store->likes()->latest('pivot_created_at')->first())->pivot_created_at;

            // いいねが一度もない または 最後のいいねから180日経過している
            if (is_null($lastLikedAt) || $lastLikedAt < $cutoffDate) {
                if ($store->image && strpos($store->image, 'storage/store_images/') === 0) {
                    $path = str_replace('storage/', '', $store->image);
                    Storage::disk('public')->delete($path);

                    $store->update([
                        'image' => asset('images/no_image.jpg'),
                    ]);

                    $deletedCount++;
                }
            }
        }

        echo "{$deletedCount}件の古い店舗画像を削除しました。\n";
    }
}

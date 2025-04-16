<?php

namespace App\Services;

use GuzzleHttp\Client;

class GooglePlacesService
{
    protected $apiKey;
    protected $client;

    public function __construct()
    {
        $this->apiKey = config('services.GOOGLE_MAPS_API_KEY');
        $this->client = new Client();
    }

    
   // 共有URLから店舗詳細を取得（1.リダイレクト → 2.店名・位置抽出 → 3.テキスト検索）
    public function getPlaceDetailsFromSharedUrl(string $shortUrl)
    {
         // ① リダイレクト先のURLを取得（短縮URLを実際のGoogle Maps URLに変換）
        $finalUrl = $this->resolveRedirectUrl($shortUrl);
        \Log::info('リダイレクトURL: ' . $finalUrl);
    
        // ② 緯度・経度をURLから抽出
        $latLng = $this->extractLatLngFromUrl($finalUrl);
        \Log::info('抽出した緯度経度: ', $latLng ?? []);
    
        // ③ 店名らしき文字列をURLから抽出
        if (preg_match('/maps\/place\/([^\/]+)/', $finalUrl, $matches)) {
            $placeName = urldecode($matches[1]);
            \Log::info('抽出した店名: ' . $placeName);
    
            // ④ FindPlaceFromText APIに投げるパラメータを作成
            $params = [
                'input' => $placeName,
                'inputtype' => 'textquery',
                'fields' => 'place_id',
                'key' => $this->apiKey,
            ];
    
            // ⑤ locationbias（位置優先）を指定（半径500m内で検索）
            if ($latLng) {
                $params['locationbias'] = "circle:500@{$latLng['lat']},{$latLng['lng']}";
            }
    
            // ⑥ APIリクエストを送信し、place_id を取得
            $response = $this->client->get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json', [
                'query' => $params
            ]);
    
            $body = json_decode($response->getBody(), true);
            $placeId = $body['candidates'][0]['place_id'] ?? null;
    
            \Log::info('取得した place_id: ' . $placeId);
    
            // ⑦ 詳細取得
            if ($placeId) {
                return $this->getPlaceDetails($placeId);
            }
        }
    
        return null;
    }
   



    // ① 短縮URLをリダイレクトして、最終的な Google Maps の URL を取得する
    public function resolveRedirectUrl(string $shortUrl): string
    {
        $response = $this->client->request('GET', $shortUrl, [
            'allow_redirects' => true,
            'max_redirects' => 5,
            'on_stats' => function (\GuzzleHttp\TransferStats $stats) use (&$finalUrl) {
                $finalUrl = (string) $stats->getEffectiveUri();
            },
        ]);

        return $finalUrl ?? $shortUrl;
    }


    // ② Google Maps のリダイレクト後の URL から place_id を直接抽出する（可能な場合）
    public function extractPlaceId(string $redirectedUrl): ?string
    {
        // 新形式のURLパターン
        if (preg_match('/!1s([^!]+)/', $redirectedUrl, $matches)) {
            return $matches[1];
        }

        // 古い形式のURLパターン
        if (preg_match('/place\/[^\/]+\/@[^\/]+,([^\/\?]+)\?/', $redirectedUrl, $matches)) {
            return $matches[1];
        }

        // URLクエリに placeid が含まれている場合
        if (preg_match('/placeid=([^&]+)/', $redirectedUrl, $matches)) {
            return $matches[1];
        }

        return null;
    }


    // ③ 緯度・経度から place_id を取得（Geocoding API を使用）
    public function getPlaceIdFromLatLng(float $lat, float $lng): ?string
    {
        $response = $this->client->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'latlng' => "{$lat},{$lng}",
                'key' => $this->apiKey,
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        return $body['results'][0]['place_id'] ?? null;
    }


    // ④ place_id から店舗の詳細情報を取得（日本語で取得）
    public function getPlaceDetails(string $placeId)
    {
        $response = $this->client->get("https://maps.googleapis.com/maps/api/place/details/json", [
            'query' => [
                'place_id' => $placeId,
                'key' => $this->apiKey,
                'fields' => 'name,formatted_address,geometry,formatted_phone_number,website,photos,place_id',
                'language' => 'ja'
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        return $body['result'] ?? null;
    }


    // ⑤ Google Maps URL から緯度・経度を抽出（@lat,lng のパターンを使用）
    public function extractLatLngFromUrl(string $url): ?array
    {
        if (preg_match('/@([\d\.]+),([\d\.]+)/', $url, $matches)) {
            return [
                'lat' => (float) $matches[1],
                'lng' => (float) $matches[2],
            ];
        }

        return null;
    }


    // ⑥ テキスト検索から place_id を取得（place name のみがわかっている場合）
    public function getPlaceIdFromText(string $text): ?string
    {
        $response = $this->client->get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json', [
            'query' => [
                'input' => $text,
                'inputtype' => 'textquery',
                'key' => $this->apiKey,
                'fields' => 'place_id'
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        return $body['candidates'][0]['place_id'] ?? null;
    }


    // ⑦ Place API の写真参照から、表示用の画像URLを生成
    public function getPhotoUrl(string $photoReference): string
    {
        return "https://maps.googleapis.com/maps/api/place/photo?maxwidth=800&photoreference={$photoReference}&key={$this->apiKey}";
    }
}

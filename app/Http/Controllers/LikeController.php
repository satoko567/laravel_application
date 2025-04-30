<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LikeController extends Controller
{
    public function store($id)
    {
        \Auth::user()->like($id);
        return $this->redirectBackWithTab();
    }
    
    public function destroy($id)
    {
        \Auth::user()->unlike($id);
        return $this->redirectBackWithTab();
    }
    
    private function redirectBackWithTab()
    {
        $referer = url()->previous();
        $tab = request('tab');
    
        // tab パラメータがあれば、クエリを付加する
        if ($tab) {
            $referer = preg_replace('/[?&]tab=[^&]*/', '', $referer); // 既存のtabを除去
            $joiner = parse_url($referer, PHP_URL_QUERY) ? '&' : '?';
            $referer .= $joiner . 'tab=' . $tab;
        }
    
        return redirect($referer);
    }    
}

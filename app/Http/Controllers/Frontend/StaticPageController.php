<?php

namespace App\Http\Controllers\Frontend;

use App\Models\ModSeoMeta;
use Illuminate\Http\Request;
use App\Helpers\HeaderHelper;
use App\Models\ModStaticPage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class StaticPageController extends Controller
{
    public function show($page)
    {


        $allowedPages = ['impressum', 'kontakt', 'agb', 'datenschutz'];

        if (!in_array($page, $allowedPages)) {
            abort(404);
        }

        $staticPage = ModStaticPage::findOrFail($page);

        $cacheKey = "seo_static_page_{$page}";
        $seo = Cache::remember($cacheKey, 3600, function () use ($page) {
            return ModSeoMeta::where('model_type', 'static_page')
                ->where('model_id', $page)
                ->first();
        });

        $defaultSeo = [
            'title' => $staticPage->title . ' - Wann-Wohin.de',
            'description' => "Hier finden Sie Informationen zum {$page} von Wann-Wohin.de.",
            'canonical' => url()->current(),
            'keywords' => ['tags' => [$page, 'Wann-Wohin', 'Reiseportal']],
            'image' => asset('default-bg.jpg'),
        ];

        $seoData = $seo ? [
            'title' => $seo->title,
            'description' => $seo->description,
            'canonical' => $seo->canonical,
            'image' => $seo->image,
            'keywords' => json_decode($seo->keywords, true) ?? [],
        ] : $defaultSeo;

        // Header-Daten laden
        $headerData = HeaderHelper::getHeaderContent();
        session(['headerData' => $headerData]);
//dd($headerData);

        return view('frontend.static-page', [
            'seo' => $seoData,
            'panorama_location_picture' => $headerData['bgImgPath'],
            'main_location_picture' => $headerData['mainImgPath'],
            'panorama_location_text' => $headerData['title_text'] ?? null,
            'page' => $page,
            'content' => ['title' => $staticPage->title, 'body' => $staticPage->body],
        ]);
    }
}

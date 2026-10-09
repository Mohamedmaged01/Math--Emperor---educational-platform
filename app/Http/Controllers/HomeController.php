<?php

namespace App\Http\Controllers;

use App\Services\AdminStore;
use App\Support\Content;
use App\Support\Seo;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(AdminStore $store): View
    {
        $isOn = fn ($v) => ! in_array($v, [false, 'false', '0', 0], true);

        $cms = $store->setting('cms');
        $hero = ['line1' => 'أتقن الرياضيات،', 'line2' => 'وتوّج درجتك بالـ 100%', 'line3' => 'في القدرات والتحصيلي'];
        if (! empty($cms['published'])) {
            $hero = [
                'line1' => ($cms['heroLine1'] ?? '') ?: $hero['line1'],
                'line2' => ($cms['heroLine2Gold'] ?? '') ?: $hero['line2'],
                'line3' => ($cms['heroLine3'] ?? '') ?: $hero['line3'],
            ];
        }

        $marketing = $store->setting('marketing');
        $adsense = ! empty($marketing['adsenseEnabled']) && empty($marketing['testMode'])
            && Seo::isValidPublisherId($marketing['publisherId'] ?? '')
            ? trim($marketing['publisherId']) : null;

        return view('home', [
            'hero' => $hero,
            'stats' => array_values(array_filter($store->rows('stats'), fn ($s) => $isOn($s['enabled'] ?? true))),
            'testimonials' => $store->rows('testimonials'),
            'seo' => Seo::resolved('home'),
            'adsensePublisher' => $adsense,
            'openPortal' => session('open'),
        ]);
    }
}

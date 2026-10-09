<?php

namespace App\Http\Controllers;

use App\Support\Seo;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        return response(Seo::sitemapXml(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        return response(implode("\n", Seo::robotsLines())."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}

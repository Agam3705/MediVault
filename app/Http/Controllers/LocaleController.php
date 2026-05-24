<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch language locale.
     */
    public function switch(Request $request, $locale)
    {
        if (in_array($locale, ['en', 'hi'])) {
            session(['locale' => $locale]);
        }

        return redirect()->back();
    }
}

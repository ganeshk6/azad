<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;

class LanguageController extends Controller
{
    public function languageSet(Request $request)
    {
        $request->validate([
            'language' => 'required|string|exists:languages,name',
        ]);

        $user = Auth::user();
        $language = $request->input('language');

        $user->preferred_language = $language;
        $user->save();

        $languageId = Language::where('name', $language)->first()->id;

        return response()->json([
            'message' => 'Language preference updated successfully.',
            'languageId' => $languageId,  
            'language' => $language,     
        ]);
    }

    public function languageGet(){
        $languages = Language::select('id', 'name')->get();

        return response()->json(['languages' => $languages]);
    }

}

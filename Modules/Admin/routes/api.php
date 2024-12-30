<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use App\Http\Controllers\DictationController;
use App\Http\Controllers\OutlineController;
use App\Http\Controllers\RulesOutlinesController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\PhrasesController;
use App\Http\Controllers\GrammaloguesController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('admin', AdminController::class)->names('admin');
});

Route::get('/dictation/language_{id}', [DictationController::class, 'dictationApi']);
Route::get('/outlines/language_{id}', [OutlineController::class, 'outlinesApi']);
Route::get('/rules-outlines/language_{id}', [RulesOutlinesController::class, 'rulesOutlineApi']);

Route::get('/dictionaries/language_{id}', [DictionaryController::class, 'dictionaryApi']);
Route::get('/sub-dictionaries/dictionary_{id}', [DictionaryController::class, 'subDictionaryApi']);

Route::get('/phrases/language_{id}', [PhrasesController::class, 'phrasesApi']);
Route::get('/sub-phrases/phrases_{id}', [PhrasesController::class, 'subPhrasesApi']);

Route::get('/grammalogues/language_{id}', [GrammaloguesController::class, 'grammaloguesApi']);
Route::get('/sub-grammalogues/grammalogue_{id}', [GrammaloguesController::class, 'subGrammaloguesApi']);

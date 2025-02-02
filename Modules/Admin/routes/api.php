<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use App\Http\Controllers\DictationController;
use App\Http\Controllers\OutlineController;
use App\Http\Controllers\RulesOutlinesController;
use App\Http\Controllers\ContractionsController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\PhrasesController;
use App\Http\Controllers\GrammaloguesController;
use App\Http\Controllers\Customer\AuthController;

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
//done
Route::get('/dictation/language_{id}', [DictationController::class, 'dictationApi']);
Route::post('/dictation-by', [DictationController::class, 'SearchByDictationApi']);

// done
Route::get('/outlines/language_{id}', [OutlineController::class, 'outlinesApi']);
Route::get('/search-by-outlines/outline_{id}', [OutlineController::class, 'SearchOutlinesApi']);
Route::post('/search-by', [OutlineController::class, 'SearchBy']);

//done
Route::get('/rules-outlines/language_{id}', [RulesOutlinesController::class, 'rulesOutlineApi']);
Route::get('/type-rules-outlines/rules_outline_{id}', [RulesOutlinesController::class, 'TypeOutlinesApi']);
Route::post('/rules-outlines-by', [RulesOutlinesController::class, 'SearchBy']);

// done
Route::get('/dictionaries/language_{id}', [DictionaryController::class, 'dictionaryApi']);
Route::get('/familier-dictionaries/familier_{id}', [DictionaryController::class, 'subDictionaryApi']);
Route::post('/dictionary-by', [DictionaryController::class, 'SearchByDictinary']);

//done
Route::get('/phrases/language_{id}', [PhrasesController::class, 'phrasesApi']);
Route::get('/sub-phrases/phrases_{id}', [PhrasesController::class, 'subPhrasesApi']);
Route::post('/phrases-by', [PhrasesController::class, 'SearchByPhrase']);

//done
Route::get('/grammalogues/language_{id}', [GrammaloguesController::class, 'grammaloguesApi']);
Route::get('/sub-grammalogues/grammalogue_{id}', [GrammaloguesController::class, 'subGrammaloguesApi']);
Route::post('/grammalogues-by', [GrammaloguesController::class, 'SearchByGrammalogue']);

//done
Route::get('/contractions/language_{id}', [ContractionsController::class, 'contractionsApi']);
Route::post('/contractions-by', [ContractionsController::class, 'SearchByContractions']);


Route::post('signup', [AuthController::class, 'sendOtp']);
Route::post('resend-otp', [AuthController::class, 'resendOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('logout', [AuthController::class, 'logout']);

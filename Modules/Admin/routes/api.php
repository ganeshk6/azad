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
use App\Http\Controllers\WordWithPrepositionController;
use App\Http\Controllers\FestivalController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CountryForeignController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\TempleController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\MinistryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\ForeignCountryController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\NumericalController;

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

Route::get('/type-rules-outlines/language_{id}', [RulesOutlinesController::class, 'TypeOfOutlines']);
Route::post('/type-rules-outlines-by', [RulesOutlinesController::class, 'typeOfSearchBy']);

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


Route::get('/word-with-preposition/language_{id}', [WordWithPrepositionController::class, 'getApi']);
Route::post('/word-with-preposition-by', [WordWithPrepositionController::class, 'searchApi']);

Route::get('/festivals/language_{id}', [FestivalController::class, 'getApi']);
Route::post('/festivals-by', [FestivalController::class, 'searchApi']);

Route::get('/days/language_{id}', [DayController::class, 'getApi']);
Route::post('/days-by', [DayController::class, 'searchApi']);

Route::get('/countries/language_{id}', [CountryController::class, 'getApi']);
Route::post('/countries-by', [CountryController::class, 'searchApi']);

Route::get('/country-foreign/language_{id}', [CountryForeignController::class, 'getApi']);
Route::post('/country-foreign-by', [CountryForeignController::class, 'searchApi']);

Route::get('/parties/language_{id}', [PartyController::class, 'getApi']);
Route::post('/parties-by', [PartyController::class, 'searchApi']);

Route::get('/books/language_{id}', [BookController::class, 'getApi']);
Route::post('/books-by', [BookController::class, 'searchApi']);

Route::get('/diseases/language_{id}', [DiseaseController::class, 'getApi']);
Route::post('/diseases-by', [DiseaseController::class, 'searchApi']);

Route::get('/temples/language_{id}', [TempleController::class, 'getApi']);
Route::post('/temples-by', [TempleController::class, 'searchApi']);

Route::get('/months/language_{id}', [MonthController::class, 'getApi']);
Route::post('/months-by', [MonthController::class, 'searchApi']);

Route::get('/ministries/language_{id}', [MinistryController::class, 'getApi']);
Route::post('/ministries-by', [MinistryController::class, 'searchApi']);

Route::get('/states/language_{id}', [StateController::class, 'getApi']);
Route::post('/states-by', [StateController::class, 'searchApi']);

Route::get('/foreign-contries/language_{id}', [ForeignCountryController::class, 'getApi']);
Route::post('/foreign-contries-by', [ForeignCountryController::class, 'searchApi']);

Route::get('/persons/language_{id}', [PersonController::class, 'getApi']);
Route::post('/persons-by', [PersonController::class, 'searchApi']);

Route::get('/cities/language_{id}', [CityController::class, 'getApi']);
Route::post('/cities-by', [CityController::class, 'searchApi']);

Route::get('/places/language_{id}', [PlaceController::class, 'getApi']);
Route::post('/places-by', [PlaceController::class, 'searchApi']);

Route::get('/numerical/language_{id}', [NumericalController::class, 'getApi']);
Route::post('/numerical-by', [NumericalController::class, 'searchApi']);


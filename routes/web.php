<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\PhrasesController;
use App\Http\Controllers\WordWithPrepositionController;
use App\Http\Controllers\FestivalController;
use App\Http\Controllers\NumericalController;
use App\Http\Controllers\CorrectSpellingController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\UniqueOutlineController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\ForeignCountryController;
use App\Http\Controllers\MinistryController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\TempleController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CountryForeignController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\DictationController;
use App\Http\Controllers\GrammaloguesController;
use App\Http\Controllers\OutlineController;
use App\Http\Controllers\RulesOutlinesController;
use App\Http\Controllers\ContractionsController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Dictionary 
    Route::any('/dictionary', [DictionaryController::class, 'dictionary'])->name('dictionary');
    Route::get('/dictionary/words', [DictionaryController::class, 'getWords'])->name('dictionary-getWords');
    Route::any('/dictionary/edit/{id}', [DictionaryController::class, 'dictionaryEdit'])->name('dictionary-edit');
    Route::delete('/dictionary/{id}', [DictionaryController::class, 'destroy'])->name('dictionary-delete');

    // Phrases 
    Route::any('/phrases', [PhrasesController::class, 'phrases'])->name('phrases');
    Route::get('/phrases/words', [PhrasesController::class, 'getWords'])->name('phrases-getWords');
    Route::any('/phrases/edit/{id}', [PhrasesController::class, 'phrasesEdit'])->name('phrases-edit');
    Route::delete('/phrases/{id}', [PhrasesController::class, 'destroy'])->name('phrases-delete');

    // dictation 
    Route::any('/dictation', [DictationController::class, 'dictation'])->name('dictation');
    Route::get('/dictation/words', [DictationController::class, 'getWords'])->name('dictation-getWords');
    Route::any('/dictation/edit/{id}', [DictationController::class, 'dictationEdit'])->name('dictation-edit');
    Route::delete('/dictation/{id}', [DictationController::class, 'destroy'])->name('dictation-delete');
    
    // Grammalogues 
    Route::any('/grammalogues', [GrammaloguesController::class, 'grammalogues'])->name('grammalogues');
    Route::get('/grammalogues/words', [GrammaloguesController::class, 'getWords'])->name('grammalogues-getWords');
    Route::any('/grammalogues/edit/{id}', [GrammaloguesController::class, 'grammaloguesEdit'])->name('grammalogues-edit');
    Route::delete('/grammalogues/{id}', [GrammaloguesController::class, 'destroy'])->name('grammalogues-delete');

    // basic outlines 
    Route::any('/outlines', [OutlineController::class, 'outlines'])->name('outlines');
    Route::get('/outlines/words', [OutlineController::class, 'getWords'])->name('outlines-getWords');
    Route::any('/outlines/edit/{id}', [OutlineController::class, 'outlinesEdit'])->name('outlines-edit');
    Route::delete('/outlines/{id}', [OutlineController::class, 'destroy'])->name('outlines-delete');

    // basic outlines 
    Route::any('/rules-for-outlines-formation', [RulesOutlinesController::class, 'rulesOutlines'])->name('rulesOutlines');
    Route::get('/rules-for-outlines-formation/words', [RulesOutlinesController::class, 'getWords'])->name('rulesOutlines-getWords');
    Route::any('/rules-for-outlines-formation/edit/{id}', [RulesOutlinesController::class, 'rulesOutlinesEdit'])->name('rulesOutlines-edit');
    Route::delete('/rules-for-outlines-formation/{id}', [RulesOutlinesController::class, 'destroy'])->name('rulesOutlines-delete');

    // basic outlines 
    Route::any('/contractions', [ContractionsController::class, 'contractions'])->name('contractions');
    Route::get('/contractions/words', [ContractionsController::class, 'getWords'])->name('contractions-getWords');
    Route::any('/contractions/edit/{id}', [ContractionsController::class, 'contractionsEdit'])->name('contractions-edit');
    Route::delete('/contractions/{id}', [ContractionsController::class, 'destroy'])->name('contractions-delete');

    // Word with preposition outlines 
    Route::any('/word-with-preposition', [WordWithPrepositionController::class, 'wordWithPreposition'])->name('word-with-preposition');
    Route::get('/word-with-preposition/words', [WordWithPrepositionController::class, 'getWords'])->name('word-with-preposition-getWords');
    Route::any('/word-with-preposition/edit/{id}', [WordWithPrepositionController::class, 'wordWithPrepositionEdit'])->name('word-with-preposition-edit');
    Route::delete('/word-with-preposition/{id}', [WordWithPrepositionController::class, 'destroy'])->name('word-with-preposition-delete');

    // festivals outlines 
    Route::any('/festivals', [FestivalController::class, 'festival'])->name('festivals');
    Route::get('/festivals/words', [FestivalController::class, 'getWords'])->name('festivals-getWords');
    Route::any('/festivals/edit/{id}', [FestivalController::class, 'festivalEdit'])->name('festivals-edit');
    Route::delete('/festivals/{id}', [FestivalController::class, 'destroy'])->name('festivals-delete');

    // days outlines 
    Route::any('/days', [DayController::class, 'days'])->name('days');
    Route::get('/days/words', [DayController::class, 'getWords'])->name('days-getWords');
    Route::any('/days/edit/{id}', [DayController::class, 'edit'])->name('days-edit');
    Route::delete('/days/{id}', [DayController::class, 'destroy'])->name('days-delete');

    // countries outlines 
    Route::any('/countries', [CountryController::class, 'countries'])->name('countries');
    Route::get('/countries/words', [CountryController::class, 'getWords'])->name('countries-getWords');
    Route::any('/countries/edit/{id}', [CountryController::class, 'edit'])->name('countries-edit');
    Route::delete('/countries/{id}', [CountryController::class, 'destroy'])->name('countries-delete');

    // Country & foreign  outlines 
    Route::any('/country-foreign', [CountryForeignController::class, 'countryForeign'])->name('country-foreign');
    Route::get('/country-foreign/words', [CountryForeignController::class, 'getWords'])->name('country-foreign-getWords');
    Route::any('/country-foreign/edit/{id}', [CountryForeignController::class, 'edit'])->name('country-foreign-edit');
    Route::delete('/country-foreign/{id}', [CountryForeignController::class, 'destroy'])->name('country-foreign-delete');

    // parties outlines 
    Route::any('/parties', [PartyController::class, 'parties'])->name('parties');
    Route::get('/parties/words', [PartyController::class, 'getWords'])->name('parties-getWords');
    Route::any('/parties/edit/{id}', [PartyController::class, 'edit'])->name('parties-edit');
    Route::delete('/parties/{id}', [PartyController::class, 'destroy'])->name('parties-delete');

    // books outlines 
    Route::any('/books', [BookController::class, 'books'])->name('books');
    Route::get('/books/words', [BookController::class, 'getWords'])->name('books-getWords');
    Route::any('/books/edit/{id}', [BookController::class, 'edit'])->name('books-edit');
    Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books-delete');

    // diseases outlines 
    Route::any('/diseases', [DiseaseController::class, 'diseases'])->name('diseases');
    Route::get('/diseases/words', [DiseaseController::class, 'getWords'])->name('diseases-getWords');
    Route::any('/diseases/edit/{id}', [DiseaseController::class, 'edit'])->name('diseases-edit');
    Route::delete('/diseases/{id}', [DiseaseController::class, 'destroy'])->name('diseases-delete');

    // temples outlines 
    Route::any('/temples', [TempleController::class, 'temples'])->name('temples');
    Route::get('/temples/words', [TempleController::class, 'getWords'])->name('temples-getWords');
    Route::any('/temples/edit/{id}', [TempleController::class, 'edit'])->name('temples-edit');
    Route::delete('/temples/{id}', [TempleController::class, 'destroy'])->name('temples-delete');

    // months outlines 
    Route::any('/months', [MonthController::class, 'months'])->name('months');
    Route::get('/months/words', [MonthController::class, 'getWords'])->name('months-getWords');
    Route::any('/months/edit/{id}', [MonthController::class, 'edit'])->name('months-edit');
    Route::delete('/months/{id}', [MonthController::class, 'destroy'])->name('months-delete');

    // ministries outlines 
    Route::any('/ministries', [MinistryController::class, 'ministries'])->name('ministries');
    Route::get('/ministries/words', [MinistryController::class, 'getWords'])->name('ministries-getWords');
    Route::any('/ministries/edit/{id}', [MinistryController::class, 'edit'])->name('ministries-edit');
    Route::delete('/ministries/{id}', [MinistryController::class, 'destroy'])->name('ministries-delete');

    // states outlines 
    Route::any('/states', [StateController::class, 'states'])->name('states');
    Route::get('/states/words', [StateController::class, 'getWords'])->name('states-getWords');
    Route::any('/states/edit/{id}', [StateController::class, 'edit'])->name('states-edit');
    Route::delete('/states/{id}', [StateController::class, 'destroy'])->name('states-delete');
    
    // Foreign countries  outlines 
    Route::any('/foreign-contries', [ForeignCountryController::class, 'foreignContries'])->name('foreign-contries');
    Route::get('/foreign-contries/words', [ForeignCountryController::class, 'getWords'])->name('foreign-contries-getWords');
    Route::any('/foreign-contries/edit/{id}', [ForeignCountryController::class, 'edit'])->name('foreign-contries-edit');
    Route::delete('/foreign-contries/{id}', [ForeignCountryController::class, 'destroy'])->name('foreign-contries-delete');

    // persons outlines 
    Route::any('/persons', [PersonController::class, 'persons'])->name('persons');
    Route::get('/persons/words', [PersonController::class, 'getWords'])->name('persons-getWords');
    Route::any('/persons/edit/{id}', [PersonController::class, 'edit'])->name('persons-edit');
    Route::delete('/persons/{id}', [PersonController::class, 'destroy'])->name('persons-delete');

    // cities outlines 
    Route::any('/cities', [CityController::class, 'cities'])->name('cities');
    Route::get('/cities/words', [CityController::class, 'getWords'])->name('cities-getWords');
    Route::any('/cities/edit/{id}', [CityController::class, 'edit'])->name('cities-edit');
    Route::delete('/cities/{id}', [CityController::class, 'destroy'])->name('cities-delete');

    // places outlines 
    Route::any('/places', [PlaceController::class, 'places'])->name('places');
    Route::get('/places/words', [PlaceController::class, 'getWords'])->name('places-getWords');
    Route::any('/places/edit/{id}', [PlaceController::class, 'edit'])->name('places-edit');
    Route::delete('/places/{id}', [PlaceController::class, 'destroy'])->name('places-delete');

    // numerical outlines 
    Route::any('/numerical', [NumericalController::class, 'numerical'])->name('numerical');
    Route::get('/numerical/words', [NumericalController::class, 'getWords'])->name('numerical-getWords');
    Route::any('/numerical/edit/{id}', [NumericalController::class, 'edit'])->name('numerical-edit');
    Route::delete('/numerical/{id}', [NumericalController::class, 'destroy'])->name('numerical-delete');

    // numerical outlines 
    Route::any('/unique-outline', [UniqueOutlineController::class, 'uniqueOutline'])->name('unique-outline');
    Route::get('/unique-outline/words', [UniqueOutlineController::class, 'getWords'])->name('unique-outline-getWords');
    Route::any('/unique-outline/edit/{id}', [UniqueOutlineController::class, 'edit'])->name('unique-outline-edit');
    Route::delete('/unique-outline/{id}', [UniqueOutlineController::class, 'destroy'])->name('unique-outline-delete');

    // numerical outlines 
    Route::any('/correct-spelling', [CorrectSpellingController::class, 'correctSpelling'])->name('correct-spelling');
    Route::get('/correct-spelling/words', [CorrectSpellingController::class, 'getWords'])->name('correct-spelling-getWords');
    Route::any('/correct-spelling/edit/{id}', [CorrectSpellingController::class, 'edit'])->name('correct-spelling-edit');
    Route::delete('/correct-spelling/{id}', [CorrectSpellingController::class, 'destroy'])->name('correct-spelling-delete');

    // numerical outlines 
    Route::any('/notices', [NoticeController::class, 'notices'])->name('notices');
    Route::get('/notices/words', [NoticeController::class, 'getWords'])->name('notices-getWords');
    Route::any('/notices/edit/{id}', [NoticeController::class, 'edit'])->name('notices-edit');
    Route::delete('/notices/{id}', [NoticeController::class, 'destroy'])->name('notices-delete');

});

require __DIR__.'/auth.php';

Route::post('/language', [LanguageController::class, 'languageSet'])->name('language-set');
Route::get('/get-language', [LanguageController::class, 'languageGet'])->name('language-get');

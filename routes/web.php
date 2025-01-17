<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\PhrasesController;
use App\Http\Controllers\DictationController;
use App\Http\Controllers\GrammaloguesController;
use App\Http\Controllers\OutlineController;
use App\Http\Controllers\RulesOutlinesController;
use App\Http\Controllers\ContractionsController;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
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

});

require __DIR__.'/auth.php';

Route::post('/language', [LanguageController::class, 'languageSet'])->name('language-set');
Route::get('/get-language', [LanguageController::class, 'languageGet'])->name('language-get');

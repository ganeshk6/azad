<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RulesOutline;
use App\Models\TypeRulesOutline;
use Storage;

class RulesOutlinesController extends Controller
{
    public function rulesOutlines(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'sentence' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $dictation = new RulesOutline();
            $dictation->sentence = $request->input('sentence');
            $dictation->language_id = $request->input('languageId');
            $dictation->save(); 
    
            return response()->json([
                'message' => 'sentence added successfully!',
                'id' => $dictation->id,
            ], 200);
        }
        return Inertia::render('RulesOutline/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = RulesOutline::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function rulesOutlinesEdit(Request $request, $id)
{
    if ($request->isMethod('post')) {
        $request->validate([
            'sentence' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'wordSections' => 'nullable|array',
            'wordSections.*.word' => 'nullable|string|max:255',
            'wordSections.*.description' => 'nullable|string',
            'wordSections.*.signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $dictation = RulesOutline::findOrFail($id);
            $dictation->sentence = $request->input('sentence');
            $dictation->description = $request->input('description');

            // Handle main image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = "{$dictation->id}_rulesoutlines." . $file->getClientOriginalExtension();
                $filePath = "images/rulesOutlines/{$fileName}";

                // Store the file in public storage
                $file->storeAs('images/rulesOutlines', $fileName, 'public');
                $dictation->image = "/{$filePath}";
            }

            $dictation->save();

            // Handle word sections
            $incomingIds = collect($request->input('wordSections'))->pluck('id')->filter()->toArray();
            $existingIds = TypeRulesOutline::where('rules_outline_id', $dictation->id)->pluck('id')->toArray();
            $idsToDelete = array_diff($existingIds, $incomingIds);
            TypeRulesOutline::whereIn('id', $idsToDelete)->delete();

            foreach ($request->input('wordSections') as $index => $section) {
                $word = TypeRulesOutline::updateOrCreate(
                    ['id' => $section['id'] ?? null],
                    [
                        'rules_outline_id' => $dictation->id,
                        'language_id' => $dictation->language_id,
                        'word' => $section['word'],
                        'description' => $section['description'],
                    ]
                );

                // Handle signature upload
                if ($request->hasFile("wordSections.{$index}.signature")) {
                    $file = $request->file("wordSections.{$index}.signature");
                    $fileName = "{$word->id}_tyeprulesoutlines." . $file->getClientOriginalExtension();
                    $filePath = "images/rulesOutlines/{$fileName}";

                    // Store file
                    $file->storeAs('images/rulesOutlines', $fileName, 'public');
                    $word->signature = "/{$filePath}";
                    $word->save();
                }
            }

            return redirect()->route('rulesOutlines-edit', ['id' => $dictation->id])
                ->with('success', 'Rules outlines updated successfully!');
        } catch (\Exception $e) {
            \Log::error("Error updating rules outlines: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred while updating. Please try again.']);
        }
    }

    // Get the existing data
    $dictation = RulesOutline::with('TypeRulesOutline')->findOrFail($id);

    $phrasesData = [
        'id' => $dictation->id,
        'sentence' => $dictation->sentence,
        'description' => $dictation->description,
        'image' => $dictation->image,
        'wordSections' => $dictation->TypeRulesOutline->map(function ($word) {
            return [
                'id' => $word->id,
                'word' => $word->word,
                'description' => $word->description,
                'signature' => $word->signature,
            ];
        }),
    ];

    return Inertia::render('RulesOutline/Edit', [
        'phrasesData' => $phrasesData,
    ]);
}

    

    public function destroy($id)
    {
        $word = RulesOutline::find($id);

        if ($word) {
            $word->delete();
            return response()->json(['message' => 'Word deleted successfully'], 200);
        }

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function rulesOutlineApi($id)
    {
        $dictation = RulesOutline::where('language_id', $id)->with('TypeRulesOutline')->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                'id'=> $item->id,
                'sentence'=> $item->sentence,
                'image'=> $item->image,
                'type_rules_outline'=> $item->TypeRulesOutline->select('word', 'description', 'signature', 'rules_outline_id')
            ];
        });

        return response()->json(['rule_outline'=> $dictationData ]);
    }

    public function TypeOutlinesApi($id)
    {
        $dictation = TypeRulesOutline::select('word', 'description', 'signature', 'rules_outline_id')->where('rules_outline_id', $id)->get();

        return response()->json($dictation);
    }

    public function SearchBy(Request $request){
        $request->validate([
            'sentence' => 'required|string'
        ]);

        $searchOutline = RulesOutline::select('sentence', 'image')->where('sentence', $request->sentence)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        return response()->json([
            $searchOutline, 
        ]);
    }


    public function TypeOfOutlines($id)
    {
        $dictation = TypeRulesOutline::where('language_id', $id)->get();

        return response()->json($dictation);
    }

    public function typeOfSearchBy(Request $request)
    {
        $request->validate([
            'word' => 'required|string'
        ]);

        $searchOutline = TypeRulesOutline::where('word', $request->word)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        $responseData = [
            'word' => $searchOutline->word,
            'description' => $searchOutline->description,
            'signature' => $searchOutline->signature,
            'language_id' => $searchOutline->language_id,
            'rules_outline_id' => $searchOutline->rules_outline_id,
        ];

        return response()->json([$responseData]);
    }
}

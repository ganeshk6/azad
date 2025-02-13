<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WordWithPreposition;
use Inertia\Inertia;

class WordWithPrepositionController extends Controller
{
    public function wordWithPreposition(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $phrases = new WordWithPreposition();
            $phrases->letter = $request->input('letter');
            $phrases->language_id = $request->input('languageId');
            $phrases->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $phrases->id,
            ], 200);
        }
        return Inertia::render('WordWithPreposition/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = WordWithPreposition::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function wordWithPrepositionEdit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string',
                'sign' => 'nullable'
            ]);

            $phrase = WordWithPreposition::findOrFail($id);
            $phrase->letter = $request->input('letter');

            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                $fileName = "{$phrase->id}_word_with_preposition." . $file->getClientOriginalExtension();
                $filePath = "images/WordWithPreposition/{$fileName}";

                $file->storeAs('images/WordWithPreposition', $fileName, 'public');
            
                $phrase->sign = $filePath;
            } 

            $phrase->save();

            return redirect()->route('word-with-preposition-edit', ['id' => $phrase->id])
                ->with('success', 'festivals with preposition updated successfully!');
        }

        $phrase = WordWithPreposition::findOrFail($id);

        return Inertia::render('WordWithPreposition/Edit', [
            'phrasesData' => [
                'id' => $phrase->id,
                'letter' => $phrase->letter,
                'sign' => $phrase->sign,
            ],
        ]);
    }

    public function destroy($id)
    {
        $word = WordWithPreposition::find($id);

        $word->delete();

        return response()->json(['message' => 'Word not found'], 404);
    }


    public function getApi($id)
    {
        $dictation = WordWithPreposition::where('language_id', $id)->orderBy('letter', 'asc')->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                // 'id' => $item->id,
                'word'=> $item->letter,
                'sign'=> $item->sign,
                // 'outline_search' => $item->OutlineSearch->select('notes')
            ];
        });

        return response()->json($dictationData);
    }

    public function searchApi(Request $request)
    {
        $request->validate([
            'letter' => 'required|string'
        ]);

        $searchOutline = WordWithPreposition::select('letter', 'sign')->where('letter', $request->letter)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        $responseData = [
            'word' => $searchOutline->letter,
            'sign' => $searchOutline->sign,
        ];

        return response()->json([$responseData]);
    }
}

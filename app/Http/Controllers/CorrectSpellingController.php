<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CorrectSpelling;
use Inertia\Inertia;

class CorrectSpellingController extends Controller
{
    public function correctSpelling(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $phrases = new CorrectSpelling();
            $phrases->letter = $request->input('letter');
            $phrases->language_id = $request->input('languageId');
            $phrases->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $phrases->id,
            ], 200);
        }
        return Inertia::render('CorrectSpelling/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = CorrectSpelling::where('language_id', $request->input('languageId'))->get();
        
        return response()->json($words);
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string',
                'sign' => 'nullable'
            ]);

            $phrase = CorrectSpelling::findOrFail($id);
            $phrase->letter = $request->input('letter');

            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                $fileName = "{$phrase->id}_CorrectSpelling." . $file->getClientOriginalExtension();
                $filePath = "images/CorrectSpelling/{$fileName}";

                $file->storeAs('images/CorrectSpelling', $fileName, 'public');
            
                $phrase->sign = $filePath;
            } 

            $phrase->save();

            return redirect()->route('correct-spelling-edit', ['id' => $phrase->id])
                ->with('success', 'CorrectSpelling updated successfully!');
        }

        $phrase = CorrectSpelling::findOrFail($id);

        return Inertia::render('CorrectSpelling/Edit', [
            'phrasesData' => [
                'id' => $phrase->id,
                'letter' => $phrase->letter,
                'sign' => $phrase->sign,
            ],
        ]);
    }

    public function destroy($id)
    {
        $word = CorrectSpelling::find($id);

        $word->delete();

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function getApi($id)
    {
        $dictation = CorrectSpelling::where('language_id', $id)->orderBy('letter', 'asc')->get();

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

        $searchOutline = CorrectSpelling::select('letter', 'sign')->where('letter', $request->letter)->first();

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

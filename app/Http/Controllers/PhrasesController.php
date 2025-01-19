<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phrase;
use App\Models\PhraseWord;
use Inertia\Inertia;

class PhrasesController extends Controller
{
    public function phrases(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $phrases = new Phrase();
            $phrases->letter = $request->input('letter');
            $phrases->language_id = $request->input('languageId');
            $phrases->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $phrases->id,
            ], 200);
        }
        return Inertia::render('Phrases/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = Phrase::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function phrasesEdit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string',
                'sign' => 'nullable|string', 
                'wordSections' => 'nullable|array',
                'wordSections.*.word' => 'nullable|string|max:255',
                'wordSections.*.description' => 'nullable|string',
                'wordSections.*.signature' => 'nullable|string',
            ]);

            $phrase = Phrase::findOrFail($id);
            $phrase->letter = $request->input('letter');

            if ($request->input('sign')) {
                $imageData = $request->input('sign');
                $imagePath = public_path("images/phrases/{$phrase->id}_phrase.png");
                $imageContent = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
                file_put_contents($imagePath, $imageContent);
                $phrase->sign = "/images/phrases/{$phrase->id}_phrase.png";
            } elseif ($request->has('clearSign') && $request->clearSign) {
                $phrase->sign = null;
                $imagePath = public_path("images/phrases/{$phrase->id}_phrase.png");
            
                if (file_exists($imagePath)) {
                    unlink($imagePath); 
                }
            }
            $phrase->save();

            $incomingIds = collect($request->input('wordSections'))->pluck('id')->filter()->toArray();
            $existingIds = PhraseWord::where('phrase_id', $phrase->id)->pluck('id')->toArray();
            $idsToDelete = array_diff($existingIds, $incomingIds);
            PhraseWord::whereIn('id', $idsToDelete)->delete();

            foreach ($request->input('wordSections') as $section) {

                $word = PhraseWord::updateOrCreate(
                    ['id' => $section['id']],
                    [
                        'phrase_id' => $phrase->id,
                        'word' => $section['word'],
                        'description' => $section['description'] ?? null,
                        'signature' => $section['signature'] ?? null,
                    ]
                );

                if (!empty($section['signature']) && $word !== null) {
                    $signaturePath = public_path("images/phrases/{$word->id}_phrase.png");
                    $signatureData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $section['signature']));
                    file_put_contents($signaturePath, $signatureData);
                    $word->signature = "/images/phrases/{$word->id}_phrase.png";
                    $word->save();
                }
            }

            return redirect()->route('phrases-edit', ['id' => $phrase->id])
                ->with('success', 'Phrases updated successfully!');
        }

        $phrase = Phrase::findOrFail($id);
        $wordSections = $phrase->PhraseWord->map(function ($word) {
            return [
                'id' => $word->id,
                'word' => $word->word,
                'description' => $word->description,
                'signature' => $word->signature,
            ];
        });

        return Inertia::render('Phrases/Edit', [
            'phrasesData' => [
                'id' => $phrase->id,
                'letter' => $phrase->letter,
                'sign' => $phrase->sign,
                'wordSections' => $wordSections,
            ],
        ]);
    }

    public function destroy($id)
    {
        $word = Phrase::find($id);

        if ($word) {
            PhraseWord::where('phrase_id', $id)->delete();
            $word->delete();
            return response()->json(['message' => 'Word deleted successfully'], 200);
        }

        return response()->json(['message' => 'Word not found'], 404);
    }


    public function phrasesApi($id)
    {
        $dictation = Phrase::select('letter', 'sign')->where('language_id', $id)->get();

        return response()->json($dictation);
    }

    public function subPhrasesApi($id)
    {
        $dictation = PhraseWord::where('phrase_id', $id)->get();

        return response()->json(['phrase' => $dictation ]);
    }
    
    public function SearchByPhrase(Request $request)
    {
        $request->validate([
            'letter' => 'required|string'
        ]);

        $searchOutline = Phrase::select('letter', 'sign')->where('letter', $request->letter)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        return response()->json([
            'phrase' => $searchOutline,
        ], 200);
    }
}

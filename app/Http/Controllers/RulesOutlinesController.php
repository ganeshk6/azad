<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RulesOutline;
use App\Models\TypeRulesOutline;

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
                'image' => 'nullable|string',
                'wordSections' => 'nullable|array',
                'wordSections.*.word' => 'nullable|string|max:255',
                'wordSections.*.description' => 'nullable|string',
                'wordSections.*.signature' => 'nullable|string',
            ]);
    
            try {
                // Update the main RulesOutline record
                $dictation = RulesOutline::findOrFail($id);
                $dictation->sentence = $request->input('sentence');
                $dictation->description = $request->input('description');
                
                if ($request->input('signatureImage')) {
                    $imageData = $request->input('signatureImage');
                    $imagePath = public_path("images/rulesOutlines/{$dictation->id}_rulesoutlines.png");
                    $imageContent = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
                    file_put_contents($imagePath, $imageContent);
                    $dictation->image = "/images/rulesOutlines/{$dictation->id}_rulesoutlines.png";
                } elseif ($request->has('clearSign') && $request->clearSign) {
                    $dictation->image = null;
                    $imagePath = public_path("images/rulesOutlines/{$dictation->id}_rulesoutlines.png");
                
                    if (file_exists($imagePath)) {
                        unlink($imagePath); 
                    }
                }
    
                $dictation->save();
    
                // Handle word sections
                $incomingIds = collect($request->input('wordSections'))->pluck('id')->filter()->toArray();
                $existingIds = TypeRulesOutline::where('rules_outline_id', $dictation->id)->pluck('id')->toArray();
                $idsToDelete = array_diff($existingIds, $incomingIds);
                TypeRulesOutline::whereIn('id', $idsToDelete)->delete();
    
                foreach ($request->input('wordSections') as $section) {
                    $word = TypeRulesOutline::updateOrCreate(
                        ['id' => $section['id'] ?? null],
                        [
                            'rules_outline_id' => $dictation->id,
                            'word' => $section['word'],
                            'description' => $section['description'] ?? null,
                            'signature' => $section['signature'] ?? null,
                        ]
                    );
    
                    if (!empty($section['signature'])) {
                        $signatureData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $section['signature']));
                        $signaturePath = public_path("images/rulesOutlines/{$word->id}_tyeprulesoutlines.png");
                        
                        if (file_put_contents($signaturePath, $signatureData)) {
                            $word->signature = "/images/rulesOutlines/{$word->id}_tyeprulesoutlines.png";
                            $word->save();
                        } else {
                            throw new \Exception("Failed to save signature for word section ID {$word->id}");
                        }
                    }
                }
    
                return redirect()->route('rulesOutlines-edit', ['id' => $dictation->id])
                    ->with('success', 'Rules outlines updated successfully!');
            } catch (\Exception $e) {
                // Log the error for debugging
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
                'description'=> $item->description,
                'image'=> $item->image,
            ];
        });

        return response()->json($dictation);
    }

    public function TypeOutlinesApi($id)
    {
        $dictation = TypeRulesOutline::where('rules_outline_id', $id)->get();

        return response()->json($dictation);
    }
}

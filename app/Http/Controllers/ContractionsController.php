<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contraction;

class ContractionsController extends Controller
{
    public function contractions(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'sentence' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $dictation = new Contraction();
            $dictation->sentence = $request->input('sentence');
            $dictation->language_id = $request->input('languageId');
            $dictation->save(); 
    
            return response()->json([
                'message' => 'sentence added successfully!',
                'id' => $dictation->id,
            ], 200);
        }
        return Inertia::render('Contraction/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = Contraction::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function contractionsEdit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'sentence' => 'required|string|max:255',
                'image' => 'nullable|string',
            ]);
        
            $dictation = Contraction::findOrFail($id);
            
            if ($request->input('image')) {
                $imageData = $request->input('image');
                $imagePath = public_path("images/contractions/{$dictation->id}_contractions.png");
                $imageContent = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
                file_put_contents($imagePath, $imageContent);
                $dictation->image = "/images/contractions/{$dictation->id}_contractions.png";
            } elseif ($request->has('clearSign') && $request->clearSign) {
                $dictation->image = null;
                $imagePath = public_path("images/contractions/{$dictation->id}_contractions.png");
            
                if (file_exists($imagePath)) {
                    unlink($imagePath); 
                }
            }

            $dictation->sentence = $request->input('sentence');
            $dictation->save();
        
            return redirect()->route('contractions-edit', ['id' => $dictation->id])
                ->with('success', 'contractions updated successfully!');
        }              
        
        $dictation = Contraction::findOrFail($id);

        $dictationData = [
            'id'=> $dictation->id,
            'sentence'=> $dictation->sentence,
            'image'=> $dictation->image,
        ];

        // echo "<pre>"; print_r($dictationData); die;
        return Inertia::render('Contraction/Edit', [
            'dictation' => $dictationData
        ]);
    }

    public function destroy($id)
    {
        $word = Contraction::find($id);

        if ($word) {
            $word->delete();
            return response()->json(['message' => 'Word deleted successfully'], 200);
        }

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function contractionsApi($id)
    {
        $dictation = Contraction::where('language_id', $id)->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                'sentence'=> $item->sentence,
                'image'=> $item->image,
            ];
        });

        return response()->json(['contraction' => $dictationData ]);
    }

    public function SearchByContractions(Request $request)
    {
        $request->validate([
            'sentence' => 'required|string'
        ]);

        $searchOutline = Contraction::select('sentence', 'image' )->where('sentence', $request->sentence)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        return response()->json([
            'contraction' => $searchOutline,
        ], 200);
    }
}

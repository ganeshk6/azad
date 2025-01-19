<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Dictation;

class DictationController extends Controller
{
    public function dictation(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'sentence' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $dictation = new Dictation();
            $dictation->sentence = $request->input('sentence');
            $dictation->language_id = $request->input('languageId');
            $dictation->save(); 
    
            return response()->json([
                'message' => 'sentence added successfully!',
                'id' => $dictation->id,
            ], 200);
        }
        return Inertia::render('Dictation/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = Dictation::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function dictationEdit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'sentence' => 'required|string|max:255',
                'image' => 'nullable|string',
                'link' => 'nullable|string|max:255',
            ]);
        
            $dictation = Dictation::findOrFail($id);
        
            if ($request->input('image')) {
                $imageData = $request->input('image');
                $imagePath = public_path("images/dictations/{$dictation->id}_dictations.png");
                $imageContent = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
                file_put_contents($imagePath, $imageContent);
                $dictation->image = "/images/dictations/{$dictation->id}_dictations.png";
            } elseif ($request->has('clearSign') && $request->clearSign) {
                $dictation->image = null;
                $imagePath = public_path("images/dictations/{$dictation->id}_dictations.png");
            
                if (file_exists($imagePath)) {
                    unlink($imagePath); 
                }
            }
        
            $dictation->sentence = $request->input('sentence');
            $dictation->link = $request->input('link');
            $dictation->save();
        
            return redirect()->route('dictation-edit', ['id' => $dictation->id])
                ->with('success', 'Dictation updated successfully!');
        }              
        
        $dictation = Dictation::findOrFail($id);

        $dictationData = [
            'id'=> $dictation->id,
            'sentence'=> $dictation->sentence,
            'link'=> $dictation->link,
            'image'=> $dictation->image,
        ];

        // echo "<pre>"; print_r($dictationData); die;
        return Inertia::render('Dictation/Edit', [
            'dictation' => $dictationData
        ]);
    }

    public function destroy($id)
    {
        $word = Dictation::find($id);

        if ($word) {
            $word->delete();
            return response()->json(['message' => 'Word deleted successfully'], 200);
        }

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function dictationApi($id)
    {
        $dictation = Dictation::where('language_id', $id)->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                'word'=> $item->sentence,
                'link'=> $item->link,
                'sign'=> $item->image,
            ];
        });
        return response()->json($dictationData, 200);
    }

    public function SearchByDictationApi(Request $request)
    {
        $request->validate([
            'word' => 'required|string'
        ]);

        $searchOutline = Dictation::where('sentence', $request->word)->get();

        $dictationData = $searchOutline->map(function ($item) {
            return [
                'word'=> $item->sentence,
                'link'=> $item->link,
                'sign'=> $item->image,
            ];
        });

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        return response()->json($dictationData,200);
    }
}

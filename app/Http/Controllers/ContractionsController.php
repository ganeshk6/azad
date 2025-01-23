<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contraction;
use Storage;

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
                'sentence' => 'required|string',
                'image' => 'nullable',
            ]);
        
            $dictation = Contraction::findOrFail($id);
            
            if ($request->hasFile('image')) {
                // Get the uploaded file
                $file = $request->file('image');
            
                $fileName = "{$dictation->id}_contractions." . $file->getClientOriginalExtension();
                $filePath = "images/contractions/{$fileName}";
            
                // Store the file in the public disk
                $file->storeAs('images/contractions', $fileName, 'public');
            
                // Save the file path to the database
                $dictation->image = $filePath;
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
                'word'=> $item->sentence,
                'sign'=> $item->image,
            ];
        });

        return response()->json( $dictationData );
    }

    public function SearchByContractions(Request $request)
    {
        $request->validate([
            'sentence' => 'required|string'
        ]);

        $searchOutline = Contraction::where('sentence', $request->sentence)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }
        $searchOutlineData = [
            'word' => $searchOutline->sentence,
            'sign' => $searchOutline->image,
        ];

        return response()->json([$searchOutlineData]);
    }
}

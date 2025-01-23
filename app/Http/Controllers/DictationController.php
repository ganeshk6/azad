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
                'image' => 'nullable',
                'link' => 'nullable|string|max:255',
            ]);
        
            $dictation = Dictation::findOrFail($id);
        
            if ($request->hasFile('image')) {
                // Get the uploaded file
                $file = $request->file('image');
            
                // Generate a unique file name with the desired directory
                $fileName = "{$dictation->id}_dictations." . $file->getClientOriginalExtension();
                $filePath = "images/dictations/{$fileName}";
            
                // Store the file in the public disk
                $file->storeAs('images/dictations', $fileName, 'public');
            
                // Save the file path to the database
                $dictation->image = $filePath;
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

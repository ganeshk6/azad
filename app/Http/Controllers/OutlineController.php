<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Outline;
use App\Models\SearchOutline;

class OutlineController extends Controller
{
    public function outlines(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'sentence' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $dictation = new Outline();
            $dictation->sentence = $request->input('sentence');
            $dictation->language_id = $request->input('languageId');
            $dictation->save(); 
    
            return response()->json([
                'message' => 'sentence added successfully!',
                'id' => $dictation->id,
            ], 200);
        }
        return Inertia::render('Outline/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = Outline::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function outlinesEdit(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            $notes = $request->input('notes');
            if (is_string($notes)) {
                $notes = json_decode($notes, true);
            }

            // Validate the notes input
            if (!is_array($notes)) {
                return response()->json(['message' => 'Invalid notes format.'], 422);
            }

            $request->merge(['notes' => $notes]);

            $request->validate([
                'sentence' => 'required|string|max:255',
                'image' => 'nullable',
                'notes' => 'nullable|array',
                'notes.*.notes' => 'nullable|string|max:255',
            ]);
        
            $dictation = Outline::findOrFail($id);
        
            if ($request->hasFile('image')) {
                // Get the uploaded file
                $file = $request->file('image');
            
                // Generate a unique file name with the desired directory
                $fileName = "{$dictation->id}_outlines." . $file->getClientOriginalExtension();
                $filePath = "images/outlines/{$fileName}";
            
                // Store the file in the public disk
                $file->storeAs('images/outlines', $fileName, 'public');
            
                // Save the file path to the database
                $dictation->image = $filePath;
            }
        
            $dictation->sentence = $request->input('sentence');
            $dictation->save();
        
            $dictation->OutlineSearch()->delete();

            foreach ($notes as $note) {
                SearchOutline::create([
                    'outline_id' => $dictation->id,
                    'notes' => $note['notes'],
                ]);
            }
        
            return redirect()->route('outlines-edit', ['id' => $dictation->id])
                ->with('success', 'Outlines updated successfully!');
        }
                     
        
        $dictation = Outline::with('OutlineSearch')->findOrFail($id);
        $wordSections = $dictation->OutlineSearch->map(function ($word) {
            return [
                'id' => $word->id,
                'outline_id' => $word->outline_id,
                'notes' => $word->notes,
            ];
        });

        $dictationData = [
            'id'=> $dictation->id,
            'sentence'=> $dictation->sentence,
            'image'=> $dictation->image,
            'notes' => $wordSections,
        ];

        // echo "<pre>"; print_r($dictationData); die;
        return Inertia::render('Outline/Edit', [
            'dictation' => $dictationData
        ]);
    }

    public function destroy($id)
    {
        $word = Outline::find($id);

        if ($word) {
            $word->delete();
            return response()->json(['message' => 'Word deleted successfully'], 200);
        }

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function outlinesApi($id)
    {
        $dictation = Outline::where('language_id', $id)->with('OutlineSearch')->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                // 'id' => $item->id,
                'word'=> $item->sentence,
                'sign'=> $item->image,
                // 'outline_search' => $item->OutlineSearch->select('notes')
            ];
        });

        return response()->json($dictationData, 200);
    }

    public function SearchOutlinesApi($id)
    {
        $dictation = SearchOutline::select('notes')->where('outline_id', $id)->get();

        return response()->json(['dictation' => $dictation ]);
    }
    public function SearchBy(Request $request)
    {
        $request->validate([
            'notes' => 'required|string'
        ]);

        $searchOutlines = SearchOutline::where('notes', $request->notes)->get();
        
        if ($searchOutlines->isEmpty()) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        $results = [];

        foreach ($searchOutlines as $searchOutline) {
            $outline = Outline::find($searchOutline->outline_id);

            if ($outline) {
                $results[] = [
                    'notes' => $searchOutline->notes,
                    'word' => $outline->sentence,
                    'sign' => $outline->image,
                ];
            }
        }

        if (empty($results)) {
            return response()->json([
                'message' => 'No associated outlines found for the provided notes.',
            ], 404);
        }

        return response()->json($results);
    }

}

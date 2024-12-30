<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RulesOutline;

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
                'description' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        
            $dictation = RulesOutline::findOrFail($id);
        
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $dictation->id . '_rulesoutlines.' . $image->getClientOriginalExtension();
                $imagePath = public_path('images/rulesOutlines');
                $image->move($imagePath, $imageName);
        
                $dictation->image = 'images/rulesOutlines/' . $imageName;
            }
        
            $dictation->sentence = $request->input('sentence');
            $dictation->description = $request->input('description');
            $dictation->save();
        
            return redirect()->route('rulesOutlines-edit', ['id' => $dictation->id])
                ->with('success', 'rulesOutlines updated successfully!');
        }              
        
        $dictation = RulesOutline::findOrFail($id);

        $dictationData = [
            'id'=> $dictation->id,
            'sentence'=> $dictation->sentence,
            'description'=> $dictation->description,
            'image'=> $dictation->image,
        ];

        // echo "<pre>"; print_r($dictationData); die;
        return Inertia::render('RulesOutline/Edit', [
            'dictation' => $dictationData
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
        $dictation = RulesOutline::where('language_id', $id)->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                'id'=> $item->id,
                'sentence'=> $item->sentence,
                'description'=> $item->description,
                'image'=> $item->image,
            ];
        });

        return response()->json($dictationData);
    }
}

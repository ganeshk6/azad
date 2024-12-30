<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Outline;

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
            $request->validate([
                'sentence' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        
            $dictation = Outline::findOrFail($id);
        
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $dictation->id . '_outlines.' . $image->getClientOriginalExtension();
                $imagePath = public_path('images/outlines');
                $image->move($imagePath, $imageName);
        
                $dictation->image = 'images/outlines/' . $imageName;
            }
        
            $dictation->sentence = $request->input('sentence');
            $dictation->save();
        
            return redirect()->route('outlines-edit', ['id' => $dictation->id])
                ->with('success', 'Outlines updated successfully!');
        }              
        
        $dictation = Outline::findOrFail($id);

        $dictationData = [
            'id'=> $dictation->id,
            'sentence'=> $dictation->sentence,
            'image'=> $dictation->image,
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
        $dictation = Outline::where('language_id', $id)->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                'id'=> $item->id,
                'sentence'=> $item->sentence,
                'image'=> $item->image,
            ];
        });

        return response()->json($dictationData);
    }
}

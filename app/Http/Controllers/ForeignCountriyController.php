<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForeignCountry;
use Inertia\Inertia;

class ForeignCountryController extends Controller
{
    public function foreignContries(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $phrases = new ForeignCountry();
            $phrases->letter = $request->input('letter');
            $phrases->language_id = $request->input('languageId');
            $phrases->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $phrases->id,
            ], 200);
        }
        return Inertia::render('ForeignCountry/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = ForeignCountry::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string',
                'sign' => 'nullable'
            ]);

            $phrase = ForeignCountry::findOrFail($id);
            $phrase->letter = $request->input('letter');

            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                $fileName = "{$phrase->id}_foreign_countries." . $file->getClientOriginalExtension();
                $filePath = "images/ForeignCountry/{$fileName}";

                $file->storeAs('images/ForeignCountry', $fileName, 'public');
            
                $phrase->sign = $filePath;
            } 

            $phrase->save();

            return redirect()->route('foreign-contries-edit', ['id' => $phrase->id])
                ->with('success', 'ForeignCountry updated successfully!');
        }

        $phrase = ForeignCountry::findOrFail($id);

        return Inertia::render('ForeignCountry/Edit', [
            'phrasesData' => [
                'id' => $phrase->id,
                'letter' => $phrase->letter,
                'sign' => $phrase->sign,
            ],
        ]);
    }

    public function destroy($id)
    {
        $word = ForeignCountry::find($id);

        $word->delete();

        return response()->json(['message' => 'Word not found'], 404);
    }

}

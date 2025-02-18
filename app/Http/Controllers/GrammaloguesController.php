<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Grammalogue;
use App\Models\SubGrammalogue;

class GrammaloguesController extends Controller
{
    public function grammalogues(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'wordTitle' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $dictionary = new Grammalogue();
            $dictionary->word = $request->input('wordTitle');
            $dictionary->language_id = $request->input('languageId');
            $dictionary->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $dictionary->id,
            ], 200);
        }
        return Inertia::render('Grammalogue/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = Grammalogue::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function grammaloguesEdit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'word' => 'required|string|max:255',
                'description' => 'nullable|string',
                'sub_entries' => 'nullable',
                'sub_entries.*.sub_word' => 'nullable|string|max:255',
                'sub_entries.*.sub_description' => 'nullable|string',
                'sign' => 'nullable', 
            ]);

            $dictionary = Grammalogue::findOrFail($id);

            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                // Generate a unique file name with the desired directory
                $fileName = "{$dictionary->id}_grammalogue." . $file->getClientOriginalExtension();
                $filePath = "images/grammalogue/{$fileName}";
            
                // Store the file in the public disk
                $file->storeAs('images/grammalogue', $fileName, 'public');
            
                // Save the file path to the database
                $dictionary->sign = $filePath;
            } 

            $dictionary->word = $request->input('word');
            $dictionary->description = $request->input('description');
            $dictionary->save();

            $subEntries = $request->has('sub_entries') 
                    ? json_decode($request->input('sub_entries', '[]'), true) 
                    : [];
            // Save sub-entries
            $dictionary->subEntries()->delete();
            foreach ($subEntries as $subEntry) {
                SubGrammalogue::create([
                    'grammalogue_id' => $dictionary->id,
                    'sub_word' => $subEntry['sub_word'],
                    'sub_description' => $subEntry['sub_description'],
                ]);
            }

            return redirect()->route('grammalogues-edit', ['id' => $dictionary->id])
                ->with('success', 'grammalogues updated successfully!');
        }

        $dictionary = Grammalogue::with('subEntries')->findOrFail($id);

        return Inertia::render('Grammalogue/Edit', [
            'dictionary' => $dictionary,
        ]);
    }

    public function destroy($id)
    {
        $word = Grammalogue::find($id);

        if ($word) {
            SubGrammalogue::where('grammalogue_id', $id)->delete();
            $word->delete();
            return response()->json(['message' => 'Word deleted successfully'], 200);
        }

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function grammaloguesApi($id)
    {
        $dictation = Grammalogue::select('word', 'sign')->where('language_id', $id)->get();

        return response()->json($dictation);
    }

    public function subGrammaloguesApi($id)
    {
        $dictation = SubGrammalogue::where('grammalogue_id', $id)->get();

        return response()->json($dictation);
    }

    public function SearchByGrammalogue(Request $request)
    {
        $request->validate([
            'word' => 'required|string'
        ]);

        $searchOutline = Grammalogue::select('word', 'sign')->where('word', $request->word)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        return response()->json([$searchOutline]);
    }
}

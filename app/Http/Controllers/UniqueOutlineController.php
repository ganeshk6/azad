<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UniqueOutline;
use Inertia\Inertia;

class UniqueOutlineController extends Controller
{
    public function uniqueOutline(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $phrases = new UniqueOutline();
            $phrases->letter = $request->input('letter');
            $phrases->language_id = $request->input('languageId');
            $phrases->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $phrases->id,
            ], 200);
        }
        return Inertia::render('UniqueOutline/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = UniqueOutline::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            // echo"<pre>";print_r($request->all());die;
            $request->validate([
                'letter' => 'required|string',
                'sign' => 'nullable'
            ]);
            
            $phrase = UniqueOutline::findOrFail($id);
            $phrase->letter = $request->input('letter');
            
            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                $fileName = "{$phrase->id}_unique_outline." . $file->getClientOriginalExtension();
                $filePath = "images/UniqueOutline/{$fileName}";

                $file->storeAs('images/UniqueOutline', $fileName, 'public');
            
                $phrase->sign = $filePath;
            } 

            $phrase->save();

            return redirect()->route('unique-outline-edit', ['id' => $phrase->id])
                ->with('success', 'UniqueOutline updated successfully!');
        }

        $phrase = UniqueOutline::findOrFail($id);

        return Inertia::render('UniqueOutline/Edit', [
            'phrasesData' => [
                'id' => $phrase->id,
                'letter' => $phrase->letter,
                'sign' => $phrase->sign,
            ],
        ]);
    }

    public function destroy($id)
    {
        $word = UniqueOutline::find($id);

        $word->delete();

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function getApi($id)
    {
        $dictation = UniqueOutline::where('language_id', $id)->orderBy('letter', 'asc')->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                // 'id' => $item->id,
                'word'=> $item->letter,
                'sign'=> $item->sign,
                // 'outline_search' => $item->OutlineSearch->select('notes')
            ];
        });

        return response()->json($dictationData);
    }

    public function searchApi(Request $request)
    {
        $request->validate([
            'letter' => 'required|string'
        ]);

        $searchOutline = UniqueOutline::select('letter', 'sign')->where('letter', $request->letter)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        $responseData = [
            'word' => $searchOutline->letter,
            'sign' => $searchOutline->sign,
        ];

        return response()->json([$responseData]);
    }
}

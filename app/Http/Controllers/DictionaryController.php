<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Dictionary;
use App\Models\SubDictionary;
use App\Models\ChieldDictionary;

class DictionaryController extends Controller
{
    public function dictionary(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'wordTitle' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $dictionary = new Dictionary();
            $dictionary->word = $request->input('wordTitle');
            $dictionary->language_id = $request->input('languageId');
            $dictionary->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $dictionary->id,
            ], 200);
        }
        return Inertia::render('Dictionary/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = Dictionary::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function dictionaryEdit(Request $request, $id)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'word' => 'required|string|max:255',
                'sub_entries' => 'nullable|array',
                // 'sub_entries.*.image' => 'nullable|image',
                // 'sub_entries.*.child_entries.*.image' => 'nullable|image',
            ]);
        
            $dictionary = Dictionary::findOrFail($id);
        
            // Update the dictionary word
            $dictionary->word = $request->input('word');
            $dictionary->save();
            // Delete existing sub-entries and their child entries
            foreach ($dictionary->subEntries as $subEntry) {
                $subEntry->childEntries()->delete(); 
                $subEntry->delete(); 
            }
        
            $subDictionaries = $request->input('sub_entries');
            if(isset($subDictionaries)){
                foreach ($subDictionaries as $subDictIndex => $subDict) {
                    $subImagePath = $subDict['image'] ?? null;  
                    if ($subImagePath && strpos($subImagePath, 'http') === 0) {
                        $subImagePath = str_replace(asset('storage/app/public/'). '/', '', $subImagePath);
                    }
                    if ($request->hasFile("sub_entries.{$subDictIndex}.image")) {
                        $subImage = $request->file("sub_entries.{$subDictIndex}.image");
                        $subImageName = "sub_dict_{$subDictIndex}." . $subImage->getClientOriginalExtension();
                        $subImagePath = "images/dictionary/sub_dictionary/{$subImageName}";
                
                        $subImage->storeAs('images/dictionary/sub_dictionary', $subImageName, 'public');
                    }   
                    $subDictionary = SubDictionary::updateOrCreate(
                        [
                            'id' => $subDict['id'] ?? null,
                            'dictionary_id' => $dictionary->id,
                            'language_id' => $dictionary->language_id
                        ],
                        [
                            'title' => $subDict['title'],
                            'image' => $subImagePath,
                        ]
                    );
                    // echo"<pre>";print_r($subDictionaries);die;
                    if(isset($subDict['child_entries'])){
                        foreach ($subDict['child_entries'] as $childIndex=>$child) {
                            $childImagePath = $child['image'] ?? null;  
                            if ($childImagePath && strpos($childImagePath, 'http') === 0) {
                                $childImagePath = str_replace(asset('storage/app/public/').'/', '', $childImagePath);
                            }
                            if ($request->hasFile("sub_entries.{$subDictIndex}.child_entries.{$childIndex}.image")) {
                                $subImage = $request->file("sub_entries.{$subDictIndex}.child_entries.{$childIndex}.image");
                                $subImageName = "chield_dict_{$subDictIndex}." . $subImage->getClientOriginalExtension();
                                $childImagePath = "images/dictionary/child_dictionary/{$subImageName}";
                        
                                $subImage->storeAs('images/dictionary/child_dictionary', $subImageName, 'public');
                            }

                            // Create or update child entry
                            ChieldDictionary::updateOrCreate(
                                [
                                    'id' => $child['id'] ?? null,
                                    'sub_dictionary_id' => $subDictionary->id,
                                ],
                                [
                                    'title' => $child['title'],
                                    'image' => $childImagePath,
                                    'dictionary_id' => $dictionary->id,
                                ]
                            );
                        }
                    }
                }
            }
        
            return redirect()->route('dictionary-edit', ['id' => $dictionary->id])
                ->with('success', 'Dictionary updated successfully!');
        }
        

        $dictionary = Dictionary::with('subEntries.childEntries')->findOrFail($id);

        // echo"<pre>";print_r($dictionary->toArray());die;

        return Inertia::render('Dictionary/Edit', [
            'dictionary' => $dictionary,
        ]);
    }

    public function destroy($id)
    {
        $word = Dictionary::find($id);

        if ($word) {
            SubDictionary::where('dictionary_id', $id)->delete();
            $word->delete();
            return response()->json(['message' => 'Word deleted successfully'], 200);
        }

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function dictionaryApi($id)
    {
        $dictation = SubDictionary::select('id','title', 'image')->where('language_id', $id)->get();

        return response()->json($dictation);
    }
    
    public function subDictionaryApi($id){
        $familierWord = SubDictionary::where('id', $id)->with('childEntries')->first();

        $chieldDic = SubDictionary::where('id', '!=', $familierWord->id)->where('dictionary_id', $familierWord->dictionary_id)->get();

        return response()->json([
            'family' => $familierWord, 
            'similer_family' => $chieldDic
        ]);
    }

    public function SearchByDictinary(Request $request)
    {
        $request->validate([
            'title' => 'required|string'
        ]);

        $searchOutline = SubDictionary::where('title', $request->title)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        return response()->json([
            'search_by'=> $searchOutline, 
        ]);
    }

    public function uploadImage($image, $dictionaryId, $type)
    {
        $path = "images/dictionary/{$dictionaryId}/{$folder}";
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path($path), $imageName);
        return "{$path}/{$imageName}";
        
    }


}

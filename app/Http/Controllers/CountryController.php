<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\SubCountry;
use Inertia\Inertia;

class CountryController extends Controller
{
    public function countries(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $phrases = new Country();
            $phrases->letter = $request->input('letter');
            $phrases->language_id = $request->input('languageId');
            $phrases->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $phrases->id,
            ], 200);
        }
        return Inertia::render('Country/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = Country::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string',
                'sign' => 'nullable',
                'SubCountry' => 'nullable|array',
                'SubCountry.*.title' => 'nullable|string',
                'SubCountry.*.image' => 'nullable|image',
            ]);

            $phrase = Country::findOrFail($id);
            $phrase->letter = $request->input('letter');

            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                $fileName = "{$phrase->id}_countries." . $file->getClientOriginalExtension();
                $filePath = "images/Country/{$fileName}";

                $file->storeAs('images/Country', $fileName, 'public');
            
                $phrase->sign = $filePath;
            } 

            $phrase->save();

            // Handle word sections
            $incomingIds = collect($request->input('SubCountry'))->pluck('id')->filter()->toArray();
            $existingIds = SubCountry::where('country_id', $phrase->id)->pluck('id')->toArray();
            $idsToDelete = array_diff($existingIds, $incomingIds);
            SubCountry::whereIn('id', $idsToDelete)->delete();

            foreach ($request->input('SubCountry') as $index => $section) {
                $word = SubCountry::updateOrCreate(
                    ['id' => $section['id'] ?? null],
                    [
                        'country_id' => $phrase->id,
                        'language_id' => $phrase->language_id,
                        'title' => $section['title'],
                    ]
                );

                // Handle signature upload
                if ($request->hasFile("SubCountry.{$index}.image")) {
                    $file = $request->file("SubCountry.{$index}.image");
                    // echo"<pre>";print_r($file);die;
                    $fileName = "{$word->id}_subday." . $file->getClientOriginalExtension();
                    $filePath = "images/SubCountry/{$fileName}";

                    // Store file
                    $file->storeAs('images/SubCountry', $fileName, 'public');
                    $word->image = "/{$filePath}";
                    $word->save();
                }
            }


            return redirect()->route('countries-edit', ['id' => $phrase->id])
                ->with('success', 'Country updated successfully!');
        }

        $phrase = Country::findOrFail($id);

        return Inertia::render('Country/Edit', [
            'phrasesData' => [
                'id' => $phrase->id,
                'letter' => $phrase->letter,
                'sign' => $phrase->sign,
                'SubCountry' => $phrase->SubCountry->map(function ($sunday) {
                    return [
                        'id' => $sunday->id,
                        'title' => $sunday->title,
                        'image' => $sunday->image,
                    ];
                }),
            ],
        ]);
    }

    public function destroy($id)
    {
        $word = Country::find($id);

        $word->delete();

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function getApi($id)
    {
        $dictation = Country::with('SubCountry')->where('language_id', $id)->orderBy('letter', 'asc')->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                // 'id' => $item->id,
                'word'=> $item->letter,
                'sign'=> $item->sign,
                'sub_country' => $item->SubCountry->map(function ($sunday) {
                    return [
                        'id' => $sunday->id,
                        'title' => $sunday->title,
                        'image' => $sunday->image,
                    ];
                }),
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

        $searchOutline = Country::with('SubCountry')->where('letter', $request->letter)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        $responseData = [
            'word' => $searchOutline->letter,
            'sign' => $searchOutline->sign,
            'sub_country' => $searchOutline->SubCountry->map(function ($sunday) {
                return [
                    'id' => $sunday->id,
                    'title' => $sunday->title,
                    'image' => $sunday->image,
                ];
            }),
        ];

        return response()->json([$responseData]);
    }
}

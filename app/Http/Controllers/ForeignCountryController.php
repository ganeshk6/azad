<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForeignCountry;
use App\Models\SubForeignCountry;
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
                'sign' => 'nullable',
                'SubForeignCountry' => 'nullable|array',
                'SubForeignCountry.*.title' => 'nullable|string',
                'SubForeignCountry.*.image' => 'nullable|image',
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

            // Handle word sections
            $incomingIds = collect($request->input('SubForeignCountry'))->pluck('id')->filter()->toArray();
            $existingIds = SubForeignCountry::where('foreign_country_id', $phrase->id)->pluck('id')->toArray();
            $idsToDelete = array_diff($existingIds, $incomingIds);
            SubForeignCountry::whereIn('id', $idsToDelete)->delete();

            foreach ($request->input('SubForeignCountry') as $index => $section) {
                $word = SubForeignCountry::updateOrCreate(
                    ['id' => $section['id'] ?? null],
                    [
                        'foreign_country_id' => $phrase->id,
                        'language_id' => $phrase->language_id,
                        'title' => $section['title'],
                    ]
                );

                // Handle signature upload
                if ($request->hasFile("SubForeignCountry.{$index}.image")) {
                    $file = $request->file("SubForeignCountry.{$index}.image");
                    // echo"<pre>";print_r($file);die;
                    $fileName = "{$word->id}_subday." . $file->getClientOriginalExtension();
                    $filePath = "images/SubForeignCountry/{$fileName}";

                    // Store file
                    $file->storeAs('images/SubForeignCountry', $fileName, 'public');
                    $word->image = "/{$filePath}";
                    $word->save();
                }
            }

            return redirect()->route('foreign-contries-edit', ['id' => $phrase->id])
                ->with('success', 'ForeignCountry updated successfully!');
        }

        $phrase = ForeignCountry::findOrFail($id);

        return Inertia::render('ForeignCountry/Edit', [
            'phrasesData' => [
                'id' => $phrase->id,
                'letter' => $phrase->letter,
                'sign' => $phrase->sign,
                'SubForeignCountry' => $phrase->SubForeignCountry->map(function ($sunday) {
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
        $word = ForeignCountry::find($id);

        $word->delete();

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function getApi($id)
    {
        $dictation = ForeignCountry::with('SubForeignCountry')->where('language_id', $id)->orderBy('letter', 'asc')->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                // 'id' => $item->id,
                'word'=> $item->letter,
                'sign'=> $item->sign,
                'sub_foreign_country' => $item->SubForeignCountry->map(function ($sunday) {
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

        $searchOutline = ForeignCountry::with('SubForeignCountry')->where('letter', $request->letter)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        $responseData = [
            'word' => $searchOutline->letter,
            'sign' => $searchOutline->sign,
            'sub_foreign_country' => $searchOutline->SubForeignCountry->map(function ($sunday) {
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

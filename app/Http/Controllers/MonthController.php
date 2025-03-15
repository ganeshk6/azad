<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Month;
use App\Models\SubMonth;
use Inertia\Inertia;

class MonthController extends Controller
{
    public function months(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $phrases = new Month();
            $phrases->letter = $request->input('letter');
            $phrases->language_id = $request->input('languageId');
            $phrases->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $phrases->id,
            ], 200);
        }
        return Inertia::render('Month/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = Month::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string',
                'sign' => 'nullable',
                'SubMonth' => 'nullable|array',
                'SubMonth.*.title' => 'nullable|string',
                'SubMonth.*.image' => 'nullable|image',
            ]);

            $phrase = Month::findOrFail($id);
            $phrase->letter = $request->input('letter');

            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                $fileName = "{$phrase->id}_months." . $file->getClientOriginalExtension();
                $filePath = "images/Month/{$fileName}";

                $file->storeAs('images/Month', $fileName, 'public');
            
                $phrase->sign = $filePath;
            } 

            $phrase->save();

            // Handle word sections
            $incomingIds = collect($request->input('SubMonth'))->pluck('id')->filter()->toArray();
            $existingIds = SubMonth::where('month_id', $phrase->id)->pluck('id')->toArray();
            $idsToDelete = array_diff($existingIds, $incomingIds);
            SubMonth::whereIn('id', $idsToDelete)->delete();

            foreach ($request->input('SubMonth') as $index => $section) {
                $word = SubMonth::updateOrCreate(
                    ['id' => $section['id'] ?? null],
                    [
                        'month_id' => $phrase->id,
                        'language_id' => $phrase->language_id,
                        'title' => $section['title'],
                    ]
                );

                // Handle signature upload
                if ($request->hasFile("SubMonth.{$index}.image")) {
                    $file = $request->file("SubMonth.{$index}.image");
                    // echo"<pre>";print_r($file);die;
                    $fileName = "{$word->id}_subday." . $file->getClientOriginalExtension();
                    $filePath = "images/SubMonth/{$fileName}";

                    // Store file
                    $file->storeAs('images/SubMonth', $fileName, 'public');
                    $word->image = "/{$filePath}";
                    $word->save();
                }
            }

            return redirect()->route('months-edit', ['id' => $phrase->id])
                ->with('success', 'Month updated successfully!');
        }

        $phrase = Month::findOrFail($id);

        return Inertia::render('Month/Edit', [
            'phrasesData' => [
                'id' => $phrase->id,
                'letter' => $phrase->letter,
                'sign' => $phrase->sign,
                'SubMonth' => $phrase->SubMonth->map(function ($sunday) {
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
        $word = Month::find($id);

        $word->delete();

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function getApi($id)
    {
        $dictation = Month::with('SubMonth')->where('language_id', $id)->orderBy('letter', 'asc')->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                // 'id' => $item->id,
                'word'=> $item->letter,
                'sign'=> $item->sign,
                'sub_month'=> $item->SubMonth->map(function ($sunday) {
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

        $searchOutline = Month::with('SubMonth')->where('letter', $request->letter)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        $responseData = [
            'word' => $searchOutline->letter,
            'sign' => $searchOutline->sign,
            'sub_day'=> $searchOutline->SubMonth->map(function ($sunday) {
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Day;
use App\Models\SubDay;
use Inertia\Inertia;

class DayController extends Controller
{
    public function days(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $phrases = new Day();
            $phrases->letter = $request->input('letter');
            $phrases->language_id = $request->input('languageId');
            $phrases->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $phrases->id,
            ], 200);
        }
        return Inertia::render('Day/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = Day::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string',
                'sign' => 'nullable',
                'SubDay' => 'nullable|array',
                'SubDay.*.title' => 'nullable|string',
                'SubDay.*.image' => 'nullable|image',
            ]);

            $phrase = Day::findOrFail($id);
            $phrase->letter = $request->input('letter');

            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                $fileName = "{$phrase->id}_days." . $file->getClientOriginalExtension();
                $filePath = "images/Day/{$fileName}";

                $file->storeAs('images/Day', $fileName, 'public');
            
                $phrase->sign = $filePath;
            } 

            $phrase->save();

            // Handle word sections
            $incomingIds = collect($request->input('SubDay'))->pluck('id')->filter()->toArray();
            $existingIds = SubDay::where('day_id', $phrase->id)->pluck('id')->toArray();
            $idsToDelete = array_diff($existingIds, $incomingIds);
            SubDay::whereIn('id', $idsToDelete)->delete();

            foreach ($request->input('SubDay') as $index => $section) {
                $word = SubDay::updateOrCreate(
                    ['id' => $section['id'] ?? null],
                    [
                        'day_id' => $phrase->id,
                        'language_id' => $phrase->language_id,
                        'title' => $section['title'],
                    ]
                );

                // Handle signature upload
                if ($request->hasFile("SubDay.{$index}.image")) {
                    $file = $request->file("SubDay.{$index}.image");
                    // echo"<pre>";print_r($file);die;
                    $fileName = "{$word->id}_subday." . $file->getClientOriginalExtension();
                    $filePath = "images/subday/{$fileName}";

                    // Store file
                    $file->storeAs('images/subday', $fileName, 'public');
                    $word->image = "/{$filePath}";
                    $word->save();
                }
            }

            return redirect()->route('days-edit', ['id' => $phrase->id])
                ->with('success', 'Day updated successfully!');
        }

        $phrase = Day::with('SubDay')->findOrFail($id);

        return Inertia::render('Day/Edit', [
            'phrasesData' => [
                'id' => $phrase->id,
                'letter' => $phrase->letter,
                'sign' => $phrase->sign,
                'SubDay' => $phrase->SubDay->map(function ($sunday) {
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
        $word = Day::find($id);

        $word->delete();

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function getApi($id)
    {
        $dictation = Day::with('SubDay')->where('language_id', $id)->orderBy('letter', 'asc')->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                // 'id' => $item->id,
                'word'=> $item->letter,
                'sign'=> $item->sign,
                'sub_day'=> $item->SubDay->map(function ($sunday) {
                    return [
                        'id' => $sunday->id,
                        'title' => $sunday->title,
                        'image' => $sunday->image,
                    ];
                }),
            ];
        });

        return response()->json($dictationData);
    }

    public function searchApi(Request $request)
    {
        $request->validate([
            'letter' => 'required|string'
        ]);

        $searchOutline = Day::with('SubDay')->where('letter', $request->letter)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        $responseData = [
            'word' => $searchOutline->letter,
            'sign' => $searchOutline->sign,
            'sub_day'=> $searchOutline->SubDay->map(function ($sunday) {
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

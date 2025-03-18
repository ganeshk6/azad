<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\SubNotice;
use Inertia\Inertia;

class NoticeController extends Controller
{
    public function notices(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string|max:255',
                'languageId' => 'required|integer|exists:languages,id', 
            ]);
    
            $phrases = new Notice();
            $phrases->letter = $request->input('letter');
            $phrases->language_id = $request->input('languageId');
            $phrases->save(); 
    
            return response()->json([
                'message' => 'Word added successfully!',
                'id' => $phrases->id,
            ], 200);
        }
        return Inertia::render('Notice/Index');
    }
    
    public function getWords(Request $request)
    {
        $request->validate([
            'languageId' => 'required|integer|exists:languages,id', 
        ]);

        $words = Notice::where('language_id', $request->input('languageId'))->get();

        return response()->json($words);
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'letter' => 'required|string',
                'sign' => 'nullable'
            ]);

            $phrase = Notice::findOrFail($id);
            $phrase->letter = $request->input('letter');

            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                $fileName = "{$phrase->id}_notices." . $file->getClientOriginalExtension();
                $filePath = "images/Notice/{$fileName}";

                $file->storeAs('images/Notice', $fileName, 'public');
            
                $phrase->sign = $filePath;
            } 

            $phrase->save();

            // Handle word sections
            $incomingIds = collect($request->input('SubNotice'))->pluck('id')->filter()->toArray();
            $existingIds = SubNotice::where('notice_id', $phrase->id)->pluck('id')->toArray();
            $idsToDelete = array_diff($existingIds, $incomingIds);
            SubNotice::whereIn('id', $idsToDelete)->delete();

            foreach ($request->input('SubNotice') as $index => $section) {
                $word = SubNotice::updateOrCreate(
                    ['id' => $section['id'] ?? null],
                    [
                        'notice_id' => $phrase->id,
                        'language_id' => $phrase->language_id,
                        'title' => $section['title'],
                    ]
                );

                // Handle signature upload
                if ($request->hasFile("SubNotice.{$index}.image")) {
                    $file = $request->file("SubNotice.{$index}.image");
                    // echo"<pre>";print_r($file);die;
                    $fileName = "{$word->id}_sub_notice." . $file->getClientOriginalExtension();
                    $filePath = "images/SubNotice/{$fileName}";

                    // Store file
                    $file->storeAs('images/SubNotice', $fileName, 'public');
                    $word->image = "/{$filePath}";
                    $word->save();
                }
            }// Handle word sections

            return redirect()->route('notices-edit', ['id' => $phrase->id])
                ->with('success', 'Notice updated successfully!');
        }

        $phrase = Notice::findOrFail($id);

        return Inertia::render('Notice/Edit', [
            'phrasesData' => [
                'id' => $phrase->id,
                'letter' => $phrase->letter,
                'sign' => $phrase->sign,
                'SubNotice' => $phrase->SubNotice->map(function ($sunday) {
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
        $word = Notice::find($id);

        $word->delete();

        return response()->json(['message' => 'Word not found'], 404);
    }

    public function getApi($id)
    {
        $dictation = Notice::where('language_id', $id)->orderBy('letter', 'asc')->get();

        $dictationData = $dictation->map(function ($item) {
            return [
                // 'id' => $item->id,
                'word'=> $item->letter,
                'sign'=> $item->sign,
                'sub_notice' => $item->SubNotice->map(function ($sunday) {
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

        $searchOutline = Notice::select('letter', 'sign')->where('letter', $request->letter)->first();

        if (!$searchOutline) {
            return response()->json([
                'message' => 'No matching notes found.',
            ], 404);
        }

        $responseData = [
            'word' => $searchOutline->letter,
            'sign' => $searchOutline->sign,
            'sub_notice' => $searchOutline->SubNotice->map(function ($sunday) {
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

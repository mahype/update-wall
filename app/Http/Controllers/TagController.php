<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:50|unique:tags,name',
            'color' => 'required|in:gray,red,yellow,green,blue,indigo,purple,pink',
        ]);

        Tag::create($request->only('name', 'color'));

        $redirect = $request->input('redirect');

        return redirect($redirect ?? url()->previous())
            ->with('success', 'Tag wurde erstellt.');
    }

    public function destroy(Tag $tag)
    {
        $name = $tag->name;
        $tag->delete();

        return redirect()->back()
            ->with('success', "Tag \"{$name}\" wurde gelöscht.");
    }
}

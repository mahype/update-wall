<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function index()
    {
        $tokens = ApiToken::with('user')
            ->withCount('machines')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.tokens.index', compact('tokens'));
    }

    public function create()
    {
        return view('admin.tokens.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $result = ApiToken::createFor(
            $request->user(),
            $request->name
        );

        return redirect()->route('admin.tokens.index')
            ->with('success', 'Token erstellt.')
            ->with('plain_token', $result['plain_text']);
    }

    public function revoke(ApiToken $token)
    {
        $token->revoke();

        return redirect()->route('admin.tokens.index')
            ->with('success', "Token \"{$token->name}\" wurde widerrufen.");
    }

    public function destroy(ApiToken $token)
    {
        $token->delete();

        return redirect()->route('admin.tokens.index')
            ->with('success', "Token \"{$token->name}\" wurde gelöscht.");
    }
}

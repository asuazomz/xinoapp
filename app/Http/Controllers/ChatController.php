<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Chat::class);

        return Chat::all();
    }

    public function store(Request $request)
    {
        $this->authorize('create', Chat::class);

        $data = $request->validate([
            'name' => ['required'],
            'signed' => ['boolean'],
            'user_id' => ['required', 'exists:users'],
        ]);

        return Chat::create($data);
    }

    public function show(Chat $chat)
    {
        $this->authorize('view', $chat);

        return $chat;
    }

    public function update(Request $request, Chat $chat)
    {
        $this->authorize('update', $chat);

        $data = $request->validate([
            'name' => ['required'],
            'signed' => ['boolean'],
            'user_id' => ['required', 'exists:users'],
        ]);

        $chat->update($data);

        return $chat;
    }

    public function destroy(Chat $chat)
    {
        $this->authorize('delete', $chat);

        $chat->delete();

        return response()->json();
    }
}

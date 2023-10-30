<?php

namespace App\Http\Controllers;

use App\Models\CliSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CliSessionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $cliSession = CliSession::create([
            'name' => $request->name,
            'uuid' => Str::uuid()->toString(),
        ]);

        return response()->json([
            'id' => $cliSession->uuid,
            'url' => route('register', ['cli_session' => $cliSession->uuid]),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliSession = CliSession::with('user')
            ->where('uuid', $id)
            ->firstOrFail();

        // Session exists but no user yet
        if (! $cliSession->user) {
            return response('',202);
        }

        // User is associated with session (successful login or register)
        // Give them a new, usable API token
        $cliSession->delete(); // Ensure we can't use this session any longer

        // TODO: Generate a for-real api token
        $apiToken = Str::random(32);

        return [
            'api_token' => $apiToken,
        ];
    }
}

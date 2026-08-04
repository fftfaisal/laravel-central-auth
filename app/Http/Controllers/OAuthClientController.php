<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Laravel\Passport\ClientRepository;

class OAuthClientController extends Controller
{
    public function __construct(protected ClientRepository $clients) {}

    /**
     * List all OAuth applications.
     */
    public function index()
    {
        $clients = DB::table('oauth_clients')
            ->where('revoked', false)
            ->whereNull('owner_id')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'secret' => $c->secret,
                'redirect' => json_decode($c->redirect_uris ?? '[]', true)[0] ?? '',
                'created_at' => $c->created_at,
            ]);

        return Inertia::render('clients/Index', [
            'clients' => $clients,
        ]);
    }

    /**
     * Create a new OAuth application.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'redirect' => 'required|url|max:500',
        ]);

        $this->clients->createAuthorizationCodeGrantClient(
            name: $validated['name'],
            redirectUris: [$validated['redirect']],
            confidential: true,
            user: null
        );

        return redirect()->route('clients.index')->with('success', 'OAuth application created successfully.');
    }

    /**
     * Update an existing OAuth application.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'redirect' => 'required|url|max:500',
        ]);

        $client = $this->clients->find($id);

        if (! $client) {
            abort(404, 'OAuth client not found.');
        }

        $this->clients->update($client, $validated['name'], [$validated['redirect']]);

        return redirect()->route('clients.index')->with('success', 'OAuth application updated successfully.');
    }

    /**
     * Revoke/delete an OAuth application.
     */
    public function destroy(string $id)
    {
        $client = $this->clients->find($id);

        if (! $client) {
            abort(404, 'OAuth client not found.');
        }

        // Revoke all access tokens for this client
        DB::table('oauth_access_tokens')
            ->where('client_id', $id)
            ->update(['revoked' => true]);

        // Revoke the client
        DB::table('oauth_clients')
            ->where('id', $id)
            ->update(['revoked' => true]);

        return redirect()->route('clients.index')->with('success', 'OAuth application revoked successfully.');
    }
}

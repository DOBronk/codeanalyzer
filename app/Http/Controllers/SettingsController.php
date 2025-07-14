<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['gh_api_key' => 'string|max:255']);

        try {
            $request->user()->settings()->update($data);
        } catch (\Exception $e) {
            return back()->withError('Kon API key niet opslaan')->withInput();
        }

        return redirect()->route('codeanalyzer.settings')->with('message', 'Instellingen opgeslagen!');
    }
}

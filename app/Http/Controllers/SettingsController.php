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
        $data = $request->validate(['apikey' => 'string|max:255']);

        try {
            auth()->user()->settings()->update(['gh_api_key' => $data['apikey']]);
        } catch (\Exception $e) {
            return redirect()->back()->withError('Kon API key niet opslaan');
        }

        return redirect()->route('codeanalyzer.settings')->with('message','Instellingen opgeslagen!');
    }
}

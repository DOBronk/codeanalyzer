<?php

namespace App\Http\Controllers;

use App\Services\GithubService;
use Illuminate\Http\Request;
use App\Utilities\TreeBuilder;

class RepositoriesController extends Controller
{
    /**
     * List all owner repositories
     */
    public function index(Request $request, GithubService $git)
    {
        try {
            $data = $git->gitDatabase()->repositories()->getRepositories($request['owner']);
        } catch (\Exception $e) {
            return response('not found', 404);
        }

        return  array_map(fn($name) => ['name' => $name], $data);
    }


    public function create(Request $request)
    {
        return $request->all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, GithubService $git)
    {
        $items = $git->gitDatabase()->repositories()->getBranches($request['owner'], $request['repository']);

        return array_map(fn($name) => ['name' => $name], $items);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, GithubService $git)
    {
        $items = $git->gitDatabase()->repositories()->getRepositoryTree($request->all());
        return TreeBuilder::buildTree5($items);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, GithubService $git)
    {
        $repository = $git->gitDatabase()->repositories()->getRepository($request['owner'], $request['repository']);
        return $repository['default_branch'];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Services\GithubService;
use Inertia\Inertia;
use App\Utilities\TreeBuilder;

class FileManagerController extends Controller

{

    public function index(Request $request, GithubService $git)
    {
        $files = $git->gitDatabase()->repositories()->getRepositoryFiles(['owner' => 'DOBronk', 'repository' => 'codeanalyzer', 'branch' => 'lang']);
        $data = array_map(fn($name) => ['name' => $name], $git->gitDatabase()->repositories()->getRepositories('DOBronk'));
        $nodes = TreeBuilder::buildTree5($files);

        return Inertia::render(
            'treeviewer',
            ['owner' => 'dobronk', 'csrf' => csrf_token(), 'route' => 'http://localhost/showdata']
        )
            ->withViewData(['vueHeader' => trans('job.create')]);
    }

    public function index2(Request $request, GithubService $git)
    {
        return Inertia::render('repository', ['csrf' => csrf_token()])
            ->withViewData(['inertia' => true, 'vueHeader' => trans('job.create')]);
    }
}

/* {
                    key: '0',
                    data: {
                        name: 'Applications',
                        size: '100kb',
                        type: 'Folder'
                    },
                    children: [
                        {
                            key: '0-0',
                            data: {
                                name: 'Vue',
                                size: '25kb',
                                type: 'Folder'
                            },
                            children: [
                                {
                                    key: '0-0-0',
                                    data: {
                                        name: 'vue.app',
                                        size: '10kb',
                                        type: 'Application'
                                    }
                                },
                                {
                                    key: '0-0-1',
                                    data: {
                                        name: 'native.app',
                                        size: '10kb',
                                        type: 'Application'
                                    }
                                },
                                {
                                    key: '0-0-2',
                                    data: {
                                        name: 'mobile.app',
                                        size: '5kb',
                                        type: 'Application'
                                    }
                                }
                            ]
                        },
                        */

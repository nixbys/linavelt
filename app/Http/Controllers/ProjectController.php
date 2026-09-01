<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Auth::user()
            ->projects()
            ->latest('updated_at')
            ->get();

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        $technologies = config('technologies');

        return view('projects.create-wizard', compact('technologies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:120'],
            'type'         => ['required', 'string'],
            'language'     => ['required', 'string'],
            'framework'    => ['nullable', 'string'],
            'integrations' => ['nullable', 'array'],
            'integrations.*' => ['string'],
        ]);

        $project = Auth::user()->projects()->create([
            'name'         => $validated['name'],
            'type'         => $validated['type'],
            'language'     => $validated['language'],
            'framework'    => $validated['framework'] ?? null,
            'integrations' => $validated['integrations'] ?? [],
            'status'       => 'draft',
        ]);

        return redirect()->route('projects.canvas', $project);
    }

    public function show(Project $project): View
    {
        $this->gate($project);

        return view('projects.show', compact('project'));
    }

    public function canvas(Project $project): View
    {
        $this->gate($project);

        return view('projects.canvas', compact('project'));
    }

    public function data(Project $project): JsonResponse
    {
        $this->gate($project);

        return response()->json(['project_data' => $project->project_data]);
    }

    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id'   => ['required', 'integer'],
            'project_data' => ['required', 'array'],
            'html'         => ['nullable', 'string', 'max:5000000'],
            'css'          => ['nullable', 'string', 'max:500000'],
        ]);

        $project = Project::where('id', $validated['project_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $project->update([
            'project_data' => $validated['project_data'],
            'html'         => $validated['html'] ?? null,
            'css'          => $validated['css'] ?? null,
        ]);

        return response()->json(['status' => 'saved', 'project_id' => $project->id]);
    }

    public function publish(Project $project): RedirectResponse
    {
        $this->gate($project);

        $project->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('status', "\"{$project->name}\" has been published.");
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->gate($project);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('status', 'Project deleted.');
    }

    private function gate(Project $project): void
    {
        abort_unless($project->user_id === Auth::id(), 403);
    }
}

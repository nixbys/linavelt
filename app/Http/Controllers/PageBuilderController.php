<?php

namespace App\Http\Controllers;

use App\Models\PageDesign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PageBuilderController extends Controller
{
    public function index(): View
    {
        $designs = Auth::user()->pageDesigns()->latest()->get();

        return view('builder.index', compact('designs'));
    }

    public function create(): View
    {
        return view('builder.canvas', ['design' => null]);
    }

    public function edit(PageDesign $design): View
    {
        $this->authorizeOwnership($design);

        return view('builder.canvas', compact('design'));
    }

    public function data(PageDesign $design): JsonResponse
    {
        $this->authorizeOwnership($design);

        return response()->json(['project_data' => $design->project_data]);
    }

    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'design_id'    => ['nullable', 'integer'],
            'project_data' => ['required', 'array'],
            'html'         => ['nullable', 'string'],
            'css'          => ['nullable', 'string'],
        ]);

        $user = Auth::user();

        if ($validated['design_id']) {
            $design = PageDesign::where('id', $validated['design_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
        } else {
            $design = new PageDesign(['user_id' => $user->id]);
        }

        $design->project_data = $validated['project_data'];
        $design->html = $validated['html'] ?? null;
        $design->css  = $validated['css'] ?? null;
        $design->save();

        return response()->json(['design_id' => $design->id, 'status' => 'saved']);
    }

    public function publish(PageDesign $design): RedirectResponse
    {
        $this->authorizeOwnership($design);

        $design->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        return redirect()->route('builder.designs.index')
            ->with('status', "\"{$design->name}\" has been published.");
    }

    public function destroy(PageDesign $design): RedirectResponse
    {
        $this->authorizeOwnership($design);
        $design->delete();

        return redirect()->route('builder.designs.index')
            ->with('status', 'Design deleted.');
    }

    private function authorizeOwnership(PageDesign $design): void
    {
        abort_unless($design->user_id === Auth::id(), 403);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventTheme;
use Illuminate\Http\Request;

class EventThemeController extends Controller
{
    private function validationRules(): array
    {
        return [
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string|max:255',
            'emoji'            => 'nullable|string|max:10',
            'start_month'      => 'required|integer|between:1,12',
            'start_day'        => 'required|integer|between:1,31',
            'end_month'        => 'required|integer|between:1,12',
            'end_day'          => 'required|integer|between:1,31',
            'primary_color'    => 'required|string|max:20',
            'secondary_color'  => 'required|string|max:20',
            'accent_color'     => 'required|string|max:20',
            'css_class'        => 'required|string|max:60|regex:/^[a-z0-9\-]+$/',
            'banner_icon'      => 'nullable|string|max:60',
            'banner_text'      => 'nullable|string|max:200',
            'banner_subtext'   => 'nullable|string|max:200',
            'priority'         => 'required|integer|min:0|max:999',
            'show_countdown'   => 'nullable|boolean',
            'countdown_label'  => 'nullable|string|max:100',
            'countdown_month'  => 'nullable|integer|between:1,12',
            'countdown_day'    => 'nullable|integer|between:1,31',
            'is_active'        => 'nullable|boolean',
        ];
    }

    public function index()
    {
        $themes = EventTheme::orderByDesc('priority')->orderBy('start_month')->orderBy('start_day')->get();
        $active = EventTheme::getActiveSafe();
        return view('admin.event-themes.index', compact('themes', 'active'));
    }

    public function create()
    {
        return view('admin.event-themes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());
        $data['is_active']      = $request->boolean('is_active');
        $data['show_countdown'] = $request->boolean('show_countdown');

        EventTheme::create($data);
        EventTheme::flushCache();

        return redirect()->route('admin.event-themes.index')
            ->with('success', 'Tema event "' . $data['name'] . '" berhasil ditambahkan!');
    }

    public function edit(EventTheme $eventTheme)
    {
        return view('admin.event-themes.edit', compact('eventTheme'));
    }

    public function update(Request $request, EventTheme $eventTheme)
    {
        $rules = $this->validationRules();
        // css_class tidak boleh diubah setelah dibuat
        unset($rules['css_class']);

        $data = $request->validate($rules);
        $data['is_active']      = $request->boolean('is_active');
        $data['show_countdown'] = $request->boolean('show_countdown');

        $eventTheme->update($data);
        EventTheme::flushCache();

        return redirect()->route('admin.event-themes.index')
            ->with('success', 'Tema "' . $eventTheme->name . '" berhasil diperbarui!');
    }

    public function destroy(EventTheme $eventTheme)
    {
        $name = $eventTheme->name;
        $eventTheme->delete();
        EventTheme::flushCache();

        return back()->with('success', 'Tema "' . $name . '" berhasil dihapus.');
    }

    public function toggle(EventTheme $eventTheme)
    {
        $eventTheme->update(['is_active' => ! $eventTheme->is_active]);
        EventTheme::flushCache();

        $status = $eventTheme->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', 'Tema "' . $eventTheme->name . '" berhasil ' . $status . '.');
    }

    public function simulator()
    {
        $themes = EventTheme::where('is_active', true)->orderByDesc('priority')->get();
        return view('admin.event-themes.simulator', compact('themes'));
    }

    public function simulatorPreview(Request $request)
    {
        $dateStr = $request->input('date', now()->format('Y-m-d'));

        try {
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $dateStr)->setTimezone('Asia/Makassar');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Tanggal tidak valid'], 422);
        }

        $month = (int) $date->format('n');
        $day   = (int) $date->format('j');

        $themes = EventTheme::where('is_active', true)
            ->orderByDesc('priority')
            ->get();

        $matched = null;
        foreach ($themes as $theme) {
            if (EventTheme::isInRange($month, $day, $theme->start_month, $theme->start_day, $theme->end_month, $theme->end_day)) {
                $matched = $theme;
                break;
            }
        }

        return response()->json([
            'date'    => $date->isoFormat('dddd, D MMMM YYYY'),
            'matched' => $matched ? [
                'name'            => $matched->name,
                'emoji'           => $matched->emoji,
                'primary_color'   => $matched->primary_color,
                'secondary_color' => $matched->secondary_color,
                'accent_color'    => $matched->accent_color,
                'banner_text'     => $matched->banner_text,
                'banner_subtext'  => $matched->banner_subtext,
                'banner_icon'     => $matched->banner_icon,
                'priority'        => $matched->priority,
                'css_class'       => $matched->css_class,
                'show_countdown'  => $matched->show_countdown,
                'countdown_label' => $matched->countdown_label,
            ] : null,
        ]);
    }
}

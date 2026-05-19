<?php

namespace App\Http\Controllers;

use App\Services\LogFileReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogViewerController extends Controller
{
    public function __construct(
        private readonly LogFileReader $logFiles
    ) {}

    public function index(): View
    {
        return view('dashboard.logs', [
            'files' => $this->logFiles->listFiles(),
        ]);
    }

    public function show(Request $request, string $file): View
    {
        $search = $request->string('search')->trim()->toString();
        $search = $search !== '' ? $search : null;

        $result = $this->logFiles->read($file, $search);

        return view('dashboard.log-show', [
            'filename' => $file,
            'content' => $result['content'],
            'lines' => $result['lines'],
            'truncated' => $result['truncated'],
            'matchCount' => $result['match_count'],
            'search' => $search ?? '',
        ]);
    }

    public function clear(string $file): RedirectResponse
    {
        $this->logFiles->clear($file);

        return redirect()
            ->route('dashboard.logs.show', ['file' => $file])
            ->with('status', "Log file \"{$file}\" has been cleared.");
    }
}

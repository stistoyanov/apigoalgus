<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use Symfony\Component\Finder\SplFileInfo;

class LogFileReader
{
    public function logsPath(): string
    {
        return storage_path('logs');
    }

    /**
     * @return list<array{name: string, size: int, modified: int}>
     */
    public function listFiles(): array
    {
        if (! File::isDirectory($this->logsPath())) {
            return [];
        }

        $files = [];

        foreach (File::files($this->logsPath()) as $file) {
            if (! $file instanceof SplFileInfo || ! str_ends_with($file->getFilename(), '.log')) {
                continue;
            }

            $files[] = [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'modified' => $file->getMTime(),
            ];
        }

        usort($files, fn (array $a, array $b) => $b['modified'] <=> $a['modified']);

        return $files;
    }

    public function resolvePath(string $filename): string
    {
        if (! preg_match('/^[a-zA-Z0-9._-]+\.log$/', $filename)) {
            throw new InvalidArgumentException('Invalid log file name.');
        }

        $path = $this->logsPath().DIRECTORY_SEPARATOR.$filename;

        if (! File::exists($path) || ! File::isFile($path)) {
            throw new InvalidArgumentException('Log file not found.');
        }

        return $path;
    }

    /**
     * @return array{content: string, lines: int, truncated: bool, match_count: int|null}
     */
    public function read(string $filename, ?string $search = null, int $maxLines = 500): array
    {
        $path = $this->resolvePath($filename);
        $allLines = file($path, FILE_IGNORE_NEW_LINES) ?: [];

        if ($search !== null && $search !== '') {
            $needle = mb_strtolower($search);
            $allLines = array_values(array_filter(
                $allLines,
                fn (string $line) => str_contains(mb_strtolower($line), $needle)
            ));
        }

        $totalLines = count($allLines);
        $truncated = $totalLines > $maxLines;

        if ($truncated) {
            $allLines = array_slice($allLines, -$maxLines);
        }

        return [
            'content' => implode(PHP_EOL, $allLines),
            'lines' => $totalLines,
            'truncated' => $truncated,
            'match_count' => $search ? $totalLines : null,
        ];
    }

    public function clear(string $filename): void
    {
        $path = $this->resolvePath($filename);
        File::put($path, '');
    }
}

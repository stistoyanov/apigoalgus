@extends('layouts.dashboard')

@section('title', 'Logs')

@section('content')
    <div class="panel">
        <h1>Log files</h1>
        <p class="lead">Laravel logs in <code>storage/logs</code></p>

        @if (empty($files))
            <p>No log files found.</p>
        @else
            <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Size</th>
                        <th>Modified</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($files as $file)
                        <tr>
                            <td><code>{{ $file['name'] }}</code></td>
                            <td>{{ number_format($file['size'] / 1024, 1) }} KB</td>
                            <td>{{ \Carbon\Carbon::createFromTimestamp($file['modified'])->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <a href="{{ route('dashboard.logs.show', $file['name']) }}" class="btn btn-outline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
@endsection

@extends('backend.layouts.main')

@section('main-content')
    <div class="container-xl">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Backup-Übersicht</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('verwaltung.seo-table-manager.backup.run') }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-primary">Backup manuell starten</button>
                </form>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Datei</th>
                            <th>Datum</th>
                            <th>Größe</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($backups as $backup)
                        <tr>
                            <td>{{ basename($backup['path']) }}</td>
                            <td>{{ $backup['date'] }}</td>
                            <td>{{ $backup['size'] }}</td>
                            <td>
                                <a href="{{ route('verwaltung.seo-table-manager.backup.download', $backup['path']) }}" class="btn btn-sm btn-success">Download</a>
                                <form action="{{ route('verwaltung.seo-table-manager.backup.delete', $backup['path']) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Backup wirklich löschen?')">Löschen</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Keine Backups verfügbar.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@extends('raadmin.layout.master')

@section('main-content')
<div class="container-xl">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Header Content Management</h3>
            <div class="ms-auto">
                <a href="{{ route('verwaltung.site-manager.header_contents.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Add New Header Content
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-vcenter table-hover card-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Background Image</th>
                            <th>Main Image</th>
                            <th>Title</th>
                            <th>Main Text</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($headerContents as $content)
                            <tr>
                                <td>{{ $content->id }}</td>
                                <td>
                                    @php
                                        $bgImgPath = null;
                                        if (Storage::exists($content->bg_img)) {
                                            $bgImgPath = Storage::url($content->bg_img);
                                        } elseif (file_exists(public_path($content->bg_img))) {
                                            $bgImgPath = asset($content->bg_img);
                                        }
                                    @endphp
                                    @if ($bgImgPath)
                                        <img src="{{ $bgImgPath }}" alt="Background Image" class="img-fluid rounded" style="max-width: 100px;">
                                    @else
                                        <span class="text-muted">–</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $mainImgPath = null;
                                        if (Storage::exists($content->main_img)) {
                                            $mainImgPath = Storage::url($content->main_img);
                                        } elseif (file_exists(public_path($content->main_img))) {
                                            $mainImgPath = asset($content->main_img);
                                        }
                                    @endphp
                                    @if ($mainImgPath)
                                        <img src="{{ $mainImgPath }}" alt="Main Image" class="img-fluid rounded" style="max-width: 100px;">
                                    @else
                                        <span class="text-muted">–</span>
                                    @endif
                                </td>
                                <td>{{ $content->title ?? '–' }}</td>
                                <td style="max-width: 300px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                                    {!! $content->main_text !!}
                                </td>
                                <td class="text-end">
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('verwaltung.site-manager.header_contents.edit', $content->id) }}" class="btn btn-sm btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                            Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ route('verwaltung.site-manager.header_contents.destroy', $content->id) }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No header content found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Toast für Flash-Nachrichten
        @if(session('toast'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: '{{ session('toast.type') }}',
                title: '{{ session('toast.message') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal2-tabler-toast'
                }
            });
        @endif

        // Bestätigungsdialog für Löschen
        function confirmDelete(url) {
            Swal.fire({
                title: 'Sind Sie sicher?',
                text: 'Dieser Header-Inhalt wird dauerhaft gelöscht. Dies kann nicht rückgängig gemacht werden.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#206bc4', // Tabler Primary
                cancelButtonColor: '#d63939', // Tabler Danger
                confirmButtonText: 'Ja, löschen!',
                cancelButtonText: 'Abbrechen'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
@endsection

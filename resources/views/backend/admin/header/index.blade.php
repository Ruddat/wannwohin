@extends('backend.layouts.main')

@section('main-content')
<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Header Content Management</h3>
            <div class="ms-auto">
                <a href="{{ route('verwaltung.header-manager.header_contents.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Add New Header Content
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Background Image</th>
                            <th>Main Image</th>
                            <th>Main Text</th>
                            <th>Title</th>
                            <th>Actions</th>
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
                                            $bgImgPath = Storage::url($content->bg_img); // Bild aus Storage
                                        } elseif (file_exists(public_path($content->bg_img))) {
                                            $bgImgPath = asset($content->bg_img); // Bild aus Public
                                        }
                                    @endphp

                                    @if ($bgImgPath)
                                        <img src="{{ $bgImgPath }}" alt="Background Image" class="img-thumbnail" style="width: 100px;">
                                    @else
                                        <span class="text-danger">No Image Found</span>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $mainImgPath = null;
                                        if (Storage::exists($content->main_img)) {
                                            $mainImgPath = Storage::url($content->main_img); // Bild aus Storage
                                        } elseif (file_exists(public_path($content->main_img))) {
                                            $mainImgPath = asset($content->main_img); // Bild aus Public
                                        }
                                    @endphp

                                    @if ($mainImgPath)
                                        <img src="{{ $mainImgPath }}" alt="Main Image" class="img-thumbnail" style="width: 100px;">
                                    @else
                                        <span class="text-danger">No Image Found</span>
                                    @endif
                                </td>
                                <td style="max-width: 300px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                                    {!! $content->main_text !!}
                                </td>
                                <td>{{ $content->title ?? '-' }}</td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('verwaltung.header-manager.header_contents.edit', $content->id) }}" class="btn btn-warning btn-sm">
                                            <i class="ti ti-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('verwaltung.header-manager.header_contents.destroy', $content->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ route('verwaltung.header-manager.header_contents.destroy', $content->id) }}')">
                                                <i class="ti ti-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No header content found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this header content? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    function confirmDelete(url) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = url;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>





@endsection

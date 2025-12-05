@extends('raadmin.layout.master')

@section('main-content')
<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Header Content</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('verwaltung.site-manager.header_contents.update', $headerContent->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Current Background Image</label>
                    <div class="mb-2">
                        @php
                            $bgImgPath = null;
                            if (Storage::exists($headerContent->bg_img)) {
                                $bgImgPath = Storage::url($headerContent->bg_img);
                            } elseif (file_exists(public_path($headerContent->bg_img))) {
                                $bgImgPath = asset($headerContent->bg_img);
                            }
                        @endphp
                        @if ($bgImgPath)
                            <img src="{{ $bgImgPath }}" class="img-thumbnail" style="width: 150px;">
                        @else
                            <span class="text-danger">No Background Image Found</span>
                        @endif
                    </div>
                    <label class="form-label">New Background Image (Optional)</label>
                    <input type="file" name="bg_img" class="form-control @error('bg_img') is-invalid @enderror">
                    @error('bg_img')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Main Image</label>
                    <div class="mb-2">
                        @php
                            $mainImgPath = null;
                            if (Storage::exists($headerContent->main_img)) {
                                $mainImgPath = Storage::url($headerContent->main_img);
                            } elseif (file_exists(public_path($headerContent->main_img))) {
                                $mainImgPath = asset($headerContent->main_img);
                            }
                        @endphp
                        @if ($mainImgPath)
                            <img src="{{ $mainImgPath }}" class="img-thumbnail" style="width: 150px;">
                        @else
                            <span class="text-danger">No Main Image Found</span>
                        @endif
                    </div>
                    <label class="form-label">New Main Image (Optional)</label>
                    <input type="file" name="main_img" class="form-control @error('main_img') is-invalid @enderror">
                    @error('main_img')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Main Text</label>
                    <textarea name="main_text" id="editor" class="form-control @error('main_text') is-invalid @enderror" rows="4">{{ $headerContent->main_text }}</textarea>
                    @error('main_text')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Title (Optional)</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ $headerContent->title }}">
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
<div class="mb-3">
    <label class="form-label">
        Slug <span class="text-danger">*</span>
        <span
            class="text-muted ms-1"
            style="cursor: help;"
            data-bs-toggle="tooltip"
            data-bs-placement="right"
            title="Ein Slug ist der URL-Name wie 'explore-relax-now'. Er bestimmt, unter welcher Adresse dieser Header angezeigt wird."
        >
            (?)
        </span>
    </label>

    <select name="slug" class="form-select @error('slug') is-invalid @enderror" required>
        <option value="" disabled>Wähle einen Slug</option>

        @php
            $slugs = [
                'explore',
                'explore-relax-now',
                'explore-relax-month',
                'explore-relax-later',
                'explore-adventure-now',
                'explore-adventure-month',
                'explore-adventure-later',
                'explore-culture-now',
                'explore-culture-month',
                'explore-culture-later',
                'explore-amusement-now',
                'explore-amusement-month',
                'explore-amusement-later',
                'startpage-1',
                'explore-trips'
            ];
        @endphp

        @foreach ($slugs as $slug)
            <option value="{{ $slug }}" {{ $headerContent->slug === $slug ? 'selected' : '' }}>
                {{ $slug }}
            </option>
        @endforeach
    </select>

    <small class="form-hint text-muted mt-1 d-block">
        <strong>Hinweis:</strong> Der Slug ist der technische Name der Seite (z. B. <code>explore-relax-now</code>).
        Er erscheint in der URL und steuert, auf welcher Seite der Header angezeigt wird.
    </small>

    @error('slug')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('verwaltung.site-manager.header_contents.index') }}" class="btn btn-secondary me-2">
                        <i class="ti ti-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [
                'heading', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'
            ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            }
        })
        .then(editor => {
            console.log('CKEditor initialized:', editor);
        })
        .catch(error => {
            console.error('CKEditor error:', error);
        });
});
    </script>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>

@endpush

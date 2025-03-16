@extends('backend.layouts.main')

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
                                $bgImgPath = Storage::url($headerContent->bg_img); // Bild aus Storage
                            } elseif (file_exists(public_path($headerContent->bg_img))) {
                                $bgImgPath = asset($headerContent->bg_img); // Bild aus Public
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
                                $mainImgPath = Storage::url($headerContent->main_img); // Bild aus Storage
                            } elseif (file_exists(public_path($headerContent->main_img))) {
                                $mainImgPath = asset($headerContent->main_img); // Bild aus Public
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
    <!-- CKEditor CDN -->
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
@endpush

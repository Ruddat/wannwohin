@extends('backend.layouts.main')

@section('main-content')
<div class="container-xl">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create New Header Content</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('verwaltung.site-manager.header_contents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Background Image</label>
                    <input type="file" name="bg_img" class="form-control @error('bg_img') is-invalid @enderror">
                    @error('bg_img')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Main Image</label>
                    <input type="file" name="main_img" class="form-control @error('main_img') is-invalid @enderror">
                    @error('main_img')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Main Text</label>
                    <textarea name="main_text" id="editor" class="form-control @error('main_text') is-invalid @enderror" rows="4"></textarea>
                    @error('main_text')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Title (Optional)</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror">
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                    <select name="slug" class="form-select @error('slug') is-invalid @enderror" required>
                        <option value="" disabled selected>Wähle einen Slug</option>
                        <option value="explore">explore</option>
                        <option value="explore-relax-now">explore-relax-now</option>
                        <option value="explore-relax-month">explore-relax-month</option>
                        <option value="explore-relax-later">explore-relax-later</option>
                        <option value="explore-adventure-now">explore-adventure-now</option>
                        <option value="explore-adventure-month">explore-adventure-month</option>
                        <option value="explore-adventure-later">explore-adventure-later</option>
                        <option value="explore-culture-now">explore-culture-now</option>
                        <option value="explore-culture-month">explore-culture-month</option>
                        <option value="explore-culture-later">explore-culture-later</option>
                        <option value="explore-amusement-now">explore-amusement-now</option>
                        <option value="explore-amusement-month">explore-amusement-month</option>
                        <option value="explore-amusement-later">explore-amusement-later</option>
                        <option value="startpage-1">startpage-1</option> <!-- Für andere Seiten -->
                    </select>
                    <small class="form-hint">Wähle einen Slug, der zur Seite passt (z. B. für Explore-Seite).</small>
                    @error('slug')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>


                <div class="d-flex justify-content-end">
                    <a href="{{ route('verwaltung.site-manager.header_contents.index') }}" class="btn btn-secondary me-2">
                        <i class="ti ti-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check"></i> Save
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
@endpush

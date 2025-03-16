<!-- resources/views/partials/blog.blade.php -->
<section id="blog" class="section section-no-border bg-color-light m-0">
    <div class="container">
        <div class="row">
            <div class="col">
                <h2 class="text-color-quaternary font-weight-extra-bold text-uppercase">My Blog</h2>

                @php
                    // Dummy-Daten als Platzhalter
                    $blog_posts = $blog_posts ?? [
                        [
                            'title' => 'Design Driven',
                            'image_url' => 'assets/img/blog/blog-1.jpg',
                            'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
                            'content' => 'Lorem ipsum dolor sit amet...',
                            'slug' => 'design-driven',
                            'created_at' => '2021-07-12',
                        ],
                        [
                            'title' => 'UI, UX Concepts',
                            'image_url' => 'assets/img/blog/blog-2.jpg',
                            'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
                            'content' => 'Lorem ipsum dolor sit amet...',
                            'slug' => 'ui-ux-concepts',
                            'created_at' => '2021-07-12',
                        ],
                    ];
                @endphp

                @if ($blog_posts && count($blog_posts) > 0)
                    <div class="row">
                        @foreach ($blog_posts as $index => $post)
                            <div class="col-lg-6 mb-5 mb-lg-0">
                                <article class="thumb-info custom-thumb-info-2 custom-box-shadow-1 appear-animation"
                                         data-aos="fade-in"
                                         data-aos-delay="{{ $index * 200 }}"
                                         data-aos-duration="1000">
                                    <div class="thumb-info-wrapper">
                                        <a href="{{ asset($post['image_url']) }}" class="glightbox"
                                           data-gallery="blog-gallery"
                                           data-title="{{ $post['title'] }}">
                                            <img src="{{ asset($post['image_url']) }}" alt="{{ $post['title'] }}" class="img-fluid" />
                                        </a>
                                    </div>
                                    <div class="thumb-info-caption">
                                        <div class="thumb-info-caption-text">
                                            <h4 class="mb-2">
                                                <a href="/blog/{{ $post['slug'] }}"
                                                   class="text-decoration-none text-color-dark font-weight-semibold">
                                                    {{ $post['title'] }}
                                                </a>
                                            </h4>
                                            <p class="custom-text-color-2">
                                                {{ Str::limit($post['excerpt'] ?? $post['content'], 100) }}
                                            </p>
                                        </div>
                                        <hr class="solid m-0 mt-4 mb-2">
                                        <div class="row justify-content-between">
                                            <div class="col-auto custom-blog-post-date text-uppercase font-weight-semibold text-color-dark text-2">
                                                {{ \Carbon\Carbon::parse($post['created_at'])->format('F j, Y') }}
                                            </div>
                                            <div class="col-auto custom-blog-post-share text-uppercase font-weight-semibold text-color-dark text-2">
                                                Share:
                                                <ul class="mb-0 d-inline-flex list-unstyled gap-2">
                                                    <li>
                                                        <a class="item-facebook text-decoration-none text-uppercase"
                                                           href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/blog/' . $post['slug'])) }}"
                                                           target="_blank"
                                                           title="Share on Facebook">Facebook</a>
                                                    </li>
                                                    <li>
                                                        <a class="item-twitter text-decoration-none text-uppercase"
                                                           href="https://twitter.com/intent/tweet?url={{ urlencode(url('/blog/' . $post['slug'])) }}&text={{ urlencode($post['title']) }}"
                                                           target="_blank"
                                                           title="Share on Twitter">Twitter</a>
                                                    </li>
                                                    <li>
                                                        <a class="item-linkedin text-decoration-none text-uppercase"
                                                           href="https://www.linkedin.com/shareArticle?url={{ urlencode(url('/blog/' . $post['slug'])) }}&title={{ urlencode($post['title']) }}"
                                                           target="_blank"
                                                           title="Share on LinkedIn">LinkedIn</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <p>Es sind derzeit keine Blogposts verfügbar.</p>
                    </div>
                @endif

                <div class="col-12 pt-4 mt-4 text-center">
                    <a href="/blog"
                       class="btn btn-primary btn-outline custom-btn-style-2 font-weight-bold custom-border-radius-1 text-uppercase">
                        View All
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@if(isset($articleSection) && $articleSection->is_active && isset($articles) && $articles->total() > 0)
    <section class="articles-section" id="articles">
        <div class="container">
            @if($articleSection->eyebrow)
                <p class="articles-eyebrow"><span></span>{{ $articleSection->eyebrow }}</p>
            @endif
            @if($articleSection->heading)<h2>{{ $articleSection->heading }}</h2>@endif

            <div class="articles-grid">
                @foreach($articles as $article)
                    <article class="article-card">
                        <div class="article-media">
                            @if($article->image)<img src="{{ asset($article->image) }}" alt="{{ $article->title }}" loading="lazy">@endif
                        </div>
                        <div class="article-body">
                            <p class="article-meta">
                                <strong>{{ $article->category }}</strong><i></i>
                                <time datetime="{{ $article->published_on->toDateString() }}">{{ $article->published_on->format('d M Y') }}</time>
                            </p>
                            <h3>{{ $article->title }}</h3>
                            <p class="article-desc">{{ $article->description }}</p>
                            <a class="article-more" href="{{ route('insights.show', $article) }}" aria-label="Read article: {{ $article->title }}">Read article <span aria-hidden="true">&rarr;</span></a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="articles-bar">
                <p class="articles-count">Showing {{ $articles->firstItem() }}&ndash;{{ $articles->lastItem() }} of {{ $articles->total() }} article{{ $articles->total() === 1 ? '' : 's' }}</p>

                @if($articles->hasPages())
                    @php
                        $last = $articles->lastPage();
                        $now = $articles->currentPage();
                        // first page, last page, and the pages next to the current one, with "..." for the gaps
                        $shown = collect([1, $last, $now - 1, $now, $now + 1])->filter(fn ($p) => $p >= 1 && $p <= $last)->unique()->sort()->values();
                    @endphp
                    <nav class="articles-pages" aria-label="Pagination">
                        @if($articles->onFirstPage())
                            <span class="is-disabled is-edge" aria-disabled="true">&larr; Prev</span>
                        @else
                            <a class="is-edge" href="{{ $articles->previousPageUrl() }}" rel="prev">&larr; Prev</a>
                        @endif

                        @foreach($shown as $idx => $page)
                            @if($idx > 0 && $page - $shown[$idx - 1] > 1)<span class="is-gap" aria-hidden="true">&hellip;</span>@endif
                            @if($page === $now)
                                <span class="is-current" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $articles->url($page) }}" aria-label="Page {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($articles->hasMorePages())
                            <a class="is-edge" href="{{ $articles->nextPageUrl() }}" rel="next">Next &rarr;</a>
                        @else
                            <span class="is-disabled is-edge" aria-disabled="true">Next &rarr;</span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </section>
@endif

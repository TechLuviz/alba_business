@extends('admin.layout')

@section('title', 'Newsletter')
@section('page_title', 'Newsletter Subscribers')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Site Settings <span>&rsaquo;</span> Newsletter @endsection

@section('content')
    @php
        $sortLink = fn ($key) => route('admin.newsletter.index', array_merge(request()->except('page'), ['sort' => $key, 'dir' => ($sort === $key && $dir === 'asc') ? 'desc' : 'asc']));
        $arrow = fn ($key) => $sort === $key ? ($dir === 'asc' ? ' ▲' : ' ▼') : '';
        $hasFilter = $q !== '';
    @endphp
    <section class="card panel">
        <div class="panel-head">
            <h2>Subscribers ({{ $subscribers->total() }})</h2>
            <div class="panel-tools">
                @if($subscribers->total() > 0 || $hasFilter)
                    <a href="{{ route('admin.newsletter.export') }}" class="btn red">Download CSV</a>
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('admin.newsletter.index') }}" class="dt-bar">
            <input type="hidden" name="sort" value="{{ $sort }}"><input type="hidden" name="dir" value="{{ $dir }}">
            <label class="dt-len">Show
                <select name="per_page" onchange="this.form.submit()">
                    @foreach([10, 25, 50, 100] as $n)<option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>@endforeach
                    <option value="all" @selected($perPage === 'all')>All</option>
                </select> entries
            </label>
            <div class="dt-search">
                <input type="search" name="q" value="{{ $q }}" maxlength="100" placeholder="Search email..." aria-label="Search subscribers">
                <button type="submit" class="btn small">Search</button>
                @if($hasFilter)<a href="{{ route('admin.newsletter.index', ['per_page' => $perPage]) }}" class="dt-clear">Clear</a>@endif
            </div>
        </form>

        <form method="POST" action="{{ route('admin.newsletter.bulk-destroy') }}" id="bulkForm" data-confirm="Delete selected subscribers?" data-noun="subscriber" data-nouns="subscribers" class="dt-bulk" hidden>
            @csrf @method('DELETE')
            <span id="bulkCount">0 selected</span>
            <button type="submit" class="btn red small">Delete selected</button>
        </form>

        <div style="overflow-x: auto;">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 36px;"><input type="checkbox" id="checkAll" aria-label="Select all subscribers on this page" @disabled($subscribers->isEmpty())></th>
                    <th>S.N.</th>
                    <th><a class="dt-sort" href="{{ $sortLink('email') }}">Email{{ $arrow('email') }}</a></th>
                    <th><a class="dt-sort" href="{{ $sortLink('subscribed') }}">Subscribed on{{ $arrow('subscribed') }}</a></th>
                    <th class="r">Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($subscribers as $i => $s)
                <tr>
                    <td><input type="checkbox" class="row-check" name="ids[]" value="{{ $s->id }}" form="bulkForm" aria-label="Select {{ $s->email }}"></td>
                    <td>{{ $subscribers->firstItem() + $i }}</td>
                    <td class="strong">{{ $s->email }}</td>
                    <td>{{ $s->created_at?->format('d M Y, H:i') }}</td>
                    <td class="r actions">
                        <form method="POST" action="{{ route('admin.newsletter.destroy', $s) }}" data-confirm="Remove this subscriber?">@csrf @method('DELETE')<button class="act del" title="Remove">@include('admin.partials.icon-trash')</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">{{ $hasFilter ? 'No subscribers match your search.' : 'No subscribers yet. Emails entered in the website footer appear here.' }}</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>

        @if($subscribers->total() > 0)
            <div class="dt-foot">
                <span class="dt-info">Showing {{ $subscribers->firstItem() }} to {{ $subscribers->lastItem() }} of {{ $subscribers->total() }} entries</span>
                @if($subscribers->hasPages())
                    @php
                        $cur = $subscribers->currentPage(); $last = $subscribers->lastPage();
                        $pages = collect([1, $last])->merge(range(max(1, $cur - 2), min($last, $cur + 2)))->unique()->sort()->values();
                    @endphp
                    <nav class="dt-pager" aria-label="Pagination">
                        @if($subscribers->onFirstPage())<span class="dis">&lsaquo; Prev</span>@else<a href="{{ $subscribers->previousPageUrl() }}">&lsaquo; Prev</a>@endif
                        @foreach($pages as $idx => $p)
                            @if($idx > 0 && $p - $pages[$idx - 1] > 1)<span class="gap">&hellip;</span>@endif
                            @if($p === $cur)<span class="cur" aria-current="page">{{ $p }}</span>@else<a href="{{ $subscribers->url($p) }}">{{ $p }}</a>@endif
                        @endforeach
                        @if($subscribers->hasMorePages())<a href="{{ $subscribers->nextPageUrl() }}">Next &rsaquo;</a>@else<span class="dis">Next &rsaquo;</span>@endif
                    </nav>
                @endif
            </div>
        @endif
    </section>

    @include('admin.partials.datatable-assets')
@endsection

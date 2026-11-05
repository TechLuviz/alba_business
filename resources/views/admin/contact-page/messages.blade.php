@extends('admin.layout')

@section('title', 'Enquiries')
@section('page_title', 'Contact Page - Enquiries')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Contact Page <span>&rsaquo;</span> Enquiries @endsection

@section('content')
    @php
        $sortLink = fn ($key) => route('admin.contactpage.messages.index', array_merge(request()->except('page'), ['sort' => $key, 'dir' => ($sort === $key && $dir === 'asc') ? 'desc' : 'asc']));
        $arrow = fn ($key) => $sort === $key ? ($dir === 'asc' ? ' ▲' : ' ▼') : '';
        $hasFilter = $q !== '';
    @endphp
    <section class="card panel">
        <div class="panel-head">
            <h2>Enquiries ({{ $messages->total() }})</h2>
            <div class="panel-tools">
                @if($messages->total() > 0 || $hasFilter)
                    <a href="{{ route('admin.contactpage.messages.export') }}" class="btn red">Download CSV</a>
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('admin.contactpage.messages.index') }}" class="dt-bar">
            <input type="hidden" name="sort" value="{{ $sort }}"><input type="hidden" name="dir" value="{{ $dir }}">
            <label class="dt-len">Show
                <select name="per_page" onchange="this.form.submit()">
                    @foreach([10, 25, 50, 100] as $n)<option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>@endforeach
                    <option value="all" @selected($perPage === 'all')>All</option>
                </select> entries
            </label>
            <div class="dt-search">
                <input type="search" name="q" value="{{ $q }}" maxlength="100" placeholder="Search name, email, phone, course..." aria-label="Search enquiries">
                <button type="submit" class="btn small">Search</button>
                @if($hasFilter)<a href="{{ route('admin.contactpage.messages.index', ['per_page' => $perPage]) }}" class="dt-clear">Clear</a>@endif
            </div>
        </form>

        <form method="POST" action="{{ route('admin.contactpage.messages.bulk-destroy') }}" id="bulkForm" data-confirm="Delete selected enquiries?" data-noun="enquiry" data-nouns="enquiries" class="dt-bulk" hidden>
            @csrf @method('DELETE')
            <span id="bulkCount">0 selected</span>
            <button type="submit" class="btn red small">Delete selected</button>
        </form>

        <div style="overflow-x: auto;">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 36px;"><input type="checkbox" id="checkAll" aria-label="Select all enquiries on this page" @disabled($messages->isEmpty())></th>
                    <th>S.N.</th>
                    <th><a class="dt-sort" href="{{ $sortLink('name') }}">Name{{ $arrow('name') }}</a></th>
                    <th><a class="dt-sort" href="{{ $sortLink('email') }}">Contact{{ $arrow('email') }}</a></th>
                    <th><a class="dt-sort" href="{{ $sortLink('course') }}">Course{{ $arrow('course') }}</a></th>
                    <th><a class="dt-sort" href="{{ $sortLink('received') }}">Received{{ $arrow('received') }}</a></th>
                    <th class="r">Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($messages as $i => $m)
                <tr>
                    <td><input type="checkbox" class="row-check" name="ids[]" value="{{ $m->id }}" form="bulkForm" aria-label="Select enquiry from {{ $m->name }}"></td>
                    <td>{{ $messages->firstItem() + $i }}</td>
                    <td class="strong ellipsis" style="max-width: 180px;">{{ $m->name }}</td>
                    <td>
                        <a href="mailto:{{ $m->email }}" class="ellipsis" style="display:block; max-width: 230px;">{{ $m->email }}</a>
                        <small style="color: var(--muted);">{{ $m->phone }}</small>
                    </td>
                    <td class="ellipsis" style="max-width: 200px;">{{ $m->course }}</td>
                    <td>{{ $m->created_at?->format('d M Y, H:i') }}</td>
                    <td class="r actions">
                        <button type="button" class="act" title="Read message" data-view="{{ json_encode(['type' => 'text', 'title' => $m->name . ' - ' . $m->course, 'desc' => $m->message . "\n\n" . $m->email . ' | ' . $m->phone]) }}">@include('admin.partials.icon-eye')</button>
                        <form method="POST" action="{{ route('admin.contactpage.messages.destroy', $m) }}" data-confirm="Delete this enquiry?">@csrf @method('DELETE')<button class="act del" title="Delete">@include('admin.partials.icon-trash')</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">{{ $hasFilter ? 'No enquiries match your search.' : 'No enquiries yet. Messages sent through the Contact page form appear here.' }}</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>

        @if($messages->total() > 0)
            <div class="dt-foot">
                <span class="dt-info">Showing {{ $messages->firstItem() }} to {{ $messages->lastItem() }} of {{ $messages->total() }} entries</span>
                @if($messages->hasPages())
                    @php
                        $cur = $messages->currentPage(); $last = $messages->lastPage();
                        $pages = collect([1, $last])->merge(range(max(1, $cur - 2), min($last, $cur + 2)))->unique()->sort()->values();
                    @endphp
                    <nav class="dt-pager" aria-label="Pagination">
                        @if($messages->onFirstPage())<span class="dis">&lsaquo; Prev</span>@else<a href="{{ $messages->previousPageUrl() }}">&lsaquo; Prev</a>@endif
                        @foreach($pages as $idx => $p)
                            @if($idx > 0 && $p - $pages[$idx - 1] > 1)<span class="gap">&hellip;</span>@endif
                            @if($p === $cur)<span class="cur" aria-current="page">{{ $p }}</span>@else<a href="{{ $messages->url($p) }}">{{ $p }}</a>@endif
                        @endforeach
                        @if($messages->hasMorePages())<a href="{{ $messages->nextPageUrl() }}">Next &rsaquo;</a>@else<span class="dis">Next &rsaquo;</span>@endif
                    </nav>
                @endif
            </div>
        @endif
    </section>

    @include('admin.partials.datatable-assets')

    <div class="modal" id="viewModal">
        <div class="modal-box">
            <button type="button" class="canvas-close" data-close-modal>&times;</button>
            <div id="viewMedia"></div>
            <h3 id="viewTitle"></h3>
            <p id="viewDesc"></p>
        </div>
    </div>
@endsection

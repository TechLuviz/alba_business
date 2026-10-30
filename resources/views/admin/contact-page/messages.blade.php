@extends('admin.layout')

@section('title', 'Enquiries')
@section('page_title', 'Contact Page - Enquiries')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Page Settings <span>&rsaquo;</span> Contact Page <span>&rsaquo;</span> Enquiries @endsection

@section('content')
    <section class="card panel">
        <div class="panel-head">
            <h2>Enquiries ({{ $messages->count() }})</h2>
            <div class="panel-tools">
                @if($messages->isNotEmpty())
                    <a href="{{ route('admin.contactpage.messages.export') }}" class="btn red">Download CSV</a>
                @endif
            </div>
        </div>

        <table class="tbl">
            <thead>
                <tr><th>S.N.</th><th>Name</th><th>Contact</th><th>Course</th><th>Received</th><th class="r">Action</th></tr>
            </thead>
            <tbody>
            @forelse($messages as $i => $m)
                <tr>
                    <td>{{ $i + 1 }}</td>
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
                <tr><td colspan="6" class="empty">No enquiries yet. Messages sent through the Contact page form appear here.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <div class="modal" id="viewModal">
        <div class="modal-box">
            <button type="button" class="canvas-close" data-close-modal>&times;</button>
            <div id="viewMedia"></div>
            <h3 id="viewTitle"></h3>
            <p id="viewDesc"></p>
        </div>
    </div>
@endsection

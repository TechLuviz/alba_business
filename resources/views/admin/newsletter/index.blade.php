@extends('admin.layout')

@section('title', 'Newsletter')
@section('page_title', 'Newsletter Subscribers')
@section('breadcrumbs')<a href="{{ route('admin.banners.index') }}">Home</a> <span>&rsaquo;</span> Site Settings <span>&rsaquo;</span> Newsletter @endsection

@section('content')
    <section class="card panel">
        <div class="panel-head">
            <h2>Subscribers ({{ $subscribers->count() }})</h2>
            <div class="panel-tools">
                @if($subscribers->isNotEmpty())
                    <a href="{{ route('admin.newsletter.export') }}" class="btn red">Download CSV</a>
                @endif
            </div>
        </div>

        <table class="tbl">
            <thead>
                <tr><th>S.N.</th><th>Email</th><th>Subscribed on</th><th class="r">Action</th></tr>
            </thead>
            <tbody>
            @forelse($subscribers as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="strong">{{ $s->email }}</td>
                    <td>{{ $s->created_at?->format('d M Y, H:i') }}</td>
                    <td class="r actions">
                        <form method="POST" action="{{ route('admin.newsletter.destroy', $s) }}" data-confirm="Remove this subscriber?">@csrf @method('DELETE')<button class="act del" title="Remove">@include('admin.partials.icon-trash')</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">No subscribers yet. Emails entered in the website footer appear here.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection

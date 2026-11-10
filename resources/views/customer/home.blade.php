@extends('layouts.app')
@section('title', 'Shops')

@section('content')
    <h1>Shops</h1>

    @if ($shops->isEmpty())
        <div class="card">No shops available yet.</div>
    @else
        <div class="grid">
            @foreach ($shops as $shop)
                <a class="product" href="{{ route('shops.show', $shop) }}" style="color:inherit">
                    <div class="body">
                        <h3>{{ $shop->name }}</h3>
                        <div class="muted">{{ $shop->description }}</div>
                        <div><span class="badge green">{{ $shop->products_count }} product(s)</span></div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection

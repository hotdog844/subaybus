@extends('layouts.admin')

@section('title', 'View Feedback')

@section('content')
    <style>
        .feedback-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 5px solid #3498db;
        }
        .feedback-card.status-read {
            border-left-color: #95a5a6;
            opacity: 0.8;
        }
        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            color: #555;
        }
        .feedback-subject {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
        }
        .feedback-body {
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        .feedback-actions {
            text-align: right;
        }
    </style>

    <h2>User Feedback & Messages</h2>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; text-align: center;">
            {{ session('success') }}
        </div>
    @endif

    <div class="feedback-container" style="margin-top: 2rem;">
        @forelse ($feedbackItems as $item)
            <div class="feedback-card status-{{ $item->status }}">
                <div class="feedback-header">
                    <div>
                        <strong>From:</strong> {{ $item->name }} ({{ $item->email }})
                    </div>
                    <div>
                        <strong>Received:</strong> {{ $item->created_at->format('M d, Y, h:i A') }}
                    </div>
                </div>
                <p class="feedback-subject">{{ $item->subject }}</p>
                <div class="feedback-body">
                    {{ $item->message }}
                </div>
                <div class="feedback-actions">
    @if($item->status == 'new')
        <form action="{{ route('admin.feedback.read', $item) }}" method="POST" style="display: inline-block;">
            @csrf
            @method('PATCH')
            <button type="submit" style="background: #2ecc71; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;">Mark as Read</button>
        </form>
    @endif
    <form action="{{ route('admin.feedback.destroy', $item) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this message?');">
    @csrf
    @method('DELETE')
    <button type="submit" style="background: #e74c3c; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;">Delete</button>
</form>
</div>
            </div>
        @empty
            <div class="feedback-card">
                <p style="text-align: center;">There are no feedback messages.</p>
            </div>
        @endforelse
    </div>
@endsection
@if (session('status'))
    <div class="notice notice--success" role="status"><x-icon name="check" /> {{ session('status') }}</div>
@endif
@if (session('error'))
    <div class="notice notice--error" role="alert"><x-icon name="circle-alert" /> {{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="notice notice--error" role="alert">
        <x-icon name="circle-alert" />
        <div>
            <strong>يرجى مراجعة الحقول التالية:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if ($isRootLevel && !$messages->count())
    <div class="alert alert-warning">Сообщений нет</div>
@endif

@foreach ($messages as $message)
    <div class="media mt-3 @if(!$isRootLevel) ml-3 @endif">
        <div class="media-body">
            <p>{{ $message->text }}</p>

            @can('update', $message)
                <a href="{{ action('GuestbookController@index', ['editId' => $message]) }}"
                   class="btn btn-outline-secondary">Изменить</a>
            @endcan
            @can('answer', $message)
                <a href="{{ action('GuestbookController@index', ['answerId' => $message]) }}"
                   class="btn btn-outline-secondary">Ответить</a>
            @endcan
            @if ($message->image)
                <a href="{{ $message->getImageUrl() }}" class="btn btn-outline-info">
                    <span class="d-block d-sm-none">Скачать</span>
                    <span class="d-none d-sm-block">Скачать изображение</span>
                </a>
            @endif

            @include('guestbook._list', ['messages' => $message->answers, 'isRootLevel' => false])
        </div>
    </div>
@endforeach

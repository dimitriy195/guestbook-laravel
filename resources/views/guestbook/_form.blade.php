@canany(['update', 'create'], $message)
    {!! Form::open([
    'id' => 'guestbook-message-form',
    'class' => 'mb-3',
    'url' => !$message->exists
    ? action('GuestbookController@store', ['answerId' => $answerMessage])
    : action('GuestbookController@update', ['message' => $message]),
    'method' => 'post',
    'enctype' => 'multipart/form-data'
    ]) !!}
    {!! Form::model($message) !!}

    <div id="guestbook-message-form-errors" class="alert alert-danger"
         style="@if(!$errors->any()) display: none @endif">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

    @if ($answerMessage)
        <div class="alert alert-secondary" role="alert">
            <div>
                <b>Ответ на комментарий:</b><br>
                <blockquote class="blockquote text-truncate">
                    {{$answerMessage->text}}
                </blockquote>
            </div>
        </div>
    @endif

    @if ($message->exists)
        <div class="alert alert-secondary" role="alert">
            <div>
                <b>Изменение комментария:</b><br>
                <blockquote class="blockquote text-truncate">
                    {{$message->text}}
                </blockquote>
            </div>
        </div>
    @endif

    <div class="form-group">
        {!! Form::textarea('text', null, ['placeholder' => 'Сообщение', 'class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::file('image', ['class' => 'form-control-file']) !!}
    </div>

    <button class="btn btn-primary" id="guestbook-message-form-button">Написать</button>
    {!! Form::close() !!}

    <script type="text/javascript">
		$(document).ready(() => {
                {{--    TODO Перенести класс в общий JS --}}
			class FormAjaxValidation {
				constructor($form, $formSubmitButton, $errorsContainer, validationUrl) {
					this.$form = $form;
					this.$formButton = $formSubmitButton;
					this.$errorsContainer = $errorsContainer;
					this.validationUrl = validationUrl;

					this.subscribeOnSubmit();
				}

				subscribeOnSubmit() {
					let context = this;
					this.$formButton.on('click', (e) => {
						e.preventDefault();

						let dataRaw = context.$form.serializeArray();
						let data = {};
						dataRaw.forEach(item => {
							data[item.name] = item.value;
						});

						$.ajax({
							url: context.validationUrl,
							type: 'POST',
							data: data,
						})
							.done((data) => {
								context.$form.submit();
								context.hideErrors();
							})
							.fail(data => {
								context.printErrors(data.responseJSON.errors);
							});
					});
				}

				printErrors(errors) {
					let context = this;
					this.$errorsContainer.html('');
					this.$errorsContainer.show();
					this.$errorsContainer.append('<ul></ul>');
					$.each(errors, function (key, value) {
						context.$errorsContainer.find("ul").append('<li>' + value[0] + '</li>');
					});
				}

				hideErrors() {
					this.$errorsContainer.hide();
				}
			}

			new FormAjaxValidation(
				$('#guestbook-message-form'),
				$('#guestbook-message-form-button'),
				$('#guestbook-message-form-errors'),
				'<?= action('GuestbookController@validateData') ?>'
			);
		});
    </script>
@else
    <div class="alert alert-warning">Что бы оставить сообщение, нужно авторизоваться</div>
@endcanany

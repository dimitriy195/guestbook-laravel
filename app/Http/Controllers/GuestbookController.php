<?php

namespace App\Http\Controllers;

use App\Facades\Guestbook;
use App\GuestbookMessage;
use App\Repositories\GuestbookMessagesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestbookController extends Controller
{
    protected $messagesRepository;


    public function __construct(GuestbookMessagesRepository $messagesRepository)
    {
        $this->messagesRepository = $messagesRepository;
    }

    public function index(Request $request)
    {
        $message = GuestbookMessage::find($request->get('editId')) ?? new GuestbookMessage();
        $answerMessage = GuestbookMessage::find($request->get('answerId'));
        $messages = GuestbookMessage::with('answers')->where('answer_id', null)->orderBy('id', 'desc')->paginate(5);

        return view('guestbook.index', compact('message', 'messages', 'answerMessage'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->validateFormData($request);

        $data = $request->only('text');
        $image = $request->file('image');
        $answerMessage = GuestbookMessage::find($request->get('answerId'));

        $this->messagesRepository->create(Auth::user(), $data, $image, $answerMessage);

        return redirect()->action('GuestbookController@index');
    }

    /**
     * @param Request $request
     * @param GuestbookMessage $guestbookMessage
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, GuestbookMessage $message)
    {
        $this->validateFormData($request);

        $data = $request->only('text');
        $image = $request->file('image');

        $this->messagesRepository->update(Auth::user(), $message, $data, $image);

        return redirect()->action('GuestbookController@index');
    }

    public function validateData(Request $request)
    {
        $this->validateFormData($request);
    }

    /**
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateFormData(Request $request): void
    {
        $this->validate($request, [
            'text' => 'required|string|max:1000',
            'image' => 'image|mimes:jpeg,png,jpg|max:100|dimensions:min_width=100,min_height=100',
        ]);
    }
}

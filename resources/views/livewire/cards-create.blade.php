<div class="container mt-5">
    <div class="container mt-5">
        <h3>New Cards</h3>
        <span>Edit title</span>
        @if($questions)
        <button class="float-right" wire:click="exportCSV">Export to CSV</button>
        @endif
        <button class="float-right">Archive card set</button>
        <button class="float-right">Create New card</button>
    </div>

    <div class="flex-row space-y-5 space-5 mt-5 mb-5">
        @foreach($questions as $key => $question)
        <div class="bg-white border border-gray-200 rounded-xl m-2">
            @if(!$question['edit'])
            <div style="">
                <div class="p-4">
                    <div class="d-flex flex-wrap items-center justify-between -mt-4 -ml-4 sm:flex-nowrap">
                        <div class="mt-4 ml-4">
                            <div class="d-flex items-center">
                                <h3 class="text-lg font-bold text-gray-700">
                                    {{ $key + 1 }}. {{ $question['title'] }}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <fieldset class="mt-4 mb-2">
                        <legend class="sr-only ">Choices</legend>
                        <div class="space-y-5">
                        @if(count($question['answers']) > 1)
                            @foreach($question['answers'] as $k => $answer)
                            <div class="relative d-flex items-start mt-1">
                                <div class="d-flex items-center mt-1">
                                    <input
                                        type="radio"
                                        id="{{ $question['id'] }}-{{$k}}"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 cursor-pointer focus:ring-indigo-500"
                                        value="{{$answer['title']}}"
                                        wire:model="questions.{{$key}}.answer"
                                    >
                                </div>
                                <div class="ml-3 cursor-pointer text-md">
                                    <label for="{{ $question['id'] }}-{{$k}}" class="font-medium text-gray-700">{{ $alphabet[$k] }})</label>
                                    <span class="text-gray-600">{{ $answer['title'] }}</span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <textarea wire:model="questions.{{$key}}.answer" rows="2" class="flex-1 block w-full min-w-0 text-gray-800 border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                            @if($question['check'])
                            <div class="mt-2 text-gray-200">
                                <span class="text-gray-600">
                                    The answer is:
                                    <span class="font-medium text-gray-500">
                                        {{ $this->getRightAnswer($key) }}
                                    </span>
                                </span>
                            </div>
                            @endif
                            <br>
                            <button type="button" wire:click="setBool({{$key}}, 'check', true)" class="inline-flex items-center px-4 py-2 mt-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Check Answer
                            </button>
                        @endif
                            @if($question['answer'] && (count($question['answers']) > 1 || $question['check']))
                                @if($question['answer'] == $this->getRightAnswer($key))
                                <div class="font-bold text-green-400">Correct!</div>
                                <div class="mt-2 italic text-gray-600">
                                    {{ $question['info'] }}
                                </div>
                                @else
                                <div class="font-bold text-red-400">Whoops!
                                    Try
                                    again
                                </div>
                                @endif
                            @endif
                        </div>
                    </fieldset>
                </div>
                <div class="d-flex justify-start space-x-1 text-gray-400 border-t border-gray-200 float-right" style="margin-top: -80px;">
                    <div class="d-flex justify-end p-4 space-x-2 grow">
                        <div>
                            <svg wire:click="setBool({{$key}}, 'edit', true)" class="w-5 ml-2 cursor-pointer hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </div>
                        <div>
                            <div class="relative">
                                <svg wire:click="setBool({{$key}}, 'delete', true)" class="w-5 ml-2 cursor-pointer hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
                @if($question['delete'])
                    <div class="absolute z-10 w-48 p-2 px-3 mt-2 mr-6 text-sm text-gray-600 bg-white border border-gray-200 rounded-md shadow-lg -right-5">
                        <div class="d-flex flex-col space-y-3">
                            <div>Are you sure you want to delete this question?</div>
                            <div class="d-flex justify-end space-x-2 font-semibold">
                                <button wire:click="setBool({{$key}}, 'delete', false)" class="mr-1 text-gray-500 hover:text-gray-700">Cancel</button>
                                <button wire:click="removeQuestion({{$key}})" class="text-red-500 hover:text-red-700">Delete</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @else
            <div>
                <div class="p-4">
                    <fieldset>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-3">
                                <h2 class="mb-2 text-lg font-bold">Edit
                                    {{--<span x-text="$humanize(question.type)">Multiple Choice</span>--}}
                                    Question
                                </h2>
                            </div>
                            <div class="col-span-3">
                                <label class="block text-sm font-semibold text-gray-800 cursor-pointer text-md" for="questionText">
                                    Question
                                </label>
                                <textarea
                                    placeholder="Type a question"
                                    class="flex-1 block w-full min-w-0 text-gray-800 border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 mt-1"
                                    rows="3"
                                    label="Question" wire:model="questions.{{$key}}.title"></textarea>
                            </div>
                            @foreach($question['answers'] as $k => $answer)
                            <div class="col-span-3">
                                <label for="answerText" class="block text-sm font-semibold text-gray-800">
                                    Answer {{ count($question['answers']) > 1 ? $alphabet[$k]: '' }}
                                </label>
                                <input
                                    class="flex-1 block w-full min-w-0 text-gray-800 border border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 mt-1"
                                    type="text"
                                    name="answerText"
                                    label="Answer"
                                    rows="2"
                                    wire:model="questions.{{$key}}.answers.{{$k}}.title">
                            </div>
                            @endforeach

                            @if(count($question['answers']) > 1)
                            <div class="col-span-3">
                                <label class="block text-sm font-semibold text-gray-800 cursor-pointer text-md" for="correctAnswer-{{$key}}">
                                    Correct Answer
                                </label>
                                <select id="correctAnswer-{{$key}}" wire:change="setRightAnswer({{$key}},$event.target.value)" class="mt-1 block w-full py-2 pl-3 pr-10 text-base text-gray-800 border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="" disabled style="display:none">Select Correct Answer</option>
                                    @foreach($question['answers'] as $k => $answer)
                                    <option value="{{$k}}" @if($answer['correct']) selected="selected" @endif>
                                        {{ $alphabet[$k] }} ({{ $answer['title'] }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-span-3">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-800 cursor-pointer text-md" for="answerInfo">
                                        Answer Info (optional)
                                    </label>
                                    <textarea
                                        placeholder=""
                                        class="flex-1 block w-full min-w-0 text-gray-800 border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 mt-1"
                                        rows="3"
                                        name="info"
                                        label="Answer Info"
                                        wire:model="questions.{{$key}}.info"
                                    ></textarea>
                                    <p class="mt-1 text-xs text-gray-500">
                                        This will be shown to the user after they answer the
                                        question correctly.
                                    </p>
                                </div>
                            </div>
                            <div class="col-span-3">
                                <div class="d-flex justify-end">
                                    <button class="px-5 py-2 text-md mt-1 ml-4" wire:click="editQuestion({{$key}})">
                                        Done
                                    </button>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="container mt-5 mb-5">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">From highlight</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">From text</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">From manual</a>
            </li>
        </ul>
        <form>
            <div class="form-group mt-4">
                <div class="row">
                    <div class="col">
                        <label for="chapter">Chapter</label>
                        <input
                            type="text"
                            id="chapter"
                            wire:model="chapter"
                            class="form-control @if($error && !$chapter) border-danger @endif"
                            placeholder="Chapter">
                    </div>
                    <div class="col">
                        <label for="position">Page / Pos</label>
                        <input
                            type="text"
                            id="position"
                            wire:model="page_position"
                            class="form-control @if($error && !$page_position) border-danger @endif"
                            placeholder="Page / Pos">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="exampleFormControlTextarea1">Highlight</label>
                <span class="float-right">{{ strlen($highlight) }}</span>
                <textarea
                    class="form-control @if($error && !$highlight) border-danger @endif"
                    wire:model="highlight"
                    id="exampleFormControlTextarea1"
                    rows="3"
                ></textarea>
            </div>
            <div class="row">
                <div class="col">
                    <label for="inputState">Question type</label>
                    <select id="inputState" wire:model="questionType" class="form-control">
                        @foreach($questionTypes as $type)
                        <option>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label for="inputState2">Difficulty</label>
                    <select id="inputState2" wire:model="difficult" class="form-control">
                        @foreach($difficultTypes as $type)
                            <option>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label for="inputState3">Number of questions</label>
                    <select id="inputState3" wire:model="numQuestions" class="form-control">
                        @for($i = 1; $i <= 12; $i++)
                            <option @if($this->isMix($i)) disabled @endif>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </form>
        <br>
        @if($loading)
            <div class="spinner-border float-right" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <br>
        @endif
        <button class="float-right" wire:click="generate">Generate questions</button>
        <button class="float-right" wire:click="clear">Clear</button>
    </div>
</div>


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

